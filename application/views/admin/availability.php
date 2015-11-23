  <div class="row">
        <div class="col-lg-12">
          <h1 class="page-header">Availability</h1>
        </div>
        <!-- /.col-lg-12 --> 
      </div>
        <div class="row">
        <div class="col-lg-12">
       <div class="panel panel-primary" id="campaign-slots-panel">
            <div class="panel-heading">Assign slots to a campaign</div>
             <div class="panel-body">
             	<form>
                <div class="form-group">
                <label>Select the campaign to configure slots on</label><br>
                <select id="slot-campaign-select" class="selectpicker" data-width="230px">
                <option value="">Select campaign</option>
            <?php foreach ($campaign_access as $client => $camp_array) { ?>
                <optgroup label="<?php echo $client ?>">
                    <?php foreach ($camp_array as $camp) { ?>
                        <option value="<?php echo $camp['id'] ?>"><?php echo $camp['name'] ?></option>
                    <?php } ?>
                </optgroup>
            <?php } ?>
        </select>
                </select>
                </div>
                
                  <div class="form-group">
                <label>Select the user</label><br>
                <select id="slot-user-select" disabled class="selectpicker"><option value="">Select user</option>
                </select>
                </div>
                
                 <div class="form-group">
                <label>Select the slots available to this user on this campaign</label><br>
                <select name="slots" disabled multiple class="selectpicker"><option value="">Select slots</option>
                </select>
                </div>            
      </form>
      </div>
      </div>
      </div>
      </div>          
                 <div class="row">
        <div class="col-lg-12">
       <div class="panel panel-primary" id="campaign-slots-panel">
            <div class="panel-heading">Setup a users availability by day</div>
             <div class="panel-body">
                <form>
                  <div class="form-group">
                <label>Select the campaign to configure slots on</label><br>
                <select name="slot_campaign" class="selectpicker"><option value="">Select campaign</option>
                </select>
                </div>
                
                <div class="form-group">
                <label>Select the user</label><br>
                <select name="slot_user" class="selectpicker"><option value="">Select user</option>
                </select>
                </div>
                
               
                </form>
            </div>
      </div>
      
      </div>
      </div>
      
      
           <div class="row">
        <div class="col-lg-12">
       <div class="panel panel-primary" id="campaign-slots-panel">
            <div class="panel-heading">Setup a users availability by date <div class="pull-right"><button class="btn btn-default btn-xs">View all</button></div></div>
             <div class="panel-body">
                <form>
                  <div class="form-group">
                <label>Select the campaign to configure slots on</label><br>
                <select name="slot_campaign" class="selectpicker"><option value="">Select campaign</option>
                </select>
                </div>
                
                <div class="form-group">
                <label>Select the user</label><br>
                <select name="slot_user" class="selectpicker"><option value="">Select user</option>
                </select>
                </div>
                 <div class="form-group">
                <label>Select the date</label><br>
                <input class="form-control datepicker" name="date" type="text"/>
                </select>
                </div>
                
                </form>
            </div>
      </div>
      
      </div>
      </div>
<script>
$(document).ready(function(){
	alert("In progress");
	admin.init();
	admin.slots.init();
});
</script>