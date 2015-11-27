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

	if($('#slot-pot-id').val()=="51"||$('#slot-pot-id').val()=="52"){
		$('#slot-attendee').val('139');
	} else if ($('#slot-pot-id').val()=="34"||$('#slot-pot-id').val()=="35"||$('#slot-pot-id').val()=="37"||$('#slot-pot-id').val()=="38"){
		$('#slot-attendee').val('122');
	} else if ($('#slot-pot-id').val()=="49"||$('#slot-pot-id').val()=="48"||$('#slot-pot-id').val()=="47"||$('#slot-pot-id').val()=="46"){
		$('#slot-attendee').val('137');
	}  else if ($('#slot-pot-id').val()=="57"||$('#slot-pot-id').val()=="58"||$('#slot-pot-id').val()=="59"||$('#slot-pot-id').val()=="60"){ 
	$('#slot-attendee').val('143');
	}  else if ($('#slot-pot-id').val()=="61"||$('#slot-pot-id').val()=="62"){ 
	$('#slot-attendee').val('142');
	}  else {
		if($('#slot-pot-id').val()=="41"&&$('#slot-source-id').val()=="41"){ 
			//peterborough
		$('#slot-attendee').val('121');
		} else if($('#slot-pot-id').val()=="41"&&$('#slot-source-id').val()=="68"){
			//cumbria
		$('#slot-attendee').val('178');	
		}
	}


},
appointment_setup:function(){
	console.log("Setting up appointment from ghs.js");
	campaign_functions.set_appointment_start($('[name="slot-choice"]:checked').attr('data-date')+' '+$('[name="slot-choice"]:checked').attr('data-time'));
	if($('#slot-pot-id').val()=="51"||$('#slot-pot-id').val()=="52"){
		//if southway install
		$('.attendeepicker').selectpicker('val',[139]);
		$('.typepicker').selectpicker('val',[4])
	} else if($('#slot-pot-id').val()=="61"||$('#slot-pot-id').val()=="62"){
		//darlington install
		$('.attendeepicker').selectpicker('val',[142]);
		$('.typepicker').selectpicker('val',[4])
	} else if($('#slot-pot-id').val()=="57"||$('#slot-pot-id').val()=="58"||$('#slot-pot-id').val()=="59"||$('#slot-pot-id').val()=="60"){	//darlington surveys
		$('.attendeepicker').selectpicker('val',[143]);
		$('.typepicker').selectpicker('val',[3])
	} else if($('#slot-pot-id').val()=="49"||$('#slot-pot-id').val()=="46"||$('#slot-pot-id').val()=="47"||$('#slot-pot-id').val()=="48"){	//citywest surveys
		$('.attendeepicker').selectpicker('val',[137]);
		$('.typepicker').selectpicker('val',[3])
	} else if($('#slot-pot-id').val()=="41"||$('#slot-pot-id').val()=="36"||$('#slot-pot-id').val()=="38"||$('#slot-pot-id').val()=="39"||$('#slot-pot-id').val()=="40"){	//private surveys
	if($('#slot-source-id').val()=="41"){
		$('.attendeepicker').selectpicker('val',[121]);
	} else {
		$('.attendeepicker').selectpicker('val',[178]);
	}
		$('.typepicker').selectpicker('val',[3])
	} else {
		alert("*Confirm that loft access will be required during the survey. (head and shoulder inspection usually sufficient)\n*Confirm access also required to main electric meter and fuse board required - it helps if obstacles are removed prior to survey.");
		$('.attendeepicker').selectpicker('val',[122]);
		$('.typepicker').selectpicker('val',[3]);
	}
},
set_appointment_attendee: function (attendee) {
        $('.attendeepicker').selectpicker('val', [attendee]);
    },
    set_appointment_start: function (start) {
        var m = moment(start, "YYYY-MM-DD HH:mm");
        $('.startpicker').data("DateTimePicker").date(m);
        $('.endpicker').data("DateTimePicker").date(m.add('hours', 1).format('DD\MM\YYYY HH:mm'));
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