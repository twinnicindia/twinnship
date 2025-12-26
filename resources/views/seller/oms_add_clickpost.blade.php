<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title> Integrate ClickPost | {{$config->title}} </title>
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
                                <h3 class="h4 mb-4">Instruction to integrate ClickPost to Twinnship</h3>
                                <ol class="pl-lg amazon-ints">
                                    <li>Login to ClickPost Admin Panel.</li>
                                    <li>Go to Clickpost/OMS</li>
                                    <li>Copy credentials from Twinnship and paste in respective OMS.</li>
                                    <li>Before this mention like choose Twinnship as shipping partner.</li>
                                    <li>And provide the credential.</li>
                                    <li>Then integrate.</li>
                                </ol>
                                <!-- <iframe width="560" height="315" src="https://www.youtube.com/embed/ZQesDABh5eI" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe> -->
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{route('seller.oms_submit_clickpost')}}" method="post">
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
                                        <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" name="auto_fulfill" value="y" checked="checked" class="custom-control-input" id="customCheckDisabled1" disabled>
                                            <label class="custom-control-label" for="customCheckDisabled1">Fulfill orders <small>(Enabling this will auto fulfill order in ClickPost when an order is shipped with Twinnship)</small></label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" name="auto_cancel" value="y" checked="checked" class="custom-control-input" id="customCheckDisabled2" disabled>
                                            <label class="custom-control-label" for="customCheckDisabled2">Cancel orders <small>(Enabling this will auto cancel order in ClickPost when order is cancelled in Twinnship)</small></label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" name="auto_cod_paid" value="y" checked="checked" class="custom-control-input" id="customCheckDisabled3" disabled>
                                            <label class="custom-control-label" for="customCheckDisabled3">Mark as paid <small>(Mark COD orders as paid in ClickPost when orders are delivered to customer)</small></label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <!-- <button class="btn btn-primary">Submit</button> -->
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
        $('#example1').on('click','.remove_data',function(){
            var that=$(this);
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this warehouse!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, keep it'
            }).then((result) => {
                if (result.value) {
                    showOverlay();
                    $.ajax({
                        url : '{{url('/')."/delete-employees"}}/'+that.data('id'),
                        success : function (response) {
                            hideOverlay();
                            Swal.fire(
                                'Deleted!',
                                'Information has been deleted.',
                                'success'
                            );
                            $('#row'+that.data('id')).remove();
                        },
                        error : function (response) {
                            hideOverlay();
                            Swal.fire('Oops...', 'Something went wrong!', 'error');
                        }
                    });
                }
            })
        });
        $('#example1').on('click','.modify_data',function () {
            showOverlay();
            var that=$(this);
            $.ajax({
                url : '{{url('/')."/modify-employees/"}}' + that.data('id'),
                success: function (response) {
                    var info=JSON.parse(response);
                    var all_permissions=info.permissions.split(',');
                    for(var i=0;i<all_permissions.length;i++)
                        $('#per_'+all_permissions[i]).prop('checked',true);
                    $('#form1').prop('action','{{route('seller.update_employees')}}');
                    $('#hid').val(info.id);
                    $('#employee_name').val(info.employee_name);
                    $('#email').val(info.email);
                    $('#mobile').val(info.mobile);
                    $('#password').val(info.password);
                    $('#data_div').hide();
                    $('#form_div').fadeIn();
                    hideOverlay();
                },
                error : function (response) {
                    hideOverlay();
                    Swal.fire('Oops...', 'Something went wrong!', 'error');
                }
            });
        });

        $('.copyClass').click(function(){
            var that = $(this);
            var text = that.data('text');
            //alert(text);
            navigator.clipboard.writeText(text);
            $.notify(' Copied..',{ delay: 0,verticalAlign:'top', align:'right', type: 'success', icon:'check'});
        });

        $('#password').dblclick(function () {
            var that=$(this);
            that.prop('type','text');
        });
        $('#password').blur(function () {
            var that=$(this);
            that.prop('type','password');
        });
    });
</script>
</body>
</html>
