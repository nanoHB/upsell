@extends('layouts.master')
@section('content')
    <div class="offer-table">
        <table class="table table-bordered table-list-product table-hover dataTable">
            <thead>
                <th class="ls-name">Products name</th>
                <th class="ls-purchase">Purchase</th>
                <th class="ls-purchase-amount">Purchase Amount</th>
            </thead>
            <tbody>
                @foreach($productAddedData as $value)
                    <tr>
                        <td>
                            {{$value['name']}}
                        </td>
                        <td>
                            {{$value['time']}}
                        </td>
                        <td>
                            {{round($value['amount'],2)}}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="chart-group">
        <span>Number of views/Added to cart/Purchase</span>
        <div class="chart">
            <canvas id="timeChart" style="height: 250px; width: 510px;" width="510" height="250"></canvas>
        </div>
    </div>
    <div class="chart-group">
        <span>Amount purchase</span>
        <div class="chart">
            <canvas id="amountChart" style="height: 250px; width: 510px;" width="510" height="250"></canvas>
        </div>
    </div>
@endsection
@section('page-script')
    <script type="text/javascript">
        $(function () {
            var label = {!! json_encode($listMonth) !!};
            var data = {{json_encode($viewData)}};
            var purchaseData = {{json_encode($purchaseData)}};
            var amountData = {{json_encode($amountData)}};
            var addCartData = {!! json_encode($addCartData) !!};
            var timeChartData = {
                labels  : label,
                datasets: [
                    {
                        label               : 'Views',
                        fillColor           : 'rgba(60,141,188,0.9)',
                        strokeColor         : 'rgba(60,141,188,0.8)',
                        pointColor          : '#3b8bba',
                        pointStrokeColor    : 'rgba(60,141,188,1)',
                        pointHighlightFill  : '#fff',
                        pointHighlightStroke: 'rgba(60,141,188,1)',
                        data                : data
                    },
                    {
                        label               : 'Purchase',
                        fillColor           : 'rgba(210, 214, 222, 1)',
                        strokeColor         : 'rgba(255, 0, 0, 0.8)',
                        pointColor          : 'rgba(210, 214, 222, 1)',
                        pointStrokeColor    : '#c1c7d1',
                        pointHighlightFill  : '#fff',
                        pointHighlightStroke: 'rgba(220,220,220,1)',
                        data                : purchaseData
                    },
                    {
                        label               : 'Added data',
                        fillColor           : 'rgba(210, 214, 222, 1)',
                        strokeColor         : 'rgba(60, 242, 24, 1)',
                        pointColor          : 'rgba(210, 214, 222, 1)',
                        pointStrokeColor    : '#c1c7d1',
                        pointHighlightFill  : '#fff',
                        pointHighlightStroke: 'rgba(220,220,220,1)',
                        data                : addCartData
                    }
                ]
            };
            var amountChartData = {
                labels  : label,
                datasets: [
                    {
                        label               : 'Purchase',
                        fillColor           : 'rgba(210, 214, 222, 1)',
                        strokeColor         : 'rgba(255, 0, 0, 0.8)',
                        pointColor          : 'rgba(210, 214, 222, 1)',
                        pointStrokeColor    : '#c1c7d1',
                        pointHighlightFill  : '#fff',
                        pointHighlightStroke: 'rgba(220,220,220,1)',
                        data                : amountData
                    }
                ]
            };
            var areaChartOptions = {
                //Boolean - If we should show the scale at all
                showScale               : true,
                //Boolean - Whether grid lines are shown across the chart
                scaleShowGridLines      : false,
                //String - Colour of the grid lines
                scaleGridLineColor      : 'rgba(0,0,0,.05)',
                //Number - Width of the grid lines
                scaleGridLineWidth      : 1,
                //Boolean - Whether to show horizontal lines (except X axis)
                scaleShowHorizontalLines: true,
                //Boolean - Whether to show vertical lines (except Y axis)
                scaleShowVerticalLines  : true,
                //Boolean - Whether the line is curved between points
                bezierCurve             : true,
                //Number - Tension of the bezier curve between points
                bezierCurveTension      : 0.3,
                //Boolean - Whether to show a dot for each point
                pointDot                : false,
                //Number - Radius of each point dot in pixels
                pointDotRadius          : 4,
                //Number - Pixel width of point dot stroke
                pointDotStrokeWidth     : 1,
                //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
                pointHitDetectionRadius : 20,
                //Boolean - Whether to show a stroke for datasets
                datasetStroke           : true,
                //Number - Pixel width of dataset stroke
                datasetStrokeWidth      : 2,
                //Boolean - Whether to fill the dataset with a color
                datasetFill             : true,
                  //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
                  maintainAspectRatio     : true,
                  //Boolean - whether to make the chart responsive to window resizing
                  responsive              : true,
                {{--legendTemplate       : '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<segments.length; i++){%><li><span style="background-color:<%=segments[i].fillColor%>"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>'--}}
                }
            //-------------
            //- LINE CHART -
            //--------------
            var timeChartCanvas          = $('#timeChart').get(0).getContext('2d');
            var timeChart                = new Chart(timeChartCanvas);
            var amountChartCanvas          = $('#amountChart').get(0).getContext('2d');
            var amountChart                = new Chart(amountChartCanvas);
            var lineChartOptions         = areaChartOptions;
            lineChartOptions.datasetFill = false;
            timeChart.Line(timeChartData, lineChartOptions);
            amountChart.Line(amountChartData, lineChartOptions)
        });
    </script>
@stop
