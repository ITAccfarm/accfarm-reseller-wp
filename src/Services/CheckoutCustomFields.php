<?php

namespace Src\Services;

use Src\Traits\Singleton;

class CheckoutCustomFields
{
    use Singleton;

    public function register()
    {
        // Add fields to checkout
        add_action('woocommerce_checkout_before_customer_details', [$this, 'custom_checkout_fields']);
        
        // Custom fields validation
        add_action('woocommerce_checkout_process', [$this, 'custom_checkout_fields_process']);

        // Store data to order meta
        add_action('woocommerce_checkout_create_order', [$this, 'custom_checkout_fields_update_meta'], 10, 2);
    }

    public function custom_checkout_fields()
    {
        $checkout = WC()->checkout;
        $accfarmProductType = $this->getAccfarmProductType();

        switch ($accfarmProductType) {
            case 'review':
                $this->reviewFields($checkout);
                break;
            case 'install':
                $this->installFields($checkout);
                break;
        }
    }

    public function custom_checkout_fields_process()
    {
        $accfarmProductType = $this->getAccfarmProductType();

        switch ($accfarmProductType) {
            case 'review':
                $this->reviewValidation();
                break;
            case 'install':
                $this->installValidation();
                break;
        }
    }

    public function custom_checkout_fields_update_meta($order, $data)
    {
        $accfarmProductType = $this->getAccfarmProductType();

        switch ($accfarmProductType) {
            case 'review':
                $this->reviewSaveCustom($order);
                break;
            case 'install':
                $this->installSaveCustom($order);
                break;
        }
    }

    private function reviewFields($checkout)
    {
        echo '<div id="account_data_fields"><h2>' . 'Account Data' . '</h2>';

        woocommerce_form_field( '_review_url', [
            'type'          => 'url',
            'class'         => ['form-row-wide'],
            'label'         => __('URL for reviews', 'accfarm-reseller'),
            'placeholder'   => __('Review URL', 'accfarm-reseller'),
            'required'      => true,
        ], $checkout->get_value('_review_url'));

        woocommerce_form_field( '_review_reviews', [
            'type'          => 'textarea',
            'class'         => ['form-row-wide'],
            'label'         => __('Reviews to upload', 'accfarm-reseller'),
            'placeholder'   => __('Reviews separated by next line', 'accfarm-reseller'),
            'required'      => false,
        ], $checkout->get_value('_review_reviews'));

        echo '</div>';
    }

    private function installFields($checkout)
    {
        echo '<div id="account_data_fields"><h2>' . 'Account Data' . '</h2>';

        woocommerce_form_field( '_install_app_link', [
            'type'          => 'url',
            'class'         => ['form-row-wide'],
            'label'         => __('Link to the application', 'accfarm-reseller'),
            'placeholder'   => __('App link', 'accfarm-reseller'),
            'required'      => true,
        ], $checkout->get_value('_install_app_link'));

        woocommerce_form_field( '_install_app_id', [
            'type'          => 'text',
            'class'         => ['form-row-wide'],
            'label'         => __('Application ID', 'accfarm-reseller'),
            'placeholder'   => __('App ID', 'accfarm-reseller'),
            'required'      => true,
        ], $checkout->get_value('_install_app_id'));

        woocommerce_form_field( '_install_days', [
            'type'          => 'number',
            'class'         => ['form-row-wide'],
            'label'         => __('Span of days for installs', 'accfarm-reseller'),
            'placeholder'   => __('Days', 'accfarm-reseller'),
            'required'      => true,
        ], $checkout->get_value('_install_days'));

        woocommerce_form_field( '_install_country', [
            'type'          => 'text',
            'class'         => ['form-row-wide'],
            'label'         => __('Country', 'accfarm-reseller'),
            'placeholder'   => __('Country', 'accfarm-reseller'),
            'required'      => true,
        ], $checkout->get_value('_install_country'));

        woocommerce_form_field( '_install_reviews', [
            'type'          => 'textarea',
            'class'         => ['form-row-wide'],
            'label'         => __('Reviews to upload', 'accfarm-reseller'),
            'placeholder'   => __('Reviews separated by next line', 'accfarm-reseller'),
            'required'      => false,
        ], $checkout->get_value('_install_reviews'));

        echo '</div>';
    }

    private function reviewValidation()
    {
        if (empty($_POST['_review_url'])) {
            wc_add_notice(__('Please fill in "URL for reviews".', 'accfarm-reseller'), 'error');
        }
    }

    private function installValidation()
    {
        if (empty($_POST['_install_app_link'])) {
            wc_add_notice(__('Please fill in "Link to the application".', 'accfarm-reseller'), 'error');
        }

        if (empty($_POST['_install_app_id'])) {
            wc_add_notice(__('Please fill in "Application ID".', 'accfarm-reseller'), 'error');
        }

        if (empty($_POST['_install_days'])) {
            wc_add_notice(__('Please fill in "Span of days for installs".', 'accfarm-reseller'), 'error');
        }

        if (empty($_POST['_install_country'])) {
            wc_add_notice(__('Please fill in "Country".', 'accfarm-reseller'), 'error');
        }
    }

    private function reviewSaveCustom($order)
    {
        if (!empty($_POST['_accfarm_review_url'])) {
            $order->update_meta_data('_review_url', sanitize_text_field($_POST['_review_url']));
        }

        if (!empty($_POST['_accfarm_review_reviews'])) {
            $order->update_meta_data('_review_reviews', sanitize_text_field($_POST['_review_reviews']));
        }

        $order->save();
    }

    private function installSaveCustom($order)
    {
        $data = [];

        if (!empty($_POST['_install_app_link'])) {
            $data['app_link'] = sanitize_text_field($_POST['_install_app_link']);
        }

        if (!empty($_POST['_install_app_id'])) {
            $data['app_id'] = sanitize_text_field($_POST['_install_app_id']);
        }

        if (!empty($_POST['_install_days'])) {
            $data['days'] = sanitize_text_field($_POST['_install_days']);
        }

        if (!empty($_POST['_install_country'])) {
            $data['country'] = sanitize_text_field($_POST['_install_country']);
        }

        if (!empty($_POST['_install_reviews'])) {
            $order->update_meta_data('_accfarm_install_reviews', sanitize_text_field($_POST['_install_reviews']));
        }

        $order->update_meta_data('_accfarm_install', json_encode($data));
        $order->save();
    }

    private function getAccfarmProductType()
    {
        $cart = WC()->cart->get_cart();
        $product = array_pop($cart);

        return get_post_meta($product['product_id'], '_accfarm_product_type', true);
    }
}