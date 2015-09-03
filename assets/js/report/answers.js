	$(document).ready(function(){
		answers.init()
	});
	
var answers = {
    init: function () {
		answers.question_chart()
		$(document).on("click","#answers-filter",function(e){
			e.preventDefault();
			answers.answers_panel($(this).attr('data-id'))
			answers.question_chart($(this).attr('data-id'))
		});

    },
    answers_panel: function(survey) {
        $.ajax({
            url: helper.baseUrl + 'reports/answers_data',
            type: "POST",
            dataType: "JSON",
            data: {
                survey: survey
            },
			beforeSend: function(){
			            $('.answers_panel').html('<img src="'+helper.baseUrl+'assets/img/ajax-loader-bar.gif" /> ');	
			}
        }).done(function (response) {
			 $('.answers_panel').empty();
            var $rows = "";
            if (response.success) {
				$('.answers_panel').append('<h4>'+response.data[0].survey_name+' Answer Statitics</h4>');
                $.each(response.data, function (i, val) {
					$rows += '<tr><td>'+val.survey_name+'</td><td>'+val.question_name+'</td><td><span class="glyphicon glyphicon-info-sign tt"  data-toggle="tooltip" data-placement="left" title="'+val.question_script+'"></span></td><td>'+val.count+'</td><td>'+val.average+'</td><td><a href="'+val.perfects+'">'+val.tens+'</a></td><td><a href="'+val.lows+'">'+val.low_score+'</a></td></tr>';
				});
				$('.answers_panel').append('<table class="table table-striped table-responsive"><thead><th>Survey</th><th>Question</th><th>Script</th><th>Answered</th><th>Average Score</th><th>Perfect 10s</th><th>Below 4</th></thead><tbody>' + $rows + '</tbody></table>');
            } else {
                $('.answers_panel').append('<p>' + response.msg + '</p>');
            }
			$('.tt').tooltip();
        });
    },
	question_chart:function(filter){
				 $.ajax({
                url: helper.baseUrl + 'charts/question_chart',
                type: "POST",
                dataType: "JSON",
				data: {
                    filter: filter, limit:'50'
                },
			beforeSend: function(){
			            $('#answers-chart').html('<img src="'+helper.baseUrl+'assets/img/ajax-loader-bar.gif" /> ');	
			}
            }).done(function (response) {
				$('.panel-title').text("Question Scores");
				if(response.length){
					$('#answers-chart').empty();
					//load the bar chart
					new Morris.Bar({
  element: 'answers-chart',
  data: response,
  xkey: 'question_name',
  ykeys: ['score'],
  labels: ['Average Score'],
  ymin:0,
  ymax:10,
  stacked:false,
  xLabelMargin: 10,
  xLabelAngle:90,
  hoverCallback: function (index, options, content, row) { return content+row['script'] },
  hideHover:true
});
//adjust the height of the xlabel container so that they fit
$('svg').attr('height','460');
				} else {
				$('#answers-chart').html('<p>No surveys have been completed</p>');	
				}
			});
}
}