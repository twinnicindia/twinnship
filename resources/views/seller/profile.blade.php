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
                <div class="card" style="margin-left: 60px">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="m-b-0">
                                    <i class="mdi mdi-checkbox-intermediate"></i> Profile
                                    <span class="float-right h5 mt-1">Seller Code({{Session()->get('MySeller')->code}})</span>
                                </h4>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="profileForm" method="post" action="{{route('seller.update_profile')}}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <br>
                                    <div class="avatar-wrapper">
                                        <img class="profile-pic" src="{{Session('MySeller')->profile_image==""?asset('public/assets/seller/images/user-photo.svg'):asset(Session('MySeller')->profile_image)}}" />
                                        <div class="upload-button" style="top:-35px;left:-15px;">
                                            <i class="fa fa-arrow-circle-up" aria-hidden="true"></i>
                                        </div>
                                        <input class="file-upload" type="file" accept="image/*" name="profile" />
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                            <label>First Name</label>
                                                <input type="text" name="first_name" id="registerFirstName" class="form-control" placeholder="First Name" value="{{$profile->first_name}}" required>

                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                            <label>Last Name</label>
                                                <input name="last_name" id="registerLastName" type="text" class="form-control" placeholder="Last Name" value="{{$profile->last_name}}" required>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                            <label>Company Name</label>
                                                <input type="text" name="company_name" id="registerCompanyName" class="form-control" placeholder="Company Name" value="{{$profile->company_name}}" required>

                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                            <label>Email</label>
                                                <input type="email" name="email" id="registerEmail" class="form-control" placeholder="Email Address" readonly value="{{$profile->email}}" required>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                    <label>Mobile Number</label>
                                        <div class="input-group ship-form-group" read>
                                            <div class="input-group-prepend">
                                                <select class="form-control" name="country" id="country" readonly>
                                                    <option value="+91">+91</option>
                                                    <option value="+7">+7</option>
                                                    <option value="+1">+!</option>
                                                </select>
                                            </div>
                                            <input type="text" maxlength="11" name="mobile" id="registerMobile" class="form-control" readonly placeholder="Mobile Number" value="{{$profile->mobile}}" required>
                                        </div>
                                    </div>
                                    <br>

                                    <button type="submit" class="btn btn-primary">Update Profile</button>
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

        $(document).on('keypress','#mobile',function(e){
        if($(e.target).prop('value').length>=10){
        if(e.keyCode!=32)
        {return false}
        }})

            var readURL = function(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        $('.profile-pic').attr('src', e.target.result);
                    }

                    reader.readAsDataURL(input.files[0]);
                }
            }

            $(".file-upload").on('change', function() {
                readURL(this);
            });

            $(".upload-button").on('click', function() {
                $(".file-upload").click();
            });

            $('#profileForm').validate({
            rules: {
                first_name: {
                    required: true
                },
                last_name: {
                    required: true
                },
                company_name: {
                    required: true
                },
                email: {
                    required: true,
                    email :true
                },
                mobile: {
                    required: true,
                    minlength: 10,
                    maxlength: 10
                },
            },
            messages: {
                first_name: {
                    required: "Please Enter First Name",
                },
                last_name: {
                    required: "Please Enter Last Name",
                },
                company_name: {
                    required: "Please Enter Company Name",
                },
                email: {
                    required: "Please Enter Email",
                    email : "Please Enter Valid Email"
                },
                mobile: {
                    required: "Please Enter a Mobile Number",
                    minlength: "Your mobile number must be 10 digits",
                    maxlength: "Your mobile number must be 10 digits"
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
