<!-- START MCSYNCAPP -->
<div id="melicon-page-importer-main" class="melicon-app">
    <?php

    use StoreSync\Meliconnect\Modules\Importer\Controllers\ImportController;
    use StoreSync\Meliconnect\Core\Helpers\Helper;

    $importController = new ImportController();
    $data = $importController->getData();


    $headerTitle = __('Importer', 'meliconnect');

    include MC_PLUGIN_ROOT . 'includes/Core/Views/Partials/header.php';

    ?>

    <div class="melicon-main">
        <div class="melicon-container">
            <!-- START FIND MATCH MODAL -->

            <div id="melicon-find-match-modal" class="modal">
                <div class="modal-background"></div>
                <div class="modal-card">
                    <header class="modal-card-head">
                        <p class="modal-card-title"><?php _e('Find Match', 'meliconnect'); ?></p>
                        <button class="modal-close is-large" aria-label="<?php esc_attr_e('close', 'meliconnect'); ?>"></button>
                    </header>
                    <section class="modal-card-body">

                        <div class="columns">
                            <div class="column is-6">
                                <p><strong><?php _e('Meli listing:', 'meliconnect'); ?></strong>:</p>
                            </div>
                            <div class="column is-6">
                                <p><strong><?php _e('Woo product:', 'meliconnect'); ?></strong>:</p>
                            </div>
                        </div>
                        <div class="columns">
                            <div class="column is-6">
                                <input type="hidden" name="melicon-match-modal_user-listing-id" id="melicon-match-modal_user-listing-id" value="">
                                <p><strong id="melicon-meli-listing-title-to-match"></strong>:</p>
                            </div>
                            <div class="column is-6">
                                <div id="melicon-match-product-select-container">
                                    <div class="melicon-field field">
                                        <div class="melicon-control control">
                                            <div class="melicon-select select is-fullwidth">
                                                <select id="melicon-match-select-products-select" style="width: 100%;">

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="columns">
                            <div id="melicon-meli-listing-data-to-match" class="column is-6">
                            </div>
                            <div id="melicon-matched-product-details" class="column is-6">
                            </div>
                        </div>

                    </section>
                    <footer class="modal-card-foot">
                        <button id="melicon-apply-match-button" class="button button-meliconnect is-success "><?php _e('Apply match', 'meliconnect'); ?></button>
                        <button class="button button-meliconnect is-danger"><?php _e('Cancel', 'meliconnect'); ?></button>
                    </footer>
                </div>
            </div>

            <!-- END FIND MATCH MODAL -->
            <div id="melicon-importer-container" class="container melicon-importer-container melicon-overflow-x">
                <?php if (isset($data['import_process_data']->status) && $data['import_process_data']->status == 'processing') { ?>
                    <div id="melicon-process-in-progress" class="box">
                        <div class="columns is-align-items-center">
                            <!-- Progreso del proceso -->
                            <div class="column is-6">
                                <input type="hidden" id="melicon-process-id-hidden" name="process_id" value="<?php echo ($data['import_process_data']->process_id); ?>">
                                <label class="label" id="melicon-process-text-title"><?php echo __('Importing is in progress', 'meliconnect'); ?></label>
                                <progress id="melicon-process-progress-bar" class="progress is-info mb-2" value="0" max="100">0%</progress>
                                <p><?php echo __('Progress:', 'meliconnect'); ?><span id="melicon-process-progress">0%</span> </p>


                                <div class="buttons mt-4">
                                    <!-- <button id="melicon-importer-pause-process" data-process-id="<?php echo ($data['import_process_data']->process_id); ?>" class="button is-warning">
                                        <span class="icon">
                                            <i class="fas fa-pause"></i>
                                        </span>
                                        <span><?php echo __('Pause', 'meliconnect'); ?></span>
                                    </button> -->
                                    <button id="melicon-importer-cancel-process" data-process-id="<?php echo ($data['import_process_data']->process_id); ?>" class="button is-danger">
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
                                            <p><strong><?php echo __('Executed:', 'meliconnect'); ?></strong><span id="melicon-process-executed"><?php echo ($data['import_process_data']->executed); ?></span> </p>
                                            <p><strong><?php echo __('Total:', 'meliconnect'); ?></strong><span id="melicon-process-total"><?php echo ($data['import_process_data']->total); ?></span></p>
                                        </div>
                                    </div>
                                    <div class="column is-6">
                                        <div class="content">
                                            <p><strong><?php echo __('Success:', 'meliconnect'); ?></strong><span id="melicon-process-total-success"> <?php echo ($data['import_process_data']->total_success); ?></span></p>
                                            <p><strong><?php echo __('Fails:', 'meliconnect'); ?></strong><span id="melicon-process-total-fails"> <?php echo ($data['import_process_data']->total_fails); ?></span></p>
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
                <?php } else if (isset($data['import_process_finished']->status) && $data['import_process_finished']->status == 'finished') { ?>
                    <div id="melicon-process-finished" class="box">
                        <div class="columns is-align-items-center">
                            <!-- Progreso del proceso -->
                            <div class="column is-6">
                                <label class="label" id="melicon-process-text-title"><?php echo __('Importing finished', 'meliconnect'); ?></label>

                                <div class="buttons mt-4">
                                    <button id="melicon-importer-view-logs" class="button is-danger">
                                        <span class="icon">
                                            <i class="fas fa-solid fa-eye"></i>
                                        </span>
                                        <span><?php echo __('View log details', 'meliconnect'); ?></span>
                                    </button>
                                    <button id="melicon-importer-delete-finished" data-process-id="<?php echo ($data['import_process_finished']->process_id); ?>" class="button is-danger">
                                        <span class="icon">
                                            <i class="fas fa-sync"></i>
                                        </span>
                                        <span><?php echo __('Strat new import', 'meliconnect'); ?></span>
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
                                    <p><strong><?php echo __('Success:', 'meliconnect'); ?></strong><span id="melicon-process-total-success"> <?php echo ($data['import_process_finished']->total_success); ?></span></p>
                                    <p><strong><?php echo __('Fails:', 'meliconnect'); ?></strong><span id="melicon-process-total-fails"> <?php echo ($data['import_process_finished']->total_fails); ?></span></p>
                                </div>

                            </div>
                        </div>

                    </div>
                <?php } else { ?>
                    <div id="" class="melicon-card">
                        <div class="columns is-align-items-center">
                            <!-- Inline Form Column -->
                            <div class="column is-4">
                                <form class="is-flex is-flex-direction-column" id="melicon-get-meli-user-listings" method="POST">
                                    <div class="field mb-3">
                                        <label class="label"><?php echo __('Select seller', 'meliconnect'); ?></label>
                                        <?php echo (Helper::getSellersSelect()) ?>
                                    </div>
                                    <!-- <div class="field mb-3">
                                        <label class="label"><?php echo __('Select publication status', 'meliconnect'); ?></label>
                                        <div class="melicon-control control">
                                            <div class="melicon-select select">
                                                <select>
                                                    <option><?php echo __('Only Active', 'meliconnect'); ?></option>
                                                    <option><?php echo __('Active and Paused', 'meliconnect'); ?></option>
                                                    <option><?php echo __('All', 'meliconnect'); ?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div> -->
                                    <div class="control">
                                        <div class="buttons">
                                            <button id="melicon-get-meli-user-listings-button" type="submit" class="button is-primary button-meliconnect"><?php echo __('Get Listings', 'meliconnect'); ?></button>
                                            <?php
                                            if ($data['meli_user_listings_to_import_count'] > 0) {
                                            ?>
                                                <button id="melicon-reset-meli-user-listings-button" class="button is-danger button-meliconnect"><?php echo __('Clean Listings', 'meliconnect'); ?></button>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <?php
                            if ($data['meli_user_listings_to_import_count'] > 0) {
                            ?>
                                <div style="display: flex;">
                                    <div style="flex: 1;height: 100px; background-color: #f4f5f8"></div>
                                    <div class="divider is-vertical"> > </div>
                                    <div style="flex: 1;height: 100px; background-color: #f4f5f8"></div>
                                </div>

                                <div class="column is-4">

                                    <div class="control mb-2">
                                        <button data-match-by="sku" class="match-all-listings-with-products button is-link button-meliconnect is-fullwidth"><?php echo __('Match Listings with Products by SKU', 'meliconnect'); ?></button>
                                    </div>
                                    <div class="control mb-2">
                                        <button data-match-by="name" class="match-all-listings-with-products button is-link button-meliconnect is-fullwidth"><?php echo __('Match Listings with Products by Name', 'meliconnect'); ?></button>
                                    </div>
                                    <!-- <div class="control mb-2">
                                        <button class="button is-link button-meliconnect is-fullwidth"><?php echo __('Match Items with Templates', 'meliconnect'); ?></button>
                                    </div> -->
                                    <div class="control mb-2">
                                        <button id="clear-all-matches" class="button is-primary button-meliconnect is-fullwidth"><?php echo __('Clear Matches', 'meliconnect'); ?></button>
                                    </div>

                                </div>

                                <div style="display: flex;">
                                    <div style="flex: 1;height: 100px; background-color: #f4f5f8"></div>
                                    <div class="divider is-vertical"> >> </div>
                                    <div style="flex: 1;height: 100px; background-color: #f4f5f8"></div>
                                </div>

                                <!-- Buttons Column -->
                                <div class="column is-3">
                                    <p><strong><?php echo __('Mercadolibre', 'meliconnect'); ?></strong> </p>
                                    <p><?php echo __('Total active items', 'meliconnect'); ?> :
                                        <span id="melicon-import-seller-total-items-active">
                                            <?php echo ($data['meli_user_listings_active_to_import_count']) ?>
                                        </span>
                                    </p>
                                    <p><?php echo __('Not active items', 'meliconnect'); ?> :
                                        <span id="melicon-import-seller-total-items-not-actived">
                                            <?php echo (($data['meli_user_listings_to_import_count'] - $data['meli_user_listings_active_to_import_count'])) ?>
                                        </span>
                                    </p>

                                    <p><strong><?php echo __('Woocommerce', 'meliconnect'); ?></strong> </p>
                                    <p>
                                        <?php echo __('Vinculated Products', 'meliconnect'); ?> :
                                        <span id="melicon-import-seller-total-products-vinculated">
                                            <?php echo ($data['woo_total_vinculated_products']) ?>
                                        </span>
                                    </p>
                                    <p>
                                        <?php echo __('Not Vinculated Products', 'meliconnect'); ?> :
                                        <span id="melicon-import-seller-total-products-desvinculated">
                                            <?php echo (($data['woo_total_active_products'] - $data['woo_total_vinculated_products'])) ?>
                                        </span>
                                    </p>
                                    <div class="control mt-4">
                                        <button id="melicon-process-import-button" class="button is-success button-meliconnect is-fullwidth">
                                            <span class="icon"><i class="fas fa-play"></i></span>
                                            <span><?php echo __('Process Import', 'meliconnect'); ?></span>
                                        </button>
                                    </div>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                <?php } ?>


                <div id="melicon-importer-table" class="melicon-card">
                    <?php if (isset($data['import_process_data']->status) && $data['import_process_data']->status == 'processing') { ?>
                        <div id="melicon-import-table-overlay" class="active"></div>
                    <?php } ?>
                    <div class="columns">
                        <div class="column-is-12">
                            <h2><?php echo __('Mercadolibre Listings', 'meliconnect'); ?></h2>

                            <div class="alignleft actions mt-4">
                                <!-- Filtros -->
                                <form method="get">
                                    <input type="hidden" name="page" value="meliconnect-importer">
                                    <div class="actions-filter">
                                        <div class="columns ">
                                            <div class="column">
                                                <?php

                                                $search_value = isset($_GET['search']) ? $_GET['search'] : '';
                                                $selected_vinculation = isset($_GET['vinculation_filter']) ? $_GET['vinculation_filter'] : '';
                                                $selected_listing_status = isset($_GET['listing_status_filter']) ? $_GET['listing_status_filter'] : '';
                                                $selected_template = isset($_GET['template_filter']) ? $_GET['template_filter'] : '';
                                                $selected_listing_type = isset($_GET['listing_type_filter']) ? $_GET['listing_type_filter'] : '';
                                                $selected_seller = isset($_GET['seller_filter']) ? $_GET['seller_filter'] : '';
                                                ?>
                                                <div class="field has-addons">
                                                    <div class="control">
                                                        <input id="user-search-input" class="input melicon-input" type="search" placeholder="<?php _e('Search By Title, SKU, Meli listing id ...', 'meliconnect'); ?>" name="search" value="<?php echo esc_attr($search_value); ?>">
                                                    </div>

                                                    <div class="control is-expanded">
                                                        <div class="select melicon-select is-fullwidth">
                                                            <select class="select melicon-select" name="vinculation_filter">
                                                                <option value=""><?php _e('All Vinculations', 'meliconnect'); ?></option>
                                                                <option value="yes_product" <?php selected($selected_vinculation, 'yes_product'); ?>><?php _e('With Vinculated Product', 'meliconnect'); ?></option>
                                                                <option value="no_product" <?php selected($selected_vinculation, 'no_product'); ?>><?php _e('Without Vinculated Product', 'meliconnect'); ?></option>
                                                                <!-- <option value="yes_template" <?php selected($selected_template, 'yes_template'); ?>><?php _e('With Vinculated Template', 'meliconnect'); ?></option>
                                                                <option value="no_template" <?php selected($selected_template, 'no_template'); ?>><?php _e('Without Vinculated Template', 'meliconnect'); ?></option> -->
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="control is-expanded">
                                                        <div class="select melicon-select is-fullwidth">
                                                            <select class="select melicon-select" name="listing_status_filter">
                                                                <option value=""><?php _e('All Status', 'meliconnect'); ?></option>
                                                                <option value="active" <?php selected($selected_listing_status, 'active'); ?>><?php _e('Active', 'meliconnect'); ?></option>
                                                                <option value="not_active" <?php selected($selected_listing_status, 'not_active'); ?>><?php _e('Not Active', 'meliconnect'); ?></option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="control is-expanded">
                                                        <div class="select melicon-select is-fullwidth">
                                                            <select class="select melicon-select" name="listing_type_filter">
                                                                <option value=""><?php _e('All Types', 'meliconnect'); ?></option>
                                                                <option value="simple" <?php selected($selected_listing_type, 'simple'); ?>><?php _e('Simple Listings', 'meliconnect'); ?></option>
                                                                <option value="variable" <?php selected($selected_listing_type, 'variable'); ?>><?php _e('Variable Listings', 'meliconnect'); ?></option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="control is-expanded">
                                                        <div class="select melicon-select is-fullwidth">
                                                            <?php echo Helper::getSellersSelect('seller_filter', true, $selected_seller); ?>
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
                                                            <button type="button" class="button button-meliconnect" onclick="window.location.href='?page=meliconnect-importer';">
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
                                <form id="melicon-import-bulk-actions-form" method="post">
                                    <input type="hidden" name="meli-listings-ids-checked" id="meli-listings-ids-checked" value="">
                                    <div class="actions-bulk mb-4">
                                        <div class="columns">
                                            <div class="column is-6">
                                                <select name="action-to-do" id="action-to-do">
                                                    <option value="-1"><?php _e('Bulk Actions', 'meliconnect'); ?></option>
                                                    <option value="import-selected"><?php _e('Import Selected', 'meliconnect'); ?></option>
                                                    <option value="match-items-products-by-name"><?php _e('Match selected with products by name', 'meliconnect'); ?></option>
                                                    <option value="match-items-products-by-sku"><?php _e('Match selected with products by sku', 'meliconnect'); ?></option>
                                                    <option value="desvinculate-items-products"><?php _e('Desvinculate selected with products', 'meliconnect'); ?></option>
                                                    <option value="desvinculate-items-and-delete"><?php _e('Desvinculate selected and delete in woocommerce', 'meliconnect'); ?></option>
                                                </select>

                                            </div>

                                            <div class="column is-2">
                                                <?php submit_button(__('Apply', 'meliconnect'), 'button button-meliconnect', 'melicon-import-bulk-actions', false); ?>
                                            </div>
                                            <div class="column melicon-import-selected-items-tag-column" style="display:none">
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

                            $userListingsTable = new StoreSync\Meliconnect\Modules\Importer\UserListingsTable();
                            $userListingsTable->prepare_items();
                            $userListingsTable->display();
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