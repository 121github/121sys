<div class="row">
    <div class="col-sm-12 col-lg-4" style="max-height:600px; overflow-y: auto">
        <form class="record-form" id="record-form">
            <div class="form-group input-group-sm" <?php if (count($campaigns) == "1") {
                echo "style='display:none'";
            } ?> >
                <p>
                    <label>Campaign</label>
                </p>
                <select name="campaign_id[]" class="selectpicker" id="campaign">
                    <option value="">Select the campaign</option>
                    <?php foreach ($campaigns as $row) { ?>
                        <option <?php if (isset($_SESSION['current_campaign']) && $_SESSION['current_campaign'] == $row['id']) { echo "selected";
                        } ?> ctype="<?php echo $row['type'] ?>" value="<?php echo $row['id'] ?>"> <?php echo $row['name'] ?> </option>
                    <?php } ?>
                </select>
                <p id="ctype-text" class="green" style="display:none"></p>
            </div>
            <div id="search-record">
                <div class="form-group input-group-sm">
              
                        <label id="name-label">URN</label>
                   
                    <input type="text" name="urn" id="urn" class="form-control"  placeholder="Search by unique reference number"/>
                </div>
                <div class="form-group input-group-sm">
                 
                        <label id="name-label">Reference Number</label>
                
                    <input type="text" name="client_ref" id="client_ref" class="form-control"  placeholder="Search by client reference number"/>
                </div>
                <div class="form-group input-group-sm">
              
                        <label id="name-label">Email Address</label>
                  
                    <input type="text" name="contact_email" id="email" class="form-control"  placeholder="Enter the email address"/>
                </div>
                <div class="form-group input-group-sm">
             
                        <label id="name-label">Company/Contact Name</label>
              
                    <input type="text" name="all_names" id="name" class="form-control"  placeholder="Enter the name of the company/contact"/>
                </div>
                <div class="form-group">
            
                        <label>Telephone (optional)</label>
     
                    <input type="text" name="all_phone" id="telephone"  class="form-control" placeholder="Enter the telephone"/>
                </div>
   <div class="form-group">

                                <label>Postcode</label>

                                <input type="text" name="postcode" id="postcode"  class="form-control" placeholder="Postcode"/>
                            </div>
                        </div>
        </form>
    </div>
    <div class="col-sm-12 col-lg-8" id="dupes-found" style="max-height: 500px; overflow: auto"></div>
</div>