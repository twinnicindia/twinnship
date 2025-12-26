<div class="card card-shadow">
    <div class="card-body bg-white">
        <div class="card-title mb-md-4">
            <h5 class="title">State Wise Split</h5>
        </div>
        <!-- change this to indiaMap to display india map again -->
        <div id="indiaMap" style="height: 350px;"></div>
    </div>
</div>

<script>
    anychart.onDocumentReady(function() {
        // create map
        var map = anychart.map();

        // create data set
        var dataSet = anychart.data.set(
            <?= json_encode($mapData) ?>
        );

        // create choropleth series
        series = map.choropleth(dataSet);
        series.geoIdField('id');
        series.colorScale(anychart.scales.linearColor('#deebf7', '#3182bd'));
        series.hovered().fill('#073D59');
        map.geoData(anychart.maps['india']);
        map.interactivity().zoomOnMouseWheel(true);
        // Disables zoom on double click
        map.interactivity().keyboardZoomAndMove(true);
        // Disables zoom on double click
        map.interactivity().zoomOnDoubleClick(true);
        map.container('indiaMap');

        var zoomController = anychart.ui.zoom();
        zoomController.target(map);
        map.listen('pointClick', function(e) {
            map.zoomToFeature(e.point.get('id'));
        })
        zoomController.render();
        map.draw();
    });
</script>
