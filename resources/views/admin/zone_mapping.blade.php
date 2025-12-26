<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Zone Mapping | {{env('appTitle')}} </title>
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
                            <h1>Manage Zone Mapping</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                                <li class="breadcrumb-item active">Zone Mapping</li>
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
                                    <h3 class="card-title">All Zone Mapping</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <button type="button" id="addDataButton" class="btn btn-info     btn-sm"data-toggle="modal" data-target="#bulkuploadzone"><i class="fa fa-plus"></i> Add Zone Mapping</button><br><br>
                                    <div class="table-responsive">

                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Sr.No</th>
                                                <th>Courier Partner</th>
                                                <th>City</th>
                                                <th>State</th>
                                                <th>Has COD</th>
                                                <th>Has DG</th>
                                                <th>Has Prepaid</th>
                                                <th>Has Reverse</th>
                                                <th>Picker Zone</th>
                                                <th>Pincode</th>
                                                <th>Routing Code</th>
                                                <!-- <th>Action</th> -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php($cnt=1)
                                            @foreach($zone_mapping as $z)
                                            <tr id="row{{$z->id}}">
                                                <td>{{$cnt++}}</td>
                                                <td>{{$PartnerName[$z->courier_partner] ?? "NA"}}</td>
                                                <td>{{$z->city}}</td>
                                                <td>{{$z->state}}</td>
                                                <td>{{$z->has_cod}}</td>
                                                <td>{{$z->has_dg}}</td>
                                                <td>{{$z->has_prepaid}}</td>
                                                <td>{{$z->has_reverse}}</td>
                                                <td>{{$z->picker_zone}}</td>
                                                <td>{{$z->pincode}}</td>
                                                <td>{{$z->routing_code}}</td>
                                                <!-- <td>
                                                    <a href="javascript:;" title="Remove Information" data-id="{{$z->id}}" class="remove_data"><i class="fa fa-trash"></i></a>
                                                </td> -->
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    </div>
                                    {{$zone_mapping->links()}}

                                </div>
                                <!-- /.card-body -->
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <div class="modal fade" id="bulkuploadzone" tabindex="-1" role="dialog" aria-labelledby="bulkuploadzoneTitle" aria-hidden="true">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content" id="fulfillment_info">
                        <form method="post" action="{{route('add_zone_mapping')}}" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="mySmallModalLabel">Bulk Upload Zone Mapping</h5>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <!-- <div class="col-sm-12 pb-10 mb-2">
                                        Download sample order upload file : <a class="text-info" href="{{url('assets/admin/WeightReconciliation.csv')}}">Download</a>
                                    </div> -->
                                    <div class="col-sm-12">
                                        <div class="m-b-10">
                                            <div class="input-group mb-3">
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" name="zones">
                                                    <label class="custom-file-label" for="inputGroupFile02">Choose file</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 text-right">
                                        <button type="submit" class="btn btn-info btn-sm">Upload</button>
                                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

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
        $('#cancelButton').click(function () {
            showData();
            $('#quickForm').trigger('reset');
        });
        $('#example1').on('click','.remove_data',function(){
            var that=$(this);
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, keep it'
            }).then((result) => {
                if (result.value) {
                    showOverlay();
                    $.ajax({
                        url : '{{url('/')."/administrator/delete-credit_receipt"}}/'+that.data('id'),
                        success : function (response) {
                            $.LoadingOverlay('hide');
                            Swal.fire(
                                'Deleted!',
                                'Information has been deleted.',
                                'success'
                            );
                            $('#row'+that.data('id')).remove();
                        },
                        error : function (response) {
                            $.LoadingOverlay('hide');
                            Swal.fire('Oops...', 'Something went wrong!', 'error');
                        }
                    });
                }
            })
        });
    });

    function showData() {
        $('#form_div').hide();
        $('#data_div').slideDown();
    }
</script>


</body>

</html>
