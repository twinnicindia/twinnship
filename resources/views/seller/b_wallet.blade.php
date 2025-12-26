<div class="row">
    <div class="col-lg-8 card-row border-0 rounded-5 container">
        <div class="row mb-3 px-4 pt-3">
            <div class="col-lg-3 border-right">
                <h5 class="text-primary fw-semibold mb-4 mt-2"><b>Balance details</b></h5>
                <div class="mb-5">
                    <span class="fs-3 fw-semibold">₹{{Session()->get('MySeller')->balance}}</span>
                    <h5 class="text-primary me-2" style="white-space: nowrap;">Available Balance</h5>
                </div>
                <div class="mb-5">
                    <span class="fs-3 fw-semibold">₹0.00</span>
                    <h5 class="text-danger">Hold Balance</h5>
                </div>
                <div class="mb-5">
                    <span class="fs-3 fw-semibold">₹0.00</span>
                    <h5 class="text-primary">Usable Amount</h5>
                </div>
            </div>
            <div class="col-lg-9 px-5">
                <h5 class="text-primary fw-semibold mb-4 mt-2"><b>Your wallet has been migrated to Twinnship Dashboard</b></h5>
                <div class="card-1 border-0 bg-light-primary">
                    <form class="mt-3" method="post" action="{{route('seller.confirm_payment')}}">
                    @csrf
                        <p class="labe">Enter the amount for your recharge </p>
                        <div class="form-group row" id="data_amount">
                            <label for="inputPassword" class="col-sm-3 text-right label">Amount :</label>
                            <div class="col-sm-9 mb-4">
                                <input  type="number" autocomplete="off" name="filter" class="form-control bg-white border-0 text-dark rounded-pill"  id="recharge_wallet_amount" placeholder="Enter Amount" value="500">
                            </div>
                            <span class="label mt-3 mb-4">Or Select amount for quick recharge</span>
                            <div class="col-sm-12 text-center mb-4">
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
                            <span class="label mt-3 mb-4">Have a coupon? Enter code to validate</span>
                            <div class="form-group mb-4 position-relative">
                                <input type="text" class="form-control bg-white border-0 text-dark rounded-pill" placeholder="Enter Coupon">
                                <button type="submit" class="position-absolute top-50 end-0 translate-middle-y bg-primary p-0 border-0 text-center text-white rounded-pill px-3 py-2 me-2 fw-semibold">
                                    Validate
                                </button>
                            </div>
                        </div>

                        <div class="modal-footer ">
                            <button type="button" id="makeRechargeButton" class="btn btn-primary text-white">Recharge</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="card-row border-0 rounded-5">
            <h4 class="text-primary text-center">Benefits of wallet recharge</h4>
            <img src="{{url('/')}}/assets/sellers/images/wallet.png" alt="">
        </div>
        <div class="card-row border-0 rounded-5">
            <h4 class="text-primary text-center">NEFT Payment - Bank details</h4>
            <div class="row">
                <div class="col-md-12 text-dark mb-2">
                    <div class="row">
                        <div class="col-md-4">Beneficiary Name :</div>
                        <div class="col-md-8">{{$account[0]->account_holder_name}} </div>
                    </div>
                </div>
                <div class="col-md-12 text-dark mb-2">
                    <div class="row">
                        <div class="col-md-4">Bank Name :</div>
                        <div class="col-md-8">{{$account[0]->bank_name}}</div>
                    </div>
                </div>
                <div class="col-md-12 text-dark mb-2">
                    <div class="row">
                        <div class="col-md-4">Bank Branch :</div>
                        <div class="col-md-8">{{$account[0]->bank_branch}}</div>
                    </div>
                </div>
                <div class="col-md-12 text-dark mb-2">
                    <div class="row">
                        <div class="col-md-4">Account Number  :</div>
                        <div class="col-md-8">{{$account[0]->account_number}}</div>
                    </div>
                </div>
                <div class="col-md-12 text-dark mb-2">
                    <div class="row">
                        <div class="col-md-4">Account Type :</div>
                        <div class="col-md-8">XXXXXXXXXXX</div>
                    </div>
                </div>
                <div class="col-md-12 text-dark mb-2">
                    <div class="row">
                        <div class="col-md-4">IFSC Code  :</div>
                        <div class="col-md-8">{{$account[0]->ifsc_code}}</div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

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
        $('#redeemButton').click(function () {
            var promo=$('#promoCode');
            if(promo.val().trim()===''){
                alert('Please enter promo code to redeem');
                promo.focus();
                return false;
            }
            showOverlay();
            $.ajax({
                type : 'post',
                data : {
                    '_token' : '{{csrf_token()}}',
                    'code' : promo.val()
                },
                url : '{{route('seller.apply-promo')}}',
                success : function (response) {
                    hideOverlay();
                    let info=JSON.parse(response);
                    if(info.status==='true')
                    {
                        $.notify('Redemption Successful amount credited to your wallet',{blur: 0.2, delay: 0,verticalAlign:'top', align:'right', type: 'success', icon:'check'});
                    }else{
                        $.notify(info.message,{blur: 0.2, delay: 0,verticalAlign:'top', align:'right', type: 'danger', icon:'close'});
                    }
                },
                error  :function (response) {
                    $.notify('Something went wrong please check again',{blur: 0.2, delay: 0,verticalAlign:'top', align:'right', type: 'danger', icon:'close'});
                }
            });
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
            if(parseInt(amount.val()) < 10 && seller_id !== 188){
                alert('Minimum recharge amount is 10');
                return false;
            }

            if($('.paymentGateway:checked').val() === 'ccavenue'){
               window.location="{{url('ccavenue')}}?amount="+amount.val();
            }
            else {
                $.ajax({
                    type: 'post',
                    data: {
                        'amount': amount.val() * 100,
                        '_token': '{{csrf_token()}}'
                    },
                    url: '{{route('seller.create_payment_order')}}',
                    success: function (response) {
                        var info = JSON.parse(response);
                        if (info.status === 'true') {
                            generate_payment(amount.val() * 100, info.order_id);
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
    $('#makeNEFTRechargeSubmitButton').click(function () {
        $('input[name="utrNumber"]').valid();
        var amount=$('#rechargeNEFTAmount'),utr=$('#utrNumber');
        if(parseInt(amount.val()) <= 0 || amount.val() === ''){
            alert('Enter valid recharge amount');
            amount.focus();
            return false;
        }
        if(parseInt(amount.val()) < 1000){
                alert('Minimum recharge amount is 1000');
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
    function generate_payment(amount,orderId) {
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
                "address": "Razorpay Corporate Office"
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
</script>