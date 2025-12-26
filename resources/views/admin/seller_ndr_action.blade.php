<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Seller NDR Action | {{env('appTitle')}} </title>
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
                        <h1>Seller NDR Action</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active">NDR Action</li>
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
                                <h3 class="card-title">All NDR Action</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Sr.No</th>
                                        <th>Seller Detail</th>
                                        <th>Courier Partner</th>
                                        <th>Customer Order Number</th>
                                        <th>AWB Number</th>
                                        <th>Customer Detail</th>
                                        <th>NDR Action</th>
                                        <th>Remark</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php($cnt=1)
                                    @foreach($orders as $a)
                                        <tr id="row{{$a->id}}">
                                            <td>{{$cnt++}}</td>
                                            <td>
                                                <b>Code :</b> {{$a->code}}<br>
                                                <b>Company Name :</b> {{$a->company_name}}
                                            </td>
                                            <td>{{$a->courier_partner}}</td>
                                            <td>{{$a->customer_order_number}}</td>
                                            <td>{{$a->awb_number}}</td>
                                            <td>
                                                <b>Name :</b> {{$a->s_customer_name}} <br>
                                                <b>Mobile :</b> {{$a->s_contact}} <br>
                                                <b>Address :</b> {{$a->s_address_line1 ." ". $a->s_address_line2}} <br>
                                                <b>State :</b> {{$a->s_state}} <br>
                                                <b>City :</b> {{$a->s_city}} <br>
                                                <b>Pincode :</b> {{$a->s_pincode}} <br>
                                            </td>
                                            <td>{{$a->sellerNdrAction->reason ?? ''}}</td>
                                            <td>{{$a->sellerNdrAction->remark ?? ''}}</td>
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

<script type="text/javascript">
    var base_path='{{url('/')}}/';
</script>
</body>
</html>
