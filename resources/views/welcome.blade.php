    @extends('layouts.master')
    @section('content')
        <!-- Main content -->
        <section class="view-dashboard content container-fluid">
            <div class="content-header">
                <h2>Overview dashboard</h2>
            </div>
            <div class="fillter-group mb-20">
                <form>
                    <label>Date and time range:</label>
                    <div id="reportrange">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span></span> <i class="fa fa-caret-down"></i>
                    </div>
                </form>
            </div>
            <div class="row">
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-aqua"><i class="ion ion-ios-eye-outline"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">View</span>
                            <span class="info-box-number" id="total-view"></span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-yellow"><i class="ion ion-ios-cart-outline"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Added To Cart</span>
                            <span class="info-box-number" id="total-added-cart"></span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-red"><i class="ion ion-ios-filing-outline"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Conversions</span>
                            <span class="info-box-number" id="total-conversion"></span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-green"><i class="ion ion-ios-pricetag-outline"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Revenue</span>
                            <span class="info-box-number" id="total-revenue"></span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="box box-list">
                        <div class="offer-by-view-box box-body">
                            <div class="box-header">
                                <h3 class="box-title">Offers by views</h3>
                            </div>
                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#highest-view">Highest</a></li>
                                <li><a data-toggle="tab" href="#lowest-view">Lowest</a></li>
                                <div class="tab-content">
                                    <div id="highest-view" class="tab-pane fade in active">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover dataTable">
                                                <tbody id="highest-view-list"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div id="lowest-view" class="tab-pane fade">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover dataTable">
                                                <tbody id="lowest-view-list"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="box box-list">
                        <div class="offer-by-conversion-box box-body">
                            <div class="box-header">
                                <h3 class="box-title">Offer</h3>
                            </div>
                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#highest-conversion">Highest</a></li>
                                <li><a data-toggle="tab" href="#lowest-conversion">Lowest</a></li>
                                <div class="tab-content">
                                    <div id="highest-conversion" class="tab-pane fade in active">
                                        <table class="table table-bordered table-hover dataTable">
                                            <tbody id="highest-conversion-list"></tbody>
                                        </table>
                                    </div>
                                    <div id="lowest-conversion" class="tab-pane fade">
                                        <table class="table table-bordered table-hover dataTable">
                                            <tbody id="lowest-conversion-list"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
    @endsection
    @section('page-script')
        <script type="text/javascript">
            $(function () {
                var start = moment().subtract(7, 'days');
                var end = moment();

                function fillViewTable(data) {
                    let lowest = data.lowestView;
                    let highest = data.highestView;
                    $('#highest-view-list').html('');
                    $.each(highest,function (i,val) {
                        $('#highest-view-list').append($('<tr>')
                            .append($('<td>',{text:val.offer_name}))
                            .append($('<td>',{text:val.offer_view+' Views'}))
                            .append($('<td>').append($('<a>',{text:' Edit', href:'/offer/edit/'+val.id}))));
                    });
                    $('#lowest-view-list').html('');
                    $.each(lowest,function (i,val) {
                        $('#lowest-view-list').append($('<tr>')
                            .append($('<td>',{text:val.offer_name}))
                            .append($('<td>',{text:val.offer_view+' Views'}))
                            .append($('<td>').append($('<a>',{text:' Edit', href:'/offer/edit/'+val.id}))));
                    });
                }

                function fillConversionTable(data) {
                    let lowest = data.lowestConversion;
                    let highest = data.highestConversion;
                    $('#highest-conversion-list').html('');
                    $.each(highest,function (i,val) {
                        $('#highest-conversion-list').append($('<tr>')
                            .append($('<td>',{text:val.offer_name}))
                            .append($('<td>',{text:val.amount + '(' +val.conversion+' %)'})));
                    });
                    $('#lowest-conversion-list').html('');
                    $.each(lowest,function (i,val) {
                        $('#lowest-conversion-list').append($('<tr>')
                            .append($('<td>',{text:val.offer_name}))
                            .append($('<td>',{text:val.amount + '(' +val.conversion+' %)'})));
                    });
                }

                function cb(start, end) {
                    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                    fillter(start.format('DD-MM-YYYY'),end.format('DD-MM-YYYY'));
                }
                function fillter(start,end) {
                    $.ajax({
                        url: 'report/getGeneral',
                        type: 'GET',
                        data: {
                            start: start,
                            end: end
                        },
                        dataType: 'json',
                        success: function (response) {
                            $('#total-view').text(response.view);
                            $('#total-added-cart').text(response.added_cart);
                            $('#total-conversion').text(response.conversion);
                            $('#total-revenue').text(response.revenue);
                            fillViewTable(response);
                            fillConversionTable(response);
                        }
                    });
                }
                $('#reportrange').daterangepicker({
                    startDate: start,
                    endDate: end,
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    }
                }, cb);
                cb(start, end);
            });
        </script>
    @stop
