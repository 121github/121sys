<style>
.disabled { color:#666; cursor:not-allowed }
</style>
<div class="panel panel-primary" id="quick-planner-panel">
  <div class="panel-heading clearfix">
    Quick Planner
    <?php if(isset($attendees)&&!isset($regions)&&count($attendees)>0){ ?>
     <div class='pull-right'>
     <input type="hidden" name="driver_id" value="" />
      <div class="input-group-btn">
        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"><span class="caret"></span> <span id="planner-attendee-text">Attendee</span> </button>
        <ul class="dropdown-menu pull-right" role="menu" id="planner-attendee-filter">
         <?php foreach($attendees as $attendee): ?>
          <li><a <?php if(empty($attendee['distance'])){ echo "class='disabled'"; } ?> href="#" data-val="<?php echo $attendee['user_id'] ?>" data-ref="attendee"><span class='filter-text'><?php echo $attendee['name'] ?></span> <small><?php echo (!empty($attendee['distance'])?$attendee['distance']." miles":"") ?> </small></a> </li>
          <?php endforeach ?>
        </ul>
      </div>
      </div>
      <script>
	 $(document).on('click','#planner-attendee-filter li a',function(e){
		  e.preventDefault();
	 });
	  $(document).on('click','#planner-attendee-filter li a:not(\'.disabled\')',function(e){
		
		 quick_planner.driver_id = $(this).attr('data-val');
		 quick_planner.load_planner();
	  });
	  </script>
      <?php } ?>
    <div class='pull-right branch-name-text'></div>
    <!--<div class="pull-right">
    <form>
    <div class="input-group">
    <input type="radio" name="slot" value="1" checked > Slot 1
    <input type="radio" name="slot" value="2"> Slot 2
       </div>
     </form>
    </div>-->
  </div>
  <div class="panel-body"  id="quick-planner"> Please choose the planner you want to use </div>
</div>
<script type="text/javascript">
$(document).on('blur','#slot-postcode',function(){
	
});
</script>


