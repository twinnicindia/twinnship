<table class="table">
    <thead class="sticky-header">
    <tr class="text-center rounded-10">
        <th style="width: 40px;">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="selectAllCheckBox">
            </div>
        </th>
        <th class="text-center">Tracking Details</th>
        <th class="text-center">Shipment Details</th>
        <th class="text-center">Status</th>
        <th class="text-center">Dimensional Details</th>
        <th class="text-center">Package Details</th>
        <th class="text-center">Customer Details</th>
        <th class="text-center">Pickup Address</th>
        <th class="text-center">Action</th>
    </tr>
    </thead>
    <tbody>

    @foreach($order as $o)
        <tr class=" rounded-10 card-row" id="row{{$o->id}}">
            <td>
                <div class="form-check">
                    <input class="form-check-input selectCheckBoxes" type="checkbox" value="{{$o->id}}" id="orderCheckbox-{{$o->id}}">
                    <label class="form-check-label" for="orderCheckbox1"></label>
                </div>
            </td>
            <td>
                @if($o->awb_number != "")
                    <div class="cell-inside-box ">
                        <p><span class="d-inline-flex align-items-center gap-1 ms-2"><a href='{{route("web.track_order",$o->awb_number)}}' target="_blank">{{$o->awb_number}}</a></span></p>
                        <p><span class="d-inline-flex align-items-center gap-1 ms-2 ">{{$o->courier_partner}} </span></p>
                        <p><span class="d-inline-flex align-items-center gap-1 ms-2"> {{date('d M Y',strtotime($o->awb_assigned_date))}}</span></p>
                        <p><span class="d-inline-flex align-items-center gap-1 ms-2">({{date('H:i A',strtotime($o->awb_assigned_date))}})</span></p>
                    </div>
                @endif
            </td>
            <td class="">
                <div class="cell-inside-box ">
                    <p><span
                            class="d-inline-flex align-items-center gap-1 ms-2">Order ID: {{$o->customer_order_number}}</span>
                    </p>
                    <p><span class="d-inline-flex align-items-center gap-1 ms-2 ">{{$o->product_name}} </span></p>
                    <p><span class="d-inline-flex align-items-center gap-1 ms-2">(Quantity - {{$o->product_qty}})</span>
                    </p>
                    <p><span class="d-inline-flex align-items-center gap-1 ms-2">SKU - {{$o->product_sku}}</span></p>
                </div>
            </td>
            <td>
                <div class="cell-inside-box ">
                    <p><span class="d-inline-flex align-items-center gap-1 ms-2 order-Status-box">{{$statusList[$o->status] ?? "NA"}}</span></p>
                </div>
            </td>
            <td>
                <div class="cell-inside-box ">
                    <p><span class="d-inline-flex align-items-center gap-1 ms-2">Dimension - {{$o->length}} * {{$o->breadth}} * {{$o->height}}</span>
                    </p>
                    <p><span class="d-inline-flex align-items-center gap-1 ms-2 ">Weight - {{round($o->weight / 1000,2)}} KG</span></p>
                    <p><span class="d-inline-flex align-items-center gap-1 ms-2">Vol. Wt-{{round(($o->vol_weight / 1000),2)}} KG</span></p>
                </div>
            </td>
            <td>
                <div class="cell-inside-box ">
                    <p><span class="d-inline-flex align-items-center gap-1 ms-2">{{$o->invoice_amount}}</span></p>
                    <p><span
                            class="d-inline-flex align-items-center gap-1 ms-2 fw-semibold text-success">{{ucfirst($o->order_type)}}</span>
                    </p>
                </div>
            </td>
            <td>
                Name : {{$o->s_customer_name}}<br>
                Contact : {{$o->s_contact}}
                <a href="javascript:" class=" mx-0" data-placement="top" data-html="true" data-toggle="tooltip" data-original-title="{{$o->s_address_line1}} <br> {{$o->s_address_line2}} <br> {{$o->s_city}} {{$o->s_state}} {{$o->s_pincode}}"><i class="fas fa-eye text-primary"></i></a>
            </td>

            <td>{{ \Illuminate\Support\Str::limit($o->p_warehouse_name, 15, '...') }}
                <a href="javascript:" class=" mx-0" data-placement="top" data-html="true" data-toggle="tooltip" data-original-title="{{$o->p_address_line1}} <br> {{$o->p_address_line2}} <br> {{$o->p_city}} {{$o->p_state}} {{$o->p_pincode}}"><i class="fas fa-eye text-primary"></i></a>
            </td>
            <td>
                <div class="d-flex align-items-center gap-1">
                    @if($o->ndr_action == 'pending' && $o->status != "delivered" && $o->status != "rto")
                        <a data-id="{{$o->id}}" type="button" class="btn btn-sm btn-primary fw-semibold reattempt_btn" data-status="{{$o->status}}" data-bs-toggle="tooltip"
                           data-bs-placement="top" title="Reattempt Order">
                           <i class="ri-restart-line"></i>
                        </a>
                        <a data-id="{{$o->id}}" type="button" class="btn btn-sm btn-primary fw-semibold rto_btn" data-status="{{$o->status}}" data-bs-toggle="tooltip"
                           data-bs-placement="top" title="RTO Order">
                           <i class="ri-arrow-go-back-line"></i>
                        </a>
                    @endif
                    @if($o->ndr_action == 'requested' && $o->status != "delivered" && $o->status != "rto")
                        <a data-id="{{$o->id}}" type="button" class="btn btn-sm btn-primary fw-semibold " data-status="{{$o->status}}" data-bs-toggle="tooltip"
                           data-bs-placement="top" title="RTO Order">
                           <i class="ri-arrow-go-back-line"></i>
                        </a>
                    @endif
                </div>
            </td>
        </tr>
    @endforeach

    </tbody>
</table>
