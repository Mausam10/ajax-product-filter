<?php

if (!defined('ABSPATH')) exit;

class APF_Ajax_Handler {
    public function __construct() {
        add_action('wp_ajax_apf_filter_products', [$this, 'filter_products']);
        add_action('wp_ajax_nopriv_apf_filter_products', [$this, 'filter_products']);
    }

    public function filter_products() {
        $args = [
            'post_type' => 'product',
            'posts_per_page' => 12,
            'post_status' => 'publish',
            'paged' => isset($_POST['page']) ? intval($_POST['page']) : 1,
        ];

        // Category filter
        if (!empty($_POST['categories'])) {
            $args['tax_query'][] = [
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => $_POST['categories'],
                'operator' => 'IN',
            ];
        }

        // Attributes filter
        if (!empty($_POST['attributes'])) {
            foreach ($_POST['attributes'] as $attribute => $values) {
                $args['tax_query'][] = [
                    'taxonomy' => 'pa_' . $attribute,
                    'field'    => 'slug',
                    'terms'    => $values,
                    'operator' => 'IN',
                ];
            }
        }


        $query = new WP_Query($args);

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                wc_get_template_part('content', 'product');
            }
        } else {
            echo '<p>No products found</p>';
        }

        wp_reset_postdata();
        wp_die();
    }
}

new APF_Ajax_Handler();
