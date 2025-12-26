<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Blog Management | {{env('appTitle')}} </title>

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
                        <h1>Manage Blog</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active">Blog</li>
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
                                <h3 class="card-title">Manage Blog</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" id="quickForm" action="{{route('add_blog')}}" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="id" id="hid" value="">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="link">Blog Title</label>
                                                <input type="text" name="title" class="form-control" id="title" placeholder="Title of Blog">
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
                                                <label for="link">Blog Description</label>
                                                <input type="textarea" name="description" class="form-control" id="description" placeholder="Description of Blog">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="link">Blog Name</label>
                                                <input type="text" name="by_name" class="form-control" id="by_name" placeholder="Name of Blog">
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="from_url">Blog Name</label>
                                                <input type="text" name="from_url" class="form-control" id="from_url" placeholder="url">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="number">Description</label>
                                                <textarea name="long_description" id="editor1" rows="10" cols="80">
                                                </textarea>
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
                                <h3 class="card-title">All Blog</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <button type="button" id="addDataButton" class="btn btn-warning"><i class="fa fa-plus"></i> Add Blog</button><br><br>
                                <div class="table-responsive">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th>Sr.No</th>
                                            <th>Image</th>
                                            <th>Title & Name </th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php($cnt=1)
                                        @foreach($blogs as $a)
                                            <tr id="row{{$a->id}}">
                                                <td>{{$cnt++}}</td>
                                                <td><img src="{{asset($a->image)}}" style="height: 100px;"></td>
                                                <td>
                                                    {{$a->title}}<br>
                                                    {{$a->by_name}}
                                                </td>
                                                <td>{{$a->description}}</td>
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
<script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>

<script type="text/javascript">
    CKEDITOR.replace( 'editor1' );
    var base_path='{{url('/')}}/';
    $(document).ready(function () {
        $('#addDataButton').click(function () {
            $('#quickForm').prop('action','{{route('add_blog')}}');
            showForm();
        });
        $('#cancelButton').click(function () {
            showData();
            $('#quickForm').trigger('reset');
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
                        url : '{{url('/')."/delete-blog"}}/'+that.data('id'),
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
                url : '{{url('/')."/blog-status"}}',
                success: function (response) {
                    var info=JSON.parse(response);
                    showSuccess('Changed','Status Changed successfully');
                    hideOverlay();
                },
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
                url : '{{url('/')."/modify-blog/"}}' + that.data('id'),
                success: function (response) {
                    var info=JSON.parse(response);
                    $('#quickForm').prop('action','{{route('update_blog')}}');
                    $('#hid').val(info.id);
                    $('#title').val(info.title);
                    $('#from_url').val(info.from_url);
                    $('#description').val(info.description);
                    CKEDITOR.instances['editor1'].setData(info.long_description);
                    $('#by_name').val(info.by_name);
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
        $.validator.setDefaults({
            submitHandler: function () {
                return true;
            }
        });
        $('#quickForm').validate({
            rules: {
                link: {
                    required: true,
                },
            },
            messages: {
                link: {
                    required: "Please enter the link",
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
