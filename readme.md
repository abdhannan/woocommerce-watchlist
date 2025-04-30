# WooCommerce Watchlist

A lightweight, flexible, and extensible Watchlist plugin for WooCommerce.  
Let your logged-in users add selected products to a personal watchlist and take full control via a clean admin interface.

---

## 🎯 Features

- ✅ Add/remove products to user-specific watchlists (login required)
- ✅ Prevent duplicates – disables add button if already in watchlist
- ✅ Watchlist page with images, description, brand, pricing, and savings
- ✅ Export watchlist: Print-friendly output and PDF download
- ✅ Admin settings page to manage button labels, styles, and messages
- ✅ Dashboard with analytics: most-watched products, user-product data
- ✅ Clean WordPress-native architecture (no bloat)

---

## 📸 Screenshots

> _Screenshots will be added in future releases_

---

## 🔧 Installation

1. Upload the plugin to the `/wp-content/plugins/woocommerce-watchlist/` directory
2. Activate the plugin through the **Plugins** menu in WordPress
3. The plugin requires WooCommerce to be active

---

## 🛠 Usage

### 🔹 Add to Watchlist

- Appears on single product and archive pages
- Requires login
- Button label and style customizable via admin

### 🔹 Watchlist Page

Use the following shortcode to display the current user’s watchlist:

```php
[watchlist_page]
```

You can embed it in any page (e.g. `/my-watchlist`), and assign that page in menus.

---

## ⚙️ Admin Features

Navigate to **Watchlist Settings** in your WordPress dashboard to configure:

### Text Settings

- Empty list message
- Add to Watchlist button label
- Remove from Watchlist button label
- Added to Watchlist label

### Style Settings

- Button background color
- Text color
- Font size

### Login Settings

- Set a custom login page (optional)
- Automatically redirect back to product after login

### Dashboard (submenu)

- View a summary of all watchlist activity
- Top products by number of users watching
- Users and their personal watchlists

---

## 🧱 Developer-Friendly

- Fully class-based architecture:
  - `WL_Init`, `WL_Utils`, `WL_Ajax`, `WL_Admin_Page`
- Built for extensibility and future customization
- WordPress native APIs only
- HTML rendered via `echo` for maximum control

---

## 📦 Filters & Extensibility (coming soon)

Future versions will include filters such as:

```php
apply_filters('wl_is_watchable_product', $product_id);
apply_filters('wl_output_button_label', $label, $product_id);
```

---

## 📈 Roadmap

- [x] Add/remove products to watchlist
- [x] Print/PDF export
- [x] Admin settings panel
- [x] Admin dashboard
- [ ] REST API support
- [ ] Watchlist sync across devices
- [ ] Email/export options
- [ ] Bulk add/remove from archive

---

## 🤝 Contributing

Have a suggestion or feature request?  
Open an issue or submit a pull request on GitHub.

---

## 📄 License

This plugin is licensed under the [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html).  
Copyright © 2024

---

**Crafted with ❤️ for serious WooCommerce users.**
