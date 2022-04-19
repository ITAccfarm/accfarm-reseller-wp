<?php

namespace Src\Services;

use Src\Traits\Singleton;

class ProductCustomFields
{
    use Singleton;

    public function register()
    {
        // The code for displaying WooCommerce Product Custom Fields
        add_action('woocommerce_product_options_general_product_data', [$this, 'woocommerce_product_custom_fields']);

        // Following code Saves  WooCommerce Product Custom Fields
        add_action('woocommerce_process_product_meta', [$this, 'woocommerce_product_custom_fields_save']);
    }

    public function woocommerce_product_custom_fields()
    {
        global $woocommerce, $post;
        echo '<div class=" product_custom_field ">';

        // Offer ID
        woocommerce_wp_text_input([
            'id' => '_accfarm_offer_id',
            'placeholder' => 'offer_id',
            'label' => __('Accfarm Offer ID', 'accfarm-reseller'),
            'type' => 'number',
            'custom_attributes' => [
                'step' => '1',
                'min' => '1'
            ]
        ]);

        // Product Type
        woocommerce_wp_select(
            array(
                'id'      => '_accfarm_product_type',
                'label'   => __('Accfarm Product Type', 'accfarm-reseller'),
                'options' => [
                    'offer' => 'Offer',
                    'review' => 'Review',
                    'install' => 'Install'
                ],
            )
        );

        echo '</div>';
    }

    public function woocommerce_product_custom_fields_save($post_id)
    {
        // Offer ID
        $woocommerce_accfarm_offer_id = $_POST['_accfarm_offer_id'];

        if (!empty($woocommerce_accfarm_offer_id)) {
            update_post_meta($post_id, '_accfarm_offer_id', esc_attr($woocommerce_accfarm_offer_id));
        }

        // Product Type
        $woocommerce_accfarm_product_type = $_POST['_accfarm_product_type'];

        if (!empty($woocommerce_accfarm_product_type)) {
            update_post_meta($post_id, '_accfarm_product_type', esc_attr($woocommerce_accfarm_product_type));
        }
    }
}