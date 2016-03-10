 <?php if(isset($collapsable)){ ?>
<div class="panel panel-primary" id="quick-planner-panel">
  <div class="panel-heading clearfix" data-toggle="collapse"  data-target="#qp-panel-slide" >      
 Quick Planner
    <?php if(isset($attendees)&&!isset($regions)&&count($attendees)>0){ ?>
    <div class='pull-right'>
      <input type="hidden" name="driver_id" value="" />
      <div class="input-group-btn" style="display:inline">
        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><span class="caret"></span> <span id="planner-attendee-text">Attendee</span> </button>
        <ul class="dropdown-menu pull-right" role="menu" id="planner-attendee-filter">
          <?php foreach($attendees as $attendee): ?>
          <li><a <?php if(empty($attendee['distance'])){ echo "class='disabled'"; } ?> href="#" data-val="<?php echo $attendee['user_id'] ?>" data-ref="attendee"><span class='filter-text'><?php echo $attendee['name'] ?> <?php if(empty($attendee['distance'])){ echo "<span class='small text-danger'>No postcode</span>"; } ?></span> <small><?php echo (!empty($attendee['distance'])?$attendee['distance']." miles":"") ?> </small></a> </li>
          <?php endforeach ?>
        </ul>
      </div>
    </div>
    <?php } ?>
    <div class='pull-right branch-name-text'></div>
  </div>
       <div id="qp-panel-slide" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
  <div class="panel-body"  id="quick-planner">Please choose the attendee to the view journey information </div>
</div>
   </div> 
    <?php } else { ?>

<div class="panel panel-primary" id="quick-planner-panel">
  <div class="panel-heading clearfix"> Quick Planner
    <?php if(isset($attendees)&&!isset($regions)&&count($attendees)>0){ ?>
    <div class='pull-right'>
      <input type="hidden" name="driver_id" value="" />
      <div class="input-group-btn" style="display:inline">
        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><span class="caret"></span> <span id="planner-attendee-text">Attendee</span> </button>
        <ul class="dropdown-menu pull-right" role="menu" id="planner-attendee-filter">
          <?php foreach($attendees as $attendee): ?>
          <li><a <?php if(empty($attendee['distance'])){ echo "class='disabled'"; } ?> href="#" data-val="<?php echo $attendee['user_id'] ?>" data-ref="attendee"><span class='filter-text'><?php echo $attendee['name'] ?> <?php if(empty($attendee['distance'])){ echo "<span class='small text-danger'>No postcode</span>"; } ?></span> <small><?php echo (!empty($attendee['distance'])?$attendee['distance']." miles":"") ?> </small></a> </li>
          <?php endforeach ?>
        </ul>
      </div>
    </div>
    <?php } ?>
    <div class='pull-right branch-name-text'></div>
  </div>
  <div class="panel-body"  id="quick-planner">Please choose the attendee to the view journey information </div>
</div>

  <?php }  ?>
  
      <script type="text/javascript">
        $(document).on('click', '#planner-attendee-filter li a', function (e) {
            e.preventDefault();
        });
        $(document).on('click', '#planner-attendee-filter li a:not(\'.disabled\')', function (e) {
            e.preventDefault();
            var type = $(this).attr('data-ref'), value = $(this).attr('data-val'), txt = $(this).find('span.filter-text').text();
            $('#slot-attendee').val(value);
            $('#quick-planner-panel').find('[name="driver_id"]').val(value);
            $('#planner-attendee-text').text(txt);
            $('#slot-attendee-text').text(txt);
            quick_planner.driver_id = value;
            quick_planner.load_planner();
            if ($('#slots-panel').length > 0) {
                record.appointment_slots_panel.load_panel();
            }

        });
    </script>
