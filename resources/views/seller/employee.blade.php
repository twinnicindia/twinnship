<!DOCTYPE html>
<html lang="zxx">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    @include('seller.pages.styles')

    <title>Employee | {{$config->title}}</title>
</head>

<body>

@include('seller.pages.header')
@include('seller.pages.side_links')


<div class="container-fluid">
    <div class="main-content d-flex flex-column">
        <div class="row justify-content-center">
            <div class="col-xxl-12 mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="card-body mt-3">
                        <div class="content-wrapper">
                            <div class="content-inner" id="data_div">
                                <div class="card">
                                    <div class="card-body">
                                        <h3 class="h4 mb-4">All Employees</h3>
                                        <button type="button" class="btn btn-primary btn-sm addInfoButton"><i class="fa fa-plus"></i> Add New Employee</button>
                                        <button type="button" class="btn btn-danger btn-sm" id="removeAllButton" style="display: none;"><i class="fa fa-trash"></i> Remove</button>
                                        @if(count($employee)!=0)
                                            <div class="table-responsive">
                                                <table class="table table-hover mb-0" id="example1">
                                                    <thead>
                                                    <tr>
                                                        <th><input type="checkbox" id="checkAllButton" value="y"></th>
                                                        <th>Sr.No</th>
                                                        <th>Name</th>
                                                        <th>Email</th>
                                                        <th>Mobile</th>
                                                        <th>Action</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @php($cnt=1)
                                                    @foreach($employee as $w)
                                                        <tr id="row{{$w->id}}">
                                                            <td><input type="checkbox" class="selectedCheck" value="{{$w->id}}"></td>
                                                            <td>{{$cnt++}}</td>
                                                            <td>{{$w->employee_name}}</td>
                                                            <td>{{$w->email}}</td>
                                                            <td>{{$w->mobile}}</td>
                                                            <td>
                                                                <button href="javascript:;" title="Edit Information" data-id="{{$w->id}}" class="btn btn-primary btn-sm modify_data mr-0"><i class="fa fa-pencil"></i></button>
                                                                <button href="javascript:;" title="Remove Information" data-id="{{$w->id}}" class="btn btn-primary btn-sm remove_data ml-0"><i class="fa fa-trash"></i></button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>

                                        @else
                                            <br><br>
                                            <h6>No employees added yet</h6>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="content-inner" id="form_div" style="display: none;">
                                    <div class="card">
                                    <div class="card-body">
                                        <h3 class="h4 mb-4">Employees</h3>
                                        <form action="{{route('seller.add_employees')}}" method="post" id="form1">
                                            @csrf
                                            <input type="hidden" name="hid" id="hid">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <div class="form-group">
                                                        <label class="label" for="employee_name">Employee Name</label>
                                                        <input type="text" class="form-control" placeholder="Employee Name" id="employee_name" name="employee_name" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <div class="form-group">
                                                        <label class="label" for="email">Email Address</label>
                                                        <input type="email" class="form-control" placeholder="Email Address" id="email" name="email"  autocomplete="off" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <div class="form-group">
                                                        <label class="label" for="mobile">Employee Mobile</label>
                                                        <input type="number" class="form-control" placeholder="Support Phone" id="mobile" name="mobile"  autocomplete="one-time-code" value="" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <div class="form-group">
                                                        <label class="label" for="password">Choose Password</label>
                                                        <input type="password" class="form-control" placeholder="Choose Password" id="password" name="password"  autocomplete="one-time-code" value="" required>
                                                        <small id="passwordHint" style="display:none;">Enter if want to change</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <label class="label" for="employee_name">Choose Permissions</label>
                                                </div>
                                                <div class="col-md-6 row">
                                                    <div class="col-sm-4 mb-3">
                                                        <div class="custom-control custom-checkbox custom-control-inline">
                                                            <input type="checkbox" name="permission[]" value="orders" class="custom-control-input" id="per_orders">
                                                            <label class="custom-control-label label" for="per_orders">Orders Management</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 mb-3">
                                                        <div class="custom-control custom-checkbox custom-control-inline">
                                                            <input type="checkbox" name="permission[]" value="reverse" class="custom-control-input" id="per_reverse">
                                                            <label class="custom-control-label label" for="per_reverse">Reverse Orders</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 mb-3">
                                                        <div class="custom-control custom-checkbox custom-control-inline">
                                                            <input type="checkbox" name="permission[]" value="shipments" class="custom-control-input" id="per_shipments">
                                                            <label class="custom-control-label label" for="per_shipments">Shipments</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 mb-3">
                                                        <div class="custom-control custom-checkbox custom-control-inline">
                                                            <input type="checkbox" name="permission[]" value="billing" class="custom-control-input" id="per_billing">
                                                            <label class="custom-control-label label" for="per_billing">Billing</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 mb-3">
                                                        <div class="custom-control custom-checkbox custom-control-inline">
                                                            <input type="checkbox" name="permission[]" value="integrations" class="custom-control-input" id="per_integrations">
                                                            <label class="custom-control-label label" for="per_integrations">Integrations</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 mb-3">
                                                        <div class="custom-control custom-checkbox custom-control-inline">
                                                            <input type="checkbox" name="permission[]" value="reports" class="custom-control-input" id="per_reports">
                                                            <label class="custom-control-label label" for="per_reports">MIS Report</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 mb-3">
                                                        <div class="custom-control custom-checkbox custom-control-inline">
                                                            <input type="checkbox" name="permission[]" value="customer_support" class="custom-control-input" id="per_customer_support">
                                                            <label class="custom-control-label label" for="per_customer_support">Customer Support</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 mb-3">
                                                        <div class="custom-control custom-checkbox custom-control-inline">
                                                            <input type="checkbox" name="permission[]" value="pii_access" class="custom-control-input" id="per_pii_access">
                                                            <label class="custom-control-label label" for="per_pii_access">PII Access</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4 mb-3">
                                                        <div class="custom-control custom-checkbox custom-control-inline">
                                                            <input type="checkbox" name="permission[]" value="tools" class="custom-control-input" id="per_tools">
                                                            <label class="custom-control-label label" for="per_tools">Tools</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div style="overflow:auto;">
                                                <div style="float:right;">
                                                    <button type="button" class="btn btn-danger" id="cancelButton">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Submit</button>
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
        </div>
    </div>
</div>

@include('seller.pages.scripts')
<script>
    var email=$('#email'),isEditing = false;
    $(document).on('keypress','#mobile',function(e){
        if($(e.target).prop('value').length>=10){
            if(e.keyCode!=32)
            {return false}
        }})


    var del_ids=[];


    $(document).ready(function () {
        $('#checkAllButton').click(function () {
            var that=$(this);
            if(that.prop('checked')){
                $('.selectedCheck').prop('checked',true);
                $('#removeAllButton').fadeIn();
            }
            else{
                $('.selectedCheck').prop('checked',false);
                $('#removeAllButton').hide();
            }
        });
        $('.selectedCheck').click(function () {
            var cnt=0;
            $('.selectedCheck').each(function () {
                if($(this).prop('checked'))
                    cnt++;
            });
            if(cnt>0)
                $('#removeAllButton').fadeIn();
            else
                $('#removeAllButton').hide();
        });
        email.blur(function () {
            if(isEditing)
                return;
            if(email.val().trim() !== ""){
                $.ajax({
                    type : 'get',
                    url : '{{url('')}}/check-employee-email/' + email.val(),
                    success : function (response) {
                        var info=JSON.parse(response);
                        if(info.status === 'false'){
                            $.notify(" Email is already used please use another email address", {animationType:"scale", align:"right", type: "danger", icon:"close"});
                            email.val('').focus();
                        }
                    },
                    error : function (response) {
                        $.notify(" Email is already used please use another email address", {animationType:"scale", align:"right", type: "danger", icon:"close"});
                    }
                });
            }
        });
        $('#removeAllButton').click(function () {
            del_ids=[];
            $('.selectedCheck').each(function () {
                if($(this).prop('checked'))
                    del_ids.push($(this).val());
            });
            if (window.confirm("Are you sure want to Delete?")) {
                showOverlay();
                $.ajax({
                    type : 'post',
                    data : {
                        "_token": "{{ csrf_token() }}",
                        'ids' : del_ids
                    },
                    url : '{{url('/')."/remove-selected-employee"}}',
                    success : function (response) {
                        hideOverlay();
                        $.notify(" Employee has been Deleted.", {animationType:"scale", align:"right", type: "success", icon:"check"});
                        // setTimeout(function () {
                        //     location.reload();
                        // },1000);
                        location.reload();

                    },
                    error : function (response) {
                        hideOverlay();
                        $.notify(" Oops... Something went wrong!", {animationType:"scale", align:"right", type: "danger", icon:"close"});
                    }
                });
            }
        });

        $('.addInfoButton').click(function () {
            $('#form1').prop('action','{{route('seller.add_employees')}}');
            $('#data_div').hide();
            $('#form_div').fadeIn();
            isEditing = false;
            $('#passwordHint').hide();
        });
        $('#cancelButton').click(function () {
            $('#form1').trigger("reset");
            $('#form_div').hide();
            $('#data_div').fadeIn();
        });
        $('#example1').on('click','.remove_data',function(){
            var that=$(this);

            if (window.confirm("Are you sure?")) {
                showOverlay();
                $.ajax({
                    url : '{{url('/')."/delete-employees"}}/'+that.data('id'),
                    success : function (response) {
                        hideOverlay();

                        $.notify(" Employee has been deleted.", {animationType:"scale", align:"right", type: "success", icon:"check"});
                        $('#row'+that.data('id')).remove();
                    },
                    error : function (response) {
                        hideOverlay();
                        $.notify(" Oops... Something went wrong!", {animationType:"scale", align:"right", type: "danger", icon:"close"});
                    }
                });
            }
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
                    // $('#password').val(info.password);
                    $('#data_div').hide();
                    $('#form_div').fadeIn();
                    isEditing = true;
                    $('#passwordHint').show();
                    hideOverlay();
                },
                error : function (response) {
                    hideOverlay();
                    $.notify(" Oops... Something went wrong!", {animationType:"scale", align:"right", type: "danger", icon:"close"});
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


        $('#form1').validate({
            rules: {
                employee_name: {
                    required: true
                },
                mobile: {
                    required: true,
                    minlength: 10,
                    maxlength : 10
                },
                email: {
                    required: true,
                    email :true
                },
                password: {
                    required: true,
                },
                'permission[]': {
                    required: true,
                },
            },
            messages: {
                employee_name: {
                    required: "Please Enter a Employee Name",
                },
                mobile: {
                    required: "Please Enter a Mobile Number",
                    minlength: "Your mobile number must be 10 digits",
                    maxlength: "Your mobile number must be 10 digits",
                },
                email: {
                    required: "Please Enter Email",
                    email : "Please Enter Valid Email"
                },
                password: {
                    required: "Please Enter Password",
                },
                'permission[]': {
                    required: "Please Select Atlease one",
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
    });

</script>
</body>

</html>
