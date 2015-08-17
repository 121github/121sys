<div class="panel panel-primary" id="slot-availability">
  <div class="panel-heading">Slot Availability
    <div class="pull-right">
    <form>
    <input type="hidden" id="slot-distance" name="distance" value="" />
      <input type="hidden" id="slot-attendee"  name="attendee" value="" />
          <div class="input-group">
             <div class="input-group-btn" style="width:230px">
                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Distance <span class="caret"></span></button>
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
    <input class="form-control input-xs" type="text" name="postcode" id="slot-postcode" value="<?php echo $details['record']['planner_postcode'] ?>" placeholder="Enter postcode" />
    
      <div class="input-group-btn">
        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">Users <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
         <?php foreach($attendees as $attendee): ?>
          <li><a href="#" class="filter" data-val="<?php echo $attendee['user_id'] ?>" data-ref="attendee"><?php echo $attendee['name'] ?></a> </li>
          <?php endforeach ?>
          <li class="divider"></li>
          <li><a class="filter" ref="#" style="color: green;" data-ref="attendee">All Users</a> </li>
        </ul>
      </div>
    </div>
     </form>
    </div>
  </div>
  <div class="panel-body" id="slots-panel"> <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /> </div>
</div>
<script type="text/javascript">
$(document).on('blur','#slot-postcode',function(){
	record.appointment_slots_panel.load_panel();
});

$(document).on('click','#slot-availability li a',function(e){
e.preventDefault();
	var type = $(this).attr('data-ref'),value = $(this).attr('data-val');
	$(this).closest('form').find('input[name="'+type+'"]').val(value);
	$(this).closest('ul').find('a').css("color","black");
    $(this).css("color","green");
	record.appointment_slots_panel.load_panel();
	
});
</script>