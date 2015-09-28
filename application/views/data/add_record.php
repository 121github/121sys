
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Add Record <span id="campaign-name-title" class="small"></span> <span class="small" id="campaign-type-title"></span></h1>
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
                <div class="col-sm-12 col-lg-3">
                 <form id="record-form">
		                    	<div class="form-group input-group-sm" <?php if(count($campaigns)=="1"){ echo "style='display:none'";  } ?> >                  		
									<p><label>Campaign</label></p> 
									<select name="campaign_id" class="selectpicker" id="campaign">
                                    	<option value="">Select the campaign</option>
                                        <?php foreach($campaigns as $row){ ?>
	                                        <option <?php if(isset($_SESSION['current_campaign'])&&$_SESSION['current_campaign']==$row['id']){ echo "selected"; } ?> ctype="<?php echo $row['type'] ?>" value="<?php echo $row['id'] ?>">
	                                        	<?php echo $row['name'] ?>
	                                        </option>
                                        <?php } ?>
									</select>
                                  
									<p id="ctype-text" class="green" style="display:none"></p>
									</div>

									<div id="company" style="display: none;">
                                    	<div class="form-group input-group-sm"> 
										<p><label>Company Name</label></p>
										<input type="text" name="company_name" id="company_name" class="form-control" placeholder="Enter the name of the company" />
									</div>
                                                                        <div class="form-group input-group-sm">   
										<p><label>House number or first line of address</label></p>
										<input type="text" name="company_add1" id="add1" class="form-control"  placeholder="House number or first line of address" />
									</div>
                                    
                                
                                
                                    <div class="form-group input-group-sm">   
										<p><label>Postcode</label></p>
										<input type="text" name="company_postcode" id="company_postcode" class="form-control"  placeholder="Enter the postcode" />
									</div>
                                 
                                   
                                    <div class="form-group input-group-sm">   
										<p><label>Telephone (optional)</label></p>
										<input type="text" name="company_telephone" id="company_telephone" class="form-control" placeholder="Enter the telephone" />
									</div>
									</div>
                                    	
									<div id="contact" style="display: none;">
                                    <div class="form-group input-group-sm">   
										<p><label>Contact Name</label></p>
										<input type="text" name="contact_name" id="contact_name" class="form-control"  placeholder="Enter the name of the contact" />
									</div>
                                    
                                  <div class="form-group input-group-sm">   
										<p><label>First line of address</label></p>
										<input type="text" name="contact_add1" id="contact_add1" class="form-control"  placeholder="First line of address" />
									</div>

                                    <div class="form-group input-group-sm">   
										<p><label>Postcode</label></p>
										<input type="text" name="contact_postcode" id="contact_postcode" class="form-control"  placeholder="Enter the postcode" />
									</div>
                                 
                                   
                                    <div class="form-group input-group-sm">   
										<p><label>Telephone (optional)</label></p>
										<input type="text" name="contact_telephone" id="contact_telephone" class="form-control" placeholder="Enter the telephone" />
									</div>
                                    </div>
								
                                	

                                    
								
								<!-- SUBMIT AND CANCEL BUTTONS -->
							    <div class="form-actions pull-right">
									<button type="submit" class="btn btn-info marl" id="continue-btn">Check Data</button>
								</div>
								    <div class="form-actions pull-right">
									<button type="submit" class="btn btn-primary marl" id="save-btn" style="display:none">Create New</button>
								</div>
                            
                              </form>
                              </div>
                              
                                   <div class="col-sm-12 col-lg-9" id="dupes-found">
                      
                      </div>
                              
                              </div>
                 
                      
                      </div>
                            
                            </div>
                  
                  
                    
                </div>
                
                
                
</div>
                <!-- /.row -->


   <script>
        $(document).ready(function() {				
					add_record.init();
					});
    </script>