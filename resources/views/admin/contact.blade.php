<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Contact Us Management | {{env('appTitle')}} </title>

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
                        <h1>Manage Contact Us </h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active">Contact Us </li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <!-- /.content -->
        <section class="content" id="data_div">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">All Contact Us </h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="table-responsive">

                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Sr.No</th>
                                        <th>Person Detail</th>
                                        <th>Company Name</th>
                                        <th>Channel Name</th>
                                        <th>Website</th>
                                        <th>Message</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php($cnt=1)
                                    @foreach($contact as $a)
                                        <tr id="row{{$a->id}}">
                                            <td>{{$cnt++}}</td>
                                            <td>
                                                Name : {{$a->first_name}}<br>
                                                Email : {{$a->email}}<br>
                                                Contact : {{$a->mobile}}<br>
                                                Type : {{$a->type}}<br>
                                                Amount : {{$a->amount}}<br>
                                                OrderId/AWB : {{$a->order_id}}<br>
                                                Purchase Date : {{$a->purchase_date}}<br>
                                                Monthly Shipment : {{$a->monthly_shipment}}<br>
                                            </td>
                                            <td>{{$a->company_name}}</td>
                                            <td>{{$a->channel_name}}</td>
                                            <td>{{$a->website}}</td>
                                            <td>{{$a->message}}</td>
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
            $('#quickForm').prop('action','{{route('add_testimonial')}}');
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
                        url : '{{url('/')."/administrator/delete-testimonial"}}/'+that.data('id'),
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
                url : '{{url('/')."/administrator/testimonial-status"}}',
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
                url : '{{url('/')."/administrator/modify-testimonial/"}}' + that.data('id'),
                success: function (response) {
                    var info=JSON.parse(response);
                    $('#quickForm').prop('action','{{route('update_testimonial')}}');
                    $('#hid').val(info.id);
                    $('#name').val(info.name);
                    $('#designation').val(info.designation);
                    $('#description').val(info.description);
                    $('#position').val(info.position);
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
