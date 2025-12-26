<div class="card db-card-bg mb-3 mb-md-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="text-center">
                    <div class="count">0</div>
                    <p class="mb-0 text-muted">Misrouted Shipments</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="text-center">
                    <div class="count">{{$lost_order}}</div>
                    <p class="mb-0 text-muted">Lost Shipments</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="text-center">
                    <div class="count">{{$damaged_order}}</div>
                    <p class="mb-0 text-muted">Damaged Shipments</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="text-center">
                    <div class="count">0</div>
                    <p class="mb-0 text-muted">Destroyed Shipments</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-md-6">
        <div class="card db-card-bg mb-3 mb-md-4" style="height:242px; overflow:auto">
            <div class="card-body">
                <h6 class="mb-4">Pickup Pendency</h6>
                <h6 class="text-muted">No Data</h6>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card db-card-bg mb-3 mb-md-4" style="height:242px; overflow:auto">
            <div class="card-body">
                <h6 class="mb-4">NDR to Reattempt Delays</h6>
                <h6 class="text-muted">No Data</h6>
            </div>
        </div>
    </div>
</div>

<div class="row mt-2">
    <div class="col-md-4 col-sm-12">
        <div class="card card-shadow">
            <div class="card-body">
                <div class="card-title mb-md-4">
                    <h6 class="title">In Transit Delay</h6>
                    <p>No Data</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-12">
        <div class="card card-shadow">
            <div class="card-body">
                <div class="card-title mb-md-4">
                    <h6 class="title">RAD to Delivery Delay</h6>
                    <p>No Data</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-12">
        <div class="card card-shadow">
            <div class="card-body">
                <div class="card-title mb-md-4">
                    <h6 class="title">RTO Delay</h6>
                    <p>No Data</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    //NDR Reason Split Chart
    anychart.onDocumentReady(function() {
        var data = [{
                x: "Customer not at Home",
                value: 23
            },
            {
                x: "Customer Refused",
                value: 12
            }
        ];
        var chart = anychart.pie(data);
        chart.innerRadius("70%");
        chart.container("NDRReasonSplit");
        chart.draw();
    });

    
        //
        anychart.onDocumentReady(function () {
      // create data set on our data
      var dataSet = anychart.data.set([
        ['08 Jan -14 Jan', 12814, 3054, 4376, 4229],
        ['15 Jan - 21 Jan', 13012, 5067, 3987, 3932],
        ['22 Jan - 28 Jan', 11624, 7004, 3574, 5221],
        ['1 Feb - 07 Feb', 8814, 9054, 4376, 9256],
        ['08 Jan -14 Feb', 12998, 12043, 4572, 3308]
      ]);

      // map data for the first series, take x from the zero column and value from the first column of data set
      var firstSeriesData = dataSet.mapAs({ x: 0, value: 1 });

      // map data for the second series, take x from the zero column and value from the second column of data set
      var secondSeriesData = dataSet.mapAs({ x: 0, value: 2 });

      // map data for the second series, take x from the zero column and value from the third column of data set
      var thirdSeriesData = dataSet.mapAs({ x: 0, value: 3 });

      // map data for the fourth series, take x from the zero column and value from the fourth column of data set
      var fourthSeriesData = dataSet.mapAs({ x: 0, value: 4 });

      // create bar chart
      var chart = anychart.column();

      // turn on chart animation
      chart.animation(true);

      // force chart to stack values by Y scale.
      chart.yScale().stackMode('value');

      // set chart title text settings
      chart.title().padding([0, 0, 5, 0]);

      // helper function to setup label settings for all series
      var setupSeriesLabels = function (series, name) {
        series.name(name).stroke('3 #fff 1');
        series.hovered().stroke('3 #fff 1');
      };

      // temp variable to store series instance
      var series;

      // create first series with mapped data
      series = chart.column(firstSeriesData);
      setupSeriesLabels(series, 'Delivered');

      // create second series with mapped data
      series = chart.column(secondSeriesData);
      setupSeriesLabels(series, 'RTO');

      // create third series with mapped data
      series = chart.column(thirdSeriesData);
      setupSeriesLabels(series, 'Pending');

      // create fourth series with mapped data
      series = chart.column(fourthSeriesData);
      setupSeriesLabels(series, 'Lost/Demaged');

      // turn on legend
      chart.legend().enabled(true).fontSize(13).padding([0, 0, 20, 0]);
      // set yAxis labels formatter
      chart.yAxis().labels().format('{%Value}{groupsSeparator: }');

      // set titles for axes
    //   chart.xAxis().title('Products by Revenue');
      chart.yAxis().title('Revenue in Rupees');

      // set interactivity hover
      chart.interactivity().hoverMode('by-x');

      chart.tooltip().valuePrefix('$').displayMode('union');

      // set container id for the chart
      chart.container('NDRStatusChart');

      // initiate chart drawing
      chart.draw();
    });
</script>