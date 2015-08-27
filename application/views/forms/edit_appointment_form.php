<form style="padding:0 20px;" id="appointment-form" class="form-horizontal">
    <input type="hidden" name="appointment_id">
    <input type="hidden" name="urn" value="<?php echo $urn ?>">

    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <div class="form-group input-group-sm">
                <p>Please enter a title for the appoinment <span class='tt glyphicon glyphicon-question-sign'
                                                                 data-toggle="tooltip"
                                                                 data-title="Try to include the company or contact name"></span>
                </p>
                <input type="text" class="form-control" name="title" style="width:95%"
                       placeholder="Eg: Meeting with Joe Bloggs" required/>
            </div>
        </div>
        <div class="col-xs-12 col-sm-6">
            <div class="form-group input-group-sm">
                <p>Please choose the appointment type</p>
                <select name="appointment_type_id" class="selectpicker typepicker" title="Choose a type"
                        data-width="95%" required>
                    <?php foreach ($types as $type): ?>
                        <option data-icon="<?php echo $type['icon'] ?>"
                                value="<?php echo $type['id'] ?>"><?php echo $type['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>
    <div class="form-group input-group-sm">
        <p>Please add appointment notes here <span class='tt glyphicon glyphicon-question-sign' data-toggle="tooltip"
                                                   data-title="These notes are sent to the attendee"></span></p>
        <input type="text" class="form-control" name="text"
               placeholder="Please note additional info or special requirements here" required/>
    </div>

    <div class="row">
        <div class="col-xs-12 col-sm-6">
            <div class="form-group input-group-sm">
                <p>Please set the start time</p>
                <input type="text" style="width:95%" class="form-control datetime startpicker" name="start"
                       placeholder="Enter the start time" required/>
            </div>
        </div>

        <div class="col-xs-12 col-sm-6">
            <div class="form-group input-group-sm">
                <p>Please set the end time</p>
                <input type="text" style="width:100%" class="form-control datetime endpicker" name="end"
                       placeholder="Enter the end time" required/>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-xs-6">
            <div class="form-group input-group-sm">
                <p>Please choose the attendee <span class='tt glyphicon glyphicon-question-sign' data-toggle="tooltip"
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
        <div class="col-xs-12 col-xs-6">
            <div class="form-group input-group-sm">
                <p>Please choose the Contact <span class='tt glyphicon glyphicon-question-sign' data-toggle="tooltip"
                                                   data-title="The contact the appointment is with. They must be sent an email or mail confirmation manually if required"></span>
                </p>
                <select name="contact_id" id="contact-select" class="contactpicker" title="Choose the contact"
                        data-width="100%" required>
                </select>
            </div>
        </div>

    </div>
    <div class="row" id="select-appointment-address" <?php if (count($addresses) == 0) {
        echo 'style="display:none"';
    } ?>>

        <div class="col-lg-12">
            <div class="form-group input-group-sm">
                <p>Please select the address the appointment will take place</p>
                <select name="address" class="selectpicker addresspicker" id="addresspicker" title="Choose the address"
                        data-width="100%">
                    <?php foreach ($addresses as $address):
                        $add = ($address['type'] == "company" ? $address['name'] . ", " : "");
                        $add1 = (isset($address['add1']) ? $address['add1'] : "");
                        $add2 = (isset($address['add2']) ? ", ".$address['add2'] : "");
                        $add3 = (isset($address['add3']) ? ", ".$address['add3'] : "");
                        $county = (isset($address['county'])?", ".$address['county']:"");
                        $add = $add1.$add2.$add3.$county;
                        $add .= (!empty($address['postcode']) ? ", " . $address['postcode'] : " - This address has no postcode!");
                        ?>
                        <option <?php if (empty($address['postcode'])) {
                            echo "disabled";
                        } ?> value="<?php echo $add . "|" . $address['postcode'] ?>"><?php echo $add ?></option>
                    <?php endforeach; ?>
                    <option value="Other">Other</option>
                </select>&nbsp;


            </div>
        </div>
    </div>
</form>
<div id="add-appointment-address" <?php if (count($addresses) > 0) {
    echo 'style="display:none"';
} ?> >
    <p>Please enter the address that the appointment will take place</p>

    <div class="col-sm-12">
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group  input-group-sm">
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
                             
     
