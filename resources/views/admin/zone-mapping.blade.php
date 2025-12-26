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
                                    <div class="row">
                                        <div class="col-md-8">
                                            <form action="{{route('administrator.zoneMapping')}}" method="get">
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <input id="search" type="text" name="q" class="form-control" value="{{ request()->q ?? '' }}" placeholder="Search..">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <input type="submit" class="btn btn-primary" value="Search">
                                                            <button type="reset" class="btn btn-primary reset-search">Reset</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="col-md-4 text-right">
                                            <button type="button" class="btn btn-primary btn-sm mx-0 importZone" id="importZone" data-placement="top" data-toggle="tooltip" data-original-title="Import Excel"><i class="fa fa-download"></i></button>
                                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#addZoneMapping"><i class="fa fa-plus"></i> Add Zone Mapping</button><br><br>
                                        </div>
                                    </div>
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
                                                    <th>COD Limit</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php($cnt=$zoneMapping->firstItem())
                                                @forelse($zoneMapping as $row)
                                                <tr id="row-{{$row->id}}">
                                                    <td>{{$cnt++}}</td>
                                                    <td>{{$PartnerName[$row->courier_partner ?? null] ?? '-'}}</td>
                                                    <td>{{$row->city}}</td>
                                                    <td>{{$row->state}}</td>
                                                    <td>{{$row->has_cod}}</td>
                                                    <td>{{$row->has_dg}}</td>
                                                    <td>{{$row->has_prepaid}}</td>
                                                    <td>{{$row->has_reverse}}</td>
                                                    <td>{{$row->picker_zone}}</td>
                                                    <td>{{$row->pincode}}</td>
                                                    <td>{{$row->routing_code}}</td>
                                                    <td>{{$row->cod_limit}}</td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="12">No Data</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    {{$zoneMapping->links()}}

                                </div>
                                <!-- /.card-body -->
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <div class="modal fade" id="addZoneMapping" tabindex="-1" role="dialog" aria-labelledby="addZoneMappingTitle" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <form method="post" action="{{route('administrator.zoneMapping.add')}}" id="addZoneMappingForm">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="addZoneMappingTitle">Add Zone Mapping</h5>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="pincode">Pincode</label>
                                            <input type="number" class="form-control" name="pincode" id="pincode" placeholder="Enter pincode" minlength="6" maxlength="6">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="city">City</label>
                                            <input type="text" class="form-control" name="city" id="city" placeholder="Enter city">
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="state">State</label>
                                            <input type="text" class="form-control" name="state" id="state" placeholder="Enter state">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 text-right">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        <button class="btn btn-secondary reset" data-dismiss="modal">Close</button>
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
        <div class="modal fade" id="importZoneModel" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="{{ route('administratorZone.importZone') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="text" id="plan_id" name="plan_id" hidden>
                    <input type="text" id="seller_id" name="seller_id" hidden>
                    <div class="modal-header">
                        <h5 class="modal-title" id="mySmallModalLabel">Seller Rate Card</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="m-b-10">
                                    <div class="input-group mb-3">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="excel" name="excel">
                                            <label class="custom-file-label" for="excel">Choose file</label>
                                        </div>
                                    </div>
                                    {{--                                    <small class="text-danger">All previous rates will be removed</small>--}}
                                    <bR>
                                    <bR>
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


    @include('admin.pages.scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $("#addZoneMappingForm").validate({
                rules: {
                    city: {
                        required: true,
                    },
                    state: {
                        required: true,
                    },
                    pincode: {
                        required: true,
                    }
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                },
                submitHandler: function(form, event) {
                    event.preventDefault();
                    showOverlay();
                    $.ajax({
                        url: $(form).attr('action'),
                        method: 'post',
                        data: $(form).serialize(),
                        success: function(res) {
                            hideOverlay();
                            $("#addZoneMapping").modal('hide');
                            if (res.status == false) {
                                Swal.fire('Error', res.message, 'error');
                            } else {
                                Swal.fire('Success', res.message, 'success');
                            }
                            $(form).trigger('reset');
                        },
                        error: function(err) {
                            hideOverlay();
                            $("#addZoneMapping").modal('hide');
                            Swal.fire('Error', 'Something went wrong!', 'error');
                            $(form).trigger('reset');
                        }
                    });
                }
            });

            $("#pincode").keyup(function() {
                if(!$(this).val()) {
                    return;
                }
                $.ajax({
                    url: "{{ route('administrator.getPincode') }}",
                    method: 'GET',
                    data: { pincode: $(this).val() },
                    success: function(res) {
                        if(res.status == true) {
                            $("#city").val(res.data.District);
                            $("#state").val(res.data.State);
                        }
                    }
                });
            });

            $('.reset').click(function() {
                $("#addZoneMappingForm").trigger('reset');
            });

            $(".reset-search").click(function() {
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.has('q')) history.back();
            });
        });
        $('#importZone').click(function () {
            $('#importZoneModel').modal('show');
        });

        $('#closeModalButton').click(function() {
            $('#importZoneModel').modal('hide');
        });
    </script>

</body>

</html>
