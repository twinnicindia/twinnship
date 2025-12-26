<div class="card card-shadow">
    <div class="card-body">
        <div class="card-title mb-md-4">
            <h6 class="title">NDR Reason Split</h6>
        </div>
        <div id="NDRReasonSplit" style="height: 320px;"></div>
    </div>
</div>
<script>
    anychart.onDocumentReady(function() {
        var data = [
                @foreach($reason_split as $r)
            {
                x: "{{$r->reason}}",
                value: {{$r->total_reason}}
            },
            @endforeach
        ];
        var chart = anychart.pie(data);
        chart.innerRadius("70%");
        chart.container("NDRReasonSplit");
        chart.draw();
    });
</script>
