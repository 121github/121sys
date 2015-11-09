// JavaScript Document

var login = {
    init: function() {
        $('.forgot-password-container').hide();

        $(document).on('click','.forgot-password',function(e){
            e.preventDefault();
            login.forgot_password();
        });

        $(document).on('click', '.close-forgot-password', function(e) {
            e.preventDefault();
            login.close_forgot_password($(this));
        });

        $(document).on('click', '.send-forgot-password', function(e) {
            e.preventDefault();
            login.send_forgot_password($(this));
        });
    },
    forgot_password: function() {
        var pagewidth = $(window).width() / 2;
        var moveto = pagewidth - 250;
        $('<div class="modal-backdrop actions in"></div>').appendTo(document.body).hide().fadeIn();
        $('.forgot-password-container').find('.forgot-password-panel').show();
        $('.forgot-password-content').show();
        $('.forgot-password-container').fadeIn()
        $('.forgot-password-container').animate({
            width: '500px',
            left: moveto,
            top: '10%'
        }, 1000);

        $('.forgot-password-form').find('input[name="username"]').val($('.form-signin').find('input[name="username"]').val());
    },
    close_forgot_password: function() {
        $('.modal-backdrop.actions').fadeOut();
        $('.forgot-password-container').fadeOut(500, function() {
            $('.forgot-password-content').show();
            $('.alert').addClass('hidden');
        });
    },
    send_forgot_password: function() {
        var username = $('.forgot-password-form').find('input[name="username"]').val();

        $.ajax({
            url: helper.baseUrl + 'user/send_email_reset_password',
            type: "POST",
            dataType: "JSON",
            data: { username:username }
        }).done(function(response) {
            if (response.success) {
                $('.forgot-password-error').hide();
                window.location.href= helper.baseUrl + 'user/login';
            }
            else {
                $('.forgot-password-error').html(response.msg);
                $('.forgot-password-error').show();
                flashalert.danger(response.msg);
            }
        });
    }
}

var restore_password = {
    init: function () {
        $(document).on('click', '.save-restored-password', function(e) {
            e.preventDefault();
            restore_password.save_restored_password($(this));
        });
    },
    save_restored_password: function(){
        var formData = 	$('.form-restore_password').serialize();
        $.ajax({
            url: helper.baseUrl + 'user/save_restored_password',
            type: "POST",
            dataType: "JSON",
            data: formData
        }).done(function(response) {
            if (response.success) {
                $('.restore-password-error').hide();
                window.location.href= helper.baseUrl + 'user/login';
            }
            else {
                $('.restore-password-error').html(response.msg);
                $('.restore-password-error').show();
                flashalert.danger(response.msg);
            }
        });
    }
}