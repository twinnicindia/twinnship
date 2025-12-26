<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Weight Reconciliation | {{env('appTitle')}} </title>
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
                            <h1>Manage Weight Reconciliation</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                                <li class="breadcrumb-item active">Weight Reconciliation Revoke</li>
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
                                    <h3 class="card-title">Revoke Weight Reconciliation</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#bulkupload"><i class="fa fa-plus"></i> Import CSV</button>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <div class="modal fade" id="bulkupload" tabindex="-1" role="dialog" aria-labelledby="bulkuploadTitle" aria-hidden="true">
                <div class="modal-dialog modal-md" role="document">
                    <div class="modal-content" id="fulfillment_info">
                        <form method="post" action="{{route('import_revoke_csv_weight_reconciliation')}}" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="mySmallModalLabel">Bulk Upload Orders</h5>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-sm-12 pb-10 mb-2">
                                        Download sample order upload file : <a class="text-info" href="{{url('assets/admin/WeightReconRevoke.csv')}}">Download</a>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="m-b-10">
                                            <div class="input-group mb-3">
                                                <div class="custom-file">
                                                    <input type="file" id="inputGroupFile02" class="custom-file-input" name="importFile">
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

            <div class="modal fade" id="lastError" tabindex="-1" role="dialog" aria-labelledby="lastErrorTitle" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content" id="fulfillment_info">
                        <div class="modal-header">
                            <h5 class="modal-title" id="mySmallModalLabel">View Last Log</h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12 pb-10 mb-2">
                                    <div class="row">
                                        <div class="col-sm-8" id="errorLogText"></div>
                                        <div class="col-sm-4">
                                            <a href="#" id="export" class="btn btn-primary btn-sm float-right"><i class="fa fa-download"></i> Export CSV</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="example1">
                                            <thead>
                                                <tr>
                                                    <th>Sr.No</th>
                                                    <th>Date</th>
                                                    <th>AWB Number</th>
                                                    <th>Dimension (CM)</th>
                                                    <th>Status</th>
                                                    <th>Remark</th>
                                                </tr>
                                            </thead>
                                            <tbody id="errorData">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 text-right">
                                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
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
        var base_path = '{{url('/')}}/';
        $(document).ready(function() {
            $('#seller').select2({
                placeholder: "Select seller",
                allowClear: true
            });
            $('#inputGroupFile02').on('change',function(){
                var fileName = $(this).val();
                $(this).next('.custom-file-label').html(fileName);
            })
            $('#addDataButton').click(function() {
                showForm();
            });
            $('#cancelButton').click(function() {
                showData();
                $('#quickForm').trigger('reset');
            });
            $('.remove_data').click(function() {
                var that = $(this);
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
                            url: '{{url('/')."/administrator/delete-weight_reconciliation"}}/' + that.data('id'),
                            success: function(response) {
                                hideOverlay();
                                Swal.fire(
                                    'Deleted!',
                                    'Information has been deleted.',
                                    'success'
                                );
                                // $('#row' + that.data('id')).remove();
                                location.reload();
                            },
                            error: function(response) {
                                hideOverlay();
                                Swal.fire('Oops...', 'Something went wrong!', 'error');
                            }
                        });
                    }
                })
            });

            $("#viewError").click(function() {
                $("#errorData").empty();
                $.ajax({
                    url: "{{ route('weight_reconciliation_error') }}",
                    method: "GET",
                    success: function(res) {
                        if(res.status && Array.isArray(res.data.logs) && res.data.logs.length > 0) {
                            $("#export").attr('href', `{{ route('export_csv_weight_reconciliation') }}?job_id=${res.data.id}&status=fail`);
                            res.data.logs.forEach((e, i) => {
                                $("#errorData").append(`
                                    <tr>
                                        <td>${++i}</td>
                                        <td>${e.created_at}</td>
                                        <td>${e.awb_number}</td>
                                        <td>Wt : ${e.weight} KG <br>
                                            (L * B *H) : ${e.length} * ${e.breadth} * ${e.height}<br>
                                        </td>
                                        <td>${e.status}</td>
                                        <td>${e.remark}</td>
                                    </tr>
                                `);
                            });
                        } else {
                            $("#errorData").append(`
                                <tr>
                                    <td colspan="6" class="text-center">No Error Found</td>
                                </tr>
                            `);
                        }
                        $("#errorLogText").html(res.message);
                        $("#lastError").modal('show');
                    }
                });
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
