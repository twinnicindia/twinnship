<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> About Management | {{env('appTitle')}} </title>

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
                        <h1>Manage Rates</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active">Manage Rates</li>
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
                                <h3 class="card-title">Pending Request</h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th>Sr.No</th>
                                            <th>Seller</th>
                                            <th>Plan</th>
                                            <th>Created</th>
                                            <th>Created By</th>
                                            <th>Action</th>
                                            <th>View</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php($cnt=1)
                                        @foreach($rates_card as $a)
                                            <tr id="row{{$a->id}}">
                                                <td>{{$cnt++}}</td>
                                                <td>{{$a->seller_id}}</td>
                                                <td>{{$a->plan_id}}</td>
                                                <td>{{$a->created}}</td>
                                                <td>{{$a->created_by}}</td>
                                                <td id="tdData-{{$a->id}}">
                                                    @if($a->status == "pending")
                                                        <button class="btn-danger rejectBtn" data-id="{{$a->id}}" id="rejectBtn-{{$a->id}}" style="border-radius: 5px;">Reject</button>
                                                        <button class="btn-success approveBtn" data-id="{{$a->id}}" id="approveBtn-{{$a->id}}" style="border-radius: 5px;">Approve</button>
                                                    @else
                                                        {{ucfirst($a->status)}}
                                                    @endif

                                                </td>
                                                <td><a href="javascript:;" data-id="{{$a->id}}" class="view_all_data"><i id="SellerRateBtn" class="fas fa-eye"></i></a></td>
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

    <div class="modal fade bd-example-modal-lg" id="SellerRatesModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Seller Rates</h5>
                </div>
                <div class="modal-body" id="RatesmodalBody">

                </div>
                <div class="modal-footer">
                    <div class="col-sm-12 text-right">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                    </div>
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

        $('#example1').on('click','.rejectBtn',function () {
            let that = $(this);
            if(confirm('Are you sure to reject this request')){
                $.ajax({
                    type:'get',
                    url:'{{url('/')."/administrator/reject-status/"}}' + that.data('id'),
                    success: function (response) {
                        if(response.status === "true")
                        {
                            $('#tdData-'+that.data('id')).html('Rejected');
                            showSuccess('Changed','Status Rejected Successfully');
                        }
                    },
                    error : function () {
                        Swal.fire('Oops...', 'Something went wrong!', 'error');
                    }
                });
            }
        }).on('click','.approveBtn',function () {
            let that = $(this);
            if(confirm('Are you sure to approve this request this can not be undone?')){
                $.ajax({
                    type:'get',
                    url:'{{url('/')."/administrator/approve-status/"}}' + that.data('id'),
                    success: function (response) {
                        if(response.status === "true")
                        {
                            $('#tdData-'+that.data('id')).html('Approved');
                            showSuccess('Changed','Status Approved Successfully');
                        }
                    },
                    error : function () {
                        Swal.fire('Oops...', 'Something went wrong!', 'error');
                    }
                });
            }
        }).on('click','.view_all_data',function () {
            var sellerId = $(this).data('id');
            fetchSellerRates(sellerId);
        });

        function fetchSellerRates(id) {
            $.ajax({
                url : '{{url('/')."/administrator/fetch-seller-rates/"}}' + id,
                type: 'get',
                success: function (data) {
                    $('#RatesmodalBody').html(data);
                    $('#SellerRatesModal').modal('show');
                },
                error : function () {
                    Swal.fire('Oops...', 'Something went wrong!', 'error');
                }
            });
        }
    });
</script>

</body>
</html>
