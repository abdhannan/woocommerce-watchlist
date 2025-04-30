<?php
class WL_Admin_Page {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_menu']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function add_menu() {
        add_menu_page(
            'Watchlist Settings',            // Page title
            'Watchlist Settings',            // Menu title
            'manage_options',                // Capability
            'watchlist-settings',            // Menu slug
            [$this, 'settings_page'],         // Callback
            'dashicons-admin-generic',        // Icon
            80                                // Position
        );

        add_submenu_page(
            'watchlist-settings',
            'Watchlist Dashboard',
            'Watchlist Dashboard',
            'manage_options',
            'watchlist-dashboard',
            [$this, 'render_dashboard']
        );
        
    }
    

    public function register_settings() {
        // Group
        register_setting('wl_settings_group', 'wl_text_settings');
        register_setting('wl_settings_group', 'wl_style_settings');
        register_setting('wl_settings_group', 'wl_custom_login_page'); // <-- tambah ini

        // Section
        add_settings_section('wl_text_section', 'Text Settings', null, 'watchlist-settings');
        add_settings_section('wl_style_section', 'Style Settings', null, 'watchlist-settings');

        // Fields - TEXT
        add_settings_field('wl_empty_message', 'Pesan Watchlist Kosong', [$this, 'field_empty_message'], 'watchlist-settings', 'wl_text_section');
        add_settings_field('wl_button_add_label', 'Label Tombol Add', [$this, 'field_button_add_label'], 'watchlist-settings', 'wl_text_section');
        add_settings_field('wl_button_remove_label', 'Label Tombol Remove', [$this, 'field_button_remove_label'], 'watchlist-settings', 'wl_text_section');

        // Fields - STYLE
        add_settings_field('wl_button_color', 'Warna Tombol', [$this, 'field_button_color'], 'watchlist-settings', 'wl_style_section');
        add_settings_field('wl_text_color', 'Warna Teks', [$this, 'field_text_color'], 'watchlist-settings', 'wl_style_section');
        add_settings_field('wl_font_size', 'Ukuran Font', [$this, 'field_font_size'], 'watchlist-settings', 'wl_style_section');

        // 
        add_settings_field('wl_button_added_label', 'Label Tombol Setelah Ditambahkan', [$this, 'field_button_added_label'], 'watchlist-settings', 'wl_text_section');


        // setting login page
        add_settings_field('wl_custom_login_page', 'Halaman Login Kustom', [$this, 'field_custom_login_page'], 'watchlist-settings', 'wl_text_section');

    }

    public function settings_page() {
        ?>
        <div class="wrap">
            <h1>Watchlist Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('wl_settings_group');
                do_settings_sections('watchlist-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    // ========== Field Callbacks ==========

    public function field_empty_message() {
        $options = get_option('wl_text_settings');
        echo '<input type="text" name="wl_text_settings[empty_message]" value="' . esc_attr($options['empty_message'] ?? 'Watchlist Anda kosong.') . '" class="regular-text">';
    }

    public function field_button_add_label() {
        $options = get_option('wl_text_settings');
        echo '<input type="text" name="wl_text_settings[button_add_label]" value="' . esc_attr($options['button_add_label'] ?? '+ Watchlist') . '" class="regular-text">';
    }

    public function field_button_remove_label() {
        $options = get_option('wl_text_settings');
        echo '<input type="text" name="wl_text_settings[button_remove_label]" value="' . esc_attr($options['button_remove_label'] ?? '❌ Hapus') . '" class="regular-text">';
    }

    public function field_button_color() {
        $options = get_option('wl_style_settings');
        echo '<input type="color" name="wl_style_settings[button_color]" value="' . esc_attr($options['button_color'] ?? '#000000') . '">';
    }

    public function field_text_color() {
        $options = get_option('wl_style_settings');
        echo '<input type="color" name="wl_style_settings[text_color]" value="' . esc_attr($options['text_color'] ?? '#000000') . '">';
    }

    public function field_font_size() {
        $options = get_option('wl_style_settings');
        echo '<input type="number" name="wl_style_settings[font_size]" value="' . esc_attr($options['font_size'] ?? '14') . '" min="10" max="30"> px';
    }

    public function field_button_added_label() {
        $options = get_option('wl_text_settings');
        echo '<input type="text" name="wl_text_settings[button_added_label]" value="' . esc_attr($options['button_added_label'] ?? '✔️ Sudah di Watchlist') . '" class="regular-text">';
    }


    // fallback setting login page
    public function field_custom_login_page() {
        $selected = get_option('wl_custom_login_page');
        wp_dropdown_pages([
            'name' => 'wl_custom_login_page',
            'show_option_none' => '-- Gunakan Default Login --',
            'selected' => $selected
        ]);
    }
    
    /**
     * render dashboard sub-menu page 
     */
    public function render_dashboard() {
        $users = get_users(['fields' => ['ID', 'display_name', 'user_email']]);
    
        $product_counts = [];
        $user_watchlist = [];
    
        foreach ($users as $user) {
            $watchlist = get_user_meta($user->ID, '_watchlist_products', true);
            if (!is_array($watchlist) || empty($watchlist)) continue;
    
            $user_watchlist[$user->ID] = $watchlist;
    
            foreach ($watchlist as $product_id) {
                if (!isset($product_counts[$product_id])) {
                    $product_counts[$product_id] = 0;
                }
                $product_counts[$product_id]++;
            }
        }
    
        arsort($product_counts); // urutkan produk dari yang paling banyak di-watch
    
        echo '<div class="wrap"><h1>Watchlist Dashboard</h1>';
    
        // Summary
        echo '<h2>Summary</h2>';
        echo '<ul>';
        echo '<li>Total users with watchlist: <strong>' . count($user_watchlist) . '</strong></li>';
        echo '<li>Total unique products in watchlists: <strong>' . count($product_counts) . '</strong></li>';
        if ($product_counts) {
            $top_product_id = array_key_first($product_counts);
            $top_product = wc_get_product($top_product_id);
            echo '<li>Top Product: <strong>' . $top_product->get_name() . '</strong> (' . $product_counts[$top_product_id] . ' users)</li>';
        }
        echo '</ul>';
    
        // Product Watch Counts
        echo '<h2>Products in Watchlists</h2>';
        echo '<table class="widefat striped"><thead><tr><th>Product</th><th>Count</th></tr></thead><tbody>';
        foreach ($product_counts as $product_id => $count) {
            $product = wc_get_product($product_id);
            if (!$product) continue;
            echo '<tr><td>' . $product->get_name() . ' (#' . $product_id . ')</td><td>' . $count . '</td></tr>';
        }
        echo '</tbody></table>';
    
        // User Details
        echo '<h2>Users and Their Watchlists</h2>';
        echo '<table class="widefat striped"><thead><tr><th>User</th><th>Products</th></tr></thead><tbody>';
        foreach ($user_watchlist as $user_id => $products) {
            $user = get_userdata($user_id);
            echo '<tr><td>' . esc_html($user->display_name) . ' (' . esc_html($user->user_email) . ')</td><td>';
            $names = [];
            foreach ($products as $pid) {
                $p = wc_get_product($pid);
                if ($p) $names[] = $p->get_name();
            }
            echo implode(', ', $names);
            echo '</td></tr>';
        }
        echo '</tbody></table>';
    
        echo '</div>';
    }
    
    
}

new WL_Admin_Page();
