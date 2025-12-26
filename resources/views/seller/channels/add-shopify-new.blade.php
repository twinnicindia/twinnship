<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title> Integrate Shopify | {{$config->title}} </title>
    @include('seller.pages.styles')
</head>
<body>
<div class="container-fluid user-dashboard">
    @include('seller.pages.header')
    @include('seller.pages.sidebar')
    <div class="content-wrapper">
        <div class="content-inner" id="form_div" style="padding:80px;padding-right:17px;margin-top:-80px;">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="h4 mb-4" style="font-weight: 600;">Instruction to integrate Shopify to Twinnship</h3>
                        <ol class="pl-lg amazon-ints" style="list-style-type: decimal; padding-left: 20px; font-weight: 500;">
                            <li>1. To proceed, please click on the "Connect Shopify With Twinnship" button on your screen.</li>
                            <li>2. You'll be diverted to the Shopify seller login page. Log in to your Shopify account by entering your email address/username and password.</li>
                            <li>3. Once logged in, the app authorization page will open where you can verify your account integration with Twinnship by clicking "Install app".</li>
                            <li>4. Now, you will be redirected to the Twinnship channel page. Here you can edit your Shopify channel to modify it as per your preferences.</li>
                        </ol>
                        <!-- <iframe width="560" height="315" src="https://www.youtube.com/embed/ZQesDABh5eI" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe> -->
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="h4 mb-4" style="font-weight: 600;">Integrate Shopify to Twinnship</h3>
                        <!-- <form action="{{route('seller.submit-shopify-new')}}" method="post">
                            @csrf -->
                            <div class="form-group">
                                <button class="btn btn-primary" style="font-weight: 600;">Feature is comming soon!!</button>
                            </div>
                        <!-- </form> -->
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
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this warehouse!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, keep it'
            }).then((result) => {
                if (result.value) {
                    showOverlay();
                    $.ajax({
                        url : '{{url('/')."/delete-employees"}}/'+that.data('id'),
                        success : function (response) {
                            hideOverlay();
                            Swal.fire(
                                'Deleted!',
                                'Information has been deleted.',
                                'success'
                            );
                            $('#row'+that.data('id')).remove();
                        },
                        error : function (response) {
                            hideOverlay();
                            Swal.fire('Oops...', 'Something went wrong!', 'error');
                        }
                    });
                }
            })
        });
        $('#example1').on('click','.modify_data',function () {
            showOverlay();
            var that=$(this);
            $.ajax({
                url : '{{url('/')."/modify-employees/"}}' + that.data('id'),
                success: function (response) {
                    var info=JSON.parse(response);
                    var all_permissions=info.permissions.split(',');
                    for(var i=0;i<all_permissions.length;i++)
                        $('#per_'+all_permissions[i]).prop('checked',true);
                    $('#form1').prop('action','{{route('seller.update_employees')}}');
                    $('#hid').val(info.id);
                    $('#employee_name').val(info.employee_name);
                    $('#email').val(info.email);
                    $('#mobile').val(info.mobile);
                    $('#password').val(info.password);
                    $('#data_div').hide();
                    $('#form_div').fadeIn();
                    hideOverlay();
                },
                error : function (response) {
                    hideOverlay();
                    Swal.fire('Oops...', 'Something went wrong!', 'error');
                }
            });
        });
        $('#password').dblclick(function () {
            var that=$(this);
            that.prop('type','text');
        });
        $('#password').blur(function () {
            var that=$(this);
            that.prop('type','password');
        });
    });
</script>
</body>
</html>
