      <form style="display:none; padding:10px 20px;" class="form-horizontal">
      <input type="hidden" name="role_id">
        <div class="form-group input-group-sm">
          <p>Please set the role name</p>
<input type="text" class="form-control" name="role_name" placeholder="Enter the role name" required/>
        </div>
        
         <div class="form-group input-group-sm">
          <p>Please set the landing page (default is dashboard)</p>
<input type="text" class="form-control" name="landing_page" placeholder="eg. dashboard/management" required/>
        </div>
        
          <div class="form-group input-group-sm">
          <p>Please set the system timeout in mins (default is 30)</p>
<input type="text" class="form-control" name="timeout" placeholder="eg. 30" required/>
        </div>
   <style>
   label { display:inline-block !important; padding-right:15px }
   </style>
   <h4>Data Access</h4>
       <ul class="list-group">
                <li class="list-group-item" style="height:auto">
      	Does the user have access to data in <b>ALL</b> camapigns? <i class="pointer glyphicon glyphicon-question-sign" data-toggle="tooltip" title="This should be reserved for system administrators"></i><br />
        <label for="data_campaigns">Yes <input type="radio" value="1" name="data_campaigns" /></label>
        <label for="data_campaigns">No <input type="radio" value="0" name="data_campaigns" /></label>
          <label for="data_mix_campaigns">Mix campaigns <input type="checkbox" name="data_mix_campaigns" /></label>
        </li>
        
          <li class="list-group-item" style="height:auto">
        Does the data need to be assigned to the user? <i class="pointer glyphicon glyphicon-question-sign" data-toggle="tooltip" title="Select no if they need to be able to see data belonging to other users. Eg. Managers"></i><br />
        <label for="data_user">Yes <input type="radio" value="1" name="data_user" /></label>
         <label for="data_user">No <input type="radio" value="0" name="data_user" /></label>
          <label for="data_null_user">Allow unassigned <input type="checkbox" name="data_null_user" /></label>
         
        </li>

        
         <li class="list-group-item" style="height:auto">
       Does the data need to be assigned to the users team?<br />
        <label for="data_team">Yes <input type="radio" value="1" name="data_team" /></label>
        <label for="data_team">No <input type="radio" value="0" name="data_team" /></label>
         <label for="data_null_team">Allow unassigned <input type="checkbox" name="data_null_team" /></label>
        
        </li>

        <li class="list-group-item" style="height:auto">
       Does the data need to be assigned to the users group?<br />
        <label for="data_group">Yes <input type="radio" value="1" name="data_group" /></label>
        <label for="data_group">No <input type="radio" value="0" name="data_group" /></label>
         <label for="data_null_group">Allow unassigned <input type="checkbox" name="data_null_group" /></label>
        
        </li>
        
        <li class="list-group-item" style="height:auto">
  		Does the data need to be assigned to the users branch?<br />
        <label for="data_branch">Yes <input type="radio" value="1" name="data_branch" /></label>
        <label for="data_branch">No <input type="radio" value="0" name="data_branch" /></label>
         <label for="data_null_branch">Allow unassigned <input type="checkbox" name="data_null_branch" /></label>
         
        </li>
        
        <li class="list-group-item" style="height:auto">
  		Does the data need to be assigned to the users region?<br />
        <label for="data_group">Yes <input type="radio" value="1" name="data_group" /></label>
        <label for="data_group">No <input type="radio" value="0" name="data_group" /></label>
         <label for="data_null_region">Allow unassigned <input type="checkbox" name="data_null_region" />					</label>
        
        </li>
        
          <li class="list-group-item" style="height:auto">
  		Which records does the user have access to? <i class="pointer glyphicon glyphicon-question-sign" data-toggle="tooltip" title="If none are ticked they only have access to live data"></i><br />
         <label for="data_pending">Pending <input type="checkbox" id="data_pending" name="data_pending" />					</label>
          <label for="data_dead">Dead <input type="checkbox" id="data_dead" name="data_dead" />					</label>
           <label for="data_parked">Parked <input type="checkbox" id="data_parked" name="data_parked" />					</label>
            <label for="data_complete">Complete <input type="checkbox" id="data_complete" name="data_complete" />					</label>
           
        </li>
       </ul>
       </ul>
       
   
                <h4>System Permissions <span class="pull-right"><button id="check-all" class="btn btn-default btn-xs"><span class="fa fa-check"></span> Check All</button> <button id="uncheck-all" class="btn btn-default btn-xs"><span class="fa fa-remove"></span> Uncheck All</button></span></h4>
        <ul class="list-group">
       <?php foreach($permissions as $group_name => $group_permissions){ ?>
    <li class="list-group-item" style="height:auto"><?php echo $group_name ?><span class="pull-right" style="width:80%">
    <?php foreach($group_permissions as $id=>$name){ ?>
    <div style="display:inline-block; width:150px"><input class="permbox" id="pm_<?php echo $id ?>" type="checkbox" name="permission[<?php echo $id ?>]"> <span data-toggle="tooltip" class="tt" title="<?php echo $name['description'] ?>"><?php echo $name['name'] ?></span></div ><?php } ?>
</span>
<div class="clearfix"></div>
    </li>
    <?php } ?>
  </ul>
    
       <div class="form-actions pull-right">
         <button class="marl btn btn-default close-btn">Cancel</button>
         <button type="submit" class="marl btn btn-primary save-btn">Save</button>
        </div>
      </form>
<script>
$(document).ready(function(){
	$('[data-toggle="tooltip"]').tooltip();
	
	$('#check-all').on('click',function(e){
		e.preventDefault();
		$('.permbox').prop('checked',true);
		
	});
	$('#uncheck-all').on('click',function(e){
		e.preventDefault();
		$('.permbox').prop('checked',false);
		
	});
});
</script>