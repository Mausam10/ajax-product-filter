jQuery(document).ready(function ($) {
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

        $.ajax({
            url: apf_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'apf_filter_products',
                categories,
                attributes,
            },
            success: function (response) {
                if (response.success) {
                    $('.products').html(response.data); // Replace WooCommerce product grid
                }
            },
        });
    }

    $('#apf-apply-filters').on('click', function () {
        fetchProducts();
    });

    $('.apf-category, .apf-attribute').on('change', function () {
        fetchProducts();
    });
});
