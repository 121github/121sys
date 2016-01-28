<form style="display:none; padding:10px 20px;" class="form-horizontal">
    <input type="hidden" name="campaign_id">

    <div class="form-group input-group-sm">
        <p>Campaign name</p>
        <input type="text" class="form-control" name="campaign_name" title="Enter the campaign name" required/>
    </div>
    <div class="form-group input-group-sm">
        <p>Campaign type</p>
        <select name="campaign_type_id" class="selectpicker pull-left" required>
            <option value="">Nothing selected</option>
            <?php foreach ($types as $row): ?>
                <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
            <?php endforeach ?>
        </select>

    </div>
    <div class="form-group input-group-sm">
        <p>Which client is the campaign for?</p>
        <select name="client_id" id="client-select" class="selectpicker pull-left">
            <option value="">Nothing selected</option>
            <?php foreach ($clients as $row): ?>
                <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
            <?php endforeach ?>
            <option value="other">Other</option>
        </select>
        <input type="text" name="new_client" class="form-control pull-left marl" style="width:200px; display:none"
               placeholder="Enter the client name"/>
    </div>
    <div class="form-group input-group-sm">
        <p>Which panels are required on the details page?</p>
        <select name="features[]" data-size="12" multiple class="selectpicker pull-left campaign-features">
            <?php foreach ($features as $row): ?>
                <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
            <?php endforeach ?>
        </select>
    </div>
    <div class="form-group input-group-sm">
        <p>Panel layout (If no custom view has been created please use <b>2col</b>)</p>
        <select name="record_layout" class="selectpicker" required>
            <?php foreach ($views as $view): ?>
                <option value="<?php echo $view ?>"><?php echo str_replace('.php', '', $view) ?></option>
            <?php endforeach ?>
        </select>
    </div>
    <div class="form-group input-group-sm">
        <p>Custom panel title (if applicable)</p>
        <input type="text" class="form-control" name="custom_panel_name" placeholder="Eg. Policy information" required/>
    </div>
     <div class="form-group input-group-sm">
        <p>Custom panel display format (default is table)</p>
        <select class="selectpicker" name="custom_panel_format"><option value="1">Table</option><option value="2">List</option></select>
    </div>
    <div class="form-group input-group-sm">
        <p>Please set the campaign status</p>
        <select name="campaign_status" class="selectpicker" required>
            <option value="">Nothing selected</option>
            <option value="1">Live</option>
            <option value="0">Not Live</option>
        </select>
    </div>
    <div class="form-group input-group-sm">
        <p>Please set the campaign start date</p>

        <div class="input-group">
            <input data-date-format="DD/MM/YYYY" name="start_date" type="text" class="form-control date"
                   placeholder="Start Date">
                      <span class="input-group-btn">
                      <button class="btn btn-default clear-input" type="button">Clear</button>
                      </span></div>
    </div>
    <div class="form-group input-group-sm">
        <p>Please set the campaign end date</p>

        <div class="input-group">
            <input name="end_date" data-date-format="DD/MM/YYYY" type="text" class="form-control date"
                   placeholder="End Date">
                      <span class="input-group-btn">
                      <button class="btn btn-default clear-input" type="button">Clear</button>
                      </span></div>
    </div>
 
<div class="form-group input-group-sm">
        <p>
            Please set the max dials allowed before they are removed
            <select name="max_dials">
           <option value="">No Limit</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="6">6</option>
            </select>
        </p>
    </div>
    
    <div class="form-group input-group-sm">
     Please set the fields to order virgin record calling priority
        <p>
            Priority 1:
  <select class="virgin-order" name="virgin_order_1">
    <option value="">Not Applicable</option>
           <option value="urn">URN</option>
            <option value="client_ref">Client Ref</option>
            <option value="rand()">Random</option>
           <option value="turnover">Turnover</option>
           <option value="distance">Distance from last appointment</option>
           <option value="employees">Employees</option>
          
            </select>
        </p>
         <p>
            Priority 2:
  <select class="virgin-order" name="virgin_order_2">
    <option value="">Not Applicable</option>
           <option value="urn">URN</option>
            <option value="client_ref">Client Ref</option>
            <option value="rand()">Random</option>
           <option value="turnover">Turnover</option>
           <option value="distance">Distance from last appointment</option>
           <option value="employees">Employees</option>
         
            </select>
        </p>
           </div>
        <?php if($_SESSION['role']=="1"){ ?>
          <div class="form-group input-group-sm">
        <p>Virgin Order Custom String <span class="text-danger">(inserted directly into SQL so be careful)</span></p>
        <input type="text" class="form-control" name="virgin_order_string" placeholder="Enter a custom order sting" />
    </div>
      <div class="form-group input-group-sm">
        <p>Virgin Order Join String <span class="text-danger">(inserted directly into SQL so be careful)</span></p>
        <input type="text" class="form-control" name="virgin_order_join" placeholder="join companies using(urn)" />
    </div>
    <?php } ?>
      <div class="form-group input-group-sm">
        <p>Telephone Protocol</p>
        <input type="text" class="form-control" name="telephone_protocol" placeholder="Default is callto:" value="callto:" />
    </div>
      <div class="form-group input-group-sm">
        <p>Telephone Prefix</p>
        <input type="text" class="form-control" name="telephone_prefix" placeholder="Prefix numbers for dialling eg. 9" />
    </div>
 
    <div class="form-group input-group-sm">
        <p>
            Please set the default map icon <span class="text-info">(if no record icon is set it will use this)</span></p>
            <button class="btn btn-info" id="map-icon"></button>
            <input type="hidden" name="map_icon">
    </div>


<div class="form-group input-group-sm">
        <p>Please set quote days for the renewal date (if applicable)</p>

        <div class="input-group">
            <div class="col-xs-4">
                <p>Min Quote Days</p>
                <input name="min_quote_days" type="text" class="form-control" placeholder="Min Quote">
            </div>
            <div class="col-xs-4">
                <p>Max Quote Days</p>
                <input name="max_quote_days" type="text" class="form-control" placeholder="Max Quote">
            </div>
            <div class="col-xs-3" style="color: red;">
                <p></p>

                <p class="quote_days_error"></p>
            </div>

        </div>
    </div>
    <div class="form-group input-group-sm">
        <p>Set a default archiving period</p>

        <div class="col-xs-3">
            <p>Last update less than</p>
            <input type="text" name="months_ago" class="form-control pull-left" style="width:100px;"
                   placeholder="eg. 12"/> <label class="control-label marl"> months ago</label>
        </div>
        <div class="col-xs-3">
            <p>How many months to include</p>
            <input type="text" name="months_num" class="form-control pull-left" style="width:100px;"
                   placeholder="eg. 3"/> <label class="control-label marl"> months</label>
        </div>
        <div class="col-xs-3" style="color: red;">
            <p></p>

            <p class="backup_error"></p>
        </div>
    </div>
    <div class="form-group">
        <p id="archive-example" class="green"></p>
    </div>
    <div class="form-actions pull-right">
        <button class="marl btn btn-default close-btn">Cancel</button>
        <button type="submit" class="marl btn btn-primary save-btn">Save</button>
    </div>
</form>
