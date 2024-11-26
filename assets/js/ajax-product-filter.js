jQuery(document).ready(function ($) {
    // Function to fetch and update product grid based on selected filters
    function fetchProducts(paged = 1) {
        const categories = [];
        $('.apf-category:checked').each(function () {
            categories.push($(this).val());
        });

        const tags = [];
        $('.apf-tag:checked').each(function () {
            tags.push($(this).val());
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
                tags,
                attributes,
                paged: paged, // Send the current page number to the server
            },
            success: function (response) {
                if (response.success) {
                    $('.products').html(response.data.products); // Replace WooCommerce product grid
                    $('.apf-pagination').html(response.data.pagination); // Replace pagination
                }
            },
        });
    }

    // Trigger filtering when any filter is changed
    $('.apf-category, .apf-tag, .apf-attribute').on('change', function () {
        fetchProducts(); // Reset to page 1 when filters change

        // Show or hide the "Remove All Filters" button based on selected filters
        if ($('.apf-category:checked').length || $('.apf-tag:checked').length || $('.apf-attribute:checked').length) {
            $('#apf-remove-all-filters').show(); // Show the button if any filter is selected
        } else {
            $('#apf-remove-all-filters').hide(); // Hide the button if no filters are selected
        }
    });

    // Trigger pagination when a page link is clicked
    $(document).on('click', '.apf-pagination-link, .apf-pagination-prev, .apf-pagination-next', function (e) {
        e.preventDefault();
        const page = $(this).data('page');
        fetchProducts(page); // Fetch products for the selected page
    });

    // Handle the "Remove All Filters" button click
    $('#apf-remove-all-filters').on('click', function () {
        // Uncheck all checkboxes
        $('.apf-category, .apf-tag, .apf-attribute').prop('checked', false);

        // Re-trigger the AJAX request with no filters applied
        fetchProducts();

        // Hide the "Remove All Filters" button after clearing the filters
        $(this).hide();
    });

    // Initial product grid when page loads with no filters applied
    $(window).on('load', function () {
        fetchProducts(); // Fetch products by default (no filter applied)

        // Hide the "Remove All Filters" button on page load if no filters are selected
        if ($('.apf-category:checked').length || $('.apf-tag:checked').length || $('.apf-attribute:checked').length) {
            $('#apf-remove-all-filters').show();
        } else {
            $('#apf-remove-all-filters').hide();
        }
    });
});

