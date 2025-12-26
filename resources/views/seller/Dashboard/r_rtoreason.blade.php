<div class="card card-shadow">
    <div class="card-body">
        <div class="card-title mb-md-4">
            <h6 class="title">RTO Reasons</h6>
        </div>
        <div id="RTOResons" style="height: 320px;"></div>
    </div>
</div>
<script>
    //NDR Reason Split Chart
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
        chart.container("RTOResons");
        chart.draw();
    });
</script>
