// JavaScript Document

/* ==========================================================================
 DETAILS
 ========================================================================== */
var details = {
    init: function() {
        $('[data-toggle="tooltip"]').tooltip();
        $(document).on("change", "#user-filter", function(e) {

            e.preventDefault();
            $('#edit-details-btn').attr('data-id',$(this).val());
            $('#add-user-address').attr('data-user-id',$(this).val());
            details.load_details();
        });

        $(document).on("click", '#edit-details-btn', function(e) {
            e.preventDefault();
            details.edit_details($(this));
        });

        $modal.on('click', '.edit-details-btn', function(e) {
            e.preventDefault();
            details.save_details();
        });

        $(document).on('click', '.reset-failed-logins-btn', function(e) {
            e.preventDefault();
            details.reset_failed_logins();
        });

        $(document).on('click', '[data-modal="add-user-address"]', function (e) {
            e.preventDefault();
            details.add_user_address($(this).attr('data-user-id'), $(this).attr('data-address-id'));
        });

        $(document).on('click', '[data-modal="remove-user-address"]', function (e) {
            e.preventDefault();
            details.remove_user_address($(this).attr('data-user-id'), $(this).attr('data-address-id'), $(this).attr('data-primary'));
        });

        $(document).on('click', '#get-address', function (e) {
            e.preventDefault();
            details.get_addresses();
        });

        $(document).on('click', '#save-address-user-btn', function(e) {
            e.preventDefault();
            details.save_address_user();
        });

        $(document).on('click', '#delete-address-user-btn', function(e) {
            e.preventDefault();
            details.delete_address_user($(this).attr('data-user-id'), $(this).attr('data-address-id'),$(this).attr('data-primary'));
        });

        $(document).on("click", '#google-login-btn', function (e) {
            e.preventDefault();
            details.login($(this).attr('data-user-id'));
        });

        $(document).on("click", '#google-logout-btn', function (e) {
            e.preventDefault();
            details.logout($(this).attr('data-user-id'));
        });

        $(document).on("click", '.sync-google-cal', function(e) {
            e.preventDefault();
            modals.users.sync_google_cal($(this).attr('data-user-id'));
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
                if (!response.data[0].google) {
                    if (helper.permissions['google sync'] > 0 && (user_id == response.session_user_id)) {
                        $('.google-account').html("<button class='btn btn-info btn-xs' data-user-id='"+user_id+"' id='google-login-btn'><i class='fa fa-google white'>oogle</i><span class='small'> [Sign In]</span></button>");
                        $('.google-content').find('.google-login-msg').html("Login into your google account");
                    }
                    else {
                        $('.google-account').html("");
                        $('.google-content').find('.google-login-msg').html("This user is not connected to a google account");
                    }
                    $('.google-content').find('.google-login-msg').show();
                    $('.google-content').find('.google-data').hide();
                    $('.google-name').html("");
                    $('.google-picture').html("");
                }
                else {
                    if (helper.permissions['google sync'] > 0 && (user_id == response.session_user_id)) {
                        $('.google-account').html("<button class='btn btn-success btn-xs' data-user-id='"+user_id+"' id='google-logout-btn'><i class='fa fa-google white'>oogle</i><span class='small'> [Logout]</span></button>");
                    }
                    else {
                        $('.google-account').html("");
                    }
                    $('.google-content').find('.google-login-msg').hide();
                    $('.google-content').find('.google-data').show();

                    //Get user info
                    $.ajax({
                        url: helper.baseUrl + 'booking/get_google_data',
                        type: "POST",
                        dataType: "JSON",
                        data: {'user_id': response.data[0].user_id}
                    }).done(function(user_response) {
                        if (user_response.success) {
                            $('.google-name').html(user_response.userInfo.name);
                            $('.google-picture').html("<img src='"+user_response.userInfo.picture+"' width='100%'>");
                            var google_details = "<tbody>" +
                                    "<tr>" +
                                        "<td style='font-weight: bold'>Name</td>" +
                                        "<td style='text-align: right'>"+user_response.userInfo.name+"</td>" +
                                    "</tr>" +
                                    "<tr>" +
                                        "<td style='font-weight: bold'>Email</td>" +
                                        "<td style='text-align: right'>"+user_response.userInfo.email+"</td>" +
                                    "</tr>" +
                                    "<tr>" +
                                        "<td style='font-weight: bold'>Google plus</td>" +
                                        "<td style='text-align: right'><a href='"+user_response.userInfo.link+"' target='_blank'><i class='fa fa-google-plus'></i></a></td>" +
                                    "</tr>" +
                                "</tbody>";
                            $('.google-details').html(google_details);

                            $.ajax({
                                url: helper.baseUrl + 'booking/get_google_calendars_by_user',
                                type: "POST",
                                dataType: "JSON",
                                data: {'user_id': response.data[0].user_id}
                            }).done(function(calendar_response) {
                                var google_calendars = "";
                                if (calendar_response.success) {
                                    google_calendars = "<thead>" +
                                            "<tr>" +
                                                "<th style='font-weight: bold'>Calendar</th>" +
                                                "<th style='font-weight: bold'>Campaign</th>" +
                                            "</tr>" +
                                        "</thead>" +
                                        "<tbody>";
                                    $.each(calendar_response.data, function (index, value) {
                                        google_calendars +=
                                                "<tr>" +
                                                "<td>"+value.calendar_name+"</td>" +
                                                "<td>"+value.campaign_name+"</td>" +
                                                "</tr>";
                                    });
                                    google_calendars += "</tbody>";
                                }
                                else {
                                    google_calendars = "No calendars added";
                                }
                                $('.google-calendars').html(google_calendars);
                            });
                        }
                    });
                }

                $.ajax({
                    url: helper.baseUrl + 'user/get_user_addresses_by_id',
                    type: "POST",
                    dataType: "JSON",
                    data: {'user_id': user_id}
                }).done(function(add_response) {
                    var addresses = '<table class="table ajax-table">'
                    if (add_response.success) {
                        $.each(add_response.data, function (index, value) {
                            addresses += '<tr>'
                                         + '<td>'+value.description+'</td>'
                                         + '<td>'+(value.primary == 1?"<span class='glyphicon glyphicon-home pull-right'></span>":"")+'</td>'
                                         + '<td>'+value.add1+' '+value.add2+'</td>'
                                         + '<td>'+value.postcode+'</td>'
                                         + '<td><span class="glyphicon glyphicon-edit pointer marl pull-right" id="edit-user-address" data-modal="add-user-address" data-user-id="'+value.user_id+'" data-address-id="'+value.address_id+'"></span></td>'
                                         + '<td><span class="glyphicon glyphicon-remove pointer marl pull-right" id="edit-user-address" data-modal="remove-user-address" data-user-id="'+value.user_id+'" data-address-id="'+value.address_id+'" data-primary="'+value.primary+'"></span></td>'
                                       + '</tr>'
                        });
                        addresses += '</table>'
                        $('.user-addresses').html(addresses);
                    }
                });
            } else {
			    flashalert.danger(response.error);
			}
        });
    },

    login: function(user_id) {
        window.location.href = helper.baseUrl + 'google/authenticate?id='+user_id;
    },
    logout: function(user_id) {
        window.location.href = helper.baseUrl + 'google/logout?id='+user_id;
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
		var mfooter = '<span class="marl btn btn-success pull-right edit-details-btn">Save</span> ' +
                      '<button data-dismiss="modal" class="btn btn-default close-modal pull-left">Cancel</button>';
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
    },

    add_user_address: function (user_id, address_id) {
        $.ajax({
            url: helper.baseUrl + 'user/add_user_address',
            type: 'POST',
            dataType: 'html',
            data: {
                user_id: user_id,
                address_id: address_id,
            }
        }).done(function (response) {
            var mheader = "User Addresss";
            var mbody = '<div class="row"><div class="col-lg-12">' + response + '</div></div>';
            var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>' +
                          '<button class="btn btn-primary pull-right" id="save-address-user-btn" type="button">Save</button>';
            modals.load_modal(mheader, mbody, mfooter);
            modal_body.css('overflow', 'visible');
            modal_body.find('input[name="house-number"]').numeric();
            if (address_id) {
                modal_body.find('input[name="address_id"]').val(address_id);
            }

            $('#primary-toggle').bootstrapToggle({
                onstyle: 'success',
            }).show().change(function(){
                if($('#primary-toggle').prop('checked')==true){
                    $('#user-address-form').find('input[name="primary"]').val('1');
                } else {
                    $('#user-address-form').find('input[name="primary"]').val('0');
                }
            });

            if (address_id) {
                $.ajax({
                    url: helper.baseUrl + 'user/get_user_address',
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        address_id: address_id,
                    }
                }).done(function (add_response) {
                    if (add_response.success) {
                        $('#user-address-form').find('input[name="description"]').val(add_response.data[0].description);
                        $('#user-address-form').find('input[name="primary"]').val(add_response.data[0].primary);
                        $('#user-address-form').find('input[name="postcode"]').val(add_response.data[0].postcode);
                        $('#user-address-form').find('input[name="add1"]').val(add_response.data[0].add1);
                        $('#user-address-form').find('input[name="add2"]').val(add_response.data[0].add2);
                        $('#user-address-form').find('input[name="add3"]').val(add_response.data[0].add3);
                        $('#user-address-form').find('input[name="add4"]').val(add_response.data[0].add4);
                        $('#user-address-form').find('input[name="locality"]').val(add_response.data[0].locality);
                        $('#user-address-form').find('input[name="city"]').val(add_response.data[0].city);
                        $('#user-address-form').find('input[name="county"]').val(add_response.data[0].county);
                        $('#user-address-form').find('input[name="country"]').val(add_response.data[0].country);

                        if (add_response.data[0].primary == 1) {
                            $('#primary-toggle').bootstrapToggle('on');
                        }
                        else {
                            $('#primary-toggle').bootstrapToggle('off');
                        }
                    }
                });
            }
        });
    },

    remove_user_address: function (user_id, address_id, primary) {

        var mheader = "User Addresss";
        var mbody = '<div class="row"><div class="col-lg-12">Do you want to remove this address?</div></div>';
        var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>' +
            '<button class="btn btn-primary pull-right" id="delete-address-user-btn" data-user-id="'+user_id+'" data-address-id="'+address_id+'" data-primary="'+primary+'" type="button">Remove</button>';
        modals.load_modal(mheader, mbody, mfooter);
        modal_body.css('overflow', 'visible');
    },

    get_addresses: function () {
        var addresses;
        var postcode = $('#user-address-form').find('#postcode').val();
        var house_number = $('#user-address-form').find('#house-number').val();
        $('#user-address-form').find('#collapse input').val('');
        $('#user-address-form').find('input[name="postcode"]').val(postcode);

        $.ajax({
            url: helper.baseUrl + 'ajax/get_addresses_by_postcode',
            type: "POST",
            dataType: "JSON",
            data: {postcode: postcode, house_number: house_number}
        }).done(function (response) {
            if (response.success) {
                $('#user-address-form').find('#postcode').val(response.postcode);
                addresses = response.data;
                flashalert.info("Please select the correct address");
                var options = "<option value=''>Select one address...</option>";

                $.each(response.data, function (i, val) {
                    options += '<option value="' + i + '">' +
                        (val.add1 ? val.add1 : '') +
                        (val.add2 ? ", " + val.add2 : '') +
                        (val.add3 ? ", " + val.add3 : '') +
                        (val.add4 ? ", " + val.add4 : '') +
                        (val.locality ? ", " + val.locality : '') +
                        (val.city ? ", " + val.city : '') +
                        (val.county ? ", " + val.county : '') +
                        (typeof val.postcode_io.country != "undefined" ? ", " + val.postcode_io.country : '') +
                        (val.postcode ? ", " + val.postcode : '') +
                        '</option>';
                });
                $('#user-address-form').find('#addresspicker')
                    .html(options)
                    .selectpicker('refresh');

                //If the house number is found set this option by default
                if (response.address_selected !== null && response.address_selected !== undefined) {
                    var address = addresses[response.address_selected];
                    $('#user-address-form').find('#add1').val(address.add1);
                    $('#user-address-form').find('#add2').val(address.add2);
                    $('#user-address-form').find('#add3').val(address.add3);
                    $('#user-address-form').find('#add4').val(address.add4);
                    $('#user-address-form').find('#locality').val(address.locality);
                    $('#user-address-form').find('#city').val(address.city);
                    $('#user-address-form').find('#county').val(address.county);
                    $('#user-address-form').find('#country').val(address.postcode_io.country);

                    $('#user-address-form').find('#addresspicker')
                        .val(response.address_selected)
                        .selectpicker('refresh');
                }
                modal_body.css('overflow', 'visible');
                $('#addresspicker-div').show();
            }
            else {
                modal_body.css('overflow', 'auto');
                $('#addresspicker-div').hide();
                flashalert.danger("No address was found. Please enter manually");
                $('#complete-address').trigger('click');
            }
        });

        $('.addresspicker').change(function () {

            var selectedId = $(this).val();
            var address = addresses[selectedId];
            $('#user-address-form').find('#postcode').val(address.postcode);
            $('#user-address-form').find('#house_number').val(address.house_number);
            $('#user-address-form').find('#add1').val(address.add1);
            $('#user-address-form').find('#add2').val(address.add2);
            $('#user-address-form').find('#add3').val(address.add3);
            $('#user-address-form').find('#add4').val(address.add4);
            $('#user-address-form').find('#locality').val(address.locality);
            $('#user-address-form').find('#city').val(address.city);
            $('#user-address-form').find('#county').val(address.county);
            $('#user-address-form').find('#country').val(address.postcode_io.country);

        });
    },

    save_address_user: function() {
        $.ajax({
            url: helper.baseUrl + 'user/save_address_user',
            type: "POST",
            dataType: "JSON",
            data: $('#user-address-form').serialize()
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

    delete_address_user: function(user_id, address_id, primary) {
        $.ajax({
            url: helper.baseUrl + 'user/delete_address_user',
            type: "POST",
            dataType: "JSON",
            data: {user_id: user_id, address_id: address_id, primary: primary}
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