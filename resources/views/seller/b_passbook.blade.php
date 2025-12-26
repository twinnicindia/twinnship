<div class="row">
    <div class="col-lg-2 col-4  mb-4">
        <div class="card border-0 bg-white rounded-10">
            <div class="card-body">
                <span class="mt-3">Current Usable Balance:</span> <span class="font-size">₹ 0</span>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-4  mb-4">
        <div class="card border-0 bg-white rounded-10">
            <div class="card-body">
                <span class="mt-3">Balance On Hold:</span> <span class="font-size">₹ 0</span>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-4  mb-4">
        <div class="card border-0 bg-white rounded-10">
            <div class="card-body">
                <span class="mt-3">Total Balance:</span> <span class="font-size">₹ 0</span>
            </div>
        </div>
    </div>
</div>
<div class="d-flex card-row p-2 justify-content-between align-items-center filterDiv">
    <div class="mb-3 mt-3 me-2">
        <div>
            <form class="src-form position-relative" >
                <input type="text" class="form-control" placeholder="Search here AWB...." style="border: 1px solid; color: black;" value="{{$filter['filterAWBList'] ?? ''}}" id="filterAWBList">
                <button type="button" class="src-btn position-absolute top-50 end-0 translate-middle-y bg-transparent p-0 border-0 applyFilterButton">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </button>
            </form>
        </div>
    </div>
    <div class="mb-3 mt-3">
        <button type="button" class=" p-2 btn btn btn-primary text-white fw-semibold me-2" data-bs-toggle="offcanvas" data-bs-target="#shipping_filter" aria-controls="shipping_filter">
            <i class="ri-filter-line me-2"></i>More Filter
        </button>
    </div>
    <div class="mb-3 mt-3">
        <button type="button" class="clearFilterButton p-2 btn btn btn-primary text-white fw-semibold me-2">
            <i class="ri-filter-off-line"></i>Clear Filter
        </button>
    </div>
    <div class="text-end d-flex flex-grow-1 justify-content-end align-items-center">
        <div class="icon transition me-5 mt-3">
            <div class="text ptext">
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn btn-primary text-white fw-semibold me-2 exportPassbookButton"  title="Export Data">
                        <i class="ri-upload-line" data-bs-toggle="tooltip" data-bs-placement="top" title="Export Data"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<table class="table">
    <thead class="sticky-header">
    <tr class="text-center rounded-10">
        <th style="width: 40px;">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value=""
                       id="selectAll">
            </div>
        </th>
        <th>Date</th>
        <th>AWB Number</th>
        <th>Courier Partner</th>
        <th>Credit</th>
        <th>Debit</th>
        <th>Balance</th>
        <th>Description</th>
    </tr>
    </thead>
    <tbody>
    @forelse($passbook as $p)
        <tr>
            <td><input type="checkbox" class="selectedCheck" value="{{$p->id}}"></td>
            <td>{{$p->datetime}}</td>
            <td>@if(!empty($p->awb_number))
                    {!! "<a href='".url('track-order/'.$p->awb_number."' target='_blank'>".$p->awb_number."</a>") !!}
                @elseif(!empty($p->awb_number1))
                    {!! "<a href='".url('track-order/'.$p->awb_number1."' target='_blank'>".$p->awb_number1."</a>") !!}
                @endif
            </td>
            <td>
                @if(!empty($p->courier_partner))
                    {{$PartnerName[$p->courier_partner] ?? "-"}}
                @else
                    {{$PartnerName[$p->courier_partner1] ?? "-"}}
                @endif
            </td>
            <td class="text-success">@if($p->type == 'c') {{$p->amount}} @else 0.00 @endif</td>
            <td class="text-danger">@if($p->type == 'd') {{$p->amount}} @else 0.00 @endif</td>
            <td>{{$p->balance}}</td>
            <td>{{$p->description}}</td>
        </tr>
    @empty
        <tr>
            <td colspan="11">No record found</td>
        </tr>
    @endforelse
    </tbody>
</table>
