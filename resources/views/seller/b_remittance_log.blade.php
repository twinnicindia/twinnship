<div class="row">
    <div class="col-lg-2 col-4  mb-4">
        <div class="card border-0 bg-white rounded-10">
            <div class="card-body">
                <span class="mt-3">Total COD:</span> <span class="font-size">₹ {{$cod_total ?? 0}}</span>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-4  mb-4">
        <div class="card border-0 bg-white rounded-10">
            <div class="card-body">
                <span class="mt-3">COD Remitted:</span> <span class="font-size">₹ {{round($remitted_cod ?? 0,2) ?? 0}}</span>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-4  mb-4">
        <div class="card border-0 bg-white rounded-10">
            <div class="card-body">
                <span class="mt-3">COD Pending:</span> <span class="font-size">₹ {{round(($cod_total ?? 0) - ($remitted_cod ?? 0) ?? 0,2) ?? 0}}</span>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-4  mb-4">
        <div class="card border-0 bg-white rounded-10">
            <div class="card-body">
                <span class="mt-3">Next Remittance Date:</span> <span class="font-size">{{isset($nextRemitDate) ? date("D, d M' y",strtotime($nextRemitDate)) : "2 May"}}</span>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-4  mb-4">
        <div class="card border-0 bg-white rounded-10">
            <div class="card-body">
                <span class="mt-3">Next Remit Amount:</span> <span class="font-size">₹ {{round($nextRemitCod ?? 0)}}</span>
            </div>
        </div>
    </div>
</div>
{{--<div class="d-flex card-row p-2 justify-content-between align-items-center filterDiv">--}}
{{--    <div class="mb-3 mt-3 me-2">--}}
{{--        <div>--}}
{{--            <form class="src-form position-relative" >--}}
{{--                <input type="text" class="form-control" placeholder="Search here AWB...." style="border: 1px solid; color: black;" id="filterAWBList">--}}
{{--                <button type="button"--}}
{{--                    class="src-btn position-absolute top-50 end-0 translate-middle-y bg-transparent p-0 border-0 applyFilterButton">--}}
{{--                    <i data-feather="search"></i>--}}
{{--                </button>--}}
{{--            </form>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--    <div class="mb-3 mt-3">--}}
{{--        <button type="button" class="clearFilterButton p-2 btn btn btn-primary text-white fw-semibold me-2">--}}
{{--            <i class="ri-filter-off-line"></i>Clear Filter--}}
{{--        </button>--}}
{{--    </div>--}}
{{--    <div class="mb-3 mt-3">--}}

{{--    </div>--}}
{{--    <div class="text-end d-flex flex-grow-1 justify-content-end align-items-center">--}}
{{--        <div class="icon transition me-5 mt-3">--}}
{{--            <div class="text ptext">--}}
{{--                <div class="d-flex justify-content-between">--}}
{{--                    <button type="button" class="btn btn btn-primary text-white fw-semibold me-2 exportOrderButton"  title="Export Data">--}}
{{--                        <i class="ri-upload-line" data-bs-toggle="tooltip" data-bs-placement="top" title="Export Data"></i>--}}
{{--                    </button>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
<table class="table">
    <thead class="sticky-header">
    <tr class="text-center rounded-10">
{{--        <th style="width: 40px;">--}}
{{--            <div class="form-check">--}}
{{--                <input class="form-check-input" type="checkbox" value=""--}}
{{--                       id="selectAll">--}}
{{--            </div>--}}
{{--        </th>--}}
        <th>Date</th>
        <th>CRF ID</th>
        <th>UTR</th>
        <th>Remit Mode</th>
        <th>Freight Charges from COD</th>
        <th>Early COD Charges</th>
        <th>RTO Reversal Amount</th>
        <th>Remmitance Amount</th>
        <th>Description</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    @forelse($remitance as $r)
        <tr>
{{--            <td><input type="checkbox" class="selectedCheck" value="{{$r->id}}"></td>--}}
            <td>{{$r->datetime}}</td>
            <td>{{$r->crf_id}}</td>
            <td>{{$r->utr_number}}</td>
            <td>{{$r->mode}}</td>
            <td> &#x20b9; 0.00</td>
            <td>&#x20b9; 0.00</td>
            <td> &#x20b9; 0.00</td>
            <td>{{$r->amount}}</td>
{{--            <td class="text-capitalize">{{$r->pay_type}}</td>--}}
            <td>{{$r->description}}</td>
            @if($r->remitted_by == 'admin')
                <td><a href='{{url("/export_admin_remittance/$r->id")}}'>
                        <button type="button" class="btn btn-primary btn-sm mx-0" data-placement="top" data-toggle="tooltip" data-original-title="Export CSV"><i class="fa fa-upload"></i></button>
                    </a>
                </td>
            @else
                <td>-</td>
            @endif
        </tr>
    @empty
        <tr>
            <td colspan="11" class="">No Records Found</td></td>
        </tr>
    @endforelse
    </tbody>
</table>
