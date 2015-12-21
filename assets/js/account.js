// JavaScript Document

/* ==========================================================================
 DETAILS
 ========================================================================== */
var details = {
    init: function() {
        $(document).on("click", ".user-filter", function(e) {
            e.preventDefault();
            $icon = $(this).closest('ul').prev('button').find('span');
            $(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");

            $('#edit-details-btn').attr('data-id',$(this).attr('id'));
            details.load_details();
        });

        $(document).on("click", '#edit-details-btn', function(e) {
            e.preventDefault();
            details.edit_details($(this));
        });

        $(document).on('click', '.close-details-btn', function(e) {
            e.preventDefault();
            details.close_details();
        });

        $(document).on('click', '.save-details-btn', function(e) {
            e.preventDefault();
            details.save_details();
        });

        $(document).on('click', '.reset-failed-logins-btn', function(e) {
            e.preventDefault();
            details.reset_failed_logins();
        });

        details.load_details();
    },
    load_details: function() {
        var user_id = $('#edit-details-btn').attr('data-id')

        $.ajax({
            url: helper.baseUrl + 'user/get_user_by_id',
            type: "POST",
            dataType: "JSON",
            data: {'user_id': user_id}
        }).done(function(response) {
            if (response.success) {
                $('.main-name').html(response.data[0].name);
                $('.main-role').html("["+response.data[0].role_name+"]");
                $('.username').html(response.data[0].username);
                $('.name').html(response.data[0].name);
                $('.role').html(response.data[0].role_name);
                $('.group').html(response.data[0].group_name);
                $('.team').html(response.data[0].team_name);
                $('.email').html(response.data[0].user_email);
                $('.telephone').html(response.data[0].user_telephone);
                $('.ext').html(response.data[0].ext);
				$('.home_postcode').html(response.data[0].home_postcode);
                $('.user_status').html(response.data[0].user_status);
                $('.login_mode').html(response.data[0].login_mode);
                $('.last_login').html(response.data[0].last_login);
                var failed_logins = response.data[0].failed_logins;
                $('.failed_logins').html((failed_logins == 0?failed_logins:failed_logins+'<span class="btn btn-sm reset-failed-logins-btn" style="color: red">Reset</span>'));
                $('.last_failed_login').html(response.data[0].last_failed_login);
                $('.reload_session').html(response.data[0].reload_session);
                $('.token').html(response.data[0].token);
                $('.pass_changed').html(response.data[0].pass_changed);
                $('.attendee').html(response.data[0].attendee);
                $('.reset_pass_token').html((response.data[0].reset_pass_token?"Yes":""));
            } else {
			flashalert.danger(response.error);	
			}
        });
    },
    edit_details: function(btn) {
        $('#details-form').find('input[name="telephone_form"]').numeric();
        $('#details-form').find('input[name="ext_form"]').numeric();
		$.ajax({ url:helper.baseUrl+'modals/user_account_details',
		type:"POST",
		data:{id:btn.attr('data-id') },
		dataType:"HTML"
	}).done(function(response){
		var mheader = "Edit User Details";
		var mbody = response;
		var mfooter = '<span class="marl btn btn-success pull-right save-details-btn">Save</span> <button data-dismiss="modal" class="btn btn-default close-modal pull-left">Cancel</button>';
		modals.load_modal(mheader,mbody,mfooter);
		var email = $('.email').html();
        var telephone = $('.telephone').html();
        var ext = $('.ext').html();
 		var home_postcode = $('.home_postcode').html();
        $('#details-form').find('input[name="email_form"]').val(email);
        $('#details-form').find('input[name="telephone_form"]').val(telephone);
        $('#details-form').find('input[name="ext_form"]').val(ext);
		 $('#details-form').find('input[name="home_postcode"]').val(home_postcode);
	});

       

    },
    close_details: function() {

        $('.modal-backdrop.details').fadeOut();
        $('.details-container').fadeOut(500, function() {
            $('.details-content').show();
            $('.alert').addClass('hidden');
        });
    },

    save_details: function() {
        $.ajax({
            url: helper.baseUrl + 'user/save_contact_details',
            type: "POST",
            dataType: "JSON",
            data: $('#details-form').serialize()
        }).done(function(response) {
            if (response.success) {
                //Reload details table
                details.load_details();
				 $('.close-modal').trigger('click');
                flashalert.success(response.msg);
            }
            else {
                flashalert.danger(response.msg);
            }
        });
    },
    reset_failed_logins: function(){
        var user_id = $('#edit-details-btn').attr('data-id')
        $.ajax({
            url: helper.baseUrl + 'user/reset_failed_logins',
            type: "POST",
            dataType: "JSON",
            data: {'user_id': user_id}
        }).done(function(response) {
            if (response.success) {
                //Reload details table
                details.load_details();
                flashalert.success(response.msg);
            }
            else {
                flashalert.danger(response.msg);
            }
        });
    }
}

/* ==========================================================================
 CHANGE PASSWORD
 ========================================================================== */
var password = {
    init: function() {
        $(document).on("click", '.save-password-btn', function(e) {
            e.preventDefault();
            export_data.save_password($(this));
        });
    }
}