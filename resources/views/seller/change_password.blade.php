<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Profile | {{$config->title}} </title>
    <link rel="stylesheet" href="{{url('/')}}/public/assets/seller/css/custom.css" type="text/css">
    @include('seller.pages.styles')
</head>

<body>
    <div class="container-fluid user-dashboard">
        @include('seller.pages.header')
        @include('seller.pages.sidebar')
        <div class="content-wrapper">
            <div class="content-inner" id="data_div">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="m-b-0">
                                    <i class="fas fa-key fa-sm"></i> Change Password
                                </h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="change_password" method="post" action="{{route('seller.update_password')}}">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="avatar-wrapper">
                                        <img class="profile-pic" src="{{Session('MySeller')->profile_image==""?asset('public/assets/seller/images/user-photo.svg'):asset(Session('MySeller')->profile_image)}}" />
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Old Password</label>
                                                <input type="password" name="old_password" id="old_password" class="form-control" placeholder="Old Password" required>
                                                <small class="text-danger"></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>New Password</label>
                                                <input name="new_password" id="new_password" type="password" class="form-control" placeholder="New Password" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Confirm Password</label>
                                                <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm Password" required>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-2">Update Password</button>
                                </div>
                            </div>
                        </form>

                    </div>

                </div>
            </div>
        </div>
    </div>
    @include('seller.pages.scripts')
    <script type="text/javascript">
        $(document).ready(function() {

            $(document).on('dblclick', '#old_password', function() {
                var input = $("#old_password");
                input.attr('type') === 'password' ? input.attr('type', 'text') : input.attr('type', 'password')
            });
            $(document).on('dblclick', '#new_password', function() {
                var input = $("#new_password");
                input.attr('type') === 'password' ? input.attr('type', 'text') : input.attr('type', 'password')
            });
            $(document).on('dblclick', '#confirm_password', function() {
                var input = $("#confirm_password");
                input.attr('type') === 'password' ? input.attr('type', 'text') : input.attr('type', 'password')
            });

            var oldPassword= $('#old_password');

            oldPassword.on('keyup', function() {
                if(this.value.length > 0)
                    {
                        oldPassword.removeClass('is-invalid');
                        oldPassword.next().html('');
                    }
            });

            oldPassword.blur(function() {
                if (oldPassword.val() !== '') {
                    $.ajax({
                        type: 'get',
                        url: '{{url('/')."/check-old-password"}}/' + oldPassword.val(),
                        success: function(response) {
                            if(response != 1){
                                oldPassword.addClass('is-invalid');
                                oldPassword.next().html('Oops... Incorrect Old Password');
                                oldPassword.val('').focus();
                            }else{
                                oldPassword.removeClass('is-invalid');
                                oldPassword.next().html('');
                            }
                        },
                        error: function(response) {
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

            $('#change_password').validate({
                rules: {
                    old_password: {
                        required: true
                    },
                    new_password: {
                        required: true
                    },
                    confirm_password: {
                        required: true,
                        equalTo : "#new_password"
                    },
                },
                messages: {
                    old_password: {
                        required: "Please Enter Old Password",
                    },
                    new_password: {
                        required: "Please Enter New Password",
                    },
                    confirm_password: {
                        required: "Please Enter Confirm Password",
                        equalTo : "Please Enter Valid Confirm Password"
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

        });
    </script>
</body>

</html>
