<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Open Weight Reconciliation | {{env('appTitle')}} </title>
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
                            <h1>Manage Weight Reconciliation</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                                <li class="breadcrumb-item active">Open Weight Reconciliation</li>
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
                                    <h3 class="card-title">Open Weight Reconciliation</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Order Id<br>Date</th>
                                                    <th>Channel</th>
                                                    <th>Order Details</th>
                                                    <th>Shipment Details</th>
                                                    <th>Entered Weight &<br> Dimensions (CM)</th>
                                                    <th>Charged Weight &<br> Dimensions (CM)</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($weight_dispute as $w)
                                                <tr>
                                                    <td>{{$w->customer_order_number}}<br>
                                                        {{$w->created}}
                                                    </td>
                                                    <td>{{$w->channel}}</td>
                                                    <td>Name : @foreach(explode(',', $w->product_name) as $name)
                                                        {{$name}}<br>
                                                        @endforeach
                                                        SKU : @foreach(explode(',', $w->product_sku) as $sku)
                                                        {{$sku}}<br>
                                                        @endforeach
                                                        Invoice Amount : {{$w->invoice_amount}}
                                                    </td>
                                                    <td>AWB : {{$w->awb_number}}<br>
                                                        Courier : {{$PartnerName[$w->courier_partner]}}</td>
                                                    <td>Wt : {{$w->e_weight}} KG<br>
                                                        (L * B * H) : {{$w->e_length}} * {{$w->e_breadth}} * {{$w->e_height}}<br>
                                                        Applied Amount : {{$w->applied_amount}}
                                                    </td>
                                                    @if(isset($w->c_weight))
                                                    <td>Wt : {{$w->c_weight}} KG<br>
                                                        (L * B * H) : {{$w->c_length}} * {{$w->c_breadth}} * {{$w->c_height}}<br>
                                                        Charged Amount : {{$w->charged_amount}}
                                                    </td>
                                                    @else
                                                    <td></td>
                                                    @endif
                                                    <td>{{$w->w_status}}</td>
                                                    <td>
                                                        <a data-id="{{$w->w_id}}" style="cursor:pointer;" class="mx-0 ViewHistory"  title="View History"><i class="fa fa-eye"></i></a>&nbsp;
                                                        <a href="javascript:;" title="Close Dispute" data-id="{{$w->w_id}}" class="closeDispute"><i class="fa fa-power-off"></i></a>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="10">No Weight Reconciliation Found</td>
                                                </tr>
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

            <section class="content" id="data_div_weight"></section>
        </div>


        <div class="modal fade" id="closeDisputeModal" tabindex="-1" role="dialog" aria-labelledby="closeDisputeModal" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content" id="fulfillment_info">
                <form method="post" action="{{route('administrator.close_weight_dispute')}}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="weight_rec_id" id="weight_rec_id">
                    <div class="modal-header">
                        <h5 class="modal-title" id="mySmallModalLabel">Close Dispute</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Closing Dispute on</label><br>
                                        <input type="radio" class="form-input-check" name="closing_type" required" value="seller"> Seller &nbsp;&nbsp;&nbsp;<input type="radio" class="form-input-check" name="closing_type" required" value="courier">&nbsp;Courier
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Remark</label>
                                        <input type="text" class="form-control" placeholder="Remark" id="remark" name="remark" required">
                                    </div>
                                </div>
                            </div>
                        <div class="row">
                            <div class="col-sm-12 text-right">
                                <button type="submit" class="btn btn-primary btn-sm">Close</button>
                            </div>
                        </div>
                    </div>
                </form>
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
        var base_path = '{{url('/')}}/';
        $(document).ready(function() {
            $('#inputGroupFile02').on('change', function() {
                var fileName = $(this).val();
                $(this).next('.custom-file-label').html(fileName);
            })
            $('#addDataButton').click(function() {
                showForm();
            });
            $('#cancelButton').click(function() {
                showData();
                $('#quickForm').trigger('reset');
            });

            $('#data_div_weight').on('click', '.BackButton', function() {
                $('#data_div_weight').hide();
                $('#data_div').fadeIn();
            });

            $('#data_div_weight').on('click', '.AddComment', function() {
                $('#AddCommentWeight').modal();
            });

            $('#data_div').on('click', '.closeDispute', function() {
                $('#closeDisputeModal').modal();
                $('#weight_rec_id').val($(this).data('id'));
            });

        });

        $('#data_div').on('click', '.ViewHistory', function() {
        showOverlay();
        var that = $(this);

        $.ajax({
            url: '{{url('/')."/administrator/get_history_weight_reconciliation/"}}' + that.data('id'),
            success: function(response) {
                $('#data_div').hide();
                $('#data_div_weight').show();
                $('#data_div_weight').html(response);
                hideOverlay();
            },
            error: function(response) {
                hideOverlay();
                $.notify(" Oops... Something went wrong!", {
                    animationType: "scale",
                    align: "right",
                    type: "danger",
                    icon: "close"
                });
            }
        });
    });

        function showForm() {
            $('#data_div').hide();
            $('#form_div').slideDown();
        }

        function showData() {
            $('#form_div').hide();
            $('#data_div').slideDown();
        }
    </script>


</body>

</html>
