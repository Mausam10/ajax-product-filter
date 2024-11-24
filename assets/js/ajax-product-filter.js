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
                $('#apf-products').html(response);
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
