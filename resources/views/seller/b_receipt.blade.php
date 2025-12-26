<table class="table">
    <thead class="sticky-header">
    <tr class="text-center rounded-10">
        <th style="width: 40px;">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value=""
                       id="selectAll">
            </div>
        </th>
        <th>Note ID</th>
        <th>Note Number</th>
        <th>Note Date</th>
        <th>Total</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    @forelse($receipt as $p)
        <tr>
            <td><input type="checkbox" class="selectedCheck" value="{{$p->id}}"></td>
            <td>{{$p->id}}</td>
            <td>{{$p->note_number}}</td>
            <td>{{$p->note_date}}</td>
            <td>{{$p->total}}</td>
            <td><a href='{{url("receipt_invoice/$p->id")}}'>View Receipt</a></td>
        </tr>
    @empty
        <tr>
            <td colspan="11">No Data</td>
        </tr>
    @endforelse
    </tbody>
</table>
