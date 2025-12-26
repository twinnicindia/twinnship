<!-- jQuery -->
<script src="{{url('/')}}/assets/admin/plugins/jquery/jquery.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.6.2/tinymce.min.js"></script>

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
    <?php if(Session()->get('MyAdmin')->type != 'admin' && Session()->get('ins','')=='n') { ?>
        $('#addBtn').remove();
    <?php } ?>
    <?php if(Session()->get('MyAdmin')->type != 'admin' && Session()->get('del','')=='n') { ?>
        $('#example1 .remove_data').remove();
        $('#deleteSelectedButton').remove();
    <?php } ?>
    <?php if(Session()->get('MyAdmin')->type != 'admin' && Session()->get('modi','')=='n') { ?>
        $('#example1 .modify_data').remove();
    <?php } ?>

    // $(document).ready(function () {
    //     $("#example1").DataTable({
    //         "responsive": true,
    //         "autoWidth": false,
    //     });
    // });
    $(document).ready( function () {
    $('#example1').DataTable();
    });

    function showSuccess(title,message) {
        $(document).Toasts('create', {
            class: 'bg-success',
            title: title,
            icon : 'fas fa-check',
            body: message,
            delay:3000,
            autohide: true
        });
    }
    function showError(title,message) {
        $(document).Toasts('create', {
            class: 'bg-danger',
            icon : 'fas fa-exclamation-triangle',
            title: title,
            body: message,
            delay:3000,
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
</script>
