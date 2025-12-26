@php
    $Channel_images=array(
        'shopify' => 'public/assets/images/channel/shopify.jpg',
        'woocommerce' => 'public/assets/images/channel/woocommerce.png',
        'magento' => 'public/assets/images/channel/magento.png',
        'storehippo' => 'public/assets/images/channel/storehippo.png',
        'kartrocket' => 'public/assets/images/channel/kartrocket.png',
        'amazon' => 'public/assets/images/channel/amazon.jpg',
        'amazon_direct' => 'public/assets/images/channel/amazon.jpg',
        'opencart' => 'public/assets/images/channel/opencart.png',
        'manual' => 'public/assets/images/channel/manual.jpg'
    );
@endphp
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>All Channels | {{$config->title}} </title>
    @include('seller.pages.styles')
</head>
<body>
<div class="container-fluid user-dashboard">
    @include('seller.pages.header')
    @include('seller.pages.sidebar')
    <div class="content-wrapper">
        <div class="content-inner" id="data_div" style="padding:80px;padding-right:17px;margin-top:-80px;display: none;">
            <div class="card">
                <div class="card-body">
                    <h3 class="h4 mb-4">
                        All Channel
                        <div class="float-right">
                            <button type="button" class="btn btn-primary back-button btn-sm mx-0"><i class="fal fa-arrow-alt-left"></i> Go Back</button>
                        </div>
                    </h3>
                    <!-- <button type="button" class="btn btn-primary btn-sm addInfoButton"><i class="fa fa-plus"></i> Add New Channel</button> -->
                    <button type="button" class="btn btn-danger btn-sm" id="removeAllButton" style="display: none;"><i class="fa fa-trash"></i> Remove</button>
                    <br><br>
                    @if(count($channels)!=0)
                    <div class="table-responsive">
                    <table class="table table-hover mb-0" id="example1">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="checkAllButton" value="y"></th>
                                <th>Sr.No</th>
                                <th>Channel Image</th>
                                <th>Channel Name</th>
                                <th>Store Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php($cnt=1)
                            @foreach($channels as $w)
                            <tr id="row{{$w->id}}">
                                <td><input type="checkbox" class="selectedCheck" value="{{$w->id}}"></td>
                                <td>{{$cnt++}}</td>
                                <td><img src="{{asset($Channel_images[$w->channel])}}" style="height: 100px;"> </td>
                                <td>{{$w->channel}}</td>
                                <td>{{$w->channel_name}}</td>
                                <td>
                                    @if($w->channel == 'amazon_direct')
                                    <a href="{{route('seller.reauthorize-amazon-direct',$w->id)}}" data-toggle="tooltip" data-original-title="Re-Authorize" target="_blank" class="btn btn-warning btn-sm view_data mx-0"><i class="fa fa-recycle"></i></a>
                                    @endif
                                    <a href="javascript:;" data-amazon_seller_id="{{$w->amazon_seller_id}}" data-toggle="tooltip" data-amazon_mws_token="{{$w->amazon_mws_token}}" data-woo_consumer="{{$w->woo_consumer_key}}" data-woo_secret="{{$w->woo_consumer_secret}}" data-shopify_key="{{$w->api_key}}" data-shopify_password="{{$w->password}}" data-shopify_shared="{{$w->shared_secret}}" data-name="{{$w->channel}}" data-url="{{$w->store_url}}" data-store="{{$w->channel_name}}" data-original-title="View Information" data-id="{{$w->id}}" class="btn btn-warning btn-sm view_data mx-0"><i class="fa fa-eye"></i></a>
                                    <a href="javascript:;" data-toggle="tooltip" data-original-title="Remove Information" data-id="{{$w->id}}" class="btn btn-danger btn-sm remove_data mx-0"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                    @else
                    <h4>No Channels added yet</h4>
                    @endif
                </div>
            </div>
        </div>
        <div class="content-inner" id="form_div" style="padding:80px;padding-right:17px;margin-top:-80px;">
            <div class="card">
                <div class="card-body">
                    <h3 class="h4 mb-4">
                        Channels
                        <div class="float-right">
                            <button type="button" class="btn btn-primary view-button"><i class="fal fa-eye"></i> View Integrations</button>
                        </div>
                    </h3>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card m-b-30">
                                <div class="card-body">
                                    <div class="text-center ">
                                        <img src="https://freelogopng.com/images/all_img/1655873523shopify-logo-white.png" class="rounded-circle" width="80" alt="">
                                    </div>
                                    <h4 class="text-center m-t-20">
                                        Shopify
                                    </h4>
                                    <div class="text-center p-b-20">
                                        <a class="btn btn-primary" href="{{route('seller.add-shopify-new')}}" style="width:auto;">Integrate</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <bR>
                    <div class="row">
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="channelInformationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Channel Information</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="card card-primary">
                            <div class="card-header card-primary">
                                <h5 class="card-title" id="channelName"></h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-striped table-bordered">
                                    <tr class="trRow trAll">
                                        <th>Store Name</th>
                                        <td id="storeName"></td>
                                    </tr>
                                    <tr class="trRow trAll">
                                        <th>Store URL</th>
                                        <td id="storeURL"></td>
                                    </tr>
                                    <tr class="trRow shopify">
                                        <th>API Key</th>
                                        <td id="shopifyApiKey"></td>
                                    </tr>
                                    <tr class="trRow shopify">
                                        <th>Shopify Password</th>
                                        <td id="shopifyPassword"></td>
                                    </tr>
                                    <tr class="trRow shopify">
                                        <th>Shopify Shared Secret</th>
                                        <td id="shopifySharedSecret"></td>
                                    </tr>
                                    <tr class="trRow woocommerce">
                                        <th>WooCommerce Consumer Key</th>
                                        <td id="wooCommerceConsumerKey"></td>
                                    </tr>
                                    <tr class="trRow woocommerce">
                                        <th>WooCommerce Secret Key</th>
                                        <td id="wooCommerceSecretKey"></td>
                                    </tr>
                                    <tr class="trRow amazon">
                                        <th>Amazon Seller ID</th>
                                        <td id="amazonSellerId"></td>
                                    </tr>
                                    <tr class="trRow amazon">
                                        <th>Amazon MWS Token</th>
                                        <td id="amazonMWSToken"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
                            $.notify(" Channel has been Deleted.", {animationType:"scale", align:"right", type: "success", icon:"check"});
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
            $('#form1').prop('action','{{route('seller.add_employees')}}');
            $('#data_div').hide();
            $('#form_div').fadeIn();
        });
        $('#cancelButton').click(function () {
            $('#form1').trigger("reset");
            $('#form_div').hide();
            $('#data_div').fadeIn();
        });
        $('#example1').on('click','.remove_data',function(){
            var that=$(this);
            if (window.confirm("Are you sure want to Delete?")) {
                    showOverlay();
                    $.ajax({
                        url : '{{url('/')."/delete-channels"}}/'+that.data('id'),
                        success : function (response) {
                            hideOverlay();
                            var info = JSON.parse(response);
                            if(info.status === 'true'){
                                $.notify(" Channel has been Deleted.", {animationType:"scale", align:"right", type: "success", icon:"check"});
                                $('#row'+that.data('id')).remove();
                            }else{
                                $.notify(" There are some orders in progress you can't delete this channel.", {animationType:"scale", align:"right", type: "danger", icon:"close"});
                            }

                        },
                        error : function (response) {
                            hideOverlay();
                            $.notify(" Oops... Something went wrong!", {animationType:"scale", align:"right", type: "danger", icon:"close"});
                        }
                    });
                }
        });
        $('#example1').on('click','.view_data',function(){
            var that=$(this);
            $('.trRow').hide();
            $('#channelName').html(that.data('name'));
            $('#storeName').html(that.data('store'));
            $('#storeURL').html(that.data('url'));
            $('#shopifyApiKey').html(that.data('shopify_key'));
            $('#shopifyPassword').html(that.data('shopify_password'));
            $('#shopifySharedSecret').html(that.data('shopify_shared'));
            $('#wooCommerceConsumerKey').html(that.data('woo_consumer'));
            $('#wooCommerceSecretKey').html(that.data('woo_secret'));
            $('#amazonSellerId').html(that.data('amazon_seller_id'));
            $('#amazonMWSToken').html(that.data('amazon_mws_token'));
            if(that.data('name') === 'shopify'){
                $('.trAll').show();
                $('.shopify').show();
            }else if(that.data('name') === 'woocommerce'){
                $('.trAll').show();
                $('.woocommerce').show();
            }
            else if(that.data('name') === 'amazon'){
                $('.trAll').show();
                $('.amazon').show();
            }
            $('#channelInformationModal').modal('show');
        });
    });
</script>
</body>
</html>
