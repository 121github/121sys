 <div class="get-company-container">
<div class="tab-content">
    <div class="tab-pane active" id="coget">
        <a href="https://www.gov.uk/government/organisations/companies-house" target="_blank" ><img style="margin-bottom: 5px;" height="40px;" src="<?php echo base_url(); ?>assets/img/companieshouse.png"></a>
        <form class="form-horizontal update-company-form">
            <input name="urn" type="hidden" value="">
            <input name="company_id" type="hidden" value="">

            <div class="form-group input-group-sm">
                <label class="col-sm-4 control-label">Co. Name</label>

                <div class="col-sm-8">
                    <input type="text" class="form-control" placeholder="Company name" name="company_name" value="">
                </div>
            </div>
            <div class='form-group input-group-sm' data-picktime="false">
                <label class="col-sm-4 control-label">Co. Number</label>

                <div class="col-sm-8">
                    <input name="company_number" placeholder="Company number" type='text' class="form-control" value=""/>
                </div>
            </div>
            <div class='form-group input-group-sm' data-picktime="false">
                <label class="col-sm-4 control-label">Co. Status</label>

                <div class="col-sm-8">
                    <input name="company_status" placeholder="Company status" type='text' class="form-control" value=""/>
                </div>
            </div>
            <div class='form-group input-group-sm' data-picktime="false">
                <label class="col-sm-4 control-label">Date of Creation</label>
                <div class="col-sm-8">
                    <input name="date_of_creation" placeholder="Date of Creation" type='text' class="form-control date2" value=""/>
                </div>
            </div>
            <div class='form-group input-group-sm' data-picktime="false">
                <label class="col-sm-4 control-label">Postal Code</label>

                <div class="col-sm-8">
                    <input name="postal_code" placeholder="Postal Code" type='text' class="form-control" value=""/>
                </div>
            </div>
            <div class='form-group input-group-sm' data-picktime="false">
                <label class="col-sm-4 control-label">Add Line1</label>

                <div class="col-sm-8">
                    <input name="address_line_1" placeholder="Address Line 1" type='text' class="form-control" value=""/>
                </div>
            </div>
            <div class='form-group input-group-sm' data-picktime="false">
                <label class="col-sm-4 control-label">Add Line2</label>

                <div class="col-sm-8">
                    <input name="address_line_2" placeholder="Address Line 2" type='text' class="form-control" value=""/>
                </div>
            </div>
            <div class='form-group input-group-sm' data-picktime="false">
                <label class="col-sm-4 control-label">Locality</label>

                <div class="col-sm-8">
                    <input name="locality" placeholder="Locality" type='text' class="form-control" value=""/>
                </div>
            </div>

            <div style="border-bottom: 1px solid grey; margin-bottom: 10px;">OFFICERS (Select the officers to add in the contacts)</div>
            <div class="company-officers"></div>

        </form>
    </div>
</div>
</div>