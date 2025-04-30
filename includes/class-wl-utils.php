<?php
class WL_Utils {
    public static function is_watchlist_enabled($product_id) {
        return get_post_meta($product_id, '_enable_watchlist', true) === 'yes';
    }

    public static function is_watchable_product($product_id) {
        return true;
    }
    

    public static function render_watchlist_button($product_id) {
        if (!self::is_watchable_product($product_id)) return;
    
        $add_label    = self::get_text_setting('button_add_label', '+ Watchlist');
        $added_label  = self::get_text_setting('button_added_label', '✔️ Sudah di Watchlist');
    
        if (!is_user_logged_in()) {
            $login_url = self::get_login_url(get_permalink($product_id));
            echo '<a href="' . esc_url($login_url) . '" class="add-to-watchlist not-logged-in">' . esc_html($add_label) . '</a>';
        } else {
            $user_id   = get_current_user_id();
            $watchlist = get_user_meta($user_id, '_watchlist_products', true) ?: [];
    
            if (in_array($product_id, $watchlist)) {
                // Sudah di watchlist
                echo '<button class="add-to-watchlist" disabled>' . esc_html($added_label) . '</button>';
            } else {
                // Belum
                echo '<button class="add-to-watchlist" data-product-id="' . esc_attr($product_id) . '">' . esc_html($add_label) . '</button>';
            }
        }
    }
    
    
    

    public static function get_text_setting($key, $default = '') {
        $options = get_option('wl_text_settings');
        return $options[$key] ?? $default;
    }

    public static function get_style_setting($key, $default = '') {
        $options = get_option('wl_style_settings');
        return $options[$key] ?? $default;
    }



    // custom login
    public static function get_login_url($redirect_to = '') {
        $custom_login_page = get_option('wl_custom_login_page'); // setting opsional, nanti kita buat di admin
    
        if ($custom_login_page) {
            // Kalau admin set custom page
            $login_url = get_permalink($custom_login_page);
        } else {
            // Kalau tidak, pakai wp-login.php default
            $login_url = wp_login_url();
        }
    
        if ($redirect_to) {
            $login_url = add_query_arg('redirect_to', urlencode($redirect_to), $login_url);
        }
    
        return $login_url;
    }
    
    
    
}
