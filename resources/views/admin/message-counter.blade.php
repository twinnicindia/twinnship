<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Message Counter | {{env('appTitle')}} </title>

    @include('admin.pages.styles')

</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        @include('admin.pages.header')
        @include('admin.pages.sidebar')
        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Message Report</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                                <li class="breadcrumb-item active"> Message Report</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <!-- /.content -->
            <section class="content" id="data_div">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Message Report</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="clearfix"></div>
                                    <form action="#" id="order_report_form" method="post">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="seller">Select Seller</label>
                                                    <select name="seller" id="seller" class="form-control">
                                                        @foreach($sellers as $s)
                                                            <option value="{{$s->id}}">{{$s->first_name." ".$s->last_name."(".$s->code.")"}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="start_date">From Date</label>
                                                    <input id="start_date" type="date" name="start_date" class="form-control" max="{{date('Y-m-d')}}" value="{{date('Y-m-d',strtotime('-7 days'))}}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="end_date">To Date</label>
                                                    <input id="end_date" type="date" name="end_date" class="form-control" max="{{date('Y-m-d')}}" value="{{date('Y-m-d')}}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="message_type">Message Type</label>
                                                    <select name="message_type" id="message_type" class="form-control">
                                                        <option value="whatsapp">WhatsApp</option>
                                                        <option value="sms">SMS</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group mt-2">
                                                    <br>
                                                    <input type="button" class="btn btn-primary" id="getReport" value="Get Report">
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    </div>
                </div>
        </section>
            <!-- /.content -->
        <section class="content" id="order_data_div" style="display: none;">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Message Counters</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body" id="order-data">
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
    <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->
    @include('admin.pages.scripts')
    <script>
        $(document).ready(function () {

            $('#getReport').click(function () {
                showOverlay();
                $.ajax({
                    type: 'post',
                    data: {
                        '_token' : '{{csrf_token()}}',
                        'message_type' : $('#message_type').val(),
                        'start_date' : $('#start_date').val(),
                        'end_date' : $('#end_date').val(),
                        'seller' : $('#seller').val(),
                        'seller_name' : $('#seller').find(":selected").text(),
                    },
                    url : '{{route('administrator.submit-message-counter')}}',
                    success : function(response){
                        $('#order_data_div').show();
                        $('#order-data').html(response);
                        hideOverlay();
                    },
                    error : function (response) {
                        hideOverlay();
                        alert("Something went wrong");
                    }
                });
            });

            $('#order_report_form').validate({
                rules: {
                    start_date: {
                        required: true
                    },
                    end_date: {
                        required: true
                    },
                },
                messages: {
                    start_date: {
                        required: "Please Select From Date",
                    },
                    end_date: {
                        required: "Please Select To Date",
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
            $('#seller').select2({
                placeholder: "Select seller",
                allowClear: true
            });
        });
    </script>
</body>

</html>
