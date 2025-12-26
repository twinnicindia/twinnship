<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Courier Blocking Management | {{env('appTitle')}} </title>

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
                        <h1>Manage Courier Blocking</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active"> Courier Blocking</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <section class="content" id="form_div">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 col-sm-12 col-lg-12">
                        <div class="card card-primary card-outline card-tabs">
                            <div class="card-header">
                                <h3>Block Courier</h3>
                            </div>
                            <div class="card-body">
                                <form method="post" enctype="multipart/form-data" action="{{ route('administrator.blockCourier.store') }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="sellerId">Select Seller</label>
                                                <select type="text" class="form-control" id="sellerId" name="seller_id" required>
                                                    <option value="">Select Seller</option>
                                                    @foreach($sellers as $seller)
                                                        <option value="{{ $seller->id }}">{{ "{$seller->first_name} {$seller->last_name} ({$seller->code})" }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <label>Block Courier Partner</label>
                                        </div>
                                        <table class="table table-hover mx-auto" style="width: 98%;">
                                            <thead>
                                                <tr>
                                                    <th>Courier</th>
                                                    <th>Zone A</th>
                                                    <th>Zone B</th>
                                                    <th>Zone C</th>
                                                    <th>Zone D</th>
                                                    <th>Zone E</th>
                                                    <th>Order Type</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($courier_partners as $courier)
                                                <tr>
                                                    <td>
                                                        <input type="text" name="courier_{{ $courier->id }}" value="{{ $courier->id }}" id="courier_{{ $courier->id }}" hidden>
                                                        <div class="form-group">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input checkbox is_blocked_{{ $courier->id }}" name="is_blocked_{{ $courier->id }}" value="y" id="is_blocked_{{ $courier->id }}">
                                                                <label class="custom-control-label pt-1 block-courier" for="is_blocked_{{ $courier->id }}" data-cid="{{ $courier->id }}">{{ $courier->title }}</label>
                                                            </div>
                                                        </div>
                                                    <td>
                                                        <div class="form-group">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input checkbox zone_a_{{ $courier->id }}" name="zone_a_{{ $courier->id }}" value="y" id="zone_a_{{ $courier->id }}">
                                                                <label class="custom-control-label pt-1 block-zone" for="zone_a_{{ $courier->id }}" data-cid="{{ $courier->id }}" data-zone="a"></label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input checkbox zone_b_{{ $courier->id }}" name="zone_b_{{ $courier->id }}" value="y" id="zone_b_{{ $courier->id }}">
                                                                <label class="custom-control-label pt-1 block-zone" for="zone_b_{{ $courier->id }}" data-cid="{{ $courier->id }}" data-zone="b"></label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input checkbox zone_c_{{ $courier->id }}" name="zone_c_{{ $courier->id }}" value="y" id="zone_c_{{ $courier->id }}">
                                                                <label class="custom-control-label pt-1 block-zone" for="zone_c_{{ $courier->id }}" data-cid="{{ $courier->id }}" data-zone="c"></label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input checkbox zone_d_{{ $courier->id }}" name="zone_d_{{ $courier->id }}" value="y" id="zone_d_{{ $courier->id }}">
                                                                <label class="custom-control-label pt-1 block-zone" for="zone_d_{{ $courier->id }}" data-cid="{{ $courier->id }}" data-zone="d"></label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input checkbox zone_e_{{ $courier->id }}" name="zone_e_{{ $courier->id }}" value="y" id="zone_e_{{ $courier->id }}">
                                                                <label class="custom-control-label pt-1 block-zone" for="zone_e_{{ $courier->id }}" data-cid="{{ $courier->id }}" data-zone="e"></label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <div class="custom-control custom-checkbox" style="display: inline-block;">
                                                                <input type="checkbox" class="custom-control-input checkbox cod_{{ $courier->id }}" name="cod_{{ $courier->id }}" value="y" id="cod_{{ $courier->id }}">
                                                                <label class="custom-control-label pt-1 pr-3 block-zone" for="cod_{{ $courier->id }}" data-cid="{{ $courier->id }}" data-type="cod">COD</label>
                                                            </div>
                                                            <div class="custom-control custom-checkbox" style="display: inline-block;">
                                                                <input type="checkbox" class="custom-control-input checkbox prepaid_{{ $courier->id }}" name="prepaid_{{ $courier->id }}" value="y" id="prepaid_{{ $courier->id }}">
                                                                <label class="custom-control-label pt-1 block-zone" for="prepaid_{{ $courier->id }}" data-cid="{{ $courier->id }}" data-type="prepaid">Prepaid</label>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="submit-container" style="overflow:auto;">
                                        <div style="float:right;">
                                            <button type="submit" class="btn btn-primary">Block</button>
                                            <button type="reset" class="btn btn-danger">Reset</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!-- /.card -->
                        </div>
                    </div>
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
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
    // Show overlay
    showOverlay();
    $(document).ready(function() {
        // Hide overlay
        hideOverlay();

        $('#sellerId').select2({
            // placeholder: "Select seller",
            allowClear: true
        });

        // Get seller blocked courier partners
        $(".submit-container").hide();
        $("#sellerId").change(function() {
            $(".checkbox").prop("checked", false);
            // Show overlay
            showOverlay();
            let sellerId = $(this).val();
            if(!sellerId) {
                $(".submit-container").hide();
                // Hide overlay
                hideOverlay();
                return;
            } else {
                $(".submit-container").show();
            }
            $.ajax({
                url: "{{ route('administrator.blockCourier.get')}}",
                method: "get",
                data: {
                    sellerId: sellerId
                },
                success: function(res) {
                    if(res.statusCode == 200) {
                        res.data.forEach((e) => {
                            $(`#is_blocked_${e.courier_partner_id}`).prop("checked", (e.is_blocked == "y" ? true : false));
                            $(`#zone_a_${e.courier_partner_id}`).prop("checked", (e.zone_a == "y" ? true : false));
                            $(`#zone_b_${e.courier_partner_id}`).prop("checked", (e.zone_b == "y" ? true : false));
                            $(`#zone_c_${e.courier_partner_id}`).prop("checked", (e.zone_c == "y" ? true : false));
                            $(`#zone_d_${e.courier_partner_id}`).prop("checked", (e.zone_d == "y" ? true : false));
                            $(`#zone_e_${e.courier_partner_id}`).prop("checked", (e.zone_e == "y" ? true : false));
                            $(`#cod_${e.courier_partner_id}`).prop("checked", (e.cod == "y" ? true : false));
                            $(`#prepaid_${e.courier_partner_id}`).prop("checked", (e.prepaid == "y" ? true : false));
                        });
                    }
                    // Hide overlay
                    hideOverlay();
                }
            });
        });

        $(".block-courier").click(function() {
            $(`#zone_a_${$(this).data('cid')}`).prop("checked", !$(`#is_blocked_${$(this).data('cid')}`).prop("checked"));
            $(`#zone_b_${$(this).data('cid')}`).prop("checked", !$(`#is_blocked_${$(this).data('cid')}`).prop("checked"));
            $(`#zone_c_${$(this).data('cid')}`).prop("checked", !$(`#is_blocked_${$(this).data('cid')}`).prop("checked"));
            $(`#zone_d_${$(this).data('cid')}`).prop("checked", !$(`#is_blocked_${$(this).data('cid')}`).prop("checked"));
            $(`#zone_e_${$(this).data('cid')}`).prop("checked", !$(`#is_blocked_${$(this).data('cid')}`).prop("checked"));
            $(`#cod_${$(this).data('cid')}`).prop("checked", !$(`#is_blocked_${$(this).data('cid')}`).prop("checked"));
            $(`#prepaid_${$(this).data('cid')}`).prop("checked", !$(`#is_blocked_${$(this).data('cid')}`).prop("checked"));
        });

        $(".block-zone").click(function() {
            if(
                ($(this).data('zone') == "a" ? !$(`#zone_a_${$(this).data('cid')}`).prop("checked") : $(`#zone_a_${$(this).data('cid')}`).prop("checked")) &&
                ($(this).data('zone') == "b" ? !$(`#zone_b_${$(this).data('cid')}`).prop("checked") : $(`#zone_b_${$(this).data('cid')}`).prop("checked")) &&
                ($(this).data('zone') == "c" ? !$(`#zone_c_${$(this).data('cid')}`).prop("checked") : $(`#zone_c_${$(this).data('cid')}`).prop("checked")) &&
                ($(this).data('zone') == "d" ? !$(`#zone_d_${$(this).data('cid')}`).prop("checked") : $(`#zone_d_${$(this).data('cid')}`).prop("checked")) &&
                ($(this).data('zone') == "e" ? !$(`#zone_e_${$(this).data('cid')}`).prop("checked") : $(`#zone_e_${$(this).data('cid')}`).prop("checked")) &&
                ($(this).data('type') == "cod" ? !$(`#cod_${$(this).data('cid')}`).prop("checked") : $(`#cod_${$(this).data('cid')}`).prop("checked")) &&
                ($(this).data('type') == "prepaid" ? !$(`#prepaid_${$(this).data('cid')}`).prop("checked") : $(`#prepaid_${$(this).data('cid')}`).prop("checked"))
            ) {
                $(`#is_blocked_${$(this).data('cid')}`).prop("checked", true);
            } else {
                $(`#is_blocked_${$(this).data('cid')}`).prop("checked", false);
            }
        });
    });
</script>
</body>
</html>
