<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> AWB Thresholds | {{env('appTitle')}} </title>
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
                            <h1>AWB Thresholds</h1>
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
                                    <h3 class="card-title">All AWB Thresholds</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Sr.No</th>
                                                    <th>Courier Partner</th>
                                                    <th>Used Awbs</th>
                                                    <th>Available Awbs</th>
                                                    <th>Total Awbs</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($thresholds as $row)
                                                <tr>
                                                    <td>{{$loop->iteration}}</td>
                                                    <td>{{$row['courier_partner']}}</td>
                                                    <td><strong class="{{ $row['used_awb_in_pr'] < 90 ? 'text-success' : 'text-danger' }}">{{$row['used_awb_in_pr']}} %</strong></td>
                                                    <td><strong class="{{ $row['available_awb_in_pr'] > 10 ? 'text-success' : 'text-danger' }}">{{$row['available_awb_in_pr']}} %</strong></td>
                                                    <td><strong>{{number_format($row['total_awb'])}}</strong></td>
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
