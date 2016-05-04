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
	var username = $('.form-signin').find('input[name="username"]').val();
       var mbody =   '<form class="form-horizontal forgot-password-form">'+
                  '<div class="form-group">'+
                      '<p>Enter your username to recieve password reset instructions by email</p>'+
                      '<span style="color: red; font-size: 11px; display: none;" class="forgot-password-error"></span>'+
                      '<input type="text" name="username" value="'+username+'" class="form-control" placeholder="Username" required>'+
                  '</div></form>'
					  mheader = "Forgot Password";
					  mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Cancel</button><button class="marl btn btn-success send-forgot-password pull-right">Send</button>';
					  modals.load_modal(mheader,mbody,mfooter);

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