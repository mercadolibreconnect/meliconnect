<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
use Meliconnect\Meliconnect\Modules\Importer\Controllers\ImportController;

$importController = new ImportController();
$data             = $importController->getData();


$headerTitle = esc_html__( 'Importer', 'meliconnect' );

require MELICONNECT_PLUGIN_ROOT . 'includes/Core/Views/Partials/header.php';

?>
<!-- START MCSYNCAPP -->
<div id="meliconnect-page-importer-main" class="meliconnect-app">
	<div class="meliconnect-main">
		<div class="meliconnect-container">
			<!-- START FIND MATCH MODAL -->

			<div id="meliconnect-find-match-modal" class="meliconnect-modal">
				<div class="meliconnect-modal-background"></div>
				<div class="meliconnect-modal-card">
					<header class="meliconnect-modal-card-head">
						<p class="meliconnect-modal-card-title"><?php esc_html_e( 'Find Match', 'meliconnect' ); ?></p>
						<button class="meliconnect-modal-close meliconnect-is-large" aria-label="<?php esc_attr_e( 'close', 'meliconnect' ); ?>"></button>
					</header>
					<section class="meliconnect-modal-card-body">

						<div class="meliconnect-columns">
							<div class="meliconnect-column meliconnect-is-6">
								<p><strong><?php esc_html_e( 'Meli listing:', 'meliconnect' ); ?></strong>:</p>
							</div>
							<div class="meliconnect-column meliconnect-is-6">
								<p><strong><?php esc_html_e( 'Woo product:', 'meliconnect' ); ?></strong>:</p>
							</div>
						</div>
						<div class="meliconnect-columns">
							<div class="meliconnect-column meliconnect-is-6">
								<input type="hidden" name="meliconnect-match-modal_user-listing-id" id="meliconnect-match-modal_user-listing-id" value="">
								<p><strong id="meliconnect-meli-listing-title-to-match"></strong>:</p>
							</div>
							<div class="meliconnect-column meliconnect-is-6">
								<div id="meliconnect-match-product-select-container">
									<div class="meliconnect-field">
										<div class="meliconnect-control">
											<div class=" meliconnect-is-fullwidth">
												<select id="meliconnect-match-select-products-select" style="width: 100%;">

												</select>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="meliconnect-columns">
							<div id="meliconnect-meli-listing-data-to-match" class="meliconnect-column meliconnect-is-6">
							</div>
							<div id="meliconnect-matched-product-details" class="meliconnect-column meliconnect-is-6">
							</div>
						</div>

					</section>
					<footer class="meliconnect-modal-card-foot">
						<button id="meliconnect-apply-match-button" class="meliconnect-button  meliconnect-is-success "><?php esc_html_e( 'Apply match', 'meliconnect' ); ?></button>
						<button class="meliconnect-button  meliconnect-is-danger"><?php esc_html_e( 'Cancel', 'meliconnect' ); ?></button>
					</footer>
				</div>
			</div>

			<!-- END FIND MATCH MODAL -->
			<div id="meliconnect-importer-container" class="meliconnect-container meliconnect-importer-container meliconnect-overflow-x">
				<?php if ( isset( $data['import_process_data']->status ) && $data['import_process_data']->status == 'processing' ) { ?>
					<div id="meliconnect-process-in-progress" class="meliconnect-box">
						<div class="meliconnect-columns meliconnect-is-align-items-center">
							<!-- Progreso del proceso -->
							<div class="meliconnect-column meliconnect-is-6">
								<input type="hidden" id="meliconnect-process-id-hidden" name="process_id" value="<?php echo esc_attr( $data['import_process_data']->process_id ?? 0 ); ?>">
								<label class="meliconnect-label" id="meliconnect-process-text-title"><?php esc_html_e( 'Importing is in progress', 'meliconnect' ); ?></label>
								<progress id="meliconnect-process-progress-bar" class="progress meliconnect-is-info meliconnect-meliconnect-mb-2" value="0" max="100">0%</progress>
								<p><?php esc_html_e( 'Progress:', 'meliconnect' ); ?><span id="meliconnect-process-progress">0%</span> </p>


								<div class="meliconnect-buttons meliconnect-mt-4">

									<button id="meliconnect-importer-cancel-process" data-process-id="<?php echo esc_attr( $data['import_process_data']->process_id ?? 0 ); ?>" class="meliconnect-button meliconnect-is-danger">
										<span class="meliconnect-icon">
											<i class="fas fa-trash"></i>
										</span>
										<span><?php esc_html_e( 'Cancel', 'meliconnect' ); ?></span>
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
											<p><strong><?php esc_html_e( 'Executed:', 'meliconnect' ); ?></strong><span id="meliconnect-process-executed"><?php echo esc_html( $data['import_process_data']->executed ?? 0 ); ?></span> </p>
											<p><strong><?php esc_html_e( 'Total:', 'meliconnect' ); ?></strong><span id="meliconnect-process-total"><?php echo esc_html( $data['import_process_data']->total ?? 0 ); ?></span></p>
										</div>
									</div>
									<div class="meliconnect-column meliconnect-is-6">
										<div class="content">
											<p><strong><?php esc_html_e( 'Success:', 'meliconnect' ); ?></strong><span id="meliconnect-process-total-success"> <?php echo esc_html( $data['import_process_data']->total_success ?? 0 ); ?></span></p>
											<p><strong><?php esc_html_e( 'Fails:', 'meliconnect' ); ?></strong><span id="meliconnect-process-total-fails"> <?php echo esc_html( $data['import_process_data']->total_fails ?? 0 ); ?></span></p>
										</div>
									</div>
								</div>
								<div class="meliconnect-columns">
									<div class="meliconnect-column meliconnect-is-12">
										<p><strong><?php esc_html_e( 'Execution Time:', 'meliconnect' ); ?></strong><span id="meliconnect-process-execution-time"> <?php echo esc_html( $data['execution_time'] ); ?> </span> </p>
									</div>
								</div>
							</div>
						</div>

					</div>
				<?php } elseif ( isset( $data['import_process_finished']->status ) && $data['import_process_finished']->status == 'finished' ) { ?>
					<div id="meliconnect-process-finished" class="meliconnect-box">
						<div class="meliconnect-columns meliconnect-is-align-items-center">
							<!-- Progreso del proceso -->
							<div class="meliconnect-column meliconnect-is-6">
								<label class="meliconnect-label" id="meliconnect-process-text-title"><?php esc_html_e( 'Importing finished', 'meliconnect' ); ?></label>

								<div class="meliconnect-buttons meliconnect-mt-4">
									<button id="meliconnect-importer-view-logs" class="meliconnect-button meliconnect-is-warning">
										<span class="meliconnect-icon">
											<i class="fas fa-solid fa-eye"></i>
										</span>
										<span><?php esc_html_e( 'View log details', 'meliconnect' ); ?></span>
									</button>
									<button id="meliconnect-importer-delete-finished" data-process-id="<?php echo esc_attr( $data['import_process_finished']->process_id ?? 0 ); ?>" class="meliconnect-button meliconnect-is-success">
										<span class="meliconnect-icon">
											<i class="fas fa-sync"></i>
										</span>
										<span><?php esc_html_e( 'Strat new import', 'meliconnect' ); ?></span>
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

								<div class="content">
									<p><strong><?php esc_html_e( 'Success:', 'meliconnect' ); ?></strong><span id="meliconnect-process-total-success"> <?php echo esc_html( $data['import_process_finished']->total_success ?? 0 ); ?></span></p>
									<p><strong><?php esc_html_e( 'Fails:', 'meliconnect' ); ?></strong><span id="meliconnect-process-total-fails"> <?php echo esc_html( $data['import_process_finished']->total_fails ?? 0 ); ?></span></p>
								</div>

							</div>
						</div>

					</div>
				<?php } else { ?>
					<div id="" class="meliconnect-card meliconnect-p-4">
						<div class="meliconnect-columns meliconnect-is-align-items-center">
							<!-- Inline Form Column -->
							<div class="meliconnect-column meliconnect-is-4">
								<form class="is-flex meliconnect-is-flex-direction-column" id="meliconnect-get-meli-user-listings" method="POST">
									<div class="meliconnect-field mb-3">
										<label class="meliconnect-label"><?php esc_html_e( 'Select seller', 'meliconnect' ); ?></label>

										<?php
										$selectName = 'user_id';
										include MELICONNECT_PLUGIN_ROOT . 'includes/Core/Views/Partials/meliconnect_sellers_select.php';
										?>
									</div>

									<div class="meliconnect-control">
										<div class="meliconnect-buttons">
											<button id="meliconnect-get-meli-user-listings-button" type="submit" class="meliconnect-button meliconnect-is-primary "><?php esc_html_e( 'Get Listings', 'meliconnect' ); ?></button>
											<?php
											if ( $data['meli_user_listings_to_import_count'] > 0 ) {
												?>
												<button id="meliconnect-reset-meli-user-listings-button" class="meliconnect-button meliconnect-is-warning "><?php esc_html_e( 'Clean Listings', 'meliconnect' ); ?></button>
												<?php
											}
											?>
										</div>
									</div>
								</form>
							</div>
							<?php
							if ( $data['meli_user_listings_to_import_count'] > 0 ) {
								?>
								<div style="display: flex;">
									<div style="flex: 1;height: 100px; background-color: #f4f5f8"></div>
									<div class="divider meliconnect-is-vertical"> > </div>
									<div style="flex: 1;height: 100px; background-color: #f4f5f8"></div>
								</div>

								<div class="meliconnect-column meliconnect-is-4">

									<div class="meliconnect-control meliconnect-mb-2">
										<button data-match-by="sku" class="match-all-listings-with-products meliconnect-button meliconnect-is-link meliconnect-is-light  meliconnect-is-fullwidth"><?php esc_html_e( 'Match Listings with Products by SKU', 'meliconnect' ); ?></button>
									</div>
									<div class="meliconnect-control meliconnect-mb-2">
										<button data-match-by="name" class="match-all-listings-with-products meliconnect-button meliconnect-is-link meliconnect-is-light  meliconnect-is-fullwidth"><?php esc_html_e( 'Match Listings with Products by Name', 'meliconnect' ); ?></button>
									</div>
									<!-- <div class="meliconnect-control meliconnect-mb-2">
										<button class="meliconnect-button meliconnect-is-link  meliconnect-is-fullwidth"><?php esc_html_e( 'Match Items with Templates', 'meliconnect' ); ?></button>
									</div> -->
									<div class="meliconnect-control meliconnect-mb-2">
										<button id="clear-all-matches" class="meliconnect-button meliconnect-is-primary  meliconnect-is-light meliconnect-is-fullwidth"><?php esc_html_e( 'Clear Matches', 'meliconnect' ); ?></button>
									</div>

								</div>

								<div style="display: flex;">
									<div style="flex: 1;height: 100px; background-color: #f4f5f8"></div>
									<div class="divider meliconnect-is-vertical"> >> </div>
									<div style="flex: 1;height: 100px; background-color: #f4f5f8"></div>
								</div>

								<!-- Buttons Column -->
								<div class="meliconnect-column meliconnect-is-3">
									<p><strong><?php esc_html_e( 'Mercadolibre', 'meliconnect' ); ?></strong> </p>
									<p><?php esc_html_e( 'Total active items', 'meliconnect' ); ?> :
										<span id="meliconnect-import-seller-total-items-active">
											<?php echo esc_html( $data['meli_user_listings_active_to_import_count'] ); ?>
										</span>
									</p>
									<p><?php esc_html_e( 'Not active items', 'meliconnect' ); ?> :
										<span id="meliconnect-import-seller-total-items-not-actived">
											<?php echo esc_html( ( $data['meli_user_listings_to_import_count'] - $data['meli_user_listings_active_to_import_count'] ) ); ?>
										</span>
									</p>

									<p><strong><?php esc_html_e( 'Woocommerce', 'meliconnect' ); ?></strong> </p>
									<p>
										<?php esc_html_e( 'Vinculated Products', 'meliconnect' ); ?> :
										<span id="meliconnect-import-seller-total-products-vinculated">
											<?php echo esc_html( $data['woo_total_vinculated_products'] ); ?>
										</span>
									</p>
									<p>
										<?php esc_html_e( 'Not Vinculated Products', 'meliconnect' ); ?> :
										<span id="meliconnect-import-seller-total-products-desvinculated">
											<?php echo esc_html( ( $data['woo_total_active_products'] - $data['woo_total_vinculated_products'] ) ); ?>
										</span>
									</p>
									<div class="meliconnect-control meliconnect-mt-4">
										<button id="meliconnect-process-import-button" class="meliconnect-button meliconnect-is-success  meliconnect-is-fullwidth">
											<span class="meliconnect-icon"><i class="fas fa-play"></i></span>
											<span><?php esc_html_e( 'Process Import', 'meliconnect' ); ?></span>
										</button>
									</div>
								</div>
								<?php
							}
							?>
						</div>
					</div>
				<?php } ?>


				<div id="meliconnect-importer-table" class="meliconnect-card meliconnect-p-4">
					<?php if ( isset( $data['import_process_data']->status ) && $data['import_process_data']->status == 'processing' ) { ?>
						<div id="meliconnect-import-table-overlay" class="active"></div>
					<?php } ?>
					<div class="meliconnect-columns">
						<div class="meliconnect-column-is-12">
							<h2><?php esc_html_e( 'Mercadolibre Listings', 'meliconnect' ); ?></h2>

							<div class="alignleft actions meliconnect-mt-4">
								<!-- Filtros -->
								<form method="get">
									<input type="hidden" name="page" value="meliconnect-importer">
									<div class="actions-filter">
										<div class="meliconnect-columns ">
											<div class="meliconnect-column">
												<?php
												$search_value            = isset( $_GET['search'] ) ? sanitize_text_field( wp_unslash( $_GET['search'] ) ) : '';
												$selected_vinculation    = isset( $_GET['vinculation_filter'] ) ? sanitize_text_field( wp_unslash( $_GET['vinculation_filter'] ) ) : '';
												$selected_listing_status = isset( $_GET['listing_status_filter'] ) ? sanitize_text_field( wp_unslash( $_GET['listing_status_filter'] ) ) : '';
												$selected_template       = isset( $_GET['template_filter'] ) ? sanitize_text_field( wp_unslash( $_GET['template_filter'] ) ) : '';
												$selected_listing_type   = isset( $_GET['listing_type_filter'] ) ? sanitize_text_field( wp_unslash( $_GET['listing_type_filter'] ) ) : '';
												$selected_seller         = isset( $_GET['seller_filter'] ) ? sanitize_text_field( wp_unslash( $_GET['seller_filter'] ) ) : '';
												?>
												<div class="meliconnect-field meliconnect-has-addons">
													<div class="meliconnect-control">
														<input id="user-search-input" class="meliconnect-input" type="search" placeholder="<?php esc_html_e( 'Search By Title, SKU, Meli listing id ...', 'meliconnect' ); ?>" name="search" value="<?php echo esc_attr( $search_value ); ?>">
													</div>

													<div class="meliconnect-control meliconnect-is-expanded">
														<div class="meliconnect-select meliconnect-is-fullwidth">
															<select class="meliconnect-select" name="vinculation_filter">
																<option value=""><?php esc_html_e( 'All Vinculations', 'meliconnect' ); ?></option>
																<option value="yes_product" <?php selected( $selected_vinculation, 'yes_product' ); ?>><?php esc_html_e( 'With Vinculated Product', 'meliconnect' ); ?></option>
																<option value="no_product" <?php selected( $selected_vinculation, 'no_product' ); ?>><?php esc_html_e( 'Without Vinculated Product', 'meliconnect' ); ?></option>
																<!-- <option value="yes_template" <?php selected( $selected_template, 'yes_template' ); ?>><?php esc_html_e( 'With Vinculated Template', 'meliconnect' ); ?></option>
																<option value="no_template" <?php selected( $selected_template, 'no_template' ); ?>><?php esc_html_e( 'Without Vinculated Template', 'meliconnect' ); ?></option> -->
															</select>
														</div>
													</div>

													<div class="meliconnect-control meliconnect-is-expanded">
														<div class="meliconnect-select meliconnect-is-fullwidth">
															<select class="meliconnect-select" name="listing_status_filter">
																<option value=""><?php esc_html_e( 'All Status', 'meliconnect' ); ?></option>
																<option value="active" <?php selected( $selected_listing_status, 'active' ); ?>><?php esc_html_e( 'Active', 'meliconnect' ); ?></option>
																<option value="not_active" <?php selected( $selected_listing_status, 'not_active' ); ?>><?php esc_html_e( 'Not Active', 'meliconnect' ); ?></option>
															</select>
														</div>
													</div>

													<div class="meliconnect-control meliconnect-is-expanded">
														<div class="meliconnect-select meliconnect-is-fullwidth">
															<select class="meliconnect-select" name="listing_type_filter">
																<option value=""><?php esc_html_e( 'All Types', 'meliconnect' ); ?></option>
																<option value="simple" <?php selected( $selected_listing_type, 'simple' ); ?>><?php esc_html_e( 'Simple Listings', 'meliconnect' ); ?></option>
																<option value="variable" <?php selected( $selected_listing_type, 'variable' ); ?>><?php esc_html_e( 'Variable Listings', 'meliconnect' ); ?></option>
															</select>
														</div>
													</div>

													<div class="meliconnect-control meliconnect-is-expanded">
														<div class="meliconnect-select meliconnect-is-fullwidth">

															<?php
															$sellerSelectAddAll = true;

															require MELICONNECT_PLUGIN_ROOT . 'includes/Core/Views/Partials/meliconnect_sellers_select.php';
															?>
														</div>
													</div>

													<div class="meliconnect-control">
														<p class="meliconnect-buttons meliconnect-has-addons">
															<!-- <?php submit_button( esc_html__( 'Filter', 'meliconnect' ), 'meliconnect-button ', 'filter_action', false ); ?> -->
															<button type="submit" class="meliconnect-button ">
																<span class="meliconnect-icon meliconnect-is-small">
																	<i class="fas fa-search"></i>
																</span>
															</button>
															<button type="button" class="meliconnect-button " onclick="window.location.href='?page=meliconnect-importer';">
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
								<form id="meliconnect-import-bulk-actions-form" method="post">
									<input type="hidden" name="meli-listings-ids-checked" id="meli-listings-ids-checked" value="">
									<div class="actions-bulk mb-4">
										<div class="meliconnect-columns">
											<div class="meliconnect-column meliconnect-is-6">
												<select name="action-to-do" id="action-to-do">
													<option value="-1"><?php esc_html_e( 'Bulk Actions', 'meliconnect' ); ?></option>
													<option value="import-selected"><?php esc_html_e( 'Import Selected', 'meliconnect' ); ?></option>
													<option value="match-items-products-by-name"><?php esc_html_e( 'Match selected with products by name', 'meliconnect' ); ?></option>
													<option value="match-items-products-by-sku"><?php esc_html_e( 'Match selected with products by sku', 'meliconnect' ); ?></option>
													<option value="desvinculate-items-products"><?php esc_html_e( 'Desvinculate selected with products', 'meliconnect' ); ?></option>
													<option value="desvinculate-items-and-delete"><?php esc_html_e( 'Desvinculate selected and delete in woocommerce', 'meliconnect' ); ?></option>
												</select>

											</div>

											<div class="meliconnect-column meliconnect-is-2">
												<?php submit_button( esc_html__( 'Apply', 'meliconnect' ), 'meliconnect-button ', 'meliconnect-import-bulk-actions', false ); ?>
											</div>
											<div class="meliconnect-column meliconnect-import-selected-items-tag-column" style="display:none">
												<span class="tag meliconnect-is-success meliconnect-is-light meliconnect-is-large">
													<span id="selected-items-count"></span> &nbsp; <?php esc_html_e( 'Items selected', 'meliconnect' ); ?>
													<button type="button" class="delete" id="meliconnect-clear-selected-items"></button>
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

	<?php require MELICONNECT_PLUGIN_ROOT . 'includes/Core/Views/Partials/footer.php'; ?>
</div>
<!-- END MCSYNCAPP -->