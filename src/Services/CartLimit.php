<?php

namespace Src\Services;

use Src\Traits\Singleton;

class CartLimit
{
    use Singleton;

    public function register()
    {
        add_filter( 'woocommerce_add_to_cart_validation', [$this, 'only_one_in_cart'], 9999, 2 );
        add_filter( 'wc_add_to_cart_message', [$this, 'remove_continue_message']);
    }

    public function only_one_in_cart($passed, $added_product_id)
    {
        wc_empty_cart();
        return $passed;
    }

    public function remove_continue_message($string, $product_id = 0): string
    {
        return '';
    }
}