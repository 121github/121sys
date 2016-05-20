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
        <label for="data[all_campaigns]">Yes <input type="radio" value="1" name="data[all_campaigns]" /></label>
        <label for="data[all_campaigns]">No <input type="radio" value="0" name="data[all_campaigns]" /></label>
          <label for="data[mix_campaigns]">Mix campaigns <input type="checkbox" name="data[mix_campaigns]" /></label>
        </li>
        
          <li class="list-group-item" style="height:auto">
        Does the data need to be assigned to the user? <i class="pointer glyphicon glyphicon-question-sign" data-toggle="tooltip" title="Select no if they need to be able to see data belonging to other users. Eg. Managers"></i><br />
        <label for="data[user_records]">Yes <input type="radio" value="1" name="data[user_records]" /></label>
         <label for="data[user_records]">No <input type="radio" value="0" name="data[user_records]" /></label>
          <label for="data[unassigned_user]">Allow unassigned <input type="checkbox" name="data[unassigned_user]" /></label>
         
        </li>

        
         <li class="list-group-item" style="height:auto">
       Does the data need to be assigned to the users team?<br />
        <label for="data[team_records]">Yes <input type="radio" value="1" name="data[team_records]" /></label>
        <label for="data[team_records]">No <input type="radio" value="0" name="data[team_records]" /></label>
         <label for="data[unassigned_team]">Allow unassigned <input type="checkbox" name="data[unassigned_team]" /></label>
        
        </li>

        <li class="list-group-item" style="height:auto">
       Does the data need to be assigned to the users group?<br />
        <label for="data[group_records]">Yes <input type="radio" value="1" name="data[group_records]" /></label>
        <label for="data[group_records]">No <input type="radio" value="0" name="data[group_records]" /></label>
         <label for="data[unassigned_group]">Allow unassigned <input type="checkbox" name="data[unassigned_group]" /></label>
        
        </li>
        
        <li class="list-group-item" style="height:auto">
  		Does the data need to be assigned to the users branch?<br />
        <label for="data[branch_records]">Yes <input type="radio" value="1" name="data[branch_records]" /></label>
        <label for="data[branch_records]">No <input type="radio" value="0" name="data[branch_records]" /></label>
         <label for="data[unassigned_branch]">Allow unassigned <input type="checkbox" name="data[unassigned_branch]" /></label>
         
        </li>
        
        <li class="list-group-item" style="height:auto">
  		Does the data need to be assigned to the users region?<br />
        <label for="data[region_records]">Yes <input type="radio" value="1" name="data[region_records]" /></label>
        <label for="data[region_records]">No <input type="radio" value="0" name="data[region_records]" /></label>
         <label for="data[unassigned_region]">Allow unassigned <input type="checkbox" name="data[unassigned_region]" />					</label>
        
        </li>
        
          <li class="list-group-item" style="height:auto">
  		Which records does the user have access to? <i class="pointer glyphicon glyphicon-question-sign" data-toggle="tooltip" title="If none are ticked they only have access to live data"></i><br />
         <label for="data[pending]">Pending <input type="checkbox" id="data[pending]" name="data[pending]" />					</label>
          <label for="data[dead]">Dead <input type="checkbox" id="data[dead]" name="data[dead]" />					</label>
           <label for="data[parked]">Parked <input type="checkbox" id="data[parked]" name="data[parked]" />					</label>
            <label for="data[complete]">Complete <input type="checkbox" id="data[complete]" name="data[complete]" />					</label>
           
        </li>
       </ul>
       </ul>
          
        <h4>Override Permissions</h4>
        <ul class="list-group">
    <li class="list-group-item" style="height:auto">
    <?php foreach($permissions['Override'] as $id=>$name){ ?>
    <div style="display:inline-block; width:150px"><input class="permbox" id="pm_<?php echo $id ?>" type="checkbox" name="permission[<?php echo $id ?>]"> <span data-toggle="tooltip" class="tt" title="<?php echo $name['description'] ?>"><?php echo $name['name'] ?></span></div ><?php } ?>
<div class="clearfix"></div>
    </li>
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