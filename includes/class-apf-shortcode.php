<?php

if (!defined('ABSPATH')) exit;

class APF_Shortcode {
    public function __construct() {
        add_shortcode('ajax_product_filter_sidebar', [$this, 'render_sidebar_filters']);
    }

    public function render_sidebar_filters() {
        ob_start();
        ?>
        <aside id="apf-sidebar-filters">
            <h3>Filter by Categories</h3>
            <ul>
                <?php
                $categories = get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true]);
                foreach ($categories as $category) {
                    echo '<li>
                            <label>
                                <input type="checkbox" class="apf-category" value="' . esc_attr($category->slug) . '">
                                ' . esc_html($category->name) . '
                            </label>
                          </li>';
                }
                ?>
            </ul>

            <h3>Filter by Tags</h3>
            <ul>
                <?php
                $tags = get_terms(['taxonomy' => 'product_tag', 'hide_empty' => true]);
                foreach ($tags as $tag) {
                    echo '<li>
                            <label>
                                <input type="checkbox" class="apf-tag" value="' . esc_attr($tag->slug) . '">
                                ' . esc_html($tag->name) . '
                            </label>
                          </li>';
                }
                ?>
            </ul>

            <h3>Filter by Attributes</h3>
            <?php
            $attributes = wc_get_attribute_taxonomies();
            foreach ($attributes as $attribute) {
                $taxonomy = 'pa_' . $attribute->attribute_name;
                $terms = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => true]);
                if (!empty($terms)) {
                    echo '<h4>' . esc_html($attribute->attribute_label) . '</h4><ul>';
                    foreach ($terms as $term) {
                        echo '<li>
                                <label>
                                    <input type="checkbox" class="apf-attribute" data-attribute="' . esc_attr($attribute->attribute_name) . '" value="' . esc_attr($term->slug) . '">
                                    ' . esc_html($term->name) . '
                                </label>
                              </li>';
                    }
                    echo '</ul>';
                }
            }
            ?>

            <!-- Remove All Filters Button -->
            <button id="apf-remove-all-filters" class="apf-remove-all-filters" style="display:none;">Remove All Filters</button>
        </aside>
        <?php
        return ob_get_clean();
    }
}

new APF_Shortcode();
