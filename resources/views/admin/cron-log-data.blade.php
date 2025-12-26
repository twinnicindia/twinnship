<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Cron Logs | {{env('appTitle')}} </title>
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
                            <h1>Cron Logs</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                                <li class="breadcrumb-item active">Cron Logs</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- /.content -->
            <section class="content" id="data_div">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">All Logs</h3>
                                    <a href="{{route('export.cronLogData', [request()->route()->parameter('slug')])}}" class="btn btn-sm btn-primary float-right">Export</a>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Sr.No</th>
                                                    <th>Cron Name</th>
                                                    <th>Status</th>
                                                    <th style="width: 15%">Success</th>
                                                    <th style="width: 20%">Errors</th>
                                                    <th>Affected Rows</th>
                                                    <th>Started At</th>
                                                    <th>Finished At</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php($cnt=1)
                                                @foreach($cronLogs as $a)
                                                <tr id="row{{$a->id}}">
                                                    <td>{{$cnt++}}</td>
                                                    <td>{{$a->cron_name}}</td>
                                                    <td><span style="{{ $a->status == 'failed' ? 'color:red;' : 'color:green;'}}">{{$a->status}}</span></td>
                                                    <td style="width: 15%">{{$a->success}}</td>
                                                    <td style="width: 20%">{{$a->errors}}</td>
                                                    <td>
                                                        Inserted: {{$a->row_inserted}}</br>
                                                        Updated: {{$a->row_updated}}</br>
                                                        Deleted: {{$a->row_deleted}}
                                                    </td>
                                                    <td>{{$a->started_at}}</td>
                                                    <td>{{$a->finished_at}}</td>
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
    </div>
    <!-- ./wrapper -->

    @include('admin.pages.scripts')
    </script>
</body>

</html>
