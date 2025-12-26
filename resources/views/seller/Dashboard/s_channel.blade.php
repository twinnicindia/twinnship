<div class="card card-shadow">
    <div class="card-body">
        <div class="card-title mb-md-4">
            <h6 class="title">Shipment's Channel</h6>
        </div>
        <div class="table-responsive h-300" style="min-height: 327px;white-space: nowrap;">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Channels</th>
                    <th>Orders</th>
                </tr>
                </thead>
                <tbody>
                @forelse($shipment_channel as $c)
                    <tr>
                        <td class="text-capitalize">{{$c->channel}}</td>
                        <td>{{$c->total_order}}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">No Data Found</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
