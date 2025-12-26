<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Seller COD Remittance {{env('appTitle')}} </title>
    @include('admin.pages.styles')
    <style>
        .user-dashboard .btn {
            min-width: 40px;
        }
    </style>
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
                        <h1>Seller COD Remittance</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active"> Seller COD Remittance</li>
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
                                <h3 class="card-title">Seller COD Remittance</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <form action="{{route('administrator.seller_remittance_data')}}" method="get">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="seller_id">Select Seller</label>
                                                <select name="seller_id" class="form-control" id="seller_id">
                                                    <option value="">Select Seller</option>
                                                    @foreach($sellers as $row)
                                                        <option value="{{$row->id}}" {{$row->id == session('rm_seller_id') ? 'selected' : ''}}>{{$row->first_name.' '.$row->last_name."($row->code)"}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 mt-2">
                                            <div class="form-group">
                                                <br>
                                                <label for="seller_id"></label>
                                                <input type="submit" class="btn btn-primary" value="Search">
                                                <input type="button" class="btn btn-primary" value="Reset" onclick="window.location.href='{{ route('administrator.seller_remittance_data') }}';">
                                            </div>
                                        </div>
                                    </div>
                                </form>


                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Seller</th>
                                        <th>Total COD</th>
                                        <th>COD Pending</th>
                                        <th>COD Remitted</th>
                                        <th>Next Remit Date</th>
                                        <th>Next Remit Amount</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @if(!empty($cod_total) || !empty($remitted_cod) || !empty($nextRemitCod))
                                        <tr>
                                            <td>{{$seller->company_name ?? ""}} ({{$seller->code ?? ""}})</td>
                                            <td>₹ {{$cod_total ?? ""}}</td>
                                            <td>₹ {{($cod_total - $remitted_cod) ?? 0}}</td>
                                            <td>₹ {{$remitted_cod ?? 0}}</td>
                                            <td>{{isset($nextRemitDate) ? date("D, d M' y", strtotime($nextRemitDate)) : date("D, d M' y", strtotime("next Wednesday"))}}</td>
                                            <td>₹ {{$nextRemitCod ?? 0}}</td>
                                            <td>

                                            </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td colspan="6" class="text-center">No Data Found</td>
                                        </tr>
                                    @endif
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
