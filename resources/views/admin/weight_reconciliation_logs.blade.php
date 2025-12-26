<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Weight Reconciliation Logs | {{env('appTitle')}} </title>
    @include('admin.pages.styles')
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        @include('admin.pages.header')
        <!-- /.navbar -->
        <!-- Main Sidebar Container -->
        @include('admin.pages.sidebar')

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Manage Weight Reconciliation Logs</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                                <li class="breadcrumb-item active">Weight Reconciliation Logs</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <section class="content" id="data_div">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">All Weight Reconciliation Logs</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Sr.No</th>
                                                    <th>Date</th>
                                                    <th>Name</th>
                                                    <th>Status</th>
                                                    <th>Remark</th>
                                                    <th>Stats</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php($cnt=1)
                                                @foreach($logs as $w)
                                                <tr id="row{{$w->id}}">
                                                    <td>{{$cnt++}}</td>
                                                    <td>{{$w->created_at}}</td>
                                                    <td>{{$w->job_name}}</td>
                                                    <td>{{$w->status}}</td>
                                                    <td>{{$w->remark}}</td>
                                                    <td>
                                                        <b>Total</b>: {{$w->total_records}}<br>
                                                        <b>Success</b>: {{$w->success}}<br>
                                                        <b>Failed</b>: {{$w->failed}}<br>
                                                        <b>Already Uploaded</b>: {{$w->already_uploaded}}
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('export_csv_weight_reconciliation') }}?job_id={{$w->id}}&status=fail" class="btn btn-sm btn-primary m-1">Export Error</a>
                                                        <a href="{{ route('export_csv_weight_reconciliation') }}?job_id={{$w->id}}" class="btn btn-sm btn-primary m-1">Download File</a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- /.content-wrapper -->
        @include('admin.pages.footer')

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    @include('admin.pages.scripts')
    </script>


</body>

</html>
