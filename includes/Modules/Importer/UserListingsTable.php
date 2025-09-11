<?php

namespace Meliconnect\Meliconnect\Modules\Importer;

use Meliconnect\Meliconnect\Core\Helpers\Helper;

// includes/Core/Controllers/ProductsListTableController.php
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class UserListingsTable extends \WP_List_Table
{

    public function __construct()
    {
        parent::__construct([
            'singular' => esc_html__('User Listing', 'meliconnect'), // Singular name of the listed records.
            'plural'   => esc_html__('User Listings', 'meliconnect'), // Plural name of the listed records.
            'ajax'     => false // Does this table support ajax?
        ]);
    }





    public static function get_user_listings($per_page, $page_number, $filters = [], $orderby = 'meli_listing_title', $order = 'asc')
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'melicon_user_listings_to_import';

        // Construir las cláusulas WHERE y los parámetros de consulta
        list($where_sql, $query_params) = self::build_filters_query($filters);

        $offset = ($page_number - 1) * $per_page;

        // Añadir los parámetros de paginación a los parámetros de consulta
        $query_params[] = $per_page;
        $query_params[] = $offset;


        return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table_name} {$where_sql} ORDER BY {$orderby} {$order} LIMIT %d OFFSET %d", $query_params), ARRAY_A);
    }

    private static function build_filters_query($filters)
    {
        global $wpdb;

        $where_clauses = [];
        $query_params = [];

        $filters = [
            'search'      => isset($_REQUEST['search']) ? sanitize_text_field(wp_unslash($_REQUEST['search'])) : '',
            'vinculation' => isset($_REQUEST['vinculation_filter']) ? sanitize_text_field(wp_unslash($_REQUEST['vinculation_filter'])) : '',
            'listing_type' => isset($_REQUEST['listing_type_filter']) ? sanitize_text_field(wp_unslash($_REQUEST['listing_type_filter'])) : '',
            'seller_id'   => isset($_REQUEST['seller_filter']) ? sanitize_text_field(wp_unslash($_REQUEST['seller_filter'])) : '',
            'status'      => isset($_REQUEST['listing_status_filter']) ? sanitize_text_field(wp_unslash($_REQUEST['listing_status_filter'])) : '',
        ];


        if (!empty($filters['search'])) {
            $search = '%' . $wpdb->esc_like($filters['search']) . '%';
            $where_clauses[] = "(meli_listing_title LIKE %s OR meli_sku LIKE %s OR meli_listing_id LIKE %s)";
            $query_params[] = $search;
            $query_params[] = $search;
            $query_params[] = $search;
        }

        if (!empty($filters['vinculation'])) {
            if ($filters['vinculation'] == 'yes_product') {
                $where_clauses[] = "vinculated_product_id IS NOT NULL AND vinculated_product_id > 0";
            } elseif ($filters['vinculation'] == 'no_product') {
                $where_clauses[] = "vinculated_product_id IS NULL OR vinculated_product_id = 0";
            } elseif ($filters['vinculation'] == 'yes_template') {
                $where_clauses[] = "vinculated_template_id IS NOT NULL AND vinculated_template_id > 0";
            } elseif ($filters['vinculation'] == 'no_template') {
                $where_clauses[] = "vinculated_template_id IS NULL OR vinculated_template_id = 0";
            }
        }

        if (!empty($filters['listing_type'])) {
            $where_clauses[] = "meli_product_type = %s";
            $query_params[] = $filters['listing_type'];
        }

        if (!empty($filters['seller_id']) && $filters['seller_id'] != 'all') {
            $where_clauses[] = "meli_user_id = %s";
            $query_params[] = $filters['seller_id'];
        }

        if (!empty($filters['status']) && $filters['status'] != 'all') {
            if ($filters['status'] == 'active') {
                $where_clauses[] = "meli_status = %s";
                $query_params[] = 'active';
            } else {
                $where_clauses[] = "meli_status != %s";
                $query_params[] = 'active';
            }
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


    public static function record_count($filters = [])
    {
        global $wpdb;

        // Nombre de la tabla (seguro con $wpdb->prefix)
        $table_name = $wpdb->prefix . 'melicon_user_listings_to_import';

        // Construir las cláusulas WHERE y los parámetros de consulta
        list($where_sql, $query_params) = self::build_filters_query($filters);

        // Ejecutar la consulta directamente con prepare si hay parámetros
        return !empty($query_params)
            ? (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$table_name} {$where_sql}", $query_params))
            : (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table_name} {$where_sql}");
    }

    public function no_items()
    {
        esc_html_e('No user listings available.', 'meliconnect');
    }

    public function column_default($item, $column_name)
    {
        if (isset($item[$column_name])) {
            return $item[$column_name];
        }

        return esc_html__('No data available', 'meliconnect');
    }

    public function column_meli_listing_title($item)
    {
        $title = '<strong>' . $item['meli_listing_title'] . '</strong>';
        $response = json_decode($item['meli_response'], true);
        $permalink = isset($response['permalink']) ? $response['permalink'] : '#';

        $actions = [
            'view' => sprintf('<a href="%s" target="_blank">View</a>', esc_url($permalink)),

        ];

        return $title . $this->row_actions($actions);
    }

    public function column_has_product_vinculation($item)
    {
        $text = $item['vinculated_product_id']
            ? Helper::meliconnectPrintTag(esc_html__('To update', 'meliconnect'), 'melicon-is-warning')
            : Helper::meliconnectPrintTag(esc_html__('To create', 'meliconnect'), 'melicon-is-danger');

        $subText = '';

        if ($item['vinculated_product_id']) {
            $matchMessages = [
                'is_product_match_by_name' => esc_html__('Match by same Name', 'meliconnect'),
                'is_product_match_by_sku'  => esc_html__('Match by same SKU', 'meliconnect'),
                'is_product_match_manually'  => esc_html__('Custom Match', 'meliconnect')
            ];

            foreach ($matchMessages as $key => $message) {
                if (!empty($item[$key])) {
                    $subText .= '<p class="mt-2">' . Helper::meliconnectPrintTag($message, ' melicon-is-link melicon-is-light') . '</p>';
                }
            }
        }

        $hasMatch = ($item['is_product_match_by_name'] == 1 || $item['is_product_match_by_sku'] == 1 || $item['is_product_match_manually'] == 1) && isset($item['vinculated_product_id']) && !empty($item['vinculated_product_id']);
        $product_type = esc_sql($item['product_type'] ?? 'simple');
        $meli_response = json_decode($item['meli_response'], true);

        if ($hasMatch) {
            // Si hay coincidencia, mostrar solo la opción "Clear Match"
            $actions = [
                'clear-match' => sprintf(
                    '<a href="#" class="melicon-clear-product-match" data-meli-listing-id="%s">' . esc_html__('Clear Match', 'meliconnect') . '</a>',
                    esc_attr($item['meli_listing_id'])
                )
            ];
        } else {
            $actions = $item['vinculated_product_id']
                ? [
                    'view' => sprintf('<a href="%s" target="_blank">' . esc_html__('View', 'meliconnect') . '</a>', esc_url(get_permalink($item['vinculated_product_id']))),
                    'edit' => sprintf('<a href="%s" target="_blank">' . esc_html__('Edit', 'meliconnect') . '</a>', esc_url(get_edit_post_link($item['vinculated_product_id']))),
                    'delete-vinculation' => sprintf('<a href="#" class="melicon-delete-product-vinculation" data-product-type="%s" data-woo-product-id="%d" data-meli-listing-id="%s">' . esc_html__('Desvinculate', 'meliconnect') . '</a>', esc_attr($product_type), esc_attr($item['vinculated_product_id']), esc_attr($item['meli_listing_id']))
                ]
                : [
                    'find-a-match' => sprintf(
                        '<a href="#" class="melicon-find-product-to-match melicon-js-modal-trigger" 
                    data-user-listing-id ="%s"
                    data-product-type="%s"  
                    data-listing-id="%s"
                    data-meli-listing-title="%s"
                    data-meli-listing-type="%s" 
                    data-meli-sku="%s" 
                    data-meli-status="%s" 
                    data-price="%s" 
                    data-available-quantity="%s" 
                    data-target="melicon-find-match-modal">' . esc_html__('Find match', 'meliconnect') . '</a>',
                        esc_attr($item['id']),
                        esc_attr($product_type),
                        esc_attr($item['meli_listing_id']),
                        esc_attr($item['meli_listing_title']),
                        esc_attr($item['meli_product_type']),
                        esc_attr($item['meli_sku']),
                        esc_attr($item['meli_status']),
                        esc_attr($meli_response['price']),
                        esc_attr($meli_response['available_quantity'])
                    )
                ];
        };

        return '<div style="text-align: center;">' .  $text . $subText . $this->row_actions($actions) . '</div>';
    }


    public function column_meli_listing_id($item)
    {
        // Decodificar la respuesta JSON de MercadoLibre
        $meli_response = json_decode($item['meli_response'], true);

        $product_type_class = $item['meli_product_type'] === 'simple' ? 'has-text-link' : 'has-text-success';
        $status_class = $item['meli_status'] === 'active' ? 'has-text-success' : 'has-text-danger';


?>

        <div class="melicon-columns melicon-is-multiline">
            <!-- Primera Columna -->
            <div class="melicon-column melicon-is-5">
                <strong><?php esc_html_e('ID', 'meliconnect'); ?>:</strong> <?php echo esc_html($item['meli_listing_id']); ?><br>
                <strong><?php esc_html_e('User ID', 'meliconnect'); ?>:</strong> <?php echo esc_html($item['meli_user_id']); ?><br>
                <?php if (!empty($item['meli_sku'])): ?>
                    <strong><?php esc_html_e('SKU', 'meliconnect'); ?>:</strong> <?php echo esc_html($item['meli_sku']); ?>
                <?php endif; ?>
                <strong><?php esc_html_e('Status', 'meliconnect'); ?>:</strong>
                <span class="melicon-color-text <?php echo esc_attr($status_class); ?>"> <?php echo esc_html($item['meli_status']); ?></span>
                <br>
                <strong><?php esc_html_e('Product Type', 'meliconnect'); ?>:</strong>
                <span class="melicon-color-text <?php echo esc_attr($product_type_class); ?>"> <?php echo esc_html($item['meli_product_type']); ?></span>
                <strong><?php esc_html_e('Listing Type', 'meliconnect'); ?>:</strong> <?php echo esc_html($meli_response['listing_type_id']); ?>
            </div>

            <!-- Segunda Columna -->
            <div class="melicon-column melicon-is-6">
                <strong><?php esc_html_e('Price', 'meliconnect'); ?>:</strong> <?php echo esc_html($meli_response['price']); ?><br>
                <strong><?php esc_html_e('Sold Quantity', 'meliconnect'); ?>:</strong> <?php echo esc_html($meli_response['sold_quantity']); ?><br>
                <strong><?php esc_html_e('Available Quantity', 'meliconnect'); ?>:</strong> <?php echo esc_html($meli_response['available_quantity']); ?>
            </div>
        </div>
<?php
    }

    public function column_has_template_vinculation($item)
    {
        $text = $item['vinculated_template_id']
            ? Helper::meliconnectPrintTag(esc_html__('Using template', 'meliconnect'), 'melicon-is-warning')
            : Helper::meliconnectPrintTag(esc_html__('To create', 'meliconnect'), 'melicon-is-danger');

        $actions = [];

        if (! empty($item['vinculated_template_id'])) {
            $url = add_query_arg(
                [
                    'page'    => isset($_REQUEST['page']) ? sanitize_key(wp_unslash($_REQUEST['page'])) : '',
                    'action'  => 'delete',
                    'listing' => absint($item['id']),
                ],
                admin_url('admin.php')
            );

            $actions['delete-vinculation'] = sprintf(
                '<a class="melicon-delete-template-vinculation" data-listing-id="%s" data-template-id="%s" href="%s">%s</a>',
                esc_attr($item['meli_listing_id']),
                esc_attr($item['vinculated_template_id']),
                esc_url($url),
                esc_html__('Desvinculate', 'meliconnect')
            );
        } else {
            // Si en algún momento querés habilitar el "Find match"
            // podés construirlo de forma similar con add_query_arg y esc_url().
            $actions = [];
        }

        return $text . $this->row_actions($actions);
    }


    public function column_import_status($item)
    {
        switch ($item['import_status']) {

            case 'processing':
                $html = Helper::meliconnectPrintTag(esc_html__('Processing', 'meliconnect'), 'melicon-is-info');
                break;
            case 'paused':
                $html = Helper::meliconnectPrintTag(esc_html__('Paused', 'meliconnect'), 'melicon-is-secondary');
                break;
            case 'failed':
                $html = Helper::meliconnectPrintTag(esc_html__('Failed', 'meliconnect'), 'melicon-is-danger');
                break;
            case 'finished':
                $html = Helper::meliconnectPrintTag(esc_html__('Imported', 'meliconnect'), 'melicon-is-success');
                break;
            default:
                $html = Helper::meliconnectPrintTag(esc_html__('Pending', 'meliconnect'), 'melicon-is-warning');
                break;
        }

        return '<div style="text-align: center;">' . $html . '</div>';
    }

    public function column_cb($item)
    {
        return sprintf('<input type="checkbox" name="bulk-actions[]"  class="bulk-checkbox" value="%s" />', $item['meli_listing_id']);
    }


    public function get_columns()
    {
        $columns = [
            'cb' => '<input type="checkbox" />',
            'meli_listing_title' => esc_html__('Listing', 'meliconnect'),
            'meli_listing_id' => esc_html__('Listing Data', 'meliconnect'),
            /* 'meli_user_id' => esc_html__('User ID', 'meliconnect'),
            'meli_product_type' => esc_html__('Product Type', 'meliconnect'),
            'meli_status' => esc_html__('Meli Status', 'meliconnect'), */
            'has_product_vinculation' => '<div style="text-align: center; font-weight: bold">' . esc_html__('Woo Product', 'meliconnect') . '</div>',
            /* 'has_template_vinculation' => esc_html__('Meliconnect Template', 'meliconnect'), */
            'import_status' => '<div style="text-align: center; font-weight: bold">' . esc_html__('Import Status', 'meliconnect') . '</div>',
        ];

        return $columns;
    }

    public function get_sortable_columns()
    {
        $sortable_columns = array(
            'meli_listing_title' => array('meli_listing_title', true),
            'meli_user_id' => array('meli_user_id', true),
            'meli_product_type' => array('meli_product_type', true),
            'meli_status' => array('meli_status', true),
        );

        return $sortable_columns;
    }

    //Overrides method to not show pagination on top page


    public function prepare_items()
    {
        $columns  = $this->get_columns();
        $hidden   = array('meli_user_id');
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        $per_page     = $this->get_items_per_page('user_listings_per_page', 10);
        $current_page = $this->get_pagenum();

        // Recoger los filtros de la solicitud con sanitización
        $filters = [
            'search'       => isset($_REQUEST['search']) ? sanitize_text_field(wp_unslash($_REQUEST['search'])) : '',
            'vinculation'  => isset($_REQUEST['vinculation_filter']) ? sanitize_key(wp_unslash($_REQUEST['vinculation_filter'])) : '',
            'listing_type' => isset($_REQUEST['listing_type_filter']) ? sanitize_key(wp_unslash($_REQUEST['listing_type_filter'])) : '',
            'seller_id'    => isset($_REQUEST['seller_filter']) ? sanitize_text_field(wp_unslash($_REQUEST['seller_filter'])) : '',
        ];

        // Obtener el conteo total basado en los filtros
        $total_items = self::record_count($filters);

        // Configurar la paginación
        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page,
        ]);

        // Ordenar resultados
        $orderby = ! empty($_REQUEST['orderby']) ? sanitize_key(wp_unslash($_REQUEST['orderby'])) : 'meli_listing_title';
        $order   = ! empty($_REQUEST['order']) ? sanitize_key(wp_unslash($_REQUEST['order'])) : 'asc';

        $this->items = self::get_user_listings($per_page, $current_page, $filters, $orderby, $order);
    }
}
