<input type="hidden" value="{{$sku->total()}}" id="total_sku">
<input type="hidden" value="{{$sku->lastPage()}}" id="totalPage">
<input type="hidden" value="{{$sku->currentPage()}}" id="currentPage">
@php($cnt=1)
@foreach($sku as $s)
<tr id="row{{$s->id}}">
    <td><input type="checkbox" class="selectedCheck" value="{{$s->id}}"></td>
    <td>{{$cnt++}}</td>
    <td>{{$s->sku}}</td>
    <td>{{$s->product_name}}</td>
    <td>{{$s->brand_name ?? '-'}}</td>
    <td>{{$s->weight}} kg</td>
    <td>
        L * B * H ({{$s->length}} * {{$s->width}} * {{$s->height}})
    </td>
    <td>
        <a href="javascript:;" data-toggle="tooltip" data-original-title="Edit Information" data-id="{{$s->id}}" class="btn btn-success btn-sm modify_data mx-0"><i class="fa fa-pencil"></i></a>
        <a href="javascript:;" data-toggle="tooltip" data-original-title="Remove Information" data-id="{{$s->id}}" class="btn btn-danger btn-sm remove_data mx-0"><i class="fa fa-trash"></i></a>
    </td>
</tr>
@endforeach
