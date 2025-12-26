<input type="hidden" value="{{$sku->total()}}" id="total_sku">
<input type="hidden" value="{{$sku->lastPage()}}" id="totalPage">
<input type="hidden" value="{{$sku->currentPage()}}" id="currentPage">
@php($cnt=1)
@foreach($sku as $s)
<tr id="row{{$s->id}}">
    <td><input type="checkbox" class="selectedCheck" value="{{$s->id}}"></td>
    <td>{{$cnt++}}</td>
    <td>{{$s->parent_sku}}</td>
    <td>{{$s->child_sku}}</td>
    <td>
        <a href="javascript:;" title="Remove Information" data-id="{{$s->id}}" class="btn btn-danger btn-sm remove_data mx-0"><i class="fa fa-trash"></i></a>
    </td>
</tr>
@endforeach