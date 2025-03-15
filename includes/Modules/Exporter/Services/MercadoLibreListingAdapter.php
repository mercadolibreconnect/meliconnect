<?php

namespace StoreSync\Meliconnect\Modules\Exporter\Services;

use StoreSync\Meliconnect\Core\Helpers\Helper;
use StoreSync\Meliconnect\Core\Helpers\MeliconMeli;
use StoreSync\Meliconnect\Core\Models\Template;
use StoreSync\Meliconnect\Core\Models\UserConnection;

/**
 * Handles communication with the external server to processs and get raw listing data
 * to send to Mercado Libre API.
 */
class MercadoLibreListingAdapter
{
    protected $apiEndpoint;

    public function __construct()
    {

            $this->apiEndpoint = 'https://meliconnect.com/api/v1/process-listing-data-to-export';
        
    }

    public function getTransformedListingData($meli_user_data, $woo_product_id, $template_id, $meliListingId = NULL)
    {

        $rawData = [
            'extra_data' => [
                'meli_user_id' => $meli_user_data->user_id,
                'woo_product_id' => $woo_product_id,
                'template_id' => $template_id,
                'meliListingId' => $meliListingId,
                'domain' => Helper::getDomainName(),
            ],
            'meli_user' => $meli_user_data,
            'export_settings' => Helper::getMeliconnectOptions('export'),
            'meli_listing' => MeliconMeli::getMercadoLibreListingData($meliListingId, $meli_user_data->access_token),
            'woo_product' => $this->getWooProductData($woo_product_id),
            'template' => $this->getMeliconTemplateData($template_id),
        ];

        //Helper::logData('Raw data sent to hub: ' . wp_json_encode($rawData), 'custom-export');

        update_post_meta($woo_product_id, 'melicon_last_export_json_sent', $rawData);

        // Enviar datos sin procesar al servidor y recibir datos transformados
        $server_response = $this->sendDataToServer($rawData);

        Helper::logData('Server response:' . $server_response, 'custom-export');

        return json_decode($server_response, true);
    }

    public function getMeliconTemplateData($template_id)
    {
        $template['data'] = Template::getTemplateData($template_id);

        if (empty($template['data'])) {
            return null;
        }
        $template['attributes'] = Template::getTemplateAttributes($template_id);
        return $template;
    }

    public function getWooProductData($woo_product_id)
    {
        $product = wc_get_product($woo_product_id);
        if (!$product) {
            return null;
        }

        $product_meta = $this->getWooProductMeta($woo_product_id);

        // Obtener la URL y los metadatos de la imagen principal
        $image_id = $product->get_image_id();
        $image_data = $this->getImageData($image_id);

        // Obtener las URLs y los metadatos de la galería de imágenes
        $gallery_image_ids = $product->get_gallery_image_ids();
        $gallery_images = [];
        foreach ($gallery_image_ids as $gallery_image_id) {
            $gallery_images[] = $this->getImageData($gallery_image_id);
        }

        $product_data = [
            'data' => [
                'id' => $product->get_id(),
                'name' => $product->get_name(),
                'price' => $product->get_price(),
                'regular_price' => $product->get_regular_price(),
                'sale_price' => $product->get_sale_price(),
                'sku' => $product->get_sku(),
                'description' => $product->get_description(),
                'short_description' => $product->get_short_description(),
                'status' => $product->get_status(),
                'date_created' => $product->get_date_created(),
                'date_modified' => $product->get_date_modified(),
                'stock_quantity' => $product->get_stock_quantity(),
                'stock_status' => $product->get_stock_status(),
                'weight' => $product->get_weight(),
                'dimensions' => $product->get_dimensions(false),
                'shipping_class' => $product->get_shipping_class(),
                'image' => $image_data,
                'gallery_images' => $gallery_images,
                'category_ids' => $product->get_category_ids(),
                'tag_ids' => $product->get_tag_ids(),
                'total_sales' => $product->get_total_sales(),
                'manage_stock' => $product->get_manage_stock(),
                'type' => $product->get_type(),
                'catalog_visibility' => $product->get_catalog_visibility(),
            ],
            'meta' => $product_meta,
        ];

        if ($product->is_type('variable')) {
            $variations = [];
            $variation_ids = $product->get_children();

            foreach ($variation_ids as $variation_id) {
                $variation = wc_get_product($variation_id);
                $variation_meta = $this->getWooProductMeta($variation_id);

                // Obtener la URL y los metadatos de la imagen de la variación
                $variation_image_id = $variation->get_image_id();
                $variation_image_data = $this->getImageData($variation_image_id);

                $variations[] = [
                    'variations_data' => [
                        'id' => $variation->get_id(),
                        'price' => $variation->get_price(),
                        'regular_price' => $variation->get_regular_price(),
                        'sale_price' => $variation->get_sale_price(),
                        'sku' => $variation->get_sku(),
                        'stock_status' => $variation->get_stock_status(),
                        'attributes' => $variation->get_attributes(),
                        'weight' => $variation->get_weight(),
                        'dimensions' => $variation->get_dimensions(false),
                        'shipping_class_id' => $variation->get_shipping_class_id(),
                        'image' => $variation_image_data,
                    ],
                    'variations_meta' => $variation_meta,
                ];
            }

            $product_data['variations'] = $variations;
        }

        return $product_data;
    }

    private function getWooProductMeta($product_id)
    {
        $meta_data = [];
        $meta_fields = get_post_meta($product_id);

        foreach ($meta_fields as $key => $value) {
            $meta_data[$key] = maybe_unserialize($value[0]);
        }

        return $meta_data;
    }

    private function getImageData($woo_image_id)
    {
        if (!$woo_image_id) {
            return null;
        }

        $image_url = wp_get_attachment_url($woo_image_id);

        // Obtiene el ID de MercadoLibre asociado desde los meta datos de la imagen en WooCommerce
        $ml_image_id = get_post_meta($woo_image_id, 'melicon_meli_image_id', true);
        $ml_image_seller_id = get_post_meta($woo_image_id, 'melicon_meli_image_seller_id', true);
        $ml_image_url = get_post_meta($woo_image_id, 'melicon_meli_image_url', true);

        // Si existe un ID de MercadoLibre, verifica que la imagen exista en MercadoLibre
        if (!empty($ml_image_id)) {
            $ml_image_data = MeliconMeli::getMeliImageData($ml_image_id);

            if (isset($ml_image_data['id']) && !empty($ml_image_data['id'])) {
                $ml_image_id = $ml_image_data['id'];
            }
        }

        // Si no hay un ID de MercadoLibre o la imagen no existe en MercadoLibre, sube la imagen
        /* if (empty($ml_image_id)) {
            $acces_token = ''; 
            $ml_image_id = MeliconMeli::uploadPictureToMeli($woo_image_id, $acces_token);
        } */

        return [
            'woo_id' => $woo_image_id,
            'ml_image_id' => !empty($ml_image_id) ? $ml_image_id : null,
            'ml_image_url' => !empty($ml_image_url) ? $ml_image_url : null,
            'ml_image_seller_id' => !empty($ml_image_seller_id) ? $ml_image_seller_id : null,
            'url' => $image_url,
        ];
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
}
