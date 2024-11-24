<?php

if (!defined('ABSPATH')) exit;

class APF_Ajax_Handler {
    public function __construct() {
        add_action('wp_ajax_apf_filter_products', [$this, 'filter_products']);
        add_action('wp_ajax_nopriv_apf_filter_products', [$this, 'filter_products']);
    }

    public function filter_products() {
        // Prepare default WooCommerce query arguments
        $args = wc_get_loop_prop('query_args', []);

        // If no filters are selected, show all products
        if (empty($_POST['categories']) && empty($_POST['attributes']) && empty($_POST['min_price']) && empty($_POST['max_price'])) {
            // Show default WooCommerce product query
            $args = [
                'post_type' => 'product',
                'posts_per_page' => 12,
                'orderby' => 'date',
                'order' => 'DESC',
            ];
        } else {
            // Add filters to the query
            if (!empty($_POST['categories'])) {
                $args['tax_query'][] = [
                    'taxonomy' => 'product_cat',
                    'field'    => 'slug',
                    'terms'    => $_POST['categories'],
                    'operator' => 'IN',
                ];
            }

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
        }

        // Query products
        $query = new WP_Query($args);

        ob_start();

        if ($query->have_posts()) {
            woocommerce_product_loop_start();

            while ($query->have_posts()) {
                $query->the_post();
                wc_get_template_part('content', 'product');
            }

            woocommerce_product_loop_end();
        } else {
            echo '<p>No products found</p>';
        }

        wp_reset_postdata();

        $response = ob_get_clean();
        wp_send_json_success($response);
    }
}

new APF_Ajax_Handler();
