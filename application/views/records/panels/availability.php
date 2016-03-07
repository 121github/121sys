<?php if(isset($collapsable)){ ?> 
    <div class="panel panel-primary" id="slot-availability">
  <div class="panel-heading clearfix" role="button" data-toggle="collapse" data-parent="#detail-accordion" href="#slots-panel-slide" aria-expanded="true" aria-controls="slots-panel-slide">Slot Availability
    <div class="pull-right">
    <form style="padding:0; margin:0">
    <input type="hidden" id="slot-pot-id" value="<?php echo $record['pot_id'] ?>" />
     <input type="hidden" id="slot-source-id" value="<?php echo $record['source_id'] ?>" />
        <input type="hidden" id="slot-campaign-id" value="<?php echo $record['campaign_id'] ?>" />
    <input type="hidden" id="slot-distance" name="distance" value="" />
      <input type="hidden" id="slot-attendee"  name="attendee" value="<?php echo (count($attendees)=="1"?$attendees[0]['user_id']:"") ?>" />
      <input type="hidden" id="slot-closest" value="<?php echo !empty($attendees)?$attendees[0]['user_id']:"" ?>" />
          <input type="hidden" id="app-type" value="" />
    <input class="form-control input-xs" style="min-width:100px;margin-top:2px" type="hidden" name="postcode" id="slot-postcode" value="<?php echo $details['record']['planner_postcode'] ?>" placeholder="Enter postcode" />

  <?php if (!empty($attendees)){ ?>
   <div class="input-group-btn" style="display:inline">
        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><span class="caret"></span> <span id="slot-attendee-text">Attendee</span> </button>
        <ul class="dropdown-menu pull-right" id="availability-attendee-filter" role="menu">
         <?php foreach($attendees as $attendee): ?>
          <li><a href="#" class="filter" data-val="<?php echo $attendee['user_id'] ?>" data-ref="attendee"><span class='filter-text'><?php echo $attendee['name'] ?></span> <small><?php echo (!empty($attendee['distance'])?$attendee['distance']." miles":"") ?> </small></a> </li>
          <?php endforeach ?>
        </ul>
      </div>
<?php } ?>
     </form>
  </div></div>
   <div id="slots-panel-slide" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
  <div class="panel-body" id="slots-panel"><p><?php echo empty($attendees)?"No attendees have been configured":"Please choose an attendee to view their availability" ?></p></div>
</div></div>
    <?php } else { ?>

<div class="panel panel-primary" id="slot-availability">
  <div class="panel-heading clearfix">Slot Availability
    <div class="pull-right">
    <form style="padding:0; margin:0">
    <input type="hidden" id="slot-pot-id" value="<?php echo $record['pot_id'] ?>" />
     <input type="hidden" id="slot-source-id" value="<?php echo $record['source_id'] ?>" />
        <input type="hidden" id="slot-campaign-id" value="<?php echo $record['campaign_id'] ?>" />
    <input type="hidden" id="slot-distance" name="distance" value="" />
      <input type="hidden" id="slot-attendee"  name="attendee" value="<?php echo (count($attendees)=="1"?$attendees[0]['user_id']:"") ?>" />
      <input type="hidden" id="slot-closest" value="<?php echo !empty($attendees)?$attendees[0]['user_id']:"" ?>" />
          <input type="hidden" id="app-type" value="" />
    <input class="form-control input-xs" style="min-width:100px;margin-top:2px" type="hidden" name="postcode" id="slot-postcode" value="<?php echo $details['record']['planner_postcode'] ?>" placeholder="Enter postcode" />

  <?php if (!empty($attendees)){ ?>
      <div class="input-group-btn">
        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><span class="caret"></span> <span id="slot-attendee-text">Attendee</span> </button>
        <ul class="dropdown-menu pull-right" id="availability-attendee-filter" role="menu">
         <?php foreach($attendees as $attendee): ?>
          <li><a href="#" class="filter" data-val="<?php echo $attendee['user_id'] ?>" data-ref="attendee"><span class='filter-text'><?php echo $attendee['name'] ?></span> <small><?php echo (!empty($attendee['distance'])?$attendee['distance']." miles":"") ?> </small></a> </li>
          <?php endforeach ?>
        </ul>
      </div>
<?php } ?>
     </form>
    </div>
  </div>
  <div class="panel-body" id="slots-panel"><p><?php echo empty($attendees)?"No attendees have been configured":"Please choose an attendee to view their availability" ?></p></div>
</div>


    <?php }  ?>

<script type="text/javascript">
$(document).ready(function(){
	if($('#slot-attendee').val()!==""){
		record.appointment_slots_panel.load_panel();	
	}
});


$(document).on('blur','#slot-postcode',function(){
	record.appointment_slots_panel.load_panel();
});

$(document).on('click','#slot-availability li a',function(e){
e.preventDefault();
	var type = $(this).attr('data-ref'),value = $(this).attr('data-val'),txt=$(this).find('span.filter-text').text();		quick_planner.driver_id = value;
	$('#slot-attendee-text').text(txt);
	$('#slot-attendee').val(value);
	record.appointment_slots_panel.load_panel();	
	if(typeof quick_planner.contact_postcode !== "undefined"){
	$('#planner-attendee-text').text(txt);
	$('#quick-planner-panel').find('[name="driver_id"]').val(value);
	if($('#quick-planner-panel').length>0){
	quick_planner.load_planner();
	}
	}

});
</script>