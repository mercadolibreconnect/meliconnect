<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$meli_attrs                   = $data['meli_attrs'] ?? array();
$pending_required_attrs_names = $data['pending_required_attrs_names'] ?? array();
$instance                     = $data['instance'] ?? null;

?>

<div id="meliconnect-mercadolibre-attributes" class="meliconnect_hide_if_change_category meliconnect-mt-2">
	<?php if ( ! empty( $pending_required_attrs_names ) ) : ?>
		<div class="meliconnect-mercadolibre-attributes-warning" style="background-color: #ffcccc; padding: 10px; border-radius: 5px; font-size: 14px">
			<p>
				<strong><?php esc_html_e( 'Following Attributes are REQUIRED and are missing in your product to update or create in Mercadolibre:', 'meliconnect' ); ?></strong><br>
				<span style="font-size: 16px">
					<?php echo esc_html( implode( ', ', array_map( 'esc_html', $pending_required_attrs_names ) ) ); ?>
				</span>
			</p>
		</div>
	<?php endif; ?>

	<hr>

	<?php if ( ! empty( $meli_attrs ) && is_array( $meli_attrs ) ) : ?>
		<p><strong><?php esc_html_e( 'Mercadolibre Attributes:', 'meliconnect' ); ?></strong></p>
		<p><?php esc_html_e( 'You can create following attributes to update or create in Mercadolibre.', 'meliconnect' ); ?></p>
		<div class="meliconnect-mercadolibre-attributes-table" style="max-height: 500px; overflow-y: auto">
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th scope="col" class="manage-column column-primary" style="font-weight: bold">
							<?php esc_html_e( 'Name', 'meliconnect' ); ?>
						</th>
						<th scope="col" class="manage-column" style="text-align:center; font-weight: bold">
							<?php esc_html_e( 'Possible Values', 'meliconnect' ); ?>
						</th>
						<th scope="col" class="manage-column" style="text-align:center; font-weight: bold">
							<?php esc_html_e( 'Required', 'meliconnect' ); ?>
						</th>
						<th scope="col" class="manage-column" style="text-align:center; font-weight: bold">
							<?php esc_html_e( 'Catalog Required', 'meliconnect' ); ?>
						</th>
						<th scope="col" class="manage-column" style="text-align:center; font-weight: bold">
							<?php esc_html_e( 'Can be used for variations', 'meliconnect' ); ?>
						</th>
					</tr>
				</thead>
				<tbody>


					<?php foreach ( $meli_attrs as $attr ) : ?>
						<tr>
							<td class="meliconnect-column-primary">
								<?php echo esc_html( $attr->name ); ?>
								<?php if ( $instance->attr_is_matched( $attr->name ) ) : ?>
									<i class="fas fa-solid fa-check meliconnect-color-success"></i>
								<?php endif; ?>
							</td>
							<td style="text-align:center; min-width: 100px">


								<p><strong><?php esc_html_e( 'Meli value type: ', 'meliconnect' ); ?></strong>
									<?php echo esc_html( $instance->get_attr_value_type( $attr ) ); ?>
								</p>

								<?php

								$is_required = in_array( $attr->value_type, array( 'list', 'boolean' ) );

								if ( isset( $attr->values ) && is_array( $attr->values ) && ! empty( $attr->values ) ) {

									if ( $is_required ) {
										$label = esc_html__( 'Required values:', 'meliconnect' );
									} else {
										$label = esc_html__( 'Suggested values:', 'meliconnect' );
									}

									?>
									<p><strong><?php echo esc_html( $label ); ?></strong></p>
									<?php

									foreach ( $attr->values as $value ) {
										$value_name = $value->name;
										echo esc_html( $value_name ) . ' | ';
									}
								}
								?>
							</td>
							<td style="text-align:center">
								<?php if ( $instance->isRequiredAttribute( $attr ) ) : ?>
									<span class="meliconnect-tag meliconnect-bg-success"><?php esc_html_e( 'YES', 'meliconnect' ); ?></span>
								<?php else : ?>
									<span class="meliconnect-tag meliconnect-bg-error"><?php esc_html_e( 'NO', 'meliconnect' ); ?></span>
								<?php endif; ?>
							</td>
							<td style="text-align:center">
								<?php if ( ! empty( $attr->tags->catalog_required ) ) : ?>
									<span class="meliconnect-tag meliconnect-bg-success"><?php esc_html_e( 'YES', 'meliconnect' ); ?></span>
								<?php else : ?>
									<span class="meliconnect-tag meliconnect-bg-error"><?php esc_html_e( 'NO', 'meliconnect' ); ?></span>
								<?php endif; ?>
							</td>
							<td style="text-align:center">
								<?php if ( ! empty( $attr->tags->allow_variations ) ) : ?>
									<span class="meliconnect-tag meliconnect-bg-success"><?php esc_html_e( 'YES', 'meliconnect' ); ?></span>
								<?php else : ?>
									<span class="meliconnect-tag meliconnect-bg-error"><?php esc_html_e( 'NO', 'meliconnect' ); ?></span>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
</div>