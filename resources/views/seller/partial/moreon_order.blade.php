<table class="table">
    <thead class="sticky-header">
    <tr class="text-center rounded-10">
        <th style="width: 40px;">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="selectAllCheckBox">
            </div>
        </th>
        @if(strtolower($selected_tab) != 'split' || strtolower($selected_tab) != 'merge')
        <th class="text-center">Tracking Details</th>
        @endif
        <th class="text-center">Shipment Details</th>
        <th class="text-center">Status</th>
        <th class="text-center">Dimensional Details</th>
        <th class="text-center">Shipment Amount</th>
        <th class="text-center">Customer Details</th>
        <th class="text-center">Action</th>
    </tr>
    </thead>
    <tbody>

    @foreach($order as $o)
        <tr class="text-center rounded-10 card-row">
            <td>
                <div class="form-check">
                    <input class="form-check-input selectCheckBoxes" type="checkbox" value="{{$o->id}}" id="orderCheckbox-{{$o->id}}">
                    <label class="form-check-label" for="orderCheckbox1"></label>
                </div>
            </td>
            @if(strtolower($selected_tab) != 'split' || strtolower($selected_tab) != 'merge')
            <td>
                @if($o->awb_number != "")
                    <div class="cell-inside-box ">
                        <p><span class="d-inline-flex align-items-center gap-1 ms-2">{{$o->awb_number}}</span></p>
                        <p><span class="d-inline-flex align-items-center gap-1 ms-2 ">{{$o->courier_partner}} </span></p>
                        <p><span class="d-inline-flex align-items-center gap-1 ms-2"> {{date('d M Y',strtotime($o->awb_assigned_date))}}</span></p>
                        <p><span class="d-inline-flex align-items-center gap-1 ms-2">({{date('H:i A',strtotime($o->awb_assigned_date))}})</span></p>
                    </div>
                @endif
            </td>
            @endif
            <td class="text-center">
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
                    <p><span class="d-inline-flex align-items-center gap-1 ms-2 order-Status-box">pending</span></p>
                </div>
            </td>
            <td>
                <div class="cell-inside-box ">
                    <p><span class="d-inline-flex align-items-center gap-1 ms-2">Dimension - {{$o->length}} * {{$o->breadth}} * {{$o->height}}</span>
                    </p>
                    <p><span class="d-inline-flex align-items-center gap-1 ms-2 ">Weight - {{$o->weight}} KG</span></p>
                    <p><span class="d-inline-flex align-items-center gap-1 ms-2">Vol. Wt-0.20 KG</span></p>
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
                <div class="cell-inside-box ">
                    <p><span class="d-inline-flex align-items-center gap-1 ms-2">{{$o->s_customer_name}}</span></p>
                    <p><span
                            class="d-inline-flex align-items-center gap-1 ms-2 ">{{$o->s_address_line1}}, {{$o->s_address_line2}}</span>
                    </p>
                    <p><span class="d-inline-flex align-items-center gap-1 ms-2 ">{{$o->s_city}}, {{$o->s_state}}</span>
                    </p>
                    <p><span class="d-inline-flex align-items-center gap-1 ms-2 ">{{$o->s_pincode}}</span></p>
                </div>
            </td>

            <td>
                <div class="d-flex align-items-center gap-1">
                    @if($o->status == 'pending')
                        <a data-id="{{$o->id}}" type="button" class="btn btn-sm btn-warning fw-semibold shipOrderButton" data-status="{{$o->status}}" data-bs-toggle="tooltip"
                           data-bs-placement="top" title="Ship Order">
                            <i class="ri-truck-line"></i>
                        </a>
                        <a type="button" class="btn btn-sm btn-primary fw-semibold" data-bs-toggle="tooltip"
                           data-bs-placement="top" title="Edit Order">
                            <i class="ri-pencil-line"></i>
                        </a>
                    @endif
                    @if($o->status != 'pending' && $o->status != 'cancelled' && $o->status != 'delivered' )
                        <a type="button" class="btn btn-sm btn-primary fw-semibold" data-bs-toggle="tooltip"
                           data-bs-placement="top" title="Download Invoice">
                            <i class="ri-download-line"></i>
                        </a>
                        <a type="button" class="btn btn-sm btn-primary fw-semibold" data-bs-toggle="tooltip"
                           data-bs-placement="top" title="Print Label">
                            <i class="ri-printer-line"></i>
                        </a>
                        <a type="button" class="btn btn-sm btn-primary fw-semibold" data-bs-toggle="tooltip"
                           data-bs-placement="top" title="Reverse Order">
                            <i class="ri-arrow-go-back-line"></i>
                        </a>
                    @endif
                    <!-- <a type="button" class="btn btn-sm btn-primary fw-semibold" data-bs-toggle="tooltip"
                       data-bs-placement="top" title="Clone Order">
                        <i class="ri-file-copy-fill"></i>
                    </a> -->
                </div>
            </td>
        </tr>
    @endforeach

    </tbody>
</table>
