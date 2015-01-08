// JavaScript Document
var filter = {
    init: function () {
		filter.count_records();

		$(document).on('blur','input[type="text"]',function(){
			var postcode = $('.filter-form').find('input[name="postcode"]').val();
			//The postcode is evaluated in the current_postcode_input click control
			if (!postcode.length) {
				filter.count_records();
			}
		});

		$( ".current_postcode_input" ).change(function() {
			var postcode = $('.filter-form').find('input[name="postcode"]').val();
			filter.check_postcode(postcode);
		});

		$(document).on('click','input[type="checkbox"]',function(){
			filter.count_records();
		});

		$(document).on('change','select:not(#campaign-select,.actions_parked_code_select,.actions_ownership_select,.actions_campaign_select)',function(e){
			filter.count_records();
		});

		$(document).on('click','.submit-filter',function(e){
			e.preventDefault();
			filter.apply_filter();

		});

		$(document).on('click','.actions-filter',function(e){
			e.preventDefault();
			filter.actions();
		});

		$(document).on('click', '.close-actions', function(e) {
			e.preventDefault();
			filter.close_actions($(this));
		});

		$('.actions-container').hide();

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

		$(document).on('click', '.change-parkedcode', function(e) {
			$('.actions-content').fadeOut(1000, function() {
				$('.edit-parkedcode-form').fadeIn(1000)
			});
		});
		$(document).on('click', '.actions-parkedcode-btn', function(e) {
			e.preventDefault();
			var urn_list = filter.get_urn_list();
			var parked_code_id = $('.actions_parked_code_select option:selected').val();
			filter.save_parked_code(urn_list, parked_code_id);
		});

		$(document).on('click', '.change-ownership', function(e) {
			$('.actions-content').fadeOut(1000, function() {
				$('.edit-ownership-form').fadeIn(1000)
			});
		});
		$(document).on('click', '.actions-ownership-btn', function(e) {
			e.preventDefault();
			var urn_list = filter.get_urn_list();
			var ownership_id = $('.actions_ownership_select option:selected').val();
			filter.save_ownership(urn_list, ownership_id);
		});

		$(document).on('click', '.copy-records', function(e) {
			$('.actions-content').fadeOut(1000, function() {
				$('.copy-records-form').fadeIn(1000)
			});
		});
		$(document).on('click', '.actions-copy-btn', function(e) {
			e.preventDefault();
			var urn_list = filter.get_urn_list();
			var campaign_id = $('.actions_campaign_select option:selected').val();
			filter.copy_records(urn_list, campaign_id);
		});


		$(document).on('click', '.close-edit-actions-btn', function(e) {
			e.preventDefault();
			filter.close_edit_actions();
		});

		$('.actions_parked_code_select').on('change', function(){
			var selected = $('.actions_parked_code_select option:selected').val();
			if (selected) {
				$('.actions-parkedcode-btn').prop('disabled', false);
			}
			else {
				$('.actions-parkedcode-btn').prop('disabled', true);
			}
		});
		$('.actions_ownership_select').on('change', function(){
			var selected = $('.actions_ownership_select option:selected').val();
			if (selected) {
				$('.actions-ownership-btn').prop('disabled', false);
			}
			else {
				$('.actions-ownership-btn').prop('disabled', true);
			}
		});
		$('.actions_campaign_select').on('change', function(){
			var selected = $('.actions_campaign_select option:selected').val();
			if (selected) {
				$('.actions-copy-btn').prop('disabled', false);
			}
			else {
				$('.actions-copy-btn').prop('disabled', true);
			}
		});
	},
	check_postcode:function(postcode){
		var data = {postcode :  postcode};
		$.ajax({
            url: helper.baseUrl + 'search/get_coords',
            type: "POST",
            dataType: "JSON",
            data: data
        }).done(function(response) {
			if(!response.success){
				$('.filter-form').find('input[name="postcode"]').val('')
				flashalert.danger("The postcode does not exist or the connection with Google Maps fails: "+response.error);
			}
			else {
				$('.filter-form').find('input[name="lat"]').val(response.coords.lat);
				$('.filter-form').find('input[name="lng"]').val(response.coords.lng);
				filter.count_records();
			}
		});
	},
	check_distance:function(){
		var lat = $('.filter-form').find('input[name="lat"]').val();
		var lng = $('.filter-form').find('input[name="lng"]').val();
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
				$('.actions-filter').prop('disabled', true);
			} else {
				$('button[type="submit"]').prop('disabled', false);
				$('.record-count').html(response.data).css('color','green');
				$('.actions-filter').prop('disabled', false);
			}
		});

		filter.reload_actions();
				
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
	},
	actions: function() {
		var pagewidth = $(window).width() / 2;
		var moveto = pagewidth - 250;
		$('<div class="modal-backdrop actions in"></div>').appendTo(document.body).hide().fadeIn();
		$('.actions-container').find('.actions-panel').show();
		$('.actions-content').show();
		$('.actions-container').fadeIn()
		$('.actions-container').animate({
			width: '500px',
			left: moveto,
			top: '10%'
		}, 1000);

		$('.records-found').html($('.record-count').html());

		$('.actions-parkedcode-btn').prop('disabled', true);
		$('.actions-ownership-btn').prop('disabled', true);
		$('.actions-copy-btn').prop('disabled', true);

	},
	close_actions: function() {
		$('.modal-backdrop.actions').fadeOut();
		$('.actions-container').fadeOut(500, function() {
			$('.actions-content').show();
			$('.alert').addClass('hidden');
		});
		$('.edit-parkedcode-form').hide();
		$('.edit-ownership-form').hide();
		$('.copy-records-form').hide();
	},
	close_edit_actions: function() {
		$('.edit-parkedcode-form').hide();
		$('.edit-ownership-form').hide();
		$('.copy-records-form').hide();
		$('.actions-content').fadeIn(1000);
	},
	reload_actions: function() {
		$('.actions-parkedcode-btn').prop('disabled', true);
		$('.actions-ownership-btn').prop('disabled', true);
		$('.actions-copy-btn').prop('disabled', true);

		$('.change-parkedcode-result').html("");
		$('.change-ownership-result').html("");
		$('.copy-records-result').html("");

		$(".edit-parkedcode-form").trigger("reset");
		$(".edit-ownership-form").trigger("reset");
		$(".copy-records-form").trigger("reset");
		$('.actions_parked_code_select').selectpicker('render');
		$('.actions_ownership_select').selectpicker('render');
		$('.actions_campaign_select').selectpicker('render');

	},
	get_urn_list: function() {

		var urn_list;
		$.ajax({
			url: helper.baseUrl + 'search/get_urn_list',
			type: "POST",
			dataType: "JSON",
			async: false
		}).done(function(response){
			urn_list = response.data;
		});

		return urn_list;
	},

	save_parked_code : function(urn_list, parked_code_id) {

		$.ajax({
			url: helper.baseUrl + 'search/save_parked_code',
			type: "POST",
			dataType: "JSON",
			data: {'urn_list': urn_list, 'parked_code_id': parked_code_id},
			beforeSend: function(){
				$('.saving').html("<img src='"+helper.baseUrl+"assets/img/ajax-loader-bar.gif' />");
				$('.actions-parkedcode-btn').prop('disabled', true);
			}
		}).done(function(response) {
			if (response.success) {
				flashalert.success(response.msg);
				$('.change-parkedcode-result').html("Success").css('color', 'green');
			}
			else {
				flashalert.danger(response.msg);
				$('.change-parkedcode-result').html("Error").css('color', 'red');
			}
			$('.saving').html("");
			filter.close_edit_actions();
		});
	},
	save_ownership : function(urn_list, ownership_id) {
		$.ajax({
			url: helper.baseUrl + 'search/save_ownership',
			type: "POST",
			dataType: "JSON",
			data: {'urn_list': urn_list, 'ownership_id': ownership_id},
			beforeSend: function(){
				$('.saving').html("<img src='"+helper.baseUrl+"assets/img/ajax-loader-bar.gif' />");
				$('.actions-ownership-btn').prop('disabled', true);
			}
		}).done(function(response) {
			if (response.success) {
				flashalert.success(response.msg);
				$('.change-ownership-result').html("Success").css('color', 'green');
			}
			else {
				flashalert.danger(response.msg);
				$('.change-ownership-result').html("Error").css('color', 'red');
			}
			$('.saving').html("");
			filter.close_edit_actions();
		});
	},
	copy_records : function(urn_list, campaign_id) {
		$.ajax({
			url: helper.baseUrl + 'search/copy_records',
			type: "POST",
			dataType: "JSON",
			data: {'urn_list': urn_list, 'campaign_id': campaign_id},
			beforeSend: function(){
				$('.saving').html("<img src='"+helper.baseUrl+"assets/img/ajax-loader-bar.gif' />");
				$('.actions-copy-btn').prop('disabled', true);
			}
		}).done(function(response) {
			if (response.success) {
				flashalert.success(response.msg);
				$('.copy-records-result').html("Success").css('color', 'green');
			}
			else {
				flashalert.danger(response.msg);
				$('.copy-records-result').html("Error").css('color', 'red');
			}
			$('.saving').html("");
			filter.close_edit_actions();
		});
	}
}