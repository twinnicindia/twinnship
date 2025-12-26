<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Seller CRM Management | {{env('appTitle')}} </title>

    @include('admin.pages.styles')

</head>

<body class="hold-transition sidebar-mini layout-fixed">
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
                        <h1>Manage Seller CRM</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active">Seller CRM Information</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
        @if(isset($sellerInfo))
        <section class="content" id="data_div" style="display:none;">
        @else
        <section class="content" id="data_div" style="display:block;">
        @endif
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <p class="card-title h3">Seller CRM Information
                                    <div class="float-right">
                                        <a href="javascript:;" class="assignEmployee" style="display: none">
                                            <button type="button" class="btn btn-primary btn-sm mx-0" data-placement="top" data-toggle="tooltip" data-original-title="Assign Employee"><i class="fa fa-user-plus"></i></button>
                                        </a>
                                        <a href="{{route('export_seller')}}">
                                            <button type="button" class="btn btn-primary btn-sm mx-0" data-placement="top" data-toggle="tooltip" data-original-title="Export CSV"><i class="fa fa-upload"></i></button>
                                        </a>
                                    </div>
                                </p>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <form action="{{route('sellerEmployee')}}" method="get">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <input type="text" name="q" class="form-control" value="{{ request()->q ?? '' }}" placeholder="Search..">
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <input type="submit" class="btn btn-primary" value="Search">
                                                        <a class="btn btn-primary ml-1" href="{{route('sellerEmployee')}}">Reset</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <!-- <button type="button" id="addDataButton" class="btn btn-warning"><i class="fa fa-plus"></i> Add Why Choose</button><br><br> -->
                                <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="seller-data">
                                    <thead>
                                    <tr>
                                        <th><input type="checkbox" id="checkAllButton" value="y"></th>
                                        <th>Sr.No</th>
                                        <th>Seller Name</th>
                                        <th>Company Name</th>
                                        <th>Seller Details</th>
                                        <th>KYC Status</th>
                                        <th style="width:90px;">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php($cnt=1)
                                    @forelse($seller as $key => $s)
                                        <tr id="row{{$s->id}}">
                                            <td><input type="checkbox" class="selectedCheck" data-status="{{$s->verified}}" value="{{$s->id}}"></td>
                                            <td>{{$cnt++}}</td>
                                            <td>{{$s->first_name.' '.$s->last_name."($s->code)"}}</td>
                                            <td>{{$s->company_name}}</td>
                                            <td>
                                                Email :- {{$s->email}}<br>
                                                Contact :- {{$s->mobile}}<br>
                                                CRM :- {{$employee_name[$s->id]->name ?? ''}}
                                            </td>
                                            @if($s->verified == 'y')
                                                <td><span class="badge badge-success">Verified</span></td>
                                            @else
                                                <td><span class="badge badge-danger">Not Verified</span></td>
                                            @endif
                                            <td>
                                                @if($s->verified == 'y')
                                                <a href='javascript:;' class="assign_employee" data-id="{{$s->id}}" data-employee_id="{{$employee_name[$s->id]->id ?? ''}}" title="Assign CRM"><i class="fa fa-user-plus"></i></a>&nbsp;
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
{{--                                {{ $seller->links() }}--}}
                                </div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                    </div>
                </div>
            </div>
        </section>
        </section>
    </div>

    <div class="modal fade bd-example-modal-sm" id="assignEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Assign CRM <span id="cnt">1</span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="employee_id">Assign To</label>
                        <select name="employee_id" id="employee_id" class="form-control">
                            @foreach($employee as $e)
                            <option value="{{$e->id}}">{{$e->name}}</option>
                            @endforeach
                        </select>
                        <input type="hidden" name="assigned_employee" id="assigned_employee">
                        <input type="hidden" name="seller_id" id="seller_id">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" id="saveAssignEmployee" class="btn btn-primary">Save changes</button>
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
        $('#seller-data').on('click','.assign_employee',function () {
            var that = $(this);
            $('#assigned_employee').val(that.data('employee_id'));
            $('#seller_id').val(that.data('id'));
            $('#assignEmployeeModal').modal('show');
            $('#employee_id').val(that.data('employee_id'));
            //$('#progressbar').attr('aria-valuenow','10').addClass('w-10');
        });

        $('#checkAllButton').click(function () {
            var that = $(this);
            if (that.prop('checked')) {
                // $('.selectedCheck').trigger("click");
                $('.selectedCheck').prop('checked', true);
                $('.assignEmployee').fadeIn();
            }
            else{
                $('.selectedCheck').prop('checked', false);
                $('.assignEmployee').fadeOut();
            }
        });

        $('#seller-data').on('click','.selectedCheck',function () {
            var cnt = 0;
            var that = $(this);
            $('.selectedCheck:visible').each(function () {
                if($(this).prop('checked'))
                    cnt++;
            });

            if(cnt > 0){
                $('.assignEmployee').fadeIn();
            }
            else{
                $('.assignEmployee').fadeOut();
            }
        });

        $('.assignEmployee').click(function () {
            export_ids = [];
            $('.selectedCheck:visible').each(function () {
                if ($(this).prop('checked'))
                    export_ids.push($(this).val());
            });
            $('#seller_id').val(export_ids);
            $('#assignEmployeeModal').modal('show');
        });

        $('#saveAssignEmployee').click(function () {
            showOverlay();
            $.ajax({
                type: 'post',
                data: {
                    '_token': '{{csrf_token()}}',
                    'employee_id': $('#employee_id').val(),
                    // 'assigned_employee_id': $('#assigned_employee').val(),
                    'seller_ids': $('#seller_id').val()
                },
                url: '{{url('administrator/assign-seller-employee')}}',
                success: function (response) {
                    // $('#emp_name-'+$('#seller_id').val()).html($('#employee_id option:selected').text());
                    // $('#empDisplay-'+$('#seller_id').val()).attr('data-employee_id',$('#employee_id').val())
                    // $('#seller_id').val('');
                    $('#assignEmployeeModal').modal('hide');
                    hideOverlay();
                    showSuccess('Success','Employee Assigned Successfully')
                    setTimeout(function () {
                        window.location.reload();
                    },1000)
                },
                error: function (response) {
                    $('#assignEmployeeModal').modal('hide');
                    hideOverlay();
                    showError('Error','Something went wrong');
                }
            });
        });
    });
</script>
</body>

</html>
