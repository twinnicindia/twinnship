<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Login Page | {{env('appTitle')}}</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{url('/')}}/assets/admin/plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{url('/')}}/assets/admin/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{url('/')}}/assets/admin/dist/css/adminlte.min.css">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="#"><b>{{env('appTitle')}}</b></a>
    </div>
    <!-- /.login-logo -->
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Sign in to start your session</p>

            <form action="{{route('administrator.check_login')}}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="input-group mb-3">
                    <input type="text" class="form-control" placeholder="Email or Mobile" name="username" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" class="form-control" placeholder="Password" name="password" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" id="remember">
                            <label for="remember">
                                Remember Me
                            </label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                    </div>
                    <!-- /.col -->
                </div>
            </form>

            <!-- /.social-auth-links -->

        </div>
        <!-- /.login-card-body -->
    </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<!-- jQuery -->
<script src="{{url('/')}}/assets/admin/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="{{url('/')}}/assets/admin/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- jquery-validation -->
<script src="{{url('/')}}/assets/admin/plugins/jquery-validation/jquery.validate.min.js"></script>
<script src="{{url('/')}}/assets/admin/plugins/jquery-validation/additional-methods.min.js"></script>

<!-- DataTables -->
<script src="{{url('/')}}/assets/admin/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="{{url('/')}}/assets/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="{{url('/')}}/assets/admin/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{url('/')}}/assets/admin/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<!-- AdminLTE App -->
<script src="{{url('/')}}/assets/admin/dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{url('/')}}/assets/admin/dist/js/demo.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>


<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap-switch-button@1.1.0/dist/bootstrap-switch-button.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"></script>
<!-- Toastr -->
<script src="{{url('/')}}/assets/admin/plugins/toastr/toastr.min.js"></script>

<script type="text/javascript">
    @php
        if(Session()->has('notification'))
        {
            switch (Session('notification')['type']){
                case "success":
                    echo "showSuccess('".Session('notification')['title']."','".Session('notification')['message']."')";
                    break;
                case "error":
                    echo "showError('".Session('notification')['title']."','".Session('notification')['message']."')";
                    break;
            }
            Session()->forget('notification');
        }
    @endphp
    $(document).ready(function () {
        $("#example1").DataTable({
            "responsive": true,
            "autoWidth": false,
        });
    });
    function showSuccess(title,message) {
        $(document).Toasts('create', {
            class: 'bg-success',
            title: title,
            subtitle: 'Subtitle',
            body: message,
            delay:5000,
            autohide: true
        });
    }
    function showError(title,message) {
        $(document).Toasts('create', {
            class: 'bg-danger',
            title: title,
            subtitle: 'Subtitle',
            body: message,
            delay:5000,
            autohide: true
        });
    }
    function showWarning(title,message) {
        $(document).Toasts('create', {
            class: 'bg-warning',
            title: title,
            subtitle: 'Subtitle',
            body: message,
            delay:5000,
            autohide: true
        });
    }
    function showInfo(title,message) {
        $(document).Toasts('create', {
            class: 'bg-info',
            title: title,
            subtitle: 'Subtitle',
            body: message,
            delay:5000,
            autohide: true
        });
    }
    function showDefault(title,message) {
        $(document).Toasts('create', {
            class: 'bg-default',
            title: title,
            subtitle: 'Subtitle',
            body: message,
            delay:5000,
            autohide: true
        });
    }
</script>

</body>
</html>
