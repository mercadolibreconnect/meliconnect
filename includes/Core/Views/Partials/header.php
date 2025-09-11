<?php

use Meliconnect\Meliconnect\Core\Models\Notification;

Notification::init();

$notifications = Notification::getNotifications();


?>


<div class="meliconnect-admin-page">
    <div class="melicon-app">
        <div class="melicon-header melicon-mb-5">
            <div class="melicon-message-bar">
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
            <div class="melicon-container header-container ">
                <div class="melicon-header-content">
                    <a href="https://meliconnect.com" target="_blank">
                        <img src="<?php echo esc_html(MC_PLUGIN_URL); ?>/includes/Core/Assets/Images/logo-mercadolibre-connect.png" alt="Meliconnect" class="melicon-logo" style="height: 40px; margin-top: 8px;">
                    </a>
                    <span class="spacer"></span>
                    <span class="page-name"><?php echo esc_html($headerTitle ?? ''); ?></span>


                    <div class="header-actions">
                        <a class="melicon-sidebar-toggle">
                            <span class="round">
                                <?php
                                if (count($notifications) > 0) {
                                    echo '<span id="melicon-total-notifications" class="round number ">' . count($notifications) . '</span>';
                                }
                                ?>
                                <i class="fas fa-bell melicon-notifications"></i> <!-- Reemplazo de notificaciones -->
                            </span>
                        </a>
                        <a href="https://mercadolibre.meliconnect.com/" target="_blank">
                            <span class="round">
                                <i class="fas fa-question-circle melicon-circle-question-mark"></i> <!-- Reemplazo del signo de interrogación -->
                            </span>
                        </a>
                    </div>
                </div>
            </div>



        </div>

        <div id="melicon-sidebar" class="melicon-sidebar">
            <div id="melicon-sidebar-inner" class="melicon-sidebar-inner">
                <div class="melicon-sidebar-inner-header">
                    <div class="melicon-columns">
                        <div class="melicon-column melicon-is-10">
                            <span class='melicon-sidebar-inner-header-title'><?php esc_html_e('New notifications', 'meliconnect'); ?></span>
                        </div>
                        <div class="melicon-column melicon-is-2 has-text-right">
                            <a class='melicon-sidebar-inner-header-cancel' onclick="toggleSidebar()">
                                <i class="fa fa-times" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="melicon-sidebar-inner-content">
                    <?php

                    if (count($notifications) > 0) {
                        foreach ($notifications as $notification) {
                    ?>
                            <!-- ITEM STARTS -->
                            <div class="melicon-sidebar-inner-content-item melicon-columns" data-notificationId="<?php echo esc_attr($notification->unique_id) ?>">
                                <div class="melicon-sidebar-inner-content-item-icon melicon-column melicon-is-1">
                                    <?php echo isset($notification->icon_html) && !empty($notification->icon_html) ? wp_kses_post($notification->icon_html) : ''; ?>
                                </div>
                                <div class="melicon-sidebar-inner-content-item-body melicon-column melicon-is-11">
                                    <div class="melicon-sidebar-inner-content-item-body-header melicon-columns">
                                        <div class="melicon-sidebar-inner-content-item-body-header-title melicon-column melicon-is-8">
                                            <?php echo wp_kses_post($notification->title_html); ?>
                                        </div>
                                        <div class="melicon-sidebar-inner-content-item-body-header-date melicon-column melicon-is-4 has-text-right">
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
                                    <div class="melicon-sidebar-inner-content-item-body-text melicon-columns">
                                        <div class="melicon-column melicon-is-12">
                                            <?php echo wp_kses_post($notification->text_html); ?>
                                        </div>
                                    </div>
                                    <div class="melicon-sidebar-inner-content-item-body-actions melicon-columns">
                                        <?php if (isset($notification->bt1_text) && !empty($notification->bt1_text)) {  ?>
                                            <div class="melicon-sidebar-inner-content-item-body-actions-btn">
                                                <a class="melicon-button  melicon-is-success melicon-is-small" data-notificationId="<?php echo esc_attr($notification->unique_id) ?>" href="<?php echo esc_url($notification->bt1_link) ?>" target="_blank">
                                                    <?php echo esc_html($notification->bt1_text) ?>
                                                </a>
                                            </div>
                                        <?php }  ?>
                                        <?php if (isset($notification->bt2_text) && !empty($notification->bt2_text)) {  ?>
                                            <div class="melicon-sidebar-inner-content-item-body-actions-btn">
                                                <a class="melicon-button  melicon-is-small" data-notificationId="<?php echo esc_attr($notification->unique_id); ?>" href="<?php echo esc_url($notification->bt2_link); ?>" target="_blank">
                                                    <?php echo esc_html($notification->bt2_text) ?>
                                                </a>
                                            </div>
                                        <?php }  ?>
                                        <div class="melicon-sidebar-inner-content-item-body-actions-link has-text-right">
                                            <a href="#" class="melicon-sidebar-inner-content-item-body-actions-dismiss" onclick="dismissMessage(event, '<?php echo esc_js($notification->unique_id); ?>')"><?php esc_html_e('Dismiss', 'meliconnect'); ?></a>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- ITEM ENDS -->
                        <?php

                        }
                    } else {
                        ?>
                        <div class="melicon-sidebar-inner-content-item melicon-columns">
                            <div class="melicon-sidebar-inner-content-item-body melicon-column melicon-is-12">
                                <div class="melicon-sidebar-inner-content-item-body-no-text melicon-columns">
                                    <div class="melicon-column melicon-is-12">
                                        <p><?php esc_html_e('There are no new notifications', 'meliconnect'); ?></p>

                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php
                    }
                    ?>

                </div>
                <div class="melicon-sidebar-inner-footer">
                    <div class="melicon-columns">
                        <div class="melicon-column melicon-is-12 has-text-right">

                            <?php
                            if (count($notifications) > 0) {
                            ?>
                                <a onclick="dismissMessage(event, 'all')" class='melicon-sidebar-inner-footer-dismissAll'><?php esc_html_e('Dismiss All', 'meliconnect'); ?></a>
                            <?php
                            }
                            ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="melicon-sidebar-overlay"> </div>
    </div>
</div>


