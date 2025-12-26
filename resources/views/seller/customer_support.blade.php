<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Customer Support | {{$config->title}} </title>
    @include('seller.pages.styles')
    <style>
        .c-pointer {
            cursor: pointer;
        }
    </style>
    <style>
        .card-link {
            display: block;
            text-decoration: none;
            color: inherit;
            cursor: pointer;
        }
    </style>
</head>

<body>
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
    <div class="container-fluid user-dashboard">
        @include('seller.pages.header')
        @include('seller.pages.sidebar')
        <div class="content-wrapper">

            <div class="content-inner" id="support_card">
                <div class="card" style="margin-left: 60px;">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 my-2">
                                <h3 class="h4 mb-4">Customer Support</h3>
                            </div>
                            <div class="col-md-8 text-center">
                                <button class="btn btn-primary viewTicket" role="button" style="float: inline-end;">VIEW TICKETS</button>
                            </div>
                        </div>
                        <div class="row mb-4">
                        <div class="row">
                            <div class="col-lg-3 col-md-6 mb-5 mt-4">
                                <a data-value="ship_related_issue" data-name="Shipment Related Issue"
                                    class="card-link addInfoButton c-pointer">
                                    <div class="justify-content-center card-row h-100 border-0 rounded-10"
                                        style="background-color: #cacafa; font-weight: 300; font-size: 5em;">
                                        <div
                                            class="align-items-center d-flex justify-content-center mt-5 mb-5">
                                            <div class="icon transition align-items-center">
                                                <i class="ri-truck-line"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-center text-dark flex-grow-1"> <b>Shipment Related
                                            Issue.</b></p>
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-5 mt-4">
                                <a data-value="pickup_related_issue" data-name="Pickup Related Issue"
                                    class="card-link addInfoButton c-pointer">
                                    <div class="justify-content-center card-row h-100 border-0 rounded-10 "
                                        style="background-color: #cacafa; font-weight: 300; font-size: 5em;">
                                        <div
                                            class="align-items-center d-flex justify-content-center mt-5 mb-5">
                                            <div class="icon transition align-items-center">
                                                <i class="ri-archive-line"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-center text-dark flex-grow-1 mt-2"> <b>Pickup Related
                                            Issue.</b></p>
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-5 mt-4">
                                <a data-value="pickup_related_issue" data-name="Pickup Related Issue"
                                    class="card-link addInfoButton c-pointer">
                                    <div class="justify-content-center card-row h-100 border-0 rounded-10 "
                                        style="background-color: #cacafa; font-weight: 300; font-size: 5em;">
                                        <div
                                            class="align-items-center d-flex justify-content-center mt-5 mb-5">
                                            <div class="icon transition align-items-center">
                                                <i class="ri-temp-hot-fill"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-center text-dark flex-grow-1 mt-2"> <b>Weight Related
                                            Issue.</b></p>
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-5 mt-4">
                                <a data-value="tech_related_issue" data-name="Tech Related Issue"
                                    class="card-link addInfoButton c-pointer">
                                    <div class="justify-content-center card-row h-100 border-0 rounded-10 "
                                        style="background-color: #cacafa; font-weight: 300; font-size: 5em;">
                                        <div
                                            class="align-items-center d-flex justify-content-center mt-5 mb-5">
                                            <div class="icon transition align-items-center">
                                                <i class="ri-terminal-window-line"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-center text-dark flex-grow-1 mt-2"> <b>Tech Related
                                            Issue.</b></p>
                                </a>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-5 mt-4">
                                <a data-value="billing_remittance" data-name="Billing & Remittance"
                                    class="card-link addInfoButton c-pointer">
                                <div class="justify-content-center card-row h-100 border-0 rounded-10 "
                                    style="background-color: #cacafa; font-weight: 300; font-size: 5em;">
                                    <div
                                        class="align-items-center d-flex justify-content-center mt-5 mb-5">
                                        <div class="icon transition align-items-center">
                                            <i class="ri-bill-line"></i>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-center text-dark flex-grow-1 mt-2"> <b>Billing &
                                        Remittance.</b></p>
                                    </a>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-inner" id="data_div" style="display: none;">
                <div class="card" style="margin-left: 60px;">
                    <div class="card-body">
                        <h3 class="h4">Ticket
                        <div class="float-right">
                            <button type="button" class="btn btn-primary BackButton btn-sm mx-0"><i class="fal fa-arrow-alt-left"></i> Go Back</button>
                        </div>
                        </h3>
                        <br>
                        @if(count($customer_support)!=0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="example1">
                                <thead>
                                    <tr>
                                        <th>Ticket Id</th>
                                        <th>Awb Numbers</th>
                                        <th>Escalation Type</th>
                                        <th>Details</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Sevierity</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($customer_support as $c)
                                    <tr id="row{{$c->id}}">
                                        <td><a href='{{url("view-escalation/$c->id")}}'>{{$c->ticket_no}}</a></td>
                                        <td>{{$c->awb_number}}</td>
                                        <td>{{$keyType[$c->type] ?? 'Unknown Type'}}</td>
                                        <td>{{$c->issue}}</td>
                                        <td>{{$c->raised}}</td>
                                        @if($c->status == 'o')
                                        <td class="text-danger">Open</td>
                                        @else
                                        <td class="text-success">Closed</td>
                                        @endif
                                        <td  style="color : @if($c->sevierity =='Low') #28a745 @elseif($c->sevierity =='Medium') #e2a918 @elseif($c->sevierity =='High') #ff0000 @else #bc0000 @endif">{{$c->sevierity==''? 'low' : $c->sevierity}}</td>
                                        <td>
                                            <button title="Close Ticket" data-id="{{$c->id}}" class="close_ticket btn btn-primary btn-sm mx-0" data-placement="top" data-toggle="tooltip" data-original-title="Close Ticket"><i class="fa fa-times"></i></button>
                                            @if($c->sevierity == "Critical")
                                            <a type="button" href="javascript:;" data-placement="top" data-toggle="tooltip" data-original-title="Escalate" data-id="{{$c->id}}" class="btn btn-danger btn-sm escalate_btn mx-0"><i class="far fa-radiation"></i></a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <h4>No Escalation added yet</h4>
                        @endif
                    </div>
                </div>
            </div>
            <div class="container-fluid" id="form_div" style="display: none;">
                <div class="main-content d-flex flex-column">
                    <div class="content-wrapper">
                        <div class="content-inner card-row">
                            <h3 class="h4 mb-4" id="issue_title"></h3>
                            <div class="row">
                                <div class="col-lg-6 col-sm-12">
                                    <form action="{{route('seller.add_escalation')}}" method="post" id="escalation_form" enctype="multipart/form-data">
                                    @csrf
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group mb-4">
                                                    <label class="label">AWB Numbers(Comma Seprated)</label>
                                                    <div class="form-group position-relative">
                                                        <input type="text" class="form-control text-dark ps-5 h-58" id="awb_number" name="awb_number" placeholder="AWB Numbers" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="form-group mb-4">
                                                    <label class="label">Courier Partner    </label>
                                                    <div class="form-group position-relative">
                                                        <input type="text" class="form-control text-dark ps-5 h-58" id="courierPartner" name="courierPartner"
                                                            placeholder="Courier Partner    " required>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="form-group mb-4">
                                                    <label for="gst_number">Issue</label>
                                                    <select name="s_issue" required="" id="s_issue" class="custom-select">
                                                        <option value="">Choose one</option>
                                                        <option value="proof of delivery">Proof of Delivery</option>
                                                        <option value="re-attempt">Re-attempt</option>
                                                        <option value="self collect - branch address required">Self Collect - Branch Address Required</option>
                                                        <option value="forward stuck in transit">Forward Stuck In Transit</option>
                                                        <option value="forward delivery dispute">Forward Delivery Dispute</option>
                                                        <option value="rto stuck in transit">RTO Stuck In Transit</option>
                                                        <option value="rto delivery dispute">RTO Delivery Dispute</option>
                                                        <option value="hold shipment">Hold Shipment</option>
                                                        <option value="rto instruction">RTO Instruction</option>
                                                        <option value="change payment type">Change Payment Type - COD/Prepaid</option>
                                                        <option value="reverse pickup delivery issue">Reverse Pickup Delivery Issue</option>
                                                        <option value="status mismatch">Status Mismatch</option>
                                                        <option value="other">Other</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="form-group mb-4">
                                                    <label class="label">Subject</label>
                                                    <div class="form-group position-relative">
                                                        <input type="text" class="form-control text-dark ps-5 h-58" id="subject" name="subject"
                                                            placeholder="Add Your Issue" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="form-group mb-4">
                                                    <label class="label">Remarks</label>
                                                    <div class="form-group position-relative">
                                                    <textarea type="text" class="form-control" rows="3" placeholder="Enter Remark" id="remark" name="remark" required></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="form-group mb-4">
                                                    <label class="label">Add Your Issue</label>
                                                    <div class="form-group position-relative">
                                                        <textarea type="text" class="form-control" rows="5"  id="issue" name="issue"
                                                            placeholder="Add Your Issuet" required> </textarea>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="form-group mb-4">
                                                    <label class="label">Attachments(If any)</label>
                                                    <div class="form-group position-relative">
                                                        <input type="File" class="form-control text-dark ps-5 h-58" id="attachments" name="attachment[]" multiple >
                                                    </div>
                                                </div>
                                            </div>

                                            <div style="overflow:auto;">
                                                <div style="float:right;">
                                                    <button type="button" class="btn btn-danger" id="cancelButton">Cancel</button>
                                                    <button type="submit" class="btn btn-primary"
                                                        id="warehouseBtnSubmit">Submit</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-lg-6 col-sm-12 ">
                                    <img width="1000" height="813" decoding="async" src="{{url('/')}}/assets/sellers/images/person-3.png"
                                    alt="img">
                                </div>
                            </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Escalte Ticekt Modal -->
<div class="modal fade" id="EsclateTicketModal" tabindex="-1" role="dialog" aria-labelledby="EsclateTicketModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Escalate Ticket</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('seller.escalateTicket')}}" id="cloneOrderForm" method="post">
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

    @include('seller.pages.scripts')
    <script type="text/javascript">
        selectedTab='{{isset($_GET['page'])?$_GET['page']:"all_orders"}}';

        $(document).ready(function() {
            if(selectedTab =="escalate"){
                $('.viewTicket').trigger('click');
            }
            $('.escalate_btn').click(function(){
                $('#ticket_id').val($(this).data('id'));
                $('#EsclateTicketModal').modal('show');
            });


            $('.awbCheck').blur(function(){
                if($('.awbCheck').val().length > 0){
                    showOverlay();
                    $.ajax({
                        type: 'post',
                        data : {
                            '_token' : "{{csrf_token()}}",
                            'awb_number' : $('#awb_number').val()
                        },
                        url: "{{route('seller.checkAwbNumber')}}",
                        success: function (response) {
                            hideOverlay();
                            if(response.status === false){
                                $.notify(" Please provide valid awb numbers!", {
                                    animationType: "scale",
                                    align: "right",
                                    type: "danger",
                                    icon: "close"
                                });
                                $('.awbCheck').focus();
                            }
                            else{
                                $('#courierPartner').val(response.courierTitle);
                                $('#courierKeyword').val(response.courier_keyword);
                            }
                        },
                        error: function (response) {
                            hideOverlay();
                            $.notify(" Oops... Something went wrong!", {
                                animationType: "scale",
                                align: "right",
                                type: "danger",
                                icon: "close"
                            });
                        }
                    });
                }
            });

        });

            $('.addInfoButton').click(function() {
                $('#data_div').hide();
                $('#support_card').hide();
                $('#form_div').fadeIn();

                var value = $(this).data('value');
                if (value == 'ship_related_issue') {
                    $('#shipment_related_field').show();
                    $('#other_issue_field').hide();
                } else {
                    $('#other_issue_field').show();
                    $('#shipment_related_field').hide();
                }
                $('#escalation_type').val(value);
                $('#issue_title').html($(this).data('name'));
            });

            $('.viewTicket').click(function() {
                $('#form1').trigger("reset");
                $('#form_div').hide();
                $('#support_card').hide();
                $('#data_div').fadeIn();
            });

            $('.BackButton').click(function() {
                $('#support_card').fadeIn();
                $('#data_div').hide();
            });

            $('#cancelButton').click(function() {
                $('#form1').trigger("reset");
                $('#form_div').hide();
                $('#support_card').fadeIn();
            });

            $('#data_div').on('click', '.close_ticket', function () {
            var that = $(this);
            if (window.confirm("Are you Sure Want to Close this Ticket ?")) {
                showOverlay();
                $.ajax({
                    url: '{{url('/')."/close-ticket"}}/' + that.data('id'),
                    success: function (response) {
                        hideOverlay();
                        location.reload();
                    },
                    error: function (response) {
                        hideOverlay();
                        $.notify(" Oops... Something went wrong!", {
                            blur: 0.2,
                            delay: 0,
                            verticalAlign: "top",
                            animationType: "scale",
                            align: "right",
                            type: "danger",
                            icon: "close"
                        });
                    }
                });
            }
        });

            $('#escalation_form').validate({
                rules: {
                    awb_number: {
                        required: true
                    },
                    s_issue: {
                        required: true,
                    },
                    issue: {
                        required: true,
                    },
                    remark: {
                        required: true
                    },
                    subject: {
                        required: true
                    },
                },
                messages: {
                    awb_number: {
                        required: "Please Enter AWB Number",
                    },
                    s_issue: {
                        required: "Please Enter Select Issue",
                    },
                    issue: {
                        required: "Please Enter Issue Message",
                    },
                    remark: {
                        required: "Please Enter Remark",
                    },
                    subject: {
                        required: "Please Enter Subject",
                    },
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
                }
            });
    </script>
</body>

</html>
