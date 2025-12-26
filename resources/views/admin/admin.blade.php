<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Admin | {{env('appTitle')}} </title>
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
                        <h1>Manage Admin</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active">Admin</li>
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
                                <h3 class="card-title">Manage Admin</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" id="quickForm" action="{{route('add_administrator')}}" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="id" id="hid" value="">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">Admin Name</label>
                                                <input type="text" name="name" class="form-control" id="name" placeholder="Enter Name of Admin">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="type">Admin Type</label>
                                                <select type="text" name="type" class="form-control" id="type">
                                                    <option value="admin">Administrator</option>
                                                    <option value="user">Admin User</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">Email Address</label>
                                                <input type="email" name="email" class="form-control" id="email" placeholder="Email Address">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="mobile">Contact Number</label>
                                                <input type="number" name="mobile" class="form-control" id="mobile" placeholder="Contact Number">
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
                                                <label for="image">Choose Image</label>
                                                <input type="file" name="image" class="form-control" id="image">
                                                <img src="" id="imageSource" style="max-height: 100px;max-width: 100px;">
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
                    <!--/.col (left) -->
                    <!-- right column -->
                    <div class="col-md-6">

                    </div>
                    <!--/.col (right) -->
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
                                <h3 class="card-title">All Admin List</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <button type="button" id="addDataButton" class="btn btn-info btn-sm"><i class="fa fa-plus"></i> Add Admin</button><br><br>
                                <div class="table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Sr.No</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Password</th>
                                        <th>Status</th>
                                        <th>Rights</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php($cnt=1)
                                    @foreach($admin as $a)
                                        <tr id="row{{$a->id}}">
                                            <td>{{$cnt++}}</td>
                                            <td><img src="{{asset($a->image)}}" style="height: 100px;"></td>
                                            <td>{{$a->name}}</td>
                                            <td>{{$a->email}}</td>
                                            <td>{{$a->mobile}}</td>
                                            <td>******</td>
                                            <td>
                                                <input class="change_status" data-id="{{$a->id}}" type="checkbox" data-toggle="switchbutton" {{$a->status=="y"?"checked":""}} data-onstyle="success" data-onlabel="Allowed" data-offlabel="Blocked" data-offstyle="danger">
                                            </td>
                                            <td>
                                                @if($a->type=="user")
                                                <a href="javascript:;" class="change_rights" data-id="{{$a->id}}"><i class="fa fa-eye"></i></a>
                                                @endif
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

<div class="modal fade bd-example-modal-lg" id="modal-default" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Change Rights</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-striped">
                    <tr>
                        <th>Sr.No</th>
                        <th>Module</th>
                        <th>Insert</th>
                        <th>Delete</th>
                        <th>Modify</th>
                    </tr>
                    <?php $cnt=1; foreach ($master as $m) { ?>
                    <tr>
                        <td><?= $cnt++; ?></td>
                        <td><input type="button" id="rights<?php echo $m->id; ?>" data-id="<?php echo  $m->id; ?>" data-color="red" class="btn btn-danger all_rights" value="<?php echo  $m->title; ?>" style="margin: 5px;"></td>
                        <td><input type="checkbox" id="ins<?= $m->id; ?>"></td>
                        <td><input type="checkbox" id="del<?= $m->id; ?>"></td>
                        <td><input type="checkbox" id="modi<?= $m->id; ?>"></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" id="save_rights_btn" class="btn btn-primary waves-effect waves-light">Save Rights</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

@include('admin.pages.scripts')

<script type="text/javascript">
    var base_path='{{url('/')}}/';
    $(document).ready(function () {
        $('#addDataButton').click(function () {
            $('#quickForm').prop('action','{{route('add_administrator')}}');
            showForm();
        });
        $('#cancelButton').click(function () {
            showData();
            $('#quickForm').trigger('reset');
        });
        $('.all_rights').click(function () {
            var that=$(this);
            if(that.data('color')=='white')
            {
                that.data('color','red');
                that.removeClass('btn-success');
                that.addClass('btn-danger');
            }
            else{
                that.data('color','white');
                that.removeClass('btn-danger');
                that.addClass('btn-success');
            }
        });
        $('#save_rights_btn').click(function () {
            showOverlay();
            var ins,del,modi;
            var rights=Array();
            $('.all_rights').each(function () {
                var that=$(this);
                if(that.data('color')=='white')
                {
                    var id=that.data('id');
                    ins=$('#ins'+id).prop('checked')?'y':'n';
                    del=$('#del'+id).prop('checked')?'y':'n';
                    modi=$('#modi'+id).prop('checked')?'y':'n';
                    rights.push(that.data('id') + "_" + ins + "_" + del + "_" + modi);
                }
            });
            $.ajax({
                type : 'post',
                data : {
                    '_token' : '{{ csrf_token() }}',
                    'admin' : admin_id,
                    'rights' : rights
                },
                url : base_path + 'save_rights',
                success : function (response) {
                    showSuccess('Rights Saved','Admin Rights saved Successfully');
                    $('#modal-default').modal('hide');
                    hideOverlay();
                },
                error : function () {
                    showError('Error','Something went wrong please try again..');
                    hideOverlay();
                }
            });
        });
        $('#example1').on('click','.change_rights',function () {
            showOverlay();
            $('.all_rights').removeClass('btn-success');
            $('.all_rights').addClass('btn-danger');
            admin_id=$(this).data('id');
            $.ajax({
                type : 'get',
                url : base_path + 'get_administrator_rights/' + admin_id,
                success : function (response) {
                    var info=JSON.parse(response);
                    var ids=info.rights.split('^');
                    for(var i=0;i<ids.length;i++){
                        var perm=ids[i].split('_');
                        var per_id=perm[0];
                        $('#rights' + per_id).data('color','white');
                        $('#rights' + per_id).removeClass('btn-danger');
                        $('#rights' + per_id).addClass('btn-success');
                        if(perm[1]=='y')
                            $('#ins' + per_id).prop('checked',true);
                        else
                            $('#ins' + per_id).prop('checked',false);
                        if(perm[2]=='y')
                            $('#del' + per_id).prop('checked',true);
                        else
                            $('#del' + per_id).prop('checked',false);
                        if(perm[3]=='y')
                            $('#modi' + per_id).prop('checked',true);
                        else
                            $('#modi' + per_id).prop('checked',false);
                        $('#modal-default').modal('show');

                    }
                    hideOverlay();
                },
                error : function () {
                    showError('Error','Something went wrong please try again later.');
                    hideOverlay();
                }
            });
        });
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
                        url : '{{url('/')."/delete-administrator"}}/'+that.data('id'),
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
                url : '{{url('/')."/administrator-status"}}',
                error : function (response) {
                    hideOverlay();
                    Swal.fire('Oops...', 'Something went wrong!', 'error');
                }
            });
        });
        $('#example1').on('click','.modify_data',function () {
            showOverlay();
            var that=$(this);
            $.ajax({
                url : '{{url('/')."/modify-administrator/"}}' + that.data('id'),
                success: function (response) {
                    var info=JSON.parse(response);
                    $('#quickForm').prop('action','{{route('update_administrator')}}');
                    $('#hid').val(info.id);
                    $('#name').val(info.name);
                    $('#type').val(info.type);
                    $('#email').val(info.email);
                    $('#mobile').val(info.mobile);
                    $('#password').val(info.password);
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
