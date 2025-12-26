<div class="card card-shadow">
    <div class="card-body">
        <div class="card-title mb-md-4">
            <h5 class="title">Prepaid vs. COD Orders</h5>
        </div>
        <div id="CodvsPrepaidChart" style="height: 320px;"></div>
    </div>
</div>

<script>
    //CodvsPrepaidChart
    anychart.onDocumentReady(function() {
        var data = [{
            x: "Cash On Delivery",
            value: "{{$cod_order}}"
        },
            {
                x: "Prepaid",
                value: "{{$prepaid_order}}"
            }
        ];
        var chart = anychart.pie(data);
        chart.innerRadius("70%");
        chart.container("CodvsPrepaidChart");
        chart.draw();
    });
</script>
