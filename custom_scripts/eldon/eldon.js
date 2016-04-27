// JavaScript Document

var campaign_function = {
init:function(){
$('.outcomepicker').prop('disabled',true).selectpicker('refresh')
},
appointment_setup:function(){
	
},
contact_form_setup:function(){
	
},
appointment_saved:function(){
	
},
set_outcome_delays:function(){
	var color = $('#custom-panel').find('.c1').text();
	if(color=="Gold"){
	$('#outcomes').find("option[value='97']").attr('delay',24*30*3);
} else if(color=="Platinum"){
	$('#outcomes').find("option[value='97']").attr('delay',24*30*1);
}
 else if(color=="Silver"){
	$('#outcomes').find("option[value='97']").attr('delay',24*30*6);
}
 else if(color=="Bronze"){
	$('#outcomes').find("option[value='97']").attr('delay',24*30*12);
}
$('#outcomes').find("option[value='99']").attr('delay',24);
$('.outcomepicker').prop('disabled',false).selectpicker('refresh')
}

}

$(document).ready(function(){
function run(){
campaign_function.set_outcome_delays();
}
setTimeout(function(){ campaign_function.set_outcome_delays(); },3000);
});
