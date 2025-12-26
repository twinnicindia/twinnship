<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title> Integrate EasyShip | {{$config->title}} </title>
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
                                <h3 class="h4 mb-4">Instruction to integrate EasyShip to Twinnship</h3>
                                <ol class="pl-lg amazon-ints">
                                    <li>Login to EasyShip Admin Panel.</li>
                                    <li>Go to Apps.</li>
                                    <li>Click on Private Apps Button.</li>
                                    <li>Click on Create a Private App.</li>
                                    <li>Enter Title name under the Description tab and click on Save.</li>
                                    <li>Click on Title, as you entered earlier.</li>
                                    <li>Here you will find EasyShip API Key, password, Shared Secret.</li>
                                    <li>Copy the identifiers and integrate the channel.</li>
                                </ol>
                                <!-- <iframe width="560" height="315" src="https://www.youtube.com/embed/ZQesDABh5eI" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe> -->
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="h4 mb-4">Instruction to integrate EasyShip to Twinnship</h3>
                                <form action="{{route('seller.oms_submit_easyship')}}" method="post">
                                    @csrf
                                    <div class="form-group">
                                        <label for="oms_title">OMS Title</label>
                                        <input type="text" class="form-control" name="oms_title" id="oms_title" placeholder="OMS Title" value="" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="easyship_bearer_token">EasyShip Bearer Token</label>
                                        <input type="text" class="form-control" id="easyship_bearer_token" name="easyship_bearer_token" value="" placeholder="Enter EasyShip Bearer Token" required>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" name="auto_fulfill" value="y" checked="checked" class="custom-control-input" id="customCheckDisabled1">
                                            <label class="custom-control-label" for="customCheckDisabled1">Fulfill orders <small>(Enabling this will auto fulfill order in EasyShip when an order is shipped with Twinnship)</small></label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" name="auto_cancel" value="y" checked="checked" class="custom-control-input" id="customCheckDisabled2">
                                            <label class="custom-control-label" for="customCheckDisabled2">Cancel orders <small>(Enabling this will auto cancel order in EasyShip when order is cancelled in Twinnship)</small></label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox custom-control-inline">
                                            <input type="checkbox" name="auto_cod_paid" value="y" checked="checked" class="custom-control-input" id="customCheckDisabled3">
                                            <label class="custom-control-label" for="customCheckDisabled3">Mark as paid <small>(Mark COD orders as paid in EasyShip when orders are delivered to customer)</small></label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-primary">Submit</button>
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
