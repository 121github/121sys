    <div id="appointment-panel" class="panel panel-primary">
      <div class="panel-heading clearfix">Appointments  <?php if(in_array("add appointments",$_SESSION['permissions'])){ ?><span class="glyphicon glyphicon-plus pointer pull-right marl" data-modal="create-appointment" data-urn="<?php echo $record['urn'] ?>"></span><?php } ?> <span class="glyphicon glyphicon-calendar pull-right pointer view-calendar pointer"></span></div>
      <div class="panel-body appointment-panel"> 
        <div class="panel-content"> 
          <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
        </div>
      </div>
    </div>
    
    <!--
        <div class="panel panel-primary webforms-panel">
      <div class="panel-heading clearfix">Calendar</div>
      <div class="panel-body">
        <div class="panel-content"> 
         <div class="responsive-calendar">
  <div class="controls">
      <a class="pull-left" data-go="prev"><div class="btn"><i class="icon-chevron-left"></i></div></a>
      <h4><span data-head-year></span> <span data-head-month></span></h4>
      <a class="pull-right" data-go="next"><div class="btn"><i class="icon-chevron-right"></i></div></a>
  </div><hr/>
  <div class="day-headers">
    <div class="day header">Mon</div>
    <div class="day header">Tue</div>
    <div class="day header">Wed</div>
    <div class="day header">Thu</div>
    <div class="day header">Fri</div>
    <div class="day header">Sat</div>
    <div class="day header">Sun</div>
  </div>
  <div class="days" data-group="days">

  </div>
</div>
        </div>
      </div>
    </div>

    <script>
$( document ).ready( function() {
	function addLeadingZero(num) {
    if (num < 10) {
      return "0" + num;
    } else {
      return "" + num;
    }
  }
	
  $('.responsive-calendar').responsiveCalendar({
  events: {
    "2014-12-30": {
      "number": 2, 
      "badgeClass": 
      "active", 
      "dayEvents": [
        {
          "name": "Important meeting",
          "hour": "17:30" 
        },
        {
          "name": "Morning meeting at coffee house",
          "hour": "08:15" 
        }
      ]
    }
  },
  onActiveDayHover: function(events) {
      var thisDayEvent, key;

      key = $(this).data('year')+'-'+addLeadingZero( $(this).data('month') )+'-'+addLeadingZero( $(this).data('day') );
      thisDayEvent = events[key];
      console.log(key);
    }
  });
  
});
</script>

-->    