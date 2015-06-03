 <form id="filter-form" class="filter-form">
<div class="page-header">
  <h2>Search Records<small class="pull-right"><button type="submit" class="btn btn-default submit-filter">View Records</button> <?php if(in_array("search actions",$_SESSION['permissions'])){ ?><button class="btn btn-default actions-filter">Actions</button><?php } ?> <button class="btn btn-default clear-filter">Clear Filter</button> Found: <span class="record-count"></span></small></h2>
</div>
<div class="row">
 <div class="col-sm-3 col-xs-12">
  <div class="panel panel-info" id="filter-panel" >
    <div class="panel-heading">Your search options</div>
     <div class="panel-body" style="overflow-x:auto">
     No filters have been applied. You can use search options on this page to find records matching a specific critera
     </div>
 </div>
 </div>
  <div class="col-sm-9 col-xs-12">
    <div class="panel-group search-panels" id="accordion">
      <?php if(in_array("search campaigns",$_SESSION['permissions'])){ 
	  if(count($campaigns)>1||count($campaign_types)>1||count($clients)>1||count($sources)>1){ ?>
        <div class="panel panel-primary visible">
          <div class="panel-heading pointer" data-toggle="collapse" data-parent="#accordion" href="#collapseZero">
           <h4 class="panel-title"><div class="pull-right glyphicon glyphicon-minus"></div>Campaign Filter Options</h4>
          </div>
          <div id="collapseZero" class="panel-collapse collapse in">
            <div class="panel-body" style="display:none">
            <div class="form-group">
         
                <label style="display:block">Campaign</label>
                <select id="campaign_id" name="campaign_id[]" class="selectpicker campaigns_select" data-width="100%" data-size="5" <?php if(in_array("mix campaigns",$_SESSION['permissions'])){ echo "multiple"; } ?> title="All campaigns">
<?php foreach($campaigns as $row): ?>
                  <?php if(in_array($row['id'],$_SESSION['campaign_access']['array'])):  ?>
                  <option <?php if(@in_array($row['id'],$_SESSION['filter']['values']['campaign_id'])||count($campaigns)=="1"){ echo "selected"; } else { echo (isset($_SESSION['current_campaign'])&&$_SESSION['current_campaign']==$row['id']?"selected":""); } ?> value="<?php echo $row['id'] ?>" ><?php echo $row['name'] ?></option>
                  <?php endif ?>
				  <?php endforeach; ?>
                </select>
           
            <?php if(count($clients)>1){ ?>
                <label>Client</label>
                <br>
                <select id="client_id" name="client_id[]" class="selectpicker" data-width="100%" data-size="5" multiple>
                  <?php foreach($clients as $row): ?>
                  <option <?php if(@in_array($row['id'],$_SESSION['filter']['values']['client_id'])){ echo "selected"; } ?> value="<?php echo $row['id'] ?>" ><?php echo $row['name'] ?></option>
                  <?php endforeach; ?>
                </select>
                <?php } ?>
                <?php if(count($campaign_types)>1){ ?>
                <label>Campaign Type</label>
                <br>
                <select id="campaign_type_id"  name="campaign_type_id[]" class="selectpicker" data-width="100%" data-size="5" multiple>
                  <?php foreach($campaign_types as $row): ?>
                  <option <?php if(@in_array($row['id'],$_SESSION['filter']['values']['campaign_type_id'])){ echo "selected"; } ?> value="<?php echo $row['id'] ?>" ><?php echo $row['name'] ?></option>
                  <?php endforeach; ?>
                </select>
                    <?php } ?>
                  <?php if(count($sources)>1){ ?>
                <label>Data Source</label>
                <br>
                <select id="source_id" name="source_id[]" class="selectpicker" data-width="100%" data-size="5" multiple>
                  <?php foreach($sources as $row): ?>
                  <option <?php if(@in_array($row['id'],$_SESSION['filter']['values']['source_id'])){ echo "selected"; } ?> value="<?php echo $row['id'] ?>" ><?php echo $row['name'] ?></option>
                  <?php endforeach; ?>
                </select>
                 <?php } ?>
              </div>
             
              <button type="submit" class="btn btn-default pull-right submit-filter">View Records</button>
              Found: <span class="record-count"></span> </div>
          </div>
        </div>
         <?php } ?>
        <?php } ?>
        
        <!-------------------->
        <!--  RECORD FILTER -->
        <!-------------------->
        <div class="panel panel-primary">
          <div class="panel-heading pointer" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
            <h4 class="panel-title"><div class="pull-right glyphicon glyphicon-plus"></div>Record Filter Options</h4>
          </div>
          <div id="collapseOne" class="panel-collapse collapse ">
            <div class="panel-body">
              <div class="form-group">
                <label>URN</label>
                <input id="urn" <?php if(isset($_SESSION['filter']['values']['urn'])){ echo "value='".$_SESSION['filter']['values']['urn']."'"; } ?> type="text" name="urn" class="form-control" placeholder="Enter the unique reference number">
              </div>
              <div class="form-group">
                <label>Client Reference</label>
                <input id="client_ref" <?php if(isset($_SESSION['filter']['values']['client_ref'])){ echo "value='".$_SESSION['filter']['values']['client_ref']."'"; } ?> type="text" name="client_ref" class="form-control" placeholder="Enter the client reference ID">
              </div>
              <div class="form-group">
                <label>Call Outcome</label>
                <br>
                <select id="outcome_id" name="outcome_id[]" class="selectpicker" data-width="100%" data-size="5" multiple title="Any">
                  <?php foreach($outcomes as $row): ?>
                  <option <?php if(@in_array($row['id'],$_SESSION['filter']['values']['outcome_id'])){ echo "selected"; } ?> value="<?php echo $row['id'] ?>" ><?php echo $row['name'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label>Progress Status</label>
                <br>
                <select id="progress_id" name="progress_id[]" class="selectpicker" data-width="100%" data-size="5" multiple title="Any">
                  <?php foreach($progress as $row): ?>
                  <option <?php if(@in_array($row['id'],$_SESSION['filter']['values']['progress_id'])){ echo "selected"; } ?> value="<?php echo $row['id'] ?>" ><?php echo $row['name'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label>Status</label>
                <select id="record_status" name="record_status[]" class="selectpicker record-status" data-width="100%" data-size="5"  multiple title="Any">
                  <?php foreach($status as $row): ?>
                  <option <?php if(@in_array($row['id'],$_SESSION['filter']['values']['record_status'])
				  ||count($status)=="1"){ echo "selected"; } ?> value="<?php echo $row['id'] ?>" ><?php echo $row['name'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <?php if(in_array("search parked",$_SESSION['permissions'])){ ?>
               <div class="form-group">
                <label>Parked Status</label>
                <select id="parked_code" name="parked_code[]" class="selectpicker parked-code" data-width="100%" title="Unparked" data-size="5" multiple>
                  <?php foreach($parked_codes as $row): ?>
                  <option <?php if(@in_array($row['id'],$_SESSION['filter']['values']['parked_code'])){ echo "selected"; } ?> value="<?php echo $row['id'] ?>" ><?php echo $row['name'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <?php } ?>
               <?php if(in_array("search groups",$_SESSION['permissions'])){ ?>
              <div class="form-group">
                <label>Group Ownership</label>
                <select id="group_id" name="group_id[]" class="selectpicker" data-width="100%" data-size="5" multiple title="Any">
                  <?php foreach($groups as $row): ?>
                  <option  <?php if(@in_array($row['id'],$_SESSION['filter']['values']['group_id'])||empty($_SESSION['filter']['values'])&&$row['id']==$_SESSION['group']&&in_array("view own group",$_SESSION['permissions'])){ echo "selected"; } ?> value="<?php echo $row['id'] ?>" ><?php echo $row['name'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
                <?php } ?>
                 <?php if(in_array("search any owner",$_SESSION['permissions'])){ ?>
              <div class="form-group">
                <label>User Ownership</label>
                <select id="user_id" name="user_id[]" class="selectpicker" data-width="100%" data-size="5" <?php if(count($users)>1){ echo "multiple"; } ?>  title="Any">
                  <?php foreach($users as $row): ?>
                  <option <?php if(@in_array($row['id'],$_SESSION['filter']['values']['user_id'])||empty($_SESSION['filter']['values'])&&$row['id']==$_SESSION['user_id']&&in_array("own records",$_SESSION['permissions'])){ echo "selected"; } ?> value="<?php echo $row['id'] ?>" ><?php echo $row['name'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <?php } ?>
              <div class="form-group">
                <label>Next Call Date</label>
                <div class="row">
                  <div class="col-md-6 col-sm-6">
                    <div class="input-group">
                      <input id="nextcall-0" <?php if(@isset($_SESSION['filter']['values']['nextcall'][0])){ echo "value='".$_SESSION['filter']['values']['nextcall'][0]."'"; } ?> name="nextcall[0]" type="text" class="form-control date" placeholder="Date from">
                      <span class="input-group-btn">
                      <button class="btn btn-default clear-input" type="button">Clear</button>
                      </span></div>
                  </div>
                  <div class="col-md-6 col-sm-6">
                    <div class="input-group">
                      <input id="nextcall-1" <?php if(@isset($_SESSION['filter']['values']['nextcall'][1])){ echo "value='".$_SESSION['filter']['values']['nextcall'][1]."'"; } ?> name="nextcall[1]" type="text" class="form-control date" placeholder="Date to">
                      <span class="input-group-btn">
                      <button class="btn btn-default clear-input" type="button">Clear</button>
                      </span> </div>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label>Last Call Date</label>
                <div class="row">
                  <div class="col-md-6 col-sm-6">
                    <div class="input-group">
                      <input id="lastcall-0" <?php if(@isset($_SESSION['filter']['values']['date_updated'][0])){ echo "value='".$_SESSION['filter']['values']['date_updated'][0]."'";  } ?> name="date_updated[0]" type="text" class="form-control date" placeholder="Date from">
                      <span class="input-group-btn">
                      <button class="btn btn-default clear-input" type="button">Clear</button>
                      </span></div>
                  </div>
                  <div class="col-md-6 col-sm-6">
                    <div class="input-group">
                      <input id="lastcall-1" <?php if(@isset($_SESSION['filter']['values']['date_updated'][1])){ echo "value='".$_SESSION['filter']['values']['date_updated'][1]."'"; } ?> name="date_updated[1]" type="text" class="form-control date" placeholder="Date to">
                      <span class="input-group-btn">
                      <button class="btn btn-default clear-input" type="button">Clear</button>
                      </span></div>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label>Creation Date</label>
                <div class="row">
                  <div class="col-md-6 col-sm-6">
                    <div class="input-group">
                      <input id="creation-0" <?php if(@isset($_SESSION['filter']['values']['date_added'][0])){ echo "value='".$_SESSION['filter']['values']['date_added'][0]."'"; } ?> name="date_added[0]" type="text" class="form-control date" placeholder="Date from">
                      <span class="input-group-btn">
                      <button class="btn btn-default clear-input" type="button">Clear</button>
                      </span></div>
                  </div>
                  <div class="col-md-6 col-sm-6">
                    <div class="input-group">
                      <input id="creation-1" <?php if(@isset($_SESSION['filter']['values']['date_added'][1])){ echo "value='".$_SESSION['filter']['values']['date_added'][1]."'"; } ?> name="date_added[1]" type="text" class="form-control date" placeholder="Date to">
                      <span class="input-group-btn">
                      <button class="btn btn-default clear-input" type="button">Clear</button>
                      </span> </div>
                  </div>
                </div>
              </div>
              <button type="submit" class="btn btn-default pull-right submit-filter" >View Records</button>
              Found: <span class="record-count"></span> </div>
          </div>
        </div>
        
        <!--------------------->
        <!--  CONTACT FILTER -->
        <!--------------------->
        <div class="panel panel-primary">
          <div class="panel-heading pointer" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
            <h4 class="panel-title"><div class="pull-right glyphicon glyphicon-plus"></div>Contact Filter Options</h4>
          </div>
          <div id="collapseTwo" class="panel-collapse collapse">
            <div class="panel-body">
              <div class="form-group">
                <label>Contact ID</label>
                <input id="contact_id" <?php if(@isset($_SESSION['filter']['values']['contact_id'])){ echo "value='".$_SESSION['filter']['values']['contact_id']."'"; } ?> name="contact_id" type="intextput" class="form-control" placeholder="Enter the contact ID">
              </div>
              <div class="form-group">
                <label>Name</label>
                <input id="fullname" <?php if(@isset($_SESSION['filter']['values']['fullname'])){ echo "value='".$_SESSION['filter']['values']['fullname']."'"; } ?> name="fullname" type="text" class="form-control" placeholder="Enter all or part of the contact's name">
              </div>
             <div class="form-group">  
               <label>Contact Phone</label>
               <div class="input-group">
      <input id="phone" type="text" name="phone" class="form-control"  <?php if(@isset($_SESSION['filter']['values']['phone'])){ echo "value='".$_SESSION['filter']['values']['phone']."'"; } ?> placeholder="Enter all or part of a phone number">
      <span class="input-group-btn">
        <button class="btn btn-default no-number" data-type="contact" type="button">No Numbers</button>
      </span>
    </div>
          </div>  
              <div class="form-group">
                <label>Job/Position</label>
                <input id="position" name="position" <?php if(@isset($_SESSION['filter']['values']['position'])){ echo "value='".$_SESSION['filter']['values']['position']."'"; } ?> type="text" class="form-control" placeholder="Enter all or part of the job title">
              </div>
              <div class="form-group">
                <label>Date of birth</label>
                <div class="row">
                  <div class="col-md-6 col-sm-6">
                    <div class="input-group">
                      <input id="dob-0" <?php if(@isset($_SESSION['filter']['values']['dob'][0])){  echo "value='".$_SESSION['filter']['values']['dob'][0]."'"; } ?> name="dob[0]" type="text" class="form-control dob" placeholder="Date from">
                      <span class="input-group-btn">
                      <button class="btn btn-default clear-input" type="button">Clear</button>
                      </span> </div>
                  </div>
                  <div class="col-md-6 col-sm-6">
                    <div class="input-group">
                      <input id="dob-1" <?php if(@isset($_SESSION['filter']['values']['dob'][1])){  echo "value='".$_SESSION['filter']['values']['dob'][1]."'";; } ?> name="dob[1]" type="text" class="form-control dob" placeholder="Date to">
                      <span class="input-group-btn">
                      <button class="btn btn-default clear-input" type="button">Clear</button>
                      </span> </div>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label>Email Address</label>
                <input id="contact_email" <?php if(@isset($_SESSION['filter']['values']['email'])){ echo "value='".$_SESSION['filter']['values']['email']."'"; } ?> name="contact_email" type="text" class="form-control" placeholder="Enter the contact email address">
              </div>
              <div class="form-group">
                <label>Address</label>
                <input id="address" <?php if(@isset($_SESSION['filter']['values']['address'])){ echo "value='".$_SESSION['filter']['values']['address']."'"; } ?> name="address" type="text" class="form-control" placeholder="Enter all or part of the address">
              </div>
              <button type="submit" class="btn btn-default pull-right submit-filter">View Records</button>
              Found: <span class="record-count"></span></div>
          </div>
        </div>
        
        <!--------------------->
        <!--  COMPANY FILTER -->
        <!--------------------->
        <div class="panel panel-primary">
          <div class="panel-heading pointer" data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
            <h4 class="panel-title"><div class="pull-right glyphicon glyphicon-plus"></div>Company Filter Options</h4>
          </div>
          <div id="collapseThree" class="panel-collapse collapse">
            <div class="panel-body">
              <div class="form-group">
                <label>Company ID</label>
                <input id="company_id" <?php if(@isset($_SESSION['filter']['values']['company_id'])){ echo "value='".$_SESSION['filter']['values']['company_id']."'"; } ?> name="company_id" type="text" class="form-control" placeholder="Enter the company ID">
              </div>
              <div class="form-group">
                <label>Company Name</label>
                <input id="coname" <?php if(@isset($_SESSION['filter']['values']['coname'])){ echo "value='".$_SESSION['filter']['values']['coname']."'"; } ?> name="coname" type="text" class="form-control" placeholder="Enter all or part of the company name">
              </div>
             <div class="form-group">  
               <label>Company Phone</label>
               <div class="input-group">
      <input id="company_phone"  type="text" name="company_phone" class="form-control"  <?php if(@isset($_SESSION['filter']['values']['company_phone'])){ echo "value='".$_SESSION['filter']['values']['company_phone']."'"; } ?> placeholder="Enter all or part of a phone number">
      <span class="input-group-btn">
        <button class="btn btn-default no-number" data-type="company"  type="button">No Numbers</button>
      </span>
    </div>
          </div>    
           
              <div class="form-group">
                <label>Sector</label>
                <select id="sector_id" name="sector_id[]" class="selectpicker sector-select" data-width="100%" data-size="5" multiple  title="Any">
                  <?php foreach($sectors as $row): ?>
                  <option <?php if(@in_array($row['id'],$_SESSION['filter']['values']['sector_id'])){ echo "selected"; } ?> value="<?php echo $row['id'] ?>" ><?php echo $row['name'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label>Subsector</label>
                <select id="subsector_id"  name="subsector_id[]" class="selectpicker subsector-select" data-width="100%" data-size="5" multiple  title="Any">
                  <?php foreach($subsectors as $row): ?>
                  <option <?php if(@in_array($row['id'],$_SESSION['filter']['values']['subsector_id'])){ echo "selected"; } ?> value="<?php echo $row['id'] ?>" ><?php echo $row['name'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label>Turnover</label>
                <div class="row">
                  <div class="col-md-6 col-sm-6">
                    <input id="turnover-0" <?php if(@isset($_SESSION['filter']['values']['turnover'][0])){ echo $_SESSION['filter']['values']['turnover'][0]; } ?> name="turnover[0]" type="text" class="form-control" placeholder="From">
                  </div>
                  <div class="col-md-6 col-sm-6">
                    <input id="turnover-1" <?php if(@isset($_SESSION['filter']['values']['turnover'][1])){ echo $_SESSION['filter']['values']['turnover'][1]; } ?> name="turnover[1]" type="text" class="form-control" placeholder="To">
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label>Employees</label>
                <div class="row">
                  <div class="col-md-6 col-sm-6">
                    <input id="employees-0" <?php if(@isset($_SESSION['filter']['values']['employees'][0])){ echo $_SESSION['filter']['values']['employees'][0]; } ?> name="employees[0]" type="text" class="form-control" placeholder="From">
                  </div>
                  <div class="col-md-6 col-sm-6">
                    <input id="employees-1" <?php if(@isset($_SESSION['filter']['values']['employees'][1])){ echo $_SESSION['filter']['values']['employees'][1]; } ?> name="employees[1]" type="text" class="form-control" placeholder="To">
                  </div>
                </div>
              </div>
              <button type="submit" class="btn btn-default pull-right  submit-filter">View Records</button>
              Found: <span class="record-count"></span> </div>
          </div>
        </div>
        
        <!------------------------------->
        <!--  POSTCODE DISTANCE FILTER -->
        <!------------------------------->
        <div class="panel panel-primary">
          <div class="panel-heading pointer" data-toggle="collapse" data-parent="#accordion" href="#collapseFour">
            <h4 class="panel-title"><div class="pull-right glyphicon glyphicon-plus"></div>Postcode Distance Filter Options</h4>
          </div>
          <div id="collapseFour" class="panel-collapse collapse">
            <div class="panel-body">
              <div class="form-group">
                <label>
                	Postcode
                	<span>
                		<a id="use-my-location" href="#" class="locate-postcode" type="button" data-icon="location" data-iconpos="right">Find my location</a>
            			<div class="error geolocation-error"></div>
            		</span>
            	</label>
                <input id="postcode" <?php if(@isset($_SESSION['filter']['values']['postcode'])){ echo "value='".$_SESSION['filter']['values']['postcode']."'"; } ?> name="postcode" type="text" class="form-control current_postcode_input" placeholder="Enter the Postcode">
                <input <?php if(@isset($_SESSION['filter']['values']['lat'])){ echo "value='".$_SESSION['filter']['values']['lat']."'"; } ?> name="lat" type="hidden">
                <input <?php if(@isset($_SESSION['filter']['values']['lng'])){ echo "value='".$_SESSION['filter']['values']['lng']."'"; } ?> name="lng" type="hidden">
              </div>
              <div class="form-group">
                <label>Distance <span class="distance"> 0</span></label>
                <input <?php if(@isset($_SESSION['filter']['values']['distance'])){ echo "value='".$_SESSION['filter']['values']['distance']."'"; } ?> name="distance" type="text" id="slide_id" class="form-control slider" data-slider-min="0" data-slider-max="300" data-slider-step="5" data-slider-value="0" data-slider-orientation="horizontal" data-slider-selection="before" data-slider-formater="zerona">
              </div>
              <button type="submit" class="btn btn-default pull-right submit-filter">View Records</button>
              Found: <span class="record-count"></span> </div>
          </div>
        </div>
        
        <!---------------------->
        <!--  ADVANCED FILTER -->
        <!---------------------->
        <div class="panel panel-primary">
          <div class="panel-heading pointer" data-toggle="collapse" data-parent="#accordion" href="#collapseFive">
            <h4 class="panel-title"><div class="pull-right glyphicon glyphicon-plus"></div>Advanced Filter Options</h4>
          </div>
          <div id="collapseFive" class="panel-collapse collapse">
            <div class="panel-body">
                          <div class="checkbox">
                <label>New records only (Not yet called)</label>
                <input id="new_only" <?php if(@isset($_SESSION['filter']['values']['new_only'])){ echo "checked"; } ?> name="new_only" type="checkbox">
              </div>
                            <div class="form-group">
                <label>Number of dials</label>
                <select id="dials" name="dials" class="selectpicker" data-width="100%" data-dropupAuto="false" data-size="5"  title="Any">
                                  <option <?php if(@$_SESSION['filter']['values']['dials']==""){ echo "selected"; } ?> value="" >Any</option>
                  <option <?php if(@$_SESSION['filter']['values']['dials']=="zero"){ echo "selected"; } ?> value="zero" >None</option>
                  <option <?php if(@$_SESSION['filter']['values']['dials']=="1"){ echo "selected"; } ?> value="1" >1</option>
                  <option <?php if(@$_SESSION['filter']['values']['dials']=="2"){ echo "selected"; } ?> value="2" >2</option>
                  <option <?php if(@$_SESSION['filter']['values']['dials']=="3"){ echo "selected"; } ?> value="3" >3</option>
                  <option <?php if(@$_SESSION['filter']['values']['dials']=="4"){ echo "selected"; } ?> value="4" >4</option>
                  <option <?php if(@$_SESSION['filter']['values']['dials']=="5"){ echo "selected"; } ?> value="5" >5</option>
                  <option <?php if(@$_SESSION['filter']['values']['dials']=="5:more"){ echo "selected"; } ?> value="5:more">Over 5</option>
                  <option <?php if(@$_SESSION['filter']['values']['dials']=="5:less"){ echo "selected"; } ?> value="5:less">Less than 5</option>
                  <option <?php if(@$_SESSION['filter']['values']['dials']=="4:less"){ echo "selected"; } ?> value="4:less" >Less than 4</option>
                  <option <?php if(@$_SESSION['filter']['values']['dials']=="3:less"){ echo "selected"; } ?> value="3:less" >Less than 3</option>
                  </select>
              </div>
              <div class="checkbox">
                <label>Records with survey only</label>
                <input id="survey" <?php if(@isset($_SESSION['filter']['values']['survey'])){ echo "checked"; } ?> name="survey" type="checkbox">
              </div>
              <div class="checkbox">
                <label>Favorites only</label>
                <input id="favorites" <?php if(@isset($_SESSION['filter']['values']['favorites'])){ echo "checked"; } ?> name="favorites" type="checkbox">
              </div>
              <div class="checkbox">
                <label>Flagged as urgent only</label>
                <input id="urgent" <?php if(@isset($_SESSION['filter']['values']['urgent'])){ echo "checked"; } ?> name="urgent" type="checkbox">
              </div>
              <div class="form-group">
                <label>Order results by</label>
                <select id="order" name="order" class="selectpicker" data-width="100%" data-dropupAuto="false" data-size="5">
                  <option value="" >None</option>
                  <option <?php if(@$_SESSION['filter']['values']['order']=="random"){ echo "selected"; } ?> value="random" >Random</option>
                  <option <?php if(@$_SESSION['filter']['values']['order']=="nextcall"){ echo "selected"; } ?> value="nextcall" >Nextcall</option>
                  <option <?php if(@$_SESSION['filter']['values']['order']=="lastcall"){ echo "selected"; } ?> value="lastcall" >Lastcall</option>
                  <option <?php if(@$_SESSION['filter']['values']['order']=="creation"){ echo "selected"; } ?> value="creation" >Creation</option>
                  <option <?php if(@$_SESSION['filter']['values']['order']=="turnover"){ echo "selected"; } ?> value="turnover" >Turnover</option>
                  <option <?php if(@$_SESSION['filter']['values']['order']=="employees"){ echo "selected"; } ?> value="employees" >Employees</option>
                  <option <?php if(@$_SESSION['filter']['values']['order']=="distance"){ echo "selected"; } ?> value="distance" disabled="disabled">Distance</option>
                </select>
              </div>
              <div class="form-group">
                <label>Order direction</label>
                <select id="order_direction" name="order_direction" class="selectpicker" data-width="100%" data-dropupAuto="false" data-size="2">
                  <option value="" >Ascending</option>
                  <option value="desc" >Descending</option>
                </select>
              </div>
              <div class="form-group">
                  <label>Emails</label>
                  <select id="email" name="email[]" class="selectpicker" data-width="100%" data-size="2" multiple  title="Show all">
                      <option value="read" >Records with an email read confirmed</option>
                      <option value="sent" >Records with emails sent</option>
                      <option value="unsent" >Record with failed emails</option>
                  </select>
              </div>
              <button type="submit" class="btn btn-default pull-right submit-filter">View Records</button>
              Found: <span class="record-count"></span> </div>
          </div>
        </div>
     

    
            <!---------------------->
        <!--  CUSTOM FILTER -->
        <!---------------------->
        <div class="panel panel-primary">
          <div class="panel-heading pointer" data-toggle="collapse" data-parent="#accordion" href="#collapseSix">
            <h4 class="panel-title"><div class="pull-right glyphicon glyphicon-plus"></div>Custom Field Filter</h4>
          </div>
          <div id="collapseSix" class="panel-collapse collapse">
            <div class="panel-body">
<div id="custom_fields">

</div>
              <button type="submit" class="btn btn-default pull-right submit-filter">View Records</button>
              Found: <span class="record-count"></span> </div>
          </div>
        </div>
     
    </div>
    
    
  </div>
</div>
 </form>

 <div class="panel panel-primary actions-container">
   <?php $this->view('forms/actions_filter_form.php'); ?>
 </div>
<script>
$(document).ready(function(){
	 $('.panel-collapse').on('show.bs.collapse', function () {
      $(this).prev('div').find('.glyphicon').removeClass("glyphicon-plus").addClass("glyphicon-minus");
    });

    $('.panel-collapse').on('hidden.bs.collapse', function () {
       $(this).prev('div').find('.glyphicon').removeClass("glyphicon-minus").addClass("glyphicon-plus");
    });
	
	
	$('.selectpicker').selectpicker();
	filter.init();

	$('.slider').slider({tooltip:"hide"});
	$('.slider').on('slide', function (ev){
		var newval = ev.value;	
		if(ev.value=="0"){
		newval="na";	
		}
		$(this).closest('td').next('td').find('.slider-value').val(newval);
        if (ev.value < 99) {
            $(this).find('.slider-selection').css('background', '#428041');
        }
        if (ev.value >=100 && ev.value <= 200) {
            $(this).find('.slider-selection').css('background', '#FF9900');
        }
        if (ev.value > 200) {
            $(this).find('.slider-selection').css('background', '#FF8282');
        }
    });	

	$('#slide_id').on('slideStop', function(ev){
		var newVal = ev.value;
	    $( ".distance" ).text(newVal);
		setTimeout(function() {
	    filter.check_distance();
		},300);
	});
	
	$('#collapseZero').find('.panel-body').show();
});
</script> 
