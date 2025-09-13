<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
use Meliconnect\Meliconnect\Core\Models\UserConnection;

// Obtén la lista de vendedores
$sellers = UserConnection::getUser();

// Definir las variables necesarias para la vista
$selectName = isset($selectName) ? $selectName : 'seller_filter'; // Puedes personalizarlo si es necesario
$selected_seller = isset($selected_seller) ? $selected_seller : null;
$sellerSelectAddAll = isset($sellerSelectAddAll) ? $sellerSelectAddAll : false;

?>
<!-- Vista HTML del select -->
<?php if (empty($sellers)): ?>
    <p><?php echo esc_html__('Please connect a user to your account.', 'meliconnect'); ?></p>
<?php elseif (count($sellers) === 1): ?>
    <?php 
        $seller = $sellers[0];
        echo '<p>' . esc_html($seller->nickname) . '</p>';
        echo '<input type="hidden" name="' . esc_attr($selectName) . '" value="' . esc_attr($seller->user_id) . '">';
    ?>
<?php else: ?>
    <div class="meliconnect-control">
        <div class="meliconnect-select">
            <select name="<?php echo esc_attr($selectName); ?>">
                <?php if ($sellerSelectAddAll): ?>
                    <option value="all" <?php selected($selected_seller, 'all'); ?>>
                        <?php echo esc_html__('All Sellers', 'meliconnect'); ?>
                    </option>
                <?php endif; ?>
                <?php foreach ($sellers as $seller): ?>
                    <option value="<?php echo esc_attr($seller->user_id); ?>" <?php selected($selected_seller, $seller->user_id); ?>>
                        <?php echo esc_html($seller->nickname); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
<?php endif; ?>
