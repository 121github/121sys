
var eldon = {
	init:function(){
		eldon.overdue_visits();
		eldon.tasks_panel();
		   $('.daterange').daterangepicker({
                opens: "left",
                ranges: {
                    'Over 1 Week': [moment().subtract('year', 5), moment().subtract('days', 7)],
                    'Over 1 Month': [moment().subtract('year', 5), moment().subtract('month', 1)],
                    'Over 3 Months': [moment().subtract('year', 5), moment().subtract('month', 3)],
                    'Over 6 Month': [moment().subtract('year', 5),moment().subtract('month', 6)],
                    'Over 1 Year': [moment().subtract('year', 5), moment().subtract('month', 12)],
                    'Never': [moment().subtract('year', 5), moment().subtract('month', 1)]
                },
                format: 'DD/MM/YYYY',
                minDate: "01/01/2010",
                maxDate: moment(),
                startDate: moment(),
                endDate: moment()
            },
            function(start, end, element) {
                var $btn = this.element;
                //$btn.find('.date-text').html(start.format('MMMM D') + ' - ' + end.format('MMMM D'));
                $btn.closest('form').find('input[name="date_from"]').val(start.format('YYYY-MM-DD'));
                $btn.closest('form').find('input[name="date_to"]').val(end.format('YYYY-MM-DD'));
                eldon.overdue_visits()
            });
        $(document).on("click", '.daterange', function(e) {
            e.preventDefault();
        });

        $(document).on("click", '.filter[data-ref="campaign"]', function(e) {
            e.preventDefault();
						$icon = $(this).closest('ul').prev('button').find('span');
			$(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('form').find('input[name="campaign"]').val($(this).attr('id'));
            eldon.overdue_visits()
        });

        $(document).on("click", '.filter[data-ref="agent"]', function(e) {
            e.preventDefault();
						$icon = $(this).closest('ul').prev('button').find('span');
			$(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('form').find('input[name="agent"]').val($(this).attr('id'));
			$(this).closest('form').find('input[name="colname"]').val($(this).text());
			$(this).closest('form').find('input[name="team"]').val('');
            eldon.overdue_visits()
        });
	},
	tasks_panel:function(){
			$.ajax({ url:helper.baseUrl+'dashboard/pending_tasks',
	dataType:"JSON",
	type:"POST"
	}).done(function(response){
			if(response.data.length>0){
				var tbody = "";
            $.each(response.data, function (i, val) {
                    tbody += "<tr class='pointer' data-modal='view-record' data-urn='"+ val.urn +"'><td>" + val.campaign_name + "</td><td>" + val.company + "</td><td>" + val.task_name + "</td><td>" + val.task_status + "</td><td>" + val.date + "</td><td>" + val.name + "</td></tr>";
            });
			var table = '<div class="table-responsive"><table class="table table-bordered table-hover table-striped"><thead><tr><th>Campaign</th><th>Company</th><th>Task</th><th>Task Status</th><th>Date Set</th><th>Set By</th></tr></thead><tbody>'+tbody+'</tbody></table></div>'
			$('#tasks-panel').html(table);
			} else {
			$('#tasks-panel').html('<p>No records need to be visited yet</p>');	
			}
		
	});
		
	},
overdue_visits:function(){
	$.ajax({ url:helper.baseUrl+'dashboard/overdue_visits',
	dataType:"JSON",
	type:"POST",
	data: $('#overdue-filter').serialize()
	}).done(function(response){
			if(response.data.length>0){
				var tbody = "";
            $.each(response.data, function (i, val) {
                    tbody += "<tr class='pointer' data-modal='view-record' data-urn='"+ val.urn +"'><td>" + val.type + "</td><td>" + val.category + "</td><td>" + val.name + "</td><td>" + val.owner + "</td><td>" + val.last_update + "</td><td>" + val.outcome + "</td></tr>";
            });
			var table = '<div class="table-responsive"><table class="table table-bordered table-hover table-striped"><thead><tr><th>Campaign</th><th>Category</th><th>Company</th><th>User</th><th>Date Update</th><th>Outcome</th></tr></thead><tbody>'+tbody+'</tbody></table></div>'
			$('#overdue-panel').html(table);
			} else {
			$('#overdue-panel').html('<p>No records need to be visited yet</p>');	
			}
		
	});
}
}
$(document).ready(eldon.init());
