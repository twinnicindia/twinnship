<div class="card card-shadow">
    <div class="card-body">
        <div class="card-title mb-md-4">
            <h5 class="title">Courier Split</h5>
        </div>
        <div id="CourierSplitChart" style="height: 320px;"></div>
    </div>
</div>

<script>
    //Courier Split Chart
    anychart.onDocumentReady(function() {
        var data = [
                @foreach($courier_split as $c)
            {
                x: "{{$c->courier_partner!=''?$PartnerName[$c->courier_partner] :'-'}}",
                value: {{$c->total_order}}
            },
            @endforeach
        ];
        var chart = anychart.pie(data);
        chart.innerRadius("70%");
        chart.container("CourierSplitChart");
        chart.draw();
    });
</script>
