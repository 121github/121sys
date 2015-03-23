
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Add Record <span id="campaign-name-title" class="small"></span> <span class="small" id="campaign-type-title"></span></h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <form id="record-form">
                    	<div class="panel panel-primary">
                            <div class="panel-heading"> <i class="fa fa-bar-chart-o fa-fw"></i>Add Record
                            </div>
                            <!-- /.panel-heading -->
                            <div class="panel-body">
		                    	<div class="form-group input-group-sm">                  		
									<p><label>Campaign</label></p>
									<select name="campaign_id" class="selectpicker" id="campaign">
                                    	<option value="">Select the campaign</option>
                                        <?php foreach($campaigns as $row){ ?>
	                                        <option ctype="<?php echo $row['type'] ?>" value="<?php echo $row['id'] ?>">
	                                        	<?php echo $row[ 'name'] ?>
	                                        </option>
                                        <?php } ?>
									</select>
									<p id="ctype-text" class="green" style="display:none"></p>
									
									<div id="company" style="display: none;">
										<p><label>Company Name</label></p>
										<input type="text" name="company_name" id="company_name" class="form-control" style="width:200px;  placeholder="Enter the name of the company" />
									</div>
									
									<div id="contact" style="display: none;">
										<p><label>Contact Name</label></p>
										<input type="text" name="contact_name" id="contact_name" class="form-control" style="width:200px;  placeholder="Enter the name of the contact" />
									</div>
								</div>
								
								<!-- SUBMIT AND CANCEL BUTTONS -->
							    <div class="form-actions pull-right">
									<button class="marl btn btn-default close-btn">Cancel</button>
									<button type="submit" class="marl btn btn-primary save-btn">Save</button>
								</div>
							</div>
                    </form>
                </div>

                <!-- /.row -->


   <script>
        $(document).ready(function() {				
					add_record.init();
					});
    </script>