// JavaScript Document
var sms = {
    init: function (urn) {
		if(getCookie('placeholder_error')){
		var key = getCookie('placeholder_error');
		var mheader = "Missing placeholder data";
		var mbody = "<p class='text-danger'><span class='glyphicon glyphicon-info-sign'></span> The <strong>"+key+"</strong> placeholder was found in this sms template but there is no data for this field. Please check this sms carefully as will need to be edited where the missing placeholder is.</p>"	
		var mfooter = '<button data-dismiss="modal" class="btn btn-primary close-modal pull-right" type="button">Ok</button>';
		modals.load_modal(mheader,mbody,mfooter);	
		}
        this.urn = urn;
        $('.selectpicker').selectpicker({title: "Please select"});
        $('.tt').tooltip();
        $(document).on('click', '.send-sms', function (e) {
            e.preventDefault();

            if (!sms.validate_phone()) {
                flashalert.danger("The recipient sms address is invalid");
            }
            else {
                if ($('input[name="send_to"]').val() !== '') {
                    sms.send_sms($(this));
                } else {
                    flashalert.danger("Please ensure the <b>to</b> field is populated");
                }
            }
        });
        $(document).on('click', '.close-sms', function (e) {
            e.preventDefault();
            window.history.back();
        });
        $(document).on('click', '.add-contact', function (e) {
            e.preventDefault();
            modal.add_contact($(this).attr('option'));
        });
        $(document).on('click', '.add-contact-option', function (e) {
            e.preventDefault();
            sms.add_contact_option($(this).attr('item-id'), $(this).attr('sms'), $(this).attr('option'));
        });

        $("button[type=submit]").attr('disabled', false);

        //Empty attachment table
        sms.empty_attachment_table();
        //start the function to load the groups into the table
        sms.load_attachments();
    },
    validate_phone: function() {
        var re = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/;

        var validation = true;

        var send_to = ($('input[name="send_to"]').val().replace(" ","")).split(",");

        $('.to-msg').hide();

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
        return validation;

    },
    send_sms: function ($btn) {

        $('textarea[name="body"]').html(btoa($('#summernote').code()));
        $.ajax({
            url: helper.baseUrl + "sms/send_sms",
            type: "POST",
            dataType: "JSON",
            data: $('form').serialize(),
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
        });
    },
    add_contact_option: function (id, sms, option) {
        content = $('form').find('input[name=' + option + ']').val();
        if (content.length) {
            content = content + ', ';
        }
        $('form').find('input[name=' + option + ']').val(content + sms);
        $('.' + id + option).text("Added");
    },
}

/* ==========================================================================
 MODALS ON THIS PAGE
 ========================================================================== */
var modal = {

    add_contact: function (option) {
       modal_header.text('Add Contact');
        //Get the contacts
        var urn = $('form').find('input[name="urn"]').val();
        var contacts;
        $.ajax({
            url: helper.baseUrl + "sms/get_contacts",
            type: "POST",
            dataType: "JSON",
            data: {urn: urn}
        }).done(function (response) {
            if (response.success) {
                contacts = '<table class="table"><thead><tr><th>Name</th><th>SMS</th><th></th></tr></thead><tbody>';
                var i = 1;
                $.each(response.data, function (key, val) {
                    options = '<span class="glyphicon glyphicon-plus pull-right add-contact-option" option="' + option + '" sms="' + val["sms"] + '"item-id="' + key + '"></span>';
                    contacts += '<tr><td>' + val["name"] + '</td><td>' + val["sms"] + '</td><td class="' + key + option + '">' + options + '</td></tr>';
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