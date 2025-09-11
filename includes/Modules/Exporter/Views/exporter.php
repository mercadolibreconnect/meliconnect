<!-- START MCSYNCAPP -->
<div id="melicon-page-exporter-main" class="melicon-app">
    <?php

    use Meliconnect\Meliconnect\Core\Helpers\Helper;
    use Meliconnect\Meliconnect\Modules\Exporter\Controllers\ExportController;

    $exportController = new ExportController();
    $data = $exportController->getData();


    $headerTitle = esc_html__('Exporter', 'meliconnect');



    include MC_PLUGIN_ROOT . 'includes/Core/Views/Partials/header.php';

    ?>

    <div class="melicon-main">
        <div class="melicon-container">

            <div id="melicon-exporter-container" class="melicon-container melicon-exporter-container melicon-overflow-x">
                <?php if (isset($data['export_process_data']->status) && $data['export_process_data']->status == 'processing') { ?>
                    <div id="melicon-process-in-progress" class="melicon-box">
                        <div class="melicon-columns melicon-is-align-items-center">
                            <!-- Progreso del proceso -->
                            <div class="melicon-column melicon-is-6">
                                <input type="hidden" id="melicon-process-id-hidden" name="process_id" value="<?php echo esc_attr($data['export_process_data']->process_id ?? 0); ?>">
                                <label class="melicon-label" id="melicon-process-text-title"><?php esc_html_e('Exporting is in progress', 'meliconnect'); ?></label>
                                <progress id="melicon-process-progress-bar" class="progress melicon-is-info melicon-mb-2" value="0" max="100">0%</progress>
                                <p><?php esc_html_e('Progress:', 'meliconnect'); ?><span id="melicon-process-progress">0%</span> </p>


                                <div class="melicon-buttons melicon-mt-4">
                                    <button id="melicon-exporter-cancel-process" data-process-id="<?php echo esc_attr($data['export_process_data']->process_id ?? 0); ?>" class="melicon-button melicon-is-danger">
                                        <span class="melicon-icon">
                                            <i class="fas fa-trash"></i>
                                        </span>
                                        <span><?php esc_html_e('Cancel', 'meliconnect'); ?></span>
                                    </button>
                                </div>
                            </div>

                            <div style="display: flex;">
                                <div style="flex: 1;height: 100px; background-color: #f4f5f8"></div>
                                <div class="divider melicon-is-vertical"> >> </div>
                                <div style="flex: 1;height: 100px; background-color: #f4f5f8"></div>
                            </div>

                            <!-- Información del proceso -->
                            <div class="melicon-column melicon-is-6">
                                <div class="melicon-columns">
                                    <div class="melicon-column melicon-is-6">
                                        <div class="content">
                                            <p><strong><?php esc_html_e('Executed:', 'meliconnect'); ?></strong><span id="melicon-process-executed"><?php echo esc_html($data['export_process_data']->executed ?? 0); ?></span> </p>
                                            <p><strong><?php esc_html_e('Total:', 'meliconnect'); ?></strong><span id="melicon-process-total"><?php echo esc_html($data['export_process_data']->total ?? 0); ?></span></p>
                                        </div>
                                    </div>
                                    <div class="melicon-column melicon-is-6">
                                        <div class="content">
                                            <p><strong><?php esc_html_e('Success:', 'meliconnect'); ?></strong><span id="melicon-process-total-success"> <?php echo esc_html($data['export_process_data']->total_success ?? 0); ?></span></p>
                                            <p><strong><?php esc_html_e('Fails:', 'meliconnect'); ?></strong><span id="melicon-process-total-fails"> <?php echo esc_html($data['export_process_data']->total_fails ?? 0); ?></span></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="melicon-columns">
                                    <div class="melicon-column melicon-is-12">
                                        <p><strong><?php esc_html_e('Execution Time:', 'meliconnect'); ?></strong><span id="melicon-process-execution-time"> <?php echo esc_html($data['execution_time']); ?> </span> </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                <?php } else if (isset($data['export_process_finished']->status) && $data['export_process_finished']->status == 'finished') { ?>
                    <div id="melicon-process-finished" class="melicon-box">
                        <div class="melicon-columns melicon-is-align-items-center">
                            <!-- Progreso del proceso -->
                            <div class="melicon-column melicon-is-6">
                                <label class="melicon-label" id="melicon-process-text-title"><?php esc_html_e('Exporting finished', 'meliconnect'); ?></label>

                                <div class="melicon-buttons melicon-mt-4">
                                    <button id="melicon-exporter-view-logs" class="melicon-button melicon-is-danger">
                                        <span class="melicon-icon">
                                            <i class="fas fa-solid fa-eye"></i>
                                        </span>
                                        <span><?php esc_html_e('View log details', 'meliconnect'); ?></span>
                                    </button>
                                    <button id="melicon-strat-new-export" data-process-id="<?php echo esc_attr($data['export_process_finished']->process_id ?? 0); ?>" class="melicon-button">
                                        <span class="melicon-icon">
                                            <i class="fas fa-play"></i>
                                        </span>
                                        <span><?php esc_html_e('Start new export', 'meliconnect'); ?></span>
                                    </button>
                                </div>
                            </div>

                            <div style="display: flex;">
                                <div style="flex: 1;height: 100px; background-color: #f4f5f8"></div>
                                <div class="divider melicon-is-vertical"> >> </div>
                                <div style="flex: 1;height: 100px; background-color: #f4f5f8"></div>
                            </div>

                            <!-- Información del proceso -->
                            <div class="melicon-column melicon-is-6">

                                <div class="melicon-content">
                                    <p><strong><?php esc_html_e('Success:', 'meliconnect'); ?></strong><span id="melicon-process-total-success"> <?php echo esc_html($data['export_process_finished']->total_success ?? 0); ?></span></p>
                                    <p><strong><?php esc_html_e('Fails:', 'meliconnect'); ?></strong><span id="melicon-process-total-fails"> <?php echo esc_html($data['export_process_finished']->total_fails ?? 0); ?></span></p>
                                </div>

                            </div>
                        </div>

                    </div>
                <?php } ?>


                <div id="melicon-exporter-table" class="melicon-card melicon-p-4">
                    <?php if (isset($data['export_process_data']->status) && $data['export_process_data']->status == 'processing') { ?>
                        <div id="melicon-export-table-overlay" class="active"></div>
                    <?php } ?>


                    <div class="melicon-columns">
                        <div class="melicon-column melicon-is-12">
                            <h2><?php esc_html_e('Woocommerce Products', 'meliconnect'); ?></h2>

                            <div class="alignleft actions melicon-mt-4">
                                <!-- Filtros -->
                                <form method="get">
                                    <input type="hidden" name="page" value="meliconnect-exporter">
                                    <div class="actions-filter">
                                        <div class="melicon-columns">
                                            <div class="melicon-column">
                                                <?php
                                                $search_value = isset($_GET['search']) ? sanitize_text_field(wp_unslash($_GET['search'])) : '';
                                                $selected_product_vinculation = isset($_GET['product_vinculation_filter']) ? sanitize_text_field(wp_unslash($_GET['product_vinculation_filter'])) : '';
                                                $selected_product_type = isset($_GET['product_type_filter']) ? sanitize_text_field(wp_unslash($_GET['product_type_filter'])) : '';
                                                $per_page = isset( $_REQUEST['export_products_per_page'] ) ? absint( wp_unslash( $_REQUEST['export_products_per_page'] ) ) : 10;
                                                ?>

                                                <div class="melicon-field melicon-has-addons">
                                                    <div class="melicon-control">
                                                        <input id="user-search-input" class="melicon-input" type="search" placeholder="<?php esc_html_e('Search By Name, SKU, Meli listing id ...', 'meliconnect'); ?>" name="search" value="<?php echo esc_attr($search_value); ?>">
                                                    </div>

                                                    <div class="melicon-control melicon-is-expanded">
                                                        <div class="melicon-select melicon-is-fullwidth">
                                                            <select class="melicon-select" name="product_vinculation_filter">
                                                                <option value=""><?php esc_html_e('All Vinculations', 'meliconnect'); ?></option>
                                                                <option value="yes_product" <?php selected($selected_product_vinculation, 'yes_product'); ?>><?php esc_html_e('With Vinculated Product', 'meliconnect'); ?></option>
                                                                <option value="no_product" <?php selected($selected_product_vinculation, 'no_product'); ?>><?php esc_html_e('Without Vinculated Product', 'meliconnect'); ?></option>
                                                                <option value="yes_template" <?php selected($selected_product_vinculation, 'yes_template'); ?>><?php esc_html_e('With Vinculated Template', 'meliconnect'); ?></option>
                                                                <option value="no_template" <?php selected($selected_product_vinculation, 'no_template'); ?>><?php esc_html_e('Without Vinculated Template', 'meliconnect'); ?></option> -->
                                                            </select>
                                                        </div>
                                                    </div>


                                                    <div class="melicon-control melicon-is-expanded">
                                                        <div class="melicon-select melicon-is-fullwidth">
                                                            <select class="melicon-select" name="product_type_filter">
                                                                <option value=""><?php esc_html_e('All Types', 'meliconnect'); ?></option>
                                                                <option value="simple" <?php selected($selected_product_type, 'simple'); ?>><?php esc_html_e('Simple Products', 'meliconnect'); ?></option>
                                                                <option value="variable" <?php selected($selected_product_type, 'variable'); ?>><?php esc_html_e('Variable Products', 'meliconnect'); ?></option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="melicon-control melicon-is-expanded">
                                                        <div class="melicon-select melicon-is-fullwidth">
                                                            <select class="melicon-select" name="export_products_per_page">
                                                                <option value="5" <?php selected($per_page, 5); ?>>5</option>
                                                                <option value="10" <?php selected($per_page, 10); ?>>10</option>
                                                                <option value="20" <?php selected($per_page, 20); ?>>20</option>
                                                                <option value="50" <?php selected($per_page, 50); ?>>50</option>
                                                                <option value="-1" <?php selected($per_page, -1); ?>><?php esc_html_e('All', 'meliconnect'); ?></option>
                                                            </select>
                                                        </div>
                                                    </div>


                                                    <div class="melicon-control">
                                                        <p class="melicon-buttons melicon-has-addons">
                                                            <!-- <?php submit_button(esc_html__('Filter', 'meliconnect'), 'melicon-button ', 'filter_action', false); ?> -->
                                                            <button type="submit" class="melicon-button ">
                                                                <span class="melicon-icon melicon-is-small">
                                                                    <i class="fas fa-search"></i>
                                                                </span>
                                                            </button>
                                                            <button type="button" class="melicon-button " onclick="window.location.href='?page=meliconnect-exporter';">
                                                                <span class="melicon-icon melicon-is-small">
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
                                <form id="melicon-export-bulk-actions-form" method="post">
                                    <input type="hidden" name="meli-listings-ids-checked" id="meli-listings-ids-checked" value="">
                                    <div class="actions-bulk mb-4">
                                        <div class="melicon-columns">
                                            <div class="melicon-column melicon-select melicon-is-3">
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

                                            <div class="melicon-column melicon-is-2">
                                                <input type="submit" name="melicon-export-bulk-actions" id="melicon-export-bulk-actions" class=" melicon-button  melicon-is-primary" value="<?php esc_html_e('Apply', 'meliconnect') ?>">
                                            </div>
                                            <div class="melicon-column melicon-export-selected-items-tag-column" style="display:none">
                                                <span class="tag melicon-is-success melicon-is-light melicon-is-large">
                                                    <span id="selected-items-count"></span> &nbsp; <?php esc_html_e('Items selected', 'meliconnect'); ?>
                                                    <button type="button" class="delete" id="melicon-clear-selected-items"></button>
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

    <?php include MC_PLUGIN_ROOT . 'includes/Core/Views/Partials/footer.php'; ?>
</div>
<!-- END MCSYNCAPP -->