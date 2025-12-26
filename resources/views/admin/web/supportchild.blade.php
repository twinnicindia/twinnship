<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Child Category Management | {{env('appTitle')}} </title>
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
                        <h1>Manage Child-Category</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active">Child-Category</li>
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
                                <h3 class="card-title">Manage Child-Category</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" id="quickForm" action="{{route('add-childcategory')}}" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="id" id="hid" value="">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="category">Select Category</label>
                                                <select name="category" class="form-control" id="category">
                                                    <option value="">Select Category</option>
                                                    @foreach($support as $c)
                                                        <option value="{{$c->id}}">{{$c->title}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="subcategory">Select Sub-Category</label>
                                                <select name="subcategory" class="form-control" id="subcategory">
                                                    <option value="">Select Sub-Category</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="title">Child-Category Title</label>
                                                <input type="text" name="title" class="form-control" id="title" placeholder="Enter Title of Child-Category">
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
                                                <label for="firstname">First Name</label>
                                                <input type="text" name="firstname" class="form-control" id="firstname" placeholder="Enter firstname of Child-Category">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="lastname">First Name</label>
                                                <input type="text" name="lastname" class="form-control" id="lastname" placeholder="Enter lastname of Child-Category">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="description">Description</label>
                                                <textarea name="description" id="address1"rows="10" cols="80">
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
                                <h3 class="card-title">All Child-Category</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <button type="button" id="addDataButton" class="btn btn-warning"><i class="fa fa-plus"></i> Add Child-Category</button><br><br>
                                <div class="table-responsive">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th>Sr.No</th>
                                            <th>Category</th>
                                            <th>Sub-Category</th>
                                            <th>Title</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php($cnt=1)
                                        @foreach($supportchild as $a)
                                            <tr id="row{{$a->id}}">
                                                <td>{{$cnt++}}</td>
                                                <td>{{$a->category}}</td>
                                                <td>{{$a->support_sub}}</td>
                                                <td>{{$a->title}}</td>
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
    CKEDITOR.replace( 'description' );
    var base_path='{{url('/')}}/',slug = $('#slug'),title = $('#title'), category = $('#category'),subcategory = $('#subcategory'),subCategoryId=0;
    let token = '{{csrf_token()}}';
    $(document).ready(function () {
        $('#addDataButton').click(function () {
            $('#quickForm').prop('action','{{route('add-childcategory')}}');
            showForm();
        });
        title.keyup(function () {
            slug.val(convertToSlug(title.val()));
        });
        slug.change(function () {
            title.keyup();
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
                        url : base_path + 'admin/delete-childcategory/'+that.data('id'),
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
        category.change(function () {
            if(category.val() !== ''){
                $.ajax({
                    type : 'post',
                    data : {
                        '_token' : '{{csrf_token()}}',
                        'category' : category.val()
                    },
                    url : base_path + 'admin/get-category-subcategory',
                    success : function (response) {
                        subcategory.html('<option value="">Select Sub-Category</option>');
                        for(var i=0;i< response.length;i++){
                            subcategory.append('<option value="' + response[i].id + '">' + response[i].title + '</option>');
                        }
                        if(subCategoryId !== 0)
                            subcategory.val(subCategoryId);
                    },
                    error : function () {
                        Swal.fire('Oops...', 'Something went wrong!', 'error');
                    }
                });
            }
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
                url : base_path + 'admin/status-childcategory',
                success: function (response) {
                    if(response.status === 'true')
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
                url : base_path + 'admin/modify-childcategory/' + that.data('id'),
                success: function (response) {
                    var info=JSON.parse(response);
                    $('#quickForm').prop('action','{{route('update-childcategory')}}');
                    $('#hid').val(info.id);
                    $('#title').val(info.title);
                    $('#firstname').val(info.firstname);
                    $('#lastname').val(info.lastname);
                    $('#description').val(info.description);
                    category.val(info.support_id);
                    subCategoryId = info.supportsub_id;
                    $('#imageSource').prop('src','{{url('/')}}/' + info.image);
                    category.change();
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
                $('button[type=submit]').attr('disabled','disabled');
                return true;
            }
        });
        $('#quickForm').validate({
            rules: {
                title: {
                    required: true,
                },
                category: {
                    required: true,
                },
                subcategory: {
                    required: true,
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
        function showForm() {
            $('#data_div').hide();
            $('#form_div').slideDown();
        }
        function showData() {
            $('#form_div').hide();
            $('#data_div').slideDown();
        }
    });
</script>
</body>
</html>
