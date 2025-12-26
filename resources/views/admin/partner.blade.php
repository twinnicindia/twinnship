<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Partners Management | {{env('appTitle')}} </title>

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
                        <h1>Manage Partners</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active"> Partners</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content" id="form_div" style="display: none;">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- jquery validation -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Manage Partners</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" id="quickForm" action="{{route('add_partner')}}" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="id" id="hid" value="">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="title">Partner Title</label>
                                                <input type="text" name="title" class="form-control" id="title" placeholder="Partner Title">
                                            </div>
                                        </div>
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="keyword">Partner Keyword</label>
                                                <input type="text" name="keyword" class="form-control" id="keyword" placeholder="Partner Keyword">
                                            </div>
                                        </div>
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="api_key">API Key</label>
                                                <input type="text" name="api_key" class="form-control" id="api_key" placeholder="API Key">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="ship_url">Ship URL</label>
                                                <input type="text" name="ship_url" class="form-control" id="ship_url" placeholder="Ship URL">
                                            </div>
                                        </div>
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="track_url">Track URL</label>
                                                <input type="text" name="track_url" class="form-control" id="track_url" placeholder="Track URL">
                                            </div>
                                        </div>
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="other_key">Other Key</label>
                                                <input type="text" name="other_key" class="form-control" id="other_key" placeholder="Other Key">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="weight_initial">Initial Weight</label>
                                                <input type="text" name="weight_initial" class="form-control" id="weight_initial" placeholder="Initial Weight">
                                            </div>
                                        </div>
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="extra_limit">Extra Limit</label>
                                                <input type="text" name="extra_limit" class="form-control" id="extra_limit" placeholder="Extra Limit">
                                            </div>
                                        </div>
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="image">Choose Image</label>
                                                <input type="file" name="image" class="form-control" id="image">
                                            </div>
                                        </div>
                                    </div>
                                    <br />
                                    <h6>Default Rates</h6>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="zone_a">Zone A</label>
                                                <input type="text" name="zone_a" class="form-control" id="zone_a" placeholder="Zone A">
                                            </div>
                                        </div>
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="zone_b">Zone B</label>
                                                <input type="text" name="zone_b" class="form-control" id="zone_b" placeholder="Zone B">
                                            </div>
                                        </div>
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="zone_c">Zone C</label>
                                                <input type="text" name="zone_c" class="form-control" id="zone_c" placeholder="Zone C">
                                            </div>
                                        </div>
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="zone_d">Zone D</label>
                                                <input type="text" name="zone_d" class="form-control" id="zone_d" placeholder="Zone D">
                                            </div>
                                        </div>
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="zone_e">Zone E</label>
                                                <input type="text" name="zone_e" class="form-control" id="zone_e" placeholder="Zone E">
                                            </div>
                                        </div>
                                    </div>
                                    <h6>Overweight Extra Rates</h6>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="extra_zone_a">Zone A</label>
                                                <input type="text" name="extra_zone_a" class="form-control" id="extra_zone_a" placeholder="Zone A">
                                            </div>
                                        </div>
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="extra_zone_b">Zone B</label>
                                                <input type="text" name="extra_zone_b" class="form-control" id="extra_zone_b" placeholder="Zone B">
                                            </div>
                                        </div>
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="extra_zone_c">Zone C</label>
                                                <input type="text" name="extra_zone_c" class="form-control" id="extra_zone_c" placeholder="Zone C">
                                            </div>
                                        </div>
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="extra_zone_d">Zone D</label>
                                                <input type="text" name="extra_zone_d" class="form-control" id="extra_zone_d" placeholder="Zone D">
                                            </div>
                                        </div>
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="extra_zone_e">Zone E</label>
                                                <input type="text" name="extra_zone_e" class="form-control" id="extra_zone_e" placeholder="Zone E">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="cod_charge">COD Charge</label>
                                                <input type="text" name="cod_charge" class="form-control" id="cod_charge" placeholder="COD Charge">
                                            </div>
                                        </div>
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="cod_maintenance">COD Maintenance</label>
                                                <input type="text" name="cod_maintenance" class="form-control" id="cod_maintenance" placeholder="COD Maintenance">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                    <button type="button" class="btn btn-danger" id="cancelButton">Cancel</button>
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
        <section class="content" id="add_mapping_form" style="display: none;">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- jquery validation -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Add Zone Mappings</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" id="zoneForm" action="{{route('add_zone_mapping')}}" method="post" enctype="multipart/form-data">
                                <input type="hidden" name="id" id="partner_id" value="">
                                <input type="hidden" name="keyword" id="courier_partner" value="">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="title">Choose CSV File</label>
                                                <a download="" href="{{asset('public/samples/sample_zone.csv')}}">Download Sample File</a>
                                                <input type="file" name="zones" class="form-control" id="zones" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                    <button type="button" class="btn btn-danger" id="cancelButton">Cancel</button>
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
        <section class="content" id="data_div">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">All Links</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <button type="button" id="addDataButton" class="btn btn-info btn-sm"><i class="fa fa-plus"></i> Add Partner</button><br><br>
                                <div class="table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Sr.No</th>
                                        <th>Title</th>
                                        <th>Keyword</th>
                                        <th>API Keys</th>
                                        <th>URLs</th>
                                        <th>Initial Weight</th>
                                        <th>Extra Limit</th>
                                        <th>Zone Mapping</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php($cnt=1)
                                    @foreach($partner as $a)
                                        <tr id="row{{$a->id}}">
                                            <td>{{$cnt++}}</td>
                                            <td>{{$a->title}}</td>
                                            <td>{{$a->keyword}}</td>
                                            <td>
                                                API Key : {{$a->api_key}}<br>
                                                Other Key : {{$a->other_key}}<br>
                                            </td>
                                            <td>
                                                Ship URL : {{$a->ship_url}}<br>
                                                Track URL : {{$a->track_url}}<br>
                                            </td>
                                            <td>{{$a->weight_initial}}</td>
                                            <td>{{$a->extra_limit}}</td>
                                            <td>
                                                <a href="javascript:" class="upload_mapping" data-keyword="{{$a->keyword}}" data-id="{{$a->id}}"><i class="fa fa-upload"></i></a>
                                            </td>
                                            <td>
                                                <input class="change_status" data-id="{{$a->id}}" type="checkbox" data-toggle="switchbutton" {{$a->status=="y"?"checked":""}} data-onstyle="success" data-onlabel="Allowed" data-offlabel="Blocked" data-offstyle="danger">
                                            </td>
                                            <td>
                                                <a href="javascript:;" title="Edit Information" data-id="{{$a->id}}" class="modify_data"><i class="fa fa-pencil-alt"></i></a>&nbsp;
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
        $('#addDataButton').click(function () {
            $('#quickForm').prop('action','{{route('add_partner')}}');
            showForm();
        });
        $('#cancelButton').click(function () {
            showData();
            $('#quickForm').trigger('reset');
        });
        $('#example1').on('click','.upload_mapping',function(){
            var that=$(this);
            $('#partner_id').val(that.data('id'));
            $('#courier_partner').val(that.data('keyword'));
            $('#data_div').hide();
            $('#add_mapping_form').slideDown();
        });
        $('#example1').on('click','.remove_data',function(){
            var that=$(this);
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this imaginary file!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, keep it'
            }).then((result) => {
                if (result.value) {
                    showOverlay();
                    $.ajax({
                        url : '{{url('/')."/administrator/delete-partner"}}/'+that.data('id'),
                        success : function (response) {
                            hideOverlay();
                            Swal.fire(
                                'Deleted!',
                                'Information has been deleted.',
                                'success'
                            );
                            $('#row'+that.data('id')).remove();
                        },
                        error : function (response) {
                            hideOverlay();
                            Swal.fire('Oops...', 'Something went wrong!', 'error');
                        }
                    });
                }
            })
        });
        $('#example1').on('change','.change_status',function(){
            var that=$(this);
            showOverlay();
            $.ajax({
                method : 'post',
                data : {
                  '_token' : '{{ csrf_token() }}',
                  'id' : that.data('id'),
                  'status' : that.prop('checked')?'y':'n',
                },
                url : '{{url('/')."/administrator/partner-status"}}',
                success: function (response) {
                    var info=JSON.parse(response);
                    showSuccess('Changed','Status Changed successfully');
                    hideOverlay();
                },
                error : function (response) {
                    hideOverlay();
                    Swal.fire('Oops...', 'Something went wrong!', 'error');
                }
            });
        });
        $('#example1').on('click','.modify_data',function () {
            showOverlay();
            var that=$(this);
            $.ajax({
                url : '{{url('/')."/administrator/modify-partner/"}}' + that.data('id'),
                success: function (response) {
                    console.log(response);
                    var info=JSON.parse(response);
                    $('#quickForm').prop('action','{{route('update_partner')}}');
                    $('#hid').val(info.id);
                    $('#title').val(info.title);
                    $('#keyword').val(info.keyword);
                    $('#api_key').val(info.api_key);
                    $('#other_key').val(info.other_key);
                    $('#ship_url').val(info.ship_url);
                    $('#track_url').val(info.track_url);
                    $('#weight_initial').val(info.weight_initial);
                    $('#extra_limit').val(info.extra_limit);
                    showForm();
                    hideOverlay();
                },
                error : function (response) {
                    hideOverlay();
                    Swal.fire('Oops...', 'Something went wrong!', 'error');
                }
            });
        });
        $.validator.setDefaults({
            submitHandler: function () {
                return true;
            }
        });
        $('#quickForm').validate({
            rules: {
                title: {
                    required: true,
                },
                keyword: {
                    required: true,
                },
                api_key: {
                    required: true,
                },
                other_key: {
                    required: true,
                },
                ship_url: {
                    required: true,
                },
                track_url: {
                    required: true,
                },
            },
            messages: {
                required: "This field is required"
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
