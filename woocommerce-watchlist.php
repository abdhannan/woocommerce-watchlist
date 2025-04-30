<?php
/**
 * Plugin Name: WooCommerce Watchlist
 * Description: Sistem watchlist khusus produk tertentu dengan fitur login, export PDF, dan print.
 * Version: 1.0
 * Author: Abd Hanann
 * Author URI: abdhannan.codes
 * Requires Plugins: woocommerce
 */

if (!defined('ABSPATH')) exit;

define('WL_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('WL_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once WL_PLUGIN_PATH . 'includes/class-wl-init.php';

register_activation_hook(__FILE__, ['WL_Init', 'activate']);
add_action('plugins_loaded', ['WL_Init', 'init']);
