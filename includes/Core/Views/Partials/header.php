<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
use Meliconnect\Meliconnect\Core\Models\Notification;

Notification::init();

$notifications = Notification::getNotifications();


?>


<div class="meliconnect-admin-page">
    <div class="meliconnect-app">
        <div class="meliconnect-header meliconnect-mb-5">
            <div class="meliconnect-message-bar">
                <div class="upgrade-text">
                    <div>
                        <?php
                        $text = esc_html__('Check the available <strong>documentation</strong>. You can quickly clear up any questions', 'meliconnect');
                        echo wp_kses($text, array('strong' => array()));
                        ?>
                        <a href="https://mercadolibre.meliconnect.com/" target="_blank" class="text-white">
                            <?php esc_html_e('Check now', 'meliconnect'); ?>
                        </a>
                    </div>
                </div>
                <i class="fas fa-times meliconseo-close"></i> <!-- Font Awesome icon -->
            </div>
            <!---->
            <!---->
            <div class="meliconnect-container header-container ">
                <div class="meliconnect-header-content">
                    <a href="https://meliconnect.com" target="_blank">
                        <img src="<?php echo esc_html(MELICONNECT_PLUGIN_URL); ?>/includes/Core/Assets/Images/logo-mercadolibre-connect.png" alt="Meliconnect" class="meliconnect-logo" style="height: 40px; margin-top: 8px;">
                    </a>
                    <span class="spacer"></span>
                    <span class="page-name"><?php echo esc_html($headerTitle ?? ''); ?></span>


                    <div class="header-actions">
                        <a class="meliconnect-sidebar-toggle">
                            <span class="round">
                                <?php
                                if (count($notifications) > 0) {
                                    echo '<span id="meliconnect-total-notifications" class="round number ">' . count($notifications) . '</span>';
                                }
                                ?>
                                <i class="fas fa-bell meliconnect-notifications"></i> <!-- Reemplazo de notificaciones -->
                            </span>
                        </a>
                        <a href="https://mercadolibre.meliconnect.com/" target="_blank">
                            <span class="round">
                                <i class="fas fa-question-circle meliconnect-circle-question-mark"></i> <!-- Reemplazo del signo de interrogación -->
                            </span>
                        </a>
                    </div>
                </div>
            </div>



        </div>

        <div id="meliconnect-sidebar" class="meliconnect-sidebar">
            <div id="meliconnect-sidebar-inner" class="meliconnect-sidebar-inner">
                <div class="meliconnect-sidebar-inner-header">
                    <div class="meliconnect-columns">
                        <div class="meliconnect-column meliconnect-is-10">
                            <span class='meliconnect-sidebar-inner-header-title'><?php esc_html_e('New notifications', 'meliconnect'); ?></span>
                        </div>
                        <div class="meliconnect-column meliconnect-is-2 has-text-right">
                            <a class='meliconnect-sidebar-inner-header-cancel' onclick="toggleSidebar()">
                                <i class="fa fa-times" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="meliconnect-sidebar-inner-content">
                    <?php

                    if (count($notifications) > 0) {
                        foreach ($notifications as $notification) {
                    ?>
                            <!-- ITEM STARTS -->
                            <div class="meliconnect-sidebar-inner-content-item meliconnect-columns" data-notificationId="<?php echo esc_attr($notification->unique_id) ?>">
                                <div class="meliconnect-sidebar-inner-content-item-icon meliconnect-column meliconnect-is-1">
                                    <?php echo isset($notification->icon_html) && !empty($notification->icon_html) ? wp_kses_post($notification->icon_html) : ''; ?>
                                </div>
                                <div class="meliconnect-sidebar-inner-content-item-body meliconnect-column meliconnect-is-11">
                                    <div class="meliconnect-sidebar-inner-content-item-body-header meliconnect-columns">
                                        <div class="meliconnect-sidebar-inner-content-item-body-header-title meliconnect-column meliconnect-is-8">
                                            <?php echo wp_kses_post($notification->title_html); ?>
                                        </div>
                                        <div class="meliconnect-sidebar-inner-content-item-body-header-date meliconnect-column meliconnect-is-4 has-text-right">
                                            <?php
                                            $current_timestamp = time();
                                            $date = new DateTime($notification->date_from);
                                            $diff = $date->diff(new DateTime("@$current_timestamp"));

                                            if ($diff->days > 0) {
                                                $diff_time = "hace {$diff->days} día/s";
                                            } elseif ($diff->h > 0) {
                                                $diff_time = "hace {$diff->h} hora/s";
                                            } else {
                                                $diff_time = "hace $diff->i minuto/s";
                                            }

                                            echo esc_html($diff_time);
                                            ?>
                                        </div>
                                    </div>
                                    <div class="meliconnect-sidebar-inner-content-item-body-text meliconnect-columns">
                                        <div class="meliconnect-column meliconnect-is-12">
                                            <?php echo wp_kses_post($notification->text_html); ?>
                                        </div>
                                    </div>
                                    <div class="meliconnect-sidebar-inner-content-item-body-actions meliconnect-columns">
                                        <?php if (isset($notification->bt1_text) && !empty($notification->bt1_text)) {  ?>
                                            <div class="meliconnect-sidebar-inner-content-item-body-actions-btn">
                                                <a class="meliconnect-button  meliconnect-is-success meliconnect-is-small" data-notificationId="<?php echo esc_attr($notification->unique_id) ?>" href="<?php echo esc_url($notification->bt1_link) ?>" target="_blank">
                                                    <?php echo esc_html($notification->bt1_text) ?>
                                                </a>
                                            </div>
                                        <?php }  ?>
                                        <?php if (isset($notification->bt2_text) && !empty($notification->bt2_text)) {  ?>
                                            <div class="meliconnect-sidebar-inner-content-item-body-actions-btn">
                                                <a class="meliconnect-button  meliconnect-is-small" data-notificationId="<?php echo esc_attr($notification->unique_id); ?>" href="<?php echo esc_url($notification->bt2_link); ?>" target="_blank">
                                                    <?php echo esc_html($notification->bt2_text) ?>
                                                </a>
                                            </div>
                                        <?php }  ?>
                                        <div class="meliconnect-sidebar-inner-content-item-body-actions-link has-text-right">
                                            <a href="#" class="meliconnect-sidebar-inner-content-item-body-actions-dismiss" onclick="dismissMessage(event, '<?php echo esc_js($notification->unique_id); ?>')"><?php esc_html_e('Dismiss', 'meliconnect'); ?></a>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- ITEM ENDS -->
                        <?php

                        }
                    } else {
                        ?>
                        <div class="meliconnect-sidebar-inner-content-item meliconnect-columns">
                            <div class="meliconnect-sidebar-inner-content-item-body meliconnect-column meliconnect-is-12">
                                <div class="meliconnect-sidebar-inner-content-item-body-no-text meliconnect-columns">
                                    <div class="meliconnect-column meliconnect-is-12">
                                        <p><?php esc_html_e('There are no new notifications', 'meliconnect'); ?></p>

                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php
                    }
                    ?>

                </div>
                <div class="meliconnect-sidebar-inner-footer">
                    <div class="meliconnect-columns">
                        <div class="meliconnect-column meliconnect-is-12 has-text-right">

                            <?php
                            if (count($notifications) > 0) {
                            ?>
                                <a onclick="dismissMessage(event, 'all')" class='meliconnect-sidebar-inner-footer-dismissAll'><?php esc_html_e('Dismiss All', 'meliconnect'); ?></a>
                            <?php
                            }
                            ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="meliconnect-sidebar-overlay"> </div>
    </div>
</div>


