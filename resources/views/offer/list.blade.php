@extends('layouts.master')
@section('content')
<section class="content container-fluid">
    <div class="content-header">
        <div class="row align-items-center">
            <div class="col-md-7">
                <h2>List offers</h2>
            </div>
            <div class="col-md-5 text-right">
                <a href="/offer/new" class="btn btn-primary">Create New Offers</a>
            </div>
        </div>
    </div>
    <div class="box box-list">
        <div class="box-body">
            <div class="fillter">
                <form id="filter-form">
                    @csrf
                    <div class="fillter-header">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <input id="Label_active_filter_all" type="radio" class="active-status" value="all" name="active_filter" checked>
                                    <label for="Label_active_filter_all">All</label>
                                </div>
                                <div class="form-group">
                                    <input id="Label_active_filter_1" type="radio" class="active-status" value="1" name="active_filter">
                                    <label for="Label_active_filter_1">Active</label>
                                </div>
                                <div class="form-group">
                                    <input id="Label_active_filter_0" type="radio" class="active-status" value="0" name="active_filter">
                                    <label for="Label_active_filter_0">Inactive</label>
                                </div>
                            </div>
                            <div class="col-md-2 text-right">
                                <select name="offer_type" class="form-control">
                                    <option value="">Filter by offer type</option>
                                    <option value="up-sell">Upsell</option>
                                    <option value="cross-sell">Cross-sell</option>
                                </select>
                            </div>
                            <div class="col-md-2 text-right">
                                <select name="page_limit" class="form-control">
                                    <option value="10">10</option>
                                    <option value="15">15</option>
                                    <option value="20">20</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="form-group">
                    <div class="name-filter input-group">
                        <input type="text" id="name-filter" maxlength="64" value class="form-control">
                        <div class="icon-search input-group-btn">
                            <button id="search-btn" class="btn btn-primary"><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <hr />
            <div id="list-offer">
                <div class="table-responsive">
                    <table class="table table-bordered table-list-offers table-hover dataTable">
                        <thead>
                            <tr role="row">
                                <th class="ls-active">Active</th>
                                <th class="ls-name">Name</th>
                                <th class="ls-type">Type</th>
                                <th class="ls-location">Location</th>
                                <th class="ls-date">Date Created</th>
                                <th class="ls-report">Report</th>
                                <th class="ls-action">Delete</th>
                            </tr>
                        </thead>
                        @foreach($listOffer as $value)
                            <tr>
                                <td>
                                    <label class="switch-checkbox">
                                        <input type="checkbox" class="change-active-status" offer-id="{{$value->id}}" @if($value->active) checked @endif data-toggle="toggle">
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td class="ls-name">
                                    <span> <a href="{{url('/').'/offer/edit/'.$value->id}}">{{$value->name}}</a></span>
                                </td>
                                <td class="ls-type">
                                    {{$value->type}}
                                </td>
                                <td class="ls-location">
                                    {{$value->trigger_place}}
                                </td>
                                <td>
                                    {{$value->created_at}}
                                </td>
                                <td>
                                    <a href="{{url('/').'/report/offer/'.$value->id}}"><i class="fa fa-bar-chart" aria-hidden="true"></i></a>
                                </td>
                                <td>
                                    <button class="btn btn-default button-delete-offer" offer-id="{{$value->id}}"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                    {!! $listOffer->links() !!}
                </div>
                </div>
        </div>
    </div>
</section>
@endsection
@section('page-script')
    <script type="text/javascript">
        function filterTable(page) {
            let pattern = '';
            if (typeof page !== 'undefined'){
                pattern = '?page='+page;
            }
            let data = $('#filter-form').serialize();
            data = data+'&name='+$('#name-filter').val();
            $.ajax({
                url: '/offer/getTable'+pattern,
                type: 'POST',
                data: data,
                success: function (response) {
                    $('#list-offer').html(response.view);
                }
            });
        }
        $('#filter-form').change(function (e) {
            filterTable();
        });
        $('#list-offer').on('click','a.page-link',function (e) {
            e.preventDefault();
            let page = $(this).attr('href');
            page = page.substr(page.length-1,page.length);
            filterTable(page);
        });
        $('#list-offer').on('click','.change-active-status',function (e) {
            let status = $(this).is(":checked");
            let offerId = $(this).attr('offer-id');
            let data = {
                "_token": "{{ csrf_token() }}",
                "status": status,
                "id": offerId
            }
            $.ajax({
                url: '/offer/changeStatus',
                type: 'POST',
                data: data
            });
        });
        $('#list-offer').on('click','.button-delete-offer',function (e) {
            let offer_id = $(this).attr('offer-id');
            let page = $('span.page-link').text();
            page = page.substr(1,2);
            $.ajax({
                url: '/offer/delete',
                type: 'GET',
                data: {
                    id:offer_id
                },
                success: function (response) {
                    if (response.success = true){
                        filterTable(page);
                    }
                }
            });
        });
        $('#search-btn').click(function () {
            filterTable();
        });
    </script>
@stop