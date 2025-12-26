<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Archive Data | {{env('appTitle')}} </title>

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
                        <h1>Archive Data</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active">Archive Data</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content" id="form_div">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- jquery validation -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Archive Data</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" id="quickForm" action="{{route('administrator.run-archival')}}" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="id" id="hid" value="">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="type">Type</label>
                                                <select name="type" class="form-control" id="type">
                                                    <option value="orders">Orders Data</option>
                                                    <option value="others">Other Data</option>
                                                    <option value="pending_orders">Pending Orders</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="date">Select Date</label>
                                                <input type="date" name="date" class="form-control" id="date" value="{{date('Y-m-d',strtotime('-3 month'))}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md" id="responseData">
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="button" id="runArchivalButton" class="btn btn-primary">Run Archival</button>
                                </div>
                            </form>
                        </div>
                        <!-- /.card -->
                    </div>
                    <!--/.col (left) -->
                    <!-- right column -->
                    <!--/.col (right) -->
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- jquery validation -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Archive Data</h3>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Sr.No</th>
                                    <th>Table</th>
                                    <th>Deleted Before</th>
                                    <th>Executed</th>
                                    <th>Records Deleted</th>
                                    <th>IP</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php($cnt=1)
                                @foreach($recent as $r)
                                    <tr>
                                        <td>{{$cnt++}}</td>
                                        <td>{{$r->table_name}}</td>
                                        <td>{{$r->deleted_before}}</td>
                                        <td>{{$r->executed}}</td>
                                        <td>{{$r->no_of_records}}</td>
                                        <td>{{$r->ip_address}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">

                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- /.content -->
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

<script type="text/javascript">
    var base_path='{{url('/')}}/',orders_date = '{{date('Y-m-d',strtotime('-3 months'))}}',others_date = '{{date('Y-m-d',strtotime('-6 months'))}}',pending_orders = '{{date('Y-m-d',strtotime('-15 days'))}}';
    $(document).ready(function () {
        $('#type').change(function () {
            let that = $(this);
            if(that.val() === 'orders'){
                $('#date').val(orders_date).prop('max',orders_date);
            }else if(that.val() === 'others') {
                $('#date').val(others_date).prop('max', others_date);
            }else if(that.val() === 'pending_orders'){
                $('#date').val(pending_orders).prop('max', pending_orders);
            }
        });
        $('#runArchivalButton').click(function () {
            showOverlay();
            $('#quickForm').ajaxSubmit({
                success : function (response) {
                    hideOverlay();
                    $('#responseData').html(JSON.stringify(response));
                }
            });
        });
    });
</script>
</body>
</html>
