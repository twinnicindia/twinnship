<table class="table">
    <thead class="sticky-header">
    <tr class="text-center rounded-10">
        <th style="width: 40px;">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="selectAllCheckBox">
            </div>
        </th>
        @if(strtolower($selected_tab) != 'processing')
        <th class="text-center">Tracking Details</th>
        @endif
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
                    <input class="form-check-input selectCheckBoxes" type="checkbox" value="{{$o->id}}" id="orderCheckbox-{{$o->id}}" data-status="{{$o->status}}">
                    <label class="form-check-label" for="orderCheckbox1"></label>
                </div>
            </td>
            @if(strtolower($selected_tab) != 'processing')
            <td>
                @if($o->awb_number != "")
                    <div class="cell-inside-box ">
                        <p><span class="d-inline-flex align-items-center gap-1 ms-2"><a href='{{route("web.track_order",$o->awb_number)}}' target="_blank">{{$o->awb_number}}</a></span></p>
                        <p><span class="d-inline-flex align-items-center gap-1 ms-2 ">{{$PartnerName[$o->courier_partner] ?? "NA"}} </span></p>
                        <p><span class="d-inline-flex align-items-center gap-1 ms-2"> {{date('d M Y',strtotime($o->awb_assigned_date))}}</span></p>
                        <p><span class="d-inline-flex align-items-center gap-1 ms-2">({{date('H:i A',strtotime($o->awb_assigned_date))}})</span></p>
                    </div>
                @endif
            </td>
            @endif
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
                    @if($o->status == 'delivered' )
                        <p><span class="d-inline-flex align-items-center gap-1 ms-2">{{date('d M Y',strtotime($o->delivered_date))}} ({{date('H:i A',strtotime($o->delivered_date))}})</span></p>
                    @endif
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
                    <p>
                        <span class="d-inline-flex align-items-center gap-1 ms-2 fw-semibold text-success">{{ucfirst($o->order_type)}}</span><br>
                        <span class="d-inline-flex align-items-center gap-1 ms-2 fw-semibold text-{{$o->o_type == "forward" ? "success" : "danger"}}">{{ucfirst($o->o_type ?? "")}}</span><br>
                        <span class="d-inline-flex align-items-center gap-1 ms-2 fw-semibold text-{{strtolower($o->shipment_type) == "mps" ? "info" : ""}}">{{ucfirst($o->shipment_type ?? "")}}</span>
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
                    @if($o->status == 'pending')
                        <a data-id="{{$o->id}}" type="button" class="btn btn-sm btn-warning fw-semibold shipOrderButton" data-status="{{$o->status}}" data-bs-toggle="tooltip"
                           data-bs-placement="top" title="Ship Order">
                            <i class="ri-truck-line"></i>
                        </a>
                        <a type="button" class="btn btn-sm btn-primary fw-semibold modify_data" data-id="{{$o->id}}" data-bs-toggle="tooltip"
                           data-bs-placement="top" title="Edit Order">
                            <i class="ri-pencil-line"></i>
                        </a>
                    @endif
                    @if($o->status != 'pending' && $o->status != 'cancelled' && $o->status != 'delivered' && $selected_tab != "cancelled" && $selected_tab != "returns")
                        <a type="button" href="{{("single_order/invoice/pdf/$o->id")}}" class="btn btn-sm btn-primary fw-semibold" data-bs-toggle="tooltip"
                           data-bs-placement="top" title="Download Invoice">
                            <i class="ri-download-line"></i>
                        </a>
                        <a type="button" target="_blank" href="{{("single_order/lable/pdf/$o->id")}}" class="btn btn-sm btn-primary fw-semibold" data-bs-toggle="tooltip"
                           data-bs-placement="top" title="Print Label">
                            <i class="ri-printer-line"></i>
                        </a>
                        <a type="button" class="btn btn-sm btn-primary fw-semibold reverse_data" data-number="{{$o->id}}" data-bs-toggle="tooltip"
                           data-bs-placement="top" title="Reverse Order">
                            <i class="ri-arrow-go-back-line"></i>
                        </a>
                    @endif
                    <a type="button" class="btn btn-sm btn-primary fw-semibold clone_data" data-number="{{$o->id}}" data-bs-toggle="tooltip"
                       data-bs-placement="top" title="Clone Order">
                        <i class="ri-file-copy-fill"></i>
                    </a>
                    @if($selected_tab == 'processing')
                        <a type="button" class="btn btn-sm btn-danger fw-semibold delete_data" data-number="{{$o->id}}" data-bs-toggle="tooltip"
                           data-bs-placement="top" title="Delete Order">
                            <i class="ri-delete-bin-fill"></i>
                        </a>
                    @endif
                    @if($selected_tab == 'ready_to_ship' || $selected_tab == 'manifest' || ($selected_tab == 'reverse' && $o->o_type == 'reverse' && $o->status != 'rto_delivered' && $o->status != 'cancelled'))
                        <a type="button" class="btn btn-sm btn-danger fw-semibold cancel_order" data-number="{{$o->id}}" data-bs-toggle="tooltip"
                           data-bs-placement="top" title="Cancel Order">
                            <i class="ri-close-fill"></i>
                        </a>
                    @endif
                </div>
            </td>
        </tr>
    @endforeach

    </tbody>
</table>
