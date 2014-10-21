 <form id="filter-form">
<div class="page-header">
  <h2>Search Records<small class="pull-right"><button type="submit" class="btn btn-default submit-filter">View Records</button> <button class="btn btn-default clear-filter">Clear Filter</button> Found: <span class="record-count"></span></small></h2>
</div>
<div class="row">
  <div class="col-md-12 col-sm-12">
    <div class="panel-group" id="accordion">
      <?php if(in_array("search campaigns",$_SESSION['permissions'])){ ?>
        <div class="panel panel-primary visible">
          <div class="panel-heading">
            <h4 class="panel-title"> <a data-toggle="collapse" data-parent="#accordion" href="#collapseFive"> Campaign Filter Options </a> </h4>
          </div>
          <div id="collapseFive" class="panel-collapse collapse in">
            <div class="panel-body">
            <div class="form-group">
             <?php if(count($campaigns)>1){ ?>
                <label>Campaign</label>
                <br>
                <select  name="campaign_id[]" class="selectpicker" data-width="100%" data-size="5" multiple>
                  <?php foreach($campaigns as $row): ?>
                  <?php if(in_array($row['id'],$_SESSION['campaign_access']['array'])):  ?>
                  <option <?php if(@in_array($row['id'],$_SESSION['filter']['values']['campaign_id'])){ echo "selected"; } else { echo (isset($_SESSION['current_campaign'])&&$_SESSION['current_campaign']==$row['id']?"selected":""); } ?> value="<?php echo $row['id'] ?>" ><?php echo $row['name'] ?></option>
                  <?php endif ?>
				  <?php endforeach; ?>
                </select>
                 <?php } ?>
                 <!--
            <?php if(count($clients)>1){ ?>
                <label>Client</label>
                <br>
                <select  name="client_id[]" class="selectpicker" data-width="100%" data-size="5" multiple>
                  <?php foreach($clients as $row): ?>
                  <option <?php if(@in_array($row['id'],$_SESSION['filter']['values']['client_id'])){ echo "selected"; } ?> value="<?php echo $row['id'] ?>" ><?php echo $row['name'] ?></option>
                  <?php endforeach; ?>
                </select>
                <?php } ?>
                <?php if(count($campaign_types)>1){ ?>
                <label>Campaign Type</label>
                <br>
                <select  name="campaign_type_id[]" class="selectpicker" data-width="100%" data-size="5" multiple>
                  <?php foreach($campaign_types as $row): ?>
                  <option <?php if(@in_array($row['id'],$_SESSION['filter']['values']['campaign_type_id'])){ echo "selected"; } ?> value="<?php echo $row['id'] ?>" ><?php echo $row['name'] ?></option>
                  <?php endforeach; ?>
                </select>
                    <?php } ?>
                  <?php if(count($sources)>1){ ?>
                <label>Data Source</label>
                <br>
                <select  name="source_id[]" class="selectpicker" data-width="100%" data-size="5" multiple>
                  <?php foreach($sources as $row): ?>
                  <option <?php if(@in_array($row['id'],$_SESSION['filter']['values']['source_id'])){ echo "selected"; } ?> value="<?php echo $row['id'] ?>" ><?php echo $row['name'] ?></option>
                  <?php endforeach; ?>
                </select>
                 <?php } ?>
                  -->
              </div>
             
              <button type="submit" class="btn btn-default pull-right submit-filter">View Records</button>
              Found: <span class="record-count"></span> </div>
          </div>
        </div>
        <?php } ?>
        
        <!-------------------->
        <!--  RECORD FILTER -->
        <!-------------------->
        <div class="panel panel-primary">
          <div class="panel-heading">
            <h4 class="panel-title"> <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne"> Record Filter Options</a> </h4>
          </div>
          <div id="collapseOne" class="panel-collapse collapse ">
            <div class="panel-body">
              <div class="form-group">
                <label>URN</label>
                <input <?php if(isset($_SESSION['filter']['values']['urn'])){ echo "value='".$_SESSION['filter']['values']['urn']."'"; } ?> type="text" name="urn" class="form-control" placeholder="Enter the unique reference number">
              </div>
              <div class="form-group">
                <label>Client Reference</label>
                <input <?php if(isset($_SESSION['filter']['values']['client_ref'])){ echo "value='".$_SESSION['filter']['values']['client_ref']."'"; } ?> type="text" name="client_ref" class="form-control" placeholder="Enter the client reference ID">
              </div>
              <div class="form-group">
                <label>Last Call Outcome</label>
                <br>
                <select  name="outcome_id[]" class="selectpicker" data-width="100%" data-size="5" multiple>
                  <?php foreach($outcomes as $row): ?>
                  <option <?php if(@in_array($row['id'],$_SESSION['filter']['values']['outcome_id'])){ echo "selected"; } ?> value="<?php echo $row['id'] ?>" ><?php echo $row['name'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label>Manager Progress</label>
                <br>
                <select  name="progress_id[]" class="selectpicker" data-width="100%" data-size="5" multiple>
                  <?php foreach($progress as $row): ?>
                  <option <?php if(@in_array($row['id'],$_SESSION['filter']['values']['progress_id'])){ echo "selected"; } ?> value="<?php echo $row['id'] ?>" ><?php echo $row['name'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label>Status</label>
                <select  name="record_status[]" class="selectpicker record-status" data-width="100%" data-size="5" multiple>
                  <?php foreach($status as $row): ?>
                  <option <?php if(@in_array($row['id'],$_SESSION['filter']['values']['record_status'])||empty($_SESSION['filter']['values'])&&$row['id']=="1"){ echo "selected"; } ?> value="<?php echo $row['id'] ?>" ><?php echo $row['name'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
               <div class="form-group">
                <label>Parked Records</label>
                <select  name="parked_code[]" class="selectpicker parked-code" data-width="100%" data-size="5" multiple>
                  <?php foreach($parked_codes as $row): ?>
                  <option <?php if(@in_array($row['id'],$_SESSION['filter']['values']['parked_code'])){ echo "selected"; } ?> value="<?php echo $row['id'] ?>" ><?php echo $row['name'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label>Group Ownership</label>
                <select name="group_id[]" class="selectpicker" data-width="100%" data-size="5" multiple>
                  <?php foreach($groups as $row): ?>
                  <option  <?php if(@in_array($row['id'],$_SESSION['filter']['values']['group_id'])){ echo "selected"; } ?> value="<?php echo $row['id'] ?>" ><?php echo $row['name'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label>User Ownership</label>
                <select  name="user_id[]" class="selectpicker" data-width="100%" data-size="5" multiple>
                  <?php foreach($users as $row): ?>
                  <option <?php if(@in_array($row['id'],$_SESSION['filter']['values']['user_id'])){ echo "selected"; } ?> value="<?php echo $row['id'] ?>" ><?php echo $row['name'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label>Next Call Date</label>
                <div class="row">
                  <div class="col-md-6 col-sm-6">
                    <div class="input-group">
                      <input <?php if(@isset($_SESSION['filter']['values']['nextcall'][0])){ echo "value='".$_SESSION['filter']['values']['nextcall'][0]."'"; } ?> name="nextcall[0]" type="text" class="form-control date" placeholder="Date from">
                      <span class="input-group-btn">
                      <button class="btn btn-default clear-input" type="button">Clear</button>
                      </span></div>
                  </div>
                  <div class="col-md-6 col-sm-6">
                    <div class="input-group">
                      <input <?php if(@isset($_SESSION['filter']['values']['nextcall'][1])){ echo "value='".$_SESSION['filter']['values']['nextcall'][1]."'"; } ?> name="nextcall[1]" type="text" class="form-control date" placeholder="Date to">
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
                      <input <?php if(@isset($_SESSION['filter']['values']['date_updated'][0])){ echo "value='".$_SESSION['filter']['values']['date_updated'][0]."'";  } ?> name="date_updated[0]" type="text" class="form-control date" placeholder="Date from">
                      <span class="input-group-btn">
                      <button class="btn btn-default clear-input" type="button">Clear</button>
                      </span></div>
                  </div>
                  <div class="col-md-6 col-sm-6">
                    <div class="input-group">
                      <input <?php if(@isset($_SESSION['filter']['values']['date_updated'][1])){ echo "value='".$_SESSION['filter']['values']['date_updated'][1]."'"; } ?> name="date_updated[1]" type="text" class="form-control date" placeholder="Date to">
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
                      <input <?php if(@isset($_SESSION['filter']['values']['date_added'][0])){ echo "value='".$_SESSION['filter']['values']['date_added'][0]."'"; } ?> name="date_added[0]" type="text" class="form-control date" placeholder="Date from">
                      <span class="input-group-btn">
                      <button class="btn btn-default clear-input" type="button">Clear</button>
                      </span></div>
                  </div>
                  <div class="col-md-6 col-sm-6">
                    <div class="input-group">
                      <input <?php if(@isset($_SESSION['filter']['values']['date_added'][1])){ echo "value='".$_SESSION['filter']['values']['date_added'][1]."'"; } ?> name="date_added[1]" type="text" class="form-control date" placeholder="Date to">
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
          <div class="panel-heading">
            <h4 class="panel-title"> <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo"> Contact Filter Options </a> </h4>
          </div>
          <div id="collapseTwo" class="panel-collapse collapse">
            <div class="panel-body">
              <div class="form-group">
                <label>Contact ID</label>
                <input <?php if(@isset($_SESSION['filter']['values']['contact_id'])){ echo "value='".$_SESSION['filter']['values']['contact_id']."'"; } ?> name="contact_id" type="intextput" class="form-control" placeholder="Enter the contact ID">
              </div>
              <div class="form-group">
                <label>Name</label>
                <input <?php if(@isset($_SESSION['filter']['values']['fullname'])){ echo "value='".$_SESSION['filter']['values']['fullname']."'"; } ?> name="fullname" type="text" class="form-control" placeholder="Enter all or part of the contact's name">
              </div>
              <div class="form-group">
                <label>Phone</label>
                <input <?php if(@isset($_SESSION['filter']['values']['phone'])){ echo "value='".$_SESSION['filter']['values']['phone']."'"; } ?> name="phone" type="text" class="form-control" placeholder="Enter all or part of a phone number">
              </div>
              <div class="form-group">
                <label>Job/Position</label>
                <input name="position" <?php if(@isset($_SESSION['filter']['values']['position'])){ echo "value='".$_SESSION['filter']['values']['position']."'"; } ?> type="text" class="form-control" placeholder="Enter all or part of the job title">
              </div>
              <div class="form-group">
                <label>Date of birth</label>
                <div class="row">
                  <div class="col-md-6 col-sm-6">
                    <div class="input-group">
                      <input <?php if(@isset($_SESSION['filter']['values']['dob'][0])){  echo "value='".$_SESSION['filter']['values']['dob'][0]."'"; } ?> name="dob[0]" type="text" class="form-control dob" placeholder="Date from">
                      <span class="input-group-btn">
                      <button class="btn btn-default clear-input" type="button">Clear</button>
                      </span> </div>
                  </div>
                  <div class="col-md-6 col-sm-6">
                    <div class="input-group">
                      <input <?php if(@isset($_SESSION['filter']['values']['dob'][1])){  echo "value='".$_SESSION['filter']['values']['dob'][1]."'";; } ?> name="dob[1]" type="text" class="form-control dob" placeholder="Date to">
                      <span class="input-group-btn">
                      <button class="btn btn-default clear-input" type="button">Clear</button>
                      </span> </div>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label>Email Address</label>
                <input <?php if(@isset($_SESSION['filter']['values']['email'])){ echo "value='".$_SESSION['filter']['values']['email']."'"; } ?> name="email" type="text" class="form-control" placeholder="Enter the contact email address">
              </div>
              <div class="form-group">
                <label>Address</label>
                <input <?php if(@isset($_SESSION['filter']['values']['address'])){ echo "value='".$_SESSION['filter']['values']['address']."'"; } ?> name="address" type="text" class="form-control" placeholder="Enter all or part of the address">
              </div>
              <button type="submit" class="btn btn-default pull-right submit-filter">View Records</button>
              Found: <span class="record-count"></span></div>
          </div>
        </div>
        
        <!--------------------->
        <!--  COMPANY FILTER -->
        <!--------------------->
        <div class="panel panel-primary">
          <div class="panel-heading">
            <h4 class="panel-title"> <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree"> Company Filter Options </a> </h4>
          </div>
          <div id="collapseThree" class="panel-collapse collapse">
            <div class="panel-body">
              <div class="form-group">
                <label>Company ID</label>
                <input <?php if(@isset($_SESSION['filter']['values']['company_id'])){ echo "value='".$_SESSION['filter']['values']['company_id']."'"; } ?> name="company_id" type="text" class="form-control" placeholder="Enter the company ID">
              </div>
              <div class="form-group">
                <label>Company Name</label>
                <input <?php if(@isset($_SESSION['filter']['values']['coname'])){ echo "value='".$_SESSION['filter']['values']['coname']."'"; } ?> name="coname" type="text" class="form-control" placeholder="Enter all or part of the company name">
              </div>
              <div class="form-group">
                <label>Sector</label>
                <select  name="sector_id[]" class="selectpicker" data-width="100%" data-size="5" multiple>
                  <?php foreach($sectors as $row): ?>
                  <option <?php if(@in_array($row['id'],$_SESSION['filter']['values']['sector_id'])){ echo "selected"; } ?> value="<?php echo $row['id'] ?>" ><?php echo $row['name'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label>Subsector</label>
                <select  name="subsector_id[]" class="selectpicker" data-width="100%" data-size="5" multiple>
                  <?php foreach($subsectors as $row): ?>
                  <option <?php if(@in_array($row['id'],$_SESSION['filter']['values']['subsector_id'])){ echo "selected"; } ?> value="<?php echo $row['id'] ?>" ><?php echo $row['name'] ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label>Turnover</label>
                <div class="row">
                  <div class="col-md-6 col-sm-6">
                    <input <?php if(@isset($_SESSION['filter']['values']['turnover'][0])){ echo $_SESSION['filter']['values']['turnover'][0]; } ?> name="turnover[0]" type="text" class="form-control" placeholder="From">
                  </div>
                  <div class="col-md-6 col-sm-6">
                    <input <?php if(@isset($_SESSION['filter']['values']['turnover'][1])){ echo $_SESSION['filter']['values']['turnover'][1]; } ?> name="turnover[1]" type="text" class="form-control" placeholder="To">
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label>Employees</label>
                <div class="row">
                  <div class="col-md-6 col-sm-6">
                    <input <?php if(@isset($_SESSION['filter']['values']['employees'][0])){ echo $_SESSION['filter']['values']['employees'][0]; } ?> name="employees[0]" type="text" class="form-control" placeholder="From">
                  </div>
                  <div class="col-md-6 col-sm-6">
                    <input <?php if(@isset($_SESSION['filter']['values']['employees'][1])){ echo $_SESSION['filter']['values']['employees'][1]; } ?> name="employees[1]" type="text" class="form-control" placeholder="To">
                  </div>
                </div>
              </div>
              <button type="submit" class="btn btn-default pull-right">View Records</button>
              Found: <span class="record-count"></span> </div>
          </div>
        </div>
        
        <!------------------------------->
        <!--  POSTCODE DISTANCE FILTER -->
        <!------------------------------->
        <div class="panel panel-primary">
          <div class="panel-heading">
            <h4 class="panel-title"> <a data-toggle="collapse" data-parent="#accordion" href="#collapseFour"> Postcode Distance Filter Options </a> </h4>
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
                <input <?php if(@isset($_SESSION['filter']['values']['postcode'])){ echo "value='".$_SESSION['filter']['values']['postcode']."'"; } ?> name="postcode" type="text" class="form-control current_postcode_input" placeholder="Enter the Postcode">
                <input <?php if(@isset($_SESSION['filter']['values']['lat'])){ echo "value='".$_SESSION['filter']['values']['lat']."'"; } ?> name="lat" type="hidden">
                <input <?php if(@isset($_SESSION['filter']['values']['lng'])){ echo "value='".$_SESSION['filter']['values']['lng']."'"; } ?> name="lng" type="hidden">
              </div>
              <div class="form-group">
                <label>Distance <span class="distance"> 0</span></label>
                <input <?php if(@isset($_SESSION['filter']['values']['distance'])){ echo "value='".$_SESSION['filter']['values']['distance']."'"; } ?> name="distance" type="text" id="slide_id" class="form-control slider" data-slider-min="0" data-slider-max="300" data-slider-step="5" data-slider-value="0" data-slider-orientation="horizontal" data-slider-selection="before" data-slider-formater="zerona">
              </div>
              <button type="submit" class="btn btn-default pull-right">View Records</button>
              Found: <span class="record-count"></span> </div>
          </div>
        </div>
        
        <!---------------------->
        <!--  ADVANCED FILTER -->
        <!---------------------->
        <div class="panel panel-primary">
          <div class="panel-heading">
            <h4 class="panel-title"> <a data-toggle="collapse" data-parent="#accordion" href="#collapseFive"> Advanced Filter Options </a> </h4>
          </div>
          <div id="collapseFive" class="panel-collapse collapse">
            <div class="panel-body">
                          <div class="checkbox">
                <label>New records only (Not yet called)</label>
                <input <?php if(@isset($_SESSION['filter']['values']['new_only'])){ echo "checked"; } ?> name="new_only" type="checkbox">
              </div>
                            <div class="form-group">
                <label>Number of dials</label>
                <select name="dials" class="selectpicker" data-width="100%" data-dropupAuto="false" data-size="5">
                  <option value="" >Any</option>
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
                <input <?php if(@isset($_SESSION['filter']['values']['survey'])){ echo "checked"; } ?> name="survey" type="checkbox">
              </div>
              <div class="checkbox">
                <label>My favorites only</label>
                <input <?php if(@isset($_SESSION['filter']['values']['favorites'])){ echo "checked"; } ?> name="favorites" type="checkbox">
              </div>
              <div class="checkbox">
                <label>Flagged as urgent only</label>
                <input <?php if(@isset($_SESSION['filter']['values']['urgent'])){ echo "checked"; } ?> name="urgent" type="checkbox">
              </div>
              <div class="form-group">
                <label>Order results by</label>
                <select name="order" class="selectpicker" data-width="100%" data-dropupAuto="false" data-size="5">
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
                <select name="order_direction" class="selectpicker" data-width="100%" data-dropupAuto="false" data-size="2">
                  <option value="" >Ascending</option>
                  <option value="desc" >Descending</option>
                </select>
              </div>
              <button type="submit" class="btn btn-default pull-right submit-filter">View Records</button>
              Found: <span class="record-count"></span> </div>
          </div>
        </div>
     
    </div>
  </div>
</div>
 </form>
<script>
$(document).ready(function(){
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
	    filter.check_distance();
	});
});
</script> 
