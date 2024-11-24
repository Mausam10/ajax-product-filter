jQuery(document).ready(function ($) {
    // Function to fetch and update product grid based on selected filters
    function fetchProducts() {
        const categories = [];
        $('.apf-category:checked').each(function () {
            categories.push($(this).val());
        });

        const attributes = {};
        $('.apf-attribute:checked').each(function () {
            const attribute = $(this).data('attribute');
            const value = $(this).val();

            if (!attributes[attribute]) {
                attributes[attribute] = [];
            }
            attributes[attribute].push(value);
        });

        const minPrice = $('#apf-min-price').val();
        const maxPrice = $('#apf-max-price').val();

        $.ajax({
            url: apf_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'apf_filter_products',
                categories,
                attributes,
                min_price: minPrice,
                max_price: maxPrice,
            },
            success: function (response) {
                if (response.success) {
                    $('.products').html(response.data); // Replace WooCommerce product grid
                }
            },
        });
    }

    // Trigger filtering when any filter is changed
    $('.apf-category, .apf-attribute').on('change', function () {
        fetchProducts();
    });

    // Trigger filtering when price range is changed
    $('#apf-min-price, #apf-max-price').on('change', function () {
        fetchProducts();
    });

    // Initial product grid when page loads with no filters applied
    $(window).on('load', function () {
        fetchProducts(); // Fetch products by default (no filter applied)
    });
});
