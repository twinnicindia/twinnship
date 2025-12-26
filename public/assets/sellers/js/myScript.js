var registerMobile=$('#registerMobile');
var registerEmail=$('#registerEmail');
// Example starter JavaScript for disabling form submissions if there are invalid fields
(function() {
    'use strict';
    window.addEventListener('load', function() {
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.getElementsByClassName('needs-validation');
        // Loop over them and prevent submission
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
    registerMobile.keyup(function () {
        var that=$(this);
        that.val(that.val().replace(/[^0-9]/g,''));
    });
    registerMobile.blur(function () {
        if(registerMobile.val()!==''){
            $.ajax({
                type : 'get',
                url : base_path + 'check-seller-mobile/' + registerMobile.val(),
                success : function (response) {
                    var info=JSON.parse(response);
                    if(info.status=='false'){
                        registerMobile.addClass('is-invalid');
                        registerMobile.next().html('Oops... This Mobile Number is already Registered with us');
                        registerMobile.val('').focus();
                    }
                },
                error : function (response) {
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

    $('#registerEmail').on('keyup', function() {
        console.log(this.value.length);
        if(this.value.length > 0)
        {
            registerEmail.removeClass('is-invalid');
            registerEmail.next().html(''); 
        }
    });

    $('#registerMobile').on('keyup', function() {
        console.log(this.value.length);
        if(this.value.length > 0)
        {
            registerMobile.removeClass('is-invalid');
            registerMobile.next().html(''); 
        }
    });

    registerEmail.blur(function () {
        if(registerEmail.val()!==''){
            $.ajax({
                type : 'get',
                url : base_path + 'check-seller-email/' + registerEmail.val(),
                success : function (response) {
                    var info=JSON.parse(response);
                    if(info.status=='false'){
                        // $.notify(" Oops... This Email Address is already registered with us!", {
                        //     blur: 0.2,
                        //     delay: 0,
                        //     verticalAlign: "top",
                        //     animationType: "scale",
                        //     align: "right",
                        //     type: "danger",
                        //     icon: "close"
                        // });
                        registerEmail.addClass('is-invalid');
                        registerEmail.next().html('Oops... This Email Address is already Registered with us');
                        registerEmail.val('').focus();
                    }
                },
                error : function (response) {
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
})();
