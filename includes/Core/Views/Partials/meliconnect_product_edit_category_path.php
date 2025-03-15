<?php if (!empty($categories)) : ?>
    <select>
        <option value=""><?php esc_html_e('Select a category', 'meliconnect'); ?></option>
        <?php foreach ($categories as $category) : ?>
            <option value="<?php echo esc_attr($category->id); ?>"><?php echo esc_html($category->name); ?></option>
        <?php endforeach; ?>
    </select>
<?php endif; ?>

<div class="options_group">
    <p class="form-field">
        <label class="melicon-selected-category-span">
            <?php esc_html_e('Category Root', 'meliconnect'); ?>:
        </label>
        <nav class="description melicon-is-inline-block melicon-category-path melicon-breadcrumb melicon-has-succeeds-separator melicon-ml-4" aria-label="breadcrumbs">
            <ul>
                <?php foreach ($path_from_root as $parent) : ?>
                    <li>
                        <a href="/" class="melicon-category-link" data-category-id="<?php echo esc_attr($parent->id); ?>">
                            <?php echo esc_html($parent->name); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
                <?php if (!empty($path_from_root)) : ?>
                    <li>
                        <a href="" class="melicon-category-link" data-category-id="0">
                            <i class="fa fa-times melicon-ml-2" aria-hidden="true"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </p>
</div>
