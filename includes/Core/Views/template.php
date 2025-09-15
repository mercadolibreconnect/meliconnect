<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

use Meliconnect\Meliconnect\Core\Controllers\SettingController;

// Crea una instancia del controlador
$settingController = new SettingController();

// Obtén los datos necesarios
$data = $settingController->getData();

$headerTitle = esc_html__( 'Settings', 'meliconnect' );

require MELICONNECT_PLUGIN_ROOT . 'includes/Core/Views/Partials/header.php';

?>
<!-- START MCSYNCAPP -->
<div id="meliconnect-page-core-settings" class="meliconnect-app">
	<div class="meliconnect-main">
		<div class="meliconnect-container">

			<div class="meliconnect-postbox meliconnect-intro meliconnect-level">
				<div class="meliconnect-level-left">
				</div>
				<div class="meliconnect-level-right">
				</div>
			</div>

			<div id="meliconnect-settings-container" class="meliconnect-container meliconnect-settings-container meliconnect-overflow-x">
				<div id="sync-hub-settings-spinner" class="meliconnect-card">
					<div class="meliconnect-columns">
						<div class="meliconnect-column meliconnect-is-12">
							<p><i class="fa fa-spinner fa-spin" style="font-size:20px;"></i> <?php esc_html_e( 'Loading settings', 'meliconnect' ); ?> ...</p>
						</div>
					</div>

				</div>
				<div id="">
				</div>
			</div>

		</div>
	</div>

	<?php require MELICONNECT_PLUGIN_ROOT . 'includes/Core/Views/Partials/footer.php'; ?>
</div>
<!-- END MCSYNCAPP -->