<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Recharge Requests | {{env('appTitle')}} </title>

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
                        <h1>Recharge Requests</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active">Recharge Requests</li>
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
                                <h3 class="card-title">All Recharge Requests</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                            <div class="table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Sr.No</th>
                                        <th>Seller</th>
                                        <th>Amount</th>
                                        <th>Type</th>
                                        <th>UTR Number</th>
                                        <th>Approve??</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php($cnt=1)
                                    @foreach($recharge as $a)
                                        <tr id="row{{$a->id}}">
                                            <td>{{$cnt++}}</td>
                                            <td>{{$a->first_name.' '.$a->last_name."($a->code)"}}</td>
                                            <td>{{$a->amount}}</a></td>
                                            <td>{{$a->type=='neft' ? 'NEFT' : 'COD'}}</a></td>
                                            <td>{{$a->utr_number!=''? $a->utr_number : '-'}}</a></td>
                                            <td>
                                                <!-- <input class="change_status" data-id="{{$a->id}}" type="checkbox" data-toggle="switchbutton" {{$a->status=="y"?"checked":""}} data-onstyle="success" data-onlabel="Allowed" data-offlabel="Blocked" data-offstyle="danger"> -->
                                                <button type="button" title="Accept" class="btn btn-primary btn-sm mx-0 acceptRequest" data-id="{{$a->id}}" data-placement="top" data-toggle="tooltip" data-original-title="Accept">Accept</button>
                                                <button type="button" title="Declined" class="btn btn-danger btn-sm mx-0 declinedRequest" data-id="{{$a->id}}" data-placement="top" data-toggle="tooltip" data-original-title="Cancel Order">Declined</button>
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

<script type="text/javascript">
    var base_path='{{url('/')}}/';
    $(document).ready(function () {
        // $('#example1').on('change','.change_status',function(){
        //     var that=$(this);
        //     if(confirm('Are you sure to approve this payment??')){
        //         showOverlay();
        //         $.ajax({
        //             method : 'post',
        //             data : {
        //                 '_token' : '{{ csrf_token() }}',
        //                 'id' : that.data('id'),
        //             },
        //             url : '{{url('/')."/administrator/approve-neft"}}',
        //             success: function (response) {
        //                 var info=JSON.parse(response);
        //                 hideOverlay();
        //                 showSuccess('Changed','Payment Approved successfully');
        //                 location.reload();
        //             },
        //             error : function (response) {
        //                 hideOverlay();
        //                 Swal.fire('Oops...', 'Something went wrong!', 'error');
        //             }
        //         });
        //     }
        // });

        $('#data_div').on('click','.declinedRequest',function(){
            var that=$(this);
            if(confirm('Are you sure to declined this Payment??')){
                showOverlay();
                $.ajax({
                    method : 'post',
                    data : {
                        '_token' : '{{ csrf_token() }}',
                        'id' : that.data('id'),
                        'status' : 'n',
                    },
                    url : '{{url('/')."/administrator/approve-neft"}}',
                    success: function (response) {
                        var info=JSON.parse(response);
                        hideOverlay();
                        showSuccess('Changed','Request Declined successfully');
                        location.reload();
                    },
                    error : function (response) {
                        hideOverlay();
                        Swal.fire('Oops...', 'Something went wrong!', 'error');
                    }
                });
            }
        });
        $('#data_div').on('click','.acceptRequest',function(){
            var that=$(this);
            if(confirm('Are you sure to approve this Payment??')){
                showOverlay();
                $.ajax({
                    method : 'post',
                    data : {
                        '_token' : '{{ csrf_token() }}',
                        'id' : that.data('id'),
                        'status' : 'y',
                    },
                    url : '{{url('/')."/administrator/approve-neft"}}',
                    success: function (response) {
                        var info=JSON.parse(response);
                        hideOverlay();
                        showSuccess('Changed','Payment Approved successfully');
                        location.reload();
                    },
                    error : function (response) {
                        hideOverlay();
                        Swal.fire('Oops...', 'Something went wrong!', 'error');
                    }
                });
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
