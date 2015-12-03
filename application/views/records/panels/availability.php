<div class="panel panel-primary" id="slot-availability">
  <div class="panel-heading clearfix">Slot Availability
    <div class="pull-right">
    <form>
    <input type="hidden" id="slot-pot-id" value="<?php echo $record['pot_id'] ?>" />
     <input type="hidden" id="slot-source-id" value="<?php echo $record['source_id'] ?>" />
        <input type="hidden" id="slot-campaign-id" value="<?php echo $record['campaign_id'] ?>" />
    <input type="hidden" id="slot-distance" name="distance" value="" />
      <input type="hidden" id="slot-attendee"  name="attendee" value="" />
      <input type="hidden" id="slot-closest" value="<?php echo $attendees[0]['user_id'] ?>" />
          <input type="hidden" id="app-type" value="" />
            <!--<div class="input-group"  style="width:280px">
          <div class="input-group-btn">
        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><span id="slot-type-text">Type</span> <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
         <?php foreach($types as $type): ?>
          <li><a href="#" class="filter" data-val="<?php echo $type['id'] ?>" data-ref="type"><?php echo $type['name'] ?></a> </li>
          <?php endforeach ?>
          <li class="divider"></li>
          <li><a class="filter" ref="#" style="color: green;" data-ref="appointment_type">Show all</a> </li>
        </ul>
      </div>-->
          
          <!--
             <div class="input-group-btn">
                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span id="slot-distance-text">Distance</span> <span class="caret"></span></button>
        <ul class="dropdown-menu">
          <li><a class="filter" data-val="1" data-ref="distance" href="#">1 Mile</a></li>
          <li><a class="filter" data-val="3" data-ref="distance" href="#">3 Miles</a></li>
          <li><a class="filter" data-val="5" data-ref="distance" href="#">5 Miles</a></li>
           <li><a class="filter" data-val="10" data-ref="distance" href="#">10 Miles</a></li>
           <li><a class="filter" data-val="15" data-ref="distance" href="#">15 Miles</a></li>
           <li><a class="filter" data-val="20" data-ref="distance" href="#">20 Miles</a></li>
           <li><a class="filter" data-val="30" data-ref="distance" href="#">30 Miles</a></li>
           <li><a class="filter" data-val="50" data-ref="distance" href="#">50 Miles</a></li>
           <li><a class="filter" data-val="100" data-ref="distance" href="#">100 Miles</a></li>
          <li role="separator" class="divider"></li>
          <li><a class="filter" data-ref="distance" href="#" style="color: green;">Any Distance</a></li>
        </ul>
      </div>
    >
    <input class="form-control input-xs" style="min-width:100px;margin-top:2px" type="text" name="postcode" id="slot-postcode" value="<?php echo $details['record']['planner_postcode'] ?>" placeholder="Enter postcode" />
  -->
      <div class="input-group-btn">
        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><span class="caret"></span> <span id="slot-attendee-text">Attendee</span> </button>
        <ul class="dropdown-menu pull-right" role="menu">
         <?php foreach($attendees as $attendee): ?>
          <li><a href="#" class="filter" data-val="<?php echo $attendee['user_id'] ?>" data-ref="attendee"><span class='filter-text'><?php echo $attendee['name'] ?></span> <small><?php echo (!empty($attendee['distance'])?$attendee['distance']." miles":"") ?> </small></a> </li>
          <?php endforeach ?>
          <li class="divider"></li>
          <li><a class="filter" ref="#" style="color: green;" data-ref="attendee">Show all</a> </li>
        </ul>
      </div>

     </form>
    </div>
  </div>
  <div class="panel-body" id="slots-panel"><p>Please choose an attendee to view their availability</p></div>
</div>
<script type="text/javascript">
$(document).on('blur','#slot-postcode',function(){
	record.appointment_slots_panel.load_panel();
});

$(document).on('click','#slot-availability li a',function(e){
e.preventDefault();
	var type = $(this).attr('data-ref'),value = $(this).attr('data-val'),txt=$(this).find('span.filter-text').text();
	$(this).closest('form').find('input[name="'+type+'"]').val(value);
	
	$(this).closest('ul').find('a').css("color","black");
    $(this).css("color","green");
	if(type=="distance"){
	$(this).closest('form').find('#slot-'+type+'-text').text(txt);	
	} else if(type=="attendee"){
		$(this).closest('form').find('#slot-'+type+'-text').text(txt);	
	} else if(type=="type"){
		$(this).closest('form').find('#slot-'+type+'-text').text(txt);	
	}
	record.appointment_slots_panel.load_panel();
	
});
</script>