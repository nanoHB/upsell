@extends('layouts.master')
@section('content')

<section class="content container-fluid">
    <div class="content-header">
        <div class="row align-items-center">
            <div class="col-md-7">
                <h2>Reports</h2>
            </div>
        </div>
    </div>

    <div class="box box-list">
        <div class="box-body">
            <div id="report-offer-content">
            </div>
        </div>
    </div>
    <div class="report-product-content">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#product-added-cart">Number added to cart</a></li>
                <li><a data-toggle="tab" href="#amount-added-cart">Amount added to cart</a></li>
                <li><a data-toggle="tab" href="#product-purchased">Number purchased</a></li>
                <li><a data-toggle="tab" href="#amount-purchased">Amount purchased</a></li>
            </ul>
            <div class="tab-content">
                <div id="product-added-cart" class="tab-pane fade in active">
                    <div class="cavas_report">
                        <canvas id="added-chart" style="height: 265px; width: 530px;" width="530" height="265"></canvas>
                    </div>
                </div>
                <div id="amount-added-cart" class="tab-pane fade">
                    <div class="cavas_report">
                        <canvas id="added-amount-chart" style="height: 265px; width: 530px;" width="530"
                                height="265"></canvas>
                    </div>
                </div>
                <div id="product-purchased" class="tab-pane fade">
                    <div class="cavas_report">
                        <canvas id="purchase-chart" style="height: 265px; width: 530px;" width="530" height="265"></canvas>
                    </div>
                </div>
                <div id="amount-purchased" class="tab-pane fade">
                    <div class="cavas_report">
                        <canvas id="purchase-amount-chart" style="height: 265px; width: 530px;" width="530"
                                height="265"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>    
@endsection
@section('page-script')
    <script type="text/javascript">
        var addedChartData = null;
        var amountChartData = null;
        var purchaseChartData = null;
        var amountPurchaseChartData = null;

        function getTable(page) {
            let pageParam = '';
            if (typeof(page) != 'undefined') pageParam = '?page=' + page;
            $.ajax({
                url: '/report/getTable' + pageParam,
                type: 'GET',
                success: function (response) {
                    $('#report-offer-content').html(response.view);
                }
            });
        }

        $(function () {
            getTable();
            $('#report-offer-content').on('click', 'a.page-link', function (e) {
                e.preventDefault();
                let page = $(this).text();
                getTable(page);
            });
            //chart option
            let pieOptions = {
                //Boolean - Whether we should show a stroke on each segment
                segmentShowStroke: true,
                //String - The colour of each segment stroke
                segmentStrokeColor: '#fff',
                //Number - The width of each segment stroke
                segmentStrokeWidth: 2,
                //Number - The percentage of the chart that we cut out of the middle
                percentageInnerCutout: 50, // This is 0 for Pie charts
                //Number - Amount of animation steps
                animationSteps: 100,
                //String - Animation easing effect
                animationEasing: 'easeOutBounce',
                //Boolean - Whether we animate the rotation of the Doughnut
                animateRotate: true,
                //Boolean - Whether we animate scaling the Doughnut from the centre
                animateScale: false,
                //Boolean - whether to make the chart responsive to window resizing
                responsive: true,
                // Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
                maintainAspectRatio: true,
                //String - A legend template
                {{--legendTemplate       : '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<segments.length; i++){%><li><span style="background-color:<%=segments[i].fillColor%>"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>'--}}
            };
            $.ajax({
                url: '/report/chartData',
                type: 'GET',
                dataType: 'json',
                success: function (response) {
                    addedChartData = response.product_view;
                    amountChartData = response.product_amount;
                    purchaseChartData = response.purchase_time;
                    amountPurchaseChartData = response.purchase_amount;
                    var addedChartCanvas = $('#added-chart').get(0).getContext('2d');
                    var addedChart = new Chart(addedChartCanvas);
                    addedChart.Doughnut(addedChartData, pieOptions);
                }
            });
            $('.nav-tabs a').on('shown.bs.tab', function (e) {
                var selector = $(e.target).attr("href") + ' canvas';
                var pieChartCanvas = $(selector).get(0).getContext('2d'); // activated tab
                var pieChart = new Chart(pieChartCanvas);
                var pieData = null;
                var selector_id = $(selector).attr('id');
                switch (selector_id) {
                    case 'added-chart':
                        pieData = addedChartData;
                        break;
                    case 'added-amount-chart':
                        pieData = amountChartData;
                        break;
                    case 'purchase-chart':
                        pieData = purchaseChartData;
                        break;
                    case 'purchase-amount-chart':
                        pieData = amountPurchaseChartData;
                        break
                }
                pieChart.Doughnut(pieData, pieOptions);

            });
        });
    </script>
@endsection