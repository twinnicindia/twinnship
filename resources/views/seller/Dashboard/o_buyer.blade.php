<div class="card card-shadow">
    <div class="card-body">
        <div class="card-title mb-md-4">
            <h5 class="title">Buyer Demographics</h5>
        </div>
        <div id="BuyerDemographicsChart" style="height: 320px;"></div>
    </div>
</div>

<script>
    //CodvsPrepaidChart
    anychart.onDocumentReady(function() {
        var data = [{
            x: "Male",
            value: 6
        },
            {
                x: "Female",
                value: 2
            },
            {
                x: "Undetermined",
                value: 1
            }
        ];
        var chart = anychart.pie(data);
        chart.innerRadius("70%");
        chart.container("BuyerDemographicsChart");
        chart.draw();
    });
</script>
