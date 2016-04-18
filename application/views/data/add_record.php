<div class="row">
  <div class="col-lg-12">
    <h1 class="page-header">Add Record <span id="campaign-name-title" class="small"></span> <span class="small"
                                                                                                      id="campaign-type-title"></span> </h1>
  </div>
  <!-- /.col-lg-12 --> 
</div>
<!-- /.row -->
<div class="row">
  <div class="col-lg-12">
    <div class="panel panel-primary">
      <div class="panel-heading"> Add Record</div>
      <!-- /.panel-heading -->
      <div class="panel-body">
        <div class="row">
          <div class="col-sm-12 col-lg-4">
            <form class="record-form" id="record-form">
              <div class="form-group input-group-sm" <?php if (count($campaigns) == "1") {
                                echo "style='display:none'";
                            } ?> >
                <p>
                  <label>Campaign</label>
                </p>
                <select name="campaign_id" class="selectpicker" id="campaign">
                  <option value="">Select the campaign</option>
                  <?php foreach ($campaigns as $row) { ?>
                  <option <?php if (isset($_SESSION['current_campaign']) && $_SESSION['current_campaign'] == $row['id']) { echo "selected";
                                        } ?> ctype="<?php echo $row['type'] ?>" value="<?php echo $row['id'] ?>"> <?php echo $row['name'] ?> </option>
                  <?php } ?>
                </select>
                <p id="ctype-text" class="green" style="display:none"></p>
              </div>
              <div id="create-record">
                <div class="form-group input-group-sm">
                  <p>
                    <label id="name-label">Company Name</label>
                  </p>
                  <input type="text" name="" id="name" class="form-control  input-sm"  placeholder="Enter the name of the company"/>
                </div>
                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                  <div class="panel" style="overflow:visible">
                    <div class="" role="tab" id="headingOne">

                          <div style="display:inline-block;width:30%">
                          <label>Postcode</label>
                            <input type="text" style="display:inline-block" name="postcode" id="postcode"  class="form-control input-sm" placeholder="Postcode"/>
                            </div>
                        

                                 <div style="display:inline-block;width:35%">
                            <label>House No.</label>
                            <input style="display:inline-block" type="text" class="form-control  input-sm" placeholder="House number" id="house-number" name="" value="">
                            </div>

                               <div style="display:inline-block;width:30%">
                                     <label>&nbsp;</label>
                             <button class="btn btn-default" id="get-address">Find Address</button>
                          </div>
                          <div class="form-group"  style="padding-top:10px">
                            <div id="addresspicker-div" style="display:none">
                              <select class="form-control  input-sm addresspicker" placeholder="Address" id="addresspicker">
                              </select>
                            </div>
                          </div>
                   
              
                      <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse" aria-expanded="true" aria-controls="collapseOne" id="complete-company-address">Toggle full address view <i class="fa fa-caret-down"></i>
</a>
                      <div id="collapse" class="panel-collapse collapse" style="padding-top:5px">
                        <div class="form-group">
                          <input type="text" name="" id="add1" class="form-control input-sm" placeholder="First line of address"/>
                        </div>
                        <div class="form-group">
                          <input type="text" name="" id="add2" class="form-control input-sm" placeholder="Second line of address"/>
                        </div>
                        <div class="form-group">
                          <input type="text" name="" id="add3" class="form-control input-sm"  placeholder="Third line of address"/>
                        </div>
                        <div class="form-group">
                          <input type="text" name="" id="add4" class="form-control input-sm" placeholder="Fourth line of address"/>
                        </div>
                        <div class="form-group">
                          <input type="text" name="" id="locality"  class="form-control input-sm"  placeholder="Locality"/>
                        </div>
                        <div class="form-group">
                          <input type="text" name="" id="city"  class="form-control input-sm"  placeholder="Town/City"/>
                        </div>
                        <div class="form-group">
                          <input type="text" name="" id="county"  class="form-control input-sm"   placeholder="County"/>
                        </div>
                        <div class="form-group">
                          <input type="text" name="" id="country" class="form-control input-sm"  placeholder="Country"/>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <p>
                    <label>Telephone (optional)</label>
                  </p>
                  <input type="text" name="" id="telephone"  class="form-control  input-sm" placeholder="Enter the telephone"/>
                </div>
              </div>
              
              <!-- SUBMIT AND CANCEL BUTTONS -->
              <div class="form-actions pull-right">
                <button type="submit" class="btn btn-info marl" id="continue-btn">Check Data</button>
              </div>
              <div class="form-actions pull-right">
                <button type="submit" class="btn btn-primary marl" id="save-btn" style="display:none"> Create New </button>
              </div>
            </form>
          </div>
          <div class="col-sm-12 col-lg-8" id="dupes-found"> </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- /.row --> 

<script>
    $(document).ready(function () {
        add_record.init();
    });
</script>