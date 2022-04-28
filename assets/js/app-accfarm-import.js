jQuery(document).ready(async function ($) {
    // Data
    let accfarmData = {
        categories: {},
        products: {},
        offers: {},

        categoryId: 0,
        productId: 0,
    };

    let importCategoriesArray = [];
    let importProductsArray = [];
    let importOffersArray = [];

    let page = 'categories';

    let importStarted = false;

    // Init
    getCategories().then(() => {
        categoriesTable();
    });

    $('#import-all').click(function () {
        if (importStarted) {
            return;
        }

        $(':button').prop('disabled', true);
        importStarted = true;

        importAll().then(() => {
            window.location.replace(document.location.origin + '/wp-admin/edit.php?post_type=product');
        }).catch(e => {
            if (e.statusText) {
                    alert('Error! ' + e.statusText);
                } else {
                    alert('Error!')
                }
            importStarted = false;
            $(':button').prop('disabled', false);
        });
    });

    // Events
    $('.check-all').click(function () {
        if ($(this).prop('checked')) {
            $('input[type="checkbox"]').prop('checked', true);
        } else {
            $('input[type="checkbox"]').prop('checked', false);
        }
    });

    $('#import-selected').click(function () {
        let checked = $('input:checked');

        if (importStarted || checked.length == 0) {
            return;
        }

        $(':button').prop('disabled', true);
        importStarted = true;

        if (page == 'categories') {
            checked.each(index => {
                importCategoriesArray.push($(checked[index]).attr('data-id'));
            });

            importCategories().then(() => {
                window.location.replace(document.location.origin + '/wp-admin/edit.php?post_type=product');
            }).catch(e => {
                if (e.statusText) {
                    alert('Error! ' + e.statusText);
                } else {
                    alert('Error!')
                }
                importStarted = false;
                $(':button').prop('disabled', false);
            });
        } else if (page == 'products') {
            checked.each(index => {
                importProductsArray.push($(checked[index]).attr('data-id'));
            });

            importProducts().then(() => {
                window.location.replace(document.location.origin + '/wp-admin/edit.php?post_type=product');
            }).catch(e => {
                if (e.statusText) {
                    alert('Error! ' + e.statusText);
                } else {
                    alert('Error!')
                }
                importStarted = false;
                $(':button').prop('disabled', false);
            });
        } else if (page == 'offers') {
            checked.each(index => {
                importOffersArray.push($(checked[index]).attr('data-id'));
            });

            importOffers().then(() => {
                window.location.replace(document.location.origin + '/wp-admin/edit.php?post_type=product');
            }).catch(e => {
                if (e.statusText) {
                    alert('Error! ' + e.statusText);
                } else {
                    alert('Error!')
                }
                importStarted = false;
                $(':button').prop('disabled', false);
            });
        }
    });

    $("#go-back").click(function () {
        if (page == 'offers') {
            productsTable();
        } else if (page == 'products') {
            categoriesTable();
        }
    });

    $('#set-accfarm-prices').click(function () {
        if ($(this).prop('checked')) {
            $('.prices-dependent').show();
        } else {
            $('.prices-dependent').hide();
        }
    });

    $('#price-margin').on('input', function () {
        updatePriceMargins(this);
    });

    $('#price-margin-select').on('change', function () {
        updatePriceMargins($('#price-margin'));
    });

    function categoryLink() {
        $('.link-category').click(function () {
            accfarmData.categoryId = $(this).attr('data-id');

            if (!accfarmData.categoryId) {
                return;
            }

            productsTable();
        });
    }

    function productLink() {
        $('.link-product').click(function () {
            accfarmData.productId = $(this).attr('data-id');

            if (!accfarmData.productId) {
                return;
            }

            offersTable();
        });
    }

    function importOne() {
        $('.button-cimport').click(function () {
            let id = $(this).attr('data-id');

            if (importStarted) {
                return;
            }

            importStarted = true;
            $(':button').prop('disabled', true);

            if (page == 'categories') {
                importCategoriesArray.push(id);

                importCategories().then(() => {
                    window.location.replace(document.location.origin + '/wp-admin/edit.php?post_type=product');
                }).catch(e => {
                    if (e.statusText) {
                        alert('Error! ' + e.statusText);
                    } else {
                        alert('Error!')
                    }
                    importStarted = false;
                    $(':button').prop('disabled', false);
                });
            } else if (page == 'products') {
                importProductsArray.push(id);

                importProducts().then(() => {
                    window.location.replace(document.location.origin + '/wp-admin/edit.php?post_type=product');
                }).catch(e => {
                    if (e.statusText) {
                        alert('Error! ' + e.statusText);
                    } else {
                        alert('Error!')
                    }
                    importStarted = false;
                    $(':button').prop('disabled', false);
                });
            } else if (page == 'offers') {
                importOffersArray.push(id);

                importOffers().then(() => {
                    window.location.replace(document.location.origin + '/wp-admin/edit.php?post_type=product');
                }).catch(e => {
                    if (e.statusText) {
                        alert('Error! ' + e.statusText);
                    } else {
                        alert('Error!')
                    }
                    importStarted = false;
                    $(':button').prop('disabled', false);
                });
            }
        });
    }

    // Functions
    function updatePriceMargins(obj) {
        if ($(obj).val()) {
            let marginType = $('#price-margin-select option:selected').attr('value');
            let value = $(obj).val();

            if (marginType == 'sum') {
                $('.price-value-data').each(function () {
                    $(this).text(
                        Math.round((parseFloat($(this).attr('data-base-value')) + parseFloat(value)) * 100) / 100
                    );
                });
            } else if (marginType == 'percent') {
                $('.price-value-data').each(function () {
                    $(this).text(
                        Math.round(((parseFloat($(this).attr('data-base-value')) * (parseFloat(value) / 100))) * 100) / 100
                    );
                });
            }

        } else {
            $('.price-value-data').each(function () {
                $(this).text($(this).attr('data-base-value'));
            });
        }
    }

    function getCategories() {
        return wp.ajax
            .post('accfarm_get_categories_data')
            .done(response => {
                accfarmData.categories = response;
            }).catch(error => {
                if (error.responseJSON.data.error) {
                    alert(error.responseJSON.data.error);
                }
            });
    }

    function getOffers(productId) {
        return wp.ajax
            .post('accfarm_get_offers_data', {product_id: productId})
            .done(response => {
                accfarmData.offers = response;
            }).catch(error => {
                if (error.responseJSON.data.error) {
                    alert(error.responseJSON.data.error);
                }
            });
    }

    function importCategories() {
        let options = getOptions();

        return wp.ajax
            .post('accfarm_get_categories_import_ids', {
                categories: importCategoriesArray,
                publish: options.publish,
                setPrices: options.setPrices,
                margin: options.margin,
                marginType: options.marginType,
                addCategories: options.addCategories
            })
            .done(response => {
                accfarmData.offers = response;
            });
    }

    function importProducts() {
        let options = getOptions();

        return wp.ajax
            .post('accfarm_get_products_import_ids', {
                products: importProductsArray,
                publish: options.publish,
                setPrices: options.setPrices,
                margin: options.margin,
                marginType: options.marginType,
                addCategories: options.addCategories
            })
            .done(() => {});
    }

    function importOffers() {
        let offersNew = [];

        accfarmData.offers.forEach(offer => {
            importOffersArray.forEach(id => {
                if (id == offer.id) {
                    offersNew.push(offer);
                }
            });
        });

        let options = getOptions();

        return wp.ajax
            .post('accfarm_get_offers_import_ids', {
                offers: offersNew,
                publish: options.publish,
                setPrices: options.setPrices,
                margin: options.margin,
                marginType: options.marginType,
                addCategories: options.addCategories
            })
            .done(() => {});
    }

    function importAll() {
        return wp.ajax
            .post('accfarm_get_offers_import_all', getOptions())
            .done(() => {});
    }

    function categoriesTable() {
        clearTable();
        checkPage('categories');
        $('.af-type-name').html('Category');
        $('#af-table-body').append('<tr></tr>');

        accfarmData.categories.forEach(category => {
            $('#af-table-body tr:last').after('<tr>' +
                '<td><input style="margin-left: 8px;" data-id="' + category.id + '" type="checkbox" class="check-category"></td>' +
                '<td><a href="#" class="link-category" data-id="' + category.id + '">' + category.name + '</a></td>' +
                '<td><input value="Import Category Offers" data-id="' + category.id + '" type="button" class="button-cimport"></td>' +
                '</tr>');
        });

        importOne();
        categoryLink();
    }

    function productsTable() {
        checkPage('products');
        clearTable();
        $('.af-type-name').html('Product');
        $('#af-table-body').append('<tr></tr>');

        accfarmData.categories.forEach(category => {
            if (category.id != accfarmData.categoryId) {
                return;
            }

            category.product.forEach(product => {
                $('#af-table-body tr:last').after('<tr>' +
                    '<td><input style="margin-left: 8px;" data-id="' + product.id + '" type="checkbox" class="check-product"></td>' +
                    '<td><a href="#" class="link-product" data-id="' + product.id + '">' + product.name + '</a></td>' +
                    '<td><input value="Import Product Offers" data-id="' + product.id + '" type="button" class="button-cimport"></td>' +
                    '</tr>');
            });
        });

        importOne();
        productLink();
    }

    function offersTable() {
        checkPage('offers');
        clearTable();
        $('.af-type-name').html('Offer');
        $('#af-table-body').append('<tr></tr>');

        getOffers(accfarmData.productId)
            .then(() => {
                if (page == 'products') {
                    return;
                }
                accfarmData.offers.forEach(offer => {
                    $('#af-table-body tr:last').after('<tr>' +
                        '<td><input style="margin-left: 8px;" data-id="' + offer.id + '" type="checkbox" class="check-offer"></td>' +
                        '<td>' + offer.name + ' <b data-base-value="' + offer.price_value + '" class="price-value-data">' + offer.price_value + '</b><b>$</b></td>' +
                        '<td><input value="Import Offer" data-id="' + offer.id + '" type="button" class="button-cimport"></td>' +
                        '</tr>');
                    });

                updatePriceMargins($('#price-margin'));
                importOne();
                });
    }

    function checkPage(setPage) {
        page = setPage;

        if (page == 'categories') {
            $('#go-back').hide();
        } else {
            $('#go-back').show();
        }
    }

    function getOptions() {
        let publish = $('#publish-products').prop('checked');
        let addCategories = $('#add-woo-categories').prop('checked');
        let setPrices = $('#set-accfarm-prices').prop('checked');
        let margin = $('#price-margin').val();
        let marginType = $('#price-margin-select option:selected').attr('value');

        return {
            publish: publish,
            setPrices: setPrices,
            margin: margin,
            marginType: marginType,
            addCategories: addCategories,
        }
    }

    function clearTable() {
        $('#af-table-body').empty();
    }
});