<?php
if (!empty($categories)) : ?>
    <option value=""><?= esc_html__('Select a category', 'meliconnect'); ?></option><?php foreach ($categories as $category) : ?><option value="<?= esc_attr($category->id); ?>"><?= esc_html($category->name); ?></option><?php endforeach; ?>
<?php else : ?>
    <option value=""><?= esc_html__('No categories found', 'meliconnect'); ?></option>
<?php endif; ?>
