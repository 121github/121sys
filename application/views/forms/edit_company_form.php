<ul class="nav nav-tabs" style=" background:#eee; width:100%;">
  <li class="active"><a href="#general" class="tab" data-toggle="tab">General</a></li>
  <li class="tab-alert">You must create the company before adding phone numbers</li>
  <li class="phone-tab"><a href="#phone" class="tab" data-toggle="tab">Phone Numbers</a></li>
  <li class="address-tab"><a href="#address" class="tab" data-toggle="tab">Addresses</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
  <div class="tab-pane active" id="general">
    <form class="form-horizontal">
      <input name="urn" type="hidden" value="">
      <input name="company_id" type="hidden" value="">
      <div class="form-group input-group-sm">
        <label class="col-sm-3 control-label">Co. Name</label>
        <div class="col-sm-9">
          <input type="text" class="form-control" placeholder="Company name" name="name" value="">
        </div>
      </div>
      <div class="form-group input-group-sm">
        <label class="col-sm-3 control-label">Description</label>
        <div class="col-sm-9">
          <input type="text" class="form-control" placeholder="Company Description" name="description"
                           value="">
        </div>
      </div>
      <div class='form-group input-group-sm' data-picktime="false">
        <label class="col-sm-3 control-label">Company #</label>
        <div class="col-sm-9">
          <input name="conumber" placeholder="Company number" type='text' class="form-control" value=""/>
        </div>
      </div>
      <div class="form-group input-group-sm">
        <label class="col-sm-3 control-label">Turnover</label>
        <div class="col-sm-9">
          <input type="text" class="form-control force-int" placeholder="Annual Turnover" name="turnover"
                           value="">
        </div>
      </div>
      <div class="form-group input-group-sm">
        <label class="col-sm-3 control-label">Website</label>
        <div class="col-sm-9">
          <input type="text" class="form-control" placeholder="Website address" name="website" value="">
        </div>
      </div>
      <div class="form-group input-group-sm">
        <label class="col-sm-3 control-label">Employees</label>
        <div class="col-sm-9">
          <input type="text" class="form-control force-int" placeholder="Number of employees" name="employees"
                           value="">
        </div>
      </div>
      <div class="form-group input-group-sm">
        <label class="col-sm-3 control-label">Status</label>
        <div class="col-sm-9">
          <input type="text" class="form-control force-int" placeholder="Company status" name="status"
                           value="">
        </div>
      </div>
      <div class='form-group input-group-sm' data-picktime="false">
        <label class="col-sm-3 control-label">Date of Creation</label>
        <div class="col-sm-9">
          <input name="date_of_creation" placeholder="Date of Creation" type='text' class="form-control dateyears" value=""/>
        </div>
      </div>
    </form>
  </div>
  <div class="tab-pane" id="phone">
    <div class="table-container">
      <p class="pull-right"><a href="#" class="company-add-item">Add phone number</a>
      <div class="clearfix"></div>
      </p>
      <p class="none-found">There are no numbers linked to this company</p>
      <table class="table">
        <thead>
        <th>Description</th>
          <th>Number</th>
          <th>CTPS</th>
          <th>Options</th>
            </thead>
        <tbody>
        </tbody>
      </table>
    </div>
    <form class="form-horizontal" id="company-phone-form">
      <input name="company_id" type="hidden" value="">
      <input class="item-id" name="telephone_id" type="hidden" value="">
            <div class="form-group">
      <label>Enter the phone number details</label>
        <div class="input-group">
          <div class="input-group-btn">
            <button type="button" class="btn btn-default dropdown-toggle btn-sm" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Description <span class="caret"></span></button>
            <ul class="dropdown-menu">
              <li><a href="#">Reception</a></li>
              <li><a href="#">Head Quarters</a></li>
              <li><a href="#">Direct Line</a></li>
              <li><a href="#">Fax</a></li>
              <li><a href="#">Switchboard</a></li>
            </ul>
          </div>
          <!-- /btn-group -->
<input type="text" class="form-control input-sm" placeholder="Select the number type or enter it here"
 name="description" value="">
        </div>
        <!-- /input-group --> 
      </div>
      <div class="form-group">
        <input type="text" class="form-control input-sm" placeholder="Enter the phone number" name="telephone_number"  value="">
      </div>
      <div class="form-group">
        <div class="row">
          <div class="col-sm-9">
            <label>Is the number on the TPS list?</label>
            <select class="form-control selectpicker" placeholder="Is this number CTPS registered?" name="ctps">
              <option value="1">Yes</option>
              <option value="0">No</option>
              <option value="">Unknown</option>
            </select>
          </div>
          <div class="col-sm-3">
            <label style="display:block">&nbsp;</label>
            <span class="btn btn-info ctps-btn">Check CTPS</span> </div>
        </div>
      </div>
    </form>
  </div>
  <div class="tab-pane" id="address">
    <div class="table-container">
      <p class="pull-right"><a href="#" class="company-add-item">Add another address</a>
      <div class="clearfix"></div>
      </p>
      <p class="none-found">There are no addresses linked to this company</p>
      <table class="table">
        <thead>
        <th>Add1</th>
          <th>Postcode</th>
          <th>Primary</th>
          <th>Options</th>
            </thead>
        <tbody>
        </tbody>
      </table>
    </div>
    <form class="form-horizontal" id="company-address-form">
      <input class="item-id" name="address_id" type="hidden" value="">
      <input name="company_id" type="hidden" value="">
          <div class="form-group">
        <label>Enter the address details <span class="glyphicon glyphicon-info-sign tt" data-toggle="tooltip" title="Enter the postcode and house number below to auto populate the address fields"></span></label>
      </div>
      <div class="row">
        <div class="col-md-3">
          <div class="form-group">
            <input type="text" class="form-control input-sm" style="width:95%" placeholder="Postcode" name="postcode" value="">
          </div>
        </div>
        <?php if (in_array("get address", $_SESSION['permissions'])) { ?>
        <div class="col-md-3">
          <div class="form-group">
            <input type="text" class="form-control input-sm" style="width:95%"   placeholder="House number" name="house_number"  value="">
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <button class="btn btn-sm btn-info get-company-address" style="width:95%" >Find Address</button>
          </div>
        </div>
        <div class="col-md-3">
          <div class="form-group">
            <div class="btn-group pull-right">
            <input name="primary" value="0" type="hidden" />
              <input data-width="100px" style="display:none" type="checkbox" id="primary-toggle"
                           data-on="<i class='glyphicon glyphicon-home'></i> Primary"
                           data-off="<i class='glyphicon glyphicon-home'></i> Primary" data-toggle="toggle">
            </div>
          </div>
        </div>
        <?php } ?>
      </div>
      <div class="form-group input-group-sm address-select" style="display:none;">
        <select class="form-control selectpicker" placeholder="Address" name="address">
        </select>
      </div>
      <hr />
      <div class="form-group input-group-sm">
        <input type="text" class="form-control" placeholder="1st Line of address" name="add1" value="">
      </div>
      <div class="form-group input-group-sm">
        <input type="text" class="form-control" placeholder="2nd Line of address" name="add2" value="">
      </div>
      <div class="form-group input-group-sm">
        <input type="text" class="form-control" placeholder="3rd Line of address" name="add3" value="">
      </div>
      <div class="form-group input-group-sm">
        <input type="text" class="form-control" placeholder="4rd Line of address" name="add4" value="">
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group input-group-sm">
            <input type="text" class="form-control" placeholder="Locality" name="locality" value="">
          </div>
        </div>
        <div class="col-md-1"></div>
        <div class="col-md-5">
          <div class="form-group input-group-sm">
            <input type="text" class="form-control" placeholder="Town/City" name="city" value="">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group input-group-sm">
            <input type="text" class="form-control" placeholder="County" name="county" value="">
          </div>
        </div>
        <div class="col-md-1"></div>
        <div class="col-md-5">
          <div class="form-group input-group-sm">
            <input type="text" class="form-control" placeholder="Country" name="country" value="">
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
<script>
$('#primary-toggle').bootstrapToggle({
            onstyle: 'success',
        }).show().change(function(){
			if($('#primary-toggle').prop('checked')==true){
			$tab.find('form input[name="primary"]').val('1');
			} else {
			$tab.find('form input[name="primary"]').val('0');	
			}
		});
		/*
    function changeNumberFunction() {
        var company_id = $('#company-phone-form').find('input[name="company_id"]').val();
        var telephone_number = $('#company-phone-form').find('input[name="telephone_number"]').val();
        var telephone_id = $('#company-phone-form').find('input[name="telephone_id"]').val();
        var ctps = "";
        if (telephone_number.length > 0) {
            ctps = "<span class='glyphicon glyphicon-question-sign black edit-ctps-btn tt pointer' item-company-id='"+company_id+"' item-number-id='"+telephone_id+"' item-number='"+telephone_number+"' data-toggle='tooltip' data-placement='right' title='CTPS Status is unknown. Click to check it'></span>";
            $('select[name="ctps"]').selectpicker('val', "");
        }
        $('.edit-ctps').html(ctps);
    }

    function changeCtpsFunction() {
        var company_id = $('#company-phone-form').find('input[name="company_id"]').val();
        var telephone_number = $('#company-phone-form').find('input[name="telephone_number"]').val();
        var telephone_id = $('#company-phone-form').find('input[name="telephone_id"]').val();
        var ctps_option = $('#company-phone-form').find('select[name="ctps"]').val();
        var ctps = "";
        if (telephone_number.length > 0) {
            if (ctps_option.length == 0) {
                ctps = "<span class='glyphicon glyphicon-question-sign black edit-ctps-btn tt pointer' item-company-id='" + company_id + "' item-number-id='" + telephone_id + "' item-number='" + telephone_number + "' data-toggle='tooltip' data-placement='right' title='CTPS Status is unknown. Click to check it'></span>";
            }
            else if (ctps_option == 1) {
                ctps = "<span class='glyphicon glyphicon-exclamation-sign red tt' data-toggle='tooltip' data-placement='right' title='This number IS CTPS registered'></span>";
            }
            else {
                ctps = "<span class='glyphicon glyphicon-ok-sign green tt' data-toggle='tooltip' data-placement='right' title='This number is NOT CTPS registerd'></span>";
            }
            $tab.find('.edit-ctps').html(ctps);
        }
    }

    $(document).on('click', '.edit-ctps-btn', function (e) {
        e.preventDefault();
        check_edit_ctps();
    });

    function check_edit_ctps() {
        var company_id = $('#company-phone-form').find('input[name="company_id"]').val();
        var telephone_number = $('#company-phone-form').find('input[name="telephone_number"]').val();
        var telephone_id = $('#company-phone-form').find('input[name="telephone_id"]').val();
        var ctps = '';

        console.log(telephone_number);
        $.ajax({
            url: helper.baseUrl + 'cron/check_tps',
            type: "POST",
            dataType: "JSON",
            data: {
                telephone_number: telephone_number,
                type: "ctps",
                company_id: company_id
            }
        }).done(function (response) {
            flashalert.warning(response.msg);
            if (response.ctps == 1) {
                ctps = "<span class='glyphicon glyphicon-exclamation-sign red tt' data-toggle='tooltip' data-placement='right' title='This number IS CTPS registered'></span>";
                $('#company-phone-form').find('select[name="ctps"]').selectpicker('val', 1);
                $tab.find('.edit-ctps').html(ctps);
            }
            else if (response.ctps == 0) {
                ctps = "<span class='glyphicon glyphicon-ok-sign green tt' data-toggle='tooltip' data-placement='right' title='This number is NOT CTPS registerd'></span>";
                $('#company-phone-form').find('select[name="ctps"]').selectpicker('val', 0);
                $tab.find('.edit-ctps').html(ctps);
            }
        });
    }
*/
    $(".dropdown-menu li a").click(function () {
        $('#company-phone-form').find('input[name="description"]').val($(this).text());
    });

    //Get address

    var addresses;
    $('.address-select').hide();
    $('#company-address-form').find('input[name="house_number"]').numeric();

    $(document).on('click', '.get-company-address', function (e) {
        e.preventDefault();
        get_addresses();
    });

    function get_addresses() {
        var postcode = $('#company-address-form').find('input[name="postcode"]').val();
        var house_number = $('#company-address-form').find('input[name="house_number"]').val();
        $('#company-address-form')[0].reset();
        $('#company-address-form').find('input[name="postcode"]').val(postcode);
        $('#company-address-form').find('input[name="house_number"]').val(house_number);
        $.ajax({
            url: helper.baseUrl + 'ajax/get_addresses_by_postcode',
            type: "POST",
            dataType: "JSON",
            data: {postcode: postcode, house_number: house_number}
        }).done(function (response) {
            if (response.success) {
                $('#company-address-form').find('input[name="postcode"]').val(response.postcode);
                addresses = response.data;
                flashalert.info("Please select the correct address");
                var options = "<option value=''>Select an address...</option>";

                $.each(response.data, function (i, val) {
                    options += '<option value="' + i + '">' +
                        (val.add1 ? val.add1 : '') +
                        (val.add2 ? ", " + val.add2 : '') +
                        (val.add3 ? ", " + val.add3 : '') +
                        (val.add4 ? ", " + val.add4 : '') +
                        (val.locality ? ", " + val.locality : '') +
                        (val.city ? ", " + val.city : '') +
                        (val.county ? ", " + val.county : '') +
                        (typeof val.postcode_io.country != "undefined" ? ", " + val.postcode_io.country : '') +
                        '</option>';
                });
                $('#company-address-form').find('select[name="address"]')
                    .html(options)
                    .selectpicker('refresh');

                //If the house number is found set this option by default
                if (response.address_selected !== null && response.address_selected !== undefined) {
                    var address = addresses[response.address_selected];
                    $('#company-address-form').find('input[name="add1"]').val(address.add1);
                    $('#company-address-form').find('input[name="add2"]').val(address.add2);
                    $('#company-address-form').find('input[name="add3"]').val(address.add3);
                    $('#company-address-form').find('input[name="add4"]').val(address.add4);
                    $('#company-address-form').find('input[name="locality"]').val(address.locality);
                    $('#company-address-form').find('input[name="city"]').val(address.city);
                    $('#company-address-form').find('input[name="county"]').val(address.county);
                    $('#company-address-form').find('input[name="country"]').val(address.postcode_io.country);

                    $('#company-address-form').find('select[name="address"]')
                        .val(response.address_selected)
                        .selectpicker('refresh');
                }

                modal_body.css('overflow', 'visible');
                $('.address-select').show();
            }
            else {
                modal_body.css('overflow', 'auto');
                $('.address-select').hide();
                flashalert.danger("No address found");
            }
        });

        $('.address-select .selectpicker').change(function () {

            var selectedId = $(this).val();
            var address = addresses[selectedId];
            $('#company-address-form').find('input[name="add1"]').val(address.add1);
            $('#company-address-form').find('input[name="add2"]').val(address.add2);
            $('#company-address-form').find('input[name="add3"]').val(address.add3);
            $('#company-address-form').find('input[name="add4"]').val(address.add4);
            $('#company-address-form').find('input[name="locality"]').val(address.locality);
            $('#company-address-form').find('input[name="city"]').val(address.city);
            $('#company-address-form').find('input[name="county"]').val(address.county);
            $('#company-address-form').find('input[name="country"]').val(address.postcode_io.country);

        });
    }



</script>