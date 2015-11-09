<style>
    .colorpicker-2x .colorpicker-saturation {
        width: 200px;
        height: 200px;
    }
    .colorpicker-2x .colorpicker-hue,
    .colorpicker-2x .colorpicker-alpha {
        width: 30px;
        height: 200px;
    }
    .colorpicker-2x .colorpicker-color,
    .colorpicker-2x .colorpicker-color div{
        height: 30px;
    }
</style>
<ul class="nav nav-tabs">
  <li class="color-tab"><a href="#color" class="tab" data-toggle="tab">Color</a></li>
  <li class="icon-tab"><a href="#icon" class="tab" data-toggle="tab">Icon</a></li>
    <li class="campaign-tab"><a href="#campaign" class="tab" data-toggle="tab">Campaign</a></li>
  <li class="pot-tab"><a href="#pot" class="tab" data-toggle="tab">Pot</a></li>
  <li class="source-tab"><a href="#source" class="tab" data-toggle="tab">Source</a></li>
  <?php if(!in_array("park records",$_SESSION['permissions'])){ ?>
    <li class="other-tab"><a href="#other" class="tab" data-toggle="tab">Other</a></li>
    <?php } ?>
</ul>
  <form id="record-options-form">
  <input type="hidden" name="urn" value="<?php echo $urn ?>" />
<!-- Tab panes -->
<div class="tab-content">
  
  <div class="tab-pane active" id="color">
      <label>Set the record color</label>
      <p>You can assign colors to records to help identify them on the system. The icon for this record will be shown in the selected color in the map tools</p>
    <div class="input-group" id="color-picker">
        <input type="text" name="record_color" class="form-control" placeholder="Enter HEX colour code" value="#<?php echo empty($current['record_color'])?"000":$current['record_color'] ?>" />
    <span class="input-group-addon"><i></i></span>
</div>
<p class="text-info small" style="padding-top:15px">The code above is a hex colour code. You can easily find a color code by clicking the square icon on the right</p>
      </div>

      
      
        <div class="tab-pane" id="icon">
         <label>Set the record icon</label>
      <p>You can assign icons to records to help identify them on the system. The selected icon will be used to display the record on the map tools</p>
      <input type="hidden" name="map_icon" value="<?php echo !empty($current['map_icon'])?$current['map_icon']:"" ?>"/>
      <div data-search="true" data-search-text="Search..." id="map-icon" style="color:#<?php echo empty($current['record_color'])?"000":$current['record_color'] ?>" data-iconset="fontawesome" data-icon="<?php echo !empty($current['map_icon'])?$current['map_icon']:$current['campaign_icon'] ?>" role="iconpicker" data-rows="3"
data-cols="8"></div>
              
      </div>
      

              <div class="tab-pane" id="campaign">
 <p>Move this record to another campaign</p>
        <select name="campaign_id" id="record-campaign"><option value="">--Select campaign--</option>
                <?php foreach($campaigns as $row){ ?>
        <option <?php if($current['campaign_id']==$row['id']){ echo "selected"; } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
        <?php } ?>
        </select>
                <p class="text-info small" style="padding-top:15px">This will move the complete record and all associated data to the new campaign. The record will use the new campaign setup. Data in extra fields assigned to this campaign may be lost if the fields to not match</p>
      </div>
      
        <div class="tab-pane" id="pot">
 <p>Data within a campaign can be seperated into different pots for management and reporting purposes</p>
        <select name="pot_id" id="record-pot"><option value="">--Select data pot--</option>
                <?php foreach($pots as $row){ ?>
        <option <?php if($current['pot_id']==$row['id']){ echo "selected"; } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
        <?php } ?>
        </select>
      </div>
      
        <div class="tab-pane" id="source">
        <p>This is the source of the data. It's usually set when the data is loaded into the system so it shouldn't need to be changed but it can be altered here if necessary</p>
        <select name="source_id" id="record-source"><option value="">--Select data source--</option>
        <?php foreach($sources as $row){ ?>
        <option <?php if($current['source_id']==$row['id']){ echo "selected"; } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
        <?php } ?>
        </select>
      </div>
      
              <div class="tab-pane" id="other">
                      <div class="form-group">
 <label>Park/Unpark Record <span class="glyphicon glyphicon-info-sign tt" data-toggle="tooltip" title="You can park a record to prevent it from being used"></span></label>
      <br />
        <?php if(empty($current['parked_code'])){ ?>
        <select name="parked_code" id="record-park"><option value="">--Select park reason--</option>
                <?php foreach($parked_codes as $row){ ?>
        <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
        <?php } ?>
        </select>
        <?php } else { ?>
The record is currently parked with reason: <b><?php echo $current['park_reason'] ?></b><br />
        <button class="btn btn-default" id="record-unpark">Unpark Record</button>
        <?php } ?>
</div>
        <div class="form-group">
         <label>Supress record <span class="glyphicon glyphicon-info-sign tt" data-toggle="tooltip" title="This will prevent any phone numbers allocated to the record from being dialed across all associated campaigns"></span></label>
    <br /><button class="btn btn-danger" id="record-suppress">Supress Record</button>
    </div>
    
      </div>
      
      
      </div>
      </form>
      
      <script type="text/javascript" src="<?php echo base_url() ?>assets/js/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>      
<script>
    $(function(){
        $('#color-picker').colorpicker({
            customClass: 'colorpicker-2x',
            sliders: {
                saturation: {
                    maxLeft: 200,
                    maxTop: 200
                },
                hue: {
                    maxTop: 200
                },
                alpha: {
                    maxTop: 200
                }
            }
        });
    });
	

	
				 $('button[role="iconpicker"],div[role="iconpicker"]').iconpicker();
				 // Map iconpicker
      /*  $('#map-icon').on('change', function (e) {
            record.setIcon(e.icon);
        });
		*/
		$('#record-source,#record-pot,#record-campaign,#record-park').selectpicker();
		   
</script>