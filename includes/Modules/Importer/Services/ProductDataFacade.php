<?php

namespace StoreSync\Meliconnect\Modules\Importer\Services;

use StoreSync\Meliconnect\Core\Helpers\Helper;
use StoreSync\Meliconnect\Core\Helpers\MeliconMeli;
use StoreSync\Meliconnect\Core\Models\Template;
use StoreSync\Meliconnect\Core\Models\UserConnection;

/**
 * Orchestrates the process of obtaining transformed product data from
 * the WooCommerceProductAdapter and creating or updating products in
 * WooCommerce using WooCommerceProductCreationService.
 */

class ProductDataFacade
{
    protected $wooCommerceAdapter;
    protected $productCreationService;

    public function __construct(
        WooCommerceProductAdapter $wooCommerceAdapter,
        WooCommerceProductCreationService $productCreationService
    ) {
        $this->wooCommerceAdapter = $wooCommerceAdapter;
        $this->productCreationService = $productCreationService;
    }

    public function importAndCreateProduct($meliListingId, $meli_user_id, $template_id = NULL, $woo_product_id = NULL)
    {
        $meli_user_data = UserConnection::getUser($meli_user_id);

        if (!$meli_user_data) {
            Helper::logData('User not found: ' . $meli_user_id, 'custom-import');
            return false;
        }

        $meli_listing_data = MeliconMeli::getMercadoLibreListingData($meliListingId, $meli_user_data->access_token);

        // Obtener datos transformados desde el servidor usando el adaptador
        $transformedData = $this->wooCommerceAdapter->getTransformedProductData($meli_listing_data, $meli_user_id, $template_id, $woo_product_id);

        if (!isset($transformedData['status']) || $transformedData['status'] !== 200 || !isset($transformedData['data']) || empty($transformedData['data'])) {
            // Manejo de errores si no se pudo obtener o transformar los datos

            Helper::logData('Error processing product data: ' . wp_json_encode($transformedData), 'custom-import');
            return false;
        }

        //create woocommerce product
        $woo_products_ids = $this->productCreationService->createProduct($transformedData['data'], $meli_listing_data['data']);

        if (isset($woo_products_ids) && !empty($woo_products_ids) && is_array($woo_products_ids)) {
            foreach ($woo_products_ids as $woo_product_id) {

                //$template_id = Template::createUpdateTemplateFromMeliListing('product', $woo_product_id, $meli_listing_data['data']);
                $template_id = get_post_meta($woo_product_id, 'melicon_asoc_template_id', true);

                if($template_id) {
                    //Deletes and Creates template attributes
                    Template::deleteCreateTemplatesAttributesFromMeliListing($template_id, $meli_listing_data['data'], $woo_product_id);
                } else{
                    Helper::logData('Error creating template for product: ' . $woo_product_id, 'custom-import');
                }
                
            }
        }


        return true;
    }

}
