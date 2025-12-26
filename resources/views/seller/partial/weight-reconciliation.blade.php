<table class="table">
    <thead class="sticky-header">
    <tr class="text-center rounded-10">
        <th style="width: 40px;">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value=""
                       id="selectAll">
            </div>
        </th>
        <th>Order Details</th>
        <th>Channel</th>
        <th>Product Details</th>
        <th>Order Total	</th>
        <th>Shipping details</th>
        <th>Entered Weight & Dimensions(CM)</th>
        <th>Charged Weight & Dimensions (CM)</th>
        <th>Settled Weight & Dimensions (CM)</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    @forelse($weight_reconciliation as $w)
        <tr>
            <td>
                <input type="checkbox" class="selectedCheck" data-status="{{$w->status}}" value="{{$w->w_id}}">
            </td>
            <td><a href='{{url("/view-order/$w->id")}}' target="_blank">{{$w->customer_order_number}}</a><br>
                {{$w->created}}
            </td>
            <td>{{$w->channel}}</td>
            <td>Name : @foreach(explode(',', $w->product_name) as $name)
                    {{$name}}<br>
                @endforeach
                SKU : @foreach(explode(',', $w->product_sku) as $sku)
                    {{$sku}}<br>
                @endforeach
            </td>
            <td>{{$w->invoice_amount}}</td>
            <td><span class="text-primary">AWB : {{$w->awb_number}} </span><br>
                Courier : {{$PartnerName[$w->courier_partner]}}</td>
            <td>Wt : {{$w->e_weight}} KG<br>
                (L * B * H) : {{$w->e_length}} * {{$w->e_breadth}} * {{$w->e_height}}<br>
                Applied Amount : {{$w->applied_amount}}
            </td>
            @if(isset($w->c_weight))
                <td>Wt : {{$w->c_weight}} KG<br>
                    (L * B * H) : {{$w->c_length}} * {{$w->c_breadth}} * {{$w->c_height}}<br>
                    Charged Amount : {{$w->charged_amount}}
                </td>
            @else
                <td>-</td>
            @endif
            @if(isset($w->s_weight))
                <td>Wt : {{$w->s_weight}} KG<br>
                    (L * B * H) : {{$w->s_length}} * {{$w->s_breadth}} * {{$w->s_height}}<br>
                    Settled Amount : {{$w->settled_amount}}
                </td>
            @else
                <td>-</td>
            @endif
            <td>{{$w->w_status}}</td>
            <td>
                @if($w->w_status == 'pending')
                    <button type="button" class="btn btn-primary AcceptButton btn-sm mx-0" data-id="{{$w->w_id}}" data-placement="top" data-toggle="tooltip" data-original-title="Accept"><i class="fas fa-check-square"></i></button>
                    <button type="button" class="btn btn-danger DisputeButton btn-sm mx-0" data-id="{{$w->w_id}}" data-placement="top" data-toggle="tooltip" data-original-title="Dispute"> <i class="fas fa-times"></i></button>
                        <?php
                        $seller_reconciliation_days= Session()->get('MySeller')->reconciliation_days;
                        $date = date('Y-m-d', strtotime($w->created));
                        $now = time(); // or your date as well
                        $your_date = strtotime($date);
                        $datediff = $now - $your_date;
                        $diffrence= round($datediff / (60 * 60 * 24)) - 1;
                        ?>
                    <p class="text-danger">{{$seller_reconciliation_days - $diffrence}} Working Days Remaining</p>
                @else
                    <a data-id="{{$w->w_id}}" class="mx-0 ViewHistory" style="cursor:pointer;">View History</a>
                @endif
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="10">No Billing Details found</td>
        </tr>
    @endforelse
    </tbody>
</table>
