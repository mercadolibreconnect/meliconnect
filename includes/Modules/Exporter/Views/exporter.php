<?php

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


use Meliconnect\Meliconnect\Core\Helpers\Helper;
use Meliconnect\Meliconnect\Modules\Exporter\Controllers\ExportController;

$exportController = new ExportController();
$data = $exportController->getData();


$headerTitle = esc_html__('Exporter', 'meliconnect');



include MELICONNECT_PLUGIN_ROOT . 'includes/Core/Views/Partials/header.php';

?>
<!-- START MCSYNCAPP -->
<div id="meliconnect-page-exporter-main" class="meliconnect-app">
    <div class="meliconnect-main">
        <div class="meliconnect-container">

            <div id="meliconnect-exporter-container" class="meliconnect-container meliconnect-exporter-container meliconnect-overflow-x">
                <?php if (isset($data['export_process_data']->status) && $data['export_process_data']->status == 'processing') { ?>
                    <div id="meliconnect-process-in-progress" class="meliconnect-box">
                        <div class="meliconnect-columns meliconnect-is-align-items-center">
                            <!-- Progreso del proceso -->
                            <div class="meliconnect-column meliconnect-is-6">
                                <input type="hidden" id="meliconnect-process-id-hidden" name="process_id" value="<?php echo esc_attr($data['export_process_data']->process_id ?? 0); ?>">
                                <label class="meliconnect-label" id="meliconnect-process-text-title"><?php esc_html_e('Exporting is in progress', 'meliconnect'); ?></label>
                                <progress id="meliconnect-process-progress-bar" class="progress meliconnect-is-info meliconnect-mb-2" value="0" max="100">0%</progress>
                                <p><?php esc_html_e('Progress:', 'meliconnect'); ?><span id="meliconnect-process-progress">0%</span> </p>


                                <div class="meliconnect-buttons meliconnect-mt-4">
                                    <button id="meliconnect-exporter-cancel-process" data-process-id="<?php echo esc_attr($data['export_process_data']->process_id ?? 0); ?>" class="meliconnect-button meliconnect-is-danger">
                                        <span class="meliconnect-icon">
                                            <i class="fas fa-trash"></i>
                                        </span>
                                        <span><?php esc_html_e('Cancel', 'meliconnect'); ?></span>
                                    </button>
                                </div>
                            </div>

                            <div style="display: flex;">
                                <div style="flex: 1;height: 100px; background-color: #f4f5f8"></div>
                                <div class="divider meliconnect-is-vertical"> >> </div>
                                <div style="flex: 1;height: 100px; background-color: #f4f5f8"></div>
                            </div>

                            <!-- Información del proceso -->
                            <div class="meliconnect-column meliconnect-is-6">
                                <div class="meliconnect-columns">
                                    <div class="meliconnect-column meliconnect-is-6">
                                        <div class="content">
                                            <p><strong><?php esc_html_e('Executed:', 'meliconnect'); ?></strong><span id="meliconnect-process-executed"><?php echo esc_html($data['export_process_data']->executed ?? 0); ?></span> </p>
                                            <p><strong><?php esc_html_e('Total:', 'meliconnect'); ?></strong><span id="meliconnect-process-total"><?php echo esc_html($data['export_process_data']->total ?? 0); ?></span></p>
                                        </div>
                                    </div>
                                    <div class="meliconnect-column meliconnect-is-6">
                                        <div class="content">
                                            <p><strong><?php esc_html_e('Success:', 'meliconnect'); ?></strong><span id="meliconnect-process-total-success"> <?php echo esc_html($data['export_process_data']->total_success ?? 0); ?></span></p>
                                            <p><strong><?php esc_html_e('Fails:', 'meliconnect'); ?></strong><span id="meliconnect-process-total-fails"> <?php echo esc_html($data['export_process_data']->total_fails ?? 0); ?></span></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="meliconnect-columns">
                                    <div class="meliconnect-column meliconnect-is-12">
                                        <p><strong><?php esc_html_e('Execution Time:', 'meliconnect'); ?></strong><span id="meliconnect-process-execution-time"> <?php echo esc_html($data['execution_time']); ?> </span> </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                <?php } else if (isset($data['export_process_finished']->status) && $data['export_process_finished']->status == 'finished') { ?>
                    <div id="meliconnect-process-finished" class="meliconnect-box">
                        <div class="meliconnect-columns meliconnect-is-align-items-center">
                            <!-- Progreso del proceso -->
                            <div class="meliconnect-column meliconnect-is-6">
                                <label class="meliconnect-label" id="meliconnect-process-text-title"><?php esc_html_e('Exporting finished', 'meliconnect'); ?></label>

                                <div class="meliconnect-buttons meliconnect-mt-4">
                                    <button id="meliconnect-exporter-view-logs" class="meliconnect-button meliconnect-is-danger">
                                        <span class="meliconnect-icon">
                                            <i class="fas fa-solid fa-eye"></i>
                                        </span>
                                        <span><?php esc_html_e('View log details', 'meliconnect'); ?></span>
                                    </button>
                                    <button id="meliconnect-strat-new-export" data-process-id="<?php echo esc_attr($data['export_process_finished']->process_id ?? 0); ?>" class="meliconnect-button">
                                        <span class="meliconnect-icon">
                                            <i class="fas fa-play"></i>
                                        </span>
                                        <span><?php esc_html_e('Start new export', 'meliconnect'); ?></span>
                                    </button>
                                </div>
                            </div>

                            <div style="display: flex;">
                                <div style="flex: 1;height: 100px; background-color: #f4f5f8"></div>
                                <div class="divider meliconnect-is-vertical"> >> </div>
                                <div style="flex: 1;height: 100px; background-color: #f4f5f8"></div>
                            </div>

                            <!-- Información del proceso -->
                            <div class="meliconnect-column meliconnect-is-6">

                                <div class="meliconnect-content">
                                    <p><strong><?php esc_html_e('Success:', 'meliconnect'); ?></strong><span id="meliconnect-process-total-success"> <?php echo esc_html($data['export_process_finished']->total_success ?? 0); ?></span></p>
                                    <p><strong><?php esc_html_e('Fails:', 'meliconnect'); ?></strong><span id="meliconnect-process-total-fails"> <?php echo esc_html($data['export_process_finished']->total_fails ?? 0); ?></span></p>
                                </div>

                            </div>
                        </div>

                    </div>
                <?php } ?>


                <div id="meliconnect-exporter-table" class="meliconnect-card meliconnect-p-4">
                    <?php if (isset($data['export_process_data']->status) && $data['export_process_data']->status == 'processing') { ?>
                        <div id="meliconnect-export-table-overlay" class="active"></div>
                    <?php } ?>


                    <div class="meliconnect-columns">
                        <div class="meliconnect-column meliconnect-is-12">
                            <h2><?php esc_html_e('Woocommerce Products', 'meliconnect'); ?></h2>

                            <div class="alignleft actions meliconnect-mt-4">
                                <!-- Filtros -->
                                <form method="get">
                                    <input type="hidden" name="page" value="meliconnect-exporter">
                                    <div class="actions-filter">
                                        <div class="meliconnect-columns">
                                            <div class="meliconnect-column">
                                                <?php
                                                $search_value = isset($_GET['search']) ? sanitize_text_field(wp_unslash($_GET['search'])) : '';
                                                $selected_product_vinculation = isset($_GET['product_vinculation_filter']) ? sanitize_text_field(wp_unslash($_GET['product_vinculation_filter'])) : '';
                                                $selected_product_type = isset($_GET['product_type_filter']) ? sanitize_text_field(wp_unslash($_GET['product_type_filter'])) : '';
                                                $per_page = isset($_REQUEST['export_products_per_page']) ? absint(wp_unslash($_REQUEST['export_products_per_page'])) : 10;
                                                ?>

                                                <div class="meliconnect-field meliconnect-has-addons">
                                                    <div class="meliconnect-control">
                                                        <input id="user-search-input" class="meliconnect-input" type="search" placeholder="<?php esc_html_e('Search By Name, SKU, Meli listing id ...', 'meliconnect'); ?>" name="search" value="<?php echo esc_attr($search_value); ?>">
                                                    </div>

                                                    <div class="meliconnect-control meliconnect-is-expanded">
                                                        <div class="meliconnect-select meliconnect-is-fullwidth">
                                                            <select class="meliconnect-select" name="product_vinculation_filter">
                                                                <option value=""><?php esc_html_e('All Vinculations', 'meliconnect'); ?></option>
                                                                <option value="yes_product" <?php selected($selected_product_vinculation, 'yes_product'); ?>><?php esc_html_e('With Vinculated Product', 'meliconnect'); ?></option>
                                                                <option value="no_product" <?php selected($selected_product_vinculation, 'no_product'); ?>><?php esc_html_e('Without Vinculated Product', 'meliconnect'); ?></option>
                                                                <option value="yes_template" <?php selected($selected_product_vinculation, 'yes_template'); ?>><?php esc_html_e('With Vinculated Template', 'meliconnect'); ?></option>
                                                                <option value="no_template" <?php selected($selected_product_vinculation, 'no_template'); ?>><?php esc_html_e('Without Vinculated Template', 'meliconnect'); ?></option> -->
                                                            </select>
                                                        </div>
                                                    </div>


                                                    <div class="meliconnect-control meliconnect-is-expanded">
                                                        <div class="meliconnect-select meliconnect-is-fullwidth">
                                                            <select class="meliconnect-select" name="product_type_filter">
                                                                <option value=""><?php esc_html_e('All Types', 'meliconnect'); ?></option>
                                                                <option value="simple" <?php selected($selected_product_type, 'simple'); ?>><?php esc_html_e('Simple Products', 'meliconnect'); ?></option>
                                                                <option value="variable" <?php selected($selected_product_type, 'variable'); ?>><?php esc_html_e('Variable Products', 'meliconnect'); ?></option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="meliconnect-control meliconnect-is-expanded">
                                                        <div class="meliconnect-select meliconnect-is-fullwidth">
                                                            <select class="meliconnect-select" name="export_products_per_page">
                                                                <option value="5" <?php selected($per_page, 5); ?>>5</option>
                                                                <option value="10" <?php selected($per_page, 10); ?>>10</option>
                                                                <option value="20" <?php selected($per_page, 20); ?>>20</option>
                                                                <option value="50" <?php selected($per_page, 50); ?>>50</option>
                                                                <option value="-1" <?php selected($per_page, -1); ?>><?php esc_html_e('All', 'meliconnect'); ?></option>
                                                            </select>
                                                        </div>
                                                    </div>


                                                    <div class="meliconnect-control">
                                                        <p class="meliconnect-buttons meliconnect-has-addons">
                                                            <!-- <?php submit_button(esc_html__('Filter', 'meliconnect'), 'meliconnect-button ', 'filter_action', false); ?> -->
                                                            <button type="submit" class="meliconnect-button ">
                                                                <span class="meliconnect-icon meliconnect-is-small">
                                                                    <i class="fas fa-search"></i>
                                                                </span>
                                                            </button>
                                                            <button type="button" class="meliconnect-button " onclick="window.location.href='?page=meliconnect-exporter';">
                                                                <span class="meliconnect-icon meliconnect-is-small">
                                                                    <i class="fas fa-sync"></i>
                                                                </span>
                                                            </button>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                </form>
                                <form id="meliconnect-export-bulk-actions-form" method="post">
                                    <input type="hidden" name="meli-listings-ids-checked" id="meli-listings-ids-checked" value="">
                                    <div class="actions-bulk mb-4">
                                        <div class="meliconnect-columns">
                                            <div class="meliconnect-column meliconnect-select meliconnect-is-3">
                                                <select name="action-to-do" id="action-to-do">
                                                    <option value="-1"><?php esc_html_e('Bulk Actions', 'meliconnect'); ?></option>
                                                    <option value="export-selected"><?php esc_html_e('Export Selected', 'meliconnect'); ?></option>
                                                    <!-- <option value="match-items-products-by-name"><?php esc_html_e('Match selected with listings by name', 'meliconnect'); ?></option>
                                                    <option value="match-items-products-by-sku"><?php esc_html_e('Match selected with listings by sku', 'meliconnect'); ?></option>
                                                    <option value="match-items-products-by-sku"><?php esc_html_e('Unmatch selected', 'meliconnect'); ?></option>
                                                    <option value="match-items-products-by-sku"><?php esc_html_e('Match selected with listings by gtin', 'meliconnect'); ?></option> -->
                                                    <option value="desvinculate-products"><?php esc_html_e('Desvinculate selected with listings', 'meliconnect'); ?></option>
                                                    <option value="desvinculate-products-and-pause"><?php esc_html_e('Desvinculate selected and pause in meli', 'meliconnect'); ?></option>
                                                    <option value="desvinculate-products-and-delete"><?php esc_html_e('Desvinculate selected and clouse in meli', 'meliconnect'); ?></option>
                                                </select>

                                            </div>

                                            <div class="meliconnect-column meliconnect-is-2">
                                                <input type="submit" name="meliconnect-export-bulk-actions" id="meliconnect-export-bulk-actions" class=" meliconnect-button  meliconnect-is-primary" value="<?php esc_html_e('Apply', 'meliconnect') ?>">
                                            </div>
                                            <div class="meliconnect-column meliconnect-export-selected-items-tag-column" style="display:none">
                                                <span class="tag meliconnect-is-success meliconnect-is-light meliconnect-is-large">
                                                    <span id="selected-items-count"></span> &nbsp; <?php esc_html_e('Items selected', 'meliconnect'); ?>
                                                    <button type="button" class="delete" id="meliconnect-clear-selected-items"></button>
                                                </span>
                                            </div>

                                        </div>
                                    </div>
                                </form>
                            </div>



                            <?php

                            $exportProductsTable = new Meliconnect\Meliconnect\Modules\Exporter\Services\ExportProductsTable();
                            $exportProductsTable->prepare_items();
                            $exportProductsTable->display();
                            ?>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php include MELICONNECT_PLUGIN_ROOT . 'includes/Core/Views/Partials/footer.php'; ?>
</div>
<!-- END MCSYNCAPP -->