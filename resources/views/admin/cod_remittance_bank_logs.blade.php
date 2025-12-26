<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> COD Remittance Bank | {{env('appTitle')}} </title>
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
                            <h1>Manage COD Remittance Bank</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                                <li class="breadcrumb-item active">COD Remittance Bank</li>
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
                                    <h3 class="card-title">All COD Remittance Bank</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <form action="{{route('cod_remittance_bank')}}" method="get">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="from_date">Date</label>
                                                            <input id="from_date" type="date" name="from_date" class="form-control" value="{{ request()->from_date ?? date('Y-m-d') }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group mt-2">
                                                            <br>
                                                            <input type="submit" class="btn btn-primary" value="Filter">
                                                            <a class="btn btn-primary" href="{{route('cod_remittance_bank')}}">Reset</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="col-md-4">
                                            <form action="{{route('export-cod-remittance-bank')}}" method="get" id="ExportRemittance" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="hidden_date" id="hidden_date">
                                                <button type="button" class="btn btn-primary btn-sm float-right export_remittance"><i class="fa fa-download"></i> Export CSV</button><br><br>
{{--                                                <button type="button" id="ExportOrderButton" class="btn btn-primary btn-sm mx-0 export_order_btn" data-placement="top" data-toggle="tooltip" data-original-title="Export CSV"><i class="fa fa-upload"></i></button>--}}
                                            </form>
                                        </div>
                                    </div>
                                    <div class="table-responsive">

                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Sr.No</th>
                                                <th>PYMT_PROD_TYPE_CODE</th>
                                                <th>PYMT_MODE</th>
                                                <th>DEBIT_ACC_NO</th>
                                                <th>BNF_NAME</th>
                                                <th>BENE_ACC_NO</th>
                                                <th>BENE_IFSC</th>
                                                <th>AMOUNT</th>
                                                <th>PYMT_DATE</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php($cnt=$cod_remittance->firstItem())
                                            @forelse($cod_remittance as $c)
                                            <tr id="row{{$c->id}}">
                                                <td>{{$cnt++}}</td>
                                                <td>PAB_VENDOR</td>
                                                <td>FT</td>
                                                <td>{{$config->account_number}}</td>
                                                <td>{{$c->account_holder_name}}</td>
                                                <td>{{$c->account_number}}</td>
                                                <td>{{$c->ifsc_code}}</td>
                                                <td>{{$c->total_amount}}</td>
                                                <td>{{date('d-m-Y',strtotime($c->datetime))}}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="10" class="text-center">No Data Available</td>
                                            </tr>
                                            @endforelse
                                            </tbody>
                                        </table>
                                        {{ $cod_remittance->links() }}
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
        var base_path = '{{url(' / ')}}/';
        $(document).ready(function() {
            //get the file name
            $('#remittanceForm').submit(function () {
                $('#btnSubmitRemittance').prop('disabled',true);
            });

            $("#viewError").click(function() {
                $("#errorData").empty();
                $.ajax({
                    url: "{{ route('cod_remittance_error') }}",
                    method: "GET",
                    success: function(res) {
                        if(res.status && Array.isArray(res.data.logs) && res.data.logs.length > 0) {
                            $("#export").attr('href', `{{ route('export_csv_cod_remittance') }}?job_id=${res.data.id}&status=fail`);
                            res.data.logs.forEach((e, i) => {
                                $("#errorData").append(`
                                    <tr>
                                        <td>${++i}</td>
                                        <td>${e.created_at}</td>
                                        <td>${e.crf_id}</td>
                                        <td>${e.awb_number}</td>
                                        <td>${e.cod_amount}</td>
                                        <td>${e.remittance_amount}</td>
                                        <td>${e.utr_number}</td>
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

            $('.export_remittance').click(function () {
                $('#hidden_date').val($('#from_date').val());
                $('#ExportRemittance').submit();
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
