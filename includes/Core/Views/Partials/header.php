<?php

use StoreSync\Meliconnect\Core\Models\Notification;

Notification::init();

$notifications = Notification::getNotifications();


?>

<script>
    var ajaxurl = "<?= admin_url('admin-ajax.php'); ?>";
</script>

<div class="meliconnect-admin-page">
    <div class="melicon-app">
        <div class="melicon-header">
            <div class="melicon-message-bar">
                <div class="upgrade-text">

                    <div>
                        <?php
                        $text = __('Check the available <strong>documentation</strong>. You can quickly clear up any questions', 'meliconnect');
                        echo wp_kses($text, array('strong' => array()));
                        ?>
                        <a href="https://documentacion.meliconnect.com/" target="_blank" class="text-white">
                            <?php echo __('Check now', 'meliconnect'); ?>
                        </a>
                    </div>

                </div>
                <svg viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" class="meliconseo-close">
                    <path d="M11.8211 1.3415L10.6451 0.166504L5.98305 4.82484L1.32097 0.166504L0.14502 1.3415L4.80711 5.99984L0.14502 10.6582L1.32097 11.8332L5.98305 7.17484L10.6451 11.8332L11.8211 10.6582L7.159 5.99984L11.8211 1.3415Z" fill="currentColor"></path>
                </svg>
            </div>
            <!---->
            <!---->
            <div class="melicon-container header-container">
                <div class="melicon-header-content">
                    <a href="https://meliconnect.com.com/mi-cuenta/?utm_source=WordPress&amp;utm_campaign=plugin&amp;utm_medium=header-logo" target="_blank">
                        <svg version="1.0" xmlns="http://www.w3.org/2000/svg" width="121.000000pt" height="60.000000pt" class="melicon-logo" viewBox="0 0 221.000000 60.000000" preserveAspectRatio="xMidYMid meet">
                            <g transform="translate(0.000000,60.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none">
                                <path d="M25 575 c-25 -24 -25 -25 -25 -224 0 -197 0 -200 24 -228 l24 -28
                        224 -5 223 -5 76 -42 c42 -24 81 -43 87 -43 7 0 4 17 -8 44 l-19 45 164 3
                        c225 4 210 -15 210 259 0 209 0 211 -24 230 -22 18 -45 19 -478 19 l-454 0
                        -24 -25z m586 -118 c83 -56 52 -209 -48 -236 -50 -14 -89 -3 -119 32 -23 27
                        -26 38 -22 89 5 64 29 100 83 124 43 19 68 17 106 -9z m276 -2 c33 -22 49 -85
                        34 -136 -16 -52 -47 -84 -98 -99 -76 -23 -139 29 -137 115 2 109 115 176 201
                        120z m-752 -17 c3 -18 9 -58 13 -88 l7 -55 34 88 c28 73 37 87 56 87 20 0 23
                        -8 36 -82 l14 -83 31 80 c27 69 35 80 58 83 14 2 26 0 26 -5 0 -4 -22 -60 -48
                        -123 -42 -100 -51 -115 -73 -118 -25 -3 -26 0 -39 85 l-13 88 -22 -52 c-13
                        -28 -29 -67 -36 -87 -11 -31 -18 -36 -38 -34 -24 3 -27 9 -43 103 -23 139 -23
                        145 7 145 19 0 25 -6 30 -32z" fill="#00779F"></path>
                                <path d="M491 404 c-27 -35 -29 -104 -3 -127 63 -57 156 54 108 128 -23 35
                        -77 35 -105 -1z" fill="#00779F"></path>
                                <path d="M761 404 c-26 -33 -29 -105 -5 -129 24 -24 73 -15 97 17 23 30 27 99
                        7 123 -21 25 -75 19 -99 -11z" fill="#00779F"></path>
                                <path d="M1135 455 c-37 -36 -34 -86 6 -119 17 -14 36 -26 42 -26 7 0 29 -5
                        50 -11 31 -8 38 -14 35 -32 -4 -30 -61 -35 -106 -11 -29 15 -35 16 -43 3 -30
                        -47 2 -69 104 -69 84 0 104 13 113 71 8 50 -21 82 -95 103 -66 18 -79 29 -61
                        51 16 19 44 19 86 -1 33 -16 36 -16 49 2 8 11 15 22 15 26 0 17 -63 38 -115
                        38 -46 0 -60 -4 -80 -25z" fill="#284256"></path>
                                <path d="M1340 475 c0 -3 25 -42 55 -88 51 -76 55 -86 55 -139 0 -58 0 -58 30
                        -58 30 0 30 0 30 58 0 53 4 63 55 139 30 46 55 85 55 88 0 3 -15 5 -34 5 -32
                        0 -38 -5 -68 -55 -18 -30 -35 -55 -38 -55 -3 0 -21 25 -40 55 -31 48 -39 55
                        -67 55 -18 0 -33 -2 -33 -5z" fill="#284256"></path>
                                <path d="M1640 335 l0 -145 30 0 29 0 3 92 3 91 67 -91 c59 -81 71 -92 98 -92
                        l30 0 0 145 0 145 -30 0 -30 0 0 -92 0 -92 -67 92 c-61 82 -71 91 -100 92
                        l-33 0 0 -145z" fill="#284256"></path>
                                <path d="M2005 460 c-83 -54 -88 -187 -9 -249 21 -17 41 -21 96 -21 61 0 72 3
                        95 26 l26 26 -21 20 -20 20 -21 -21 c-30 -30 -90 -29 -120 3 -77 82 21 213
                        110 147 34 -25 71 -15 62 17 -13 50 -139 71 -198 32z" fill="#284256"></path>
                            </g>
                        </svg>
                    </a>
                    <span class="spacer"></span>
                    <span class="page-name"><?= $headerTitle ?? ''; ?></span>

                    <div class="header-actions">
                        <a onclick="toggleSidebar()">
                            <span class="round">
                                <?php
                                if (count($notifications) > 0) {
                                    echo '<span id="melicon-total-notifications" class="round number ">' . count($notifications) . '</span>';
                                }
                                ?>
                                <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="melicon-notifications">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M15.8333 2.5H4.16667C3.25 2.5 2.5 3.25 2.5 4.16667V15.8333C2.5 16.75 3.24167 17.5 4.16667 17.5H15.8333C16.75 17.5 17.5 16.75 17.5 15.8333V4.16667C17.5 3.25 16.75 2.5 15.8333 2.5ZM15.8333 15.8333H4.16667V13.3333H7.13333C7.70833 14.325 8.775 15 10.0083 15C11.2417 15 12.3 14.325 12.8833 13.3333H15.8333V15.8333ZM11.675 11.6667H15.8333V4.16667H4.16667V11.6667H8.34167C8.34167 12.5833 9.09167 13.3333 10.0083 13.3333C10.925 13.3333 11.675 12.5833 11.675 11.6667Z" fill="currentColor"></path>
                                </svg>
                            </span>
                        </a>
                        <a href="https://documentacion.meliconnect.com/" target="_blank">
                            <span class="round">
                                <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="melicon-circle-question-mark">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M1.6665 10.0001C1.6665 5.40008 5.39984 1.66675 9.99984 1.66675C14.5998 1.66675 18.3332 5.40008 18.3332 10.0001C18.3332 14.6001 14.5998 18.3334 9.99984 18.3334C5.39984 18.3334 1.6665 14.6001 1.6665 10.0001ZM10.8332 13.3334V15.0001H9.1665V13.3334H10.8332ZM9.99984 16.6667C6.32484 16.6667 3.33317 13.6751 3.33317 10.0001C3.33317 6.32508 6.32484 3.33341 9.99984 3.33341C13.6748 3.33341 16.6665 6.32508 16.6665 10.0001C16.6665 13.6751 13.6748 16.6667 9.99984 16.6667ZM6.6665 8.33341C6.6665 6.49175 8.15817 5.00008 9.99984 5.00008C11.8415 5.00008 13.3332 6.49175 13.3332 8.33341C13.3332 9.40251 12.6748 9.97785 12.0338 10.538C11.4257 11.0695 10.8332 11.5873 10.8332 12.5001H9.1665C9.1665 10.9824 9.9516 10.3806 10.6419 9.85148C11.1834 9.43642 11.6665 9.06609 11.6665 8.33341C11.6665 7.41675 10.9165 6.66675 9.99984 6.66675C9.08317 6.66675 8.33317 7.41675 8.33317 8.33341H6.6665Z" fill="currentColor"></path>
                                </svg>
                            </span>
                        </a>

                    </div>
                </div>
            </div>


        </div>

        <div id="melicon-sidebar" class="melicon-sidebar">
            <div id="melicon-sidebar-inner" class="melicon-sidebar-inner">
                <div class="melicon-sidebar-inner-header">
                    <div class="columns">
                        <div class="column is-10">
                            <span class='melicon-sidebar-inner-header-title'><?php echo __('New notifications', 'meliconnect'); ?></span>
                        </div>
                        <div class="column is-2 has-text-right">
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
                            <div class="melicon-sidebar-inner-content-item columns" data-notificationId="<?php echo ($notification->unique_id) ?>">
                                <div class="melicon-sidebar-inner-content-item-icon column is-1">
                                    <?php echo ((isset($notification->icon_html) && !empty($notification->icon_html)) ? $notification->icon_html : '');  ?>
                                </div>
                                <div class="melicon-sidebar-inner-content-item-body column is-11">
                                    <div class="melicon-sidebar-inner-content-item-body-header columns">
                                        <div class="melicon-sidebar-inner-content-item-body-header-title column is-8"><?php echo ($notification->title_html) ?></div>
                                        <div class="melicon-sidebar-inner-content-item-body-header-date column is-4 has-text-right">
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

                                            echo ($diff_time);
                                            ?>
                                        </div>
                                    </div>
                                    <div class="melicon-sidebar-inner-content-item-body-text columns">
                                        <div class="column is-12">
                                            <?php echo ($notification->text_html) ?>
                                        </div>
                                    </div>
                                    <div class="melicon-sidebar-inner-content-item-body-actions columns">
                                        <?php if (isset($notification->bt1_text) && !empty($notification->bt1_text)) {  ?>
                                            <div class="melicon-sidebar-inner-content-item-body-actions-btn">
                                                <a class="button button-meliconnect is-success is-small" data-notificationId="<?php echo ($notification->unique_id) ?>" href="<?php echo ($notification->bt1_link) ?>" target="_blank">
                                                    <?php echo ($notification->bt1_text) ?>
                                                </a>
                                            </div>
                                        <?php }  ?>
                                        <?php if (isset($notification->bt2_text) && !empty($notification->bt2_text)) {  ?>
                                            <div class="melicon-sidebar-inner-content-item-body-actions-btn">
                                                <a class="button button-meliconnect is-small" data-notificationId="<?php echo ($notification->unique_id) ?>" href="<?php echo ($notification->bt2_link) ?>" target="_blank">
                                                    <?php echo ($notification->bt2_text) ?>
                                                </a>
                                            </div>
                                        <?php }  ?>
                                        <div class="melicon-sidebar-inner-content-item-body-actions-link has-text-right">
                                            <a href="#" class="melicon-sidebar-inner-content-item-body-actions-dismiss" onclick="dismissMessage(event, '<?php echo ($notification->unique_id) ?>')">Descartar</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- ITEM ENDS -->
                        <?php

                        }
                    } else {
                        ?>
                        <div class="melicon-sidebar-inner-content-item columns">
                            <div class="melicon-sidebar-inner-content-item-body column is-12">
                                <div class="melicon-sidebar-inner-content-item-body-no-text columns">
                                    <div class="column is-12">
                                        <p><?php echo __('There are no new notifications', 'meliconnect'); ?></p>

                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php
                    }
                    ?>

                </div>
                <div class="melicon-sidebar-inner-footer">
                    <div class="columns">
                        <div class="column is-12 has-text-right">

                            <?php
                            if (count($notifications) > 0) {
                            ?>
                                <a onclick="dismissMessage(event, 'all')" class='melicon-sidebar-inner-footer-dismissAll'><?php echo __('Dismiss All', 'meliconnect'); ?></a>
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


<script>
    function toggleSidebar() {
        console.log('toggleSidebar');

        var sidebar = document.getElementById('melicon-sidebar');
        var sidebarOverlay = document.getElementById('melicon-sidebar-overlay');

        sidebar.classList.toggle('open');
        sidebarOverlay.classList.toggle('open');
    }

    function dismissMessage(event, notificationId) {
        event.preventDefault();

        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'meliconnect_dismiss_message',
                notificationId: notificationId // ID de notificación a eliminar, all en caso de que sean todas
            },
            success: function(response) {
                console.log(response);

                if (response.status) {
                    if (response.notification_id == 'all') {
                        jQuery('.melicon-sidebar-inner-content-item').remove();
                        jQuery('#melicon-total-notifications').hide();
                    } else {
                        jQuery('.melicon-sidebar-inner-content-item[data-notificationId="' + response.notification_id + '"]').remove();
                        //Se descuenta uno al total de notificaciones
                        jQuery('#melicon-total-notifications').text(parseInt(jQuery('#melicon-total-notifications').text()) - 1);
                    }

                }
            },
            error: function(xhr, status, error) {
                console.log(error);
            }
        });
    }
</script>