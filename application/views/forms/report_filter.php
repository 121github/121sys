 <nav id="filter-right" class="mm-menu mm--horizontal mm-offcanvas">
        <div style="padding:30px 20px 3px">
            <form id="filter-form">
            
            <input type="hidden" name="date_from" value="<?php echo date('Y-m-d') ?>">
            <input type="hidden" name="date_to" value="<?php echo date('Y-m-d') ?>">

            <div style="margin-bottom: 5%;">
                <button type="button" class="daterange btn btn-default" data-width="100%">
                    <span class="glyphicon glyphicon-calendar"></span>
                    <span class="date-text"> <?php echo "Today"; ?> </span>
                </button>
            </div>
            
            <?php  if(isset($campaigns) && count($campaigns) > 0){ ?>
            <div class="form-group">
                <label>Campaign <span class="glyphicon glyphicon-info-sign pointer tt" data-toggle="tooltip"
                                      data-placement="right" data-title="Only show data from these campaigns" data-html="true"></span></label>
                <select name="campaign_id[]" title="All Campaigns" multiple class="selectpicker campaign-filter" data-width="100%">
                    <?php foreach ($campaigns as $campaign => $pot_data) { ?>
                        <optgroup label="<?php echo $campaign ?>">
                            <?php foreach ($pot_data as $row) { ?>
                                <option 
								<?php if($_SESSION['current_campaign']==$row['id']) { echo "selected"; } else if(isset($_SESSION['report-filter']['values']['pot_id']) && @in_array($row['id'],$_SESSION['report-filter']['values']['pot_id'])){ echo "selected"; } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                            <?php } ?>
                        </optgroup>
                    <?php } ?>
                </select>
                </div>
                <?php } ?>
            
            
           
              <?php if(in_array("filter pot",$_SESSION['permissions'])){ ?> 
            <?php  if(isset($pots) && count($pots) > 0){ ?>
            <div class="form-group">
                <label>Data Pot <span class="glyphicon glyphicon-info-sign pointer tt" data-toggle="tooltip"
                                      data-placement="right" data-title="The data pot that the record is assigned to" data-html="true"></span></label>
                <select name="pot_id[]" title="All Pots" multiple class="selectpicker" data-width="100%">
                    <?php foreach ($pots as $campaign => $pot_data) { ?>
                        <optgroup label="<?php echo $campaign ?>">
                            <?php foreach ($pot_data as $row) { ?>
                                <option <?php if(isset($_SESSION['report-filter']['values']['pot_id']) && @in_array($row['id'],$_SESSION['report-filter']['values']['pot_id'])){ echo "selected"; } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                            <?php } ?>
                        </optgroup>
                    <?php } ?>
                </select>
                </div>
                <?php } ?>
                <?php } ?>
                   <?php if(in_array("filter source",$_SESSION['permissions'])){ ?>
                 <?php  if(isset($sources) && count($sources) > 0){ ?>
                <div class="form-group">
                <label>Data Source <span class="glyphicon glyphicon-info-sign pointer tt" data-toggle="tooltip"
                                         data-placement="right" data-title="The source of the data"
                                         data-html="true"></span></label>
                <select name="source_id[]"  title="All Sources" multiple class="selectpicker" data-width="100%">
                  
                    <?php foreach ($sources as $campaign => $data_source) { ?>
                        <optgroup label="<?php echo $campaign ?>">
                            <?php foreach ($data_source as $row) { ?>
                                <option <?php if(isset($_SESSION['report-filter']['values']['source_id']) && @in_array($row['id'],$_SESSION['report-filter']['values']['source_id'])){ echo "selected"; } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                            <?php } ?>
                        </optgroup>
                    <?php } ?>
                </select>
                </div>
                 <?php } ?>
                 <?php } ?>
                                  <?php  if(in_array("filter outcomes",$_SESSION['permissions'])) ?>
                                  <?php if(isset($outcomes) && count($outcomes) > 0){ ?>
                <div class="form-group">
                <label>Outcome <span class="glyphicon glyphicon-info-sign pointer tt" data-toggle="tooltip"
                                         data-placement="right" data-title="The outcome that was selected"
                                         data-html="true"></span></label>
                <select <?php if(isset($_SESSION['report-filter']['values']['outcome_id']) && @in_array($row['id'],$_SESSION['report-filter']['values']['outcome_id'])){ echo "selected"; } ?> name="outcome_id[]"  title="All Outcomes" multiple class="selectpicker" data-width="100%">
                  
                    <?php foreach ($outcomes as $row) { ?>
                     
                                <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                    <?php } ?>
                </select>
                </div>
                 <?php } ?>
                 
                    <?php if(in_array("filter user",$_SESSION['permissions'])){ ?>
                         <?php  if(isset($owners) && count($owners) > 0){ ?>
                <div class="form-group">
                <label>User <span class="glyphicon glyphicon-info-sign pointer tt" data-toggle="tooltip"
                                         data-placement="right" data-title="The user that made the update"
                                         data-html="true"></span></label>
                <select name="user_id[]"  title="All Users" multiple class="selectpicker" data-width="100%">
                    <?php foreach ($owners as $row) { ?>
                     
                                <option <?php if(isset($_SESSION['report-filter']['values']['user_id']) && @in_array($row['id'],$_SESSION['report-filter']['values']['user_id'])){ echo "selected"; } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                    <?php } ?>
                </select>
                </div>
                 <?php } ?>
                  <?php } ?>
                  <?php if(in_array("filter branch",$_SESSION['permissions'])){ ?>
             
                 <?php if(isset($branches) && count($branches) > 0){ ?>
                <div class="form-group">
                <label>Branch <span class="glyphicon glyphicon-info-sign pointer tt" data-toggle="tooltip"
                                         data-placement="right" data-title="The branch the record is assigned to"
                                         data-html="true"></span></label>
                <select name="branch_id[]" title="All Branches" multiple class="selectpicker" data-width="100%">
    
                               <?php foreach ($branches as $campaign => $branch) { ?>
                        <optgroup label="<?php echo $campaign ?>">
                            <?php foreach ($branch as $row) { ?>
                                <option <?php if(isset($_SESSION['report-filter']['values']['source_id']) && @in_array($row['id'],$_SESSION['report-filter']['values']['source_id'])){ echo "selected"; } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                            <?php } ?>
                        </optgroup>
                    <?php } ?>
                </select>
                </div>
                 <?php } ?>
                 <?php } ?> 
                  <?php if(in_array("filter region",$_SESSION['permissions'])){ ?>
                 <?php if(isset($regions) && count($regions) > 0){ ?>
                <div class="form-group">
                <label>Region <span class="glyphicon glyphicon-info-sign pointer tt" data-toggle="tooltip"
                                         data-placement="right" data-title="The region the record is assigned to"
                                         data-html="true"></span></label>
                <select name="region_id[]" title="All Regions" multiple class="selectpicker" data-width="100%">
                       <?php foreach ($regions as $campaign => $region) { ?>
                        <optgroup label="<?php echo $campaign ?>">
                            <?php foreach ($region as $row) { ?>
                                <option <?php if(isset($_SESSION['report-filter']['values']['source_id']) && @in_array($row['id'],$_SESSION['report-filter']['values']['source_id'])){ echo "selected"; } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                            <?php } ?>
                        </optgroup>
                    <?php } ?>
                   
                </select>
                </div>
                 <?php } ?>
                 <?php } ?> 
                 
                       
              <?php if(in_array("filter status",$_SESSION['permissions'])){ ?>
                <div class="form-group">
                <label>Status <span class="glyphicon glyphicon-info-sign pointer tt" data-toggle="tooltip" data-placement="right" data-title="The status of the record" data-html="true"></span></label>
                <select name="record_status[]" title="Any Status" multiple class="selectpicker" data-width="100%">                   <?php foreach($status as $row): ?>
                  <option <?php if(isset($_SESSION['filter']['values']['record_status']) && @in_array($row['id'],$_SESSION['filter']['values']['record_status'])
				  ||count($status)=="1"){ echo "selected"; } ?> value="<?php echo $row['id'] ?>" ><?php echo $row['name'] ?></option>
                  <?php endforeach; ?>
                </select>
                </div>
               <?php } ?> 

                <div class="form-group">
              
                  <button class="btn btn-danger pull-left clear-filter"  <?php echo (isset($_SESSION['report-filter']['values'])?"":"disabled") ?> >Clear</button>
                <button class="btn btn-primary pull-right" id="filter-submit">Submit</button>
                </div>
            </form>
        </div>
    </nav>