// JavaScript Document
var campaign_functions = {
init:function(){
		  $.ajax({
                            url: helper.baseUrl + 'trackvia/check_voicemail',
                            type: "POST",
							dataType:"JSON",
                            data: {urn: $('#urn').val()}
                        }).done(function(response){
								if(response.success){
	alert("If an answer machine picks up you should leave a message!");
	}
						})
	$('#save-sticky').hide();
	$('#sticky-notes').addClass('red').prop('readonly',true);
	$(document).on('click','[data-modal="create-appointment"]',function(){
		alert("*Confirm that loft access will be required during the survey. (head and shoulder inspection usually sufficient)\n*Confirm access also required to main electric meter and fuse board required - it helps if obstacles are removed prior to survey.");
	});
},
appointment_setup:function(){
	
},
contact_form_setup:function(){
	
},
appointment_saved:function(){
	
},
email_trigger:function(){
                        $.ajax({
                            url: helper.baseUrl + 'email/trigger_email',
                            type: "POST",
                            data: {urn: record.urn}
                        }).done(function(response){
							record.email_panel.load_panel();
						});

},
sms_trigger:function(){
	   $.ajax({
                            url: helper.baseUrl + 'sms/trigger_sms',
                            type: "POST",
                            data: {urn: record.urn}
                        }).done(function(response){
							record.sms_panel.load_panel();
						});
}
}
$(document).ready(function(){
	campaign_functions.init();
});