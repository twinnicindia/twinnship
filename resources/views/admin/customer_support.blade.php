<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title> Customer Support | {{env('appTitle')}} </title>
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
                            <h1>Manage Customer Support</h1>
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
                                    <h3 class="card-title">All Customer Support</h3>
                                    <div class="float-right">
                                        <a href="{{route('administrator.export_escalation', request()->query())}}">
                                            <button type="button" class="btn btn-primary btn-sm mx-0" data-placement="top" data-toggle="tooltip" data-original-title="Export CSV"><i class="fa fa-upload"></i></button>
                                        </a>
                                    </div>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <form action="{{route('administrator.customer_support')}}" method="get">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="from_date">From Date</label>
                                                    <input id="from_date" type="date" name="from_date" class="form-control" value="{{ request()->from_date ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="to_date">To Date</label>
                                                    <input id="to_date" type="date" name="to_date" class="form-control" value="{{ request()->to_date ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="seller">Select Seller</label>
                                                    <select name="seller[]" id="seller" class="form-control" multiple>
                                                        @foreach($sellers as $s)
                                                            <option value="{{$s->id}}" {{ in_array($s->id, request()->seller ?? []) ? 'selected' : '' }}>{{$s->first_name." ".$s->last_name."(".$s->code.")"}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2 mt-2">
                                                <div class="form-group">
                                                    <br>
                                                    <label></label>
                                                    <input type="submit" class="btn btn-primary" value="Search">
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="table-responsive">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Seller Detail</th>
                                                    <th>Escalation Type</th>
                                                    <th style="width: 25%">Details</th>
                                                    <th>Date</th>
                                                    <th>Status</th>
                                                    <th>Sevierity</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(count($customer_support)!=0)
                                                @foreach($customer_support as $c)
                                                <tr id="row{{$c->id}}">
                                                    <td><a href="{{url("administrator/view-escalation/$c->id")}}">#{{$c->ticket_no}}</a></td>
                                                    <td>
                                                        Code : {{$c->code}}<bR>
                                                        Email : {{$c->email}}
                                                    </td>
                                                    <td>{{$keyType[$c->type] ?? $c->type}}</td>
                                                    <td>{{$c->issue}}</td>
                                                    <td>{{$c->raised}}</td>
                                                    @if($c->status == 'o')
                                                    <td class="text-danger">Open</td>
                                                    @else
                                                    <td class="text-success">Closed</td>
                                                    @endif
                                                    <td  style="color : @if($c->sevierity =='Low') #28a745 @elseif($c->sevierity =='Medium') #e2a918 @elseif($c->sevierity =='High') #ff0000 @else #bc0000 @endif">{{$c->sevierity==''? 'low' : $c->sevierity}}</td>
                                                    <td>
                                                        <a title="View Ticket" href="{{url('administrator/view-escalation/'.$c->id)}}" class="btn btn-primary btn-sm mx-0" data-placement="top" data-toggle="tooltip" data-original-title="View Ticket"><i class="fa fa-eye"></i></a>
                                                        <a title="Close Ticket" data-id="{{$c->id}}" class="close_ticket btn btn-info btn-sm mx-0" data-placement="top" data-toggle="tooltip" data-original-title="Close Ticket"><i class="fa fa-times"></i></a>
                                                        <a type="button" href="javascript:;" data-placement="top" data-toggle="tooltip" data-original-title="Escalate" data-id="{{$c->id}}" class="btn btn-danger btn-sm escalate_btn mx-0"><i class="fa fa-radiation"></i></a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                                @else
                                                <h4>No Escalation added yet</h4>
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

    <div class="modal fade" id="EsclateTicketModal" tabindex="-1" role="dialog" aria-labelledby="EsclateTicketModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Escalate Ticket</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('administrator.escalateTicket')}}" id="cloneOrderForm" method="post">
                    @csrf
                    <input type="hidden" name="ticket_id" id="ticket_id">
                    <div class="modal-body">
                        <label>Reason for Escalation</label>
                        <input type="text" name="escalate_reason" id="escalate_reason" class="form-control input-sm" placeholder="Enter Escalation Reasaon">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm cloneSubmitButton">Escalate</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    @include('admin.pages.scripts')
    <script type="text/javascript">
        $(document).ready(function(){
            $('#example1').on('click', '.close_ticket', function () {
                var that = $(this);
                if (window.confirm("Are you Sure Want to Close this Ticket ?")) {
                    showOverlay();
                    $.ajax({
                        url: '{{url('/')."/administrator/close-ticket"}}/' + that.data('id'),
                        success: function (response) {
                            hideOverlay();
                            location.reload();
                        },
                        error: function (response) {
                            hideOverlay();
                            showSuccess('Oops... Something went wrong!!');
                        }
                    });
                }
            });
            $('#example1').on('click','.escalate_btn',function(){
                $('#ticket_id').val($(this).data('id'));
                $('#EsclateTicketModal').modal('show');
            });
            $('#seller').select2({
                placeholder: "Select seller",
                allowClear: true
            });
        });
    </script>

</body>

</html>
