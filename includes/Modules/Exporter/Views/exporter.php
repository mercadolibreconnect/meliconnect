<!-- START MCSYNCAPP -->
<div id="melicon-page-exporter-main" class="melicon-app">
    <?php

    use StoreSync\Meliconnect\Core\Helpers\Helper;
    use StoreSync\Meliconnect\Modules\Exporter\Controllers\ExportController;

    $exportController = new ExportController();
    $data = $exportController->getData();


    $headerTitle = __('Exporter', 'meliconnect');



    include MC_PLUGIN_ROOT . 'includes/Core/Views/Partials/header.php';

    ?>

    <div class="melicon-main">
        <div class="melicon-container">

            <div id="melicon-exporter-container" class="container melicon-exporter-container melicon-overflow-x">
                <?php if (isset($data['export_process_data']->status) && $data['export_process_data']->status == 'processing') { ?>
                    <div id="melicon-process-in-progress" class="box">
                        <div class="columns is-align-items-center">
                            <!-- Progreso del proceso -->
                            <div class="column is-6">
                                <input type="hidden" id="melicon-process-id-hidden" name="process_id" value="<?php echo ($data['export_process_data']->process_id); ?>">
                                <label class="label" id="melicon-process-text-title"><?php echo __('Exporting is in progress', 'meliconnect'); ?></label>
                                <progress id="melicon-process-progress-bar" class="progress is-info mb-2" value="0" max="100">0%</progress>
                                <p><?php echo __('Progress:', 'meliconnect'); ?><span id="melicon-process-progress">0%</span> </p>


                                <div class="buttons mt-4">
                                    <button id="melicon-exporter-cancel-process" data-process-id="<?php echo ($data['export_process_data']->process_id); ?>" class="button is-danger">
                                        <span class="icon">
                                            <i class="fas fa-trash"></i>
                                        </span>
                                        <span><?php echo __('Cancel', 'meliconnect'); ?></span>
                                    </button>
                                </div>
                            </div>

                            <div style="display: flex;">
                                <div style="flex: 1;height: 100px; background-color: #f4f5f8"></div>
                                <div class="divider is-vertical"> >> </div>
                                <div style="flex: 1;height: 100px; background-color: #f4f5f8"></div>
                            </div>

                            <!-- Información del proceso -->
                            <div class="column is-6">
                                <div class="columns">
                                    <div class="column is-6">
                                        <div class="content">
                                            <p><strong><?php echo __('Executed:', 'meliconnect'); ?></strong><span id="melicon-process-executed"><?php echo ($data['export_process_data']->executed); ?></span> </p>
                                            <p><strong><?php echo __('Total:', 'meliconnect'); ?></strong><span id="melicon-process-total"><?php echo ($data['export_process_data']->total); ?></span></p>
                                        </div>
                                    </div>
                                    <div class="column is-6">
                                        <div class="content">
                                            <p><strong><?php echo __('Success:', 'meliconnect'); ?></strong><span id="melicon-process-total-success"> <?php echo ($data['export_process_data']->total_success); ?></span></p>
                                            <p><strong><?php echo __('Fails:', 'meliconnect'); ?></strong><span id="melicon-process-total-fails"> <?php echo ($data['export_process_data']->total_fails); ?></span></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="columns">
                                    <div class="column is-12">
                                        <p><strong><?php echo __('Execution Time:', 'meliconnect'); ?></strong><span id="melicon-process-execution-time"> <?php echo ($data['execution_time']); ?> </span> </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                <?php } else if (isset($data['export_process_finished']->status) && $data['export_process_finished']->status == 'finished') { ?>
                    <div id="melicon-process-finished" class="box">
                        <div class="columns is-align-items-center">
                            <!-- Progreso del proceso -->
                            <div class="column is-6">
                                <label class="label" id="melicon-process-text-title"><?php echo __('Exporting finished', 'meliconnect'); ?></label>

                                <div class="buttons mt-4">
                                    <button id="melicon-exporter-view-logs" class="button is-danger">
                                        <span class="icon">
                                            <i class="fas fa-solid fa-eye"></i>
                                        </span>
                                        <span><?php echo __('View log details', 'meliconnect'); ?></span>
                                    </button>
                                    <button id="melicon-strat-new-export" data-process-id="<?php echo ($data['export_process_finished']->process_id); ?>" class="button">
                                        <span class="icon">
                                            <i class="fas fa-play"></i>
                                        </span>
                                        <span><?php echo __('Start new export', 'meliconnect'); ?></span>
                                    </button>
                                </div>
                            </div>

                            <div style="display: flex;">
                                <div style="flex: 1;height: 100px; background-color: #f4f5f8"></div>
                                <div class="divider is-vertical"> >> </div>
                                <div style="flex: 1;height: 100px; background-color: #f4f5f8"></div>
                            </div>

                            <!-- Información del proceso -->
                            <div class="column is-6">

                                <div class="content">
                                    <p><strong><?php echo __('Success:', 'meliconnect'); ?></strong><span id="melicon-process-total-success"> <?php echo ($data['export_process_finished']->total_success); ?></span></p>
                                    <p><strong><?php echo __('Fails:', 'meliconnect'); ?></strong><span id="melicon-process-total-fails"> <?php echo ($data['export_process_finished']->total_fails); ?></span></p>
                                </div>

                            </div>
                        </div>

                    </div>
                <?php } ?>


                <div id="melicon-exporter-table" class="melicon-card">
                    <?php if (isset($data['export_process_data']->status) && $data['export_process_data']->status == 'processing') { ?>
                        <div id="melicon-export-table-overlay" class="active"></div>
                    <?php } ?>


                    <div class="columns">
                        <div class="column-is-12">
                            <h2><?php echo __('Woocommerce Products', 'meliconnect'); ?></h2>

                            <div class="alignleft actions mt-4">
                                <!-- Filtros -->
                                <form method="get">
                                    <input type="hidden" name="page" value="meliconnect-exporter">
                                    <div class="actions-filter">
                                        <div class="columns ">
                                            <div class="column">
                                                <?php
                                                $search_value = isset($_GET['search']) ? $_GET['search'] : '';
                                                $selected_product_vinculation = isset($_GET['product_vinculation_filter']) ? $_GET['product_vinculation_filter'] : '';
                                                $selected_product_type = isset($_GET['product_type_filter']) ? $_GET['product_type_filter'] : '';
                                                $per_page = isset($_REQUEST['export_products_per_page']) ? (int) $_REQUEST['export_products_per_page'] : 10;
                                                ?>
                                                <div class="field has-addons">
                                                    <div class="control">
                                                        <input id="user-search-input" class="input melicon-input" type="search" placeholder="<?php _e('Search By Name, SKU, Meli listing id ...', 'meliconnect'); ?>" name="search" value="<?php echo esc_attr($search_value); ?>">
                                                    </div>

                                                    <div class="control is-expanded">
                                                        <div class="select melicon-select is-fullwidth">
                                                            <select class="select melicon-select" name="product_vinculation_filter">
                                                                <option value=""><?php _e('All Vinculations', 'meliconnect'); ?></option>
                                                                <option value="yes_product" <?php selected($selected_product_vinculation, 'yes_product'); ?>><?php _e('With Vinculated Product', 'meliconnect'); ?></option>
                                                                <option value="no_product" <?php selected($selected_product_vinculation, 'no_product'); ?>><?php _e('Without Vinculated Product', 'meliconnect'); ?></option>
                                                                <option value="yes_template" <?php selected($selected_product_vinculation, 'yes_template'); ?>><?php _e('With Vinculated Template', 'meliconnect'); ?></option>
                                                                <option value="no_template" <?php selected($selected_product_vinculation, 'no_template'); ?>><?php _e('Without Vinculated Template', 'meliconnect'); ?></option> -->
                                                            </select>
                                                        </div>
                                                    </div>


                                                    <div class="control is-expanded">
                                                        <div class="select melicon-select is-fullwidth">
                                                            <select class="select melicon-select" name="product_type_filter">
                                                                <option value=""><?php _e('All Types', 'meliconnect'); ?></option>
                                                                <option value="simple" <?php selected($selected_product_type, 'simple'); ?>><?php _e('Simple Products', 'meliconnect'); ?></option>
                                                                <option value="variable" <?php selected($selected_product_type, 'variable'); ?>><?php _e('Variable Products', 'meliconnect'); ?></option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="control is-expanded">
                                                        <div class="select melicon-select is-fullwidth">
                                                            <select class="select melicon-select" name="export_products_per_page">
                                                                <option value="5" <?php selected($per_page, 5); ?>>5</option>
                                                                <option value="10" <?php selected($per_page, 10); ?>>10</option>
                                                                <option value="20" <?php selected($per_page, 20); ?>>20</option>
                                                                <option value="50" <?php selected($per_page, 50); ?>>50</option>
                                                                <option value="-1" <?php selected($per_page, -1); ?>><?php _e('All', 'meliconnect'); ?></option>
                                                            </select>
                                                        </div>
                                                    </div>


                                                    <div class="control">
                                                        <p class="buttons has-addons">
                                                            <!-- <?php submit_button(__('Filter', 'meliconnect'), 'button button-meliconnect', 'filter_action', false); ?> -->
                                                            <button type="submit" class="button button-meliconnect">
                                                                <span class="icon is-small">
                                                                    <i class="fas fa-search"></i>
                                                                </span>
                                                            </button>
                                                            <button type="button" class="button button-meliconnect" onclick="window.location.href='?page=meliconnect-exporter';">
                                                                <span class="icon is-small">
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
                                        <div class="columns">
                                            <div class="column is-6">
                                                <select name="action-to-do" id="action-to-do">
                                                    <option value="-1"><?php _e('Bulk Actions', 'meliconnect'); ?></option>
                                                    <option value="export-selected"><?php _e('Export Selected', 'meliconnect'); ?></option>
                                                    <!-- <option value="match-items-products-by-name"><?php _e('Match selected with listings by name', 'meliconnect'); ?></option>
                                                    <option value="match-items-products-by-sku"><?php _e('Match selected with listings by sku', 'meliconnect'); ?></option>
                                                    <option value="match-items-products-by-sku"><?php _e('Unmatch selected', 'meliconnect'); ?></option>
                                                    <option value="match-items-products-by-sku"><?php _e('Match selected with listings by gtin', 'meliconnect'); ?></option> -->
                                                    <option value="desvinculate-products"><?php _e('Desvinculate selected with listings', 'meliconnect'); ?></option>
                                                    <option value="desvinculate-products-and-pause"><?php _e('Desvinculate selected and pause in meli', 'meliconnect'); ?></option>
                                                    <option value="desvinculate-products-and-delete"><?php _e('Desvinculate selected and clouse in meli', 'meliconnect'); ?></option>
                                                </select>

                                            </div>

                                            <div class="column is-2">
                                                <?php submit_button(__('Apply', 'meliconnect'), 'button button-meliconnect', 'melicon-export-bulk-actions', false); ?>
                                            </div>
                                            <div class="column melicon-export-selected-items-tag-column" style="display:none">
                                                <span class="tag is-success is-light is-large">
                                                    <span id="selected-items-count"></span> &nbsp; <?php _e('Items selected', 'meliconnect'); ?>
                                                    <button type="button" class="delete" id="melicon-clear-selected-items"></button>
                                                </span>
                                            </div>

                                        </div>
                                    </div>
                                </form>
                            </div>



                            <?php

                            $exportProductsTable = new StoreSync\Meliconnect\Modules\Exporter\Services\ExportProductsTable();
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