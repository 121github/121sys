// JavaScript Document
var survey = {
    init: function (urn) {
		 	this.urn = urn;
			$('.selectpicker').selectpicker({title:"Please select"});
			$('.tt').tooltip();
           
			            $(document).on('click', '.close-survey', function (e) {
                e.preventDefault();

            });
            $(document).on('click', '.save-survey', function (e) {
                e.preventDefault();
                survey.save_survey($(this));
            });
            $(document).on('click', '.complete-survey', function (e) {
                e.preventDefault();
                survey.save_survey($(this));
            });
			$(document).on('click', '.close-survey', function (e) {
                e.preventDefault();
				window.history.back()
            });
	},
		set_sliders:function(){
			$('.slider').each(function(i,e){
				var val = $(this).attr('data-slider-value');
				if(val=="0"){
				var newval="na"
				} else {
				var newval = val
				}
			   var background = "";
                        if (val < 7) {
                            var background = '#FF8282';
                        }
                        if (val == 7 || val == 8) {
                            var background = '#FF9900';
                        }
                        if (val > 8) {
                            var background = '#428041';
                        }
				 $(this).slider('setValue', newval);
				 $(this).closest('td').find('.slider-selection').css('background', background);
			});
			$('.slider').on('slide', function (ev) {
                if (ev.value < 7) {
                    $(this).find('.slider-selection').css('background', '#FF8282');
                }
                if (ev.value === 7 || ev.value == 8) {
                    $(this).find('.slider-selection').css('background', '#FF9900');
                }
                if (ev.value > 8) {
                    $(this).find('.slider-selection').css('background', '#428041');
                }
				if(ev.value=="0"){
				var newval="na"
				} else {
				var newval = ev.value
				}
				$(this).closest('td').next('td').find('.slider-value').val(newval);
            });
		},
        save_survey: function ($btn) {
            var complete = false;
            if ($btn.hasClass('complete-survey')) {
                complete = "complete=1";
            }
            $.ajax({
                url: helper.baseUrl + "survey/save_survey",
                type: "POST",
                dataType: "JSON",
                data: $btn.closest('form').serialize() + '&' + complete
            }).done(function (response) {
                if (response.success) {
                    flashalert.success("Survey saved successfully");
					if(response.id){
					 $btn.closest('form').append('<input name="survey_id" value="'+response.id+'" type="hidden"/>');
					}
					if(complete){
					window.location.href=helper.baseUrl + "records/detail/"+survey.urn;	
					} else {
					flashalert.success("Survey saved successfully");	
					}
                }
            });

    }
}