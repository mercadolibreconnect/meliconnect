<?php
if (!empty($categories)) : ?>
    <option value=""><?php echo esc_html__('Select a category', 'meliconnect'); ?></option>
    <?php foreach ($categories as $category) : ?>
        <option value="<?php echo esc_attr($category->id); ?>">
            <?php echo esc_html($category->name); ?>
        </option>
    <?php endforeach; ?>
<?php endif; ?>
