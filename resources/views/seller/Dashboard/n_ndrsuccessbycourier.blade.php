<div class="card card-shadow">
    <div class="card-body">
        <div class="card-title mb-md-4">
            <h6 class="title">Success by Courier</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th style="width:190px;"></th>
                    @foreach($allPartners as $p)
                        <th>{{$PartnerName[$p] ?? ""}}</th>
                    @endforeach
                    <th>Other</th>
                    <th>Total</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>NDR Raised</td>
                    <?php $total=0; ?>
                    @foreach($allPartners as $p)
                        <td>{{$p_ndr_raised[$p] ?? 0}}</td>
                        <?php $total+=($p_ndr_raised[$p] ?? 0); ?>
                    @endforeach
                    <td>{{$p_ndr_raised['other'] ?? 0}}</td>
                    <?php $total+=($p_ndr_raised['other'] ?? 0); ?>
                    <td>{{$total}}</td>
                </tr>
                <tr>
                    <td>NDR Delivered</td>
                    <?php $total=0; ?>
                    @foreach($allPartners as $p)
                        <td>{{$p_ndr_delivered[$p] ?? 0}}</td>
                        <?php $total+=($p_ndr_delivered[$p] ?? 0); ?>
                    @endforeach
                    <td>{{$p_ndr_delivered['other'] ?? 0}}</td>
                    <?php $total+=($p_ndr_delivered['other'] ?? 0); ?>
                    <td>{{$total}}</td>
                </tr>

                </tbody>
            </table>
        </div>
    </div>
</div>
