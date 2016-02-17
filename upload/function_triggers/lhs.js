 var simulation = "";

var campaign_functions = {
	init:function(){
		
	},
	appointment_setup: function (start) {
			$modal.find('.startpicker').data("DateTimePicker").destroy();
				$modal.find('.endpicker').data("DateTimePicker").destroy();
		$modal.find('.startpicker').datetimepicker({
   stepping: 30,
   format: 'DD/MM/YYYY HH:mm',
   sideBySide:true,
   enabledHours: [9,10,11, 12, 13, 14, 15, 16, 17, 18]
   });
   $modal.find('.endpicker').datetimepicker({
   stepping: 30,
   format: 'DD/MM/YYYY HH:mm',
   sideBySide:true,
   enabledHours: [9,10,11, 12, 13, 14, 15, 16, 17, 18]
   });
	}
	
}