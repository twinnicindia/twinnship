<div class="card card-shadow">
    <div class="card-body">
        <div class="card-title mb-md-4">
            <h6 class="title">RTO Status</h6>
        </div>
        <div id="RTOStatus" style="height: 320px;"></div>
    </div>
</div>
<script>
        @php
            $index=[
                'pending' => 0,
                'rto' => 1,
                'delivered' => 2,
                'lost' => 3,
                'damaged' => 4
            ]
        @endphp
    var this_week=[0,0,0];
    var two_week=[0,0,0];
    var three_week=[0,0,0];
    var four_week=[0,0,0];
    var five_week=[0,0,0];
    @php($cnt=0)
        @foreach($this_week_status as $w)
        this_week[{{$index[$w->rto_status]}}]='{{$w->total_order}}';
    @endforeach
        @foreach($two_week_status as $w)
        two_week[{{$index[$w->rto_status]}}]='{{$w->total_order}}';
    @endforeach
        @foreach($three_week_status as $w)
        three_week[{{$index[$w->rto_status]}}]='{{$w->total_order}}';
    @endforeach
        @foreach($four_week_status as $w)
        four_week[{{$index[$w->rto_status]}}]='{{$w->total_order}}';
    @endforeach
        @foreach($five_week_status as $w)
        five_week[{{$index[$w->rto_status]}}]='{{$w->total_order}}';
    @endforeach

    anychart.onDocumentReady(function () {
        // create data set on our data
        var dataSet = anychart.data.set([
            ["{{$five_week_date}}", five_week[0], five_week[1], five_week[2]],
            ["{{$four_week_date}}", four_week[0], four_week[1], four_week[2]],
            ["{{$three_week_date}}", three_week[0], three_week[1], three_week[2]],
            ["{{$two_week_date}}", two_week[0], two_week[1], two_week[2]],
            ["{{$this_week_date}}", this_week[0], this_week[1], this_week[2]]
        ]);

        var firstSeriesData = dataSet.mapAs({ x: 0, value: 1 });
        var secondSeriesData = dataSet.mapAs({ x: 0, value: 2 });
        var thirdSeriesData = dataSet.mapAs({ x: 0, value: 3 });
        var chart = anychart.column();
        chart.animation(true);
        chart.yScale().stackMode('value');
        chart.title().padding([0, 0, 5, 0]);
        var setupSeriesLabels = function (series, name) {
            series.name(name).stroke('3 #fff 1');
            series.hovered().stroke('3 #fff 1');
        };
        var series;
        series = chart.column(firstSeriesData);
        setupSeriesLabels(series, 'Initiated');
        series = chart.column(secondSeriesData);
        setupSeriesLabels(series, 'Delivered');
        series = chart.column(thirdSeriesData);
        setupSeriesLabels(series, 'In Transit');
        chart.legend().enabled(true).fontSize(13).padding([0, 0, 20, 0]);
        chart.yAxis().labels().format('{%Value}{groupsSeparator: }');
        chart.yAxis().title('RTO Status');
        chart.interactivity().hoverMode('by-x');
        chart.tooltip().displayMode('union');
        chart.container('RTOStatus');
        chart.draw();
    });
</script>
