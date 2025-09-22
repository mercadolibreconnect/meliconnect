=== Meliconnect ===
Contributors: meliconnect
Donate link: https://mercadolibre.meliconnect.com/support-us
Tags: woocommerce, mercadolibre, integration, marketplace, sync
Requires at least: 5.8
Requires PHP:    8.0
Tested up to: 6.8
Stable tag: 1.2.2
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Seamless WooCommerce and Mercado Libre integration with real-time sync of products, stock, and prices.


== Description ==
Meliconnect is a powerful plugin that integrates WooCommerce with Mercado Libre, allowing you to import, export, and synchronize products between your WooCommerce store and Mercado Libre accounts. Manage individual products, bulk operations, and automatic processes, ensuring your store and marketplace listings are always up-to-date.

**Features:**
- Connect one or multiple Mercado Libre accounts to your domain
- Import products from Mercado Libre to WooCommerce
- Export products from WooCommerce to Mercado Libre
- Mass and individual product operations
- Automatic import/export scheduling
- Full control over stock, prices, images, and product data
- Logging system for monitoring plugin activity

== Installation ==
1. Download the plugin from WordPress.org or [Meliconnect](https://mercadolibre.meliconnect.com/).
2. Upload the ZIP file via **Plugins > Add New > Upload Plugin**.
3. Click **Install Now**.
4. Activate the plugin via **Plugins > Activate**.

== Connecting to Mercado Libre ==
1. Go to [Meliconnect](https://meliconnect.com) and log in or create an account.
2. Click **+ Create Domain**.
3. Select your plan.
4. Enter the URL of your site.
5. Choose the country of your Mercado Libre account.
6. The domain will appear as **Connection Pending**.
7. Click **Connect to MercadoLibre**.
   - Ensure you are logged out or using the correct Mercado Libre account.
   - Accept the terms and authorize the application.
8. After authorization, the page reloads and the account appears connected.
9. Verify in WordPress: **Meliconnect → Connection**.

== Configuration ==
- Access **Meliconnect → Settings** to configure:
  - Default images for listings
  - Description templates
  - Exporter (WooCommerce → Mercado Libre) and Importer (Mercado Libre → WooCommerce) settings
  - Automatic synchronization settings

=== Export Settings ===
- Choose which product data to sync: titles, stock, prices, images, etc.
- Define behavior on product creation, update, or deletion.

=== Import Settings ===
- Price adjustments, stock handling, and variations.
- Automatic adaptation of imported products.

=== Synchronization Settings ===
- Enable automatic stock and price synchronization between WooCommerce and Mercado Libre.

== Importing or Exporting Individual Products ==
1. Edit a WooCommerce product.
2. Go to **Mercado Libre** tab.
3. Select seller and category.
4. Save changes and reload.
5. Configure listing type, shipping, condition.
6. Complete required listing attributes.

== Mass Import ==
1. Go to **Meliconnect → Importer**.
2. Select a seller and click **Get Listings**.
3. Optionally, remove temporary listings with **Clean Listings**.
4. Check boxes for products to import.
5. Link to existing WooCommerce products via **Find Match** if needed.
6. Execute import:
   - Selected items: **Bulk Actions > Import Selected > Apply**
   - All items: **Process Import**

== Mass Export ==
1. Go to **Meliconnect → Exporter**.
2. Select WooCommerce products to export.
3. Use **Bulk Actions > Export Selected > Apply**.
4. Check **Meli Listing** column:
   - **To Create**: new Mercado Libre listing
   - Otherwise: update existing listing
   - Optionally unlink to recreate a listing

== Automatic Import/Export ==
1. Go to **Meliconnect → Settings → General**.
2. Activate automatic process.
3. Choose import or export.
4. Set items per process and time interval.
5. Execution methods:
   - **WordPress** cron (default)
   - **Custom** cron (server setup)
6. Cannot run automatic import/export simultaneously to avoid infinite loops.
7. Advanced settings: choose which fields to update or ignore in automatic processes.


== External services ==

This plugin relies on external services to function correctly.  

=== Mercado Libre API ===  
- **What it is**: The official API from Mercado Libre (https://developers.mercadolibre.com/).  
- **Why it is used**: Needed to connect your WooCommerce store with Mercado Libre, allowing product data synchronization (import/export), image uploads, and listing management.  
- **What data is sent**:  
  - Product details (title, description, price, stock, images) when exporting to Mercado Libre.  
  - Access tokens are used to authenticate API requests.  
- **When data is sent**: Only when you actively perform import/export actions or synchronize products with Mercado Libre.  
- **Service provider**: Mercado Libre S.R.L.  
  - Terms of Service: https://developers.mercadolibre.com.ar/en_us/mercado-libre-developer-terms-and-conditions
  - Privacy Policy: https://www.mercadolibre.com/privacy/privacy-notice

=== Meliconnect API ===  
- **What it is**: An integration service provided by Meliconnect (https://meliconnect.com).  
- **Why it is used**: Required to process and transform product and listing data before sending it to Mercado Libre or importing it into WooCommerce.  
- **What data is sent**:  
  - Product details (title, description, price, stock, attributes, categories, and images).  
  - Store configuration data needed for synchronization.  
- **When data is sent**:  
  - When exporting product data to Mercado Libre.  
  - When importing product data from Mercado Libre to WooCommerce.  
- **Service provider**: Meliconnect.  
  - Terms of Service: https://mercadolibre.meliconnect.com/terms-of-service/ 
  - Privacy Policy: https://mercadolibre.meliconnect.com/privacy-policy/ 

== FAQ ==
= How can I view logs of plugin processes? =
- Go to **WooCommerce → Status → Logs**
- Look for logs starting with **meliconnect-**
- Logs detail all plugin operations for monitoring and debugging

== Changelog ==
= 1.0.0 =
* Initial release with full WooCommerce & Mercado Libre integration
* Individual and mass import/export
* Automatic synchronization
* Logging system for plugin processes

