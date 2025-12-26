<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Async Cron Jobs | {{env('appTitle')}} </title>
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
                            <h1>Async Cron Jobs</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                                <li class="breadcrumb-item active">Cron Jobs</li>
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
                                    <h3 class="card-title">All Cron Jobs</h3>
                                    <a href="{{route('export.cronJobs')}}" class="btn btn-sm btn-primary float-right">Export</a>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Sr.No</th>
                                                    <th>Job Name</th>
                                                    <th>Last Status</th>
                                                    <th>Started At</th>
                                                    <th>Finished At</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php($cnt=1)
                                                @foreach($cronJobs as $a)
                                                <tr id="row{{$a->id}}">
                                                    <td>{{$cnt++}}</td>
                                                    <td>{{$a->job_name}}</a></td>
                                                    <td><span style="{{ $a->last_status == 'failed' ? 'color:red;' : 'color:green;'}}">{{$a->last_status}}</span></td>
                                                    <td>{{$a->started_at}}</td>
                                                    <td>{{$a->finished_at}}</td>
                                                    <td><button class="btn btn-sm btn-primary run" data-href="{{ $a->cron_url ?? '#'}}">Run</button></td>
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
    <script>
        $(document).ready(function() {
            $(".run").click(function() {
                if(confirm("Do you want to run this job?")) {
                    window.open($(this).data("href"));
                }
            });
        });
    </script>
</body>

</html>
