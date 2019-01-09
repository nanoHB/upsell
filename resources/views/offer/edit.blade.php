@extends('layouts.master')
@section('content')
    <!-- Main content -->
    <section class="content container-fluid">
        <form class="needs-validation" action="{{url('/').'/offer/edit'}}" method="post" id="offer-form" novalidate>
            @csrf
            <input type="text" name="id" value="{{$offer->id}}" hidden  >
            <div class="content-header">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <H2>Edit Offer</H2>
                        {{--<p>{{ShopifyApp::shop()->id}}</p>--}}
                    </div>
                    <div class="col-md-5 text-right">
                        <button class="btn btn-default" id="discard">Discard</button>
                        <button class="btn btn-primary" id="save">Save</button>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="box box-primary">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="title">Title</label>
                                <input type="text" name="name" class="form-control" value="{{$offer->name}}" required>
                                <div class="invalid-feedback">
                                    Name can not be empty
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box box-primary form-trigger-event">
                        <div class="box-header">
                            <h3 class="box-title">Trigger Place</h3>
                        </div>
                        <div class="box-body">
                            <div class="trigger-place form-group">
                                <div class="row">
                                    <div class="col-md-6 text-center">
                                        <div class="add-to-cart-event">
                                            <label for="Label_trigger_place_cart">
                                                <input id="Label_trigger_place_cart" type="radio" name="trigger_place"  value="product-detail" class="flat-red" value="cart" @if($offer->trigger_place == 'product-detail') checked @endif>
                                            </label>
                                            <div class="desc">
                                                <i class="fa fa-fw fa-file-text-o"></i>
                                                <span>Product Detail Page</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-center">
                                        <div class="add-to-checkout-event">
                                            <label for="Label_trigger_place_checkout">
                                                <input type="radio" id="Label_trigger_place_checkout" name="trigger_place" value="cart" class="flat-red" @if($offer->trigger_place == 'cart') checked @endif>
                                            </label>
                                            <div class="desc">
                                                <i class="fa fa-shopping-cart"></i>
                                                <span>Cart Page</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr />
                            <div class="offer-type form-group">
                                <h5>Options</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="cross-sell-type">
                                            <label for="Label_offer_type_cross">
                                                <input id="Label_offer_type_cross" type="radio" name="offer_type" value="cross-sell" class="flat-red" @if($offer->type == 'cross-sell') checked @endif>Cross-sell
                                            </label>
                                            <span>Add the offer product to the cart</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="cross-sell-type">
                                            <label for="Label_offer_type_up">
                                                <input id="Label_offer_type_up" type="radio" name="offer_type" value="up-sell" class="flat-red" @if($offer->type == 'up-sell') checked @endif>Upsell
                                            </label>
                                            <span>Replace the trigger product with the offer product</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr />
                            <div class="trigger-product-list form-group">
                                <h5>Trigger products</h5>
                                <p>You can restrict this offer to certain products. Without choosing products your upsell will happen every time the above event occurs.</p>
                                <div class="input-group">
                                    <input class="form-control" type="text" name="trigger_products" id="trigger_product_input" value="{{$triggerProduct}}" placeholder="Select Product" required/>
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-primary" id="trigger-product" data-toggle="modal"
                                                data-target="#product-modal">Select Product</button>
                                    </div>
                                    <div class="invalid-feedback">
                                        Please select trigger products
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box box-primary form-offer-list">
                        <div class="box-header">
                            <h3 class="box-title">Offer</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="offer-title">Title</label>
                                <input type="text" name="offer_title" class="form-control" value="{{$offer->title}}" required>
                                <div class="invalid-feedback">
                                    Title can not be empty
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="offer-title">Description</label>
                                <textarea name="offer_description" class="form-control" rows="3">{{$offer->description}}</textarea>
                            </div>
                            <div class="offer-product-list form-group">
                                <div class="input-group">
                                    <input class="form-control" type="text" name="offer_products" id="offer-product-input" value="{{$offerProduct}}" placeholder="Select Product" required>
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-primary" id="offer-product" data-toggle="modal"
                                                data-target="#product-modal">Select Product</button>
                                    </div>
                                </div>
                                <div class="invalid-feedback">
                                    Please select offer product
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="box box-primary form-date-range">
                        <div class="box-header">
                            <h3 class="box-title">Date Range</h3>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label for="start-time">Start date</label>
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right" name="start-time" id="start-time" value="{{date('d-m-Y',strtotime($offer->start_day))}}" required>
                                    <div class="invalid-feedback">
                                        Start date can not be empty
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="end-time">End date</label>
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control pull-right" name="end-time" id="end-time" value="{{date('d-m-Y',strtotime($offer->end_day))}}">
                                    <div class="invalid-feedback">
                                        End date can not be empty
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="modal fade" id="product-modal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span></button>
                        <h4 class="modal-title">Select Product</h4>
                    </div>
                    <div class="modal-body" id="modal-body">
                        <div class="form-horizontal">
                            <form id="product_filter">
                                @csrf
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="product_title">Enter product title</label>
                                        <input type="text" name="product_title" placeholder="Enter product title" class="form-control">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="product_collection">Custom Collection</label>
                                                <select name="product_collection" id="product_collection" class="form-control">
                                                    <option value="">-Select Custom Collection-</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="product_vendor">Search by vendor</label>
                                                <select name="product_vendor" id="product_vendor" class="form-control">
                                                    <option value="">-Vendor-</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="product_type">Search by product type</label>
                                                <select name="product_type" id="product_type" class="form-control">
                                                    <option value="">-Product Type-</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="product_limit">Result per page</label>
                                                <select name="product_limit" id="product_limit" class="form-control">
                                                    <option value="10">10</option>
                                                    <option value="25">25</option>
                                                    <option value="50">50</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="product-buttons">
                                    <button class="btn btn-primary" type="button" id="search">Search</button>
                                    <button class="btn btn-default" type="button" id="reset-filter">Reset</button>
                                </div>
                                <hr />
                            </form>
                        </div>
                        <div class="row search-products">
                            <div class="col-md-6">
                                <h4>Result</h4>
                                <div id="product-list" class="product-select-list">
                                    <div id="product_selector_wraper" class="result table-responsive">
                                        <table id="product_selector_result" class="table table-bordered">
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                    <div class="pagination-sm inline ">
                                        <ul id="product_selector_pagination" class="pagination">

                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h4>Selected Result</h4>
                                <div id="product-selected" class="product-selected-result-list table-responsive">
                                    <table id="product_selected_result" class="table table-bordered">
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="selected_complete" class="btn btn-primary pull-left">Continue with selected
                            products
                        </button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    </section>
    <!-- /.content -->
@endsection
@section('page-script')
    <script src="/js/offer.js"></script>
@stop
