# MeliConnect

**Tags:** WooCommerce, MercadoLibre, synchronization, e-commerce  
**Requires PHP:** 7.4  
**Requires at least:** 5.8  
**Tested up to:** 6.7  
**Stable tag:** 1.0.0  
**License:** GPLv3  
**License URI:** https://www.gnu.org/licenses/gpl-3.0.html  

Effortlessly synchronize WooCommerce with MercadoLibre to manage products, inventory, and pricing in real-time.


---

## Description

The **MercadoLibre Connect** plugin bridges the gap between MercadoLibre and your WooCommerce-powered store. Automate data synchronization, reduce manual work, and ensure your store and MercadoLibre listings are always up-to-date.

### Key Features

- **Product Synchronization:** Sync product data between WooCommerce and MercadoLibre, including titles, descriptions, images, prices, and stock levels.  
- **Variations Support:** Handle variable products and multiple options seamlessly.  
- **Real-Time Updates:** Automatically push updates from your WooCommerce store to MercadoLibre and vice versa.  
- **Manual Sync:** Initiate manual syncs for precise control.  
- **Customizable Mapping:** Map WooCommerce attributes to MercadoLibre fields as needed.  
- **Error Logs:** Debug synchronization issues with detailed logs.  

---

## Installation

1. Download the plugin ZIP file.  
2. Upload the plugin to the `/wp-content/plugins/` directory.  
3. Activate the plugin through the 'Plugins' menu in WordPress.  
4. Navigate to **WooCommerce > Settings > MercadoLibre Sync** to configure the plugin.

---

## Frequently Asked Questions

### Does this plugin support variable products?  
Yes, it fully supports variable products and allows mapping attributes to MercadoLibre fields.  

### Can I synchronize only specific products?  
Yes, you can manually select which products to sync.  

### Is real-time synchronization supported?  
Yes, real-time synchronization is available for automatic updates.  

### Are there any regional limitations?  
This plugin supports all regions where MercadoLibre operates.

---

## Screenshots

1. **Settings Page**: Configure your MercadoLibre account and sync preferences.  
2. **Product Mapping**: Map WooCommerce attributes to MercadoLibre fields.  
3. **Sync Logs**: Review synchronization details and debug errors.

---

## Changelog

### 1.0.0  
- Initial release.  
- Full support for product synchronization with MercadoLibre.  
- Real-time and manual sync options.

---

## License

This plugin is licensed under the GPLv2 or later. See the [LICENSE](https://www.gnu.org/licenses/gpl-2.0.html) for more details.



# - How to create .pot files for Meliconnect plugin 
Use wp CLI command to create .pot files for Meliconnect plugin
'''
wp i18n make-pot ./wp-content/plugins/meliconnect ./wp-content/plugins/meliconnect/languages/meliconnect.pot --exclude=node_modules,vendor
'''

if you are in plugin folder in terminal. Run this command
'''
wp i18n make-pot ./ ./languages/meliconnect.pot --exclude=node_modules,vendor
'''






