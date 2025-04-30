<?php
class WL_Product_Meta {
    public function __construct() {
        add_action('woocommerce_product_options_general_product_data', [$this, 'add_field']);
        add_action('woocommerce_process_product_meta', [$this, 'save_field']);
    }

    public function add_field() {
        woocommerce_wp_checkbox([
            'id' => '_enable_watchlist',
            'label' => 'Aktifkan Watchlist',
            'description' => 'Izinkan produk ini dimasukkan ke watchlist',
        ]);
    }

    public function save_field($post_id) {
        $value = isset($_POST['_enable_watchlist']) ? 'yes' : 'no';
        update_post_meta($post_id, '_enable_watchlist', $value);
    }
}
new WL_Product_Meta();
