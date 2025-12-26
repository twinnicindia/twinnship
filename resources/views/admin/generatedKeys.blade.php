<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Generated Keys | {{env('appTitle')}} </title>
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
                        <h1>Manage Keys</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active">Manage Keys</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <!-- /.content -->
        <section class="content" id="data_div">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">All Seller List</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="table-responsive">
                                <table id="example" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Sr.No<?php $cnt = $_GET['page'] ?? 1; ?></th>
                                        <th>Seller Code</th>
                                        <th>Email</th>
                                        <th>API Key</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php($cnt= (($cnt-1) * 15) + 1)
                                    @foreach($sellers as $a)
                                        <tr id="row{{$a->id}}">
                                            <td>{{$cnt++}}</td>
                                            <td>{{$a->code}}</td>
                                            <td>{{$a->email}}</td>
                                            <td>{{$a->api_key == "" ? "Not Generated" : $a->api_key}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                    {{$sellers->links()}}
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

<script type="text/javascript">
    var base_path='{{url('/')}}/';
    $(document).ready(function () {

    });
</script>
</body>
</html>
