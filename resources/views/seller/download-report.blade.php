<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Download Report | {{$config->title}} </title>
    @include('seller.pages.styles')
    <style>
        .user-dashboard .btn {
            min-width: 40px;
        }
    </style>
</head>

<body>
    <div class="container-fluid user-dashboard">
        @include('seller.pages.header')
        @include('seller.pages.sidebar')
        <div class="content-wrapper">
            <div class="content-inner">
                <div class="card">
                    <div class="card-body">
                        <h3 class="h4 mb-4">Download Report</h3>
                        <br>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Sr.No</th>
                                        <th width="20%">Report Name</th>
                                        <th>Report Type</th>
                                        <th>Report Status</th>
                                        <th>Report Request Date</th>
                                        <th>Report Completed Date</th>
                                        <th>Download</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php($cnt=$reports->firstItem())
                                    @forelse($reports as $row)
                                    <tr>
                                        <td>{{ $cnt++ }}</td>
                                        <th>{{ $row->report_name }}</td>
                                        <td>{{ $row->report_type }}</td>
                                        <td>{{ $row->report_status }}</td>
                                        <td>{{ $row->created_at }}</td>
                                        <td>{{ $row->finished_at }}</td>
                                        <td>
                                            @if(!empty($row->report_download_url))
                                                @if(file_exists($row->report_download_url))
                                                    <a href="{{ $row->report_download_url }}" target="_blank">Download</a>
                                                @else
                                                    <a href="{{route('bucket.download-from-bucket',$row->id)}}">Download</a>
                                                @endif
                                            @else
                                                -
                                            @endif
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7">No Reord Found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="flex justify-between flex-1 sm:hidden mt-3 ml-2">
                            <a class="firstPageButton relative inline-flex items-center py-2 text-sm font-medium bg-white" href="{{ $reports->url(1) }}"><i class="fas fa-fast-backward"></i></a>
                            <a class="previousPageButton inline-flex items-center px-1 py-2 text-sm font-medium bg-white" href="{{ $reports->previousPageUrl() }}"><i class="fas fa-backward"></i></a>
                            <a class="relative inline-flex items-cente py-2 text-sm font-medium bg-white" style="text-decoration:none;"> Page</a>
                            <a><input type="text" class="currentPage relative inline-flex items-cente py-1 px-3 text-sm font-medium bg-white border" disabled style="width: 4%; text-align:center" value="{{ $reports->currentPage() }}"></a>
                            <a class="relative inline-flex items-cente py-2 text-sm font-medium bg-white" style="text-decoration:none;"> of</a>
                            <a class="totalPage relative inline-flex items-cente py-2 text-sm font-medium bg-white" style="text-decoration:none;">{{ $reports->lastPage() }}</a>
                            <a class="nextPageButton inline-flex items-center px-1 py-2 text-sm font-medium bg-white" href="{{ $reports->nextPageUrl() }}"><i class="fas fa-forward"></i></a>
                            <a class="lastPageButton inline-flex items-center py-2 text-sm font-medium bg-white" href="{{ $reports->url($reports->lastPage()) }}"><i class="fas fa-fast-forward"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('seller.pages.scripts')
</body>

</html>
