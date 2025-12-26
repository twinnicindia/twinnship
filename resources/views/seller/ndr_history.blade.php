@forelse($ndr_data as $n)
    <tr>
        <td>{{$n->raised_date}}</td>
        <td>{{$n->action_by}}</td>
        <td>{{$n->reason}}</td>
        <td>{{$n->remark}}</td>
        <td class="text-capitalize">{{$n->action_status}}</td>
    </tr>
@empty
    <tr>
        <td colspan="4">No NDR Attempt History Found</td>
    </tr>
@endforelse