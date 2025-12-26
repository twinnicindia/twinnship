<!-- <div class="tablist mb-1 mt-2" id="pills-tab" role="tablist">
    <div class="me-2 card-row" role="presentation">
        <a class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home"
           type="button" role="tab" aria-controls="pills-home" aria-selected="true">Freight Invoice</a>
    </div>
    <div class="me-2 card-row" role="presentation">
        <a class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile"
           type="button" role="tab" aria-controls="pills-profile" aria-selected="false">All Other
            Invoices</a>
    </div>
</div> -->
<table class="table">
    <thead class="sticky-header">
    <tr class="text-center rounded-10">
        <th style="width: 40px;">
            <div class="form-check">
                <input class="form-check-input" type="checkbox"
                       value="" id="selectAll">
            </div>
        </th>
        <th>Invoice Id</th>
        <th>Invoice Date</th>
        <th>Due Date</th>
        <th>Total</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    @forelse($invoice as $i)
        <tr>
            <td><input type="checkbox" class="selectedCheck" value="{{$i->id}}"></td>
            <td>{{$i->inv_id}}</td>
            <td>{{$i->invoice_date}}</td>
            <td>{{$i->due_date}}</td>
            <td> &#8377; {{$i->total}}</td>
            <td><button class="btn btn-success">{{$i->status}}</button></td>
            <td><button><a target="_blank" href='{{url("billing/invoice/pdf/$i->id")}}'>View Invoice</a></button></td>
        </tr>
    @empty
        <tr>
            <td colspan="11" class="">No Data Found</td>
        </tr>
    @endforelse
    </tbody>
</table>
