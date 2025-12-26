<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Glossary Management | {{env('appTitle')}} </title>

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
                        <h1>Manage Glossary</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active"> Glossary</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content" id="form_div" style="">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- jquery validation -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Manage Glossary</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" id="quickForm" action="{{route('web.add_glossary')}}" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="id" id="hid" value="">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="title">Title</label>
                                                <input type="text" name="title" class="form-control" id="title" value="{{$glossary->title ?? ""}}" placeholder="title">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name">Name</label>
                                                <input type="text" name="name" class="form-control" id="name" value="{{$glossary->name ?? ""}}" placeholder="Name">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="image">Choose Profile Image</label>
                                                <input type="file" name="image" class="form-control" id="image">
                                                <img src="{{asset($glossary->image ?? '')}}" id="imageSource" style="max-height: 100px;max-width: 100px;">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="image1">Choose Content Image</label>
                                                <input type="file" name="image1" class="form-control" id="image1">
                                                <img src="{{asset($glossary->image1 ?? ''   )}}" id="imageSource1" style="max-height: 100px;max-width: 100px;">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="description">Glossary Content</label>
                                                <textarea name="description" id="description"rows="10" cols="80">
                                                    {{$glossary->description ?? ""}}
                                                </textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="wpr_description">WPR Content</label>
                                                <textarea name="wpr_description" id="wpr_description"rows="10" cols="80">
                                                    {{$glossary->wpr_description ?? ""}}
                                                </textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="lease_description">Lease Content</label>
                                                <textarea name="lease_description" id="lease_description"rows="10" cols="80">
                                                    {{$glossary->lease_description ?? ""}}
                                                </textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="guide_description">Guide Content</label>
                                                <textarea name="guide_description" id="guide_description"rows="10" cols="80">
                                                    {{$glossary->guide_description ?? ""}}
                                                </textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="storage_description">Storage Content</label>
                                                <textarea name="storage_description" id="storage_description"rows="10" cols="80">
                                                    {{$glossary->storage_description ?? ""}}
                                                </textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="privacypolicy">Privacy Policy</label>
                                                <textarea name="privacypolicy" id="privacypolicy"rows="10" cols="80">
                                                    {{$glossary->privacypolicy ?? ""}}
                                                </textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="termcondiction">Term & Condiction</label>
                                                <textarea name="termcondiction" id="termcondiction"rows="10" cols="80">
                                                    {{$glossary->termcondiction ?? ""}}
                                                </textarea>
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
<script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>

<script type="text/javascript">
    var base_path='{{url('/')}}/';
    CKEDITOR.replace( 'description' );
    CKEDITOR.replace( 'wpr_description' );
    CKEDITOR.replace( 'lease_description' );
    CKEDITOR.replace( 'guide_description' );
    CKEDITOR.replace( 'storage_description' );
    CKEDITOR.replace( 'privacypolicy' );
    CKEDITOR.replace( 'termcondiction' );
    $(document).ready(function () {
        $('#addDataButton').click(function () {
            $('#quickForm').prop('action','{{route('web.add_glossary')}}');
            showForm();
        });
        $('#cancelButton').click(function () {
            showData();
            $('#quickForm').trigger('reset');
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
                icon: {
                    required: true,
                },
            },
            messages: {
                link: {
                    required: "Please enter the link",
                },
                icon: {
                    required: "Please choose the icon",
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
