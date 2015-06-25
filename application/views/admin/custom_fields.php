
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
          <div class="panel-heading">Campaign Fields </div>
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
                    <table>
                      <tr>
                        <th>Text field name</th>
                        <th>Visible</th>
                      </tr>
                      <tr>
                        <td><input class="form-control" value="" name="c1[name]" id="c1"/></td>
                        <td><input type="checkbox" class="form-control" name="c1[visible]" id="c1_vis"/></td>
                      </tr>
                      <tr>
                        <td><input class="form-control" value="" name="c2[name]" id="c2"/></td>
                        <td><input type="checkbox" class="form-control" name="c2[visible]" id="c2_vis"/></td>
                      </tr>
                      <tr>
                        <td><input class="form-control" value="" name="c3[name]" id="c3"/></td>
                        <td><input type="checkbox" class="form-control" name="c3[visible]" id="c3_vis"/></td>
                      </tr>
                      <tr>
                        <td><input class="form-control" value="" name="c4[name]" id="c4"/></td>
                        <td><input type="checkbox" class="form-control" name="c4[visible]" id="c4_vis"/></td>
                      </tr>
                      <tr>
                        <td><input class="form-control" value="" name="c5[name]" id="c5"/></td>
                        <td><input type="checkbox" class="form-control" name="c5[visible]" id="c5_vis"/></td>
                      </tr>
                         <tr>
                        <td><input class="form-control" value="" name="c6[name]" id="c6"/></td>
                        <td><input type="checkbox" class="form-control" name="c6[visible]" id="c6_vis"/></td>
                      </tr>
                    </table>
                    <hr />
                    <h4>Custom Date fields</h4>
                    <table>
                      <tr>
                        <th>Date field name</th>
                        <th> Visible | </th>
                        <th> | Renewal</th>
                      </tr>
                      <tr>
                        <td><input class="form-control" value="" name="d1[name]" id="d1"/></td>
                        <td><input type="checkbox" class="form-control"  name="d1[visible]" id="d1_vis"/></td>
                        <td><input type="checkbox"  class="form-control" name="d1[renewal]" id="d1_ren"/></td>
                      </tr>
                      <tr>
                        <td><input class="form-control" value="" name="d2[name]" id="d2"/></td>
                        <td><input type="checkbox" class="form-control"  name="d2[visible]" id="d2_vis"/></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td><input class="form-control" value="" name="d3[name]" id="d3"/></td>
                        <td><input type="checkbox" class="form-control"  name="d3[visible]" id="d3_vis"/></td>
                        <td></td>
                      </tr>
                    </table>
                    <hr />
                    <h4>Custom Datetime fields</h4>
                    <table>
                      <tr>
                        <th>Datetime field name</th>
                        <th>Visible</th>
                      </tr>
                      <tr>
                        <td><input class="form-control" value="" name="dt1[name]" id="dt1"/></td>
                        <td><input type="checkbox" class="form-control" name="dt1[visible]" id="dt1_vis"/></td>
                      </tr>
                      <tr>
                        <td><input class="form-control" value="" name="dt2[name]" id="dt2"/></td>
                        <td><input type="checkbox" class="form-control" name="dt2[visible]" id="dt2_vis"/></td>
                      </tr>
                    </table>
                    <hr />
                    <h4>Custom Numeric fields</h4>
                    <table>
                      <tr>
                        <th>Numeric field name</th>
                        <th>Visible</th>
                      </tr>
                      <tr>
                        <td><input class="form-control" value="" name="n1[name]" id="n1"/></td>
                        <td><input type="checkbox" class="form-control"  name="n1[visible]" id="n1_vis"/></td>
                      </tr>
                      <tr>
                        <td><input class="form-control" value="" name="n2[name]" id="n2"/></td>
                        <td><input type="checkbox" class="form-control" name="n2[visible]" id="n2_vis"/></td>
                      </tr>
                    </table>
                    <hr />
                      <div class="form-group pull-right">
                        <button class="btn btn-primary"  id="save_fields" >Save</button>
                      </div>
                    </div>
                    </td>
               </tr>         
              </table>
            </form>
          </div>
        </div>
      </div>
      
      <!-- /.row --> 

<script src="<?php echo base_url() ?>assets/js/plugins/metisMenu/jquery.metisMenu.js"></script> 
<!-- SB Admin Scripts - Include with every page --> 
<script src="<?php echo base_url() ?>assets/js/sb-admin.js"></script> 
<script>
$(document).ready(function(){
	admin.init();
});
</script> 

