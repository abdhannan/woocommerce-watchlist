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
        // Get all filters
        $selected_user    = isset($_GET['watch_user']) ? intval($_GET['watch_user']) : 0;
        $selected_product = isset($_GET['watch_product']) ? intval($_GET['watch_product']) : 0;
        $selected_brand   = isset($_GET['watch_brand']) ? sanitize_text_field($_GET['watch_brand']) : '';
        $selected_cat     = isset($_GET['watch_cat']) ? intval($_GET['watch_cat']) : 0;
    
        $users = get_users(['fields' => ['ID', 'display_name', 'user_email']]);
    
        $product_counts   = [];
        $user_watchlist   = [];
        $all_products     = [];
    
        // Loop through all users
        foreach ($users as $user) {
            $watchlist = get_user_meta($user->ID, '_watchlist_products', true);
            if (!is_array($watchlist) || empty($watchlist)) continue;
    
            // If filtered by user, skip other users
            if ($selected_user && $user->ID !== $selected_user) continue;
    
            foreach ($watchlist as $product_id) {
                $product = wc_get_product($product_id);
                if (!$product) continue;
    
                // Filtering
                if ($selected_product && $product->get_id() != $selected_product) continue;
                if ($selected_brand) {
                    $brands = wp_get_post_terms($product_id, 'product_brand', ['fields' => 'slugs']);
                    if (!in_array($selected_brand, $brands)) continue;
                }
                if ($selected_cat) {
                    $cats = wp_get_post_terms($product_id, 'product_cat', ['fields' => 'ids']);
                    if (!in_array($selected_cat, $cats)) continue;
                }
    
                if (!isset($product_counts[$product_id])) {
                    $product_counts[$product_id] = 0;
                    $all_products[$product_id] = $product;
                }
                $product_counts[$product_id]++;
                $user_watchlist[$user->ID][] = $product_id;
            }
        }
    
        arsort($product_counts);
    
        echo '<div class="wrap"><h1>Watchlist Dashboard</h1>';
    
        // === FILTER UI ===
        echo '<form method="GET" style="margin-bottom: 20px;">';
        echo '<input type="hidden" name="page" value="watchlist-dashboard" />';
    
        // User dropdown
        wp_dropdown_users([
            'name' => 'watch_user',
            'selected' => $selected_user,
            'show_option_all' => 'All Users'
        ]);
    
        // Product dropdown
        $products = wc_get_products(['limit' => -1, 'orderby' => 'name']);
        echo '<select name="watch_product">';
        echo '<option value="">All Products</option>';
        foreach ($products as $p) {
            echo '<option value="' . $p->get_id() . '"' . selected($selected_product, $p->get_id(), false) . '>' . esc_html($p->get_name()) . '</option>';
        }
        echo '</select>';
    
        // Brand dropdown
        $brands = get_terms('product_brand', ['hide_empty' => false]);
        echo '<select name="watch_brand">';
        echo '<option value="">All Brands</option>';
        foreach ($brands as $brand) {
            echo '<option value="' . esc_attr($brand->slug) . '"' . selected($selected_brand, $brand->slug, false) . '>' . esc_html($brand->name) . '</option>';
        }
        echo '</select>';
    
        // Category dropdown
        $categories = get_terms('product_cat', ['hide_empty' => false]);
        echo '<select name="watch_cat">';
        echo '<option value="">All Categories</option>';
        foreach ($categories as $cat) {
            echo '<option value="' . esc_attr($cat->term_id) . '"' . selected($selected_cat, $cat->term_id, false) . '>' . esc_html($cat->name) . '</option>';
        }
        echo '</select>';
    
        echo ' <input type="submit" class="button button-primary" value="Filter" />';
        echo '</form>';
    
        // === SUMMARY ===
        echo '<h2>Summary</h2><ul>';
        echo '<li><strong>Total Users:</strong> ' . count($user_watchlist) . '</li>';
        echo '<li><strong>Total Unique Products:</strong> ' . count($product_counts) . '</li>';
        if ($product_counts) {
            $top_id = array_key_first($product_counts);
            $top_product = wc_get_product($top_id);
            echo '<li><strong>Top Product:</strong> ' . esc_html($top_product->get_name()) . ' (' . $product_counts[$top_id] . ' users)</li>';
        }
        echo '</ul>';
    
        // === PRODUCT TABLE ===
        echo '<h2>Products in Watchlists</h2>';
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>Product</th><th>Category</th><th>Brand</th><th>Watch Count</th></tr></thead><tbody>';
        foreach ($product_counts as $pid => $count) {
            $p = $all_products[$pid];
            $cats = wp_get_post_terms($pid, 'product_cat', ['fields' => 'names']);
            $brands = wp_get_post_terms($pid, 'product_brand', ['fields' => 'names']);
            echo '<tr>';
            echo '<td>' . esc_html($p->get_name()) . '</td>';
            echo '<td>' . esc_html(implode(', ', $cats)) . '</td>';
            echo '<td>' . esc_html(implode(', ', $brands)) . '</td>';
            echo '<td>' . intval($count) . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    
        // === USER TABLE ===
        echo '<h2>Users and Their Watchlists</h2>';
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>User</th><th>Products</th></tr></thead><tbody>';
        foreach ($user_watchlist as $uid => $product_ids) {
            $u = get_userdata($uid);
            $list = [];
            foreach ($product_ids as $pid) {
                $p = wc_get_product($pid);
                if ($p) {
                    $list[] = $p->get_name();
                }
            }
            echo '<tr>';
            echo '<td>' . esc_html($u->display_name) . ' (' . esc_html($u->user_email) . ')</td>';
            echo '<td>' . esc_html(implode(', ', $list)) . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    
        echo '</div>';
    }
    
    
    
}

new WL_Admin_Page();
