<!DOCTYPE html>
<html lang="zxx">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    @include('seller.pages.styles')

    <title>Warehouse | {{ $config->title }}</title>
</head>

<body>

    @include('seller.pages.header')
    @include('seller.pages.side_links')


    <div class="container-fluid">
        <div class="main-content d-flex flex-column">
            <div class="content-wrapper">

                <div class="content-inner" id="data_div">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="h4 mb-4">All Warehouses</h3>

                            <div class="d-flex justify-content-between align-items-center">
                                <div>

                                </div>
                                <div class="text-end">
                                    <div class="d-flex flex-wrap align-items-center">
                                        <span class="me-3">
                                            <a type="button"
                                                class="btn btn-sm py-2 addInfoButton btn-primary text-white fw-semibold">
                                                <i class="ri-add-fill"></i> Add New Warehouse
                                            </a>
                                        </span>
                                        <span class="me-3">
                                            <button class="btn btn-sm py-2 addInfoButton btn-primary text-white fw-semibold" id="removeAllButton" style="display: none;"><i class="fa fa-trash"></i> Remove</button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive  scroll-bar active">
                                <table id="example1" class="table">
                                    <thead class="sticky-header">
                                        <tr class="text-center rounded-10">
                                            <th style="width: 40px;">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value=""
                                                        id="selectAll">
                                                </div>
                                            </th>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Contact Person</th>
                                            <th>Address</th>
                                            <th>Support Details</th>
                                            <th>IsDefault?</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @php($cnt=1)
                                    @foreach($warehouse as $w)
                                        <tr id="row{{$w->id}}">
                                            <td><input type="checkbox" class="selectedCheck" value="{{$w->id}}"></td>
                                            <td>{{$cnt++}}</td>
                                            <td>
                                                Name : {{$w->warehouse_name}}<bR>
                                                Registered Name : {{$w->warehouse_code}}<bR>
                                                GST Number : {{$w->gst_number}}<bR>
                                            </td>
                                            <td>
                                                Name : {{$w->contact_name}}<bR>
                                                Number : {{$w->contact_number}}<bR>
                                            </td>
                                            <td>
                                                Address : <?= nl2br($w->address_line1) ?><bR>
                                                City : {{$w->city}}<bR>
                                                State : {{$w->state}}<bR>
                                                Pincode : {{$w->pincode}}<bR>
                                            </td>
                                            <td>
                                                Email : {{$w->support_email}}<bR>
                                                Phone : {{$w->support_phone}}<bR>
                                            </td>
                                            <td>
                                                <img class="default_change off_status" data-status="off" data-id="{{$w->id}}" id="off{{$w->id}}" src="{{asset('assets/sellers/images/off.png')}}" style="height: 55px;display:{{$w->default=='n'?'block':'none'}}">
                                                <img class="default_change on_status" data-status="on" data-id="{{$w->id}}" id="on{{$w->id}}" src="{{asset('assets/sellers/images/on.png')}}" style="height: 55px;display:{{$w->default=='y'?'block':'none'}}">
                                            </td>
                                            <td>
                                                <button type="button" href="javascript:;" data-toggle="tooltip" data-original-title="Edit Information" data-id="{{$w->id}}" class="btn btn-primary btn-sm modify_data"><i class="fa fa-pencil"></i></i></button>
                                                <button href="javascript:;" data-toggle="tooltip" data-original-title="Remove Information" data-id="{{$w->id}}" class="btn btn-primary btn-sm remove_data"><i class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-inner card-row" id="form_div" style="display: none;">

                    <h4 class="mb-4 ">Warehouse List</h4>
                    <form id="form1" method="post" action="{{route('seller.add_warehouse')}}">
                        @csrf
                        <input type="hidden" id="hid" name="hid" >
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group mb-4">
                                    <label class="label">Warehouse Name(do not use special symbols)</label>
                                    <div class="form-group position-relative">
                                        <input type="text" class="form-control text-dark ps-5 h-58"
                                            placeholder="Warehouse Name" id="warehouse_name" name="warehouse_name" required>

                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-4">
                                    <label class="label">Contact Person Name</label>
                                    <div class="form-group position-relative">
                                        <input type="text" class="form-control text-dark ps-5 h-58"
                                            placeholder="Contact Person Name" id="contact_name" name="contact_name" required>

                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-4">
                                    <label class="label">Contact Number </label>
                                    <div class="form-group position-relative">
                                        <input type="text" class="form-control text-dark ps-5 h-58" placeholder="Contact Number" id="contact_number" name="contact_number">

                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-4">
                                    <label class="label">GST Number</label>
                                    <div class="form-group position-relative">
                                        <input type="text" class="form-control text-dark ps-5 h-58"
                                            placeholder="GST Number" id="gst_number" name="gst_number">

                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group mb-4">
                                    <label class="label">Warehouse Address 1</label>
                                    <div class="form-group position-relative">
                                        <textarea type="text" class="form-control" rows="5" placeholder="Address Line 1" id="address" name="address" required></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="form-group mb-4">
                                    <label class="label">Warehouse Address 2</label>
                                    <div class="form-group position-relative">
                                        <textarea type="text" class="form-control" rows="5" placeholder="Address Line 2" id="address2" name="address2"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group mb-2">
                                    <label class="label">Pincode</label>
                                    <div class="form-group position-relative">
                                        <input type="number" class="form-control text-dark ps-5 h-58"
                                            placeholder="PIN Code" id="pincode" name="pincode" required>

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="label">City</label>
                                    <div class="form-group position-relative">
                                        <input type="text" class="form-control text-dark ps-5 h-58"
                                               placeholder="City" id="city" name="city" required>

                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group mb-4">
                                    <label class="label">State</label>
                                    <div class="form-group position-relative">
                                        <input type="text" class="form-control text-dark ps-5 h-58" placeholder="State" id="state" name="state" required>

                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-4">
                                    <label class="label">Country</label>
                                    <div class="form-group position-relative">
                                        <select class="custom-select" id="country" name="country" required>
                                            <option value="">Country</option>
                                            <option value="India" selected>India</option>
                                            <option value="Russia">Russia</option>
                                            <option value="USA">USA</option>
                                        </select>

                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-4">
                                    <label class="label">Support Email</label>
                                    <div class="form-group position-relative">
                                        <input type="text" class="form-control text-dark ps-5 h-58"
                                               placeholder="Support Email" id="support_email" name="support_email">

                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group mb-4">
                                    <label class="label">Support Phone</label>
                                    <div class="form-group position-relative">
                                        <input type="text" class="form-control text-dark ps-5 h-58"
                                               placeholder="Support Phone" id="support_phone" name="support_phone">
                                    </div>
                                </div>
                            </div>
                            <div style="overflow:auto;">
                                <div style="float:right;">
                                    <button type="button" class="btn btn-danger" id="cancelButton">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Modal -->
                <div class="modal fade" id="bulkupload" tabindex="-1" aria-labelledby="bulkupload" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content justify-content-center">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="bulkupload">Import warehouse via CSV</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center">
                                <a href="#" class="btn btn btn-primary"><i class="ri-download-line"></i>Download Sample
                                    CSV
                                    File</a>
                            </div>
                            <div class="form-group  text-center">
                                <div class="form-control h-100 w-80 text-center position-relative p-5 p-lg-5">
                                    <div class="product-upload">
                                        <label for="file-upload" class="file-upload mb-0">
                                            <i class="ri-upload-cloud-2-line fs-2 text-gray-light"></i>
                                            <span class="d-block fw-semibold text-body">Drop files
                                                here or click to upload.</span>
                                        </label>
                                        <input id="file-upload" type="file">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger text-white"
                                    data-bs-dismiss="modal">Cancle</button>
                                <button type="button" class="btn btn-primary text-white">Upload
                                    File</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end Modal -->
            </div>
        </div>
    </div>

    @include('seller.pages.scripts')
    <script>
        var del_ids=[];
        $(document).ready(function () {

            $(document).on('keypress','#gst_number',function(e){
                if($(e.target).prop('value').length>=15){
                    if(e.keyCode!=32)
                    {return false}
                }})

            $(document).on('keypress','#support_phone',function(e){
                if($(e.target).prop('value').length>=10){
                    if(e.keyCode!=32)
                    {return false}
                }})

            $(document).on('keypress','#contact_number',function(e){
                if($(e.target).prop('value').length>=10){
                    if(e.keyCode!=32)
                    {return false}
                }})

            $(document).on('keypress','#pincode',function(e){
                if($(e.target).prop('value').length>=6){
                    if(e.keyCode!=32)
                    {return false}
                }})

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
            $('#pincode').blur(function () {
                var that=$(this);
                if(that.val().trim().length===6){
                    that.removeClass('invalid');
                    showOverlay();
                    $.ajax({
                        type : 'get',
                        url : '{{url('/')}}' + '/pincode-detail/' + that.val(),
                        success : function (response) {
                            hideOverlay();
                            var info=JSON.parse(response);
                            if(info.status=="Success"){
                                $('#city').val(info.city);
                                $('#state').val(info.state);
                                $('#country').val(info.country);
                            }else{
                                $.notify(" Oops... Invalid Pincode", {animationType:"scale", align:"right", type: "danger", icon:"close"});
                                // that.val('');
                            }
                        },
                        error : function (response) {
                            hideOverlay();
                        }
                    });
                }
                else{
                    that.addClass('invalid');
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
                        url : '{{url('/')."/remove-selected-warehouse"}}',
                        success : function (response) {
                            hideOverlay();
                            // $.notify(" Warehouse has been Deleted.", {animationType:"scale", align:"right", type: "success", icon:"check"});
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
                $('#data_div').hide();
                $('#form1').prop('action','{{route('seller.add_warehouse')}}');
                $('#form_div').fadeIn();
            });
            $('#cancelButton').click(function () {
                $('#form1').trigger("reset");
                $('#form_div').hide();
                $('#data_div').fadeIn();
            });
            $('#example1').on('click','.remove_data',function(){
                var that=$(this);
                if (window.confirm("Are you sure want to Delete?")) {
                    showOverlay();
                    $.ajax({
                        url : '{{url('/')."/delete-warehouse"}}/'+that.data('id'),
                        success : function (response) {
                            hideOverlay();
                            $.notify(" Warehouse has been Deleted.", {animationType:"scale", align:"right", type: "success", icon:"check"});
                            $('#row'+that.data('id')).remove();
                        },
                        error : function (response) {
                            hideOverlay();
                            $.notify(" Oops... Something went wrong!", {animationType:"scale", align:"right", type: "danger", icon:"close"});
                        }
                    });
                }
            });

            $('#example1').on('click','.default_change',function(){
                var that=$(this);
                if(that.data('status')=='off'){
                    if (window.confirm("Are you sure want to Change Deafult Warehouse?")) {
                        showOverlay();
                        $.ajax({
                            url : '{{url('/')."/make-default"}}/'+that.data('id'),
                            success : function (response) {
                                hideOverlay();
                                $.notify(" Deafult Warehouse change successfully.", {animationType:"scale", align:"right", type: "success", icon:"check"});
                                $('.on_status').hide();
                                $('.off_status').show();
                                $('#off'+that.data('id')).hide();
                                $('#on'+that.data('id')).fadeIn();
                                // $('#row'+that.data('id')).remove();
                            },
                            error : function (response) {
                                hideOverlay();
                                $.notify(" Oops... Something went wrong!", {animationType:"scale", align:"right", type: "danger", icon:"close"});
                            }
                        });
                    }
                }
            });

            $('#warehouse_name').change(function () {
                if($('#warehouse_name').val()!==''){
                    $.ajax({
                        type : 'get',
                        url : '{{url('/')}}' + '/check-warehouse-email/' + $('#warehouse_name').val(),
                        success : function (response) {
                            var info=JSON.parse(response);
                            if(info.status=='false'){
                                var warehouseName = $('#warehouse_name').val();
                                $.notify( " " + warehouseName + " Warehouse Name is already exists", {animationType:"scale", align:"right", type: "danger", icon:"close"});
                                $('#warehouse_name').addClass('is-invalid');
                                $('#warehouse_name').focus();
                            }
                        },
                        error : function (response) {
                            $.notify(" Oops... Something went wrong!", {
                                blur: 0.2,
                                delay: 0,
                                verticalAlign: "top",
                                animationType: "scale",
                                align: "right",
                                type: "danger",
                                icon: "close"
                            });
                        }
                    });
                }
            });

            $('#example1').on('click','.modify_data',function () {
                showOverlay();
                var that=$(this);
                $.ajax({
                    url : '{{url('/')."/modify-warehouse/"}}' + that.data('id'),
                    success: function (response) {
                        var info=JSON.parse(response);
                        $('#form1').prop('action','{{route('update_warehouse')}}');
                        $('#hid').val(info.id);
                        $('#warehouse_name').val(info.warehouse_name);
                        $('#contact_number').val(info.contact_number);
                        $('#contact_name').val(info.contact_name);
                        $('#gst_number').val(info.gst_number);
                        $('#address').val(info.address_line1);
                        $('#address2').val(info.address_line2);
                        $('#city').val(info.city);
                        $('#state').val(info.state);
                        $('#pincode').val(info.pincode);
                        $('#support_email').val(info.support_email);
                        $('#support_phone').val(info.support_phone);
                        $('#country').val(info.country);
                        // $('#code').val(info.code.includes("+") ? info.code : "+"+ info.code);
                        $('#data_div').hide();
                        $('#form_div').fadeIn();
                        hideOverlay();
                    },
                    error : function (response) {
                        hideOverlay();
                        $.notify(" Oops... Something went wrong!", {animationType:"scale", align:"right", type: "danger", icon:"close"});
                    }
                });
            });


            $('#form1').validate({
                rules: {
                    warehouse_name: {
                        required: true
                    },
                    contact_name: {
                        required: true
                    },
                    contact_number: {
                        required: true,
                        minlength: 10,
                        maxlength: 10
                    },
                    address: {
                        required: true
                    },
                    pincode: {
                        required: true,
                        minlength: 6,
                        maxlength : 6,
                    },
                    city: {
                        required: true
                    },
                    state: {
                        required: true
                    },
                    country: {
                        required: true
                    },

                },

                messages: {
                    warehouse_name: {
                        required: "Please Enter a Warehouse Name",
                    },
                    contact_name: {
                        required: "Please Enter a Contact Person Name",
                    },
                    contact_number: {
                        required: "Please Enter a Contact Number",
                        minlength: "Your mobile number must be 10 digits",
                        maxlength: "Your mobile number must be 10 digits"
                    },
                    address: {
                        required: "Please Enter Address",
                    },
                    pincode: {
                        required: "Please Enter Pincode",
                        minlength: "Your Pincode number must be 6 digits",
                        maxlength: "Your Pincode number must be 6 digits",
                    },
                    city: {
                        required: "Please Enter City",
                    },
                    state: {
                        required: "Please Enter State",
                    },
                    country: {
                        required: "Please Enter Country",
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

            $('#warehouseBtnSubmit').click(function () {
                showOverlay();
            });

            $(document).ready(function () {
                $('#selectAll').change(function () {
                    $('.selectedCheck').prop('checked', $(this).prop('checked'));
                });

                $('.selectedCheck').click(function () {
                    var allChecked = $('.selectedCheck:checked').length === $('.selectedCheck').length;
                    $('#selectAll').prop('checked', allChecked);
                });
            });
        });
    </script>
</body>

</html>
