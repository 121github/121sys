// JavaScript Document

var ghs = {
	init:function(){
		$(document).on('click','#search-address,#search-reference',function(e){
			e.preventDefault();
			ghs.search_record($(this));
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
		results = "<p class='text-success'>Record was found</p>";
		results += "<a href='"+helper.baseUrl+"records/detail/"+response.urn+"' class='btn btn-success'>View</a>";
		} else {
		results = "<p class='text-danger'>No record was found, if this is a housing association tenant they should call 08002483743. If it is a private tenant you can create a new record.</p>"	
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