     <form id="details-form">
               
                    <input type="hidden" name="user_id" value="<?php echo $id ?>">
                     <div class="form-group">
                        <label>Email:</label>
                        <input type="text" name="email_form" class="form-control"/>
                    </div>
                     <div class="form-group">
                        <label>Telephone:</label>
                        <input type="text" name="telephone_form" class="form-control"/>
                    </div>
                     <div class="form-group">
                        <label>Ext:</label>
                        <input type="text" name="ext_form" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label>Home/Office Postcode <span class="glyphicon glyphicon-info-sign" data-toggle="tooltip" title="This will be used to set appointments in your area"></span>:</label>
                        <input type="text" name="home_postcode" class="form-control"/>
                    </div>
                    <script>
					$('[data-toggle="tooltip"]').tooltip();
					</script>
            </form>