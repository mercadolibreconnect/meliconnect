# Postmeta Nomenclatures for [Nombre del Plugin]

Este documento detalla las nomenclaturas utilizadas para los postmetas en el plugin Meliconnect.

## Estructura General

Cada postmeta sigue la estructura `melicon_meli_[key]`. A continuación se describen las claves utilizadas y su propósito.

## Lista de Postmetas asociados al producto

### 1. `melicon_meli_listing_id`
**Descripción:** ID de la publicación en MercadoLibre asociada al producto.  
**Tipo de dato:** string  
**Uso:** Se utiliza para identificar de manera única la publicación de MercadoLibre relacionada al producto.

### 1. `melicon_meli_seller_id`
**Descripción:** ID del vendedor de Mercadolibre asociado a la publicación.  
**Tipo de dato:** string  
**Uso:** Se la utiliza para identificar al vendedor de MercadoLibre, asociado con la publicación.


### 1. `melicon_meli_image_id`
**Descripción:** ID de la imagen de Mercadolibre asociado a la imagen de una publicación.  
**Tipo de dato:** string  
**Uso:** Para saber si la imagen ya fue cargada en Woocommerce 

### 1. `melicon_meli_image_url`
**Descripción:** URL de la imagen de Mercadolibre asociado a la imagen de una publicación.  
**Tipo de dato:** string  
**Uso:** Se la utiliza para almacenar la url de la imagen de mercadolibre

### 1. `melicon_meli_image_seller_id`
**Descripción:** ID del vendedor de Mercadolibre asociado a la iagen de una publicación.  
**Tipo de dato:** string  
**Uso:** Se la utiliza para identificar al vendedor de MercadoLibre, asociado con la iamgen de una publicación. Sirve para subir la imagen a meli



### 1. `melicon_meli_asoc_variation_id`
**Descripción:** Id de la variacion de mercadolibre asociada  a cada variación de producto.  
**Tipo de dato:** string  
**Uso:** Si el id existe la variación de MercadoLibre se actualiza, de lo contrario se crea.

### 1. `melicon_meli_asoc_variation_sync_disabled`
**Descripción:** Guarda si la variación de wocommerce se exporta o sincroniza con mercadolibre o no.  
**Tipo de dato:** boolean  
**Uso:** Si el id existe la variación de MercadoLibre se actualiza, de lo contrario se crea.



### 2. `melicon_meli_permalink`
**Descripción:** Enlace permanente (URL) de la publicación del producto en MercadoLibre.  
**Tipo de dato:** string  
**Uso:** Permite acceder directamente a la página del producto en MercadoLibre.

### 3. `melicon_meli_listing_type_id`
**Descripción:** ID del tipo de tipo de publicación en MercadoLibre (e.g., clásico, premium).  
**Tipo de dato:** string  
**Uso:** Determina el tipo de publicación que tiene el producto en MercadoLibre.

### 4. `melicon_meli_category_id`
**Descripción:** ID de la categoría en MercadoLibre donde se encuentra el producto.  
**Tipo de dato:** string  
**Uso:** Especifica la categoría a la que pertenece el producto en MercadoLibre.

### 5. `melicon_meli_status`
**Descripción:** Estado actual de la lista en MercadoLibre (e.g., activa, pausada).  
**Tipo de dato:** string  
**Uso:** Indica si el producto está disponible, pausado o finalizado en MercadoLibre.

### 6. `melicon_meli_sub_status`
**Descripción:** Subestado de la lista en MercadoLibre (e.g., en revisión, pendiente).  
**Tipo de dato:** string  
**Uso:** Proporciona información adicional sobre el estado del producto.

### 7. `melicon_meli_site_id`
**Descripción:** ID del sitio de MercadoLibre (e.g., MLA para Argentina).  
**Tipo de dato:** string  
**Uso:** Identifica el país o la región en la que se está vendiendo el producto.

### 8. `melicon_meli_catalog_product_id`
**Descripción:** ID del producto en el catálogo de MercadoLibre, si está asociado a uno.  
**Tipo de dato:** string  
**Uso:** Se utiliza cuando el producto está vinculado a un catálogo oficial de MercadoLibre.

### 9. `melicon_meli_domain_id`
**Descripción:** ID del dominio en MercadoLibre (e.g., muebles, tecnología).  
**Tipo de dato:** string  
**Uso:** Especifica el dominio o la categoría de alto nivel en la que se encuentra el producto.

### 10. `melicon_meli_channels`
**Descripción:** Canales de venta asociados al producto en MercadoLibre.  
**Tipo de dato:** array  
**Uso:** Indica los diferentes canales o puntos de venta donde está publicado el producto.

### 11. `melicon_meli_sold_quantity`
**Descripción:** Cantidad de unidades vendidas del producto en MercadoLibre.  
**Tipo de dato:** integer  
**Uso:** Lleva el registro de la cantidad de ventas realizadas a través de MercadoLibre.

### 12. `melicon_meli_shipping_mode`
**Descripción:** Modo de envío seleccionado para el producto (e.g., mercado envíos, a cargo del vendedor).  
**Tipo de dato:** string  
**Uso:** Define cómo se manejará el envío del producto a los compradores.




### 15. `melicon_matched_template_id`
**Descripción:** ID del template con el que el suuario hizo match para exportar el producto.  
**Tipo de dato:** integer  
**Uso:** Usado en el exportador masivo para asociar con un template existente productos que no tengan template vinculado


### 16. `melicon_matched_listing_id`
**Descripción:** ID de la publicación de Mercadoloibre con el que el suuario hizo match para exportar el producto.  
**Tipo de dato:** integer  
**Uso:** Usado en el exportador masivo para asociar con una publicación existente  productos que no tengan vinculación previa.

### 16. `melicon_matched_listing_by`
**Descripción:** Tipo de match ('sku','gtin', 'custom').  
**Tipo de dato:** string  
**Uso:** Usado en el exportador masivo para asociar con una publicación existente  productos que no tengan vinculación previa.

### 17. `melicon_asoc_template_id`
**Descripción:** ID del template vinculado al producto.  
**Tipo de dato:** integer  
**Uso:** Usado en el exportador masivo, una ves que se hiz match y se realizo la exportación y la misma fue correcta. El template queda vinculado. 



### 17. `melicon_export_meli_errors`
**Descripción:** Last export error item and description error. When the item was exported. If export is success it is deleted.  
**Tipo de dato:** string  
**Uso:** Usado en el exportador masivo.

### 17. `melicon_export_meli_error_time`
**Descripción:** Last export error time when the item and description was exported. If export is success it is deleted.  
**Tipo de dato:** string  
**Uso:** Usado en el exportador masivo. 

<!-- ### 17. `melicon_item_export_meli_error`
**Descripción:** Last export error when the item was exported. If export is success it is deleted.  
**Tipo de dato:** string  
**Uso:** Usado en el exportador masivo.  -->

### 17. `melicon_item_export_meli_error_time`
**Descripción:** Last export error time when the item was exported. If export is success it is deleted.  
**Tipo de dato:** string  
**Uso:** Usado en el exportador masivo. 


<!-- ### 17. `melicon_description_export_meli_error`
**Descripción:** Last export error when the item was exported. If export is success it is deleted.  
**Tipo de dato:** string  
**Uso:** Usado en el exportador masivo.  -->

### 17. `melicon_description_export_meli_error_time`
**Descripción:** Last export error time when the item was exported. If export is success it is deleted.  
**Tipo de dato:** string  
**Uso:** Usado en el exportador masivo. 


### 18. `melicon_last_export_json_sent`
**Descripción:** ASociated to a product. shows last export json sent to MercadoLibre.  
**Tipo de dato:** string  
**Uso:** Usado en el exportador masivo. 



## Notas Adicionales

- Asegúrate de mantener este documento actualizado a medida que se añaden o modifican postmetas.
- Todos los valores deben ser validados y sanitizados antes de su almacenamiento para evitar inconsistencias y problemas de seguridad.
