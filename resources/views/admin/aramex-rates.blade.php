<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Aramex Rates | {{env('appTitle')}} </title>

    @include('admin.pages.styles')
    <style type="text/css">
        .error{
            border : 1px solid crimson;
        }
        .error:hover{
            border : 1px solid crimson;
        }
        .error:focus{
            border : 1px solid crimson;
        }
    </style>

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
                        <h1>Xindus Rates</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active">Aramex Rates</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content" id="form_div">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 col-sm-12 col-lg-12">
                        <div class="card card-primary card-outline card-tabs">
                            <div class="card-header">
                                <h3>
                                    <span>Select Seller</span>
                                </h3>
                            </div>
                            <div class="card-body">
                                <form method="post" action="{{route('administrator.save-aramex-rates')}}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Select Seller</label>
                                                <select class="form-control" id="seller" name="seller">
                                                    <option value="0">All</option>
                                                    @foreach($sellers as $s)
                                                    <!-- <option value="{{$s->id}}">{{$s->first_name." ".$s->last_name}}</option> -->
                                                    <option value="{{$s->id}}">{{$s->first_name.' '.$s->last_name."($s->code)"}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="file">Choose File</label>
                                            <input type="file" name="rates" class="form-control">
                                            <small><a href="{{asset('assets/seller/aramex_rates.csv')}}" download>Download Sample</a></small>
                                        </div>
                                        <div class="col-md-2">
                                            <label for="file">&nbsp;</label>
                                            <input type="submit" value="Submit" class="btn btn-primary form-control">
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!-- /.card -->
                        </div>
                    </div>
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <section class="content" id="form_div">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12 col-sm-12 col-lg-12">
                        <div class="card card-primary card-outline card-tabs">
                            <div class="card-header p-0 pt-1 border-bottom-0">

                            </div>
                            <div class="card-body">

                            </div>
                            <!-- /.card -->
                        </div>
                    </div>
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
    var base_path='{{url('/')}}/',seller_id=0;
    $(document).ready(function () {
        $('#seller').select2({
            // placeholder: "Select seller",
            allowClear: true
        });
    });

</script>
</body>
</html>
