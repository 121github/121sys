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
    <!-- the place where days will be generated -->
  </div>
</div>
        </div>
      </div>
    </div>
    
    <script>
$( document ).ready( function() {
  $('.responsive-calendar').responsiveCalendar();
});
</script>