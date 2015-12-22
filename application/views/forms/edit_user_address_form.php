<form style="padding:0 20px;" id="user-address-form" class="form-horizontal">
    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
    <input type="hidden" name="address_id" value="">
    <!-- New address -->
    <div class="row">
        <div class="col-md-8">
            <div class="form-group">
                <input type="text" name="description" id="description" class="form-control input-sm" placeholder="Address Description"/>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <div class="btn-group pull-right">
                    <input name="primary" value="0" type="hidden" />
                    <input data-width="100px" type="checkbox" id="primary-toggle"
                           data-on="<i class='glyphicon glyphicon-home'></i> Primary"
                           data-off="<i class='glyphicon glyphicon-home'></i> Primary" data-toggle="toggle">
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div style="background-color: lightgrey; padding: 10px; width: 100%" class="col-md-12">
            <div style="display:inline-block;width:30%">
                <label>Postcode</label>
                <input type="text" style="display:inline-block" name="postcode" id="postcode"  class="form-control" placeholder="Postcode"/>
            </div>


            <div style="display:inline-block;width:35%">
                <label>House No.</label>
                <input style="display:inline-block" type="text" class="form-control" placeholder="House number" id="house-number" name="house-number" value="">
            </div>

            <div style="display:inline-block;width:30%">
                <label>&nbsp;</label>
                <button class="btn btn-default" id="get-address">Find Address</button>
            </div>

            <div class="form-group"  style="padding-top:10px; padding-left:10px">
                <div id="addresspicker-div" style="display:none">
                    <select class="form-control addresspicker" data-width="90%" placeholder="Address" id="addresspicker">
                    </select>
                </div>
            </div>

        </div>
    </div>

    <div style="margin-top: 20px">
        <div class="form-group">
            <input type="text" name="add1" id="add1" class="form-control input-sm" placeholder="First line of address"/>
        </div>
        <div class="form-group">
            <input type="text" name="add2" id="add2" class="form-control input-sm" placeholder="Second line of address"/>
        </div>
        <div class="form-group">
            <input type="text" name="add3" id="add3" class="form-control input-sm"  placeholder="Third line of address"/>
        </div>
        <div class="form-group">
            <input type="text" name="add4" id="add4" class="form-control input-sm" placeholder="Fourth line of address"/>
        </div>
        <div class="form-group">
            <input type="text" name="locality" id="locality"  class="form-control input-sm"  placeholder="Locality"/>
        </div>
        <div class="form-group">
            <input type="text" name="city" id="city"  class="form-control input-sm"  placeholder="Town/City"/>
        </div>
        <div class="form-group">
            <input type="text" name="county" id="county"  class="form-control input-sm"   placeholder="County"/>
        </div>
        <div class="form-group">
            <input type="text" name="country" id="country" class="form-control input-sm"  placeholder="Country"/>
        </div>
    </div>

</form>
                             
     
