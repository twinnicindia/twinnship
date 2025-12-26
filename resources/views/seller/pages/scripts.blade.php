<div class="modal fade" id="Rechargemodel" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Upgrade Your Shipping Limit</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="payment_option">
                    <div class="row mb-3">
                        <div class="col">
                            <span class="fs-3 fw-semibold" id="rechargeSellerBalance">₹{{Session()->get('MySeller')->balance}}</span>
                            <h5 class="text-primary me-2" style="white-space: nowrap;">Available Balance</h5>
                        </div>
                        <div class="col">
                            <span class="fs-3 fw-semibold" id="rechargeHoldBalance">₹{{$holdBalance ?? 0.00}}</span>
                            <h5 class="text-danger">Hold Balance</h5>
                        </div>
                        <div class="col">
                            <span class="fs-3 fw-semibold" id="rechargeUsableBalance">₹{{Session()->get('MySeller')->balance - ($holdBalance ?? 0)}}</span>
                            <h5 class="text-primary">Usable Amount</h5>
                        </div>
                    </div>

                    <h5 class="text-primary fw-semibold mb-4 mt-2"><b>Your wallet has been migrated to Twinnship Dashboard</b></h5>
                        <div class="card-row border-0 bg-light-primary">
                        <form class="mt-3" method="post" action="{{route('seller.confirm_payment')}}">
                            @csrf
                        <p class="label">Enter the amount for your recharge </p>
                        <div class="form-group row" id="data_amount">
                            <label for="inputPassword" class="col-sm-3 text-right label">Amount :</label>
                            <div class="col-sm-9">
                                <input  type="number" autocomplete="off" name="filter" class="form-control bg-white border-0 text-dark rounded-pill"  id="rechargeAmount" placeholder="Enter Amount" value="500">
                            </div>
                            <span class="label mt-3">Or Select amount for quick recharge</span>
                            <div class="col-sm-12 text-center ">
                                <button type="button" class="btn btn-outline-success btn-sm set_recharge_amount"
                                        data-amount="500">500</button>
                                <button type="button" class="btn btn-outline-success btn-sm set_recharge_amount"
                                        data-amount="1000">1000</button>
                                <button type="button" class="btn btn-outline-success btn-sm set_recharge_amount"
                                        data-amount="2000">2000</button>
                                <button type="button" class="btn btn-outline-success btn-sm set_recharge_amount"
                                        data-amount="5000">5000</button>
                                <button type="button" class="btn btn-outline-success btn-sm set_recharge_amount"
                                        data-amount="10000">10000</button>
                            </div>
                            <div class="form-group mt-3 mb-3">
                                <input type="radio" name="o_type" class="paymentGateway" value="razorpay" checked> RazorPay
                                <input type="radio" name="o_type" value="ccavenue" class="ml-3 paymentGateway"> CCAvenue
                            </div>
                            <span class="label mt-3">Have a coupon? Enter code to validate</span>
                            <div class="form-group mb-4 position-relative">
                                <input type="text" class="form-control bg-white border-0 text-dark rounded-pill" placeholder="Enter Coupon" id="promoCode">
{{--                                <button type="button" id="redeemButton" class="position-absolute top-50 end-0 translate-middle-y bg-primary p-0 border-0 text-center text-white rounded-pill px-3 py-2 me-2 fw-semibold">--}}
{{--                                    Validate--}}
{{--                                </button>--}}
                                <span class="pl-2 label text-danger" id="applyCodeNote"></span>
                            </div>
                            <span class="label">Available Coupons:</span>
                            <div class="mt-2 mb-2 p-3 bg-creamyellow coupons-overflow pb-3 border-radius-4px coupon-code-details position-relative border-radius-8px ng-star-inserted" style="max-height: 200px; overflow-y: auto;">
                                @foreach($coupon as $c)
                                    <div class="border-bottom-dashed-grey mb-pb-10px position-relative ng-star-inserted">
                                        <div class="custom-boot-row">
                                            <div class="custom-boot-col-6">
                                                <span class="custom-boot-bg-white fs-12px opacity-90 pt-1 pb-1 coupon-code-text custom-boot-d-inline-block">
                                                    {{$c->code}}
                                                </span>
                                            </div>
                                        </div>
                                        <p class="fs-10px opacity-50 m-0 custom-boot-p ng-star-inserted">
                                            {{$c->title}}
                                        </p>
                                        <button data-code="{{$c->code}}" data-title="{{$c->title}}" data-amount="{{$c->min_amount}}" type="button" class="redeemButton position-absolute top-50 end-0 translate-middle-y bg-primary p-0 border-0 text-center text-white rounded-pill px-3 py-2 me-2 fw-semibold">
                                            Apply
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger text-white" data-bs-dismiss="modal">Close</button>
                            <button type="button" id="makeRechargeButton" class="btn btn-primary text-white">Recharge</button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="neftModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Recharge using NEFT</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Please transfer amount in below account and enter details here.</p>
                <p style="font-weight: bold;">
                    Account Holder : {{$config->account_holder}} <bR>
                    Account Number : {{$config->account_number}}<bR>
                    IFSC Code : {{$config->ifsc_code}}
                </p>
                <form class="mt-3" method="post" action="{{route('seller.confirm_payment')}}" id="neftForm">
                    @csrf
                    <div class="form-group">
                        <label for="">Your Balance</label> :
                        <label for=""><i class="fa fa-inr"></i> {{isset(Session()->get('MySeller')->balance)?Session()->get('MySeller')->balance:0}}</label>
                    </div>
                    <div class="form-group">
                        <label for="rechargeAmount">UTR Number</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="utrNumber" name="utrNumber" placeholder="Enter UTR Number"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="rechargeAmount">Transaction Amount</label>
                        <div class="input-group mb-3">
                            <input type="number" class="form-control" id="rechargeNEFTAmount" name="amount" min="0" placeholder="Enter Transaction Amount" />
                        </div>
                    </div>
                    <button type="button" id="makeNEFTRechargeSubmitButton" class="btn btn-primary">Recharge</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script src="{{asset('assets/sellers/')}}/js/bootstrap.bundle.min.js"></script>
<script src="{{asset('assets/sellers/')}}/js/sidebar-menu.js"></script>
<script src="{{asset('assets/sellers/')}}/js/dragdrop.js"></script>
<script src="{{asset('assets/sellers/')}}/js/rangeslider.min.js"></script>
<script src="{{asset('assets/sellers/')}}/js/sweetalert.js"></script>
<script src="{{asset('assets/sellers/')}}/js/quill.min.js"></script>
<script src="{{asset('assets/sellers/')}}/js/data-table.js"></script>
<script src="{{asset('assets/sellers/')}}/js/prism.js"></script>
<script src="{{asset('assets/sellers/')}}/js/clipboard.min.js"></script>
<script src="{{asset('assets/sellers/')}}/js/feather.min.js"></script>
<script src="{{asset('assets/sellers/')}}/js/simplebar.min.js"></script>
<script src="{{asset('assets/sellers/')}}/js/apexcharts.min.js"></script>
<script src="{{asset('assets/sellers/')}}/js/amcharts.js"></script>
<script src="{{asset('assets/sellers/')}}/js/custom/ecommerce-chart.js"></script>
<script src="{{asset('assets/sellers/')}}/js/custom/custom.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{asset('assets/sellers/')}}/js/jquery.form.min.js"></script>


<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="{{asset('assets/sellers/')}}/js/popper.min.js"></script>
<script src="{{asset('assets/sellers/')}}/js/bootstrap.min.js"></script>
<!--<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>
-->
<script src="{{asset('assets/sellers/')}}/js/sweetalert.js"></script>
<script src="{{asset('assets/sellers/')}}/js/jquery.form.min.js"></script>
<script src="{{asset('assets/sellers/')}}/js/loadingoverlay.min.js"></script>

<script src="{{asset('assets/sellers/')}}/js/notify.js"></script>
<script src="{{asset('assets/sellers/')}}/js/prettify.js"></script>

<script src="{{asset('assets/sellers/')}}/js/myScript.js"></script>
<script src="{{asset('assets/sellers/')}}/js/select.js"></script>
{{--<script src="{{asset('assets/sellers/')}}/js/custom.js"></script>--}}

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js" integrity="sha512-UdIMMlVx0HEynClOIFSyOrPggomfhBKJE28LKl8yR3ghkgugPnG6iLfRfHwushZl1MOPSY6TsuBDGPK2X4zYKg==" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/additional-methods.min.js" integrity="sha512-6Uv+497AWTmj/6V14BsQioPrm3kgwmK9HYIyWP+vClykX52b0zrDGP7lajZoIY1nNlX4oQuh7zsGjmF7D0VZYA==" crossorigin="anonymous"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
<script>

    $("body").tooltip({selector: '[data-toggle=tooltip]', delay: {show: 700}});
    var cod_balance={{isset(Session()->get('MySeller')->cod_balance)?Session()->get('MySeller')->cod_balance:0}};
    <?php
        if(Session()->has('notification'))
        {
            switch (Session('notification')['type']){
                case "success":
                    echo "$.notify(\" ".Session('notification')['message']."\", {blur: 0.2, delay: 0,verticalAlign:'top', align:'right', type: 'success', icon:'check'});";
                    break;
                case "error":
                    echo "$.notify(\" ".Session('notification')['message']."\", {blur: 0.2, delay: 0,verticalAlign:'top', align:'right', type: 'danger', icon:'close'});";
                    break;

            }
            Session()->forget('notification');
        }
    ?>

        $('#notificationButton').click(function(){
            setTimeout(function(){
                $('#NotificationModal').collapse('hide');
            }, 3000);
        });


    $(document).ready(function () {
        $('.set_recharge_amount').on('click', function () {
            $('#rechargeAmount').val($(this).data('amount'));
        });

        $('.multiSelectDropDown').multiselect({
            includeSelectAllOption: true,
            enableFiltering: true,
            buttonWidth: '100%'
        });
        $('form').on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });

        $('.openRechargeModel').click(function(){
            $.ajax({
                url: '{{url('check-bulk-ship-running')}}',
                success: function(response){
                    if(response.status === 'true')
                        $('#exampleModal').modal('show');
                    else
                        $.notify(' A bulk ship is already in progress please wait till the operation completes ',{blur: 0.2, delay: 0,verticalAlign:'top', align:'right', type: 'danger', icon:'close'});
                },
                error: function (response) {
                    $.notify(' Something went wrong ',{blur: 0.2, delay: 0,verticalAlign:'top', align:'right', type: 'danger', icon:'close'});
                }
            });
        });

        $('[data-toggle="popover"]').popover();

        $('.recharge_amount').click(function () {
            var that=$(this);
            $('#rechargeAmount').val(that.data('value'));
        });
        $('#makeNEFTRechargeButton').click(function () {
            $('#exampleModal').modal('hide');
            $('#neftModal').modal('show');
        });
        $('#makeRemitRecharge').click(function () {
            $.notify(' COD recharges are temporary not available please use other recharge options',{blur: 0.2, delay: 0,verticalAlign:'top', align:'right', type: 'danger', icon:'close'});
            return false;
            var amount=$('#rechargeAmount');
            if(parseInt(amount.val()) <= 0 || amount.val() === ''){
                alert('Enter valid recharge amount');
                amount.focus();
                return false;
            }
            if(parseInt(amount.val()) > cod_balance){
                alert('Amount can not be greater than COD Balance');
                amount.focus();
                return false;
            }
            showOverlay();
            $.ajax({
                type : 'post',
                data : {
                    'amount' : amount.val(),
                    '_token' : '{{csrf_token()}}'
                },
                url : '{{route('seller.remit_cod')}}',
                success : function (response) {
                    hideOverlay();
                    let info=JSON.parse(response);
                    if(info.status==='false')
                        $.notify(info.message,{blur: 0.2, delay: 0,verticalAlign:'top', align:'right', type: 'danger', icon:'close'});
                    else{
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "Your Rechargeable Amount Is : " + info.recharge,
                            icon: 'success',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, proccess it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                showOverlay();
                                $.ajax({
                                    type : 'post',
                                    data : {
                                        'amount' : amount.val(),
                                        '_token' : '{{csrf_token()}}'
                                    },
                                    url : '{{url('/submit-cod-remit-recharge')}}',
                                    success : function (reponse) {
                                        hideOverlay();
                                        Swal.fire(
                                            'Success!',
                                            'Recharge is successful.',
                                            'success'
                                        )
                                        setTimeout(function () {
                                            location.reload();
                                        },1000)
                                    },
                                    error: function (response) {
                                        hideOverlay();
                                        $.notify("Something went wrong please try again later",{blur: 0.2, delay: 0,verticalAlign:'top', align:'right', type: 'danger', icon:'close'});
                                    }
                                });
                            }
                        })
                         //alert("Your rechargeable amount is : " + info.recharge);
                         // $.notify(' COD remitted successfully',{blur: 0.2, delay: 0,verticalAlign:'top', align:'right', type: 'success', icon:'check'});
                         // setTimeout(function () {
                         //     location.reload();
                         // },1000);
                    }
                },
                error : function (response) {
                    hideOverlay();
                    $.notify("Something went wrong please try again later",{blur: 0.2, delay: 0,verticalAlign:'top', align:'right', type: 'danger', icon:'close'});
                }
            });

            {{--$.ajax({--}}
            {{--    type : 'post',--}}
            {{--    data : {--}}
            {{--        'amount' : amount.val(),--}}
            {{--        '_token' : '{{csrf_token()}}'--}}
            {{--    },--}}
            {{--    url : '{{route('seller.remit_cod')}}',--}}
            {{--    success : function (response) {--}}
            {{--        var info=JSON.parse(response);--}}
            {{--        if(info.status === 'true'){--}}
            {{--            $('#exampleModal').modal('hide');--}}
            {{--            $.notify(' Details has been sent to admin.Amount will be credited to your wallet once approved',{blur: 0.2, delay: 0,verticalAlign:'top', align:'right', type: 'success', icon:'check'});--}}
            {{--        }--}}
            {{--    }--}}
            {{--});--}}

        });
        $('.redeemButton').click(function () {
            var that = $(this);
            if (parseInt($('#rechargeAmount').val()) >= parseInt(that.data('amount'))) {
                $('#applyCodeNote').html(that.data('title'))
                $('#promoCode').val(that.data('code'));
            }
            else{
                showError("Minimum recharge amount is "+ that.data('amount'));
            }
            // var promo=$('#promoCode');
            // if(promo.val().trim()===''){
            //     alert('Please enter promo code to redeem');
            //     promo.focus();
            //     return false;
            // }
            // showOverlay();
            {{--$.ajax({--}}
            {{--    type : 'post',--}}
            {{--    data : {--}}
            {{--        '_token' : '{{csrf_token()}}',--}}
            {{--        'code' : promo.val()--}}
            {{--    },--}}
            {{--    url : '{{route('seller.apply-promo')}}',--}}
            {{--    success : function (response) {--}}
            {{--        hideOverlay();--}}
            {{--        let info=JSON.parse(response);--}}
            {{--        if(info.status==='true')--}}
            {{--        {--}}
            {{--            $.notify('Redemption Successful amount credited to your wallet',{blur: 0.2, delay: 0,verticalAlign:'top', align:'right', type: 'success', icon:'check'});--}}
            {{--        }else{--}}
            {{--            $.notify(info.message,{blur: 0.2, delay: 0,verticalAlign:'top', align:'right', type: 'danger', icon:'close'});--}}
            {{--        }--}}
            {{--    },--}}
            {{--    error  :function (response) {--}}
            {{--        $.notify('Something went wrong please check again',{blur: 0.2, delay: 0,verticalAlign:'top', align:'right', type: 'danger', icon:'close'});--}}
            {{--    }--}}
            {{--});--}}
        });
        $('#makeRechargeButton').click(function () {
            // $.notify(' Recharge Feature available Soon.',{blur: 0.2, delay: 0,verticalAlign:'top', align:'right', type: 'danger', icon:'close'});
            // return false;
            var seller_id = {{Session()->get('MySeller')->id}};
            var amount=$('#rechargeAmount');
            if(parseInt(amount.val()) <= 0 || amount.val() === ''){
                alert('Enter valid recharge amount');
                amount.focus();
                return false;
            }
            if(parseInt(amount.val()) < 500 && seller_id != 1){
                alert('Minimum recharge amount is 500');
                return false;
            }

            if($('.paymentGateway:checked').val() === 'ccavenue'){
               window.location="{{url('ccavenue')}}?amount="+amount.val()+"&promo="+$('#promoCode').val();
            }
            else {
                $.ajax({
                    type: 'post',
                    data: {
                        'amount': amount.val() * 100,
                        '_token': '{{ csrf_token() }}'
                    },
                    url: '{{route('seller.create_payment_order')}}',
                    success: function (response) {
                        var info = JSON.parse(response);
                        if (info.status === 'true') {
                            generate_payment(amount.val() * 100, info.order_id, $('#promoCode').val());
                        }
                    },
                    error: function (response) {
                        alert('Payment Gateway under maintenance please try after some time..');
                    }
                });
            }
        });
        // setInterval(function () {
        //     checkForNotification();
        // },15000);
    });
    $('#rechargeButtonModal').click(function (){
        RefreshSellerBalance();
    });
    $('#makeNEFTRechargeSubmitButton').click(function () {
        $('input[name="utrNumber"]').valid();
        var amount=$('#rechargeNEFTAmount'),utr=$('#utrNumber');
        if(parseInt(amount.val()) <= 0 || amount.val() === ''){
            alert('Enter valid recharge amount');
            amount.focus();
            return false;
        }
        if(parseInt(amount.val()) < 1000){
            alert('Minimum recharge amount is 500');
            return false;
        }
        $.ajax({
            type : 'post',
            data : {
                'amount' : amount.val(),
                'utr' : utr.val().trim(),
                '_token' : '{{csrf_token()}}'
            },
            url : '{{route('seller.create_neft_recharge')}}',
            success : function (response) {
                var info=JSON.parse(response);
                if(info.status === 'true'){
                    $('#neftModal').modal('hide');
                    $.notify(' Details has been sent to admin.Amount will be credited to your wallet once approved',{blur: 0.2, delay: 0,verticalAlign:'top', align:'right', type: 'success', icon:'check'});
                }
            }
        });
    });
    function generate_payment(amount,orderId,promoCode) {
        var options = {
            //"key": "OBel0J7k9m608zNA6AVY6RSm", // Enter the Key ID generated from the Dashboard
            "key": "{{$config->razorpay_key}}",
            "amount": amount, // Amount is in currency subunits. Default currency is INR. Hence, 50000 refers to 50000 paise
            "currency": "INR",
            "image" : "",
            "name": "Twinnic India Private Limited",
            "description": "Wallet Recharge",
            "order_id": orderId, //This is a sample Order ID. Pass the `id` obtained in the response of Step 1
            "callback_url": "{{route('seller.payment_success')}}",
            "prefill": {
                "name": "{{Session()->get('MySeller')->name}}",
                "email": "{{Session()->get('MySeller')->email}}",
                "contact": "{{Session()->get('MySeller')->mobile}}"
            },
            "notes": {
                "address": "Razorpay Corporate Office",
                "promocode": promoCode
            },
            "theme" : {
                "color" : "#528ff0"
            }
        };
        var rzp1 = new Razorpay(options);
        rzp1.open();
    }

    //For Loading Overlay hide or Show
    function showOverlay() {
        $.LoadingOverlay("show", {
            image       : "{{asset('assets/1.png')}}",
            imageAutoResize : true,
            imageResizeFactor : 1
        });
    }
    function hideOverlay() {
        $.LoadingOverlay('hide');
    }
    function showSuccess(message) {
        $.notify(" "+message, {blur: 0.2, delay: 0,verticalAlign:'top', align:'right', type: 'success', icon:'check'});
    }
    function showError(message) {
        $.notify(" "+message, {blur: 0.2, delay: 0,verticalAlign:'top', align:'right', type: 'danger', icon:'close'});
    }
    $(function () {
        $('[data-toggle="popover"]').popover();
    });

    $('#neftForm').validate({
            rules: {
                utrNumber: {
                    required: true
                },
                amount: {
                    required: true
                },
            },
            messages: {
                utrNumber: {
                    required: "Please Enter UTR Number",
                },
                amount: {
                    required: "Please Enter Amount",
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
    function checkForNotification() {
        $.ajax({
            type : 'get',
            url : '{{route('seller.check-shipped-order-notification')}}',
            success : function (response) {
                if(response.status){
                    for(let i=0; i<=response.data.length; i++){
                        let current = response.data[i];
                        $.notify(response.notification,{blur: 0.2, delay: 0,verticalAlign:'top', align:'right', type: 'success', icon:'check'});
                    }
                }

            }

        });
    }

    function RefreshSellerBalance(){
        $.ajax({
            type: 'get',
            url: '{{route('ajax.refresh-seller-balance')}}',
            success : function (response){
                var balance = parseFloat(response.balance).toFixed(2);
                var holdBalance = parseFloat(response.hold_balance).toFixed(2);
                var usableBalance = (parseFloat(response.balance) - parseFloat(response.hold_balance)).toFixed(2);
                $('#sellerBalanceLabel').html(balance);
                $('#rechargeSellerBalance').html(balance);
                $('#rechargeHoldBalance').html(holdBalance);
                $('#rechargeUsableBalance').html(usableBalance);
            }
        });
    }
</script>
