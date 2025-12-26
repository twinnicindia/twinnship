<div class="card card-shadow">
    <div class="card-body">
        <div class="card-title mb-md-4">
            <h5 class="title">Delivery Performance</h5>
        </div>
        <div id="DeliveryPerformanceChart" style="height: 320px;"></div>
    </div>
</div>

<script>
    //DeliveryPerformanceChart
    anychart.onDocumentReady(function() {
        var data = [{
            x: "On Time Deliveries",
            value: {{$ontime_delivery}}
        },
            {
                x: "Late Deliveries",
                value: {{$late_delivery}}
            }
        ];
        var chart = anychart.pie(data);
        chart.innerRadius("70%");
        chart.container("DeliveryPerformanceChart");
        chart.draw();
    });
</script>
