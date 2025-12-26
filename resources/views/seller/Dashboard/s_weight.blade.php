<div class="card card-shadow">
    <div class="card-body">
        <div class="card-title mb-md-4">
            <h5 class="title">Weight Profile in Kgs</h5>
        </div>
        <div id="WeightProfile" style="height: 320px;"></div>
    </div>
</div>

<script>
    anychart.onDocumentReady(function() {

        var data = [{
            x: "0.5 Kgs",
            value: {{$half_kgs}}
        },
            {
                x: "0.5 - 1 Kgs",
                value: {{$one_kgs}}
            },
            {
                x: "1 - 1.5 Kgs",
                value: {{$one_half_kgs}}
            },
            {
                x: "1.5 - 2 Kgs",
                value: {{$two_kgs}}
            },
            {
                x: "2 - 5 Kgs",
                value: {{$five_kgs}}
            },
            {
                x: "5 kgs+",
                value: {{$five_kgs_plus}}
            },
        ];
        var chart = anychart.pie(data);
        chart.innerRadius("70%");
        chart.container("WeightProfile");
        chart.draw();
    });
</script>
