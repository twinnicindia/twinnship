<div class="card card-shadow">
    <div class="card-body">
        <div class="card-title mb-md-4">
            <h5 class="title">Shipment - Zone Distribution</h5>
        </div>
        <div id="ZoneDistributionChart" style="height: 350px;"></div>
    </div>
</div>

<script>
    //ZoneDistributionChart
    anychart.onDocumentReady(function() {
        var data = [{
            x: "Zone A",
            value: {{$zone_a}}
        },
            {
                x: "Zone B",
                value: {{$zone_b}}
            },
            {
                x: "Zone c",
                value: {{$zone_c}}
            },
            {
                x: "Zone D",
                value: {{$zone_d}}
            },
            {
                x: "Zone E",
                value: {{$zone_e}}
            },
        ];
        var chart = anychart.pie(data);
        chart.innerRadius("70%");
        chart.container("ZoneDistributionChart");
        chart.draw();
    });
</script>
