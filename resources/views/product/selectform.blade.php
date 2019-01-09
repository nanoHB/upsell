<div class="form-horizontal">
    <form id="product_filter">
        <div class="control-group">
            <label for="product_title">Enter product title</label>
            <input type="text" name="product_title" placeholder="Enter product title">
        </div>
        <div class="control-group">
            <label for="product_collection">Custom Collection</label>
            <select name="product_collection" id="product_collection">
                <option-Select Custom Collection-</option>
                @foreach($filterList['custom_collection'] as $value)
                    <option value="{{$value['id']}}">{{$value['title']}}</option>
                @endforeach
            </select>
        </div>
        <div class="control-group">
            <label for="product_vendor">Search by vendor</label>
            <select name="product_vendor" id="product_vendor">
                <option>-Vendor-</option>
                @foreach($filterList['vendor'] as $value)
                    <option value="{{$value}}">{{$value}}</option>
                @endforeach
            </select>
        </div>
        <div class="control-group">
            <label for="product_type">Search by product type</label>
            <select name="product_type" id="product_type">
                <option>-Product Type-</option>
                @foreach($filterList['product_type'] as $value)
                    <option value="{{$value}}">{{$value}}</option>
                @endforeach
            </select>
        </div>
        <div class="control-group"><label for="product_limit">Result per page</label>
            <select name="product_limit" id="product_limit">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
            </select></div>
        <div class="product-buttons">
            <button type="button" id="search" class="btn btn-default">Search</button>
            <button type="button" id="reset-filter" class="btn btn-default">Reset</button>
        </div>
    </form>
</div>
<div id="product-list" class="product-select-list">
    <div id="product_selector_wraper" class="result">
        <table id="product_selector_result">
            <tr>
                <td colspan="4">Result</td>
            </tr>
            @foreach($productList as $value)
                @if($value['count_variant'] <= 1)
                    <tr id="select-product-{{$value['product_id']}}">
                        <td class="pd-image">
                            <img id="product-select-image-{{$value['product_id']}}" src="{{$value['image']}}" alt="{{$value['title']}}">
                        </td>
                        <td class="pd-title">{{$value['title']}}</td>
                        <td class="pd-variants">{{$value['variants'][0]['price']}}</td>
                        <td class="pd-action">
                            <button type="button" id="add-btn-{{$value['variants'][0]['variant_id']}}" class="add-product @if($value['variants'][0]['selected'] == 'true') selected @endif" variant-id="{{$value['variants'][0]['variant_id']}}"
                                    product-price="{{$value['variants'][0]['price']}}" product-name="{{$value['title']}}"
                                    variant-title="{{$value['variants'][0]['option']}}" product-id="{{$value['product_id']}}">add</button>
                        </td>
                    </tr>
                @else
                    <tr id="select-product-{{$value['product_id']}}">
                        <td rowspan="{{$value['count_variant']+1}}" class="pd-image">
                            <img id="product-select-image-{{$value['product_id']}}" src="{{$value['image']}}" alt="{{$value['title']}}">
                        </td>
                        <td class="pd-title">{{$value['title']}}</td>
                        <td class="pd-variants">{{$value['variants'][0]['price']}}</td>
                        <td class="pd-action">
                            <button type="button" id="add-btn-{{$value['variants'][0]['variant_id']}}" class="add-product @if($value['variants'][0]['selected'] == 'true') selected @endif" variant-id="{{$value['variants'][0]['variant_id']}}"
                                    product-price="{{$value['variants'][0]['price']}}" product-name="{{$value['title']}}"
                                    variant-title="{{$value['variants'][0]['option']}}" product-id="{{$value['product_id']}}">add</button>
                        </td>
                    </tr>
                    @for($i = 1; $i<$value['count_variant']; $i++)
                        <tr id="select-product-{{$value['product_id']}}">
                            <td class="pd-title">{{$value['title']}}</td>
                            <td class="pd-variants">{{$value['variants'][$i]['price']}}</td>
                            <td class="pd-action">
                                <button type="button" id="add-btn-{{$value['variants'][$i]['variant_id']}}" class="add-product @if($value['variants'][$i]['selected'] == 'true') selected @endif" variant-id="{{$value['variants'][$i]['variant_id']}}"
                                        product-price="{{$value['variants'][$i]['price']}}" product-name="{{$value['title']}}"
                                        variant-title="{{$value['variants'][$i]['option']}}" product-id="{{$value['product_id']}}">add</button>
                        </tr>
                    @endfor
                @endif
            @endforeach
        </table>
    </div>
    <div id="product_selector_pagination" class="pagination pagination-sm inline ">

    </div>
</div>
<div id="product-selected" class="product-selected-result-list">
    <table id="product_selected_result">
        @foreach($selectedProduct as $value)
            <tr id="product-selected-row-{{$value['variant_id']}}">
                <td class="pd-image">
                    <img src="{{$value['image']}}" alt="{{$value['title']}}">
                </td>
                <td class="pd-name">{{$value['name']}}</td>
                <td class="pd-title">{{$value['title']}}</td>
                <td class="pd-variants">{{$value['price']}}</td>
                <td class="pd-action">
                    <button class="remove-product" type="button" variant-id="{{$value['variant_id']}}" product-id="{{$value['product_id']}}">remove</button>
                </td>
            </tr>
        @endforeach
    </table>
</div>
<script type="text/javascript">
    $('.add-product').on('click',function () {
        var data = {
            image: $('#product-select-image-'+$(this).attr('product-id')).attr('src'),
            name: $(this).attr('product-name'),
            price: $(this).attr('product-price'),
            product_id: $(this).attr('product-id'),
            title: $(this).attr('variant-title'),
            variant_id: $(this).attr('variant-id'),
        };
        fillSelectedProduct(0,data);
        $(this).attr('class','add-product selected');
    });
    $('#product-selected').on('click','.remove-product',function () {
        $('#add-btn-'+$(this).attr('variant-id')).attr('class','add-product');
        $('#product-selected-row-'+$(this).attr('variant-id')).remove();
    });
</script>