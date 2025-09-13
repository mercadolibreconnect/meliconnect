# Meliconnect

## 1. Requirements
- WordPress 5.8 or higher
- PHP 8.0 or higher
- WooCommerce installed and activated
- An active Mercado Libre account

## 2. Installation
1. Download the plugin from the WordPress.org repository or from [Meliconnect](https://mercadolibre.meliconnect.com/).
2. Upload the ZIP file to your WordPress via **Plugins > Add New**.
3. Click **Install Now**.
4. Activate the plugin via **Plugins > Activate**.

## 3. Connecting to Mercado Libre
The plugin allows you to link one or multiple active Mercado Libre accounts to the same domain. Follow these steps:

### Create or log in to Meliconnect
- Visit [Meliconnect](https://meliconnect.com) and log in or create a new account.

### Add your domain
1. Click **+ Create Domain**.
2. Select your plan.
3. Enter the **URL of the domain** where the plugin will be used.
4. Choose the **country** corresponding to your Mercado Libre account.
5. After saving, the domain will appear with the status **“Connection Pending”**.

### Connect your Mercado Libre account
1. Click the green **Connect to MercadoLibre** button.
2. A modal will open.  
   **Important:** Make sure you are logged out of Mercado Libre or logged in with the account you want to connect.
3. Accept Mercado Libre's terms and authorize the app (only required for the first connection).

### Confirm the connection
- Once authorized, the page will reload and show the connected account in the list.
- To verify, go to **Meliconnect → Connection** in WordPress and check the connected user and associated data.

## 4. Configuration
Before starting any mass or automatic import/export process, check and adjust settings in **Meliconnect → Settings**.

### General settings
- Default images to add to all listings.
- Description template for product listings.
- Activation and configuration of the exporter (WooCommerce → Mercado Libre) or importer (Mercado Libre → WooCommerce), manual or automatic.
- These settings will affect all import/export processes.

### Export settings
- Choose which data to sync from WooCommerce to Mercado Libre:
  - Titles
  - Stock
  - Prices
  - Images, etc.
- Specify whether the data is exported on creation, update, or both.
- Decide the behavior when a WooCommerce product is deleted while linked to an active Mercado Libre listing.

### Import settings
- Define how products from Mercado Libre are imported:
  - Price adjustments (e.g., apply a 10% discount)
  - Stock settings
- Adapt imported products automatically to your store.

### Synchronization settings
- Enable automatic processes to keep stock and prices in sync between WooCommerce and Mercado Libre in real-time.

## 5. Importing or Exporting Individual Products
### Export a WooCommerce product to Mercado Libre
1. Edit the product in WooCommerce.
2. Go to the **Mercado Libre** tab.
3. Select the connected seller and category.
4. Save changes and reload the page.
5. Configure listing details:
   - Listing type
   - Shipping methods
   - Condition (new, used, etc.)
6. Fill in the **Listing Attributes**. Required fields must be completed to create the listing.

## 6. Mass Import
1. Go to **Meliconnect → Importer**.
2. Select the seller and click **Get Listings** to load their active listings.
3. Use **Clean Listings** to remove temporary listings if needed.
4. Check the box for each listing to import as a WooCommerce product.
5. Use **Find Match** in the **Woo Product** column to link existing products.
6. Execute import:
   - **Import selected**: via **Bulk Actions > Import Selected > Apply**
   - **Import all**: click **Process Import**

## 7. Mass Export
1. Go to **Meliconnect → Exporter**.
2. Select products to export.
3. Choose **Export Selected** in **Bulk Actions** and click **Apply**.
4. Check **Meli Listing** column:
   - **To Create**: a new Mercado Libre listing will be created.
   - Otherwise, the listing will be updated.
   - Optionally, unlink a product to recreate it.

## 8. Automatic Import/Export
### Enable and configure
1. Go to **Meliconnect → Settings → General**.
2. Activate automatic process.
3. Choose import or export.
4. Set items per process and time interval.
5. Select execution method:
   - **WordPress**: uses WP cron by default.
   - **Custom**: configure server cron pointing to the provided URL.

### Prevent infinite loops
Automatic import and export cannot run simultaneously.

### Advanced field settings
- In **Exporter** and **Importer**, select which fields to update or ignore in automatic processes.

## 9. FAQs
### How can I view logs to understand what the plugin is doing?
- The plugin uses WooCommerce’s default logging system.
- Go to **WooCommerce → Status → Logs**.
- Look for logs starting with **meliconnect-**.
- These logs provide detailed information about plugin operations for debugging.

