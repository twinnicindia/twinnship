<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Delete Archive Order | {{env('appTitle')}} </title>

    @include('admin.pages.styles')

</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        @include('admin.pages.header')
        @include('admin.pages.sidebar')
        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Delete Archive Order</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                                <li class="breadcrumb-item active"> Delete Archive Order</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>
            <section class="content" id="form_div">
                <div class="container-fluid">
                    <div class="row">
                        <!-- left column -->
                        <div class="col-md-12">
                            <!-- jquery validation -->
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Delete Archive Orders</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <form role="form" id="quickForm" action="{{url('submit-delete-archive-orders')}}" method="post">
                                    <input type="hidden" name="id" id="hid" value="">
                                    @csrf
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="name">Enter Awbs</label>
                                                    <textarea rows="4" type="text" name="awbs" class="form-control" id="awbs" placeholder="Please Enter Comma Separated AWBs"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.card-body -->
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </form>
                            </div>
                            <!-- /.card -->
                        </div>
                        <!--/.col (left) -->
                        <!-- right column -->
                        <div class="col-md-6">

                        </div>
                        <!--/.col (right) -->
                    </div>
                    <!-- /.row -->
                </div><!-- /.container-fluid -->
            </section>
        </div>

    </div>
    <!-- /.content-wrapper -->
    @include('admin.pages.footer')
    <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->
    <div class="modal fade" id="importAWBModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-modal="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{route('administrator.importAwbOrders')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Export Orders</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">Choose CSV file</label>
                                    <input type="file" name="awb_numbers" class="form-control">
                                    <a href="https://www.Twinnship.in/samples/sample_awb.csv" download="">Download Sample File</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="closeModalButton" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewOrderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderModalLabel">Order Details</h5>
                </div>
                <div class="modal-body" id="order-details">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewReportRemark" tabindex="-1" aria-labelledby="orderModalLabel" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Error Log</h5>
                </div>
                <div class="modal-body">
                    <p id="log_p"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    @include('admin.pages.scripts')
    <script>
        $(document).ready(function () {

            $('#archiveOrderRadio').click(function () {
                $('#start_date').val('{{date('Y-m-d',strtotime('-270 days'))}}');
                $('#start_date').attr('max','{{date('Y-m-d',strtotime('-90 days'))}}');
                $('#end_date').val('{{date('Y-m-d',strtotime('-90 days'))}}');
                $('#end_date').attr('max','{{date('Y-m-d',strtotime('-90 days'))}}');
            });

            $('#orderRadio').click(function () {
                $('#start_date').val('{{date('Y-m-d',strtotime('-7 days'))}}');
                $('#start_date').attr('min','{{date('Y-m-d',strtotime('-90 days'))}}');
                $('#start_date').attr('max','{{date('Y-m-d')}}');
                $('#end_date').val('{{date('Y-m-d')}}');
                $('#end_date').attr('max','{{date('Y-m-d')}}');
            });


            setInterval(reloadDownloadReport(),2000);
            $('#btnOpenCsvModel').click(function () {
                $('#importAWBModal').modal('show');
            });

            $('#closeModalButton').click(function () {
                $('#importAWBModal').modal('hide');
            });

            $("#order-report-data").on('click','.view_error',function () {
                var that = $(this);
                $('#log_p').html(that.data('remark'));
                $('#viewReportRemark').modal('show');
            });

            $('#reloadDownloadReport').click(function () {
                showOverlay();
                reloadDownloadReport();
                hideOverlay();
            });

            $(".search-order").click(function() {
                let data = {};
                if($("#awb_number").val()) {
                    data.awb_number = $("#awb_number").val();
                }
                if($("#start_date").val()) {
                    data.start_date = $("#start_date").val();
                }
                if($("#end_date").val()) {
                    data.end_date = $("#end_date").val();
                }
                $.ajax({
                    'url': "{{ route('administrator.get_order_report') }}",
                    'method': "GET",
                    'data': data,
                    success: function(res) {
                        $("#order_data_div").show();
                        $("#order-data").html(res);
                    }
                });
            })

            $('#order_report_form').validate({
                rules: {
                    start_date: {
                        required: true
                    },
                    end_date: {
                        required: true
                    },
                },
                messages: {
                    start_date: {
                        required: "Please Select From Date",
                    },
                    end_date: {
                        required: "Please Select To Date",
                    },
                },
                errorElement: 'span',
                errorPlacement: function (error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function (element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });
            $('#seller').select2({
                placeholder: "Select seller",
                allowClear: true
            });
            $('#courier_partner').select2({
                placeholder: "Select Partner",
                allowClear: true
            });
            $("#exportSearchOrder").click(function() {
                $("#order_search_form").submit();
            });

            $("#order-data").on("click", ".view-order", async function() {
                var id = $(this).data("id");
                $("#order-details").html("Data not found");
                await $.ajax({
                    'url': "{{ route('administrator.get_order') }}",
                    'method': "GET",
                    'data': { id: id },
                    success: function(res) {
                        if(res) {
                            $("#order-details").html("");
                            $("#order-details").html(`
                        <table class="table table-bordered table-striped">
                            <tr>
                                <td>Order ID</td>
                                <td>${res.id}</td>
                            </tr>
                            <tr>
                                <td>Order Number</td>
                                <td>${res.order_number}</td>
                            </tr>
                            <tr>
                                <td>Seller ID</td>
                                <td>${res.seller_id}</td>
                            </tr>
                            <tr>
                                <td>AWB Number</td>
                                <td>${res.awb_number}</td>
                            </tr>
                            <tr>
                                <td>Courier Partner</td>
                                <td>${res.courier_partner}</td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>${res.status}</td>
                            </tr>
                            <tr>
                                <td>NDR Status</td>
                                <td>${res.ndr_status}</td>
                            </tr>
                            <tr>
                                <td>RTO Status</td>
                                <td>${res.rto_status}</td>
                            </tr>
                            <tr>
                                <td>AWB Assigned Date</td>
                                <td>${res.awb_assigned_date}</td>
                            </tr>
                            <tr>
                                <td>Order Date</td>
                                <td>${res.inserted}</td>
                            </tr>
                            <tr>
                                <td>Invoice Amount</td>
                                <td>${res.invoice_amount}</td>
                            </tr>
                            <tr>
                                <td>Shipping Charges</td>
                                <td>${res.shipping_charges}</td>
                            </tr>
                            <tr>
                                <td>COD Charges</td>
                                <td>${res.cod_charges}</td>
                            </tr>
                            <tr>
                                <td>COD Maintenence</td>
                                <td>${res.cod_maintenence ?? 0}</td>
                            </tr>
                            <tr>
                                <td>RTO Charges</td>
                                <td>${res.rto_charges}</td>
                            </tr>
                            <tr>
                                <td>Manifest Status</td>
                                <td>${res.manifest_status}</td>
                            </tr>
                            <tr>
                                <td>Manifest Sent</td>
                                <td>${res.manifest_sent}</td>
                            </tr>
                            <tr>
                                <td>Fulfillment Sent</td>
                                <td>${res.fulfillment_sent}</td>
                            </tr>
                            <tr>
                                <td>Vol Weight</td>
                                <td>${res.vol_weight}</td>
                            </tr>
                            <tr>
                                <td>Weight</td>
                                <td>${res.weight}</td>
                            </tr>
                            <tr>
                                <td>Height</td>
                                <td>${res.height}</td>
                            </tr>
                            <tr>
                                <td>Breadth</td>
                                <td>${res.breadth}</td>
                            </tr>
                            <tr>
                                <td>Length</td>
                                <td>${res.length}</td>
                            </tr>
                            <tr>
                                <td>Product Name</td>
                                <td>${res.product_name}</td>
                            </tr>
                            <tr>
                                <td>Product Quantiry</td>
                                <td>${res.product_qty}</td>
                            </tr>
                            <tr>
                                <td>Pickup Address</td>
                                <td>${res.pickup_address}</td>
                            </tr>
                            <tr>
                                <td>Delivery Address</td>
                                <td>${res.delivery_address}</td>
                            </tr>
                        </table>
                    `);
                        }
                    }
                });

                $("#viewOrderModal").modal("show");
            });

            $('.nextPageButton').click(function () {
                if(parseInt($('.currentPage').val()) + 1  <= parseInt($('.totalPage').html())) {
                    reloadDownloadReport(parseInt($('.currentPage').val()) + 1);
                }
            });

            $('.lastPageButton').click(function () {
                reloadDownloadReport(parseInt($('.totalPage').html()));
            });

            $('.firstPageButton').click(function () {
                reloadDownloadReport(1);
            });

            $('.previousPageButton').click(function () {
                if(parseInt($('.currentPage').val()) - 1 >= 1)
                    reloadDownloadReport(parseInt($('.currentPage').val()) - 1);
            });
        });

        function reloadDownloadReport(page = 0) {
            $.ajax({
                url: page !== 0 ? "{{url("get-Order-Download-Report?page=")}}" + page : "{{url("get-Order-Download-Report")}}",
                success: function (response) {
                    $('#order-report-data').html(response);
                    $('.totalPage').html($('#total_page').val());
                    $('#txtPageCount').val($('#currentPage').val());
                },
                error: function (response) {

                }
            });
        }
    </script>
</body>

</html>
