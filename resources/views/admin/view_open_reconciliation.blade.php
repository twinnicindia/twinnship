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
            <section class="content" id="data_div">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                        <div class="card">
                <div class="card-body">
                    <div class="div container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row m-b-5">
                                            <div class="col-md-12 my-auto">
                                                <h5 class="m-0 h6">Escalation #{{$escalation->ticket_no}} - {{$keyType[$escalation->type] ?? "NA"}}</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-lg-8">
                                        <!-- Single post-->
                                        <div class="card p-2">
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
                                                                        <p class="mb-0 h5">{{$escalation->issue}} </p>
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
                                                    <i class="mdi mdi-clock"></i> <span class="small">Raised at: </span><strong class="small font-weight-bold"> {{$date}}</strong>
                                                </div>
                                                <div class="list-group-item p-all-15 h6 ">
                                                    <span class="small"> <i class="mdi mdi-album"></i> Status: </span>
                                                    @if($escalation->status == 'o')
                                                    <strong class="text-danger small font-weight-bold"> Open</strong>
                                                    @else
                                                    <strong class="text-success small"> Closed</strong>
                                                    @endif
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
