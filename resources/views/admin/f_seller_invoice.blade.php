<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Seller Invoice {{env('appTitle')}} </title>
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
                            <h1>Seller Invoice</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                                <li class="breadcrumb-item active"> Seller Invoice</li>
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
                                    <h3 class="card-title">Generate Invoice</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <form action="{{route('administrator.finance.generate-seller-invoice')}}" method="post">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="seller_id">Select Seller</label>
                                                    <select name="sellers[]" class="form-control" id="seller_id" multiple>
                                                        @foreach($sellers as $row)
                                                            <option value="{{$row->id}}" {{$row->id == session('f_seller_id') ? 'selected' : ''}}>{{$row->first_name.' '.$row->last_name."($row->code)"}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <label>Start Date</label>
                                                <input type="date" name="from_date" value="{{session('f_from_date') == '' ? date('Y-m-d',strtotime('-7 days')) : session('f_from_date')}}" id="from_date" class="form-control">
                                            </div>
                                            <div class="col-md-2">
                                                <label>End Date</label>
                                                <input type="date" name="to_date" id="to_date" class="form-control" value="{{session('f_to_date') == '' ? date('Y-m-d') : session('f_to_date')}}">
                                            </div>
                                            <div class="col-md-2">
                                                <label>Invoice Date</label>
                                                <input type="date" name="invoice_date" id="invoice_date" class="form-control" value="{{session('invoice_date') == '' ? date('Y-m-d') : session('invoice_date')}}">
                                            </div>
                                            <div class="col-md-2 mt-2">
                                                <div class="form-group">
                                                    <br>
                                                    <label for="seller_id"></label>
                                                    <input type="submit" class="btn btn-primary" value="Generate Invoice">
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <!-- /.card-body -->
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- /.content -->
            <section class="content" id="data_div">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Seller Invoice</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <form action="{{route('administrator.f_seller_invoice')}}" method="get">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="seller_id">Select Seller</label>
                                                    <select name="seller_id" class="form-control" id="seller_id">
                                                        <option value="">All</option>
                                                        @foreach($sellers as $row)
                                                        <option value="{{$row->id}}" {{$row->id == session('f_seller_id') ? 'selected' : ''}}>{{$row->first_name.' '.$row->last_name."($row->code)"}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label>From Date</label>
                                                <input type="date" name="from_date" value="{{session('f_from_date') == '' ? date('Y-m-d',strtotime('-7 days')) : session('f_from_date')}}" id="from_date" class="form-control">
                                            </div>
                                            <div class="col-md-3">
                                                <label>To Date</label>
                                                <input type="date" name="to_date" id="to_date" class="form-control" value="{{session('f_to_date') == '' ? date('Y-m-d') : session('f_to_date')}}">
                                            </div>
                                            <div class="col-md-3 mt-2">
                                                <div class="form-group">
                                                <br>
                                                    <label for="seller_id"></label>
                                                    <input type="submit" class="btn btn-primary" value="Search">
                                                    <a href="{{route('administrator.export_billing_invoice_data')}}" class="btn btn-primary {{$display}}" id="downloadReport">Export</a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>


                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Seller Code</th>
                                                <th>Seller Name</th>
                                                <th>Invoice Id</th>
                                                <th>Invoice Date</th>
                                                <th>Due Date</th>
                                                <th>Total Amount</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php($cnt=1)
                                            @forelse($invoice as $key=> $i)
                                            <tr>
                                                <td>{{$i->code}}</td>
                                                <td>{{$i->first_name.' '.$i->last_name}}</td>
                                                <td>{{$i->inv_id}}</td>
                                                <td>{{$i->invoice_date}}</td>
                                                <td>{{$i->due_date}}</td>
                                                <td>{{$i->total}}</td>
                                                <td>{{$i->status}}</td>
                                                <td><a href='{{url("administrator/billing/invoice/pdf/$i->id")}}' class="btn btn-primary btn-sm">View</a></td>
                                            </tr>
                                            @empty
                                            <td colspan="8">No Data Found</td>
                                            @endforelse
                                        </tbody>
                                    </table>
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
        $(document).ready(function(){
            $('#seller_id').select2({
                placeholder: "Select seller",
                allowClear: true
            });
        });
    </script>
</body>

</html>
