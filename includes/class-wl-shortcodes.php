<?php
class WL_Shortcodes {
    public function __construct() {
        add_shortcode('user_watchlist', [$this, 'render_watchlist']);
    }

    public function render_watchlist() {
        if (!is_user_logged_in()) return 'Silakan login untuk melihat watchlist Anda.';

        ob_start();
        include WL_PLUGIN_PATH . 'templates/watchlist-page.php';
        return ob_get_clean();
    }
}
new WL_Shortcodes();
