<div class="panel panel-primary" id="quick-planner">
  <div class="panel-heading">Quick Planner
    <div class="pull-right">
    <form>
  
      <input type="hidden" id="slot-attendee"  name="attendee" value="" />
          <div class="input-group"  style="width:280px">
   
    <input class="form-control input-xs" type="text" name="postcode" id="slot-postcode" value="<?php echo $details['record']['planner_postcode'] ?>" placeholder="Enter postcode" />
    
      <div class="input-group-btn">
        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><span id="slot-attendee-text">Filter</span> <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
         <?php foreach($attendees as $attendee): ?>
          <li><a href="#" class="filter" data-val="<?php echo $attendee['user_id'] ?>" data-ref="attendee"><?php echo $attendee['name'] ?></a> </li>
          <?php endforeach ?>
          <li class="divider"></li>
          <li><a class="filter" ref="#" style="color: green;" data-ref="attendee">Show all</a> </li>
        </ul>
      </div>
    </div>
     </form>
    </div>
  </div>
  <div class="panel-body" id="slots-panel"> Please choose the planner you want to use </div>
</div>
<script type="text/javascript">
$(document).on('blur','#slot-postcode',function(){
	
});
</script>