<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Courier COD Remittance | {{env('appTitle')}} </title>
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
                            <h1>Manage Courier COD Remittance</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                                <li class="breadcrumb-item active">Courier COD Remittance</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <section class="content" id="form_div">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Courier COD Remittance</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <form action="{{route('administrator.upload_courier_cod_remittance.add')}}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="courier">Select Courier</label>
                                                            <select name="courier" id="courier" class="form-control" required>
                                                                <option value="">Select Courier</option>
                                                                @foreach($partners as $row)
                                                                <option value="{{$row->keyword}}">{{$row->title}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="importFile">Select File</label>
                                                            <input id="importFile" type="file" name="importFile" class="form-control" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <br>
                                                        <div class="form-group mt-2">
                                                            <input type="submit" class="btn btn-primary" value="Submit">
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content" id="data_div">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Courier COD Remittance Logs</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Sr.No</th>
                                                    <th>Date</th>
                                                    <th>Courier Partner</th>
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
                                                    <td>{{explode(':', $w->remark)[0] ?? '-'}}</td>
                                                    <td><span class="badge {{ $w->status == 'success' ? 'badge-success' : 'badge-danger' }}">{{$w->status}}</span></td>
                                                    <td>{{explode(':', $w->remark)[1] ?? '-'}}</td>
                                                    <td>
                                                        <b>Total</b>: {{$w->total_records}}<br>
                                                        <b>Success</b>: {{$w->success}}<br>
                                                        <b>Failed</b>: {{$w->failed}}<br>
                                                        <b>Already Uploaded</b>: {{$w->already_uploaded}}
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('administrator.exportCourierCodRemittanceLog') }}?job_id={{$w->id}}&status=fail" class="btn btn-sm btn-primary m-1">Export Error</a>
                                                        <a href="{{ route('administrator.exportCourierCodRemittanceLog') }}?job_id={{$w->id}}" class="btn btn-sm btn-primary m-1">Download File</a>
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
</body>

</html>
