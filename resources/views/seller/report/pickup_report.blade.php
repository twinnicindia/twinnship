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
                        <h3 class="h4 mb-4">Get Report</h3>
                        <br>
                        <form action="{{route('seller.pickup_reports')}}" method="get">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>From Date</label>
                                    <input type="date" name="from_date" value="{{date('Y-m-d',strtotime('-2 days'))}}" id="from_date" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label>To Date</label>
                                    <input type="date" name="to_date" id="to_date" class="form-control" value="{{date('Y-m-d')}}">
                                </div>
                                <div class="col-md-3 pt-2">
                                    <br>
                                    <button type="submit" class="btn btn-primary" id="searchReportButton">Get Report</button>
                                </div>
                                <div class="col-md-3 pt-2">
                                    <br>
                                    <a href="?<?= $_SERVER['QUERY_STRING']; ?><?= str_contains($_SERVER['QUERY_STRING'],'export')?'':'&export=true' ?>" class="btn btn-primary"><i class="fa fa-upload"></i> Export Data</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <br>
                @if(!empty($reports))
                <div class="card">
                    <div class="card-body">
                        <h3 class="h4 mb-4">Download Report</h3>
                        <br>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Sr.No</th>
                                        <th>AWB Number</th>
                                        <th>Shipped Date</th>
                                        <th>Picked Date</th>
                                        <th>Current Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php($cnt = (((request()->get('page') ?? 1) -1) * 10) + 1 )
                                    @foreach($reports as $r)
                                        <tr>
                                            <td>{{$cnt++}}</td>
                                            <td>{{$r->awb_number}}</td>
                                            <td>{{$r->awb_assigned_date}}</td>
                                            <td>{{$r->datetime}}</td>
                                            <td>{{$r->status}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{$reports->appends(request()->input())->links()}}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    @include('seller.pages.scripts')
</body>

</html>
