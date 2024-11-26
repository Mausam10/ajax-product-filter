<?php

if (!defined('ABSPATH')) exit;

class APF_Ajax_Handler {
    public function __construct() {
        add_action('wp_ajax_apf_filter_products', [$this, 'filter_products']);
        add_action('wp_ajax_nopriv_apf_filter_products', [$this, 'filter_products']);
    }

    public function filter_products() {
        // Set the current page from the AJAX request, default to page 1
        $paged = isset($_POST['paged']) ? $_POST['paged'] : 1;

        // Prepare default WooCommerce query arguments
        $args = wc_get_loop_prop('query_args', []);

        // If no filters are selected, show all products
        if (empty($_POST['categories']) && empty($_POST['tags']) && empty($_POST['attributes'])) {
            $args = [
                'post_type' => 'product',
                'posts_per_page' => 12,
                'orderby' => 'date',
                'order' => 'DESC',
                'paged' => $paged, // Handle pagination
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

            // Add tags filter
            if (!empty($_POST['tags'])) {
                $args['tax_query'][] = [
                    'taxonomy' => 'product_tag',
                    'field'    => 'slug',
                    'terms'    => $_POST['tags'],
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

            $args['paged'] = $paged; // Handle pagination
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

        // Handle pagination
        $total_pages = $query->max_num_pages;
        $current_page = $paged;
        $pagination = '';

        if ($total_pages > 1) {
            $pagination .= '<div class="apf-pagination">';
            if ($current_page > 1) {
                $pagination .= '<a class="apf-pagination-prev" data-page="' . ($current_page - 1) . '" href="#">Previous</a>';
            }

            for ($i = 1; $i <= $total_pages; $i++) {
                $pagination .= '<a class="apf-pagination-link" data-page="' . $i . '" href="#">' . $i . '</a>';
            }

            if ($current_page < $total_pages) {
                $pagination .= '<a class="apf-pagination-next" data-page="' . ($current_page + 1) . '" href="#">Next</a>';
            }

            $pagination .= '</div>';
        }

        wp_reset_postdata();

        // Return the product grid and pagination data
        $response = [
            'products' => ob_get_clean(),
            'pagination' => $pagination,
        ];

        wp_send_json_success($response);
    }
}

new APF_Ajax_Handler();
