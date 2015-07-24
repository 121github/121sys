// JavaScript Document
var campaign_function = {
init:function(){
	$('#save-sticky').hide();
	$('#sticky-notes').addClass('red').prop('readonly',true);
},
email_trigger:function(){
                        $.ajax({
                            url: helper.baseUrl + 'email/trigger_email',
                            type: "POST",
                            data: {urn: record.urn}
                        }).done(function(response){
							email_panel.load_panel();
						});

},
sms_trigger:function(){
	   $.ajax({
                            url: helper.baseUrl + 'sms/trigger_sms',
                            type: "POST",
                            data: {urn: record.urn}
                        }).done(function(response){
							sms_panel.load_panel();
						});
}
}
$(document).ready(function(){
	campaign_function.init();
});