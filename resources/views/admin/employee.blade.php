<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Employee | {{env('appTitle')}} </title>
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
                        <h1>Manage Employee</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active">Employee</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content" id="form_div" style="display: none;">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- jquery validation -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Manage Employee</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" id="quickForm" action="{{route('add_administrator_employee')}}" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="id" id="hid" value="">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">Employee Name</label>
                                                <input type="text" name="name" class="form-control" id="name" placeholder="Enter Name of Employee">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">Email Address</label>
                                                <input type="email" name="email" class="form-control" id="email" placeholder="Email Address">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="mobile">Contact Number</label>
                                                <input type="number" name="mobile" class="form-control" id="mobile" placeholder="Contact Number">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="image">Choose Image</label>
                                                <input type="file" name="image" class="form-control" id="image">
                                                <img src="" id="imageSource" style="max-height: 100px;max-width: 100px;">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="password">Choose Password</label>
                                                <input type="password" name="password" class="form-control" id="password" placeholder="Choose Password">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="department">Select Department</label>
                                                <select name="department" class="form-control" id="department" style="display: block; width: 100%">
                                                    <option value="finance">Finance</option>
                                                    <option value="sales">Sales</option>
                                                    <option value="technology">Technology</option>
                                                    <option value="operations">Operations</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group" id="seller_id_div">
                                                <label for="seller_ids" style="display: block">Select Seller</label>
                                                <select name="seller_ids[]" class="form-control" id="seller_ids" style="display: block; width: 100%" multiple>
                                                @foreach($sellers as $row)
                                                    <!-- <option value="{{$row->id}}">{{$row->first_name.' '.$row->last_name}}</option> -->
                                                    <option value="{{$row->id}}">{{$row->code}}</option>

                                                @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                    <button type="button" class="btn btn-danger" id="cancelButton">Cancel</button>
                                </div>
                            </form>
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
        <section class="content" id="data_div">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">All Employee List</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <button type="button" id="addDataButton" class="btn btn-info"><i class="fa fa-plus"></i> Add Employee</button><br><br>
                                <div class="table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Sr.No</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Department</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php($cnt=1)
                                    @foreach($employee as $a)
                                        <tr id="row{{$a->id}}">
                                            <td>{{$cnt++}}</td>
                                            <td><img src="{{asset($a->image)}}" style="height: 100px;"></td>
                                            <td>{{$a->name}}</td>
                                            <td>{{$a->email}}</td>
                                            <td>{{$a->mobile}}</td>
                                            <td>{{ucfirst($a->department)}}</td>
                                            <td>
                                                <input class="change_status" data-id="{{$a->id}}" type="checkbox" data-toggle="switchbutton" {{$a->status=="y"?"checked":""}} data-onstyle="success" data-onlabel="Allowed" data-offlabel="Blocked" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <a href="javascript:;" title="Edit Information" data-id="{{$a->id}}" class="modify_data"><i class="fa fa-pencil-alt"></i></a>&nbsp;
                                                <a href="javascript:;" title="Remove Information" data-id="{{$a->id}}" class="remove_data"><i class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
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
    var base_path='{{url('/')}}/';
    $(document).ready(function () {
        $('#addDataButton').click(function () {
            $('#quickForm').prop('action','{{route('add_administrator_employee')}}');
            showForm();
        });
        $('#cancelButton').click(function () {
            showData();
            $('#quickForm').trigger('reset');
        });

        $("#seller_ids").select2();

        $('#example1').on('click','.remove_data',function(){
            var that=$(this);
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this imaginary file!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, keep it'
            }).then((result) => {
                if (result.value) {
                    showOverlay();
                    $.ajax({
                        url : '{{url('/')."/delete-administrator-employee"}}/'+that.data('id'),
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
        $('#example1').on('change','.change_status',function(){
            var that=$(this);
            showOverlay();
            $.ajax({
                method : 'post',
                data : {
                  '_token' : '{{ csrf_token() }}',
                  'id' : that.data('id'),
                  'status' : that.prop('checked')?'y':'n',
                },
                success: function (response) {
                    var info=JSON.parse(response);
                    if(info.status=='true'){
                        showSuccess('Changed','Status Changed successfully');
                    }
                    else{
                        showError('Error',info.message);
                        setTimeout(function () {
                            location.reload();
                        },1000);
                    }
                    hideOverlay();
                },
                url : '{{url('/')."/administrator-employee-status"}}',
                error : function (response) {
                    hideOverlay();
                    Swal.fire('Oops...', 'Something went wrong!', 'error');
                }
            });
        });

        $('#department').change(function () {
            if($(this).val() === "sales")
                $('#seller_id_div').hide();
            else
                $('#seller_id_div').show();
        });

        $('#example1').on('click','.modify_data',function () {
            showOverlay();
            var that=$(this);
            $.ajax({
                url : '{{url('/')."/modify-administrator-employee/"}}' + that.data('id') + "?type=" + Math.floor(Math.random() * 100000),
                success: function (response) {
                    var info=JSON.parse(response);
                    $('#quickForm').prop('action','{{route('update_administrator_employee')}}');
                    $('#hid').val(info.id);
                    $('#name').val(info.name);
                    $('#email').val(info.email);
                    $('#mobile').val(info.mobile);
                    $('#password').val(info.password);
                    $('#department').val(info.department);
                    $('#department').trigger('change');
                    if(info.seller_ids) {
                        $('#seller_ids').val(info.seller_ids.split(','));
                        $("#seller_ids").trigger('change');
                    }
                    $('#imageSource').prop('src','{{url('/')}}/' + info.image);
                    showForm();
                    hideOverlay();
                },
                error : function (response) {
                    hideOverlay();
                    Swal.fire('Oops...', 'Something went wrong!', 'error');
                }
            });
        });

        $('#password').dblclick(function () {
            $(this).prop('type','text');
        });

        $('#password').blur(function () {
            $(this).prop('type','password');
        });

        $.validator.setDefaults({
            submitHandler: function () {
                return true;
            }
        });

        $('#quickForm').validate({
            rules: {
                email: {
                    required: true,
                    email: true,
                },
                password: {
                    required: true,
                    minlength: 5
                },
                name: {
                    required: true
                },
                mobile: {
                    required: true,
                    minlength:10
                },
            },
            messages: {
                email: {
                    required: "Please enter a email address",
                    email: "Please enter a valid email address"
                },
                password: {
                    required: "Please provide a password",
                    minlength: "Your password must be at least 5 characters long"
                },
                mobile: {
                    required: "Please provide Contact Number",
                    minlength: "Please enter a valid mobile number"
                },
                name: "Please enter name"
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
