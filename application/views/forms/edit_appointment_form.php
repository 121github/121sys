<form style="padding:0 20px;" id="appointment-form" class="form-horizontal">
    <input type="hidden" name="appointment_id">
    <input type="hidden" name="urn" value="<?php echo $urn ?>">
    <?php if (in_array("confirm appointment", $_SESSION['permissions'])) { ?>
        <input type="hidden" name="appointment_confirmed" value="0">
    <?php } ?>
    <div class="row">
      
        <div class="col-xs-12 col-sm-5">
            <div class="form-group input-group-sm">
                <p>Appointment type</p>
                <select name="appointment_type_id" id="typepicker" class="selectpicker typepicker" title="Choose a type"
                        data-width="95%" required>
                    <?php foreach ($types as $type): ?>
                        <option data-icon="<?php echo $type['icon'] ?>"
                                value="<?php echo $type['id'] ?>"><?php echo $type['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        
          <div class="col-xs-12 col-sm-7">
            <div class="form-group input-group-sm">
                <p>Appointment title <span class='tt glyphicon glyphicon-question-sign'
                                                                  data-toggle="tooltip"
                                                                  data-title="Try to include the company or contact name"></span>
                </p>
                <input type="text" class="form-control tt" name="title" style="width:95%"
                       placeholder="Eg: Meeting with Joe Bloggs" required
                       data-toggle="tooltip"
                       data-title="Appointment title"/>
            </div>
        </div>
    </div>
 

    <div class="row">
    <div class="col-xs-12 col-sm-5">
        
            <div class="form-group">
            <p>Appointment date &amp; time</p>
                <p><span style="display:inline-block; width:70px">Start time</span>  <input type="text" style="width:130px; display:inline-block" class="form-control datetime startpicker input-sm" name="start"
                       placeholder="Enter the start time" required/></p>
          
    
                <p><span style="display:inline-block; width:70px">End time</span>  <input type="text" style="width:130px; display:inline-block" class="form-control datetime endpicker input-sm" name="end"
                       placeholder="Enter the end time" required/></p>
               <!--<p class="small">Note:blah blah</p>-->
            </div>
        </div>
         <div class="col-xs-12 col-sm-7">
               <div class="form-group">
        <p>Appointment notes <span class='tt glyphicon glyphicon-question-sign' data-toggle="tooltip"
                                                   data-title="These notes are sent to the attendee"></span></p>
        <textarea rows="3" type="text" class="form-control input-sm" name="text"
               placeholder="Please note additional info or special requirements here" required ></textarea>
    </div>
    </div>
        
        </div>

       
    <div class="row">
        <div class="col-xs-12 col-xs-6 attendees-selection">
            <div class="form-group input-group-sm">
                <p>Choose the attendee(s) <span class='tt glyphicon glyphicon-question-sign'
                                                       data-toggle="tooltip"
                                                       data-title="Whoever the appointment is set for will recieve an email notification containing all the details"></span>
                </p>
                <select name="attendees[]" id="attendee-select" class="selectpicker attendeepicker"
                        title="Choose the attendees" data-width="95%" required>
                    <?php foreach ($attendees as $attendee): ?>
                        <option value="<?php echo $attendee['user_id'] ?>"><?php echo $attendee['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="col-xs-12 col-xs-4 branches-selection" style="display:none;">
            <div class="form-group input-group-sm">
                <p>Choose the branch <span class='tt glyphicon glyphicon-question-sign' data-toggle="tooltip"
                                                  data-title="Whoever the appointment is set for the people related with this branch will recieve an email notification containing all the details"></span>
                </p>
                <select name="branch_id" id="branch-select" class="selectpicker branchpicker"
                        title="Choose the branch" data-width="95%" required>
                    <option value="">Choose the Branch...</option>
                    <?php foreach ($branches as $branch): ?>
                        <option value="<?php echo $branch['branch_id'] ?>"><?php echo $branch['branch_name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="col-xs-12 col-xs-6 contacts-selection">
            <div class="form-group input-group-sm">
                <p>Choose the Contact <span class='tt glyphicon glyphicon-question-sign' data-toggle="tooltip"
                                                   data-title="The contact the appointment is with. They must be sent an email or mail confirmation manually if required"></span>
                </p>
                <select name="contact_id" id="contact-select" class="contactpicker" title="Choose the contact"
                        data-width="100%" required>
                </select>
            </div>
        </div>

    </div>   
    
    <?php if(!in_array("other app address",$_SESSION['permissions'])&&count($addresses) == 0){ ?>
    <p class="text-info"><i class="glyphicon glyphicon-info-sign"></i> This record has no primary address.</p>
      <?php } ?>  
    <div class="row" id="select-appointment-address" <?php if (count($addresses) == 0) {
        echo 'style="display:none"';
    } ?>>

        <div class="col-lg-12">
            <div class="form-group">
                <p>The address the appointment will take place</p>
                <select name="address" class="selectpicker addresspicker" id="addresspicker" title="Choose the address" data-width="100%">
                      <?php foreach ($addresses as $address): $x=0;
					  	if(strpos($address['description'],"Access")!==false){
						continue;
						}
                            $add = ($address['type'] == "company" ? $address['name'] . ", " : "");
							$add .= addressFormat($address);
                            if(empty($address['postcode'])){ $add .= "-This address has no postcode!"; }                            ?>
                            <option
                                <?php if ($address['primary']=="1"){
                                    echo "selected";
                                }
                                if (empty($address['postcode'])) {
                                    echo "disabled";
                                } ?>
                                value="<?php echo $add . "|" . $address['postcode'] . "|" . $address['type'] . "|" . $address['id']  ?>"
                                data-title = "<?php echo $address['description'] ?>"
                            >
                                <?php echo $add ?>
                            </option>
                        <?php $x++; endforeach; ?>
                    <?php if(in_array("other app address",$_SESSION['permissions'])){ ?>
                    <option value="Other">Other</option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </div>
  <?php if(in_array("other app address",$_SESSION['permissions'])){ ?>
    <div class="row" id="add-appointment-address" <?php if (count($addresses) > 0) {
        echo 'style="display:none"';
    } ?> >
        <p>Enter the address that the appointment will take place</p>

        <div class="col-sm-12">
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <input name="add1" class="form-control" type="text" style="width:95%"
                               placeholder="First line of address"/>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group  input-group-sm">
                        <input name="add2" class="form-control" type="text" style="width:95%"
                               placeholder="Second line of address"/>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group  input-group-sm">
                        <input name="add3" class="form-control" type="text" style="width:95%"
                               placeholder="Third line of address"/>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group  input-group-sm">
                        <input name="county" class="form-control" type="text" style="width:95%" placeholder="County"/>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group  input-group-sm">
                        <input name="new_postcode" class="form-control" type="text" style="width:95%"
                               placeholder="Postcode"/>
                    </div>
                </div>
                <div class="col-sm-6">
                    <button class="btn btn-default btn-sm" id="cancel-add-address">Cancel</button>
                    <button class="btn btn-info btn-sm" id="confirm-add-address">Confirm</button>
                </div>
            </div>
        </div>
    </div>
       <?php } ?>
<?php if (in_array("access address", $_SESSION['permissions'])) { 
$access_address = false;
  foreach ($addresses as $address):
						if(strpos($address['description'],"Access")!==false){ 
							$access_address = true; 
						}						
endforeach;


if($access_address){
?>
        <div class="row" id="select-appointment-access-address">

            <div class="col-lg-12">
                <div class="form-group">
                    <p>The access address for the appointment <span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" title="This is a location that must be visited prior to attending the appointment. For example, if you need to collect some keys to gain entry"></span></p>
                    <select name="access_address" class="selectpicker accessaddresspicker" id="accessaddresspicker"
                            data-width="100%">
                        <?php foreach ($addresses as $address):
						if(strpos($address['description'],"Access")!==false){
                            $add = ($address['type'] == "company" ? $address['name'] . ", " : "");
							$add .= addressFormat($address);
                            if(empty($address['postcode'])){ $add .= "-This address has no postcode!"; };
                            ?>
                            <option
                                <?php 
                                    echo "selected ";
                              
                                if (empty($address['postcode'])) {
                                    echo "disabled";
                                } ?>
                                value="<?php echo $add . "|" . $address['postcode'] . "|" . $address['type'] . "|" . $address['id']  ?>"
                                data-title = "<?php echo $address['description'] ?>"
                            >
                                <?php echo $add ?>
                            </option>
                            <?php } ?>
                        <?php endforeach; ?>
                        <?php if(in_array("other access address",$_SESSION['permissions'])){ ?>
                    <option value="Other">Other</option>
                    <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        <!--<div id="add-appointment-access-address" <?php if (count($addresses) > 0) { echo 'style="display:none"'; } ?>>
            <p>Select the access address for the appointment</p>

            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group  input-group-sm">
                            <input name="access_add1" class="form-control" type="text" style="width:95%"
                                   placeholder="First line of address"/>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group  input-group-sm">
                            <input name="access_add2" class="form-control" type="text" style="width:95%"
                                   placeholder="Second line of address"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group  input-group-sm">
                            <input name="access_add3" class="form-control" type="text" style="width:95%"
                                   placeholder="Third line of address"/>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group  input-group-sm">
                            <input name="access_county" class="form-control" type="text" style="width:95%" placeholder="County"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group  input-group-sm">
                            <input name="access_new_postcode" class="form-control" type="text" style="width:95%"
                                   placeholder="Postcode"/>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <button class="btn btn-default btn-sm" id="cancel-add-access-address">Cancel</button>
                        <button class="btn btn-info btn-sm" id="confirm-add-access-address">Confirm</button>
                    </div>
                </div>
            </div>-->
        </div>
        <?php } ?>
      <?php    if(!$access_address){
?>	
<p class="text-info"><i class="glyphicon glyphicon-info-sign"></i> This record has no access address.</p>
<?php } ?>
        
        
         <?php } ?>
</form>
                             
     
