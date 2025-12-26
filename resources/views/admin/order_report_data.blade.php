<table id="example1" class="table table-bordered table-striped">
    <thead>
    <tr>
        <th>Sr.No</th>
        <th>Seller ID</th>
        <th>Company Name</th>
        <th>Order ID</th>
        <th>Awb Number</th>
        <th>Courier Partner</th>
        <th>AWB Assigned Date</th>
        <th>Invoice Amount</th>
        <th>Shipping Charge</th>
        <th>Billing Details</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    @php($cnt=0)
    @forelse($all_data as $row)
        <tr>
            <td>{{++$cnt}}</td>
            <td>{{$row->seller_id}}</td>
            <td>{{$row->company_name}}</td>
            <td>{{$row->order_number}}</td>
            <td>{{$row->awb_number}}</td>
            <td>{{$row->courier_partner}}</td>
            <td>{{$row->awb_assigned_date}}</td>
            <td>{{$row->invoice_amount}}</td>
            <td>{{$row->shipping_charges}}</td>
            <td>
                Name: {{$row->s_customer_name}}</br>
                Mobile: {{$row->s_contact}}</br>
            </td>
            <td>{{$row->status}}</td>
            <td><button class="btn btn-sm btn-primary view-order" data-id="{{$row->id}}">View</button></td>
        </tr>
    @empty
    <td colspan="11">No Data Found</td>
    @endforelse
    </tbody>
</table>
