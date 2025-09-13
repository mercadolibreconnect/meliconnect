
<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
?>
<div class="options_group">
    <p class="form-field">
        <label class="meliconnect-selected-category-span">
            <?php esc_html_e('Category Root', 'meliconnect'); ?>:
        </label>
        <nav class="description meliconnect-is-inline-block meliconnect-category-path meliconnect-breadcrumb meliconnect-has-succeeds-separator meliconnect-ml-4" aria-label="breadcrumbs">
            <ul>
                <?php foreach ($path_from_root as $parent) : ?>
                    <li>
                        <a href="/" class="meliconnect-category-link" data-category-id="<?php echo esc_attr($parent->id); ?>">
                            <?php echo esc_html($parent->name); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
                <?php if (!empty($path_from_root)) : ?>
                    <li>
                        <a href="" class="meliconnect-category-link" data-category-id="0">
                            <i class="fa fa-times meliconnect-ml-2" aria-hidden="true"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </p>
</div>
