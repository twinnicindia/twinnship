<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Seller Remittance {{env('appTitle')}} </title>
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
                            <h1>Seller Remittance</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                                <li class="breadcrumb-item active"> Seller Remittance</li>
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
                                    <h3 class="card-title">Seller Remittance</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <form action="{{route('administrator.f_seller_remittance')}}" method="get">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="seller_id">Select Seller</label>
                                                    <select name="seller_id" class="form-control" id="seller_id">
                                                        <option value="">All</option>
                                                        @foreach($sellers as $row)
                                                        <option value="{{$row->id}}" {{$row->id == session('rm_seller_id') ? 'selected' : ''}}>{{$row->first_name.' '.$row->last_name."($row->code)"}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label>From Date</label>
                                                <input type="date" name="from_date" value="{{session('rm_from_date') == '' ? date('Y-m-d',strtotime('-7 days')) : session('rm_from_date')}}" id="from_date" class="form-control">
                                            </div>
                                            <div class="col-md-3">
                                                <label>To Date</label>
                                                <input type="date" name="to_date" id="to_date" class="form-control" value="{{session('rm_to_date') == '' ? date('Y-m-d') : session('rm_to_date')}}">
                                            </div>
                                            <div class="col-md-3 mt-2">
                                                <div class="form-group">
                                                <br>
                                                    <label for="seller_id"></label>
                                                    <input type="submit" class="btn btn-primary" value="Search">
                                                    <a href={{route('administrator.export_remittance_report')}} class="btn btn-primary {{$display}}" id="downloadReport">Export</a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>


                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Sr. no</th>
                                                <th>Seller Code</th>
                                                <th>Seller Name</th>
                                                <th>Transaction Id</th>
                                                <th>Date</th>
                                                <th>Amount</th>
                                                <th>Description</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php($cnt=1)
                                            @forelse($remittance as $key=> $i)
                                            <tr>
                                                <td>{{++$key}}</td>
                                                <td>{{$i->code}}</td>
                                                <td>{{$i->first_name.' '.$i->last_name}}</td>
                                                <td>{{$i->id}}</td>
                                                <td>{{$i->datetime}}</td>
                                                <td>{{$i->amount}}</td>
                                                <td>{{$i->description}}</td>
                                                <td>
                                                @if($i->remitted_by == 'admin')
                                                    <a href='{{url("administrator/billing/remmitance/export/$i->id")}}' class="btn btn-primary btn-sm"><i class="fa fa-upload"></i></a>
                                                @else
                                                -
                                                @endif
                                                </td>
                                            </tr>
                                            @empty
                                            <td colspan="8">No Data Found</td>
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
