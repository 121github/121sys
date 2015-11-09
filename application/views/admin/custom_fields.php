
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
                        <th> Visible | </th>
                         <th> | Editable | </th>
                          <th> | Use Dropdown | </th>
                           <th> | Use Radio | </th>
                            <th> | Is color? | </th>
                             <th> | Is ownership? | </th>
                              <th> | Is client ref? </th>
                      </tr>
                      <tr>
                        <td><input class="form-control" value="" name="c1[name]" id="c1"/></td>
                        <td><input type="checkbox" class="form-control" name="c1[visible]" id="c1_vis"/></td>
                        <td><input type="checkbox" class="form-control" name="c1[editable]" id="c1_edi"/></td>
                        <td><input type="checkbox" class="form-control" name="c1[is_select]" id="c1_sel"/></td>
                         <td><input type="checkbox" class="form-control" name="c1[is_radio]" id="c1_rad"/></td>
                         <td><input type="checkbox" class="form-control" name="c1[is_color]" id="c1_col"/></td>
                         <td><input type="checkbox" class="form-control" name="c1[is_owner]" id="c1_own"/></td>
                         <td><input type="checkbox" class="form-control" name="c1[is_client_ref]" id="c1_ref"/></td>
                      </tr>

                      <tr>
                        <td><input class="form-control" value="" name="c2[name]" id="c2"/></td>
                        <td><input type="checkbox" class="form-control" name="c2[visible]" id="c2_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="c2[editable]" id="c2_edi"/></td>
                        <td><input type="checkbox" class="form-control" name="c2[is_select]" id="c2_sel"/></td>
                           <td><input type="checkbox" class="form-control" name="c2[is_radio]" id="c2_rad"/></td>
                        <td><input type="checkbox" class="form-control" name="c2[is_color]" id="c2_col"/></td>
                         <td><input type="checkbox" class="form-control" name="c2[is_owner]" id="c2_own"/></td>
                         <td><input type="checkbox" class="form-control" name="c2[is_client_ref]" id="c2_ref"/></td>
                      </tr>
                      <tr>
                        <td><input class="form-control" value="" name="c3[name]" id="c3"/></td>
                        <td><input type="checkbox" class="form-control" name="c3[visible]" id="c3_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="c3[editable]" id="c3_edi"/></td>
                        <td><input type="checkbox" class="form-control" name="c3[is_select]" id="c3_sel"/></td>
                           <td><input type="checkbox" class="form-control" name="c3[is_radio]" id="c3_rad"/></td>
                            <td><input type="checkbox" class="form-control" name="c3[is_color]" id="c3_col"/></td>
                         <td><input type="checkbox" class="form-control" name="c3[is_owner]" id="c3_own"/></td>
                               <td><input type="checkbox" class="form-control" name="c4[is_client_ref]" id="c3_ref"/></td>
                         <td></td>
                      </tr>
                      <tr>
                        <td><input class="form-control" value="" name="c4[name]" id="c4"/></td>
                        <td><input type="checkbox" class="form-control" name="c4[visible]" id="c4_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="c4[editable]" id="c4_edi"/></td>
                        <td><input type="checkbox" class="form-control" name="c4[is_select]" id="c4_sel"/></td>
                           <td><input type="checkbox" class="form-control" name="c4[is_radio]" id="c4_rad"/></td>
                            <td><input type="checkbox" class="form-control" name="c4[is_color]" id="c4_col"/></td>
                         <td><input type="checkbox" class="form-control" name="c4[is_owner]" id="c4_own"/></td>
                         <td><input type="checkbox" class="form-control" name="c4[is_client_ref]" id="c4_ref"/></td>
                      </tr>
                      <tr>
                        <td><input class="form-control" value="" name="c5[name]" id="c5"/></td>
                        <td><input type="checkbox" class="form-control" name="c5[visible]" id="c5_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="c5[editable]" id="c5_edi"/></td>
                        <td><input type="checkbox" class="form-control" name="c5[is_select]" id="c5_sel"/></td>
                           <td><input type="checkbox" class="form-control" name="c5[is_radio]" id="c5_rad"/></td>
                            <td><input type="checkbox" class="form-control" name="c5[is_color]" id="c5_col"/></td>
                         <td><input type="checkbox" class="form-control" name="c5[is_owner]" id="c5_own"/></td>
                         <td><input type="checkbox" class="form-control" name="c5[is_client_ref]" id="c5_ref"/></td>
                      </tr>
                         <tr>
                        <td><input class="form-control" value="" name="c6[name]" id="c6"/></td>
                        <td><input type="checkbox" class="form-control" name="c6[visible]" id="c6_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="c6[editable]" id="c6_edi"/></td>
                        <td><input type="checkbox" class="form-control" name="c6[is_select]" id="c6_sel"/></td>
                           <td><input type="checkbox" class="form-control" name="c6[is_radio]" id="c6_rad"/></td>
                            <td><input type="checkbox" class="form-control" name="c6[is_color]" id="c6_col"/></td>
                         <td><input type="checkbox" class="form-control" name="c6[is_owner]" id="c6_own"/></td>
                         <td><input type="checkbox" class="form-control" name="c6[is_client_ref]" id="c6_ref"/></td>
                      </tr>
                    </table>
                     <script>
					  $(document).ready(function(){
						 $(document).on('change','[id*="own"]',function(){
							if($(this).prop('checked')){
								$(this).closest('tr').find('[id*="sel"]').prop('checked',true);
								$(this).closest('tr').find('[id*="col"]').prop('checked',false);
								$(this).closest('tr').find('[id*="ref"]').prop('checked',false);
							} else {
								$(this).closest('tr').find('[id*="sel"]').prop('checked',false);
								$(this).closest('tr').find('[id*="rad"]').prop('checked',false);
							}
						 });

						  	 $(document).on('change','[id*="col"]',function(){
							if($(this).prop('checked')){
								$(this).closest('tr').find('[id*="sel"]').prop('checked',true);
								$(this).closest('tr').find('[id*="own"]').prop('checked',false);
								$(this).closest('tr').find('[id*="ref"]').prop('checked',false);
							} else {
								$(this).closest('tr').find('[id*="sel"]').prop('checked',false);
								$(this).closest('tr').find('[id*="rad"]').prop('checked',false);
							}
						 });


						   $(document).on('change','[id*="rad"]',function(){
							if($(this).prop('checked')){
								$(this).closest('tr').find('[id*="sel"]').prop('checked',false);
								$(this).closest('tr').find('[id*="col"]').prop('checked',false);
								$(this).closest('tr').find('[id*="own"]').prop('checked',false);
								$(this).closest('tr').find('[id*="ref"]').prop('checked',false);							}
						 });

					  });
					  </script>
                    <hr />
                    <h4>Custom Date fields</h4>
                    <table>
                      <tr>
                        <th>Date field name </th>
                        <th> | Visible | </th>
                        <th> | Editable | </th>
                          <th> | Renewal </th>
                      </tr>
                      <tr>
                        <td><input class="form-control" value="" name="d1[name]" id="d1"/></td>
                        <td><input type="checkbox" class="form-control"  name="d1[visible]" id="d1_vis"/></td>
                        <td><input type="checkbox"  class="form-control" name="d1[editable]" id="d1_ren"/></td>
                            <td><input type="checkbox" class="form-control" name="d1[renewal]" id="d1_edi"/></td>
                      </tr>
                      <tr>
                        <td><input class="form-control" value="" name="d2[name]" id="d2"/></td>
                        <td><input type="checkbox" class="form-control"  name="d2[visible]" id="d2_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="d2[editable]" id="d2_edi"/></td>

                        <td></td>
                      </tr>
                      <tr>
                        <td><input class="form-control" value="" name="d3[name]" id="d3"/></td>
                        <td><input type="checkbox" class="form-control"  name="d3[visible]" id="d3_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="d3[editable]" id="d3_edi"/></td>
                        <td></td>
                      </tr>
                    </table>
                    <hr />
                    <h4>Custom Datetime fields</h4>
                    <table>
                      <tr>
                        <th>Datetime field name | </th>
                        <th> | Visible | </th>
                        <th> | Editable</th>
                      </tr>
                      <tr>
                        <td><input class="form-control" value="" name="dt1[name]" id="dt1"/></td>
                        <td><input type="checkbox" class="form-control" name="dt1[visible]" id="dt1_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="dt1[editable]" id="dt1_edi"/></td>
                      </tr>
                      <tr>
                        <td><input class="form-control" value="" name="dt2[name]" id="dt2"/></td>
                        <td><input type="checkbox" class="form-control" name="dt2[visible]" id="dt2_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="dt2[editable]" id="dt2_edi"/></td>
                      </tr>
                    </table>
                    <hr />
                    <h4>Custom Numeric fields</h4>
                    <table>
                      <tr>
                        <th>Numeric field name | </th>
                        <th> | Visible | </th>
                          <th > | Editable | </th>
                          <th> | Use Dropdown</th>

                      </tr>
                      <tr>
                        <td><input class="form-control" value="" name="n1[name]" id="n1"/></td>
                        <td><input type="checkbox" class="form-control"  name="n1[visible]" id="n1_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="n1[editable]" id="n1_edi"/></td>
                        <td><input type="checkbox" class="form-control" name="n1[is_select]" id="n1_sel"/></td>
                      </tr>
                      <tr>
                        <td><input class="form-control" value="" name="n2[name]" id="n2"/></td>
                        <td><input type="checkbox" class="form-control" name="n2[visible]" id="n2_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="n2[editable]" id="n2_edi"/></td>
                        <td><input type="checkbox" class="form-control" name="n2[is_select]" id="n2_sel"/></td>
                      </tr>
                        <tr>
                            <td><input class="form-control" value="" name="n3[name]" id="n3"/></td>
                            <td><input type="checkbox" class="form-control" name="n3[visible]" id="n3_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="n3[editable]" id="n3_edi"/></td>
                            <td><input type="checkbox" class="form-control" name="n3[is_select]" id="n3_sel"/></td>
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

