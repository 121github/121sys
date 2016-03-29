// JavaScript Document
var email = {
    init: function (urn) {
		if(getCookie('placeholder_error')){
		var key = getCookie('placeholder_error');
		var mheader = "Missing placeholder data";
		var mbody = "<p class='text-danger'><span class='glyphicon glyphicon-info-sign'></span> The <strong>"+key+"</strong> placeholder was found in this email template but there is no data for this field. Please check this email carefully as will need to be edited where the missing placeholder is.</p>"	
		var mfooter = '<button data-dismiss="modal" class="btn btn-primary close-modal pull-right" type="button">Ok</button>';
		modals.load_modal(mheader,mbody,mfooter);	
		}
        this.urn = urn;
        $('.selectpicker').selectpicker({title: "Please select"});
        $('.tt').tooltip();
        $(document).on('click', '.send-email', function (e) {
            e.preventDefault();

            if (!email.validate_email_input()) {
                flashalert.danger("The recipient email address is invalid");
            }
            else {
                if ($('input[name="send_to"]').val() !== '' && $('input[name="send_from"]').val() !== '') {
                    email.send_email($(this));
                } else {
                    flashalert.danger("Please ensure both <b>from</b> and <b>to</b> fields are populated");
                }
            }
        });
        $(document).on('click', '.close-email', function (e) {
            e.preventDefault();
            window.history.back();
        });
        $(document).on('click', '.add-contact', function (e) {
            e.preventDefault();
            modal.add_contact($(this).attr('option'));
        });
        $(document).on('click', '.add-contact-option', function (e) {
            e.preventDefault();
            email.add_contact_option($(this).attr('item-id'), $(this).attr('email'), $(this).attr('option'));
        });
        $(document).on('click', '.delete-attach-btn', function (e) {
            e.preventDefault();
            email.delete_attachment($(this));
        });
        $("button[type=submit]").attr('disabled', false);

        //Empty attachment table
        email.empty_attachment_table();
        //start the function to load the groups into the table
        email.load_attachments();
        email.load_record_attachments();
    },
    validate_email_input: function() {
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

//"//i don't know 

        var validation = true;

        var send_to = ($('input[name="send_to"]').val().replace(" ","")).split(",");
        var send_cc = ($('input[name="cc"]').val().replace(" ","")).split(",");
        var send_bcc = ($('input[name="bcc"]').val().replace(" ","")).split(",");

        $('.to-msg').hide();
        $('.cc-msg').hide();
        $('.bcc-msg').hide();

        $.each(send_to, function (i, val) {
            if (val.length>0) {
                if (!(re.test(val))) {
                    $('.to-msg').show();
                    validation = false;
                }
                else {
                    $('.to-msg').hide();
                }
            }
        });

        $.each(send_cc, function (i, val) {
            if (val.length>0) {
                if (!(re.test(val))) {
                    $('.cc-msg').show();
                    validation = false;
                }
                else {
                    $('.cc-msg').hide();
                }
            }
        });

        $.each(send_bcc, function (i, val) {
            if (val.length>0) {
                if (!(re.test(val))) {
                    $('.bcc-msg').show();
                    validation = false;
                }
                else {
                    $('.bcc-msg').hide();
                }
            }
        });


        return validation;

    },
    send_email: function ($btn) {
   	$('#tinymce').html(tinyMCE.activeEditor.getContent());
        $.ajax({
            url: helper.baseUrl + "email/send_email",
            type: "POST",
            dataType: "JSON",
            data: $('#container-fluid form').serialize(),
			beforeSend:function(){
			$("button[type=submit]").hide().parent().append('<img id="pending-send" src="'+helper.baseUrl+'assets/img/ajax-loader.gif" />');	
			}
        }).done(function (response) {
            if (response.success) {
                flashalert.success(response.msg);
                window.history.back();
            }
            else {
                $("button[type=submit]").show().parent().find('#pending-send').remove();
                flashalert.danger(response.msg);
            }
        }).fail(function(response){
				$("button[type=submit]").show().parent().find('#pending-send').remove();
				flashalert.danger(response.responseText);
		});;
    },
    add_contact_option: function (id, email, option) {
        content = $('#container-fluid form').find('input[name=' + option + ']').val();
        if (content.length) {
            content = content + ', ';
        }
        $('#container-fluid form').find('input[name=' + option + ']').val(content + email);
        $('.' + id + option).text("Added");
    },
    //add a new attached file to the list of the new attachments
    attach_new_file: function (filename, path) {
        var data = {
            filename: filename,
            path: path,
            newFiles: $('#container-fluid form').find('input[name="template_attachments"]').val()
        };

        $.ajax({
            url: helper.baseUrl + 'templates/set_attached_files',
            type: "POST",
            dataType: "JSON",
            data: data
        }).done(function (response) {
            $('#container-fluid form').find('input[name="template_attachments"]').val(response.data);
            //Reload the new attachments table
            email.load_new_attachments(response.data_array);
        });
    },
    //Remove an attachment from the list of new attachments
    remove_new_attach: function (path) {
        //If the path exist as a new attachment, remove from the list
        var data = {path: path, newFiles: $('#container-fluid form').find('input[name="template_attachments"]').val()};

        $.ajax({
            url: helper.baseUrl + 'templates/unset_attached_files',
            type: "POST",
            dataType: "JSON",
            data: data
        }).done(function (response) {
            $('#container-fluid form').find('input[name="template_attachments"]').val(response.data);
            //Reload the new attachments table
            email.load_new_attachments(response.data_array);
        });
    },
    //Delete action for the remove button
    delete_attachment: function ($btn) {
        var id = $btn.attr('item-id');
        var path = $btn.attr('item-path');

        //Delte attachments file uploaded without save
        var data = {id: id, path: path};
        $.ajax({
            url: helper.baseUrl + "templates/delete_attachment_by_id",
            type: 'POST',
            dataType: "JSON",
            data: data,
            success: function (data) {
                //email.load_attachments();
                email.remove_new_attach(path);
                flashalert.warning("Attachment removed");
            }
        });
    },
    //Empty attachment table
    empty_attachment_table: function () {
        //Empty attachment table
        $tbody = $('.new_attach_table').find('tbody');
        $thead = $('.new_attach_table').find('thead');
        $tbody.empty();
        $thead.empty();
        $tbody = $('.attach_table').find('tbody');
        $thead = $('.attach_table').find('thead');
        $tbody.empty();
        $thead.empty();
        $tbody = $('.record_attach_table').find('tbody');
        $thead = $('.record_attach_table').find('thead');
        $tbody.empty();
        $thead.empty();

        //Set progress-bar 0%
        $('#container-fluid form').find('input[name="template_attachments"]').val("");
        $('#progress .progress-bar').css(
            'width',
            0 + '%'
        );
        $('#files').find('#filename').empty();
        $('#files').find('#file-status').empty();
    },
    //Load attachments from the database for a particular template
    load_attachments: function () {
        var data = {id: $('#container-fluid form').find('input[name="template_id"]').val()};
        $.ajax({
            url: helper.baseUrl + "templates/get_attachments_by_template_id",
            type: 'POST',
            dataType: "JSON",
            data: data,
            success: function (data) {
                var thead, tbody;
                $tbody = $('.attach_table').find('tbody');
                $thead = $('.attach_table').find('thead');
                $tbody.empty();
                $thead.empty();
                if (jQuery.isEmptyObject(data['data'])) {
                    thead += "<th></th>";
                } else {
                    thead += "<th>Template Attachments</th>";
                }
                $.each(data['data'], function (key, val) {
                    tbody += "<tr>"
                    thead += "<th></th>";
                    tbody += "<td><input type='checkbox' checked name='" + key + "' value='checked'> <a target='_blank' href='" + val['path'] + "'>" + val['name'] + "</a></td>";
                });
                $('#attachments').find('.attach_table thead').append(thead);
                $('#attachments').find('.attach_table tbody').append(tbody);
                $('#attachments').fadeIn();
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });

        $('.ajax-table').fadeOut(1000, function () {
            $('#container-fluid form').fadeIn();
        });
    },
    //Load record attachments from the database for a particular record
    load_record_attachments: function () {
        var data = {urn: $('#container-fluid form').find('input[name="urn"]').val()};
        $.ajax({
            url: helper.baseUrl + "records/get_attachments",
            type: 'POST',
            dataType: "JSON",
            data: data,
            success: function (data) {
                var thead, tbody;
                $tbody = $('.record_attach_table').find('tbody');
                $thead = $('.record_attach_table').find('thead');
                $tbody.empty();
                $thead.empty();
                if (jQuery.isEmptyObject(data['data'])) {
                    thead += "<th></th>";
                } else {
                    thead += "<th>Record Attachments</th>";
                }
                $.each(data['data'], function (key, val) {
                    tbody += "<tr>"
                    thead += "<th></th>";
                    tbody += "<td><input type='checkbox' name='record_" + val['attachment_id'] + "' value='checked'> <a target='_blank' href='" + val['path'] + "'>" + val['name'] + "</a></td>";
                });
                $('#attachments').find('.record_attach_table thead').append(thead);
                $('#attachments').find('.record_attach_table tbody').append(tbody);
                $('#attachments').fadeIn();
            },
            error: function (jqXHR, textStatus, errorThrown) {
            }
        });

        $('.ajax-table').fadeOut(1000, function () {
            $('#container-fluid form').fadeIn();
        });
    },
    //Load the new attachments table
    load_new_attachments: function (data) {
        var thead, tbody;

        $('#upload-status').fadeIn();

        $tbody = $('.new_attach_table').find('tbody');
        $thead = $('.new_attach_table').find('thead');
        $tbody.empty();
        $thead.empty();
        if (data.length == 0) {
            thead += "<th></th>";
        } else {
            thead += "<th>New Attachments</th>";
        }
        $.each(data, function (key, val) {
            tbody += "<tr>"
            tbody += "<td><a target='_blank' href='" + val.substring(0, email.stripos(val, '?')) + "'>" + val.substring(email.stripos(val, '?') + 1) + "</a></td><td><button item-path='" + val + "' class='marl btn btn-danger delete-attach-btn'>Remove</button></td>";
        });
        $('#attachments').find('.new_attach_table thead').append(thead);
        $('#attachments').find('.new_attach_table tbody').append(tbody);
        $('#attachments').fadeIn();
        $('#upload-status').fadeOut(1000);
    },
    stripos: function (f_haystack, f_needle, f_offset) {
        //  discuss at: http://phpjs.org/functions/stripos/
        // original by: Martijn Wieringa
        //  revised by: Onno Marsman
        //   example 1: stripos('ABC', 'a');
        //   returns 1: 0

        var haystack = (f_haystack + '')
            .toLowerCase();
        var needle = (f_needle + '')
            .toLowerCase();
        var index = 0;

        if ((index = haystack.indexOf(needle, f_offset)) !== -1) {
            return index;
        }
        return false;
    }
}

/* ==========================================================================
 MODALS ON THIS PAGE
 ========================================================================== */
var modal = {

    add_contact: function (option) {
       modal_header.text('Add Contact');
        //Get the contacts
        var urn = $('#container-fluid form').find('input[name="urn"]').val();
        var contacts;
        $.ajax({
            url: helper.baseUrl + "email/get_contacts",
            type: "POST",
            dataType: "JSON",
            data: {urn: urn}
        }).done(function (response) {
            if (response.success) {
                contacts = '<table class="table"><thead><tr><th>Name</th><th>Email</th><th></th></tr></thead><tbody>';
                var i = 1;
                $.each(response.data, function (key, val) {
                    options = '<span class="glyphicon glyphicon-plus pull-right add-contact-option" option="' + option + '" email="' + val["email"] + '"item-id="' + key + '"></span>';
                    contacts += '<tr><td>' + val["name"] + '</td><td>' + val["email"] + '</td><td class="' + key + option + '">' + options + '</td></tr>';
                    i += 1;
                });
                contacts += '</tbody></table>';
                modal_body.append(contacts);
            }
        });
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        })
		modal_body.text('Select the contact that you want to add').append('<br /><br />').append(contacts);
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function (e) {
            $('#modal').modal('toggle');
        });
    }
}