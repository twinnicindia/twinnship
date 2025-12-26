<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Servicable Pincode | {{env('appTitle')}} </title>

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
                        <h1>Servicable Pincode</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active"> Servicable Pincode</li>
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
                                <h3 class="card-title">Servicable Pincode</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                            <form action="{{route('administrator.servicable_pincode')}}" method="get">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <input type="text" name="pincode" class="form-control" placeholder="Enter Pincode" value="{{request()->pincode}}">
                                            <input type="checkbox" value="y" name="fm" class="mt-2" {{isset($fm) ? 'checked' : ''}}> <small class="text-info">&nbsp;&nbsp;First Mile Serviceability</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="seller_id"></label>
                                            <input type="submit" class="btn btn-primary">
                                        </div>
                                    </div>
                                </div>
                            </form>


                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Sr.No</th>
                                        <th>Courier Partner</th>
                                        <th>Pincode</th>
                                        <th>City</th>
                                        <th>State</th>
                                        <th>Servicable</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php($cnt=1)
                                    @forelse($pincodes as $key=> $p)
                                        <tr>
                                            <td>{{++$key}}</td>
                                            <td>{{$PartnerName[$p->courier_partner] ?? ""}}</td>
                                            <td>{{$p->pincode}}</td>
                                            <td>{{$p->city}}</td>
                                            <td>{{$p->state}}</td>
                                            <td>Yes</td>
                                        </tr>
                                    @empty
                                    <td colspan="6">No Data Found</td>
                                    @endforelse
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
