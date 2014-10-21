<div id="wrapper">
<div id="sidebar-wrapper">
  <?php  $this->view('dashboard/navigation.php',$page) ?>
</div>
<div id="page-content-wrapper">
  <div id="page-wrapper">
    <div class="row">
      <div class="col-lg-12">
        <h1 class="page-header">Campaign Admin</h1>
      </div>
      <!-- /.col-lg-12 --> 
    </div>
    <!-- /.row -->
    <div class="row">
      <div class="col-lg-12">
        <div class="panel panel-primary campaign-access">
          <div class="panel-heading"> <i class="fa fa-bar-chart-o fa-fw"></i>Campaign Fields </div>
          <!-- /.panel-heading -->
          <div class="panel-body">
            <form class="form-horizontal">
              <table class="table">
                <tr>
                  <td><select class="selectpicker campaign-select" name="campaign">
                      <option value="">Select a campaign</option>
                      <?php foreach($campaigns as $row){ ?>
                      <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                      <?php } ?>
                    </select>
                    <button class="btn btn-primary pull-right"  id="save_fields" style="display:none">Save</button></td>
                </tr>
                <tr>
                  <td><div class="fields_container" style="display:none">
                      <h4>Custom Text fields</h4>
                      <div class="form-group form-inline">
                        <label for="c1">1. Text field name</label>
                        <input class="form-control" value="" name="c1" id="c1"/>
                      </div>
                      <div class="form-group form-inline">
                        <label for="c2">2. Text field name</label>
                        <input class="form-control" value="" name="c2" id="c2"/>
                      </div>
                      <div class="form-group form-inline">
                        <label for="c3">3. Text field name</label>
                        <input class="form-control" value="" name="c3" id="c3"/>
                      </div>
                      <div class="form-group form-inline">
                        <label for="c4">4. Text field name</label>
                        <input class="form-control" value="" name="c4" id="c4"/>
                      </div>
                      <div class="form-group form-inline">
                        <label for="c5">5. Text field name</label>
                        <input class="form-control" value="" name="c5" id="c5"/>
                      </div>
                      <hr />
                      <h4>Custom Date fields</h4>
                      <div class="form-group form-inline">
                        <label for="d1">1. Date field name</label>
                        <input class="form-control" value="" name="d1" id="d1"/>
                      </div>
                      <div class="form-group form-inline">
                        <label for="d2">2. Date field name</label>
                        <input class="form-control" value="" name="d2" id="d2"/>
                      </div>
                      <div class="form-group form-inline">
                        <label for="d3">3. Date field name</label>
                        <input class="form-control" value="" name="d3" id="d3"/>
                      </div>
                      <hr />
                      <h4>Custom Datetime fields</h4>
                      <div class="form-group form-inline">
                        <label for="d1">1. Datetime field name</label>
                        <input class="form-control" value="" name="dt1" id="dt1"/>
                      </div>
                      <div class="form-group form-inline">
                        <label for="d2">2. Datetime field name</label>
                        <input class="form-control" value="" name="dt2" id="dt2"/>
                      </div>
                      <hr />
                      <h4>Custom Numeric fields</h4>
                      <div class="form-group form-inline">
                        <label for="d1">1. Numeric field name</label>
                        <input class="form-control" value="" name="n1" id="n1"/>
                      </div>
                      <div class="form-group form-inline">
                        <label for="d2">2. Numeric field name</label>
                        <input class="form-control" value="" name="n2" id="n2"/>
                      </div>
                      <div class="form-group">
                        <button class="btn btn-primary"  id="save_fields" >Save</button>
                      </div>
                    </div></td>
                </tr>
              </table>
            </form>
          </div>
        </div>
      </div>
      
      <!-- /.row --> 
    </div>
    <!-- /#page-wrapper --></div>
</div>
<script src="<?php echo base_url() ?>assets/js/plugins/metisMenu/jquery.metisMenu.js"></script> 
<!-- SB Admin Scripts - Include with every page --> 
<script src="<?php echo base_url() ?>assets/js/sb-admin.js"></script> 
<script>
$(document).ready(function(){
	admin.init();
});
</script> 
