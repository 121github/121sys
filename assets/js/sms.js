// JavaScript Document
var sms = {
    init: function (urn) {
        //Max length for sms text
        var maxLength = 305;
        $('textarea').keyup(function() {
            var length = $(this).val().length;
            var length = maxLength-length;
            $('#chars').text(length);
        });

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
                if ($('#numbers_select').selectpicker('val')) {
                    sms.send_sms();
                } else {
                    flashalert.danger("Please ensure the <b>to</b> field is populated");
                }
            }
        });
        $(document).on('click', '.close-sms', function (e) {
            e.preventDefault();
            window.history.back();
        });

        $("button[type=submit]").attr('disabled', false);
    },
    validate_phone: function() {
        var re = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/;

        var validation = true;

        var send_to = $('#numbers_select').selectpicker('val');

        $('.to-msg').hide();

        if (send_to) {
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
        }

        return validation;

    },
    send_sms: function ($btn) {

        $.ajax({
            url: helper.baseUrl + "sms/send_sms",
            type: "POST",
            dataType: "JSON",
            data: $('#container-fluid form').serialize(),
			beforeSend:function(){
			$("button[type=submit]").hide().parent().append('<img id="pending-send" src="'+helper.baseUrl+'assets/img/ajax-loader.gif" />');
			}
        }).done(function (response) {
            if (response.data.test_mode == true) {
                var msg = "TEST MODE ON! " + response.data.num_messages + " message(s) should be sent in " + response.data.message.num_parts + " part(s) each from " + response.data.message.sender;
                flashalert.warning(msg);
                window.history.back();
            }
            else if (response.data.status == "success") {
                var msg = response.data.num_messages + " message(s) sent in " + response.data.message.num_parts + " part(s) each from " + response.data.message.sender;
                flashalert.success(msg);
                window.history.back();
            }
            else {
                $("button[type=submit]").show().parent().find('#pending-send').remove();
                flashalert.danger(response.data.msg);
            }
        });
    }
}