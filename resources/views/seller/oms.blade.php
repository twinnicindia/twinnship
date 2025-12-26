@php
    $Channel_images=array(
        'shopify' => 'public/assets/images/channel/shopify.jpg',
        'woocommerce' => 'public/assets/images/channel/woocommerce.png',
        'magento' => 'public/assets/images/channel/magento.png',
        'storehippo' => 'public/assets/images/channel/storehippo.png',
        'kartrocket' => 'public/assets/images/channel/kartrocket.png',
        'amazon' => 'public/assets/images/channel/amazon.jpg',
        'opencart' => 'public/assets/images/channel/opencart.png',
        'manual' => 'public/assets/images/channel/manual.jpg',
        'easyship' => 'public/assets/images/oms/easyship.png',
        'easyecom' => 'public/assets/images/oms/easyecom.png',
        'clickpost' => 'public/assets/images/oms/clickpost.png',
        'vineretail' => 'public/assets/images/oms/vineretail.png',
    );
@endphp
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>OMS | {{$config->title}} </title>
    @include('seller.pages.styles')
</head>
<body>
<div class="container-fluid user-dashboard">
    @include('seller.pages.header')
    @include('seller.pages.sidebar')
    <div class="content-wrapper">
        <div class="content-inner" id="data_div" style="display: none;">
            <div class="card">
                <div class="card-body">
                    <h3 class="h4 mb-4">
                        OMS
                        <div class="float-right">
                            <button type="button" class="btn btn-primary back-button btn-sm mx-0"><i class="fal fa-arrow-alt-left"></i> Go Back</button>
                        </div>
                    </h3>
                    <!-- <button type="button" class="btn btn-primary btn-sm addInfoButton"><i class="fa fa-plus"></i> Add New OMS</button> -->
                    <button type="button" class="btn btn-danger btn-sm" id="removeAllButton" style="display: none;"><i class="fa fa-trash"></i> Remove</button>
                    <br><br>
                    @if(count($oms)!=0)
                    <div class="table-responsive">
                    <table class="table table-hover mb-0" id="example1">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="checkAllButton" value="y"></th>
                                <th>Sr.No</th>
                                <th>OMS Image</th>
                                <th>OMS Name</th>
                                <th>Store Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php($cnt=1)
                            @foreach($oms as $w)
                            <tr id="row{{$w->id}}">
                                <td><input type="checkbox" class="selectedCheck" value="{{$w->id}}"></td>
                                <td>{{$cnt++}}</td>
                                <td><img src="{{asset($Channel_images[$w->oms_name])}}" style="height: 100px;"> </td>
                                <td>{{$w->oms_name}}</td>
                                <td>{{$w->title}}</td>
                                <td>
                                    <!-- <a href="javascript:;" title="Edit Information" data-id="{{$w->id}}" class="btn btn-success btn-sm modify_data mx-0"><i class="fa fa-pencil"></i></a>&nbsp; -->
                                    <a href="javascript:;" data-toggle="tooltip" data-original-title="Remove Information" data-id="{{$w->id}}" class="btn btn-danger btn-sm remove_data mx-0"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                    @else
                    <h5>No OMS added yet</h5>
                    @endif
                </div>
            </div>
        </div>
        <div class="content-inner" id="form_div">
            <div class="card">
                <div class="card-body">
                    <h3 class="h4 mb-4">
                        OMS
                        <div class="float-right">
                            <button type="button" class="btn btn-primary view-button btn-sm mx-0"><i class="fal fa-eye"></i> View Integrations</button>
                        </div>
                    </h3>
                    <div class="row">
                        <div class="col-md-3 mt-3">
                            <div class="card m-b-30">
                                <div class="card-body">
                                    <div class="text-center ">
                                        <img src="{{asset('public/assets/images/oms/easyship.png')}}" class="rounded-circle" width="80" alt="">
                                    </div>
                                    <h4 class="text-center m-t-20">
                                        EasyShip
                                    </h4>
                                    <div class="text-center p-b-20">
                                        <a class="btn btn-primary" href="{{ route('seller.oms_add_easyship') }}">Integrate</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mt-3">
                            <div class="card m-b-30">
                                <div class="card-body">
                                    <div class="text-center ">
                                        <img src="{{asset('public/assets/images/oms/easyecom.png')}}" class="rounded-circle" width="80" alt="">
                                    </div>
                                    <h4 class="text-center m-t-20">
                                        EasyEcom
                                    </h4>
                                    <div class="text-center p-b-20">
                                        <a class="btn btn-primary" href="{{ route('seller.oms_add_easyecom') }}">Integrate</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mt-3">
                            <div class="card m-b-30">
                                <div class="card-body">
                                    <div class="text-center ">
                                        <img src="{{asset('public/assets/images/oms/clickpost2.png')}}" class="rounded-circle" width="80" alt="">
                                    </div>
                                    <h4 class="text-center m-t-20">
                                        ClickPost
                                    </h4>
                                    <div class="text-center p-b-20">
                                        <a class="btn btn-primary" href="{{ route('seller.oms_add_clickpost') }}">Integrate</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mt-3">
                            <div class="card m-b-30">
                                <div class="card-body">
                                    <div class="text-center ">
                                        <img src="{{asset('public/assets/images/oms/omsguru.png')}}" class="rounded-circle" width="80" alt="">
                                    </div>
                                    <h4 class="text-center m-t-20">
                                        OMSGuru
                                    </h4>
                                    <div class="text-center p-b-20">
                                        <a class="btn btn-primary" href="{{ route('seller.oms_add_omsguru') }}">Integrate</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mt-3">
                            <div class="card m-b-30">
                                <div class="card-body">
                                    <div class="text-center ">
                                        <img src="{{asset('public/assets/images/oms/vineretail.png')}}" class="rounded-circle" width="80" alt="">
                                    </div>
                                    <h4 class="text-center m-t-20">
                                        VineRetail
                                    </h4>
                                    <div class="text-center p-b-20">
                                        <a class="btn btn-primary" href="{{ route('seller.oms_add_vineretail') }}">Integrate</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mt-3">
                            <div class="card m-b-30">
                                <div class="card-body">
                                    <div class="text-center ">
                                        <img src="{{asset('public/assets/images/oms/unicommerce.jpg')}}" class="rounded-circle" width="80" alt="">
                                    </div>
                                    <h4 class="text-center m-t-20">
                                        Unicommerce
                                    </h4>
                                    <div class="text-center p-b-20">
                                        <a class="btn btn-primary" href="{{ route('seller.oms_add_unicommerce') }}">Integrate</a>
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
    var del_ids=[];
    $(document).ready(function () {
        $(".view-button").click(function() {
            $("#form_div").hide();
            $("#data_div").show();
        });
        $(".back-button").click(function() {
            $("#data_div").hide();
            $("#form_div").show();
        });

        $('#checkAllButton').click(function () {
            var that=$(this);
            if(that.prop('checked')){
                $('.selectedCheck').prop('checked',true);
                $('#removeAllButton').fadeIn();
            }
            else{
                $('.selectedCheck').prop('checked',false);
                $('#removeAllButton').hide();
            }
        });
        $('.selectedCheck').click(function () {
            var cnt=0;
            $('.selectedCheck').each(function () {
                if($(this).prop('checked'))
                    cnt++;
            });
            if(cnt>0)
                $('#removeAllButton').fadeIn();
            else
                $('#removeAllButton').hide();
        });
        $('#removeAllButton').click(function () {
            del_ids=[];
            $('.selectedCheck').each(function () {
                if($(this).prop('checked'))
                    del_ids.push($(this).val());
            });
            if (window.confirm("Are you sure want to Delete?")) {
                    showOverlay();
                    $.ajax({
                        type : 'post',
                        data : {
                            "_token": "{{ csrf_token() }}",
                            'ids' : del_ids
                        },
                        url : '{{url('/')."/remove-selected-channel"}}',
                        success : function (response) {
                            hideOverlay();
                            $.notify(" OMS has been Deleted.", {animationType:"scale", align:"right", type: "success", icon:"check"});
                            setTimeout(function () {
                                location.reload();
                            },1000);
                        },
                        error : function (response) {
                            hideOverlay();
                            Swal.fire('Oops...', 'Something went wrong!', 'error');
                        }
                    });
                }
        });

        $('.addInfoButton').click(function () {
        //    alert('This Funationally is under Construcation');
            $('#data_div').hide();
            $('#form_div').fadeIn();
        });

        $('#example1').on('click','.remove_data',function(){
            var that=$(this);
            if (window.confirm("Are you sure want to Delete?")) {
                    showOverlay();
                    $.ajax({
                        url : '{{url('/')."/delete-oms"}}/'+that.data('id'),
                        success : function (response) {
                            hideOverlay();
                            $.notify(" OMS has been Deleted.", {animationType:"scale", align:"right", type: "success", icon:"check"});
                            $('#row'+that.data('id')).remove();
                        },
                        error : function (response) {
                            hideOverlay();
                            $.notify(" Oops... Something went wrong!", {animationType:"scale", align:"right", type: "danger", icon:"close"});
                        }
                    });
                }
        });

    });
</script>
</body>
</html>
