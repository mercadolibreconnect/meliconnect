<?php

namespace StoreSync\Meliconnect\Modules\Exporter\Services;

use StoreSync\Meliconnect\Core\Helpers\Helper;
use StoreSync\Meliconnect\Modules\Exporter\Models\ProductToExport;

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class ExportProductsTable extends \WP_List_Table
{
    public function __construct()
    {
        parent::__construct([
            'singular' => __('Exported Product', 'meliconnect'), // Singular name of the listed records
            'plural'   => __('Exported Products', 'meliconnect'), // Plural name of the listed records
            'ajax'     => false
        ]);

        // Check if table is empty and fill it with WooCommerce products if needed
        $this->maybe_fill_products_table();
    }

    // Define the columns to display in the table
    public function get_columns()
    {
        $columns = [
            'cb' => '<input type="checkbox" />',
            'woo_product_name'      => '<div style="text-align: center; font-weight: bold">' . __('Product Name', 'meliconnect') . '</div>',
            'product_id'            => '<div style="font-weight: bold">' . __('Woo Product Data', 'meliconnect') . '</div>',
            'has_template_vinculation' => '<div style="text-align: center; font-weight: bold">' . __('Template', 'meliconnect') . '</div>',
            'has_listing_vinculation' => '<div style="text-align: center; font-weight: bold">' . __('Meli Listing', 'meliconnect') . '</div>',
            'export_status'         => '<div style="text-align: center; font-weight: bold">' . __('Export Status', 'meliconnect') . '</div>',
        ];

        return $columns;
    }

    // Retrieve the data from the database
    public static function get_products_data($per_page, $page_number)
    {
        $orderby = !empty($_REQUEST['orderby']) ? sanitize_key($_REQUEST['orderby']) : 'woo_product_name';
        $order = !empty($_REQUEST['order']) ? sanitize_key($_REQUEST['order']) : 'asc';

        global $wpdb;
        $table_name = $wpdb->prefix . 'melicon_products_to_export';

        list($where_sql, $query_params) = self::build_filters_query($_REQUEST);



        if ($per_page == -1) {
            $sql = "SELECT * FROM {$table_name} {$where_sql} ORDER BY {$orderby} {$order}";
        } else {

            $offset = ($page_number - 1) * $per_page;
            $query_params[] = $per_page;
            $query_params[] = $offset;
            $sql = "SELECT * FROM {$table_name} {$where_sql} ORDER BY {$orderby} {$order} LIMIT %d OFFSET %d";
        }


        return $wpdb->get_results($wpdb->prepare($sql, $query_params), ARRAY_A);
    }

    // Prepare the items for the table to display
    public function prepare_items()
    {
        $columns  = $this->get_columns();
        $hidden   = [];
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = [$columns, $hidden, $sortable];

        $per_page = isset($_REQUEST['export_products_per_page']) ? (int) $_REQUEST['export_products_per_page'] : $this->get_items_per_page('export_products_per_page', 10);

        //$per_page = $this->get_items_per_page('export_products_per_page', 10);
        $current_page = $this->get_pagenum();

        // Obtener el conteo total basado en los filtros
        $total_items = self::record_count();

        // Configurar la paginación
        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page' => $per_page,
        ]);

        $this->items = self::get_products_data($per_page, $current_page);
    }

    public function get_items_per_page($option, $default = 10)
    {
        $per_page = get_user_option($option, get_current_user_id());

        if ($per_page === false) {
            $per_page = $default;
        }

        // Permitir "Todos" con un valor de -1
        return (int) $per_page;
    }

    // Default method for displaying a column
    public function column_default($item, $column_name)
    {
        if (isset($item[$column_name])) {
            return $item[$column_name];
        }

        return print_r($item, true);
    }

    public function column_cb($item)
    {
        return sprintf('<input type="checkbox" name="bulk-actions[]"  class="bulk-checkbox" value="%s" />', $item['woo_product_id']);
    }

    public function column_product_id($item)
    {
        $woo_product = wc_get_product($item['woo_product_id']);

        if (!$woo_product) {
            return __('No se encontró un producto con el ID: ', 'meliconnect') . $item['woo_product_id'];
        }

        $type = $woo_product->get_type();
        $price = $woo_product->get_price();
        $stock_quantity = $woo_product->get_stock_quantity();
        $sold_quantity = $woo_product->get_total_sales();
        $sku = $woo_product->get_sku();
        $gtin = $woo_product->get_meta('_gtin'); // Asegúrate de que el meta key sea el correcto para GTIN
        $product_type_class = $type === 'simple' ? 'has-text-link' : 'has-text-success';

?>
        <style>
            .wp-list-table .column-product_id {
                width: 40%;
            }
        </style>
        <div class="columns is-multiline">
            <!-- Primera Columna -->
            <div class="column is-5">
                <strong><?php _e('ID', 'meliconnect'); ?>:</strong> <?php echo esc_html($item['woo_product_id']); ?><br>
                <strong><?php _e('SKU', 'meliconnect'); ?>:</strong> <?php echo esc_html($sku ? $sku : __('No SKU', 'meliconnect')); ?><br>
                <strong><?php _e('GTIN', 'meliconnect'); ?>:</strong> <?php echo esc_html($gtin ? $gtin : __('No GTIN', 'meliconnect')); ?><br>
                <strong><?php _e('Product Type', 'meliconnect'); ?>:</strong> <?php echo Helper::meliconnectPrintColorText($type, $product_type_class); ?><br>
            </div>

            <!-- Segunda Columna -->
            <div class="column is-6">
                <strong><?php _e('Price', 'meliconnect'); ?>:</strong> <?php echo esc_html($price); ?><br>
                <strong><?php _e('Sold Quantity', 'meliconnect'); ?>:</strong> <?php echo esc_html($sold_quantity); ?><br>
                <strong><?php _e('Available Quantity', 'meliconnect'); ?>:</strong> <?php echo esc_html($stock_quantity); ?>
            </div>
        </div>
<?php

    }

    public function column_woo_product_name($item)
    {
        $text = $item['woo_product_name'];

        $actions = [
            'view' => sprintf('<a href="%s" target="_blank">' . __('View', 'meliconnect') . '</a>', esc_url(get_permalink($item['woo_product_id']))),
            'edit' => sprintf('<a href="%s" target="_blank">' . __('Edit', 'meliconnect') . '</a>', esc_url(get_edit_post_link($item['woo_product_id']))),
        ];

        return '<div style="text-align: center;">' .  $text . $this->row_actions($actions) . '</div>';
    }

    public function column_has_template_vinculation($item)
    {
        $meta_matched_template = get_post_meta($item['woo_product_id'], 'melicon_matched_template_id', true);
        $meta_asoc_template_id = get_post_meta($item['woo_product_id'], 'melicon_asoc_template_id', true);

        $text = ($meta_matched_template || $meta_asoc_template_id)
            ? Helper::meliconnectPrintTag(__('Using Template', 'meliconnect'), 'is-warning')
            : Helper::meliconnectPrintTag(__('To create', 'meliconnect'), 'is-danger');

        $subText = '';

        if ($meta_matched_template) {
            $subText .= '<p class="mt-2">' . Helper::meliconnectPrintTag(__('Custom match', 'meliconnect'), 'is-link is-light') . '</p>';
            $actions = [
                'clear-match' => sprintf(
                    '<a href="#" class="melicon-clear-product-match" data-woo-product-id="%s">' . __('Clear Match', 'meliconnect') . '</a>',
                    esc_attr($item['woo_product_id'])
                )
            ];
        } else {

            if ($meta_asoc_template_id) {
                /* $actions = [
                    'edit' => sprintf('<a href="%s" target="_blank">' . __('Edit', 'meliconnect') . '</a>', esc_url(get_edit_post_link($item['woo_product_id']))),
                    'delete-vinculation' => sprintf('<a href="#" class="melicon-delete-template-vinculation" data-woo-product-id="%d">' . __('Desvinculate', 'meliconnect') . '</a>', esc_attr($item['woo_product_id']))
                ]; */
                $actions = [];
            } else {
                $actions = [
                    'find-a-match' => sprintf(
                        '<a href="#" class="melicon-find-template-to-match js-modal-trigger" 
                            data-woo-product-id ="%s"
                            data-target="melicon-find-match-modal">' . __('Find match', 'meliconnect') . '</a>',
                        esc_attr($item['woo_product_id']),
                    ),
                ];
            }
        }

        return '<div style="text-align: center;">' .  $text . $subText . $this->row_actions($actions) . '</div>';
    }

    public function column_has_listing_vinculation($item)
    {

        $meta_asoc_listing = ($item['vinculated_listing_id']) ? true : false;

        $text = ($meta_asoc_listing)
            ? Helper::meliconnectPrintTag(__('To update', 'meliconnect'), 'is-warning')
            : Helper::meliconnectPrintTag(__('To create', 'meliconnect'), 'is-danger');



        if ($meta_asoc_listing && $item['meli_permalink']) {
            $actions = [
                'view' => sprintf('<a href="%s" target="_blank">' . __('View', 'meliconnect') . '</a>', esc_url($item['meli_permalink'])),
                'delete-vinculation' => sprintf('<a href="#" class="melicon-delete-listing-vinculation" data-woo-product-id="%d">' . __('Desvinculate', 'meliconnect') . '</a>', esc_attr($item['woo_product_id']))
            ];
        } else {
            $actions = [
                'vinculate-listing' => sprintf(
                    '<a href="#" class="melicon-find-listing-to-vinculate js-modal-trigger" 
                                data-woo-product-id ="%s"
                                data-target="melicon-find-vinculate-modal">' . __('Vinculate', 'meliconnect') . '</a>',
                    esc_attr($item['woo_product_id']),
                )
            ];
        }


        return '<div style="text-align: center;">' .  $text  . $this->row_actions($actions) . '</div>';
    }

    public function column_export_status($item)
    {
        $last_json_sent = get_post_meta($item['woo_product_id'], 'melicon_last_export_json_sent', true);
        $escaped_last_json_sent = self::unserialize_column_data($last_json_sent);

        $html_last_json_sent_link = '<p><a href="#" class="melicon-toggle-json" data-json-sent="' . $escaped_last_json_sent . '">' . __('Last Json Sent', 'meliconnect') . '</a></p>';
        
        switch ($item['export_status']) {

            case 'processing':
                $html = Helper::meliconnectPrintTag(__('Processing', 'meliconnect'), 'is-info');
                break;
            case 'paused':
                $html = Helper::meliconnectPrintTag(__('Paused', 'meliconnect'), 'is-secondary');
                break;
            case 'failed':
                $html = Helper::meliconnectPrintTag(__('Failed', 'meliconnect'), 'is-danger');

                if (isset($item['export_error']) && !empty($item['export_error'])) {

                    // Escapar los datos para evitar problemas de seguridad
                    $escaped_json_errors = self::unserialize_column_data($item['export_error']);

                    // Añadir el enlace para mostrar los errores
                    $html .= '<p class="mb-0"><a href="#" class="melicon-toggle-error" data-error="' . $escaped_json_errors . '">' . __('Show error', 'meliconnect') . '</a></p>';
                }

                $html .= $html_last_json_sent_link;

                break;
            case 'finished':
                $html = Helper::meliconnectPrintTag(__('Exported', 'meliconnect'), 'is-success');
                $html .= $html_last_json_sent_link;
                break;
            default:
                $html = Helper::meliconnectPrintTag(__('Pending', 'meliconnect'), 'is-warning');
                break;
        }

        

        return '<div style="text-align: center;">' . $html . '</div>';
    }

    private static function unserialize_column_data($column_data)
    {

        $unserialized = maybe_unserialize($column_data);
        $json = json_encode($unserialized);

        $escaped_json = esc_attr($json);
        return maybe_unserialize($escaped_json);
    }


    // Define sortable columns
    protected function get_sortable_columns()
    {
        return [
            'woo_product_name' => ['woo_product_name', false],
        ];
    }

    //Custom methods
    private function maybe_fill_products_table()
    {
        /* $products_to_export_count = ProductToExport::count_products_to_export();


        if ($products_to_export_count == 0) { */

        $woo_active_products = Helper::get_woo_active_products();

        ProductToExport::fill_products_table($woo_active_products);
        /* } */
    }

    private static function build_filters_query()
    {
        global $wpdb;

        $where_clauses = [];
        $query_params = [];

        $filters = [
            'search' => isset($_REQUEST['search']) ? $_REQUEST['search'] : '',
            'vinculation' => isset($_REQUEST['product_vinculation_filter']) ? $_REQUEST['product_vinculation_filter'] : '',
            'listing_type' => isset($_REQUEST['product_type_filter']) ? $_REQUEST['product_type_filter'] : '',
        ];


        if (!empty($filters['search'])) {
            $search = '%' . $wpdb->esc_like($filters['search']) . '%';
            $where_clauses[] = "(woo_product_name LIKE %s OR woo_sku LIKE %s OR woo_gtin LIKE %s)";
            $query_params[] = $search;
            $query_params[] = $search;
            $query_params[] = $search;
        }

        if (!empty($filters['vinculation'])) {
            if ($filters['vinculation'] == 'yes_product') {
                $where_clauses[] = "vinculated_listing_id IS NOT NULL AND vinculated_listing_id > 0";
            } elseif ($filters['vinculation'] == 'no_product') {
                $where_clauses[] = "vinculated_listing_id IS NULL OR vinculated_listing_id = 0";
            } elseif ($filters['vinculation'] == 'yes_template') {
                $where_clauses[] = "vinculated_template_id IS NOT NULL AND vinculated_template_id > 0";
            } elseif ($filters['vinculation'] == 'no_template') {
                $where_clauses[] = "vinculated_template_id IS NULL OR vinculated_template_id = 0";
            }
        }

        if (!empty($filters['listing_type'])) {
            $where_clauses[] = "woo_product_type = %s";
            $query_params[] = $filters['listing_type'];
        }

        $where_sql = '';
        if (!empty($where_clauses)) {
            $where_sql = ' WHERE ' . implode(' AND ', $where_clauses);
        } else {
            $where_sql = ' WHERE 1=%d';
            $query_params[] = 1;
        }



        return [$where_sql, $query_params];
    }

    public static function record_count()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'melicon_products_to_export';

        // Construir las cláusulas WHERE y los parámetros de consulta
        list($where_sql, $query_params) = self::build_filters_query();

        // Construir la consulta SQL
        $sql = "SELECT COUNT(*) FROM {$table_name} {$where_sql}";

        // Si hay parámetros en la consulta, usar prepare, de lo contrario ejecutar directamente
        if (!empty($query_params)) {
            $sql = $wpdb->prepare($sql, $query_params);
        }

        // Ejecutar la consulta
        return $wpdb->get_var($sql);
    }
}
