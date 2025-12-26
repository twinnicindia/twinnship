<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Populate Weight for All Orders | {{env('appTitle')}} </title>
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
                            <li class="breadcrumb-item active">Auto Populate Weight</li>
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
                                <h3 class="card-title">Auto Populate Weight</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" id="quickForm" action="{{route('administrator')}}" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="id" id="hid" value="">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="seller">Select Seller</label>
                                                <select name="seller" id="seller" class="form-control">
                                                    <option value="0">All Sellers</option>
                                                    @foreach($sellers as $s)
                                                        <option value="{{$s->id}}">{{$s->first_name." ".$s->last_name."(".$s->code.")"}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="autoPopulateButton" style="margin-top: 37px;">&nbsp;</label>
                                                <button type="button" id="autoPopulateButton" class="btn btn-primary">Click to Populate Weight and Dimensions</button>
                                            </div>
                                        </div>
                                    </div>
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
    var base_path='{{url('/')}}/',seller=$('#seller');
    $(document).ready(function () {
       $('#autoPopulateButton').click(function(){
            if(confirm('Are you sure to run this service??')){
                $.LoadingOverlay('show');
                $.ajax({
                    type : 'get',
                    url : '{{url('/')."/administrator/auto-populate-weight?seller="}}' + seller.val(),
                    success : function (response) {
                        $.LoadingOverlay('hide');
                        showSuccess('Success','Order details updated successfully');
                    },
                    error : function (response) {
                        $.LoadingOverlay('hide');
                        showError('Error','Something went wrong please try again later');
                    }
                });
            }
       });
    });
</script>
</body>
</html>
