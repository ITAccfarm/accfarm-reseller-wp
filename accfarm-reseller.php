<?php

/**
 * Plugin Name: Accfarm Reseller
 * Plugin URI: https://accfarm.com/
 * Description: Accfarm reseller integration into WooCommerce
 * Version: 1.0.0
 * Author: Accfarm
 * Author URI: https://accfarm.com/
 * Text Domain: accfarm-reseller
 * Domain Path: /i18n/languages/
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package AccfarmReseller
 */

if (!defined( 'ABSPATH' )) {
    exit();
}

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
} else {
    exit('Vendor autoload is missing!');
}

\Src\Services\Localization::instance()->register();

if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option( 'active_plugins' )))) {
    exit(__('You need WooCommerce to run this plugin!', 'accfarm-reseller'));
}

define('ACCFARM_RESELLER_PATH', plugin_dir_path(__FILE__));
define('ACCFARM_RESELLER_URL', plugin_dir_url(__FILE__));
define('ACCFARM_RESELLER_NAME', plugin_basename(__FILE__));
define('ACCFARM_RESELLER_FILE', __FILE__);

if (class_exists('\Src\AccfarmReseller')) {
    $accfarmReseller = \Src\AccfarmReseller::instance();

    register_activation_hook(__FILE__, [$accfarmReseller, 'activate']);
    register_deactivation_hook(__FILE__, [$accfarmReseller, 'deactivate']);
//    register_uninstall_hook(__FILE__, [$accfarmReseller, 'uninstall']);

    $accfarmReseller->init();
}