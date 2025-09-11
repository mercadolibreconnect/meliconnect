<?php

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
use Meliconnect\Meliconnect\Modules\Importer\Controllers\ImportController;

$importController = new ImportController();
$data = $importController->getData();


$headerTitle = esc_html__('Importer', 'meliconnect');

include MC_PLUGIN_ROOT . 'includes/Core/Views/Partials/header.php';

?>
<!-- START MCSYNCAPP -->
<div id="melicon-page-importer-main" class="melicon-app">
    <div class="melicon-main">
        <div class="melicon-container">
            <!-- START FIND MATCH MODAL -->

            <div id="melicon-find-match-modal" class="melicon-modal">
                <div class="melicon-modal-background"></div>
                <div class="melicon-modal-card">
                    <header class="melicon-modal-card-head">
                        <p class="melicon-modal-card-title"><?php esc_html_e('Find Match', 'meliconnect'); ?></p>
                        <button class="melicon-modal-close melicon-is-large" aria-label="<?php esc_attr_e('close', 'meliconnect'); ?>"></button>
                    </header>
                    <section class="melicon-modal-card-body">

                        <div class="melicon-columns">
                            <div class="melicon-column melicon-is-6">
                                <p><strong><?php esc_html_e('Meli listing:', 'meliconnect'); ?></strong>:</p>
                            </div>
                            <div class="melicon-column melicon-is-6">
                                <p><strong><?php esc_html_e('Woo product:', 'meliconnect'); ?></strong>:</p>
                            </div>
                        </div>
                        <div class="melicon-columns">
                            <div class="melicon-column melicon-is-6">
                                <input type="hidden" name="melicon-match-modal_user-listing-id" id="melicon-match-modal_user-listing-id" value="">
                                <p><strong id="melicon-meli-listing-title-to-match"></strong>:</p>
                            </div>
                            <div class="melicon-column melicon-is-6">
                                <div id="melicon-match-product-select-container">
                                    <div class="melicon-field">
                                        <div class="melicon-control">
                                            <div class=" melicon-is-fullwidth">
                                                <select id="melicon-match-select-products-select" style="width: 100%;">

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="melicon-columns">
                            <div id="melicon-meli-listing-data-to-match" class="melicon-column melicon-is-6">
                            </div>
                            <div id="melicon-matched-product-details" class="melicon-column melicon-is-6">
                            </div>
                        </div>

                    </section>
                    <footer class="melicon-modal-card-foot">
                        <button id="melicon-apply-match-button" class="melicon-button  melicon-is-success "><?php esc_html_e('Apply match', 'meliconnect'); ?></button>
                        <button class="melicon-button  melicon-is-danger"><?php esc_html_e('Cancel', 'meliconnect'); ?></button>
                    </footer>
                </div>
            </div>

            <!-- END FIND MATCH MODAL -->
            <div id="melicon-importer-container" class="melicon-container melicon-importer-container melicon-overflow-x">
                <?php if (isset($data['import_process_data']->status) && $data['import_process_data']->status == 'processing') { ?>
                    <div id="melicon-process-in-progress" class="melicon-box">
                        <div class="melicon-columns melicon-is-align-items-center">
                            <!-- Progreso del proceso -->
                            <div class="melicon-column melicon-is-6">
                                <input type="hidden" id="melicon-process-id-hidden" name="process_id" value="<?php echo esc_attr($data['import_process_data']->process_id ?? 0); ?>">
                                <label class="melicon-label" id="melicon-process-text-title"><?php esc_html_e('Importing is in progress', 'meliconnect'); ?></label>
                                <progress id="melicon-process-progress-bar" class="progress melicon-is-info melicon-melicon-mb-2" value="0" max="100">0%</progress>
                                <p><?php esc_html_e('Progress:', 'meliconnect'); ?><span id="melicon-process-progress">0%</span> </p>


                                <div class="melicon-buttons melicon-mt-4">

                                    <button id="melicon-importer-cancel-process" data-process-id="<?php echo esc_attr($data['import_process_data']->process_id ?? 0); ?>" class="melicon-button melicon-is-danger">
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
                                            <p><strong><?php esc_html_e('Executed:', 'meliconnect'); ?></strong><span id="melicon-process-executed"><?php echo esc_html($data['import_process_data']->executed ?? 0); ?></span> </p>
                                            <p><strong><?php esc_html_e('Total:', 'meliconnect'); ?></strong><span id="melicon-process-total"><?php echo esc_html($data['import_process_data']->total ?? 0); ?></span></p>
                                        </div>
                                    </div>
                                    <div class="melicon-column melicon-is-6">
                                        <div class="content">
                                            <p><strong><?php esc_html_e('Success:', 'meliconnect'); ?></strong><span id="melicon-process-total-success"> <?php echo esc_html($data['import_process_data']->total_success ?? 0); ?></span></p>
                                            <p><strong><?php esc_html_e('Fails:', 'meliconnect'); ?></strong><span id="melicon-process-total-fails"> <?php echo esc_html($data['import_process_data']->total_fails ?? 0); ?></span></p>
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
                <?php } else if (isset($data['import_process_finished']->status) && $data['import_process_finished']->status == 'finished') { ?>
                    <div id="melicon-process-finished" class="melicon-box">
                        <div class="melicon-columns melicon-is-align-items-center">
                            <!-- Progreso del proceso -->
                            <div class="melicon-column melicon-is-6">
                                <label class="melicon-label" id="melicon-process-text-title"><?php esc_html_e('Importing finished', 'meliconnect'); ?></label>

                                <div class="melicon-buttons melicon-mt-4">
                                    <button id="melicon-importer-view-logs" class="melicon-button melicon-is-warning">
                                        <span class="melicon-icon">
                                            <i class="fas fa-solid fa-eye"></i>
                                        </span>
                                        <span><?php esc_html_e('View log details', 'meliconnect'); ?></span>
                                    </button>
                                    <button id="melicon-importer-delete-finished" data-process-id="<?php echo esc_attr($data['import_process_finished']->process_id ?? 0); ?>" class="melicon-button melicon-is-success">
                                        <span class="melicon-icon">
                                            <i class="fas fa-sync"></i>
                                        </span>
                                        <span><?php esc_html_e('Strat new import', 'meliconnect'); ?></span>
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

                                <div class="content">
                                    <p><strong><?php esc_html_e('Success:', 'meliconnect'); ?></strong><span id="melicon-process-total-success"> <?php echo esc_html($data['import_process_finished']->total_success ?? 0); ?></span></p>
                                    <p><strong><?php esc_html_e('Fails:', 'meliconnect'); ?></strong><span id="melicon-process-total-fails"> <?php echo esc_html($data['import_process_finished']->total_fails ?? 0); ?></span></p>
                                </div>

                            </div>
                        </div>

                    </div>
                <?php } else { ?>
                    <div id="" class="melicon-card melicon-p-4">
                        <div class="melicon-columns melicon-is-align-items-center">
                            <!-- Inline Form Column -->
                            <div class="melicon-column melicon-is-4">
                                <form class="is-flex melicon-is-flex-direction-column" id="melicon-get-meli-user-listings" method="POST">
                                    <div class="melicon-field mb-3">
                                        <label class="melicon-label"><?php esc_html_e('Select seller', 'meliconnect'); ?></label>

                                        <?php
                                        $selectName = 'user_id';
                                        include MC_PLUGIN_ROOT . 'includes/Core/Views/Partials/meliconnect_sellers_select.php';
                                        ?>
                                    </div>

                                    <div class="melicon-control">
                                        <div class="melicon-buttons">
                                            <button id="melicon-get-meli-user-listings-button" type="submit" class="melicon-button melicon-is-primary "><?php esc_html_e('Get Listings', 'meliconnect'); ?></button>
                                            <?php
                                            if ($data['meli_user_listings_to_import_count'] > 0) {
                                            ?>
                                                <button id="melicon-reset-meli-user-listings-button" class="melicon-button melicon-is-warning "><?php esc_html_e('Clean Listings', 'meliconnect'); ?></button>
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
                                    <div class="divider melicon-is-vertical"> > </div>
                                    <div style="flex: 1;height: 100px; background-color: #f4f5f8"></div>
                                </div>

                                <div class="melicon-column melicon-is-4">

                                    <div class="melicon-control melicon-mb-2">
                                        <button data-match-by="sku" class="match-all-listings-with-products melicon-button melicon-is-link melicon-is-light  melicon-is-fullwidth"><?php esc_html_e('Match Listings with Products by SKU', 'meliconnect'); ?></button>
                                    </div>
                                    <div class="melicon-control melicon-mb-2">
                                        <button data-match-by="name" class="match-all-listings-with-products melicon-button melicon-is-link melicon-is-light  melicon-is-fullwidth"><?php esc_html_e('Match Listings with Products by Name', 'meliconnect'); ?></button>
                                    </div>
                                    <!-- <div class="melicon-control melicon-mb-2">
                                        <button class="melicon-button melicon-is-link  melicon-is-fullwidth"><?php esc_html_e('Match Items with Templates', 'meliconnect'); ?></button>
                                    </div> -->
                                    <div class="melicon-control melicon-mb-2">
                                        <button id="clear-all-matches" class="melicon-button melicon-is-primary  melicon-is-light melicon-is-fullwidth"><?php esc_html_e('Clear Matches', 'meliconnect'); ?></button>
                                    </div>

                                </div>

                                <div style="display: flex;">
                                    <div style="flex: 1;height: 100px; background-color: #f4f5f8"></div>
                                    <div class="divider melicon-is-vertical"> >> </div>
                                    <div style="flex: 1;height: 100px; background-color: #f4f5f8"></div>
                                </div>

                                <!-- Buttons Column -->
                                <div class="melicon-column melicon-is-3">
                                    <p><strong><?php esc_html_e('Mercadolibre', 'meliconnect'); ?></strong> </p>
                                    <p><?php esc_html_e('Total active items', 'meliconnect'); ?> :
                                        <span id="melicon-import-seller-total-items-active">
                                            <?php echo esc_html($data['meli_user_listings_active_to_import_count']) ?>
                                        </span>
                                    </p>
                                    <p><?php esc_html_e('Not active items', 'meliconnect'); ?> :
                                        <span id="melicon-import-seller-total-items-not-actived">
                                            <?php echo esc_html(($data['meli_user_listings_to_import_count'] - $data['meli_user_listings_active_to_import_count'])) ?>
                                        </span>
                                    </p>

                                    <p><strong><?php esc_html_e('Woocommerce', 'meliconnect'); ?></strong> </p>
                                    <p>
                                        <?php esc_html_e('Vinculated Products', 'meliconnect'); ?> :
                                        <span id="melicon-import-seller-total-products-vinculated">
                                            <?php echo esc_html($data['woo_total_vinculated_products']) ?>
                                        </span>
                                    </p>
                                    <p>
                                        <?php esc_html_e('Not Vinculated Products', 'meliconnect'); ?> :
                                        <span id="melicon-import-seller-total-products-desvinculated">
                                            <?php echo esc_html(($data['woo_total_active_products'] - $data['woo_total_vinculated_products'])); ?>
                                        </span>
                                    </p>
                                    <div class="melicon-control melicon-mt-4">
                                        <button id="melicon-process-import-button" class="melicon-button melicon-is-success  melicon-is-fullwidth">
                                            <span class="melicon-icon"><i class="fas fa-play"></i></span>
                                            <span><?php esc_html_e('Process Import', 'meliconnect'); ?></span>
                                        </button>
                                    </div>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                <?php } ?>


                <div id="melicon-importer-table" class="melicon-card melicon-p-4">
                    <?php if (isset($data['import_process_data']->status) && $data['import_process_data']->status == 'processing') { ?>
                        <div id="melicon-import-table-overlay" class="active"></div>
                    <?php } ?>
                    <div class="melicon-columns">
                        <div class="melicon-column-is-12">
                            <h2><?php esc_html_e('Mercadolibre Listings', 'meliconnect'); ?></h2>

                            <div class="alignleft actions melicon-mt-4">
                                <!-- Filtros -->
                                <form method="get">
                                    <input type="hidden" name="page" value="meliconnect-importer">
                                    <div class="actions-filter">
                                        <div class="melicon-columns ">
                                            <div class="melicon-column">
                                                <?php
                                                $search_value           = isset($_GET['search']) ? sanitize_text_field(wp_unslash($_GET['search'])) : '';
                                                $selected_vinculation   = isset($_GET['vinculation_filter']) ? sanitize_text_field(wp_unslash($_GET['vinculation_filter'])) : '';
                                                $selected_listing_status = isset($_GET['listing_status_filter']) ? sanitize_text_field(wp_unslash($_GET['listing_status_filter'])) : '';
                                                $selected_template      = isset($_GET['template_filter']) ? sanitize_text_field(wp_unslash($_GET['template_filter'])) : '';
                                                $selected_listing_type  = isset($_GET['listing_type_filter']) ? sanitize_text_field(wp_unslash($_GET['listing_type_filter'])) : '';
                                                $selected_seller        = isset($_GET['seller_filter']) ? sanitize_text_field(wp_unslash($_GET['seller_filter'])) : '';
                                                ?>
                                                <div class="melicon-field melicon-has-addons">
                                                    <div class="melicon-control">
                                                        <input id="user-search-input" class="melicon-input" type="search" placeholder="<?php esc_html_e('Search By Title, SKU, Meli listing id ...', 'meliconnect'); ?>" name="search" value="<?php echo esc_attr($search_value); ?>">
                                                    </div>

                                                    <div class="melicon-control melicon-is-expanded">
                                                        <div class="melicon-select melicon-is-fullwidth">
                                                            <select class="melicon-select" name="vinculation_filter">
                                                                <option value=""><?php esc_html_e('All Vinculations', 'meliconnect'); ?></option>
                                                                <option value="yes_product" <?php selected($selected_vinculation, 'yes_product'); ?>><?php esc_html_e('With Vinculated Product', 'meliconnect'); ?></option>
                                                                <option value="no_product" <?php selected($selected_vinculation, 'no_product'); ?>><?php esc_html_e('Without Vinculated Product', 'meliconnect'); ?></option>
                                                                <!-- <option value="yes_template" <?php selected($selected_template, 'yes_template'); ?>><?php esc_html_e('With Vinculated Template', 'meliconnect'); ?></option>
                                                                <option value="no_template" <?php selected($selected_template, 'no_template'); ?>><?php esc_html_e('Without Vinculated Template', 'meliconnect'); ?></option> -->
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="melicon-control melicon-is-expanded">
                                                        <div class="melicon-select melicon-is-fullwidth">
                                                            <select class="melicon-select" name="listing_status_filter">
                                                                <option value=""><?php esc_html_e('All Status', 'meliconnect'); ?></option>
                                                                <option value="active" <?php selected($selected_listing_status, 'active'); ?>><?php esc_html_e('Active', 'meliconnect'); ?></option>
                                                                <option value="not_active" <?php selected($selected_listing_status, 'not_active'); ?>><?php esc_html_e('Not Active', 'meliconnect'); ?></option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="melicon-control melicon-is-expanded">
                                                        <div class="melicon-select melicon-is-fullwidth">
                                                            <select class="melicon-select" name="listing_type_filter">
                                                                <option value=""><?php esc_html_e('All Types', 'meliconnect'); ?></option>
                                                                <option value="simple" <?php selected($selected_listing_type, 'simple'); ?>><?php esc_html_e('Simple Listings', 'meliconnect'); ?></option>
                                                                <option value="variable" <?php selected($selected_listing_type, 'variable'); ?>><?php esc_html_e('Variable Listings', 'meliconnect'); ?></option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="melicon-control melicon-is-expanded">
                                                        <div class="melicon-select melicon-is-fullwidth">

                                                            <?php
                                                            $sellerSelectAddAll = true;

                                                            include MC_PLUGIN_ROOT . 'includes/Core/Views/Partials/meliconnect_sellers_select.php';
                                                            ?>
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
                                                            <button type="button" class="melicon-button " onclick="window.location.href='?page=meliconnect-importer';">
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
                                <form id="melicon-import-bulk-actions-form" method="post">
                                    <input type="hidden" name="meli-listings-ids-checked" id="meli-listings-ids-checked" value="">
                                    <div class="actions-bulk mb-4">
                                        <div class="melicon-columns">
                                            <div class="melicon-column melicon-is-6">
                                                <select name="action-to-do" id="action-to-do">
                                                    <option value="-1"><?php esc_html_e('Bulk Actions', 'meliconnect'); ?></option>
                                                    <option value="import-selected"><?php esc_html_e('Import Selected', 'meliconnect'); ?></option>
                                                    <option value="match-items-products-by-name"><?php esc_html_e('Match selected with products by name', 'meliconnect'); ?></option>
                                                    <option value="match-items-products-by-sku"><?php esc_html_e('Match selected with products by sku', 'meliconnect'); ?></option>
                                                    <option value="desvinculate-items-products"><?php esc_html_e('Desvinculate selected with products', 'meliconnect'); ?></option>
                                                    <option value="desvinculate-items-and-delete"><?php esc_html_e('Desvinculate selected and delete in woocommerce', 'meliconnect'); ?></option>
                                                </select>

                                            </div>

                                            <div class="melicon-column melicon-is-2">
                                                <?php submit_button(esc_html__('Apply', 'meliconnect'), 'melicon-button ', 'melicon-import-bulk-actions', false); ?>
                                            </div>
                                            <div class="melicon-column melicon-import-selected-items-tag-column" style="display:none">
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

                            $userListingsTable = new Meliconnect\Meliconnect\Modules\Importer\UserListingsTable();
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