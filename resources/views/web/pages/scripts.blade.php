<script src="{{url('/')}}/public/assets/seller/js/jquery.min.js"></script>
<script src="{{url('/')}}/public/assets/seller/js/popper.min.js"></script>
<script src="{{url('/')}}/public/assets/seller/js/bootstrap.min.js"></script>
<script src="{{url('/')}}/public/assets/seller/js/owl.carousel.min.js"></script>
<script src="{{url('/')}}/public/assets/seller/js/custom.js"></script>
<script src="{{asset('public/assets/seller/')}}/js/notify.js"></script>
<script src="{{asset('public/assets/seller/')}}/js/prettify.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
    <?php
        if(Session()->has('notification'))
        {
            switch (Session('notification')['type']){
                case "success":
                    echo "$.notify(' ".Session('notification')['message']."', {blur: 0.2, delay: 0,verticalAlign:'top', align:'right', type: 'success', icon:'check'});";
                    break;
                case "error":
                    echo "$.notify(' ".Session('notification')['message']."', {blur: 0.2, delay: 0,verticalAlign:'top', align:'right', type: 'danger', icon:'close'});";
                    break;

            }
            Session()->forget('notification');
        }
    ?>
</script>
