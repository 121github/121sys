// JavaScript Document
var filter = {
    init: function () {
		 	filter.count_records();

			$(document).on('blur','input[type="text"]',function(){
				var postcode = $('form').find('input[name="postcode"]').val();
				//The postcode is evaluated in the current_postcode_input click control
				if (!postcode.length) {
					filter.count_records();
				}
			});
			
			$( ".current_postcode_input" ).change(function() {
				var postcode = $('form').find('input[name="postcode"]').val();
				filter.check_postcode(postcode);
			});

			$(document).on('click','input[type="checkbox"]',function(){
				filter.count_records();
			});
			
			$(document).on('change','select:not(#campaign-select)',function(e){
				filter.count_records();
			});
			
			$(document).on('click','.submit-filter',function(e){
				e.preventDefault();
				filter.apply_filter();

			});
			
			$(document).on('click','.clear-input',function(e){
				$(this).closest('.input-group').find('input').val('');
				filter.count_records();
			});
			
			$(document).on('click','.clear-filter',function(e){
				e.preventDefault()
				$(document).off('change', 'select');
				$('input[type="text"], input[type="select"]:not(.record-status,#campaign-select)').val('');
				$('.selectpicker:not(.record-status,#campaign-select)').selectpicker('deselectAll');
				$('.record-status').selectpicker('val',1);
				$('input[type="checkbox"]').prop('checked',false);
				filter.count_records(true);
				$(document).on('change', 'select',function(){
					filter.count_records();
				})
			});
	},
	check_postcode:function(postcode){
		$.ajax({
            url: helper.baseUrl + 'search/get_coords',
            type: "POST",
            dataType: "JSON",
            data: postcode
        }).done(function(response) {
			if(!response.success){
				$('form').find('input[name="postcode"]').val('')
				flashalert.danger("The postcode does not exist or the connection with Google Maps fails: "+response.error);
			}
			else {
				$('form').find('input[name="lat"]').val(response.coords.lat);
				$('form').find('input[name="lng"]').val(response.coords.lng);
				filter.count_records();
			}
		});
	},
	check_distance:function(){
		var lat = $('form').find('input[name="lat"]').val();
		var lng = $('form').find('input[name="lng"]').val();
		if (lat.length && lng.length) {
			filter.count_records();
		}
	},
	count_records:function(){

		var formData = 	$('#filter-form').serialize();

		$.ajax({
                url: helper.baseUrl + 'search/count_records',
                type: "POST",
                dataType: "JSON",
                data: formData,
				beforeSend: function(){
					$('.record-count').html("<img src='"+helper.baseUrl+"assets/img/ajax-load-black.gif' />");
				}
            }).done(function(response) {
				if(response.data<1){
				$('button[type="submit"]').prop('disabled', true);
				$('.record-count').text(response.data).css('color','red');
				} else {
				$('button[type="submit"]').prop('disabled', false);	
				$('.record-count').html(response.data).css('color','green');
				}
			});
				
	},
		apply_filter:function(){
		$.ajax({
                url: helper.baseUrl + 'search/apply_filter',
                type: "POST",
                dataType: "JSON",
                data: $('#filter-form').serialize()
            }).done(function(response) {
				window.location.href= helper.baseUrl + 'records/view';
				localStorage.removeItem("DataTables_DataTables_Table_0_/thinkmoney-nps/records/view");
			});
				
	}
	
}