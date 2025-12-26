<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Generate AWB Numbers | {{env('appTitle')}} </title>
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
                        <h1>Generate AWB</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active">Generate AWB</li>
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
                                <h3 class="card-title">Generate AWB</h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" id="quickForm" action="{{route('administrator.generateSellerAwb')}}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="seller">Select Seller</label>
                                                <select type="text" name="seller" class="form-control" id="seller" required>
                                                    <option value="">Select Seller</option>
                                                    @foreach($sellers as $s)
                                                    <option value="{{$s->id}}">{{$s->code."(".$s->first_name." ".$s->last_name.")"}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="partner">Select Partner</label>
                                                <select type="text" name="partner" class="form-control" id="partner" required>
                                                    <option value="">Select Partner</option>
                                                    @foreach($partners as $p)
                                                    <option value="{{$p->keyword}}">{{$p->title}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="no_awb">No. Of AWB</label>
                                                <input type="number" name="no_awb" class="form-control" id="no_awb" placeholder="No. of AWB to Generate" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Create AWBs</button>
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
                                <h3 class="card-title">Generated AWBs</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <div class="table-responsive">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>Sr.No</th>
                                        <th>Seller Name</th>
                                        <th>Partner</th>
                                        <th>No. of AWB</th>
                                        <th>Date</th>
                                        <th>Download</th>
{{--                                        <th>Used</th>--}}
{{--                                        <th>Idle</th>--}}
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php($cnt=1)
                                    @foreach($awbs as $a)
                                        <tr id="row{{$a->id}}">
                                            <td>{{$cnt++}}</td>
                                            <td>{{$a->seller_code}}</td>
                                            <td>{{$a->partner_id}}</td>
                                            <td>{{$a->no_of_awb}}</td>
                                            <td>{{date('d M Y',strtotime($a->date))}}</td>
                                            <td><a href="{{route('administrator.downloadGeneratedAWB',$a->id)}}"><i class="fa fa-file-pdf"></i></a> </td>
{{--                                            <td>6</td>--}}
{{--                                            <td>4</td>--}}
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

<div class="modal fade bd-example-modal-lg" id="modal-default" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Change Rights</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-striped">
                    <tr>
                        <th>Sr.No</th>
                        <th>Module</th>
                        <th>Insert</th>
                        <th>Delete</th>
                        <th>Modify</th>
                    </tr>
                    <?php $cnt=1; foreach ($master as $m) { ?>
                    <tr>
                        <td><?= $cnt++; ?></td>
                        <td><input type="button" id="rights<?php echo $m->id; ?>" data-id="<?php echo  $m->id; ?>" data-color="red" class="btn btn-danger all_rights" value="<?php echo  $m->title; ?>" style="margin: 5px;"></td>
                        <td><input type="checkbox" id="ins<?= $m->id; ?>"></td>
                        <td><input type="checkbox" id="del<?= $m->id; ?>"></td>
                        <td><input type="checkbox" id="modi<?= $m->id; ?>"></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" id="save_rights_btn" class="btn btn-primary waves-effect waves-light">Save Rights</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

@include('admin.pages.scripts')

<script type="text/javascript">
    var base_path='{{url('/')}}/';
    $(document).ready(function () {
        $.validator.setDefaults({
            submitHandler: function () {
                return true;
            }
        });
        $('#quickForm').validate({
            rules: {
                seller: {
                    required: true,
                },
                partner: {
                    required: true
                },
                no_awb: {
                    required: true
                }
            },
            messages: {
                seller: {
                    required: "Please select a seller"
                },
                partner: {
                    required: "Please select a courier partner"
                },
                no_awb: {
                    required: "Enter Number of AWB to Generate"
                }
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
