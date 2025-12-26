
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> File Upload Management | {{env('appTitle')}} </title>

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
                        <h1>Manage File Upload</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active">File Upload</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content" id="form_div">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- jquery validation -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Manage Career</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" id="quickForm" action="{{route('administrator.web.upload')}}" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="id" id="hid" value="">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="image">Choose Image</label>
                                                <input type="file" name="image[]" class="form-control" id="image" multiple>
                                                <img src="" id="imageSource" style="max-height: 100px;max-width: 100px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Submit</button>
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
            $('#quickForm').prop('action','{{route('add_career')}}');
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
                        url : '{{url('/')."/delete-career"}}/'+that.data('id'),
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
                url : '{{url('/')."/career-status"}}',
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
                url : '{{url('/')."/modify-career/"}}' + that.data('id'),
                success: function (response) {
                    var info=JSON.parse(response);
                    $('#quickForm').prop('action','{{route('update_career')}}');
                    $('#hid').val(info.id);
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
                title: {
                    required: true,
                },
                detail: {
                    required: true,
                },
            },
            messages: {
                title: {
                    required: "Please enter title of feature",
                },
                detail: {
                    required: "Please enter detail of feature",
                }
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
