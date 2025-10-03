<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<form id="meliconnect-general-settings-form">
	<section class="meliconnect-section">
		<div class="meliconnect-container">
			<div class="meliconnect-columns">
				<div class="meliconnect-column">
					<label class="meliconnect-label"><?php esc_html_e( 'Additional Images', 'meliconnect' ); ?></label>
					<p><?php esc_html_e( 'Here you configure the general parameters that will apply to all publications, such as photos and description template.', 'meliconnect' ); ?></p>
				</div>
			</div>
			<div id="meliconnect-image-uploaders" class="meliconnect-columns">

				<?php
				$extra_images = $general_data['meliconnect_general_image_attachment_ids'] ?? [];

                $extra_images = array_filter($extra_images, function($value) {
                    return !empty($value);
                });

                $extra_images = array_values($extra_images);

				for ( $i = 0; $i <= 4; $i++ ) :
					?>
					<div class="meliconnect-column meliconnect-is-one-fifth">
						<div class="meliconnect-image-uploader">
							<button type="button" class="delete-image" style="<?php echo isset( $extra_images[ $i ] ) ? '' : 'display: none;'; ?>"><i class="fas fa-times"></i></button>
							<div class="meliconnect-file meliconnect-has-name meliconnect-is-boxed">
								<label class="meliconnect-file-label">
									<input class="meliconnect-file-input upload_image_button" type="button" name="resume">
									<span class="meliconnect-file-cta" style="<?php echo isset( $extra_images[ $i ] ) ? 'display: none;' : ''; ?>">
										<span class="meliconnect-file-icon">
											<i class="fas fa-upload"></i>
										</span>
										<span class="meliconnect-file-label">
											<?php esc_html_e( 'Upload Image…', 'meliconnect' ); ?>
										</span>
									</span>
									<!-- Image element where the selected image will be displayed -->
									<img src="<?php echo isset( $extra_images[ $i ] ) ? esc_url( wp_get_attachment_url( $extra_images[ $i ] ) ) : ''; ?>" class="meliconnect-image-preview" style="<?php echo isset( $extra_images[ $i ] ) ? '' : 'display: none;'; ?>">
									<span class="meliconnect-file-name image-name">
										<?php echo isset( $extra_images[ $i ] ) ? esc_html( get_the_title( $extra_images[ $i ] ) ) : esc_html__( 'No file selected', 'meliconnect' ); ?>
									</span>

								</label>
							</div>
							<input type="hidden" name="meliconnect_general_image_attachment_ids[<?php echo esc_attr( $i ); ?>]" class="image_attachment_id" value="<?php echo esc_attr( isset( $extra_images[ $i ] ) ? $extra_images[ $i ] : '' ); ?>">

						</div>
					</div>
				<?php endfor; ?>

			</div>
			<div class="meliconnect-columns">
				<div class="meliconnect-column meliconnect-is-12">
					<div class="meliconnect-field">
						<label class="meliconnect-label"><?php esc_html_e( 'Description Template', 'meliconnect' ); ?></label>

						<div class="meliconnect-control">
							<textarea class="meliconnect-textarea" name="meliconnect_general_description_template" id="meliconnect_general_description_template" placeholder="<?php esc_html_e( 'Description Template', 'meliconnect' ); ?>"><?php echo isset( $general_data['meliconnect_general_description_template'] ) ? esc_textarea( $general_data['meliconnect_general_description_template'] ) : ''; ?></textarea>
						</div>
						<div class="meliconnect-buttons meliconnect-tags meliconnect-mt-4">
							<a class="meliconnect-button meliconnect-is-small insert-tag" data-tag="[title]"><?php esc_html_e( 'Title', 'meliconnect' ); ?></a>
							<a class="meliconnect-button meliconnect-is-small insert-tag" data-tag="[description]"><?php esc_html_e( 'Product Description', 'meliconnect' ); ?></a>
							<a class="meliconnect-button meliconnect-is-small insert-tag" data-tag="[excerpt]"><?php esc_html_e( 'Short Description', 'meliconnect' ); ?></a>
							<a class="meliconnect-button meliconnect-is-small insert-tag" data-tag="[variations]"><?php esc_html_e( 'Variations', 'meliconnect' ); ?></a>
						</div>
					</div>

				</div>
			</div>
		</div>
	</section>

	<hr>
	
	<section class="meliconnect-section">
		<div class="meliconnect-container">
			<?php if ( !empty($sellers_with_free_plan) ) : ?>
                <div class="meliconnect-notification meliconnect-is-warning">
                    <?php
                    printf(
                        'The following free plan users will not be able to perform automatic synchronizations: <strong>%s</strong>.',
                        implode( ', ', $sellers_with_free_plan )
                    );
                    ?>
                </div>
            <?php endif; ?>
			<div class="meliconnect-columns meliconnect-is-mobile meliconnect-is-multiline">
				<div class="meliconnect-column">
					<h2 class="meliconnect-title meliconnect-is-6"><?php esc_html_e( 'Automatic Import or Export', 'meliconnect' ); ?></h2>
					<p><?php esc_html_e( 'This will be used to execute background processes for either export or import.', 'meliconnect' ); ?></p>
				</div>
			</div>

			<div class="meliconnect-columns meliconnect-is-mobile meliconnect-is-multiline">
				<div class="meliconnect-column meliconnect-is-4">
					<div class="meliconnect-field">
						<label class="meliconnect-label" for="meliconnect_general_sync_type"><?php esc_html_e( 'Apply on', 'meliconnect' ); ?></label>
						<div class="meliconnect-control">
							<div class="meliconnect-select meliconnect-is-fullwidth">
								<select name="meliconnect_general_sync_type" id="meliconnect_general_sync_type">
									<option value="deactive" <?php selected( $general_data['meliconnect_general_sync_type'], 'deactive' ); ?>><?php esc_html_e( 'Deactivate', 'meliconnect' ); ?></option>
									<option value="import" <?php selected( $general_data['meliconnect_general_sync_type'], 'import' ); ?>><?php esc_html_e( 'Import (From Meli to Woo)', 'meliconnect' ); ?></option>
									<option value="export" <?php selected( $general_data['meliconnect_general_sync_type'], 'export' ); ?>><?php esc_html_e( 'Export (From Woo to Meli)', 'meliconnect' ); ?></option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="meliconnect-column meliconnect-is-8">
					<div class="meliconnect-columns meliconnect-meliconnect-is-mobile">
						<div class="meliconnect-column meliconnect-is-5">
							<div class="meliconnect-field">
								<label class="meliconnect-label" for="meliconnect_general_sync_items_batch"><?php esc_html_e( 'Items per batch', 'meliconnect' ); ?></label>
								<div class="meliconnect-control">
									<input class="meliconnect-input" type="number" min="1" name="meliconnect_general_sync_items_batch" id="meliconnect_general_sync_items_batch" value="<?php echo isset( $general_data['meliconnect_general_sync_items_batch'] ) ? esc_attr( $general_data['meliconnect_general_sync_items_batch'] ) : ''; ?>" min="1" max="1000">
								</div>
							</div>
						</div>
						<div class="meliconnect-column meliconnect-is-4">
							<div class="meliconnect-field">
								<label class="meliconnect-label" for="meliconnect_general_sync_frecuency_minutes"><?php esc_html_e( 'Frequency (minutes)', 'meliconnect' ); ?></label>
								<div class="meliconnect-control">
									<input class="meliconnect-input" type="number" min="1" name="meliconnect_general_sync_frecuency_minutes" id="meliconnect_general_sync_frecuency_minutes" value="<?php echo isset( $general_data['meliconnect_general_sync_frecuency_minutes'] ) ? esc_attr( $general_data['meliconnect_general_sync_frecuency_minutes'] ) : ''; ?>">
								</div>
							</div>
						</div>
						<div class="meliconnect-column meliconnect-is-3">
							<div class="meliconnect-field">
								<label class="meliconnect-label" for="meliconnect_general_sync_method"><?php esc_html_e( 'Method', 'meliconnect' ); ?></label>
								<div class="meliconnect-control">
									<div class="meliconnect-select meliconnect-is-fullwidth">
										<select name="meliconnect_general_sync_method" id="meliconnect_general_sync_method">
											<option value="wordpress" <?php selected( $general_data['meliconnect_general_sync_method'], 'WordPress' ); ?>><?php esc_html_e( 'WordPress', 'meliconnect' ); ?></option>
											<option value="custom" <?php selected( $general_data['meliconnect_general_sync_method'], 'custom' ); ?>><?php esc_html_e( 'Custom', 'meliconnect' ); ?></option>
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
						// Detectar HTTPS correctamente
						$https  = isset( $_SERVER['HTTPS'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTPS'] ) ) : '';
						$scheme = ( ! empty( $https ) && strtolower( $https ) !== 'off' ) ? 'https' : 'http';

						// Obtener el nombre del host (sanitizado)
						$host = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';

						// Construir la URL base
						$sync_url = $scheme . '://' . $host;
						?>
						<strong><?php esc_html_e( 'External automatic synchronization URL (custom):', 'meliconnect' ); ?></strong>
						<code><?php echo esc_url( $sync_url ); ?>/wp-json/meliconnect/v1/cronexternal/export-import</code>
					</div>
				</div>
				<div class="meliconnect-column meliconnect-is-3">
					<div class="meliconnect-field meliconnect-is-grouped meliconnect-is-grouped-right">
					</div>
				</div>
			</div>
		</div>
	</section>

	<hr>

	<div class="meliconnect-container">
		<div class="meliconnect-columns">
			<div class="meliconnect-column">
				<div class="meliconnect-level">
					<div class="meliconnect-level-left">
					</div>
					<div class="meliconnect-level-right">
						<button type="submit" class="meliconnect-button  meliconnect-is-primary"><?php esc_html_e( 'Save General Settings', 'meliconnect' ); ?></button>
					</div>
				</div>
			</div>
		</div>
	</div>

</form>
