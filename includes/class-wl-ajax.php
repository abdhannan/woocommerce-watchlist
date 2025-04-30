<?php
class WL_Ajax {
    public function __construct() {
        add_action('wp_ajax_add_to_watchlist', [$this, 'add']);
        add_action('wp_ajax_add_to_watchlist', ['WL_Ajax','add']);
        add_action('wp_ajax_remove_from_watchlist', [ $this, 'remove' ]);

// (untuk non-logged users tidak perlu wp_ajax_nopriv)

    }

    public function add() {
        check_ajax_referer('wl_nonce', 'security');


        $user_id = get_current_user_id();
        $product_id = absint($_POST['product_id']);

        if (!$user_id || !$product_id) wp_send_json_error();

        $watchlist = get_user_meta($user_id, '_watchlist_products', true) ?: [];

        if (!in_array($product_id, $watchlist)) {
            $watchlist[] = $product_id;
            update_user_meta($user_id, '_watchlist_products', $watchlist);
        }

        wp_send_json_success();
    }

    public function remove() {
        check_ajax_referer('wl_nonce', 'security');
    
        if (!is_user_logged_in()) {
            wp_send_json_error(['message' => 'Anda harus login.']);
        }
    
        $user_id    = get_current_user_id();
        $product_id = absint($_POST['product_id']);
        $watchlist  = get_user_meta($user_id, '_watchlist_products', true) ?: [];
    
        $key = array_search($product_id, $watchlist);
        if ($key !== false) {
            unset($watchlist[$key]);
            update_user_meta($user_id, '_watchlist_products', array_values($watchlist));
            wp_send_json_success();
        } else {
            wp_send_json_error(['message' => 'Produk tidak ditemukan.']);
        }
    }
    
}
new WL_Ajax();
