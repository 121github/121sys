
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
                       <tr>
                        <td><input class="form-control" value="" name="c7[name]" id="c7"/></td>
                        <td><input type="checkbox" class="form-control" name="c7[visible]" id="c7_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="c7[editable]" id="c7_edi"/></td>
                        <td><input type="checkbox" class="form-control" name="c7[is_select]" id="c7_sel"/></td>
                           <td><input type="checkbox" class="form-control" name="c7[is_radio]" id="c7_rad"/></td>
                            <td><input type="checkbox" class="form-control" name="c7[is_color]" id="c7_col"/></td>
                         <td><input type="checkbox" class="form-control" name="c7[is_owner]" id="c7_own"/></td>
                         <td><input type="checkbox" class="form-control" name="c7[is_client_ref]" id="c7_ref"/></td>
                      </tr>
                       <tr>
                        <td><input class="form-control" value="" name="c8[name]" id="c8"/></td>
                        <td><input type="checkbox" class="form-control" name="c8[visible]" id="c8_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="c8[editable]" id="c8_edi"/></td>
                        <td><input type="checkbox" class="form-control" name="c8[is_select]" id="c8_sel"/></td>
                           <td><input type="checkbox" class="form-control" name="c8[is_radio]" id="c8_rad"/></td>
                            <td><input type="checkbox" class="form-control" name="c8[is_color]" id="c8_col"/></td>
                         <td><input type="checkbox" class="form-control" name="c8[is_owner]" id="c8_own"/></td>
                         <td><input type="checkbox" class="form-control" name="c8[is_client_ref]" id="c8_ref"/></td>
                      </tr>
                       <tr>
                        <td><input class="form-control" value="" name="c9[name]" id="c9"/></td>
                        <td><input type="checkbox" class="form-control" name="c9[visible]" id="c9_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="c9[editable]" id="c9_edi"/></td>
                        <td><input type="checkbox" class="form-control" name="c9[is_select]" id="c9_sel"/></td>
                           <td><input type="checkbox" class="form-control" name="c9[is_radio]" id="c9_rad"/></td>
                            <td><input type="checkbox" class="form-control" name="c9[is_color]" id="c9_col"/></td>
                         <td><input type="checkbox" class="form-control" name="c9[is_owner]" id="c9_own"/></td>
                         <td><input type="checkbox" class="form-control" name="c9[is_client_ref]" id="c9_ref"/></td>
                      </tr>
                       <tr>
                        <td><input class="form-control" value="" name="c10[name]" id="c10"/></td>
                        <td><input type="checkbox" class="form-control" name="c10[visible]" id="c10_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="c10[editable]" id="c10_edi"/></td>
                        <td><input type="checkbox" class="form-control" name="c10[is_select]" id="c10_sel"/></td>
                           <td><input type="checkbox" class="form-control" name="c10[is_radio]" id="c10_rad"/></td>
                            <td><input type="checkbox" class="form-control" name="c10[is_color]" id="c10_col"/></td>
                         <td><input type="checkbox" class="form-control" name="c10[is_owner]" id="c10_own"/></td>
                         <td><input type="checkbox" class="form-control" name="c10[is_client_ref]" id="c10_ref"/></td>
                      </tr>
                    </table>
                     <script type="text/javascript">
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
                      <tr>
                        <td><input class="form-control" value="" name="d4[name]" id="d4"/></td>
                        <td><input type="checkbox" class="form-control"  name="d4[visible]" id="d4_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="d4[editable]" id="d4_edi"/></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td><input class="form-control" value="" name="d5[name]" id="d5"/></td>
                        <td><input type="checkbox" class="form-control"  name="d5[visible]" id="d5_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="d5[editable]" id="d5_edi"/></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td><input class="form-control" value="" name="d6[name]" id="d6"/></td>
                        <td><input type="checkbox" class="form-control"  name="d6[visible]" id="d6_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="d6[editable]" id="d6_edi"/></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td><input class="form-control" value="" name="d7[name]" id="d7"/></td>
                        <td><input type="checkbox" class="form-control"  name="d7[visible]" id="d7_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="d7[editable]" id="d7_edi"/></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td><input class="form-control" value="" name="d8[name]" id="d8"/></td>
                        <td><input type="checkbox" class="form-control"  name="d8[visible]" id="d8_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="d8[editable]" id="d8_edi"/></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td><input class="form-control" value="" name="d9[name]" id="d9"/></td>
                        <td><input type="checkbox" class="form-control"  name="d9[visible]" id="d9_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="d9[editable]" id="d9_edi"/></td>
                        <td></td>
                      </tr>
                      <tr>
                        <td><input class="form-control" value="" name="d10[name]" id="d10"/></td>
                        <td><input type="checkbox" class="form-control" name="d10[visible]" id="d10_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="d10[editable]" id="d10_edi"/></td>
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
                        <tr>
                        <td><input class="form-control" value="" name="dt3[name]" id="dt3"/></td>
                        <td><input type="checkbox" class="form-control" name="dt3[visible]" id="dt3_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="dt3[editable]" id="dt3_edi"/></td>
                      </tr>
                        <tr>
                        <td><input class="form-control" value="" name="dt4[name]" id="dt4"/></td>
                        <td><input type="checkbox" class="form-control" name="dt4[visible]" id="dt4_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="dt4[editable]" id="dt4_edi"/></td>
                      </tr>
                        <tr>
                        <td><input class="form-control" value="" name="dt5[name]" id="dt5"/></td>
                        <td><input type="checkbox" class="form-control" name="dt5[visible]" id="dt5_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="dt5[editable]" id="dt5_edi"/></td>
                      </tr>
                        <tr>
                        <td><input class="form-control" value="" name="dt6[name]" id="dt6"/></td>
                        <td><input type="checkbox" class="form-control" name="dt6[visible]" id="dt6_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="dt6[editable]" id="dt6_edi"/></td>
                      </tr>
                        <tr>
                        <td><input class="form-control" value="" name="dt7[name]" id="dt7"/></td>
                        <td><input type="checkbox" class="form-control" name="dt7[visible]" id="dt7_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="dt7[editable]" id="dt7_edi"/></td>
                      </tr>
                        <tr>
                        <td><input class="form-control" value="" name="dt8[name]" id="dt8"/></td>
                        <td><input type="checkbox" class="form-control" name="dt8[visible]" id="dt8_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="dt8[editable]" id="dt8_edi"/></td>
                      </tr>
                        <tr>
                        <td><input class="form-control" value="" name="dt9[name]" id="dt9"/></td>
                        <td><input type="checkbox" class="form-control" name="dt9[visible]" id="dt9_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="dt9[editable]" id="dt9_edi"/></td>
                      </tr>
                        <tr>
                        <td><input class="form-control" value="" name="dt10[name]" id="dt10"/></td>
                        <td><input type="checkbox" class="form-control" name="dt10[visible]" id="dt10_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="dt10[editable]" id="dt10_edi"/></td>
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
                             <th> | Use Decimals</th>

                      </tr>
                      <tr>
                        <td><input class="form-control" value="" name="n1[name]" id="n1"/></td>
                        <td><input type="checkbox" class="form-control"  name="n1[visible]" id="n1_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="n1[editable]" id="n1_edi"/></td>
                        <td><input type="checkbox" class="form-control" name="n1[is_select]" id="n1_sel"/></td>
                            <td><input type="checkbox" class="form-control" name="n1[is_decimal]" id="n1_dec"/></td>
                      </tr>
                      <tr>
                        <td><input class="form-control" value="" name="n2[name]" id="n2"/></td>
                        <td><input type="checkbox" class="form-control" name="n2[visible]" id="n2_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="n2[editable]" id="n2_edi"/></td>
                         <td><input type="checkbox" class="form-control" name="n2[is_decimal]" id="n2_dec"/></td>
                      </tr>
                        <tr>
                            <td><input class="form-control" value="" name="n3[name]" id="n3"/></td>
                            <td><input type="checkbox" class="form-control" name="n3[visible]" id="n3_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="n3[editable]" id="n3_edi"/></td>
                            <td><input type="checkbox" class="form-control" name="n3[is_select]" id="n3_sel"/></td>
                             <td><input type="checkbox" class="form-control" name="n3[is_decimal]" id="n3_dec"/></td>
                        </tr>
                        <tr>
                            <td><input class="form-control" value="" name="n4[name]" id="n4"/></td>
                            <td><input type="checkbox" class="form-control" name="n4[visible]" id="n4_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="n4[editable]" id="n4_edi"/></td>
                            <td><input type="checkbox" class="form-control" name="n4[is_select]" id="n4_sel"/></td>
                        <td><input type="checkbox" class="form-control" name="n4[is_decimal]" id="n4_dec"/></td>
                        </tr>
                        <tr>
                            <td><input class="form-control" value="" name="n5[name]" id="n5"/></td>
                            <td><input type="checkbox" class="form-control" name="n5[visible]" id="n5_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="n5[editable]" id="n5_edi"/></td>
                            <td><input type="checkbox" class="form-control" name="n5[is_select]" id="n5_sel"/></td>
                        <td><input type="checkbox" class="form-control" name="n5[is_decimal]" id="n5_dec"/></td>
                        </tr>
                        <tr>
                            <td><input class="form-control" value="" name="n6[name]" id="n6"/></td>
                            <td><input type="checkbox" class="form-control" name="n6[visible]" id="n6_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="n6[editable]" id="n6_edi"/></td>
                            <td><input type="checkbox" class="form-control" name="n6[is_select]" id="n6_sel"/></td>
                        <td><input type="checkbox" class="form-control" name="n6[is_decimal]" id="n6_dec"/></td>
                        </tr>
                        <tr>
                            <td><input class="form-control" value="" name="n7[name]" id="n7"/></td>
                            <td><input type="checkbox" class="form-control" name="n7[visible]" id="n7_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="n7[editable]" id="n7_edi"/></td>
                            <td><input type="checkbox" class="form-control" name="n7[is_select]" id="n7_sel"/></td>
                       <td><input type="checkbox" class="form-control" name="n7[is_decimal]" id="n7_dec"/></td>
                        </tr>
                        <tr>
                            <td><input class="form-control" value="" name="n8[name]" id="n8"/></td>
                            <td><input type="checkbox" class="form-control" name="n8[visible]" id="n8_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="n8[editable]" id="n8_edi"/></td>
                            <td><input type="checkbox" class="form-control" name="n8[is_select]" id="n8_sel"/></td>
                       <td><input type="checkbox" class="form-control" name="n8[is_decimal]" id="n8_dec"/></td>
                        </tr>
                        <tr>
                            <td><input class="form-control" value="" name="n9[name]" id="n9"/></td>
                            <td><input type="checkbox" class="form-control" name="n9[visible]" id="n9_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="n9[editable]" id="n9_edi"/></td>
                            <td><input type="checkbox" class="form-control" name="n9[is_select]" id="n9_sel"/></td>
                       <td><input type="checkbox" class="form-control" name="n9[is_decimal]" id="n9_dec"/></td>
                        </tr>
                        <tr>
                            <td><input class="form-control" value="" name="n10[name]" id="n10"/></td>
                            <td><input type="checkbox" class="form-control" name="n10[visible]" id="n10_vis"/></td>
                            <td><input type="checkbox" class="form-control" name="n10[editable]" id="n10_edi"/></td>
                            <td><input type="checkbox" class="form-control" name="n10[is_select]" id="n10_sel"/></td> <td><input type="checkbox" class="form-control" name="n10[is_decimal]" id="n10_dec"/></td>
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

