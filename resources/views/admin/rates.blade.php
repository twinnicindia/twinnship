<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Rates Management | {{env('appTitle')}} </title>

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
                            <div class="card-header p-0 pt-1 border-bottom-0">
                                <ul class="nav nav-tabs" id="custom-tabs-two-tab" role="tablist">
                                    @php($cnt=0)
                                    @foreach($plans as $p)
                                    <li class="nav-item">
                                        <a class="nav-link {{$cnt++==0?"active":""}}" id="custom-tabs-two-home-tab" data-toggle="pill" href="#custom-tabs-{{$p->id}}" role="tab" aria-controls="custom-tabs-two-home" aria-selected="true">{{$p->title}}</a>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="card-body">
                                <div class="tab-content" id="custom-tabs-two-tabContent">
                                    @php($cnt=0)
                                    @foreach($plans as $p)
                                    <div class="tab-pane fade {{$cnt++==0?"active show":""}}" id="custom-tabs-{{$p->id}}" role="tabpanel" aria-labelledby="custom-tabs-two-home-tab">
                                        <form action="{{route('save_rates')}}" method="post" id="form{{$p->id}}">
                                            @csrf
                                            <button type="button" data-id="{{$p->id}}" class="btn btn-outline-primary"><i class="fa fa-save"></i> Save Updated Rates</button><br><br>
                                            <input type="hidden" name="plan" value="{{$p->id}}">
                                            <input type="hidden" name="seller_id" value="0">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>Courier Partner</th>
                                                            <th>Within City</th>
                                                            <th>Within State</th>
                                                            <th>Metro to Metro</th>
                                                            <th>Rest of India &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                                                            <th>North East & J.K</th>
                                                            <th>COD Charges</th>
                                                            <th>COD Maintenance(%)</th>
                                                            <th>Overhold Charge(per 500gm)</th>
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
                                                            <td><input id="cod_charge_{{$pa->id."_".$p->id}}" name="cod_charge_{{$pa->id."_".$p->id}}" type="text" class="form-control input-sm" value="0"></td>
                                                            <td><input id="cod_maintenance_{{$pa->id."_".$p->id}}" name="cod_maintenance_{{$pa->id."_".$p->id}}" type="text" class="form-control input-sm" value="0"></td>
                                                            <td><input id="extra_charge_{{$pa->id."_".$p->id}}" name="extra_charge_{{$pa->id."_".$p->id}}" type="text" class="form-control input-sm" value="0"></td>
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
    var base_path='{{url('/')}}/',seller_id=0;
    $(document).ready(function () {
        showOverlay();
        $.ajax({
            type : 'get',
            data: {
                'seller_id' : seller_id
            },
            url : '{{route('get_rates')}}?rand='+ (Math.random() * 100000)+1,
            success : function (response) {
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
                   $('#extra_charge_'+info[i].partner_id+"_"+info[i].plan_id).val(info[i].extra_charge);
               }
               hideOverlay();
            },
            error  :function (response) {
                showError('Something went wrong please try again later');
                hideOverlay();
            }
        });
        $('.btn-outline-primary').click(function () {
            var that=$(this);
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
    });
</script>
</body>
</html>
