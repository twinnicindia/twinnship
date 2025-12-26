<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Escalation View | {{$config->title}} </title>
    @include('seller.pages.styles')
    <style>
    /* body{
        font-size: 10px !important;
    } */
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
            <div class="card">
                <div class="card-body">
                    <div class="div container-fluid">
                        <div class="row">
                            <div class="col-md-12" >
                                <div class="card" style="margin-left: 60px;">
                                    <div class="card-body">
                                        <div class="row m-b-5">
                                            <div class="col-md-12 my-auto">
                                                <h5 class="m-0 h6">Escalation #{{$escalation->ticket_no}} - {{$keyType[$escalation->type] ?? 'Unknown Type'}}</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-lg-8">
                                        <!-- Single post-->
                                        <div class="card p-2" style="margin-left: 60px;">
                                            <div class="card-body">
                                                <div class="row m-t-20">
                                                    <div class="col-sm-12">
                                                        <div>
                                                            <div class="list-unstyled">
                                                            <?php
                                                                $ticket_attechmant = DB::table('ticket_attachment')->where('ticket_id',$escalation->id)->get();
                                                                $date = date('d-M-Y  h:i A', strtotime($escalation->raised));
                                                            ?>
                                                                <div class="media">
                                                                    <div class="media-body">
                                                                        <!-- <p class="mt-0 mb-0 small"><span class="text-muted">{{$date}}</span></p> -->
                                                                        <p class="mb-0 h5">Issue : {{$escalation->issue}} </p>
                                                                        <p class="m-0 h5">Subject : {{$escalation->subject}} </p>
                                                                        @if(!empty($escalation->awb_number))
                                                                        <p class="m-0 h5">AWB No : {{$escalation->awb_number}} </p>
                                                                        @endif
                                                                        <p>
                                                                        @forelse($ticket_attechmant as $key=>$attechment)
                                                                        <a class="text-info mr-3 small" target="_blank" href="{{asset($attechment->attachment)}}">Attachment {{$key+1}}</a>
                                                                        @empty
                                                                        <p></p>
                                                                        @endforelse
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                                <hr class="mt-0">
                                                            @foreach($comments as $c)
                                                            <?php
                                                                $comment_attechmant = DB::table('comments_attachment')->where('ticket_comment_id',$c->id)->get();
                                                                $date = date('d-M-Y  h:i A', strtotime($c->replied));
                                                            ?>
                                                                <div class="media">
                                                                    <div class="media-body">
                                                                        <p class="mt-0 mb-0 small"><b>{{$c->replied_by}}</b> <span class="text-muted ml-3">{{$date}}</span></p>
                                                                        <p class="mb-0 small">{{$c->remark}} </p>
                                                                        <p>
                                                                        @forelse($comment_attechmant as $key=>$attechment)
                                                                        <a class="text-info mr-3 small" target="_blank" href="{{asset($attechment->attachment)}}">Attachment {{$key+1}}</a>
                                                                        @empty
                                                                        <p></p>
                                                                        @endforelse
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                                <hr class="mt-0">
                                                            @endforeach

                                                                <div class="media">
                                                                    <div class="media-body">
                                                                        <div class="row">
                                                                            <div class="col-sm-12">
                                                                                <h6 class="mt-2 mb-2"> <b>Add New remarks</b>
                                                                                </h6>
                                                                                <form id="escalation_comment_form"  action="{{route('seller.add_escalation_comment')}}" enctype="multipart/form-data" method="post">
                                                                                @csrf
                                                                                    <div class="form-group">
                                                                                        <input type="hidden" name="ticket_id" value="{{$escalation->id}}">
                                                                                        <textarea required="" class="form-control" name="remark" placeholder="Add your message here" rows="3"></textarea>
                                                                                    </div>
                                                                                    <div class="form-group files">
                                                                                        <label>Attachments (If any) </label>
                                                                                        <input type="file" name="comment_attachment[]" id="comment_attachment" class="form-control" multiple="">
                                                                                        <small>Maximum file size : 5 MB</small>
                                                                                    </div>
                                                                                    <div class="text-right">
                                                                                        <button class="btn btn-primary" type="submit">Submit</button>
                                                                                    </div>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="card m-b-30">
                                            <div class="card-header">
                                                <div class="card-title mb-0">
                                                    Escalation Info
                                                </div>
                                            </div>
                                            <div class="list-group list  list-group-flush">
                                                <div class="list-group-item">
                                                <?php
                                                  $date = date('d-M-Y  h:i A', strtotime($escalation->raised));
                                                ?>
                                                    <i class="far fa-clock fa-xs"></i> <span class="small">Raised at: </span><strong class="small font-weight-bold"> {{$date}}</strong>
                                                </div>
                                                <div class="list-group-item p-all-15 h6 ">
                                                <i class="far fa-info-circle fa-xs"></i> <span class="small">Status: </span>
                                                    @if($escalation->status == 'o')
                                                    <strong class="text-danger small font-weight-bold"> Open</strong>
                                                    @else
                                                    <strong class="text-success small font-weight-bold"> Closed</strong>
                                                    @endif
                                                </div>
                                                <div class="list-group-item">
                                                    <i class="far fa-tachometer-slow fa-xs"></i> <span class="small"> Sevierity : </span><strong class="small font-weight-bold" style="color : @if($escalation->sevierity =='low') #28a745 @elseif($escalation->sevierity =='medium') #e2a918 @elseif($escalation->sevierity =='high') #ff0000 @else #bc0000 @endif"> {{$escalation->sevierity}}</strong>
                                                </div>
                                                <div class="list-group-item">
                                                <i class="far fa-info-square fa-sm"></i> <span class="small">Last Escalate Reason: {{$escalation->escalate_reason}}</strong></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    @include('seller.pages.scripts')
    <script type="text/javascript">


    $(document).ready(function () {

        $('#escalation_comment_form').validate({
            rules: {
                remark: {
                    required: true
                },
            },
            messages: {
                remark: {
                    required: "Please Enter a Message",
                },
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
</script>
</body>

</html>
