<?php if(!isset($details['record']['urn'])): ?>
There was a problem while finding the selected record details. Maybe it does not exist or has been deleted.
<?php else: ?>
<div class="page-header">
  <h2>View Details <small>URN: <?php echo $details['record']['urn'] ?> <?php echo (!empty($details['record']['campaign'])?" [". $details['record']['campaign']."]":"") ?></small><span class="pull-right">
    <?php if(!empty($nav['prev'])): ?>
    <a type="button" class="btn btn-default btn-lg" href="<?php echo $nav['prev'] ?>">Previous</a>
    <?php endif ?>
    <?php if(!empty($nav['next'])): ?>
    <a type="button" class="btn btn-default btn-lg" href="<?php echo $nav['next'] ?>">Next</a>
    <?php endif ?>
    </span></h2>
</div>
<div class="row">
<?php //print_r($details); ?>
  <div class="col-md-4 col-sm-12">
<div class="row">
 <div class="col-md-12 col-sm-6">
<div class="panel panel-primary contact-panel">
  <!-- Default panel contents -->
  <div class="panel-heading"><h4 class="panel-title">
Contact Details<span class="glyphicon glyphicon-plus pull-right add-contact-btn"></span>
      </h4></div>
   <div class="form-container">
  <?php $this->view('forms/edit_contact_form.php',array("urn"=>$details['record']["urn"])) ?>
  </div>
  <!-- List group -->
  <ul class="list-group contacts-list">
   <?php $x=0; foreach($details['contacts']  as $id=>$contact): $x++; ?>
   
         <li  class="list-group-item" item-id="<?php echo $id ?>"><a data-toggle="collapse" data-parent="#accordion" href="#collapse-<?php echo $id ?>">
          <?php echo ($contact['name']["use_full"]?$contact['name']['fullname']:$contact['name']['title'] ." ".$contact['name']['firstname']." ".$contact['name']['lastname']); ?>
        </a>
        <span class="glyphicon glyphicon-trash pull-right del-contact-btn" data-target="#modal" item-id="<?php echo $id ?>" ></span>
        <span class="glyphicon glyphicon-pencil pull-right edit-contact-btn"  item-id="<?php echo $id ?>"></span> 
        
           <div id="collapse-<?php echo $id ?>" class="panel-collapse collapse <?php if($x>1){ echo "in"; } ?>">
      <dl class="dl-horizontal contact-detail-list">
      <?php foreach($contact['visible'] as $key=>$val){ if(!empty($val)&&$key!="Address"){ ?>
          <dt><?php echo $key ?></dt>
          <dd><?php echo $val ?></dd>
          <?php }
		  if($key=="Address"){
			 ?><dt><?php echo $key ?></dt><dd><a class="pull-right pointer" target="_blank" href="https://maps.google.com/maps?q=<?php echo $val['postcode'] ?>,+UK"><span class="glyphicon glyphicon-map-marker"></span> Map</a><?php foreach($val as $address_part){ echo (!empty($address_part)?$address_part."<br>":"");  }?>
             
             </dd>
			  <?php
		  }
	  } ?>

          
           <?php foreach($contact['telephone']  as $id=>$number): ?>
            <dt><?php echo $number['tel_name'] ?></dt>
          <dd><a href="callto:<?php echo $number['tel_num'] ?>"><?php echo $number['tel_num'] ?></a></dd>
            <?php endforeach; ?>
        </dl>
      </div>
      </li>
                 <?php endforeach; ?>
  </ul>

</div>
  </div>




      <div class="col-md-12 col-sm-6"> 
        <!--widget panel-->
        <?php if(in_array(6,$features)){ ?>
    <div class="panel panel-primary">
      <div class="panel-heading">Script Notes</div>
      <div class="panel-body">
        <?php 
		  if(!empty($details['scripts'][0]['name'])){
		  foreach($details['scripts'] as $id=>$data): 
		  if($data['expandable']): ?>
        <p><a href="#" class="view-script" script-id="<?php echo $id ?>"><?php echo $data['name'] ?></a></p>
        <?php else: ?>
        <p><?php echo $data['name'] ?></p>
        <?php 
		  endif;
		  endforeach;
		  } else {
			?>
        <p>There are no scripts configured for this campaign</p>
        <?php } ?>
      </div>
    </div>
    <?php } ?>
        <!--end widget panel--> 
      </div>
</div>
</div>

  <div class="col-md-4 col-sm-12">
    <!--record panel-->
    <div class="panel panel-primary record-panel">
      <div class="panel-heading">Record Details 
      <?php if($details['record']['favorite']){ ?>
      <span class="pull-right favorite-btn" action="remove"><span class="glyphicon glyphicon-star yellow"></span> Remove from favourites</span>
      <?php } else { ?>
     <span class="pull-right favorite-btn" action="add"><span class="glyphicon glyphicon-star-empty"></span> Add to favourites</span> 
      <?php } ?>
      </div>
      <div class="panel-body">
        <p class="last-updated">Last Updated: <?php echo (!empty($details['record']['last_update'])?$details['record']['last_update']:"Never") ?></p>
        <form>
        <input type="hidden" name="urn" id="urn" value="<?php echo $details['record']['urn'] ?>"/>
        <div class="form-group">
          <label>Next action date</label>
          <div class='input-group'>
            <input name="nextcall" id="nextcall" placeholder="Set the next action date here" type='text' class="form-control datetime" value="<?php echo (!empty($details['record']['nextcall'])?$details['record']['nextcall']:"") ?>"/>
            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span> </span> </div>
        </div>
    
        <div class="form-group input-group">
            <?php if($_SESSION['role']==3){ 
			$deadtext = " Only an administrator can unlcok this record."; 
			?>
          <select <?php if($details['record']['record_status']=="3"){ echo "disabled"; } ?> name="outcome_id" id="outcomes" class="selectpicker outcomepicker">
            <option value="">--Select a call outcome--</option>
            <?php foreach($outcomes as $outcome): ?>
            <option <?php if($details['record']['record_status']=="3"&&$outcome['outcome_id']==$details['record']['outcome_id']){ echo "selected"; } ?> value="<?php echo $outcome['outcome_id'] ?>" <?php echo ($outcome['delay_hours']?"delay='".$outcome['delay_hours']."'":"") ?>><?php echo $outcome['outcome'] ?></option>
            <?php endforeach; ?>
          </select>
          <?php } else { 
		   $deadtext = " Click the reset button below to bring it back for calling. Remember to change the ownership back if you need to";
		  ?>
            <select name="progress_id" id="progress" class="selectpicker outcomepicker">
            <option value="">No action required</option>
            <?php foreach($progress_options as $row): ?>
            <option value="<?php echo $row['id'] ?>" <?php if($details['record']['progress_id']==$row['id']){ echo "selected"; } ?> ><?php echo $row['name'] ?></option>
            <?php endforeach; ?>
          </select>
          
          <?php } ?>
        </div>
        <div class="form-group">
          <textarea name="comments" class="form-control <?php if($details['record']['record_status']=="3"){ echo "red"; } ?>" rows="3" placeholder="Enter the call notes here"><?php if($details['record']['record_status']=="3"){ echo "This record has been removed with an outcome of ".$details['record']['outcome']. $deadtext; } ?></textarea>
        </div>
        
                <div class="form-group">
                   <?php if($_SESSION['role']<3){ ?>
                <?php if($details['record']['urgent']){ ?>
                <span class="urgent-btn" action="remove"><span class="red glyphicon glyphicon-flag"></span> Unflag as urgent</span>
                <?php } else { ?>
                <span class="urgent-btn" action="add"><span class="glyphicon glyphicon-flag"></span> Flag as urgent</span>
                	<?php } ?>
                <?php } else  { ?>
                <select class="selectpicker" name="status_control">
                <option value="">--Additional Options--</option>
                <option data-icon="glyphicon glyphicon-flag" value="1">Requires Attention</option>
                <option data-icon="red glyphicon-flag" value="2">Requires Urgent Attention</option>
                </select>
                <?php } ?>
            <?php if($details['record']['record_status']=="3"){ ?>
            <button type="button" class="btn btn-default pull-right reset-record" <?php if($_SESSION['role']==3){ echo "disabled"; } ?>>Reset Record</button>
            <?php } else { ?>    
          <button type="button" class="btn btn-default pull-right update-record" <?php if($_SESSION['role']==3){ echo "disabled"; } ?>>Update Record</button><?php } ?>
        </div>
        </form>
      </div>
    </div>
    <!--end record panel--> 
  </div>
  <div class="col-md-4 col-sm-12"> 
    <!--ownership panel-->
    <div class="row">
      <div class="col-md-12 col-sm-6">
        <div class="panel panel-primary ownership-panel">
          <div class="panel-heading">Ownership <span class="glyphicon glyphicon-pencil pull-right edit-owner"></span></div>
          <div class="panel-body">
            <?php $this->view('forms/edit_ownership_form.php',$users); ?>
            <div class="panel-content">
            <!-- This panel now loads via ajax -->
            </div>
          </div>
        </div>
      </div>
      
      <!--end ownership panel-->
      
      <div class="col-md-12 col-sm-6"> 
        <!--widget panel-->
        <div class="panel panel-primary">
          <div class="panel-heading">Sticky Note</div>
          <div class="panel-body">
            <p>
              <textarea rows="3" class="form-control sticky-notes" placeholder="You can enter important notes here so they get seen. Eg. Do not call the customer before 3pm as they work night shifts!"><?php echo $details['record']['sticky_note'] ?></textarea>
            </p>
            <span class="alert-success hidden">Notes saved</span> <button class="btn btn-default pull-right save-notes">Save Notes</button>
          </div>
        </div>
        <!--end widget panel--> 
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-6 col-sm-12">
    <div class="panel panel-primary">
      <div class="panel-heading">History</div>
      <div class="panel-body history-panel">
        <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
      </div>
    </div>
  </div>
  <div class="col-md-6 col-sm-12">
    <div class="panel panel-primary">
      <div class="panel-heading">Surveys <span class="glyphicon glyphicon-plus pull-right new-survey"></span></div>
      <div class="panel-body surveys-panel">
        <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
      </div>
    </div>
  </div>
</div>
<div class="row">
 	<div class="col-md-12 col-sm-12">
      <div class="panel panel-primary">
    	 <div class="panel-heading">Recordings</div>
     		 <div class="panel-body recordings-panel">
       			 <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
      		</div>
   		 </div>
    </div>
</div>
</div>
  <div class="panel panel-primary survey-container">
<?php 
$this->view('forms/new_survey_form.php',$survey_options); ?>
</div>
<script type="text/javascript">
    $(document).ready(function () {   
        var urn = '<?php echo $details['record']['urn'] ?>';
		var role_id = '<?php echo $_SESSION['role'] ?>';
        record.init(urn,role_id);
		//initializing the generic panels
		record.contact_panel.init(urn);
		record.update_panel.init();
		//initializing the panels for this campaign
				<?php if(in_array(10,$features)){ ?>
        record.company_panel.init();
		<?php } ?>
		<?php if(in_array(3,$features)){ ?>
        record.history_panel.init();
		<?php } ?>
		<?php if(in_array(1,$features)){ ?>
        record.surveys_panel.init(urn);
		<?php } ?>
		<?php if(in_array(4,$features)){ ?>
        record.sticky_note.init();
		<?php } ?>
		<?php if(in_array(5,$features)){ ?>
        record.ownership_panel.init();
		<?php } ?>
		<?php if(in_array(6,$features)){ ?>
		record.script_panel();
		<?php } ?>
		<?php if(in_array(7,$features)){ ?>
		record.recordings_panel.init();
		<?php } ?>
		<?php if(in_array(8,$features)){ ?>
		record.additional_info.init();
		<?php } ?>
		<?php if(in_array(10,$features)){ ?>
		record.appointments_panel.init();
		<?php } ?>
    });
</script>
<?php endif; ?>