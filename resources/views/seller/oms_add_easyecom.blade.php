<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title> Integrate EasyEcom | {{$config->title}} </title>
    @include('seller.pages.styles')
</head>
<body>
<div class="container-fluid user-dashboard">
    @include('seller.pages.header')
    @include('seller.pages.sidebar')
    <div class="content-wrapper">
        <div class="content-inner" id="form_div">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="h4 mb-4">Instruction to integrate EasyEcom to Twinnship</h3>
                                <ol class="pl-lg amazon-ints">
                                    <li>Login to EasyEcom Panel.</li>
                                    <li>Navigate to Carrier Master then Choose Twinnship as Carrier Master</li>
                                    <li>Enter the Credentials as per instructed by Twinnship Integration Page</li>
                                    <li>Set Serviceability Type - Any Pincode</li>
                                    <li>Shipping Mode - All</li>
                                    <li>Priority - As per You</li>
                                    <li>Extra Credentials - Contact EasyEcom Team for eeApiToken and Enter in this Field </li>
                                </ol>
                                <!-- <iframe width="560" height="315" src="https://www.youtube.com/embed/ZQesDABh5eI" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe> -->
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{route('seller.oms_submit_easyecom')}}" method="post">
                                    @csrf
                                    <div class="form-group">
                                        <label for="easyecom_api_token">Username</label>
                                        <div class="row">
                                            <div class="col-md-11">
                                                <input type="text" class="form-control" id="username" name="username" value="{{Session()->get('MySeller')->email}}" placeholder="Enter EasyEcom Username" disabled>
                                            </div>
                                            <div class="col-md-1">
                                                <a href="javascript:;" data-toggle="tooltip" data-original-title="Copy Text" class="copyClass" style="color: #073D59" data-text="{{Session()->get('MySeller')->email}}"><i class="fa fa-copy fa-2x"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="easyecom_api_token">Password</label>
                                        <div class="row">
                                            <div class="col-md-11">
                                                <input type="text" class="form-control" id="password" name="password" value="{{Session()->get('MySeller')->password}}" placeholder="Enter EasyEcom Password" disabled>
                                            </div>
                                            <div class="col-md-1">
                                                <a href="javascript:;" data-toggle="tooltip" data-original-title="Copy Text" class="copyClass" style="color: #073D59" data-text="{{Session()->get('MySeller')->password}}"><i class="fa fa-copy fa-2x"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="easyecom_api_token">Twinnship Token</label>
                                        <div class="row">
                                            <div class="col-md-11">
                                                <input type="text" class="form-control" id="Twinnship_token" name="Twinnship_token" placeholder="Twinnship Token" value="{{Session()->get('MySeller')->api_key}}" disabled>
                                            </div>
                                            <div class="col-md-1">
                                                <a href="javascript:;" data-toggle="tooltip" data-original-title="Copy Text" class="copyClass" style="color: #073D59" data-text="{{Session()->get('MySeller')->api_key}}"><i class="fa fa-copy fa-2x"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="easyecom_api_token">Extra Parameter 1</label>
                                        <div class="row">
                                            <div class="col-md-11">
                                                <input type="text" class="form-control" id="Twinnship_token" name="Twinnship_token" placeholder="Twinnship Token" value="https://www.Twinnship.in" disabled>
                                            </div>
                                            <div class="col-md-1">
                                                <a href="javascript:;" data-toggle="tooltip" data-original-title="Copy Text" class="copyClass" style="color: #073D59" data-text="https://www.Twinnship.in"><i class="fa fa-copy fa-2x"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" name="auto_fulfill" value="y" checked="checked" class="custom-control-input" id="customCheckDisabled1" disabled>
                                            <label class="custom-control-label" for="customCheckDisabled1">Fulfill orders <small>(Enabling this will auto fulfill order in EasyEcom when an order is shipped with Twinnship)</small></label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" name="auto_cancel" value="y" checked="checked" class="custom-control-input" id="customCheckDisabled2" disabled>
                                            <label class="custom-control-label" for="customCheckDisabled2">Cancel orders <small>(Enabling this will auto cancel order in EasyEcom when order is cancelled in Twinnship)</small></label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" name="auto_cod_paid" value="y" checked="checked" class="custom-control-input" id="customCheckDisabled3" disabled>
                                            <label class="custom-control-label" for="customCheckDisabled3">Mark as paid <small>(Mark COD orders as paid in EasyEcom when orders are delivered to customer)</small></label>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>
@include('seller.pages.scripts')
<script type="text/javascript">
    $(document).ready(function () {
        $('.addInfoButton').click(function () {
            $('#form1').prop('action','{{route('seller.add_employees')}}');
            $('#data_div').hide();
            $('#form_div').fadeIn();
        });
        $('#cancelButton').click(function () {
            $('#form1').trigger("reset");
            $('#form_div').hide();
            $('#data_div').fadeIn();
        });
        $('.copyClass').click(function(){
            var that = $(this);
            var text = that.data('text');
            //alert(text);
            navigator.clipboard.writeText(text);
            $.notify(' Copied..',{ delay: 0,verticalAlign:'top', align:'right', type: 'success', icon:'check'});
        });
    });
</script>
</body>
</html>
