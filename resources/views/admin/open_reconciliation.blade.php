<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Open Tickets | {{env('appTitle')}} </title>
    @include('admin.pages.styles')
</head>

<body class="hold-transition sidebar-mini">
    @php
    $keyType=array(
    'ship_related_issue' => "Shipment Related Issue",
    'shipment_related_issue' => "Shipment Related Issue",
    'pickup_related_issue' => "Pickup Related Issue",
    'weight_related_issue' => "Weight Related Issue",
    'tech_related_issue' => "Tech Related Issue",
    'billing_remittance' => "Billing & Remittance",
    );
    @endphp
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
                            <h1>Open Tickets</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                                <li class="breadcrumb-item active">Customer Support</li>
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
                                    <h3 class="card-title">All Open Tickets</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                <div class="table-responsive">
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Escalation Type</th>
                                                <th>Subject</th>
                                                <th>AWB Number</th>
                                                <th>Details</th>
                                                <th>Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(count($open_reconciliation)!=0)
                                            @foreach($open_reconciliation as $o)
                                            <tr id="row{{$o->id}}">
                                                <td>{{$o->ticket_no}}</td>
                                                <td>{{$keyType[$o->type] ?? $o->type}}</td>
                                                <td>{{$o->subject ? $o->subject : '-'}}</td>
                                                <td>{{$o->awb_number ? $o->awb_number : '-'}}</td>
                                                <td>{{$o->issue}}</td>
                                                <td>{{$o->raised}}</td>
                                                <td>
                                                    <a href='{{url("administrator/view-open_reconciliation/$o->id")}}' class="btn btn-info btn-sm mx-0" data-placement="top" data-toggle="tooltip" data-original-title="View"><i class="fa fa-eye"></i></a>
                                                </td>
                                            </tr>
                                            @endforeach
                                            @else
                                            <h4>No Open Tickets Found</h4>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
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

</body>

</html>
