<div class="card card-shadow">
    <div class="card-body">
        <div class="card-title mb-md-4">
            <h6 class="title">RTO Count</h6>
        </div>
        <div id="RTOCount" style="height: 320px;"></div>
    </div>
</div>

<script>
    //RTO Count Chart
    anychart.onDocumentReady(function() {
        // create data
        var data = [
            {
                x: "{{$five_week_date}}",
                value: {{$five_week}}
            },
            {
                x: "{{$four_week_date}}",
                value: {{$four_week}}
            },
            {
                x: "{{$three_week_date}}",
                value: {{$three_week}}
            },
            {
                x: "{{$two_week_date}}",
                value: {{$two_week}}
            },
            {
                x: "{{$this_week_date}}",
                value: {{$this_week}}
            }
        ];
        // create a chart
        chart = anychart.line();
        // create a spline series and set the data
        var series = chart.spline(data);
        // set the container id
        chart.container("RTOCount");

        // initiate drawing the chart
        chart.draw();
    });
</script>
