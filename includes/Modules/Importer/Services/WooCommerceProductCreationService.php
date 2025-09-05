<?php

namespace Meliconnect\Meliconnect\Modules\Importer\Services;

use Meliconnect\Meliconnect\Core\Helpers\Helper;
use Meliconnect\Meliconnect\Core\Helpers\MeliconMeli;
use Meliconnect\Meliconnect\Core\Models\Template;

/**
 * Provides functionality to create or update products in WooCommerce
 * using transformed product data.
 */
class WooCommerceProductCreationService
{
    public function createProduct($productsData, $meliListingData)
    {
        $woo_products_ids = [];
        $woo_product = false;
        $import_settings = Helper::getMeliconnectOptions('import');

        foreach ($productsData as $product_data) {
            Helper::logData('-------------- START Processing product: ' . $product_data['title'] . '---------------- ', 'custom-import');

            //Helper::logData('Received data: ' . wp_json_encode($product_data), 'custom-import');

            if (isset($product_data['woo_product_id']) && $product_data['action'] === 'update') {
                $woo_product = wc_get_product($product_data['woo_product_id']);
            }


            // Crear una instancia del producto según su tipo
            if ($product_data['type'] === 'simple') {
                if (!$woo_product) {

                    if (isset($import_settings['melicon_import_type']) && $import_settings['melicon_import_type'] == 'onlyUpdate') {
                        Helper::logData('Product not found. Skipping creation process by setiings.', 'custom-import');
                        continue;
                    }

                    $woo_product = new \WC_Product_Simple();
                    $woo_product->save();
                }
                $this->add_simple_product_data($woo_product, $product_data);
            } elseif ($product_data['type'] === 'variable') {
                if (!$woo_product) {

                    if (isset($import_settings['melicon_import_type']) && $import_settings['melicon_import_type'] == 'onlyUpdate') {
                        Helper::logData('Product not found. Skipping creation process by setiings.', 'custom-import');
                        continue;
                    }

                    $woo_product = new \WC_Product_Variable();
                    $woo_product->save();
                }
            }

            // Create a product with basic data and saves it
            $woo_product = $this->createBaseProduct($woo_product, $product_data);

            // Manejo de errores
            if (is_wp_error($woo_product)) {
                // Lógica de manejo de errores
                Helper::logData('Error creating product: ' . $woo_product->get_error_message(), 'custom-import');
                return false;
            }



            $template_id = Template::createUpdateTemplateFromMeliListing('product', $woo_product->get_id(), $meliListingData);

            if ($template_id) {
                Template::deleteCreateTemplatesMetasFromMeliListing($template_id, $meliListingData);

                $product_data['extra_data']['postmetas']['melicon_asoc_template_id'] = $template_id;
            } else {
                Helper::logData('Error creating template for product: ' . $woo_product->get_id(), 'custom-import');
                return false;
            }

            if (isset($product_data['extra_data']['attributes']) && $product_data['extra_data']['attributes'] !== false) {
                $productAttributes = $this->createOrUpdateProductAttributes($woo_product, $product_data['extra_data']['attributes']);

                //Helper::logData('Attributes created:' . wp_json_encode($productAttributes), 'custom-import');
                Helper::logData('Attributes created and assigned to product', 'custom-import');
                //$woo_product->set_attributes($productAttributes);
            } else {
                Helper::logData('Attributes ignored by settings', 'custom-import');
            }


            if (isset($product_data['variations']) && !empty($product_data['variations'])) {

                $this->assignVariableAttributes($woo_product, $product_data['variations'], $template_id);

                $this->createProductVariations($woo_product->get_id(), $product_data['variations']);
            }

            // Crear postmetas si es necesario
            if (isset($product_data['extra_data']['postmetas'])) {
                $this->createPostmetas($woo_product->get_id(), $product_data['extra_data']['postmetas']);
            }

            $woo_product->save();


            $woo_products_ids[] = $woo_product->get_id();

            Helper::logData('------------------ END Product created with id: ' .  $woo_product->get_id() . '-------------------- ', 'custom-import');
        }

        return $woo_products_ids;
    }


    private function createBaseProduct($woo_product, $product_data)
    {
        Helper::logData('Woo Product id: ' . $woo_product->get_id(), 'custom-import');

        Helper::logData('Product data: ' . wp_json_encode($product_data), 'custom-import');

        // Asignar el título del producto
        if (isset($product_data['title']) && $product_data['title'] !== false) {
            $woo_product->set_name($product_data['title']);
        } else {
            Helper::logData('Title ignored by settings', 'custom-import');
        }

        if (isset($product_data['sku']) && $product_data['sku'] !== false) {
            // Verificar si el SKU ya existe
            $existing_product_id = Helper::get_active_product_id_by_sku($product_data['sku']);

            if ($existing_product_id) {
                // Si el SKU ya existe, puedes optar por omitir la asignación o manejarlo de otra manera
                Helper::logData('El SKU ya existe: ' . $product_data['sku'] . '. Para el producto con ID: ' . $existing_product_id, 'custom-import');
            } else {
                // Si el SKU no existe, lo asignamos al producto
                Helper::logData('SKU: ' . $product_data['sku'], 'custom-import');
                $woo_product->set_sku($product_data['sku']);
            }
        } else {
            Helper::logData('SKU ignored by settings', 'custom-import');
        }

        if (isset($product_data['gtin']) && $product_data['gtin'] !== false) {
            $woo_product->set_global_unique_id($product_data['gtin']);
            Helper::logData('GTIN: ' . $product_data['gtin'], 'custom-import');
        } else {
            Helper::logData('GTIN ignored by settings', 'custom-import');
        }

        // Descripciones
        if (isset($product_data['short_description']) && $product_data['short_description'] !== false) {
            
            if(empty($product_data['short_description'])) {
                $product_data['short_description'] = '';
            }

            $woo_product->set_short_description($product_data['short_description']);
        } else {
            Helper::logData('Short description ignored by settings', 'custom-import');
        }

        if (isset($product_data['description']) && $product_data['description'] !== false) {
            
            if (empty($product_data['description'])) {
                $product_data['description'] = '';
            }

            $woo_product->set_description($product_data['description']);
        } else {
            Helper::logData('Description ignored by settings', 'custom-import');
        }

        // Dimensiones
        if (isset($product_data['weight']) && $product_data['weight'] !== false) {
            $woo_product->set_weight($product_data['weight']);
        } else {
            Helper::logData('Weight ignored by settings', 'custom-import');
        }

        if (isset($product_data['length']) && $product_data['length'] !== false) {
            $woo_product->set_length($product_data['length']);
        } else {
            Helper::logData('Length ignored by settings', 'custom-import');
        }

        if (isset($product_data['width']) && $product_data['width'] !== false) {
            $woo_product->set_width($product_data['width']);
        } else {
            Helper::logData('Width ignored by settings', 'custom-import');
        }

        if (isset($product_data['height']) && $product_data['height'] !== false) {
            $woo_product->set_height($product_data['height']);
        } else {
            Helper::logData('Height ignored by settings', 'custom-import');
        }


        // Imágenes
        if (isset($product_data['pictures']['main_image']) && $product_data['pictures']['main_image'] !== false) {
            $this->create_or_update_main_image($woo_product->get_id(), $product_data['pictures']['main_image']);
        } else {
            Helper::logData('Main image ignored by settings', 'custom-import');
        }

        if (isset($product_data['pictures']['gallery_images']) && $product_data['pictures']['gallery_images'] !== false) {
            $this->create_or_update_gallery_images($woo_product->get_id(), $product_data['pictures']['gallery_images']);
        } else {
            Helper::logData('Gallery images ignored by settings', 'custom-import');
        }

        // Categorías y etiquetas
        if (isset($product_data['extra_data']['categories']) && $product_data['extra_data']['categories'] !== false) {
            $this->createOrUpdateCategories($woo_product->get_id(), $product_data['extra_data']['categories']);
        } else {
            Helper::logData('Categories ignored by settings', 'custom-import');
        }

        /* $tag_ids = $this->createUpdateTags($product_data['tags']); 
        $woo_product->set_tag_ids($tag_ids); */

        // Estado del producto
        if (isset($product_data['status']) && $product_data['status'] !== false) {
            $woo_product->set_status($product_data['status']); //(draft, pending, private, publish)
        } else {
            Helper::logData('Status ignored by settings', 'custom-import');
        }

        $woo_product->save();
        return $woo_product;
    }

    private function add_simple_product_data($woo_product, $product_data)
    {
        // Establecer el precio regular
        $woo_product->set_regular_price($product_data['price']);

        // Manejo del stock
        $woo_product->set_manage_stock($product_data['manage_stock']);
        $woo_product->set_stock_quantity($product_data['available_quantity']);

        // Establecer el estado del stock de forma dinámica
        $stock_status = ($product_data['available_quantity'] > 0) ? 'instock' : 'outofstock';
        $woo_product->set_stock_status($stock_status);

        return true;
    }

    private function assignVariableAttributes($woo_product, $variations)
    {
        $attributes = [];
        $variable_attrs_fromated = [];

        foreach ($variations as $variation) {
            foreach ($variation['variable_attrs'] as $attr) {
                //Helper::logData('Variable attribute: ' . wp_json_encode($attr), 'custom-import');

                $variable_attrs_fromated[$attr['name']][] = $attr['value_name'];
            }
        }

        foreach ($variable_attrs_fromated as $attr_name => $attr_options) {
            // Eliminar duplicados en las opciones de atributos
            $attr_options = array_unique($attr_options);

            // Crear o actualizar el atributo del producto usando la función helper
            $productAttributes = $this->createOrUpdateProductAttributes(
                $woo_product,
                [
                    [
                        'name' => $attr_name,
                        'options' => $attr_options,
                        'position' => 0,
                        'visible' => true,
                        'variation' => true,
                    ]
                ],
            );
        }

        return true;
    }

    /**
     * Crea las variaciones para un producto variable.
     */
    private function createProductVariations($product_id, $variations)
    {
        foreach ($variations as $variation_data) {
            $variation_id = isset($variation_data['variation_id']) ? $variation_data['variation_id'] : null;

            // Intentar encontrar una variación existente con el variation_id
            if ($variation_id) {
                $existing_variation_id = $this->getExistingVariationId($variation_id, $product_id);
            } else {
                $existing_variation_id = null;
            }

            if ($existing_variation_id) {
                // Actualizar variación existente
                $variation = new \WC_Product_Variation($existing_variation_id);
                Helper::logData('Updating variation: ' . $existing_variation_id, 'custom-import');
            } else {
                // Crear nueva variación
                $variation = new \WC_Product_Variation();
                $variation->set_parent_id($product_id);
                Helper::logData('Creating variation: ' . $product_id, 'custom-import');
            }

            $variation->set_regular_price($variation_data['price']);
            $variation->set_sku($variation_data['sku']);
            $variation->set_stock_quantity($variation_data['available_quantity']);
            $variation->set_manage_stock($variation_data['manage_stock']);


            // Asignar atributos de la variación
            $attributes = [];
            foreach ($variation_data['variable_attrs'] as $attr) {
                $attribute_name = wc_sanitize_taxonomy_name($attr['name']);
                $taxonomy_name = 'pa_' . $attribute_name;

                //Helper::logData('Attribute name: ' . $taxonomy_name, 'custom-import');

                $term = get_term_by('name', $attr['value_name'], $taxonomy_name);

                //Helper::logData('Term: ' . wp_json_encode($term), 'custom-import');

                if ($term) {
                    $attributes[$taxonomy_name] = $term->slug;
                } else {
                    //Helper::logData('Term not found for name: ' . $attr['value_name'] . ' in taxonomy: ' . $taxonomy_name, 'custom-import');
                }
            }

            Helper::logData('Attributes to set in variation: ' . wp_json_encode($attributes), 'custom-import');
            $variation->set_attributes($attributes);

            // Guardar variación
            $variation->save();

            // Guardar el variation_id como postmeta
            update_post_meta($variation->get_id(), 'melicon_meli_asoc_variation_id', $variation_id);

            $this->setNonVariableAttributes($variation, $variation_data['non_variable_attrs']);
        }
    }

    private function setNonVariableAttributes($variation, $non_variable_attrs)
    {
        $sku = '';
        $gtin = '';
        $custom_attributes = [];

        // Registrar los atributos no variables recibidos
        //Helper::logData('Non-variable attributes received: ' . wp_json_encode($non_variable_attrs), 'custom-import');

        foreach ($non_variable_attrs as $attr) {
            if ($attr['id'] === 'SELLER_SKU') {
                $sku = $attr['value_name'];
            } elseif ($attr['id'] === 'GTIN') {
                $gtin = $attr['value_name'];
            } else {
                $custom_attributes[$attr['id']] = $attr['value_name'];
            }
        }

        // Asignar SKU y registrar
        if ($sku) {
            $variation->set_sku($sku);
            //Helper::logData('Assigned SKU: ' . $sku, 'custom-import');
        }

        // Guardar GTIN como postmeta y registrar resultado
        if ($gtin) {
            $result = update_post_meta($variation->get_id(), '_global_unique_id', $gtin);
            if ($result === false) {
                //Helper::logData('Failed to update GTIN for variation ID ' . $variation->get_id(), 'custom-import');
            } else {
                //Helper::logData('Successfully updated GTIN for variation ID ' . $variation->get_id(), 'custom-import');
            }
        }

        // Asignar y guardar atributos personalizados, registrar cada uno
        foreach ($custom_attributes as $attribute_id => $attribute_value) {
            $taxonomy_name = 'pa_' . sanitize_title($attribute_id);

            $result = update_post_meta($variation->get_id(), '_' . $taxonomy_name, $attribute_value);
            /* if ($result === false) {
                Helper::logData('Failed to update custom attribute ' . $attribute_id . ' for variation ID ' . $variation->get_id(), 'custom-import');
            } else {
                Helper::logData('Successfully updated custom attribute ' . $attribute_id . ' with value ' . $attribute_value . ' for variation ID ' . $variation->get_id(), 'custom-import');
            } */
        }

        // Guardar variación y registrar resultado
        $saved = $variation->save();
        /* if (!$saved) {
            Helper::logData('Failed to save variation ID ' . $variation->get_id(), 'custom-import');
        } else {
            Helper::logData('Successfully saved variation ID ' . $variation->get_id(), 'custom-import');
        } */

        return $saved;
    }

    private function getExistingVariationId($variation_id, $product_id)
    {
        $args = [
            'post_type'   => 'product_variation',
            'post_status' => 'any',
            'meta_key'    => 'melicon_meli_asoc_variation_id',
            'meta_value'  => $variation_id,
            'posts_per_page' => 1,
            'post_parent' => $product_id
        ];

        $query = new \WP_Query($args);

        if ($query->have_posts()) {
            return $query->posts[0]->ID;
        }

        return null;
    }


    private function add_variable_product_data($woo_product, $product_data)
    {
        $variations = $product_data['variations'];

        foreach ($variations as $variation_data) {
            // Crear una nueva variación
            $variation = new \WC_Product_Variation();
            $variation->set_parent_id($woo_product->get_id());

            // Establecer los atributos de la variación
            $variation->set_attributes($variation_data['attributes']);

            // Establecer el precio y stock de la variación
            $variation->set_regular_price($variation_data['price']);
            $variation->set_stock_quantity($variation_data['available_quantity']);

            $stock_status = ($product_data['available_quantity'] > 0) ? 'instock' : 'outofstock';
            $variation->set_stock_status($stock_status);

            // Guardar la variación
            $variation_id = $variation->save();

            if (is_wp_error($variation_id)) {
                Helper::logData('Error creating variation: ' . $variation_id, 'custom-import');
                continue;
            }
        }



        return true;
    }

    private function createOrUpdateProductAttributes($product, $attrs)
    {
        $productAttributes = $product->get_attributes(); // Obtener los atributos existentes
        $log_text = "---- Start createOrUpdateProductAttributes for product ID: " . $product->get_id() . "----";

        foreach ($attrs as $attributeData) {
            $attributeName = wc_sanitize_taxonomy_name($attributeData['name']);
            $taxonomyName = 'pa_' . $attributeName;

            $log_text .= "\n\n";
            $log_text .= "Processing attribute: " . $attributeData['name'] . "\n";

            $attributeId = wc_attribute_taxonomy_id_by_name($attributeName);

            if (!$attributeId) {
                $log_text .= "Attribute not found. Creating new attribute: " . $attributeData['name'] . "\n";

                $attributeId = wc_create_attribute([
                    'name' => $attributeData['name'],
                    'slug' => $attributeName,
                    'type' => 'select',
                    'order_by' => 'menu_order',
                    'has_archives' => false,
                ]);

                if (is_wp_error($attributeId)) {
                    $log_text .= "Error creating attribute: " . $attributeId->get_error_message() . "\n";
                    continue;
                } else {
                    $log_text .= "Attribute created successfully with ID: " . $attributeId . "\n";
                }
            }

            // Asegurarse de que los términos (opciones) existan en la taxonomía del atributo
            foreach ($attributeData['options'] as $option) {
                if (!term_exists($option, $taxonomyName)) {
                    $log_text .= "Creating term: " . $option . " in taxonomy: " . $taxonomyName . "\n";
                    $term = wp_insert_term($option, $taxonomyName);

                    if (is_wp_error($term)) {
                        $log_text .= "Error creating term: " . $term->get_error_message() . "\n";
                    } else {
                        $log_text .= "Term created successfully with ID: " . $term['term_id'] . "\n";
                    }
                } else {
                    $log_text .= "Term already exists: " . $option . "\n";
                }
            }

            $attr = new \WC_Product_Attribute();
            $attr->set_id($attributeId);
            $attr->set_name($taxonomyName);

            // Obtener los IDs de los términos
            $option_ids = array_map(function ($option) use ($taxonomyName) {
                $term = get_term_by('name', $option, $taxonomyName);
                return $term ? $term->term_id : null;
            }, $attributeData['options']);
            $option_ids = array_filter($option_ids);

            $attr->set_options($option_ids);
            $attr->set_position($attributeData['position']);
            $attr->set_visible($attributeData['visible']);
            $attr->set_variation($attributeData['variation']);

            $log_text .= "Final attribute object created or updated: " . wp_json_encode($attr) . "\n";

            // Añadir o actualizar el atributo en el array existente
            $productAttributes[$taxonomyName] = $attr;
        }

        $log_text .= "\n\n\n";
        $log_text .= "Final product attributes array: " . wp_json_encode($productAttributes) . "\n";

        $product->set_attributes($productAttributes); // Guardar todos los atributos combinados
        $product->save();

        $log_text .= "Product saved with attributes: " . wp_json_encode($productAttributes) . "\n";
        $log_text .= "---- End createOrUpdateProductAttributes for product ID: " . $product->get_id() . "----";
        $log_text .= "\n\n\n";

        //Helper::logData($log_text, 'custom-import');

        return $productAttributes;
    }



    private function createOrUpdateAttributes($attrs)
    {
        $productAttributes = [];

        foreach ($attrs as $attributeData) {
            $attributeName = wc_sanitize_taxonomy_name($attributeData['name']);

            $attributeId = wc_attribute_taxonomy_id_by_name($attributeName);
            if (!$attributeId) {
                $attributeId = wc_create_attribute([
                    'name' => $attributeData['name'],
                    'slug' => $attributeName,
                    'type' => 'select',
                    'order_by' => 'menu_order',
                    'has_archives' => false,
                ]);
            }

            foreach ($attributeData['options'] as $option) {
                wp_insert_term($option, 'pa_' . $attributeName);
            }

            $productAttributes['pa_' . $attributeName] = [
                'name' => 'pa_' . $attributeName,
                'value' => implode(' | ', $attributeData['options']),
                'position' => $attributeData['position'],
                'is_visible' => $attributeData['visible'],
                'is_variation' => $attributeData['variation'],
                'is_taxonomy' => true,
            ];
        }

        return $productAttributes;
    }




    private function create_or_update_main_image($post_id, $main_image_data)
    {
        // Extraer el ID de la imagen
        $image_id = $main_image_data['id'];

        // Obtener la URL de la imagen más grande
        $image_url = $this->get_meli_largest_image_url($image_id);

        if (empty($image_url)) {
            Helper::logData('No valid image URL found for image ID: ' . $image_id, 'custom-import');
            return;
        }

        // Verificar si la imagen ya existe en la galería de medios usando el meta `melicon_meli_image_id`
        $attachment_id = $this->attachment_exists_by_meta('melicon_meli_image_id', $image_id);

        if (!$attachment_id) {
            Helper::logData('Image not exists', 'custom-import');

            // Subir la imagen si no existe
            $attachment_id = $this->upload_image_to_media_library($image_url);

            if ($attachment_id) {
                // Agregar los metadatos de la imagen al adjunto
                update_post_meta($attachment_id, 'melicon_meli_image_id', $image_id, true);
                update_post_meta($attachment_id, 'melicon_meli_image_url', $image_url, true);

                Helper::logData('Image uploaded and meta added: ' . $attachment_id, 'custom-import');
            } else {
                Helper::logData('Failed to upload image', 'custom-import');
                return;
            }
        }

        Helper::logData('Upload image with attachment ID: ' . $attachment_id, 'custom-import');

        // Verificar el post ID y el attachment ID
        if ($post_id && $attachment_id) {
            delete_post_thumbnail($post_id);

            $set_post_thumbnail = set_post_thumbnail($post_id, $attachment_id);
            if ($set_post_thumbnail) {
                Helper::logData('SUCCESS on Set main image for post ID: ' . $post_id, 'custom-import');
            } else {
                Helper::logData('ERROR on Set main image for post ID: ' . $post_id, 'custom-import');
            }
        } else {
            Helper::logData('ERROR on Set main image for post ID: ' . $post_id . '. Invalid post ID or attachment ID', 'custom-import');
        }
    }

    private function get_meli_largest_image_url($image_id)
    {
        // Obtener los datos de la imagen desde la API de Meli
        $meli_image_data = MeliconMeli::getMeliImageData($image_id);

        // Obtener el tamaño máximo de la imagen
        $meli_image_max_size = isset($meli_image_data['max_size']) ? $meli_image_data['max_size'] : 0;

        // Inicializar la URL de la imagen
        $image_url = '';

        // Buscar la URL de la imagen que coincida con el tamaño máximo
        if (isset($meli_image_data['variations']) && !empty($meli_image_data['variations'])) {
            foreach ($meli_image_data['variations'] as $image_variation) {
                if ($image_variation['size'] == $meli_image_max_size) {
                    $image_url = $image_variation['secure_url'];
                    break;
                }
            }
        }

        return $image_url;
    }

    private function attachment_exists_by_meta($meta_key, $meta_value)
    {
        global $wpdb;

        $query = $wpdb->get_var($wpdb->prepare("
            SELECT post_id FROM $wpdb->postmeta
            WHERE meta_key = %s AND meta_value = %s
            LIMIT 1
        ", $meta_key, $meta_value));

        return $query;
    }

    private function upload_image_to_media_library($image_url)
    {
        global $wp_filesystem;

        // Inicializar el sistema de archivos
        if (!function_exists('WP_Filesystem')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }

        if (!WP_Filesystem()) {
            Helper::logData('Error initializing WP_Filesystem', 'custom-import');
            return false;
        }

        // Obtener datos de la imagen usando wp_remote_get()
        $response = wp_remote_get($image_url);

        if (is_wp_error($response)) {
            Helper::logData('Error fetching image data: ' . $response->get_error_message(), 'custom-import');
            return false;
        }

        $image_data = wp_remote_retrieve_body($response);

        if (!$image_data) {
            Helper::logData('Error retrieving image body', 'custom-import');
            return false;
        }

        // Obtener directorio de uploads
        $upload_dir = wp_upload_dir();
        $filename = basename($image_url);
        $file_path = trailingslashit($upload_dir['path']) . $filename;

        // Guardar la imagen usando WP_Filesystem
        if (!$wp_filesystem->put_contents($file_path, $image_data, FS_CHMOD_FILE)) {
            Helper::logData('Failed to save image to upload directory', 'custom-import');
            return false;
        }

        // Preparar los datos para insertar en la biblioteca de medios
        $wp_filetype = wp_check_filetype($filename, null);
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title'     => sanitize_file_name($filename),
            'post_content'   => '',
            'post_status'    => 'inherit'
        );

        // Insertar el archivo en la biblioteca de medios
        $attach_id = wp_insert_attachment($attachment, $file_path);

        if (!$attach_id || is_wp_error($attach_id)) {
            Helper::logData('Error inserting attachment to media library', 'custom-import');
            return false;
        }

        // Generar metadatos para la imagen
        require_once ABSPATH . 'wp-admin/includes/image.php';
        $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
        wp_update_attachment_metadata($attach_id, $attach_data);

        return $attach_id;
    }


    private function create_or_update_gallery_images($post_id, $gallery_images_data)
    {
        $gallery_ids = [];
        $meli_seller_id = get_post_meta($post_id, 'melicon_meli_seller_id', true);

        foreach ($gallery_images_data as $image_data) {
            // Extraer el ID de la imagen
            $image_id = $image_data['id'];

            // Obtener la URL de la imagen más grande
            $image_url = $this->get_meli_largest_image_url($image_id);

            if (empty($image_url)) {
                Helper::logData('No valid image URL found for image ID: ' . $image_id, 'custom-import');
                continue;
            }

            // Verificar si la imagen ya existe en la galería de medios usando el meta `melicon_meli_image_id`
            $attachment_id = $this->attachment_exists_by_meta('melicon_meli_image_id', $image_id);

            if (!$attachment_id) {
                // Subir la imagen si no existe
                $attachment_id = $this->upload_image_to_media_library($image_url);

                if ($attachment_id) {
                    // Agregar los metadatos de la imagen al adjunto
                    update_post_meta($attachment_id, 'melicon_meli_image_id', $image_id, true);
                    update_post_meta($attachment_id, 'melicon_meli_image_url', $image_url, true);
                    update_post_meta($attachment_id, 'melicon_meli_image_seller_id', $meli_seller_id, true);
                } else {
                    Helper::logData('Failed to upload image for image ID: ' . $image_id, 'custom-import');
                    continue;
                }
            }

            // Agregar el ID del adjunto al array de IDs de la galería
            $gallery_ids[] = $attachment_id;
        }

        // Establecer la galería de imágenes para el producto
        if (!empty($gallery_ids)) {
            update_post_meta($post_id, '_product_image_gallery', implode(',', $gallery_ids));
        }
    }




    private function createOrUpdateCategories($productId, $categoryTree)
    {
        // Iniciar el proceso con la categoría raíz del árbol
        $categoryId = $this->processCategoryTree($categoryTree);

        if ($categoryId) {
            // Asignar la categoría más específica (final) al producto
            wp_set_object_terms($productId, [$categoryId], 'product_cat');
        }
    }

    private function processCategoryTree($category, $parentId = 0)
    {
        // Verificar que la categoría tenga nombre e ID
        if (!isset($category['name']) || !isset($category['id'])) {
            return null;
        }

        // Verificar si la categoría ya existe por nombre y padre
        $existingCategory = get_terms([
            'taxonomy' => 'product_cat',
            'name' => $category['name'],
            'parent' => $parentId,
            'hide_empty' => false,
        ]);

        if (!empty($existingCategory) && !is_wp_error($existingCategory)) {
            // Si la categoría ya existe, usar su ID
            $categoryId = $existingCategory[0]->term_id;
        } else {
            // Si no existe, crear la categoría
            $newCategory = wp_insert_term(
                $category['name'],
                'product_cat',
                [
                    'parent' => $parentId,
                    'slug' => sanitize_title($category['name']),
                ]
            );

            if (is_wp_error($newCategory)) {
                // Manejar errores en la creación de la categoría
                return null;
            }

            $categoryId = $newCategory['term_id'];
        }

        // Asociar el ID de MercadoLibre como un term meta en la categoría
        $meta_key = 'melicon_meli_category_id';
        $meta_value = $category['id'];
        update_term_meta($categoryId, $meta_key, $meta_value);

        // Procesar recursivamente las subcategorías
        if (isset($category['children']) && !empty($category['children'])) {
            return $this->processCategoryTree($category['children'], $categoryId);
        }

        return $categoryId;
    }

    private function processCategory($category, $parentId = 0)
    {
        //Helper::logData('processCategory: ' . wp_json_encode($category), 'custom-import');

        if (!isset($category['name'])) {
            return null;
        }

        //Helper::logData('processCategoryName: ' . wp_json_encode($category['name']), 'custom-import');
        // Verificar si la categoría ya existe por nombre y padre
        $existingCategory = get_term_by('name', $category['name'], 'product_cat');

        if ($existingCategory && $existingCategory->parent == $parentId) {
            // Si la categoría ya existe, usar su ID
            $categoryId = $existingCategory->term_id;
        } else {
            // Si no existe, crear la categoría
            $newCategory = wp_insert_term(
                $category['name'],
                'product_cat',
                [
                    'parent' => $parentId,
                    'slug' => sanitize_title($category['name']),
                ]
            );

            if (is_wp_error($newCategory)) {
                // Manejar errores en la creación de la categoría
                return null;
            }

            $categoryId = $newCategory['term_id'];
        }

        // Procesar las subcategorías (si existen)
        if (isset($category['children']) && !empty($category['children'])) {
            $this->processCategory($category['children'], $categoryId);
        }

        return $categoryId;
    }

    private function createUpdateTags($tag_name)
    {

        // Verifica si la etiqueta ya existe
        $term = term_exists($tag_name, 'product_tag');

        if ($term !== 0 && $term !== null) {
            // Si la etiqueta ya existe, devuelve el ID
            return is_array($term) ? $term['term_id'] : $term;
        } else {
            // Si la etiqueta no existe, créala
            $new_term = wp_insert_term($tag_name, 'product_tag');

            if (!is_wp_error($new_term)) {
                // Devuelve el ID de la nueva etiqueta
                return $new_term['term_id'];
            } else {
                // En caso de error, devuelve false
                return false;
            }
        }
    }

    private function createPostmetas($product_id, $meta_array)
    {
        if (!is_array($meta_array) || empty($product_id)) {
            return false;
        }

        foreach ($meta_array as $meta_key => $meta_value) {
            update_post_meta($product_id, $meta_key, $meta_value);
        }

        return true;
    }
}
