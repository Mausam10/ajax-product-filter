<?php
/**
 * Plugin Name: AJAX Product Filter for WooCommerce (Sidebar Edition)
 * Description: Advanced AJAX product filter with sidebar and checkboxes for WooCommerce.
 * Version: 1.1
 * Author: mausam10
 */

if (!defined('ABSPATH')) exit;

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'includes/class-apf-ajax-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-apf-shortcode.php';

// Enqueue scripts
function apf_enqueue_assets() {
    if (is_shop() || is_product_category()) {
        wp_enqueue_style('apf-style', plugin_dir_url(__FILE__) . 'assets/css/style.css');
        wp_enqueue_script('apf-script', plugin_dir_url(__FILE__) . 'assets/js/ajax-product-filter.js', ['jquery'], '1.0', true);
        wp_localize_script('apf-script', 'apf_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
        ]);
    }
}
add_action('wp_enqueue_scripts', 'apf_enqueue_assets');

//enqueue the styles
function apf_enqueue_styles() {
    wp_enqueue_style('apf-style', plugin_dir_url(__FILE__) . 'assets/css/style.css');
}
add_action('wp_enqueue_scripts', 'apf_enqueue_styles');

