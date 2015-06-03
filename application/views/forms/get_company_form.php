 <div class="get-company-container" style="padding:20px 20px 0; display:none">
    <p class="text-info"><span class="glyphicon glyphicon-info-sign"></span> This is live data from Companies House<br /></p>
        <form class="form-horizontal update-company-form">
            <input name="urn" type="hidden" value="">
            <input name="company_id" type="hidden" value="">
           
            <div class="row">
<div class="col-xs-6">
  <div class="form-group input-group-sm">
                <label style="padding:7px 0"  class="col-sm-4 small control-label">Co. Name</label>

                <div class="col-sm-8">
                    <input type="text" class="form-control input-sm" placeholder="Company name" name="company_name" value="">
                </div>
            </div>
 <div class='form-group input-group-sm' data-picktime="false">
                <label style="padding:7px 0" class="col-sm-4 small control-label">Co. Status</label>

                <div class="col-sm-8">
                    <input name="company_status" placeholder="Company status" type='text' class="form-control input-sm" value=""/>
                </div>
            </div>
</div>
<div class="col-xs-6">
 <div class='form-group input-group-sm' data-picktime="false">
                <label style="padding:7px 0"  class="col-sm-4 small control-label">Co. Number</label>

                <div class="col-sm-8">
                    <input name="company_number" placeholder="Company number" type='text' class="form-control input-sm" value=""/>
                </div>
            </div>
                        <div class='form-group input-group-sm' data-picktime="false">
                <label style="padding:7px 0"  class="col-sm-4 small control-label">Established</label>
                <div class="col-sm-8">
                    <input name="date_of_creation" placeholder="Date of Creation" type='text' class="form-control date2 input-sm" value=""/>
                </div>
            </div>

</div>

</div>
          
                 <div class="row">
<div class="col-xs-6">
           <div class='form-group input-group-sm' data-picktime="false">
                <label style="padding:7px 0"  class="col-sm-4 small control-label">Address 1</label>

                <div class="col-sm-8">
                    <input name="address_line_1" placeholder="Address Line 1" type='text' class="form-control input-sm" value=""/>
                </div>
            </div>
          <div class='form-group input-group-sm' data-picktime="false">
                <label style="padding:7px 0"  class="col-sm-4 small control-label">Locality</label>

                <div class="col-sm-8">
                    <input name="locality" placeholder="Locality" type='text' class="form-control input-sm" value=""/>
                </div>
            </div>
</div>      
<div class="col-xs-6">
            <div class='form-group input-group-sm' data-picktime="false">
                <label style="padding:7px 0"  class="col-sm-4 small control-label">Address 2</label>

                <div class="col-sm-8">
                    <input name="address_line_2" placeholder="Address Line 2" type='text' class="form-control input-sm" value=""/>
                </div>
            </div>
            <div class='form-group input-group-sm' data-picktime="false">
                <label style="padding:7px 0"  class="col-sm-4 small control-label">Postcode</label>

                <div class="col-sm-8">
                    <input name="postal_code" placeholder="Postal Code" type='text' class="form-control input-sm" value=""/>
                </div>
            </div>
</div>      
</div> 
 <select data-width="100%" class="sic_codes" name="subsector_id[]" id="sic_codes" multiple></select>

<div style="border-bottom: 1px solid grey; margin: 10px 0;">OFFICERS <small>Select the officers to add in the contacts</small></div>
            <div class="company-officers"></div>

        </form>
</div>