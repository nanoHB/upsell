$('#start-time, #end-time').datepicker({
    autoclose: true,
    format: 'd-m-yyyy'
});

function generatePagination(totalPage,selectPage) {
    $('#product_selector_pagination').html('');
    for (let i =1;i<=totalPage;i++){
        if(i == selectPage){
            $('#product_selector_pagination').append($('<li class="page-item active">').append($('<span>',{class: 'product-selector-paginate', text: i})));
        }else {
            $('#product_selector_pagination').append($('<li class="page-item">').append($('<a>',{class: 'product-selector-paginate', text: i})));
        }
    }
}

function fillProductData(i, val) {
    if (val.variants.length > 1) {
        var image = '<td class="pd-image" rowspan="' + (val.variants.length + 1) + '"><img id="product-select-image-' + val.product_id + '" src="' + val.image + '" alt="' + val.title + '"></td>';
        var name = '<td class="pd-title">' + val.title + '</td>';
        var variant = '<td class="pd-variants">' + val.variants.length + ' variants</td>';
        var listVariant = '<td class="pd-action"><i class="fa fa-chevron-down" aria-hidden="true"></i></td>';
        for (var i = 0; i < val.variants.length; i++) {
            let btn_class = 'add-product';
            if (val.variants[i].selected == 'true') {
                btn_class = 'add-product selected btn btn-success'
            }
            listVariant = listVariant + '<tr id="select-product-' + val.variants[i].variant_id + '">';
            listVariant = listVariant + '<td class="pd-variants-name">' + val.variants[i].option + '</td><td>' + val.variants[i].price + '</td><td class="pd-action"><button type="button" id="add-btn-' + val.variants[i].variant_id + '" class="btn btn-success ' + btn_class + '"' +
                ' variant-id="' + val.variants[i].variant_id + '" ' +
                'product-price="' + val.variants[i].price + '" product-name="' + val.title + '" variant-title="' + val.variants[i].option + '" product-id="' + val.product_id + '"><i class="fa fa-plus" aria-hidden="true"></i></button></td>';
            listVariant = listVariant + '</tr>';
        }
        var newNode = '<tr>' + image + name + variant + listVariant + '</tr>';
    } else {
        let btn_class = 'add-product';
        if (val.variants[0].selected == 'true') {
            btn_class = 'add-product selected btn btn-success'
        }
        var image = '<td class="pd-image"><img id="product-select-image-' + val.product_id + '" src="' + val.image + '" alt="' + val.title + '"></td>';
        var name = '<td class="pd-title">' + val.title + '</td>';
        var variant = '<td class="pd-variants">' + val.variants[0].price + '</td>';
        var listVariant = '<td class="pd-action"><button type="button" id="add-btn-' + val.variants[0].variant_id + '" class="btn btn-success ' + btn_class + '" variant-id="' + val.variants[0].variant_id + '"' +
            ' product-price="' + val.variants[0].price + '" product-name="' + val.title + '" variant-title="' + val.variants[0].option + '" product-id="' + val.product_id + '"><i class="fa fa-plus" aria-hidden="true"></i></button></td>'
        var newNode = '<tr id="select-product-' + val.variants[0].variant_id + '">' + image + name + variant + listVariant + '</tr>';
    }
    $('#product_selector_result').append(newNode);
}

function fillSelectedProduct(i, val) {
    var image = '<td class="pd-image"><img src="' + val.image + '" alt="' + val.title + '"></td>';
    var name = '<td class="pd-name">' + val.name + '</td>';
    var title = '<td class="pd-title">' + val.title + '</td>';
    var variant = '<td class="pd-variants">' + val.price + '</td>';
    var listVariant = '<td class="pd-action"><button class="btn btn-default remove-product" type="button" ' + 'variant-id="' + val.variant_id + '"  product-id="' + val.product_id + '"><i class="fa fa-trash" aria-hidden="true"></i></button></td>'
    var newNode = '<tr id="product-selected-row-' + val.variant_id + '" >' + image + name + title + variant + listVariant + '</tr>';
    $('#product_selected_result').append(newNode);
}

function clearResult() {
    $('#product_selector_result').empty();
    $('#product_collection').empty();
    $('#product_vendor').empty();
    $('#product_type').empty();
    $('#product_selector_pagination').empty();
    $('#product_selected_result').empty();
    $('#product_collection').append($('<option>', {value: '', text: '-Select Custom Collection-'}));
    $('#product_vendor').append($('<option>', {value: '', text: '-Vendor-'}));
    $('#product_type').append($('<option>', {value: '', text: '-Product Type-'}));
    $('#product_selector_result').append($('<tr>').append($('<td>', {colspan: 4, text: 'Result'})));
    $('#product_selected_result').append($('<tr>').append($('<td>', {colspan: 5, text: 'Selected Result'})));
}

$('#trigger-product').click(function () {
    $('#selected_complete').attr('form-type', 'trigger');
    var listSelected = $('#trigger_product_input').val();
    $.ajax({
        url: '/product/getForm',
        type: 'GET',
        data: {
            selected_product: listSelected
        },
        success: function (response) {
            let collection_filter = response.filterList.custom_collection;
            let product_type = response.filterList.product_type;
            let vendor = response.filterList.vendor;
            let product_list = response.productList;
            let page = response.totalPage;
            let selected_product = response.selectedProduct;
            $.each(collection_filter, function (i, val) {
                $('#product_collection').append($('<option>', {value: val.id, text: val.title}));
            });
            $.each(vendor, function (i, val) {
                $('#product_vendor').append($('<option>', {value: val, text: val}))
            });
            $.each(product_type, function (i, val) {
                $('#product_type').append($('<option>', {value: val, text: val}))
            });
            $.each(product_list, function (i, val) {
                fillProductData(i, val);
            });
            generatePagination(page,1);
            if (selected_product != 'none') {
                $.each(selected_product, function (i, val) {
                    fillSelectedProduct(i, val);
                });
            }
        }
    });
});
$('#offer-product').click(function () {
    $('#selected_complete').attr('form-type', 'offer');
    var listSelected = $('#offer-product-input').val();
    $.ajax({
        url: '/product/getForm',
        type: 'GET',
        data: {
            selected_product: listSelected
        },
        success: function (response) {
            let collection_filter = response.filterList.custom_collection;
            let product_type = response.filterList.product_type;
            let vendor = response.filterList.vendor;
            let product_list = response.productList;
            let selected_product = response.selectedProduct;
            let page = response.totalPage;
            $.each(collection_filter, function (i, val) {
                $('#product_collection').append($('<option>', {value: val.id, text: val.title}));
            });
            $.each(vendor, function (i, val) {
                $('#product_vendor').append($('<option>', {value: val, text: val}))
            });
            $.each(product_type, function (i, val) {
                $('#product_type').append($('<option>', {value: val, text: val}))
            });
            $.each(product_list, function (i, val) {
                fillProductData(i, val);
            });
            generatePagination(page,1);
            if (selected_product != 'none') {
                $.each(selected_product, function (i, val) {
                    fillSelectedProduct(i, val);
                });
            }
        }
    });
});
$('#selected_complete').click(function () {
    var selectedProduct = '';
    $('.remove-product').each(function () {
        selectedProduct = selectedProduct + $(this).attr('product-id') + '-' + $(this).attr('variant-id') + ',';
    });
    selectedProduct = selectedProduct.substring(0, selectedProduct.length - 1);
    if ($(this).attr('form-type') == 'trigger') {
        $('#trigger_product_input').val(selectedProduct);
    } else {
        $('#offer-product-input').val(selectedProduct);
    }
    $("#product-modal").modal('hide');
});
$('#product_selector_wraper').on('click', '.add-product', function () {
    var data = {
        image: $('#product-select-image-' + $(this).attr('product-id')).attr('src'),
        name: $(this).attr('product-name'),
        price: $(this).attr('product-price'),
        product_id: $(this).attr('product-id'),
        title: $(this).attr('variant-title'),
        variant_id: $(this).attr('variant-id'),
    };
    fillSelectedProduct(0, data);
    $(this).addClass('selected');
});
$('#product-selected').on('click', '.remove-product', function () {
    $('#add-btn-' + $(this).attr('variant-id')).removeClass('selected');
    $('#product-selected-row-' + $(this).attr('variant-id')).remove();
});
$('#search').click(function () {
    var formdata = $('#product_filter').serialize();
    var selectedProduct = '';
    $('.remove-product').each(function () {
        selectedProduct = selectedProduct + $(this).attr('product-id') + '-' + $(this).attr('variant-id') + ',';
    });
    selectedProduct = selectedProduct.substring(0, selectedProduct.length - 1);
    formdata = formdata + '&selected_product=' + selectedProduct;
    $.ajax({
        url: '/product/filter',
        type: 'POST',
        data: formdata,
        dataType: 'json',
        success: function (response) {
            $('#product_selector_result').empty();
            $('#product_selector_result').append($('<tr>').append($('<td>', {colspan: 4, text: 'Result'})));
            var product_list = response.productList;
            $.each(product_list, function (i, val) {
                fillProductData(i, val);
                generatePagination(response.totalPage,1);
            });
        }
    });
});
$('#product_selector_pagination').on('click','a.product-selector-paginate',function () {
    var formdata = $('#product_filter').serialize();
    var selectedProduct = '';
    var pageSelected = $(this).text();
    $('.remove-product').each(function () {
        selectedProduct = selectedProduct + $(this).attr('product-id') + '-' + $(this).attr('variant-id') + ',';
    });
    selectedProduct = selectedProduct.substring(0, selectedProduct.length - 1);
    formdata = formdata +'&page='+pageSelected + '&selected_product=' + selectedProduct;
    $.ajax({
        url: '/product/filter',
        type: 'POST',
        data: formdata,
        dataType: 'json',
        success: function (response) {
            $('#product_selector_result').empty();
            $('#product_selector_result').append($('<tr>').append($('<td>', {colspan: 4, text: 'Result'})));
            var product_list = response.productList;
            $.each(product_list, function (i, val) {
                fillProductData(i, val);
            });
            generatePagination(response.totalPage,pageSelected);
        }
    });
});
$('#product-modal').on('hidden.bs.modal',function () {
   clearResult();
});
