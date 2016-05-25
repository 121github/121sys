 <nav id="global-filter" class="mm-menu mm--horizontal mm-offcanvas">
        <div style="padding:30px 20px 3px">
            <form id="global-filter-form">
            <?php if(in_array("filter postcode",$_SESSION['permissions'])){ ?>
            <div class="form-group">
                <label>Postcode <span class="glyphicon glyphicon-info-sign pointer tt" data-toggle="tooltip"
                                      data-placement="right" data-title="Enter a postcode to find records in the same area" data-html="true"></span></label>
                                      <input  value="<?php echo isset($_SESSION['filter']['values']['postcode'])?$_SESSION['filter']['values']['postcode']:"" ?>" class="form-control" placeholder="Enter full postcode" name="postcode" />
            </div>
            <?php } ?>
   <?php if(in_array("filter postcode",$_SESSION['permissions'])){ ?>
                   <div class="form-group">
                <label>Distance <span class="glyphicon glyphicon-info-sign pointer tt" data-toggle="tooltip"
                                      data-placement="right" data-title="Enter a distance to search from the postcode entered" data-html="true"></span></label>
                                   <select name="distance" data-width="100%" class="selectpicker">
            <option <?php if(isset($_SESSION['filter']['values']['distance'])&&$_SESSION['filter']['values']['distance']=="9999"){ echo "selected"; } ?> value="9999">Any Distance</option>
             <option value="">Match Postcocode</option>
             <option <?php if(isset($_SESSION['filter']['values']['distance'])&&$_SESSION['filter']['values']['distance']=="1"){ echo "selected"; } ?> value="1">1 Mile</option>
             <option <?php if(isset($_SESSION['filter']['values']['distance'])&&$_SESSION['filter']['values']['distance']=="3"){ echo "selected"; } ?> value="3">3 Miles</option>
             <option <?php if(isset($_SESSION['filter']['values']['distance'])&&$_SESSION['filter']['values']['distance']=="5"){ echo "selected"; } ?> value="5">5 Miles</option>
             <option <?php if(isset($_SESSION['filter']['values']['distance'])&&$_SESSION['filter']['values']['distance']=="10"){ echo "selected"; } ?> value="10">10 Miles</option>
             <option <?php if(isset($_SESSION['filter']['values']['distance'])&&$_SESSION['filter']['values']['distance']=="20"){ echo "selected"; } ?> value="20">20 Miles</option>
             <option <?php if(isset($_SESSION['filter']['values']['distance'])&&$_SESSION['filter']['values']['distance']=="30"){ echo "selected"; } ?> value="30">30 Miles</option>
             <option <?php if(isset($_SESSION['filter']['values']['distance'])&&$_SESSION['filter']['values']['distance']=="50"){ echo "selected"; } ?> value="50">50 Miles</option>
             <option <?php if(isset($_SESSION['filter']['values']['distance'])&&$_SESSION['filter']['values']['distance']=="75"){ echo "selected"; } ?> value="75">75 Miles</option>
             <option <?php if(isset($_SESSION['filter']['values']['distance'])&&$_SESSION['filter']['values']['distance']=="100"){ echo "selected"; } ?> value="100">100 Miles</option>
            </select>
            </div>
           <?php } ?>
              <?php if(in_array("filter pot",$_SESSION['permissions'])){ ?> 
            <?php  if(isset($pots) && count($pots) > 0){ ?>
            <div class="form-group">
                <label>Data Pot <span class="glyphicon glyphicon-info-sign pointer tt" data-toggle="tooltip"
                                      data-placement="right" data-title="A group of specific records within a campaign" data-html="true"></span></label>
                <select name="pot_id[]" title="All Pots" multiple class="selectpicker" data-width="100%">
                    <?php foreach ($pots as $campaign => $pot_data) { ?>
                        <optgroup label="<?php echo $campaign ?>">
                            <?php foreach ($pot_data as $row) { ?>
                                <option <?php if(isset($_SESSION['filter']['values']['pot_id']) && @in_array($row['id'],$_SESSION['filter']['values']['pot_id'])){ echo "selected"; } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
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
                                <option <?php if(isset($_SESSION['filter']['values']['source_id']) && @in_array($row['id'],$_SESSION['filter']['values']['source_id'])){ echo "selected"; } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
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
                                         data-placement="right" data-title="The last outcome on the record"
                                         data-html="true"></span></label>
                <select <?php if(isset($_SESSION['filter']['values']['outcome_id']) && @in_array($row['id'],$_SESSION['filter']['values']['outcome_id'])){ echo "selected"; } ?> name="outcome_id[]"  title="All Outcomes" multiple class="selectpicker" data-width="100%">
                  
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
                                         data-placement="right" data-title="The user the record is assigned to"
                                         data-html="true"></span></label>
                <select name="user_id[]"  title="All Users" multiple class="selectpicker" data-width="100%">
                    <?php foreach ($owners as $row) { ?>
                     
                                <option <?php if(isset($_SESSION['filter']['values']['user_id']) && @in_array($row['id'],$_SESSION['filter']['values']['user_id'])){ echo "selected"; } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
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
    
                    <?php foreach ($branches as $row) { ?>
                     
                                <option <?php if(isset($_SESSION['filter']['values']['branch_id']) && @in_array($row['id'],$_SESSION['filter']['values']['branch_id'])){ echo "selected"; } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                    <?php } ?>
                </select>
                </div>
                 <?php } ?>
                 <?php } ?> 
                  <?php if(in_array("filter region",$_SESSION['permissions'])){ ?>
                 <?php if(isset($regions) && count($regions) > 0){ ?>
                <div class="form-group">
                <label>Branch <span class="glyphicon glyphicon-info-sign pointer tt" data-toggle="tooltip"
                                         data-placement="right" data-title="The branch the record is assigned to"
                                         data-html="true"></span></label>
                <select name="branch_id[]" title="All Branches" multiple class="selectpicker" data-width="100%">
    
                    <?php foreach ($regions as $row) { ?>
                     
                                <option <?php if(isset($_SESSION['filter']['values']['region_id']) && @in_array($row['id'],$_SESSION['filter']['values']['region_id'])){ echo "selected"; } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                    <?php } ?>
                </select>
                </div>
                 <?php } ?>
                 <?php } ?> 
                <div class="form-group">
              
                  <button class="btn btn-danger pull-left clear-filter"  <?php if(isset($_SESSION['filter']['values'])?$_SESSION['filter']['values']:"") ?> >Clear</button>
                <button class="btn btn-primary pull-right apply-filter">Submit</button>
                </div>
            </form>
        </div>
    </nav>