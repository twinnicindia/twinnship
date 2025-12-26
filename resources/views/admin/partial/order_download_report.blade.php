<input type="hidden" value="{{$report->lastPage()}}" id="total_page">
<input type="hidden" value="{{$report->currentPage()}}" id="currentPage">
@php ($cnt = (($report->perPage() * $report->currentPage()) - $report->perPage()) + 1)
<table class="table table-bordered table-striped">
    <tr>
        <th>Sr No</th>
        <th>Name</th>
        <th>Status</th>
        <th>Request Date</th>
        <th>Complete Date</th>
        <th>Download</th>
        <th>Action</th>
    </tr>
    @foreach($report as $a)
        <tr>
            <td>{{$cnt++}}</td>
            <td>{{$a->report_name}}</td>
            <td>{{ucfirst($a->status)}}</td>
            <td>{{$a->created_at}}</td>
            <td>{{$a->finished_at}}</td>

            <td>
                @if(!empty($a->report_download_url))
                    <a href="{{route('bucket.order-download-from-bucket',$a->id)}}" class="btn btn-primary {{$a->status != 'success' ? "disabled" : ""}}">Download</a>
                @elseif($a->status == "processing")
                    -
                @else
                    <a href="javascript:;" data-id="{{$a->id}}" data-remark="{{$a->remark}}" class="btn btn-danger view_error">Error Log</a>
                @endif
            </td>
            <td>@if($a->status == 'processing')<a class="btn btn-danger" title="Mark Failed" href="{{url('/admin/mark-failed-order-job/'.$a->id)}}"><i class="fa fa-times"></i></a>@endif</td>
        </tr>
    @endforeach
</table>
