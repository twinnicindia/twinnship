<table class="table table-bordered table-striped">
    <tr>
        <th>Sr No</th>
        <th>Name</th>
        <th>From Date</th>
        <th>To Date</th>
        <th>Message Type</th>
        <th>Total Message Sent</th>
    </tr>
        <tr>
            <td>1</td>
            <td>{{$seller_name}}</td>
            <td>{{$start_date}}</td>
            <td>{{$end_date}}</td>
            <td>{{ucfirst($message_type)}}</td>
            <td>{{$counter}}</td>
        </tr>
</table>
