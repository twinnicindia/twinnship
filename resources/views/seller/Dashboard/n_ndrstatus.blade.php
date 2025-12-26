<div class="card card-shadow">
    <div class="card-body">
        <div class="card-title mb-md-4">
            <h6 class="title">NDR Status</h6>
        </div>
        <div id="NDRStatusChart" style="height: 320px;"></div>
    </div>
</div>
<script>
        @php
            $index=[
                'pending' => 0,
                'rto' => 1,
                'delivered' => 2,
                'reattempt' => 3,
                'lost' => 4
            ]
        @endphp
    var this_week=[0,0,0,0,0];
    var two_week=[0,0,0,0,0];
    var three_week=[0,0,0,0,0];
    var four_week=[0,0,0,0,0];
    var five_week=[0,0,0,0,0];
    @php($cnt=0)
        @foreach($this_week as $w)
        this_week[{{$index[$w->ndr_action]}}]='{{$w->total_order}}';
    @endforeach
        @foreach($two_week as $w)
        two_week[{{$index[$w->ndr_action]}}]='{{$w->total_order}}';
    @endforeach
        @foreach($three_week as $w)
        three_week[{{$index[$w->ndr_action]}}]='{{$w->total_order}}';
    @endforeach
        @foreach($four_week as $w)
        four_week[{{$index[$w->ndr_action]}}]='{{$w->total_order}}';
    @endforeach
        @foreach($five_week as $w)
        five_week[{{$index[$w->ndr_action]}}]='{{$w->total_order}}';
    @endforeach
    anychart.onDocumentReady(function () {
        var dataSet = anychart.data.set([
            ["{{$five_week_date}}", five_week[0], five_week[1], five_week[2], five_week[3],five_week[4]],
            ["{{$four_week_date}}", four_week[0], four_week[1], four_week[2], four_week[3],four_week[4]],
            ["{{$three_week_date}}", three_week[0], three_week[1], three_week[2], three_week[3],three_week[4]],
            ["{{$two_week_date}}", two_week[0], two_week[1], two_week[2], two_week[3],two_week[4]],
            ["{{$this_week_date}}", this_week[0], this_week[1], this_week[2], this_week[3],this_week[4]]
        ]);
        var firstSeriesData = dataSet.mapAs({ x: 0, value: 1 });
        var secondSeriesData = dataSet.mapAs({ x: 0, value: 2 });
        var thirdSeriesData = dataSet.mapAs({ x: 0, value: 3 });
        var fourthSeriesData = dataSet.mapAs({ x: 0, value: 4 });
        var fifthSeriesData = dataSet.mapAs({ x: 0, value: 5 });
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
        setupSeriesLabels(series, 'Delivered');
        series = chart.column(secondSeriesData);
        setupSeriesLabels(series, 'RTO');
        series = chart.column(thirdSeriesData);
        setupSeriesLabels(series, 'Pending');
        series = chart.column(fourthSeriesData);
        setupSeriesLabels(series, 'Reattempt');
        series = chart.column(fifthSeriesData);
        setupSeriesLabels(series, 'lost');
        chart.legend().enabled(true).fontSize(13).padding([0, 0, 20, 0]);
        chart.yAxis().labels().format('{%Value}{groupsSeparator: }');
        chart.yAxis().title('NDR Orders');
        chart.interactivity().hoverMode('by-x');
        chart.tooltip().displayMode('union');
        chart.container('NDRStatusChart');
        chart.draw();
    });
</script>
