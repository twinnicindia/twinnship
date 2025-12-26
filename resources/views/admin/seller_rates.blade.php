<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Seller Rates Management | {{env('appTitle')}} </title>

    @include('admin.pages.styles')
    <style type="text/css">
        .error{
            border : 1px solid crimson;
        }
        .error:hover{
            border : 1px solid crimson;
        }
        .error:focus{
            border : 1px solid crimson;
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
                        <h1>Manage Rates</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active"> Rates</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content" id="form_div">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 col-sm-12 col-lg-12">
                        <div class="card card-primary card-outline card-tabs">
                            <div class="card-header">
                                <h3>
                                    <span>Select Seller</span>
                                    <div class="float-right">
                                        <button type="button" class="btn btn-primary btn-sm export" data-type="xls"><i class="fa fa-download"></i> Export</button>
                                        <button type="button" class="btn btn-primary btn-sm import"><i class="fa fa-upload"></i> Import</button>
                                    </div>
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Select Seller</label>
                                            <select class="form-control" id="seller" name="seller">
                                                <option value="0">Select Seller</option>
                                                @foreach($sellers as $s)
                                                <!-- <option value="{{$s->id}}">{{$s->first_name." ".$s->last_name}}</option> -->
                                                <option value="{{$s->id}}">{{$s->first_name.' '.$s->last_name."($s->code)"}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- /.card -->
                        </div>
                    </div>
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <section class="content" id="form_div">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 col-sm-12 col-lg-12">
                        <div class="card card-primary card-outline card-tabs">
                            <div class="card-header p-0 pt-1 border-bottom-0">
                                <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                                    @php($cnt=0)
                                    @foreach($plans as $p)
                                    <li class="nav-item">
                                        <a data-id="{{$p->id}}" class="nav-link {{$cnt++==0?"active":""}} clickOnPlanChange" id="custom-tabs-two-home-tab" data-toggle="pill" href="#custom-tabs-{{$p->id}}" role="tab" aria-controls="custom-tabs-two-home" aria-selected="true">{{$p->title}}</a>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content" id="custom-tabs-two-tabContent">
                                    @php($cnt=0)
                                    @foreach($plans as $p)
                                    <div class="tab-pane fade {{$cnt++==0?"active show":""}}" id="custom-tabs-{{$p->id}}" data-id="{{$p->id}}" role="tabpanel" aria-labelledby="custom-tabs-two-home-tab">
                                        <form action="{{route('save_rates')}}" method="post" id="form{{$p->id}}">
                                            @csrf
                                            <div class="row">
                                                <button type="button" data-id="{{$p->id}}" class="btn btn-outline-primary d-inline-block mr-3"><i class="fa fa-save"></i> Save Updated Rates</button>
                                                <div class="form-check" id="update_to_all">
                                                  <!-- <input class="form-check-input" type="checkbox" name="update_to_all" value="1" id="flexCheckChecked">
                                                  <label class="form-check-label" for="flexCheckChecked">
                                                    Update to all seller
                                                  </label> -->
                                                </div>
                                            </div><br>
                                            <input type="hidden" class="seller_ids" name="seller_id" value="0">
                                            <input type="hidden" name="plan" value="{{$p->id}}">
                                    <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Courier Partner</th>
                                                        <th>Within City&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                                        <th>Within State&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                                        <th>Metro to Metro&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                                        <th>Rest of India&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                                        <th>North East & J.K&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                                        <th>COD Charges</th>
                                                        <th>COD Maintenance(%)</th>
                                                        <th>Extra Charge Zone - A</th>
                                                        <th>Extra Charge Zone - B</th>
                                                        <th>Extra Charge Zone - C</th>
                                                        <th>Extra Charge Zone - D</th>
                                                        <th>Extra Charge Zone - E</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($partners as $pa)
                                                    <tr>
                                                        <td>{{$pa->title}}</td>
                                                        <td><input id="within_city_{{$pa->id."_".$p->id}}" name="within_city_{{$pa->id."_".$p->id}}" type="text" class="form-control input-sm sellerRate" value="0"></td>
                                                        <td><input id="within_state_{{$pa->id."_".$p->id}}" name="within_state_{{$pa->id."_".$p->id}}" type="text" class="form-control input-sm sellerRate" value="0"></td>
                                                        <td><input id="metro_to_metro_{{$pa->id."_".$p->id}}" name="metro_to_metro_{{$pa->id."_".$p->id}}" type="text" class="form-control input-sm sellerRate" value="0"></td>
                                                        <td><input id="rest_india_{{$pa->id."_".$p->id}}" name="rest_india_{{$pa->id."_".$p->id}}" type="text" class="form-control input-sm sellerRate" value="0"></td>
                                                        <td><input id="north_j_k_{{$pa->id."_".$p->id}}" name="north_j_k_{{$pa->id."_".$p->id}}" type="text" class="form-control input-sm sellerRate" value="0"></td>
                                                        <td><input id="cod_charge_{{$pa->id."_".$p->id}}" name="cod_charge_{{$pa->id."_".$p->id}}" type="text" class="form-control input-sm otherRates" value="0"></td>
                                                        <td><input id="cod_maintenance_{{$pa->id."_".$p->id}}" name="cod_maintenance_{{$pa->id."_".$p->id}}" type="text" class="form-control input-sm otherRates" value="0"></td>
                                                        <td><input id="extra_charge_a{{$pa->id."_".$p->id}}" name="extra_charge_a{{$pa->id."_".$p->id}}" type="text" class="form-control input-sm otherRates" value="0"></td>
                                                        <td><input id="extra_charge_b{{$pa->id."_".$p->id}}" name="extra_charge_b{{$pa->id."_".$p->id}}" type="text" class="form-control input-sm otherRates" value="0"></td>
                                                        <td><input id="extra_charge_c{{$pa->id."_".$p->id}}" name="extra_charge_c{{$pa->id."_".$p->id}}" type="text" class="form-control input-sm otherRates" value="0"></td>
                                                        <td><input id="extra_charge_d{{$pa->id."_".$p->id}}" name="extra_charge_d{{$pa->id."_".$p->id}}" type="text" class="form-control input-sm otherRates" value="0"></td>
                                                        <td><input id="extra_charge_e{{$pa->id."_".$p->id}}" name="extra_charge_e{{$pa->id."_".$p->id}}" type="text" class="form-control input-sm otherRates" value="0"></td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                    </div>
                                            <button type="button" data-id="{{$p->id}}" class="btn btn-outline-primary"><i class="fa fa-save"></i> Save Updated Rates</button><br><br>
                                        </form>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <!-- /.card -->
                        </div>
                    </div>
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->

        <div class="modal fade" id="import" tabindex="-1" role="dialog" aria-labelledby="bulkuploadTitle" aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content" id="fulfillment_info">
                    <form method="post" action="{{ route('sellerRateCard.import') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="text" id="plan_id" name="plan_id" hidden>
                        <input type="text" id="seller_id" name="seller_id" hidden>
                        <div class="modal-header">
                            <h5 class="modal-title" id="mySmallModalLabel">Bulk Upload Seller Rate Card</h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12 pb-10 mb-2">
                                    Download sample Seller Rate Card upload file : <a class="text-info" id="sample-url" href="#">Download</a>
                                </div>
                                <div class="col-sm-12">
                                    <div class="m-b-10">
                                    <div class="input-group mb-3">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="excel" name="excel">
                                            <label class="custom-file-label" for="excel">Choose file</label>
                                        </div>
                                    </div>
                                    <small class="text-danger">All previous rates will be removed</small>
                                        <bR>
                                        <bR>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 text-right">
                                    <button type="submit" class="btn btn-info btn-sm">Upload</button>
                                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </form>
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
    var base_path='{{url('/')}}/',seller_id=0,selectedPlanId = 1;
    $(document).ready(function () {
        $('.clickOnPlanChange').click(function () {
            selectedPlanId =  $(this).data('id');
        });
        $('#excel').on('change',function(){
            //get the file name
            var fileName = $(this).val();
            //replace the "Choose a file" label
            $(this).next('.custom-file-label').html(fileName);
        })
        $('#seller').select2({
            // placeholder: "Select seller",
            allowClear: true
        });

        fetch_sihpping_rates();
        $('.btn-outline-primary').click(function () {
            var that=$(this);
            if(!checkValidation()){
                showError('Minimum shipping rate is 20 Please Enter Rates Accordingly');
                return false;
            }
            $('#form'+that.data('id')).ajaxSubmit({
                beforeSubmit: function(){
                    showOverlay();
                },
                success : function (response) {
                    hideOverlay();
                    showSuccess('Success','Rates saved successfully');
                },
                error : function (response) {
                    hideOverlay();
                }
            });
        });
        $('#seller').change(function () {
            seller_id = $(this).val();
            $('.seller_ids').val(seller_id);
            fetch_sihpping_rates();
        });
        $('.sellerRate').blur(function(){
            var that = $(this);
            let rates = parseInt(that.val());
            if(rates != undefined && rates > 20){
                that.removeClass('error');
            }
        });

        // Export csv or xls
        $(".export").click(function() {
            location.href = "{{ route('sellerRateCard.export') }}?exportType=" + $(this).data('type') + "&seller_id=" + $('#seller').val() + '&plan_id='+selectedPlanId;
        });

        // Import
        $(".import").click(function() {
            $("#seller_id").val($("#seller").val());
            $("#plan_id").val($(".tab-pane.fade.active.show").data("id"));
            $("#sample-url").attr("href", "{{ route('sellerRateCard.exportSample') }}?seller_id=" + $("#seller_id").val() + "&plan_id=" + $("#plan_id").val());
            $("#import").modal("show");
        })
    });
    function checkValidation(){
        let success = true;
        // $('.sellerRate:visible').each(function(){
        //     var that = $(this);
        //     let rate = parseInt(that.val());
        //     if(rate == undefined || rate < 20 || isNaN(rate)){
        //         that.addClass('error');
        //         success = false;
        //     }else{
        //         that.removeClass('error');
        //     }
        // });
        return success;
    }
    function fetch_sihpping_rates() {
        showOverlay();
        if(seller_id == 0) {
            $("#update_to_all").show();
        } else {
            $("#update_to_all").hide();
        }
        $.ajax({
            type : 'get',
            data: {
                'seller_id' : seller_id
            },
            url : '{{route('get_rates')}}?rand='+ (Math.random() * 100000)+1,
            success : function (response) {
                hideOverlay();
                $('.sellerRate').val('');
                $('.otherRates').val('');
                var info=JSON.parse(response);
                for(var i=0;i<info.length;i++)
                {
                    $('#within_city_'+info[i].partner_id+"_"+info[i].plan_id).val(info[i].within_city);
                    $('#within_state_'+info[i].partner_id+"_"+info[i].plan_id).val(info[i].within_state);
                    $('#metro_to_metro_'+info[i].partner_id+"_"+info[i].plan_id).val(info[i].metro_to_metro);
                    $('#rest_india_'+info[i].partner_id+"_"+info[i].plan_id).val(info[i].rest_india);
                    $('#north_j_k_'+info[i].partner_id+"_"+info[i].plan_id).val(info[i].north_j_k);
                    $('#cod_charge_'+info[i].partner_id+"_"+info[i].plan_id).val(info[i].cod_charge);
                    $('#cod_maintenance_'+info[i].partner_id+"_"+info[i].plan_id).val(info[i].cod_maintenance);
                    $('#extra_charge_a'+info[i].partner_id+"_"+info[i].plan_id).val(info[i].extra_charge_a);
                    $('#extra_charge_b'+info[i].partner_id+"_"+info[i].plan_id).val(info[i].extra_charge_b);
                    $('#extra_charge_c'+info[i].partner_id+"_"+info[i].plan_id).val(info[i].extra_charge_c);
                    $('#extra_charge_d'+info[i].partner_id+"_"+info[i].plan_id).val(info[i].extra_charge_d);
                    $('#extra_charge_e'+info[i].partner_id+"_"+info[i].plan_id).val(info[i].extra_charge_e);
                }
            },
            error  :function (response) {
                showError('Something went wrong please try again later');
                hideOverlay();
            }
        });
    }
</script>
</body>
</html>
