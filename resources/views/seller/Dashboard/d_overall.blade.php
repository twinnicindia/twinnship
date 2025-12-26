<div class="card card-shadow">
    <div class="card-body">
        <div class="card-title mb-md-4">
            <h5 class="title">Overall Shipment Status</h5>
        </div>
        <div id="ShipmentStatusChart" style="height: 320px;"></div>
    </div>
</div>

<script>
    //ShipmentStatusChart
    anychart.onDocumentReady(function() {
        var data = [
            {
                x: "Delivered",
                value: {{$delivered}}
            },
            {
                x: "Undelivered",
                value: {{$undelivered}}
            },
            {
                x: "Intransit",
                value: {{$intransit}}
            },
            {
                x: "RTO",
                value: {{$rto}}
            },
            {
                x: "Lost/Damaged",
                value: {{$damaged}}
            }
        ];
        var chart = anychart.pie(data);
        chart.innerRadius("70%");
        chart.container("ShipmentStatusChart");
        chart.draw();
    });
</script>
