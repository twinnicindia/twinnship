<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>AdminLTE 3 | User Profile</title>
    @include('admin.pages.styles')
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <!-- Navbar -->
    @include('admin.pages.header')
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    @include('admin.pages.sidebar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Profile</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('administrator.dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active">User Profile</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-3">

                        <!-- Profile Image -->
                        <div class="card card-primary card-outline">
                            <div class="card-body box-profile">
                                <div class="text-center">
                                    <img class="profile-user-img img-fluid img-circle"
                                         style="height: 100px;"
                                         src="{{asset(Session()->get('MyAdmin')->image)}}"
                                         alt="{{Session()->get('MyAdmin')->name}}">
                                </div>

                                <h3 class="profile-username text-center">{{Session()->get('MyAdmin')->name}}</h3>

                                <p class="text-muted text-center">{{Session()->get('MyAdmin')->type=="admin"?"Web Administrator":"Web User"}}</p>

                                <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                    <div class="col-md-9">
                        <div class="card">
                            <div class="card-header p-2">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"><a class="nav-link active" href="#settings" data-toggle="tab">Your Profile</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#changePassword" data-toggle="tab">Change Password</a></li>
                                </ul>
                            </div><!-- /.card-header -->
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="tab-pane active" id="settings">
                                        <form class="form-horizontal" method="post" action="{{route('administrator.save.profile')}}" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="hid" value="{{Session()->get('MyAdmin')->id}}">
                                            <div class="form-group row">
                                                <label for="name" class="col-sm-2 col-form-label">Name</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="{{Session()->get('MyAdmin')->name}}">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="email" class="col-sm-2 col-form-label">Email</label>
                                                <div class="col-sm-10">
                                                    <input type="email" class="form-control" name="email" id="email" placeholder="Email Address" value="{{Session()->get('MyAdmin')->email}}">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="mobile" class="col-sm-2 col-form-label">Mobile</label>
                                                <div class="col-sm-10">
                                                    <input type="number" class="form-control" name="mobile" id="mobile" placeholder="Contact Number" value="{{Session()->get('MyAdmin')->mobile}}">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="image" class="col-sm-2 col-form-label">Image</label>
                                                <div class="col-sm-10">
                                                    <input type="file" class="form-control" name="image" id="image">
                                                    <img src="{{asset(Session()->get('MyAdmin')->image)}}" style="width: 100px;">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="offset-sm-2 col-sm-10">
                                                    <button type="submit" class="btn btn-primary">Save Profile</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="tab-pane" id="changePassword">
                                        <form id="ChangeForm" class="form-horizontal" method="post" action="{{route('administrator.change.password')}}">
                                            @csrf
                                            <div class="form-group row">
                                                <label for="oldPassword" class="col-sm-2 col-form-label">Old Password</label>
                                                <div class="col-sm-10">
                                                    <input type="password" name="oldPassword" class="form-control" id="oldPassword" placeholder="Old Password">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="newPassword" class="col-sm-2 col-form-label">New Password</label>
                                                <div class="col-sm-10">
                                                    <input type="password" name="newPassword" class="form-control" id="newPassword" placeholder="Old Password">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="confirmNewPassword" class="col-sm-2 col-form-label">Confirm New Password</label>
                                                <div class="col-sm-10">
                                                    <input type="password" class="form-control" id="confirmNewPassword" name="confirmNewPassword" placeholder="Old Password">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="offset-sm-2 col-sm-10">
                                                    <button id="changePasswordButton" type="button" class="btn btn-primary">Change Password</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- /.tab-pane -->
                                </div>
                                <!-- /.tab-content -->
                            </div><!-- /.card-body -->
                        </div>
                        <!-- /.nav-tabs-custom -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div><!-- /.container-fluid -->
        </section>
        <!-- /.content -->
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
<script type="text/javascript">
    $('#changePasswordButton').click(function () {
        $('#ChangeForm').ajaxSubmit({
            beforeSubmit : function () {
                showOverlay();
            },
            success : function (response) {
                var info=JSON.parse(response);
                hideOverlay();
                if(info.status=='false'){
                    showError('Error',info.message);
                }
                else{
                    showSuccess('Success','Password Changed Successfully will be redirected to login soon....');
                    setTimeout(function () {
                        window.location='{{route('administrator.logout')}}';
                    },5000);
                }

            },
            error : function (response) {
                hideOverlay();
                showError('Error',"Something went wrong please try again later");
            }
        });
    });
</script>
</html>
