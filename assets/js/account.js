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

            $('#details-form').find('input[name="user_id"]').val($(this).attr('id'));
            details.load_details();
        });

        $(document).on("click", '.edit-details-btn', function(e) {
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

        details.load_details();
    },
    load_details: function() {
        var user_id = $('#details-form').find('input[name="user_id"]').val();

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
            }
        });
    },
    edit_details: function(btn) {
        $(".save-details-btn").attr('disabled',false);

        var pagewidth = $(window).width() / 2;
        var moveto = pagewidth - 250;

        $('<div class="modal-backdrop details in"></div>').appendTo(document.body).hide().fadeIn();
        $('.details-container').find('.details-panel').show();
        $('.details-content').show();
        $('.details-container').fadeIn()
        $('.details-container').animate({
            width: '500px',
            left: moveto,
            top: '10%'
        }, 1000);

        var email = $('.email').html();
        var telephone = $('.telephone').html();
        var ext = $('.ext').html();

        $('#details-form').find('input[name="email_form"]').val(email);
        $('#details-form').find('input[name="telephone_form"]').val(telephone);
        $('#details-form').find('input[name="ext_form"]').val(ext);

    },
    close_details: function() {

        $('.modal-backdrop.details').fadeOut();
        $('.details-container').fadeOut(500, function() {
            $('.details-content').show();
            $('.alert').addClass('hidden');
        });
    },

    save_details: function() {
        $(".save-details-btn").attr('disabled','disabled');
        $.ajax({
            url: helper.baseUrl + 'user/save_contact_details',
            type: "POST",
            dataType: "JSON",
            data: $('#details-form').serialize()
        }).done(function(response) {
            if (response.success) {
                //Reload details table
                details.load_details();
                //Close edit form
                details.close_details();

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