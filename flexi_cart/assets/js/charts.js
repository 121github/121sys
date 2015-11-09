
// JavaScript Document
var charts = {
 
	line_chart:function(values,container){
		new Morris.Line({
		element: container,
        data: values,
        xkey: 'ID',
        ykeys: ['Score'],
        labels: ['Score'],
		xLabels: "ID",
		//xLabelFormat: function (x) {  return timestamp_to_uk(x,false); },
        pointSize: 2,
        hideHover: 'auto',
        resize: true,
		hoverCallback: function (index, options, content, row) { return row['uk_date']+'<br>'+row['Survey Name']+'<br>Score: '+row['Score']+'<br><a href="survey/edit/'+row['survey_id']+'">View Survey</a>';
}
  
});
/* we can add click events to the chart like this but I decided to add a link to the hover item instead
//$(document).on('click','circle',function(event){ console.log($(this).closest('#survey-chart').find('.urn').text()); });
*/
	},
	donut_chart:function(values,container){
			Morris.Donut({
  				element: container,
 				 data: values
});	
		
	},
	
	
	latest_surveys:function(filter){
		 $.ajax({
                url: helper.baseUrl + 'charts/latest_surveys',
                type: "POST",
                dataType: "JSON",
                data: {
                    filter: filter,
                }
            }).done(function (response) {
				var container = 'survey-chart';
				$('#'+container).empty();
				if(response.length){
				charts.line_chart(response,container);
				} else {
				$('#'+container).html('<h4>No surveys found</h4>');	
				}
			});
		
	},
	survey_counts:function(filter){
		 $.ajax({
                url: helper.baseUrl + 'charts/survey_counts',
                type: "POST",
                dataType: "JSON",
                data: {
                    filter: filter,
                }
            }).done(function (response) {
				var container = 'survey-counts';
				$('#'+container).empty();
				if(response.length){
				charts.donut_chart(response,container);
				} else {
				$('#'+container).html('<h4>No surveys found</h4>');	
				}
			});
		
	},
	custom_chart:function(){
				 $.ajax({
                url: helper.baseUrl + 'charts/custom_chart',
                type: "POST",
                dataType: "JSON",
				data: $('#survey-chart').closest('.panel').find('form').serialize()
            }).done(function (response) {
				if(response.length){
					$('.panel-title').text("Overview");
					$('#survey-chart').empty();
					//load the bar chart
					new Morris.Bar({
  element: 'survey-chart',
  data: response,
  xkey: 'survey_name',
  ykeys: ['survey_count', 'target'],
  labels: ['Surveys', 'Target'],
  stacked:false,
  xLabelMargin: 10,
  hoverCallback: function (index, options, content, row) { return content+'Progress: '+row['pc']+'%<br>Avg NPS: '+row['nps']; },
  hideHover:true
});
				} else {
				$('#survey-chart').html('<h4>No surveys found</h4>');	
				}
			});
		
	},
	
	
		question_chart:function(){
				 $.ajax({
                url: helper.baseUrl + 'charts/question_chart',
                type: "POST",
                dataType: "JSON",
				data: $('#survey-chart').closest('.panel').find('form').serialize()+ "&limit=3"
            }).done(function (response) {
				$('.panel-title').text("Lowest scoring questions");
				if(response.length){
					$('#survey-chart').empty();
					//load the bar chart
					new Morris.Bar({
  element: 'survey-chart',
  data: response,
  xkey: 'question_name',
  ykeys: ['score'],
  labels: ['Average Score'],
  ymin:0,
  ymax:10,
  stacked:false,
  xLabelMargin: 10,
  hoverCallback: function (index, options, content, row) { return content+row['script'] },
  hideHover:true
});
				} else {
				$('#survey-chart').html('<h4>No surveys found</h4>');	
				}
			});
		
	}
	
	
	
}
