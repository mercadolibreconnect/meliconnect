<?php

namespace Meliconnect\Meliconnect\Modules\Importer\Services;

use Meliconnect\Meliconnect\Core\Helpers\Helper;
use Meliconnect\Meliconnect\Core\Helpers\MeliconMeli;
use Meliconnect\Meliconnect\Core\Models\UserConnection;

/**
 * Handles communication with the external server to processs and send raw product data
 * and receive transformed product data.
 */
class WooCommerceProductAdapter
{
    protected $apiEndpoint;

    public function __construct() {
        // Detectar entorno y seleccionar la URL correspondiente

        $this->apiEndpoint = 'https://meliconnect.com/api/v1/process-product-data-to-import';
        
    }

    public function getTransformedProductData($meli_listing_data, $meli_user_id, $template_id = NULL, $woo_product_id = NULL, $sync_options = NULL)
    {
        

        $rawData = [
            'extra_data' => [
                'meli_user_id' => $meli_user_id,
                'woo_product_id' => $woo_product_id,
                'template_id' => $template_id,
                'domain' => Helper::getDomainName(),
            ],

            'meli_listing' => $meli_listing_data,
            'settings' => Helper::getMeliconnectOptions('import'),
            'sync_options' => $sync_options
            //'woo_product' => $this->getWooProductData($woo_product_id),
            //'woo_variations' => $this->getWooProductVariations($woo_product_id),
            //'template' => $this->getMeliconTemplateData($template_id),
            
        ];

        //Helper::logData('Raw data sent to hub: ' . wp_json_encode($rawData), 'custom-import');

        // Enviar datos sin procesar al servidor y recibir datos transformados
        $server_response = $this->sendDataToServer($rawData);

        //Helper::logData('Server response:' . $server_response, 'custom-import');

        return json_decode($server_response, true);
    }

    private function getWooProductData($woo_product_id = NULL)
    {
        if ($woo_product_id == NULL) {
            return [];
        }

        // Obtén el objeto del producto
        $product = wc_get_product($woo_product_id);

        if (!$product) {
            return [];
        }

        // Extrae la información necesaria
        $product_data = [
            'id' => $product->get_id(),
            'title' => $product->get_name(),
            'short_description' => $product->get_short_description(),
            'long_description' => $product->get_description(),
            '_sku' => $product->get_sku(),
            'is_variable' => $product->is_type('variable'),
            'categories' => $this->getProductCategories($product),
        ];

        return $product_data;
    }

    private function getWooProductVariations($woo_product_id = NULL)
    {
        if ($woo_product_id == NULL) {
            return [];
        }

        // Obtén el objeto del producto
        $product = wc_get_product($woo_product_id);

        // Verifica que el producto sea de tipo variable
        if (!$product || !$product->is_type('variable')) {
            return [];
        }

        // Obtén las variaciones del producto
        $variations = [];
        $variation_ids = $product->get_children();

        foreach ($variation_ids as $variation_id) {
            $variation = wc_get_product($variation_id);
            if ($variation) {
                $variations[] = [
                    'id' => $variation->get_id(),
                    '_sku' => $variation->get_sku(),
                    'price' => $variation->get_price(),
                    'regular_price' => $variation->get_regular_price(),
                    'sale_price' => $variation->get_sale_price(),
                    'stock_quantity' => $variation->get_stock_quantity(),
                    'attributes' => $variation->get_attributes(),
                    'is_in_stock' => $variation->is_in_stock(),
                ];
            }
        }

        return $variations;
    }

    


    private function getMeliconTemplateData()
    {
        return [];
    }

    private function sendDataToServer(array $productData)
    {
        $response = wp_remote_post($this->apiEndpoint, [
            'method'  => 'POST',
            'body'    => wp_json_encode($productData),
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);

        if (is_wp_error($response)) {
            // Manejo de error en la conexión
            return false;
        }

        $body = wp_remote_retrieve_body($response);

        if (isset($body['data']) && !empty($body['data'])) {
            return $body['data'];
        }

        return $body;
    }


    /**
     * Función auxiliar para obtener las categorías del producto.
     */
    private function getProductCategories($product)
    {
        $categories = [];
        $terms = get_the_terms($product->get_id(), 'product_cat');

        if ($terms && !is_wp_error($terms)) {
            foreach ($terms as $term) {
                if ($term->parent == 0) {
                    $categories[] = $term->name;
                }
            }
        }

        return $categories;
    }
}
