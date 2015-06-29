// JavaScript Document

var ghs = {
	init:function(){
		$(document).on('click','#search-address,#search-reference,#search-telephone',function(e){
			e.preventDefault();
			var error = "";
			$.each($(this).closest('form').find('input'),function(){
				if($(this).val()==""){
				error = "Please enter the "+$(this).prev('label').text();	
				}
			});
			if(error==""){
			ghs.search_record($(this));
			} else {
			flashalert.danger(error);
			}

		});
	},
search_record:function($btn){
	$.ajax({url:helper.baseUrl+'search/search_address',
	type:"POST",
	dataType:"JSON",
	data: $btn.closest('form').serialize()
	}).done(function(response){
		var results;
		if(response.urn){
		results = "<p><span class='text-success'>The record was found! </span>";
		if(response.parked_code){
			results += "<span class='text-danger'> We are not doing surveys in this area yet</span>"
		}
		results += "</p>";
		results += "<a href='"+helper.baseUrl+"records/detail/"+response.urn+"' class='btn btn-success'>View Record</a>";
		} else {
		results = "<p class='text-danger'>No record was found, please create a new record and capture all the contact details. If it's a housing association tenant please update the new record with the call outcome <b>Query</b>. If it's a private tenant please complete the data capture form.</p>"	
		results += "<a href='"+helper.baseUrl+"data/add_record' class='btn btn-info'>Create New</a>";
		}
		$('#search-results').html(results);
	});
	
},
/* the function for the urgent panel on the client dashboard */
    urgent_panel: function (filter) {
        $.ajax({
            url: helper.baseUrl + 'trackvia/get_rebookings',
            type: "POST",
            dataType: "JSON",
            data: $('.urgent-filter').serialize(),
        }).done(function (response) {
            $('.urgent-panel').empty();
            var $urgents = "";
            if (response.data.length > 0) {
                $.each(response.data, function (i, val) {
                    $urgents += '<li><a class="red tt pointer" href="'+helper.baseUrl+'records/detail/' + val.urn + '">' + val.fullname + '</a> <small>'+val.campaign_name+'</small><br><span class="small">Original Survey: ' + val.cancelled_date + ' '+val.cancelled_slot+'</span></li>';
                });
                $('.urgent-panel').append('<ul>' + $urgents + '</ul>');
				$('.tt').tooltip();
            } else {
                $('.urgent-panel').append('<p>' + response.msg + '</p>');
            }
        });
    }
	
}