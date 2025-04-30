<?php
$user_id = get_current_user_id();
$product_ids = get_user_meta($user_id, '_watchlist_products', true);

if (!$product_ids || empty($product_ids)) {
    echo '<p>Watchlist Anda kosong.</p>';
    return;
}

$total_price = 0;
$total_saving = 0;

echo '<div id="wl-print-area">';

echo '<div class="wl-watchlist-wrapper">';

foreach ($product_ids as $product_id) {
    $product = wc_get_product($product_id);
    if (!$product) continue;

    $price = (float) $product->get_price();
    $regular_price = (float) $product->get_regular_price();

    $total_price += $price;
    $total_saving += max(0, $regular_price - $price);

    $brand = wc_get_product_terms($product_id, 'pa_brand', ['fields' => 'names']);
    $brand_name = $brand ? esc_html($brand[0]) : '';

    echo '<div class="wl-item">';
    echo '<div class="wl-image">' . $product->get_image() . '</div>';
    echo '<div class="wl-info">';
    echo '<h3>' . esc_html($product->get_name()) . '</h3>';
    if ($brand_name) {
        echo '<p><strong>Brand:</strong> ' . $brand_name . '</p>';
    }
    echo '<p>' . wp_trim_words($product->get_description(), 25) . '</p>';

    echo '<p class="wl-price">';
    if ($regular_price > $price) {
        echo '<del>' . wc_price($regular_price) . '</del> ';
    }
    echo '<strong>' . wc_price($price) . '</strong>';
    echo '</p>';

    $remove_label = WL_Utils::get_text_setting('button_remove_label', '❌ Hapus');
    echo '<button class="remove-from-watchlist" data-product-id="' . esc_attr($product_id) . '">' . esc_html($remove_label) . '</button>';

    echo '<div class="wl-message"></div>';

    echo '</div>'; // .wl-info


    echo '</div>'; // .wl-item
}

echo '</div>'; // .wl-watchlist-wrapper



echo '<div class="wl-summary">';
echo '<p><strong>Total Harga:</strong> <span id="wl-total-price">' . wc_price($total_price) . '</span></p>';
echo '<p><strong>Total Penghematan:</strong> <span id="wl-total-saving">' . wc_price($total_saving) . '</span></p>';
echo '</div>';

echo '</div>'; // <div id="wl-print-area">

echo '<button id="wl-print">🖨️ Print</button> ';
echo '<button id="wl-export-pdf">📄 Download PDF</button>';