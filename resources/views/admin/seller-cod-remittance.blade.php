<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Seller COD Remittance | {{env('appTitle')}} </title>
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
                            <h1>Manage Seller COD Remittance</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                                <li class="breadcrumb-item active">Seller COD Remittance</li>
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
                                    <h3 class="card-title">Seller COD Remittance</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <form action="{{route('administrator.upload_seller_cod_remittance.add')}}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                <div class="row">
{{--                                                    <div class="col-md-4">--}}
{{--                                                        <div class="form-group">--}}
{{--                                                            <label for="courier">Select Seller</label>--}}
{{--                                                            <select name="seller" id="seller" class="form-control" required>--}}
{{--                                                                <option value="">Select Seller</option>--}}
{{--                                                                @foreach($sellers as $row)--}}
{{--                                                                <option value="{{$row->id}}">{{$row->first_name." ".$row->last_name."(".$row->code.")"}}</option>--}}
{{--                                                                @endforeach--}}
{{--                                                            </select>--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="importFile">Select File</label>
                                                            <input id="importFile" type="file" name="importFile" class="form-control" required>
                                                            <a href="{{asset('assets/seller/seller_cod_remittance.csv')}}">Download Sample File</a>
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
                                    <h3 class="card-title">Seller COD Remittance Logs</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="col-md-8">
                                        <form action="{{route('administrator.seller_cod_remittance')}}" method="get">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <input type="text" name="q" class="form-control" value="{{ request()->q ?? '' }}" placeholder="Search..">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <input type="submit" class="btn btn-primary" value="Search">
                                                        <a class="btn btn-primary ml-1" href="{{route('administrator.seller_cod_remittance')}}">Reset</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="table-responsive">
                                        <table id="example" class="table table-bordered table-striped">
                                            <thead>
{{--                                                <tr>--}}
{{--                                                    <th>Sr.No</th>--}}
{{--                                                    <th>Date</th>--}}
{{--                                                    <th>Courier Partner</th>--}}
{{--                                                    <th>Status</th>--}}
{{--                                                    <th>Remark</th>--}}
{{--                                                    <th>Stats</th>--}}
{{--                                                    <th>Action</th>--}}
{{--                                                </tr>--}}
                                                <tr>
                                                    <th>Sr.No</th>
                                                    <th>Awb Number</th>
                                                    <th>Courier</th>
                                                    <th>Seller Details</th>
                                                    <th>Remittance Amount</th>
                                                    <th>Remitted Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php($cnt = $remitted->firstItem())
                                                @foreach($remitted as $w)
                                                <tr id="row{{$w->id}}">
                                                    <td>{{$cnt++}}</td>
{{--                                                    <td>{{$w->created_at}}</td>--}}
{{--                                                    <td>{{explode(':', $w->remark)[0] ?? '-'}}</td>--}}
{{--                                                    <td><span class="badge {{ $w->status == 'success' ? 'badge-success' : 'badge-danger' }}">{{$w->status}}</span></td>--}}
{{--                                                    <td>{{explode(':', $w->remark)[1] ?? '-'}}</td>--}}
{{--                                                    <td>--}}
{{--                                                        <b>Total</b>: {{$w->total_records}}<br>--}}
{{--                                                        <b>Success</b>: {{$w->success}}<br>--}}
{{--                                                        <b>Failed</b>: {{$w->failed}}<br>--}}
{{--                                                        <b>Already Uploaded</b>: {{$w->already_uploaded}}--}}
{{--                                                    </td>--}}
{{--                                                    <td>--}}
{{--                                                        <a href="{{ route('administrator.exportSellerCodRemittanceLog') }}?job_id={{$w->id}}&status=fail" class="btn btn-sm btn-primary m-1">Export Error</a>--}}
{{--                                                        <a href="{{ route('administrator.exportSellerCodRemittanceLog') }}?job_id={{$w->id}}" class="btn btn-sm btn-primary m-1">Download File</a>--}}
{{--                                                    </td>--}}
                                                    <td>
                                                        {{$w->awb_number}}
                                                    </td>
                                                    <td>
                                                        {{$w->courier_partner}}
                                                    </td>
                                                    <td>
                                                        Name : {{$w->seller_name}}<br>
                                                        Code : {{$w->seller_code}}
                                                    </td>
                                                    <td>
                                                        {{$w->invoice_amount}}
                                                    </td>
                                                    <td>
                                                        {{$w->actual_date_of_remittance}}
                                                    </td>
                                                    <td>
                                                        <a href="javascript:;" class="btn btn-sm btn-success m-1 getRemitDetails" data-id="{{$w->id}}"><i class="fa fa-eye"></i></a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        {{ $remitted->links() }}
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>

        <div class="modal fade" id="viewOrderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="orderModalLabel">Remit Details</h5>
                    </div>
                    <div class="modal-body" id="order-details">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
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
        $(document).ready(function () {
            $('#example').on('click','.getRemitDetails',function(){
                $.ajax({
                    url: '{{url("/administrator/get-seller-cod-remittance-by-id")}}/' + $(this).data('id'),
                    success: function(res){
                        // var info = JSON.parse(response);
                        if(res) {
                            $("#order-details").html("");
                            $("#order-details").html(`
                        <table class="table table-bordered table-striped">
                            <tr>
                                <td>AWB Number</td>
                                <td>${res.awb_number}</td>
                            </tr>
                            <tr>
                                <td>Delivered Date</td>
                                <td>${res.delivery_date}</td>
                            </tr>
                            <tr>
                                <td>Order ID</td>
                                <td>${res.customer_order_number}</td>
                            </tr>
                            <tr>
                                <td>Invoice Amount</td>
                                <td>${res.invoice_amount}</td>
                            </tr>
                            <tr>
                                <td>Deduction</td>
                                <td>${res.deduction_amount}</td>
                            </tr>
                            <tr>
                                <td>Ref No.</td>
                                <td>${res.bank_reference_no}</td>
                            </tr>
                            <tr>
                                <td>Remarks</td>
                                <td>${res.remark}</td>
                            </tr>
                        </table>
                    `);
                        }

                    },
                    error: function(response){

                    }
                });
                $("#viewOrderModal").modal("show");
            });
        });
    </script>
</body>

</html>
