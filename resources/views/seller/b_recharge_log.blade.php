<div class="row">
    <div class="col-lg-2 col-4  mb-4">
        <div class="card border-0 bg-white rounded-10">
            <div class="card-body">
                <span class="mt-3">Successful Recharge:</span> <span class="font-size">₹ {{$successfull_recharge ?? 0}}</span>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-4  mb-4">
        <div class="card border-0 bg-white rounded-10">
            <div class="card-body">
                <span class="mt-3">Total Credit:</span> <span class="font-size">₹ {{$total_credit ?? 0}}</span>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-4  mb-4">
        <div class="card border-0 bg-white rounded-10">
            <div class="card-body">
                <span class="mt-3">Total Debit:</span> <span class="font-size">₹ {{$total_debit ?? 0}}</span>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-4  mb-4">
        <div class="card border-0 bg-white rounded-10">
            <div class="card-body">
                <span class="mt-3">Cashback:</span> <span class="font-size">₹ 0</span>
            </div>
        </div>
    </div>
    <div class="col-lg-2 col-4  mb-4">
        <div class="card border-0 bg-white rounded-10">
            <div class="card-body">
                <span class="mt-3">Referral:</span> <span class="font-size">₹ 0</span>
            </div>
        </div>
    </div>
</div>
<div class="d-flex card-row p-2 justify-content-between align-items-center filterDiv">
    <div class="mb-3 mt-3 me-2">
        <div>
            <form class="src-form position-relative" >
                <label for="filterStartDate" class="file-upload mb-0">Start Date</label>
                <input type="date" class="form-control" style="border: 1px solid; color: black;" id="filterStartDate" value="{{$filter['filterStartDate'] ?? ''}}">
                <button type="button"
                    class="src-btn position-absolute top-50 end-0 translate-middle-y bg-transparent p-0 border-0 applyFilterButton">
                    <i data-feather="search"></i>
                </button>
            </form>
        </div>
    </div>
    <div class="mb-3 mt-3 me-2">
        <div>
            <form class="src-form position-relative" >
                <label for="filterEndDate" class="file-upload mb-0">End Date</label>
                <input type="date" class="form-control" style="border: 1px solid; color: black;" id="filterEndDate" value="{{$filter['filterEndDate'] ?? ''}}">
                <button type="button"
                        class="src-btn position-absolute top-50 end-0 translate-middle-y bg-transparent p-0 border-0 applyFilterButton">
                    <i data-feather="search"></i>
                </button>
            </form>
        </div>
    </div>
    <div class="mb-3 mt-3">
        <label for="filterEndDate" class="file-upload mb-0">&nbsp;</label>
        <button type="button" class="applyFilterButton p-2 btn btn btn-primary text-white fw-semibold me-2">
            <i class="ri-filter-off-line"></i>Apply Filter
        </button>
    </div>
    <div class="mb-3 mt-3">
        <label for="filterEndDate" class="file-upload mb-0">&nbsp;</label>
        <button type="button" class="clearFilterButton p-2 btn btn btn-primary text-white fw-semibold me-2">
            <i class="ri-filter-off-line"></i>Clear Filter
        </button>
    </div>
    <div class="mb-3 mt-3">

    </div>
    <div class="text-end d-flex flex-grow-1 justify-content-end align-items-center">
        <div class="icon transition me-5 mt-3">
            <div class="text ptext">
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn btn-primary text-white fw-semibold me-2 exportOrderButton"  title="Export Data">
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
        <th>Transaction Id</th>
        <th>Amount</th>
        <th>Description</th>
    </tr>
    </thead>
    <tbody>
    @forelse($temp as $r)
        <tr>
            <td><input type="checkbox" class="selectedCheck" value="{{$r->id}}"></td>
            <td>{{$r->datetime}}</td>
            <td>{{$r->id}}</td>
            @if($r->type == 'c')
                <td class="text-success">{{round($r->amount,2)}}</td>
            @else
                <td class="text-danger">-{{round($r->amount,2)}}</td>
            @endif
            <td>{{$r->description}}</td>
        </tr>
    @empty
        <tr>
        <td colspan="11">No record found</td>
        </tr>
    @endforelse
    </tbody>
</table>
