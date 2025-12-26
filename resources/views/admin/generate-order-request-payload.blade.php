<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Generate Order Request Payload | {{env('appTitle')}} </title>
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
                            <h1>Generate Order Request Payload</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                                <li class="breadcrumb-item active">Generate Order Request Payload</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <section class="content" id="data_div">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Generate Order Request Payload</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <form action="{{route('administrator.generateOrderRequestPayload')}}" method="get">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <input type="text" name="awb_number" id="awb_number" class="form-control" placeholder="Search.." value="{{ request()->awb_number ?? '' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <input type="submit" class="btn btn-primary" value="Search">
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                        </div>

                        @if(!empty($requestPayloads))
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Order Request Payload</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="row">
                                                @foreach($requestPayloads as $awb_number => $payload)
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="awb-{{$awb_number}}">Awb Number: {{$awb_number}}</label>
                                                        <textarea type="text" name="request_payload" id="awb-{{$awb_number}}" class="form-control">{{$payload}}</textarea>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                        </div>
                        @endif
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
        $(document).ready(function() {
            $(".reset-search").click(function() {
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.has('seller_id[]')) history.back();
            });
        });
    </script>

</body>

</html>
