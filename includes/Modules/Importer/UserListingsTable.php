<?php

namespace StoreSync\Meliconnect\Modules\Importer;

use StoreSync\Meliconnect\Core\Helpers\Helper;

// includes/Core/Controllers/ProductsListTableController.php
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class UserListingsTable extends \WP_List_Table
{

    public function __construct()
    {
        parent::__construct([
            'singular' => __('User Listing', 'meliconnect'), // Singular name of the listed records.
            'plural'   => __('User Listings', 'meliconnect'), // Plural name of the listed records.
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

        $sql = "SELECT * FROM {$table_name} {$where_sql} ORDER BY {$orderby} {$order} LIMIT %d OFFSET %d";

        return $wpdb->get_results($wpdb->prepare($sql, $query_params), ARRAY_A);
    }

    private static function build_filters_query($filters)
    {
        global $wpdb;

        $where_clauses = [];
        $query_params = [];

        $filters = [
            'search' => isset($_REQUEST['search']) ? $_REQUEST['search'] : '',
            'vinculation' => isset($_REQUEST['vinculation_filter']) ? $_REQUEST['vinculation_filter'] : '',
            'listing_type' => isset($_REQUEST['listing_type_filter']) ? $_REQUEST['listing_type_filter'] : '',
            'seller_id' => isset($_REQUEST['seller_filter']) ? $_REQUEST['seller_filter'] : '',
            'status' => isset($_REQUEST['listing_status_filter']) ? $_REQUEST['listing_status_filter'] : '',
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
            if($filters['status'] == 'active'){
                $where_clauses[] = "meli_status = %s";
                $query_params[] = 'active';
            }else{
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
        $table_name = $wpdb->prefix . 'melicon_user_listings_to_import';

        // Construir las cláusulas WHERE y los parámetros de consulta
        list($where_sql, $query_params) = self::build_filters_query($filters);

        // Construir la consulta SQL
        $sql = "SELECT COUNT(*) FROM {$table_name} {$where_sql}";

        // Si hay parámetros en la consulta, usar prepare, de lo contrario ejecutar directamente
        if (!empty($query_params)) {
            $sql = $wpdb->prepare($sql, $query_params);
        }

        // Ejecutar la consulta
        return $wpdb->get_var($sql);
    }

    public function no_items()
    {
        _e('No user listings available.', 'meliconnect');
    }

    public function column_default($item, $column_name)
    {
        if (isset($item[$column_name])) {
            return $item[$column_name];
        }

        return print_r($item, true);
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
            ? Helper::meliconnectPrintTag(__('To update', 'meliconnect'), 'is-warning')
            : Helper::meliconnectPrintTag(__('To create', 'meliconnect'), 'is-danger');

        $subText = '';

        if ($item['vinculated_product_id']) {
            $matchMessages = [
                'is_product_match_by_name' => __('Match by same Name', 'meliconnect'),
                'is_product_match_by_sku'  => __('Match by same SKU', 'meliconnect'),
                'is_product_match_manually'  => __('Custom Match', 'meliconnect')
            ];

            foreach ($matchMessages as $key => $message) {
                if (!empty($item[$key])) {
                    $subText .= '<p class="mt-2">' . Helper::meliconnectPrintTag($message, 'is-link is-light') . '</p>';
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
                    '<a href="#" class="melicon-clear-product-match" data-meli-listing-id="%s">' . __('Clear Match', 'meliconnect') . '</a>',
                    esc_attr($item['meli_listing_id'])
                )
            ];
        } else {
            $actions = $item['vinculated_product_id']
                ? [
                    'view' => sprintf('<a href="%s" target="_blank">' . __('View', 'meliconnect') . '</a>', esc_url(get_permalink($item['vinculated_product_id']))),
                    'edit' => sprintf('<a href="%s" target="_blank">' . __('Edit', 'meliconnect') . '</a>', esc_url(get_edit_post_link($item['vinculated_product_id']))),
                    'delete-vinculation' => sprintf('<a href="#" class="melicon-delete-product-vinculation" data-product-type="%s" data-woo-product-id="%d" data-meli-listing-id="%s">' . __('Desvinculate', 'meliconnect') . '</a>', esc_attr($product_type), esc_attr($item['vinculated_product_id']), esc_attr($item['meli_listing_id']))
                ]
                : [
                    'find-a-match' => sprintf(
                        '<a href="#" class="melicon-find-product-to-match js-modal-trigger" 
                    data-user-listing-id ="%s"
                    data-product-type="%s"  
                    data-listing-id="%s"
                    data-meli-listing-title="%s"
                    data-meli-listing-type="%s" 
                    data-meli-sku="%s" 
                    data-meli-status="%s" 
                    data-price="%s" 
                    data-available-quantity="%s" 
                    data-target="melicon-find-match-modal">' . __('Find match', 'meliconnect') . '</a>',
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

        return '<div style="text-align: center;">' .  $text . $subText . $this->row_actions($actions). '</div>';
    }


    public function column_meli_listing_id($item)
    {
        // Decodificar la respuesta JSON de MercadoLibre
        $meli_response = json_decode($item['meli_response'], true);

        $product_type_class = $item['meli_product_type'] === 'simple' ? 'has-text-link' : 'has-text-success';
        $status_class = $item['meli_status'] === 'active' ? 'has-text-success' : 'has-text-danger';


        // Crear las columnas usando Bulma
?>
        <style>
            .wp-list-table .column-meli_listing_id {
                width: 40%;
            }
        </style>
        <div class="columns is-multiline">
            <!-- Primera Columna -->
            <div class="column is-5">
                <strong><?php _e('ID', 'meliconnect'); ?>:</strong> <?php echo esc_html($item['meli_listing_id']); ?><br>
                <strong><?php _e('User ID', 'meliconnect'); ?>:</strong> <?php echo esc_html($item['meli_user_id']); ?><br>
                <?php if (!empty($item['meli_sku'])): ?>
                    <strong><?php _e('SKU', 'meliconnect'); ?>:</strong> <?php echo esc_html($item['meli_sku']); ?>
                <?php endif; ?>
                <strong><?php _e('Status', 'meliconnect'); ?>:</strong> <?php echo Helper::meliconnectPrintColorText($item['meli_status'], $status_class); ?><br>
                <strong><?php _e('Product Type', 'meliconnect'); ?>:</strong> <?php echo Helper::meliconnectPrintColorText($item['meli_product_type'], $product_type_class); ?><br>
                <strong><?php _e('Listing Type', 'meliconnect'); ?>:</strong> <?php echo esc_html($meli_response['listing_type_id']); ?>
            </div>

            <!-- Segunda Columna -->
            <div class="column is-6">
                <strong><?php _e('Price', 'meliconnect'); ?>:</strong> <?php echo esc_html($meli_response['price']); ?><br>
                <strong><?php _e('Sold Quantity', 'meliconnect'); ?>:</strong> <?php echo esc_html($meli_response['sold_quantity']); ?><br>
                <strong><?php _e('Available Quantity', 'meliconnect'); ?>:</strong> <?php echo esc_html($meli_response['available_quantity']); ?>
            </div>
        </div>
<?php
    }

    public function column_has_template_vinculation($item)
    {

        $text = $item['vinculated_template_id'] ? Helper::meliconnectPrintTag(__('Using template', 'meliconnect'), 'is-warning') : Helper::meliconnectPrintTag(__('To create', 'meliconnect'), 'is-danger');

        /* $product_type = isset($item['product_type']) ? $item['product_type'] : 'simple'; // Valor predeterminado a 'simple'
        $product_type = esc_sql($product_type); */

        $category_id = '';

        // Construir acciones para productos vinculados
        if ($item['vinculated_template_id']) {
            $actions = [
                'delete-vinculation' => sprintf(
                    '<a class="melicon-delete-template-vinculation" data-listing-id="' . $item['meli_listing_id'] . '" data-template-id="' . $item['vinculated_template_id'] . '" href="?page=%s&action=%s&listing=%s">' . __('Desvinculate', 'meliconnect') . '</a>',
                    esc_attr($_REQUEST['page']),
                    'delete',
                    absint($item['id'])
                )
            ];
        } else {
            $actions = [
                //'find-a-match' => '<a class="melicon-find-template-to-match" data-category-id="' . $category_id . '" data-listing-id="' . $item['meli_listing_id'] . '" >' . __('Find match', 'meliconnect') . '</a>',
            ];
        }

        return $text . $this->row_actions($actions);
    }

    public function column_import_status($item)
    {
        switch ($item['import_status']) {

            case 'processing':
                $html= Helper::meliconnectPrintTag(__('Processing', 'meliconnect'), 'is-info');
                break;
            case 'paused':
                $html= Helper::meliconnectPrintTag(__('Paused', 'meliconnect'), 'is-secondary');
                break;
            case 'failed':
                $html= Helper::meliconnectPrintTag(__('Failed', 'meliconnect'), 'is-danger');
                break;
            case 'finished':
                $html= Helper::meliconnectPrintTag(__('Imported', 'meliconnect'), 'is-success');
                break;
            default:
                $html= Helper::meliconnectPrintTag(__('Pending', 'meliconnect'), 'is-warning');
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
            'meli_listing_title' => __('Listing', 'meliconnect'),
            'meli_listing_id' => __('Listing Data', 'meliconnect'),
            /* 'meli_user_id' => __('User ID', 'meliconnect'),
            'meli_product_type' => __('Product Type', 'meliconnect'),
            'meli_status' => __('Meli Status', 'meliconnect'), */
            'has_product_vinculation' => '<div style="text-align: center; font-weight: bold">' . __('Woo Product', 'meliconnect'). '</div>',
            /* 'has_template_vinculation' => __('Meliconnect Template', 'meliconnect'), */
            'import_status' =>'<div style="text-align: center; font-weight: bold">' . __('Import Status', 'meliconnect') . '</div>',
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
    protected function pagination($which)
    {
        if (empty($this->_pagination_args)) {
            return;
        }

        $total_items = $this->_pagination_args['total_items'];
        $total_pages = $this->_pagination_args['total_pages'];

        // Solo renderizar la paginación en la parte inferior
        if ('top' === $which) {
            return; // No mostrar paginación en la parte superior
        }

        $output = '<span class="displaying-num">' . sprintf(
            /* translators: %s: Number of items. */
            _n('%s item', '%s items', $total_items),
            number_format_i18n($total_items)
        ) . '</span>';

        $current = $this->get_pagenum();
        $removable_query_args = wp_removable_query_args();

        $current_url = set_url_scheme('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        $current_url = remove_query_arg($removable_query_args, $current_url);

        $page_links = array();

        $total_pages_before = '<span class="paging-input">';
        $total_pages_after  = '</span></span>';

        $disable_first = false;
        $disable_last  = false;
        $disable_prev  = false;
        $disable_next  = false;

        if (1 === $current) {
            $disable_first = true;
            $disable_prev  = true;
        }
        if ($total_pages === $current) {
            $disable_last = true;
            $disable_next = true;
        }

        if ($disable_first) {
            $page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&laquo;</span>';
        } else {
            $page_links[] = sprintf(
                "<a class='first-page button' href='%s'>" .
                    "<span class='screen-reader-text'>%s</span>" .
                    "<span aria-hidden='true'>%s</span>" .
                    '</a>',
                esc_url(remove_query_arg('paged', $current_url)),
                /* translators: Hidden accessibility text. */
                __('First page'),
                '&laquo;'
            );
        }

        if ($disable_prev) {
            $page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&lsaquo;</span>';
        } else {
            $page_links[] = sprintf(
                "<a class='prev-page button' href='%s'>" .
                    "<span class='screen-reader-text'>%s</span>" .
                    "<span aria-hidden='true'>%s</span>" .
                    '</a>',
                esc_url(add_query_arg('paged', max(1, $current - 1), $current_url)),
                /* translators: Hidden accessibility text. */
                __('Previous page'),
                '&lsaquo;'
            );
        }

        $html_current_page  = sprintf(
            '<label for="current-page-selector" class="screen-reader-text">%s</label>' .
                "<input class='current-page' id='current-page-selector' type='text' name='paged' value='%s' size='%d' aria-describedby='table-paging' />" .
                "<span class='tablenav-paging-text'>",
            /* translators: Hidden accessibility text. */
            __('Current Page'),
            $current,
            strlen($total_pages)
        );

        $html_total_pages = sprintf("<span class='total-pages'>%s</span>", number_format_i18n($total_pages));

        $page_links[] = $total_pages_before . sprintf(
            /* translators: 1: Current page, 2: Total pages. */
            _x('%1$s of %2$s', 'paging'),
            $html_current_page,
            $html_total_pages
        ) . $total_pages_after;

        if ($disable_next) {
            $page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&rsaquo;</span>';
        } else {
            $page_links[] = sprintf(
                "<a class='next-page button' href='%s'>" .
                    "<span class='screen-reader-text'>%s</span>" .
                    "<span aria-hidden='true'>%s</span>" .
                    '</a>',
                esc_url(add_query_arg('paged', min($total_pages, $current + 1), $current_url)),
                /* translators: Hidden accessibility text. */
                __('Next page'),
                '&rsaquo;'
            );
        }

        if ($disable_last) {
            $page_links[] = '<span class="tablenav-pages-navspan button disabled" aria-hidden="true">&raquo;</span>';
        } else {
            $page_links[] = sprintf(
                "<a class='last-page button' href='%s'>" .
                    "<span class='screen-reader-text'>%s</span>" .
                    "<span aria-hidden='true'>%s</span>" .
                    '</a>',
                esc_url(add_query_arg('paged', $total_pages, $current_url)),
                /* translators: Hidden accessibility text. */
                __('Last page'),
                '&raquo;'
            );
        }

        $pagination_links_class = 'pagination-links';
        if (!empty($infinite_scroll)) {
            $pagination_links_class .= ' hide-if-js';
        }
        $output .= "\n<span class='$pagination_links_class'>" . implode("\n", $page_links) . '</span>';

        if ($total_pages) {
            $page_class = $total_pages < 2 ? ' one-page' : '';
        } else {
            $page_class = ' no-pages';
        }
        $this->_pagination = "<div class='tablenav-pages{$page_class}'>$output</div>";

        // Mostrar solo en la parte inferior
        if ('bottom' === $which && $total_pages > 1) {
            echo $this->_pagination;
        }
    }

    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = array('meli_user_id');
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        $per_page = $this->get_items_per_page('user_listings_per_page', 10);
        $current_page = $this->get_pagenum();

        // Recoger los filtros de la solicitud
        $filters = [
            'search' => isset($_REQUEST['search']) ? $_REQUEST['search'] : '',
            'vinculation' => isset($_REQUEST['vinculation_filter']) ? $_REQUEST['vinculation_filter'] : '',
            'listing_type' => isset($_REQUEST['listing_type_filter']) ? $_REQUEST['listing_type_filter'] : '',
            'seller_id' => isset($_REQUEST['seller_filter']) ? $_REQUEST['seller_filter'] : '',
        ];

        // Obtener el conteo total basado en los filtros
        $total_items = self::record_count($filters);

        // Configurar la paginación
        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page' => $per_page,
        ]);

        $orderby = !empty($_REQUEST['orderby']) ? sanitize_key($_REQUEST['orderby']) : 'meli_listing_title';
        $order = !empty($_REQUEST['order']) ? sanitize_key($_REQUEST['order']) : 'asc';

        $this->items = self::get_user_listings($per_page, $current_page, $filters, $orderby, $order);
    }
}
