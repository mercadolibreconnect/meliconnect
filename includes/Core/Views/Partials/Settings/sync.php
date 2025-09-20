<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<form id="meliconnect-sync-settings-form">

	<input type="hidden" name="checkbox_fields" id="checkbox_fields" value="meliconnect_sync_stock_woo_to_meli,meliconnect_sync_price_woo_to_meli,meliconnect_sync_status_woo_to_meli,meliconnect_sync_stock_meli_to_woo,meliconnect_sync_price_meli_to_woo,meliconnect_sync_variations_price_meli_to_woo">

	<section class="meliconnect-section">
		<div class="meliconnect-container">
			<div class="meliconnect-columns">
				<div class="meliconnect-column">
					<h2 class="meliconnect-title meliconnect-is-5"><?php esc_html_e( 'Automatic Synchronization', 'meliconnect' ); ?></h2>

					<div class="meliconnect-content">
						<div class="meliconnect-columns meliconnect-is-mobile meliconnect-is-multiline">
							<div class="meliconnect-column meliconnect-is-4">
								<div class="meliconnect-field">
									<label class="meliconnect-label" for="meliconnect_sync_cron_status"><?php esc_html_e( 'Apply on', 'meliconnect' ); ?></label>
									<div class="meliconnect-control">
										<div class="meliconnect-select meliconnect-is-fullwidth">
											<select name="meliconnect_sync_cron_status" id="meliconnect_sync_cron_status">
												<option value="deactive" <?php selected( $sync_data['meliconnect_sync_cron_status'], 'deactive' ); ?>><?php esc_html_e( 'Deactivate', 'meliconnect' ); ?></option>
												<option value="active" <?php selected( $sync_data['meliconnect_sync_cron_status'], 'active' ); ?>><?php esc_html_e( 'Active', 'meliconnect' ); ?></option>
											</select>
										</div>
									</div>
								</div>
							</div>
							<div class="meliconnect-column meliconnect-is-8">
								<div class="meliconnect-columns meliconnect-meliconnect-is-mobile">
									<div class="meliconnect-column meliconnect-is-5">
										<div class="meliconnect-field">
											<label class="meliconnect-label" for="meliconnect_sync_cron_items_batch"><?php esc_html_e( 'Items per batch', 'meliconnect' ); ?></label>
											<div class="meliconnect-control">
												<input class="meliconnect-input" type="number" min="1" name="meliconnect_sync_cron_items_batch" id="meliconnect_sync_cron_items_batch" value="<?php echo isset( $sync_data['meliconnect_sync_cron_items_batch'] ) ? esc_attr( $sync_data['meliconnect_sync_cron_items_batch'] ) : ''; ?>" min="1" max="1000">
											</div>
										</div>
									</div>
									<div class="meliconnect-column meliconnect-is-4">
										<div class="meliconnect-field">
											<label class="meliconnect-label" for="meliconnect_sync_cron_frecuency_minutes"><?php esc_html_e( 'Frequency (minutes)', 'meliconnect' ); ?></label>
											<div class="meliconnect-control">
												<input class="meliconnect-input" type="number" min="1" name="meliconnect_sync_cron_frecuency_minutes" id="meliconnect_sync_cron_frecuency_minutes" value="<?php echo isset( $sync_data['meliconnect_sync_cron_frecuency_minutes'] ) ? esc_attr( $sync_data['meliconnect_sync_cron_frecuency_minutes'] ) : ''; ?>">
											</div>
										</div>
									</div>
									<div class="meliconnect-column meliconnect-is-3">
										<div class="meliconnect-field">
											<label class="meliconnect-label" for="meliconnect_sync_cron_method"><?php esc_html_e( 'Method', 'meliconnect' ); ?></label>
											<div class="meliconnect-control">
												<div class="meliconnect-select meliconnect-is-fullwidth">
													<select name="meliconnect_sync_cron_method" id="meliconnect_sync_cron_method">
														<option value="wordpress" <?php selected( $sync_data['meliconnect_sync_cron_method'], 'WordPress' ); ?>><?php esc_html_e( 'WordPress', 'meliconnect' ); ?></option>
														<option value="custom" <?php selected( $sync_data['meliconnect_sync_cron_method'], 'custom' ); ?>><?php esc_html_e( 'Custom', 'meliconnect' ); ?></option>
													</select>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="meliconnect-columns meliconnect-mt-4">
							<div class="meliconnect-column meliconnect-is-9">
								<div class="meliconnect-content">
									<?php
									// Detectar si HTTPS está activo
									$https  = isset( $_SERVER['HTTPS'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTPS'] ) ) : '';
									$scheme = ( ! empty( $https ) && strtolower( $https ) !== 'off' ) ? 'https' : 'http';

									// Obtener el nombre del host de forma segura
									$host_raw = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
									$host     = wp_parse_url( '//' . $host_raw, PHP_URL_HOST ); // asegura un host válido

									// Construir la URL base
									$sync_url = $scheme . '://' . $host;
									?>
									<strong><?php esc_html_e( 'External automatic synchronization URL (custom):', 'meliconnect' ); ?></strong>
									<code><?php echo esc_url( $sync_url ); ?>/wp-json/meliconnect/v1/cronexternal/sync</code>
								</div>

							</div>
							<div class="meliconnect-column meliconnect-is-3">
								<div class="meliconnect-field  meliconnect-is-grouped meliconnect-is-grouped-right">
								</div>
							</div>
						</div>
						<hr>
						<div class="meliconnect-columns">
							<div id="sync-setting-left-meliconnect-column" class="meliconnect-column">
								<div class="meliconnect-columns">
									<div class="meliconnect-column">
										<h3 class="meliconnect-title meliconnect-is-6"><?php esc_html_e( 'From Woo to Meli', 'meliconnect' ); ?></h3>
									</div>
								</div>
								<div class="meliconnect-columns">
									<div class="meliconnect-column">
										<?php

										self::print_setting_checkbox(
											'meliconnect_sync_stock_woo_to_meli',
											esc_html__( 'Stock Synchronization', 'meliconnect' ),
											$sync_data['meliconnect_sync_stock_woo_to_meli'],
											'true',
											esc_html__( 'When changing the STOCK in WooCommerce, WooCommerce hooks are used to capture the new stock and update it in MercadoLibre. <br>(Do not enable this feature if you have different stocks in both channels.)', 'meliconnect' )
										);

										self::print_setting_checkbox(
											'meliconnect_sync_price_woo_to_meli',
											esc_html__( 'Price Synchronization', 'meliconnect' ),
											$sync_data['meliconnect_sync_price_woo_to_meli'],
											'true',
											esc_html__( 'When changing the PRICE in WooCommerce, WooCommerce hooks are used to capture the new price and update it in MercadoLibre. <br>(Do not enable this feature if you have different prices in both channels.)', 'meliconnect' )
										);

										self::print_setting_checkbox(
											'meliconnect_sync_status_woo_to_meli',
											esc_html__( 'Status Synchronization', 'meliconnect' ),
											$sync_data['meliconnect_sync_status_woo_to_meli'],
											'true',
											esc_html__( 'When changing the STATUS in WooCommerce, update it in MercadoLibre. <br>(Do not enable this feature if you manage different product statuses in both channels.)', 'meliconnect' )
										);
										?>
									</div>
								</div>


							</div>
							<div id="sync-setting-right-meliconnect-column" class="meliconnect-column">
								<div class="meliconnect-columns">
									<div class="meliconnect-column">
										<h3 class="meliconnect-title meliconnect-is-6"><?php esc_html_e( 'From Meli to Woo', 'meliconnect' ); ?></h3>
									</div>
								</div>

								<div class="meliconnect-columns">
									<div class="meliconnect-column">
										<?php

										self::print_setting_checkbox(
											'meliconnect_sync_stock_meli_to_woo',
											esc_html__( 'Stock Synchronization', 'meliconnect' ),
											$sync_data['meliconnect_sync_stock_meli_to_woo'],
											'true',
											esc_html__( 'When changing the STOCK in MercadoLibre, update the same in WooCommerce. <br>(Do not enable this feature if you have different stocks in both channels.)', 'meliconnect' )
										);

										self::print_setting_checkbox(
											'meliconnect_sync_price_meli_to_woo',
											esc_html__( 'Price Synchronization', 'meliconnect' ),
											$sync_data['meliconnect_sync_price_meli_to_woo'],
											'true',
											esc_html__( 'When changing the PRICE in MercadoLibre, update the same in WooCommerce. <br>(Do not enable this feature if you have different prices in both channels.)', 'meliconnect' )
										);

										self::print_setting_checkbox(
											'meliconnect_sync_variations_price_meli_to_woo',
											esc_html__( 'Variation Price Synchronization', 'meliconnect' ),
											$sync_data['meliconnect_sync_variations_price_meli_to_woo'],
											'true',
											esc_html__( 'When changing the PRICE in MercadoLibre, update it across all product variations in WooCommerce. <br>(Do not enable this feature if you have different prices in both channels.)', 'meliconnect' )
										);


										?>
									</div>
								</div>
							</div>
						</div>
						<div class="meliconnect-columns">
							<div class="meliconnect-column">
								<div class="meliconnect-level">
									<div class="meliconnect-level-left">
									</div>
									<div class="meliconnect-level-right">

										<div class="meliconnect-field meliconnect-is-grouped">
											<p class="meliconnect-control">
												<button id="save-sync-button" type="submit" class="meliconnect-button  meliconnect-is-primary"><?php esc_html_e( 'Save Sync Settings', 'meliconnect' ); ?></button>
											</p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

		</div>
	</section>
</form>