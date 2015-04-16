    $(document).ready(function() {
		            "use strict";
			
            var options = {
                events_source: function(start,end){ 
				var events = [];
                $.ajax({
                    url: helper.baseUrl+'calendar/get_events',
                    dataType: 'JSON',
                    type:     'POST',
					async: false,
					data: {
					startDate: start.getTime(),
            		endDate: end.getTime(),	
					campaigns: $('#campaign-cal-select').selectpicker('val'),
					users: $('#user-select').selectpicker('val'),
					postcode: $('#dist-form').find('.current_postcode_input').val(),
					distance: $('#dist-form').find('.distance-select').val()
					}
					}).done(function (json) {
                         if(!json.success) {
                            $.error(json.error);
                        }
                        if(json.result) {
                        events =  json.result;
                        }
                     });
				return events;
},
				//modal: "#events-modal",
                view: 'month',
                tmpl_path: helper.baseUrl + 'assets/tmpls/',
                tmpl_cache: false,
                day: 'now',
                onAfterEventsLoad: function(events) {
                    if (!events) {
                        return;
                    }
                    var list = $('#eventlist');
                    list.html('');
                    $.each(events, function(key, val) {
                        $(document.createElement('li'))
                            .html('<a href="' + val.url + '">' + val.title + '</a>')
                            .appendTo(list);
                    });
                },
                onAfterViewLoad: function(view) {
                    $('.page-header h3').text(this.getTitle());
                    $('.btn-group button').removeClass('active');
                    $('button[data-calendar-view="' + view + '"]').addClass('active');
                },
                classes: {
                    months: {
                        general: 'label'
                    }
                }
            };

            var calendar = $('#calendar').calendar(options);

            $('.btn-group button[data-calendar-nav]').each(function() {
                var $this = $(this);
                $this.click(function() {
					$this.button('loading');
                    calendar.navigate($this.data('calendar-nav'));
					$this.button('reset');
                });
            });

            $('.btn-group button[data-calendar-view]').each(function() {
                var $this = $(this);
                $this.click(function() {
					$this.button('loading');
                    calendar.view($this.data('calendar-view'));
					$this.button('reset')
                });
            });

            $('#first_day').change(function() {
                var value = $(this).val();
                value = value.length ? parseInt(value) : null;
                calendar.setOptions({
                    first_day: value
                });
                calendar.view();
            });

            $('#language').change(function() {
                calendar.setLanguage($(this).val());
                calendar.view();
            });

            $('#events-in-modal').change(function() {
                var val = $(this).is(':checked') ? $(this).val() : null;
                calendar.setOptions({
                    modal: val
                });
            });
            $('#events-modal .modal-header, #events-modal .modal-footer').click(function(e) {
                //e.preventDefault();
                //e.stopPropagation();
            });

				$(document).on('change','#user-select',function(e){
					e.preventDefault();
		calendar.view();
	});


		$(document).on('change','#campaign-cal-select',function(){
		$.ajax({url:helper.baseUrl+'calendar/get_calendar_users',
		type:"POST",
		dataType:"JSON",
		data: { campaigns: $(this).val() }
		}).done(function(response){
			$('#user-select').empty();
			var $options = "";
			$.each(response.data,function(k,v){
				$options += "<option value='"+v.id+"'>"+v.name+"</options>";
			});
			$('#user-select').html($options).selectpicker('refresh');
			calendar.view();
		})
		});
		
		$(document).on('click','#distance-cal-button',function(e){
			e.preventDefault();
			modal.distance();	
		});

	
	var modal = {
distance:function(){
	   $('.modal-title').text('Set maximum distance');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').empty().html($('#dist-form').html());
		$('#modal').find('.distance-select').selectpicker();
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
			$(this).button('loading');
			var postcode = $(this).closest('#modal').find('.current_postcode_input').val();
			var distance = $(this).closest('#modal').find('.distance-select').selectpicker('val');
            $('#modal').modal('toggle');
			//have to use attr because val isnt working, maybe because think its because its hidden?
			$('#dist-form').find('.current_postcode_input').val(postcode).attr('value',postcode);
			$('#dist-form').find('.distance-select option').each(function(){
			$(this).removeAttr('selected');	
			});
			$('#dist-form').find('.distance-select').val(distance).children('option[value="'+distance+'"]').attr('selected',true);
			calendar.view();
			$(this).button('reset');
        });
	}
	}
    });
