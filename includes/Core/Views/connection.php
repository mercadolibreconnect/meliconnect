<!-- START MCSYNCAPP -->
<div id="melicon-page-core-connection" class="melicon-app">
    <?php

    use Meliconnect\Meliconnect\Core\Controllers\ConnectionController;

    // Crear una instancia del controlador y obtener los datos
    $connectionController = new ConnectionController();
    $data = $connectionController->getData();
    $headerTitle = esc_html__('Connection', 'meliconnect');

    include MC_PLUGIN_ROOT . 'includes/Core/Views/Partials/header.php';
    ?>

    <div class="melicon-main">
        <div class="melicon-container">

            <!-- Introducción -->
            <div class="melicon-postbox melicon-intro melicon-level">
                <div class="melicon-level-left">
                    <p><?php esc_html_e('Accounts connected to Mercadolibre', 'meliconnect'); ?></p>
                    <p class="melicon-has-background-warning melicon-p-2 melicon-ml-3">
                        <strong><?php esc_html_e('DOMAIN', 'meliconnect'); ?>:</strong> <?php echo esc_html($data['domain']); ?>
                    </p>
                </div>
                <div class="melicon-level-right">
                    <a href="https://meliconnect.com/?domain=<?php echo esc_attr($data['domain']); ?>" target="_blank" class="melicon-button melicon-is-success  float-end">
                        + <?php esc_html_e('ADD USER', 'meliconnect'); ?>
                    </a>

                </div>
            </div>

            <!-- Contenedor principal de conexión -->
            <div id="melicon-connection-container" class=" melicon-connection-container melicon-overflow-x">
                <div id="sync-hub-results">

                    <?php if (!empty($data['users'])) : ?>
                        <div class="melicon-columns melicon-is-multiline">
                            <?php foreach ($data['users'] as $key => $user) : ?>
                                <?php $meli_user_data = maybe_unserialize($user->meli_user_data); ?>

                                <!-- Tarjeta de usuario -->
                                <div class="melicon-card melicon-column melicon-is-4">
                                    <div class="melicon-card-content">
                                        <div class="melicon-content">
                                            <p><strong><?php esc_html_e('User:', 'meliconnect'); ?></strong> 
                                                <a href="<?php echo esc_url($user->permalink); ?>" target="_blank">
                                                    <?php echo esc_html($user->nickname); ?>
                                                </a>
                                            </p>
                                            <p><strong><?php esc_html_e('User ID:', 'meliconnect'); ?></strong> <?php echo esc_html($user->user_id); ?></p>
                                            <p style="display:none"><strong><?php esc_html_e('Access Token:', 'meliconnect'); ?></strong> <?php echo esc_html($user->access_token); ?></p>

                                            <?php if (isset($meli_user_data['body']) && !isset($meli_user_data['body']->message)) : ?>
                                                <?php $body = $meli_user_data['body']; ?>
                                                <p><strong><?php esc_html_e('Email:', 'meliconnect'); ?></strong> <?php echo esc_html($body->email ?? ''); ?></p>
                                                <p><strong><?php esc_html_e('Site ID:', 'meliconnect'); ?></strong> <?php echo esc_html(strtoupper($user->site_id ?? '')); ?></p>
                                                <p>
                                                    <strong><?php esc_html_e('Connection Token:', 'meliconnect'); ?></strong> 
                                                    <?php 
                                                        $truncated_token = !empty($user->api_token) ? substr($user->api_token, 0, 6) . '...' : ''; 
                                                        echo esc_html($truncated_token); 
                                                    ?>
                                                </p>
                                                <p><strong><?php esc_html_e('Country:', 'meliconnect'); ?></strong> <?php echo esc_html($user->country ?? ''); ?></p>
                                                <p><strong><?php esc_html_e('Seller Experience:', 'meliconnect'); ?></strong> <?php echo esc_html($body->seller_experience ?? ''); ?></p>
                                                <p><strong><?php esc_html_e('Registration Level:', 'meliconnect'); ?></strong> <?php echo esc_html($body->context->registration_level ?? ''); ?></p>
                                                <p>
                                                    <strong><?php esc_html_e('Registration Date:', 'meliconnect'); ?></strong> 
                                                    <?php 
                                                        if (!empty($body->registration_date)) {
                                                            $timestamp = strtotime($body->registration_date);
                                                            if ($timestamp) {
                                                                echo esc_html(wp_date("d/m/Y", $timestamp));
                                                            } else {
                                                                esc_html_e('Invalid date', 'meliconnect');
                                                            }
                                                        } else {
                                                            esc_html_e('Not available', 'meliconnect');
                                                        }
                                                    ?>
                                                </p>
                                                <p><strong><?php esc_html_e('Tags:', 'meliconnect'); ?></strong> <?php echo esc_html(implode(', ', $body->tags ?? [])); ?></p>
                                                <p class="<?php echo in_array('mshops', $body->tags ?? []) ? 'melicon-has-text-success' : 'melicon-has-text-danger'; ?>">
                                                    <?php echo esc_html(in_array('mshops', $body->tags ?? []) ? esc_html__('Has MercadoShops', 'meliconnect') : esc_html__('Does Not Have MercadoShops', 'meliconnect')); ?>
                                                </p>
                                            <?php else : ?>
                                                <p class="melicon-has-text-danger">
                                                    <b><?php esc_html_e('Could Not Find User Data on MercadoLibre', 'meliconnect'); ?></b>
                                                </p>
                                                <?php if (isset($meli_user_data['body']->message) && $meli_user_data['body']->message === 'invalid_token') : ?>
                                                    <p class="melicon-has-text-danger">
                                                        <?php esc_html_e('The token has expired. You need to reauthorize the domain in the', 'meliconnect'); ?>
                                                        <a href="https://www.meliconnect.com" target="_blank"><?php esc_html_e('MeliConnect App', 'meliconnect'); ?></a>.
                                                    </p>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <p class="has-text-danger"><?php esc_html_e('No users connected.', 'meliconnect'); ?></p>
                    <?php endif; ?>

                </div>
            </div>

        </div>
    </div>

    <?php include MC_PLUGIN_ROOT . 'includes/Core/Views/Partials/footer.php'; ?>
</div>
<!-- END MCSYNCAPP -->
