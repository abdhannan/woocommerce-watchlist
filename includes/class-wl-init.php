<?php
class WL_Init {
    public static function init() {
        if (!class_exists('WooCommerce')) return;
        

        require_once WL_PLUGIN_PATH . 'includes/class-wl-product-meta.php';
        require_once WL_PLUGIN_PATH . 'includes/class-wl-shortcodes.php';
        require_once WL_PLUGIN_PATH . 'includes/class-wl-ajax.php';
        require_once WL_PLUGIN_PATH . 'includes/class-wl-utils.php';
        require_once WL_PLUGIN_PATH . 'includes/class-wl-admin-page.php';


        
        add_action('woocommerce_after_shop_loop_item', function() {
            global $product;
            if ($product) {
                WL_Utils::render_watchlist_button($product->get_id());
            }
        });
        
        add_action('woocommerce_single_product_summary', function() {
            global $product;
            if ($product) {
                WL_Utils::render_watchlist_button($product->get_id());
            }
        }, 35);
        


        add_action('wp_enqueue_scripts', [__CLASS__, 'enqueue_assets']);
        add_action('wp_head', ['WL_Init', 'inject_custom_styles']);

    }

    public static function activate() {
        // Future use: flush rewrite rules if needed
    }

    public static function enqueue_assets() {
        wp_enqueue_style('wl-css', WL_PLUGIN_URL . 'assets/css/watchlist.css');
        wp_enqueue_style('wl-print', WL_PLUGIN_URL . 'assets/css/watchlist-print.css');

        wp_enqueue_script('html2pdf', 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js', [], null, true);
        wp_enqueue_script('wl-js', WL_PLUGIN_URL . 'assets/js/watchlist.js', ['jquery'], null, true);
        wp_localize_script('wl-js', 'wl_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wl_nonce')
        ]);
    }

    public static function inject_custom_styles() {
        $button_color = WL_Utils::get_style_setting('button_color', '#000000');
        $text_color = WL_Utils::get_style_setting('text_color', '#000000');
        $font_size = WL_Utils::get_style_setting('font_size', '14');
    
        echo '<style>
          .add-to-watchlist, .remove-from-watchlist {
              background-color: ' . esc_attr($button_color) . ';
              color: ' . esc_attr($text_color) . ';
              font-size: ' . intval($font_size) . 'px;
              padding: 8px 12px;
              border: none;
              border-radius: 4px;
              cursor: pointer;
          }
          .add-to-watchlist:hover, .remove-from-watchlist:hover {
              opacity: 0.8;
          }
        </style>';
    }
    
}
