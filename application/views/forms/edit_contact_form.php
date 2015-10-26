<ul class="nav nav-tabs" style=" background:#eee; width:100%;">
    <li class="active"><a href="#general" class="tab" data-toggle="tab">General</a></li>
    <li class="tab-alert">You must create the contact before adding phone numbers</li>
    <li class="phone-tab"><a href="#phone" class="tab" data-toggle="tab">Phone Numbers</a></li>
    <li class="address-tab"><a href="#address" class="tab" data-toggle="tab">Addresses</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
    <div class="tab-pane active" id="general">
        <form class="form-horizontal">
            <input name="urn" type="hidden" value="">
            <input name="contact_id" type="hidden" value="">
            <?php if ($_SESSION['config']['use_fullname']): ?>
                <div class="form-group input-group-sm">
                    <label class="col-sm-2 control-label">Fullname</label>

                    <div class="col-sm-10"><input type="text" class="form-control" placeholder="Full name"
                                                  name="fullname" value="">
                    </div>
                </div>
            <?php else: ?>
                <div class="form-group input-group-sm">
                    <input type="text" class="form-control" placeholder="Title" name="title" value="">
                </div>
                <div class="form-group input-group-sm">
                    <input type="text" class="form-control" placeholder="Firstname" name="firstname" value="">
                </div>
                <div class="form-group input-group-sm">
                    <input type="text" class="form-control" placeholder="Lastname" name="lastname" value="">
                </div>
            <?php endif ?>
            <div class="form-group input-group-sm">
                <label class="col-sm-2 control-label">Position</label>

                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="Job title" name="position" value="">
                </div>
            </div>
            <div class='form-group input-group-sm' data-picktime="false">
                <label class="col-sm-2 control-label">DOB</label>

                <div class="col-sm-10">
                    <input name="dob" placeholder="Date of birth" type='text' class="form-control dateyears" value=""/>
                </div>
            </div>
            <div class="form-group input-group-sm">
                <label class="col-sm-2 control-label">Email</label>

                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="Email Address" name="email" value="">
                </div>
            </div>
            <div class="form-group input-group-sm">
                <label class="col-sm-2 control-label">Website</label>

                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="Website address" name="website" value="">
                </div>
            </div>
            <div class="form-group input-group-sm">
                <label class="col-sm-2 control-label">Linkedin</label>

                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="Linkedin profile page or ID" name="linkedin"
                           value="">
                </div>
            </div>
            <div class="form-group input-group-sm">
                <label class="col-sm-2 control-label">Facebook</label>

                <div class="col-sm-10">
                    <input type="text" class="form-control" placeholder="Facebook profile page" name="facebook"
                           value="">
                </div>
            </div>
            <div class="form-group input-group-sm">
                <label class="col-sm-2 control-label">Notes</label>

                <div class="col-sm-10">
                    <textarea class="form-control" name="notes" placeholder="Enter contact notes here"
                              style="height:5em"></textarea>
                </div>
            </div>

        </form>
    </div>
    <div class="tab-pane" id="phone">
        <div class="table-container">
            <p class="pull-right"><a href="#" class="contact-add-item">Add phone number</a>

            <div class="clearfix"></div>
            </p>
            <p class="none-found">There are no numbers linked to this contact</p>
            <table class="table">
                <thead>
                <th>Description</th>
                <th>Number</th>
                <th>TPS</th>
                <th>Options</th>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <form class="form-horizontal contact-phone-form">
            <input name="contact_id" type="hidden" value="">
            <input class="item-id" name="telephone_id" type="hidden" value="">

            <p>Enter the phone number details</p>

            <div class="row">
                <div class="col-md-4">
                    <div class="input-append form-group btn-group">
                        <div class="input-group-sm">
                            <input type="text" class="form-control" placeholder="Description. Eg: Mobile"
                                   name="description"
                                   value="">
                        </div>
                        <a class="btn btn-default dropdown-toggle input-group-addon" data-toggle="dropdown" href="#">
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu type-selection">
                            <li><a href="#">Home</a></li>
                            <li><a href="#">Work</a></li>
                            <li><a href="#">Mobile</a></li>
                            <li><a href="#">Main</a></li>
                            <li><a href="#">Home Fax</a></li>
                            <li><a href="#">Work Fax</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group input-group-sm">
                        <input type="text" class="form-control" placeholder="Phone number" name="telephone_number"
                               value="" onchange="changeContactNumberFunction()">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group input-group-sm col-md-11">
                    <select class="form-control selectpicker" placeholder="Is this number TPS registered?" name="tps" onchange="changeTpsFunction()">
                        <option value="">Is this number TPS registered</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                        <option value="">Don't know</option>
                    </select>
                </div>
                <div class="col-md-1 edit-tps"></div>
            </div>
        </form>
    </div>
    <div class="tab-pane" id="address">
        <div class="table-container">
            <p class="pull-right"><a href="#" class="contact-add-item">Add another address</a>

            <div class="clearfix"></div>
            </p>
            <p class="none-found">There are no addresses linked to this contact</p>
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
        <form class="form-horizontal contact-address-form">
            <input class="item-id" name="address_id" type="hidden" value="">
            <input name="contact_id" type="hidden" value="">

            <p>Enter the address details</p>

            <div class="form-group input-group-sm">
                <select class="form-control selectpicker" placeholder="Is this the primary address?" name="primary">
                    <option value="1">Primary Address</option>
                    <option value="0">Secondary Address</option>
                </select>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group input-group-sm">
                        <input type="text" class="form-control" placeholder="Postcode" name="postcode" value="">
                    </div>
                </div>
                <?php if (in_array("get address", $_SESSION['permissions'])) { ?>
                    <div class="col-md-1"></div>
                    <div class="col-md-3">
                        <div class="form-group input-group-sm">
                            <input type="text" class="form-control" placeholder="House number" name="house_number"
                                   value="">
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                    <div class="col-md-2">
                        <div class="form-group input-group-sm col-md-4">
                            <span class="btn btn-sm btn-success get-contact-address">Get Address</span>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="form-group input-group-sm address-select" style="display:none;">
                <select class="form-control selectpicker" placeholder="Address" name="address"></select>
            </div>
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
    function changeContactNumberFunction() {
        var contact_id = $('.contact-phone-form').find('input[name="contact_id"]').val();
        var telephone_number = $('.contact-phone-form').find('input[name="telephone_number"]').val();
        var telephone_id = $('.contact-phone-form').find('input[name="telephone_id"]').val();
        var tps = "";
        if (telephone_number.length > 0) {
            tps = "<span class='glyphicon glyphicon-question-sign black edit-tps-btn tt pointer' item-contact-id='" + contact_id + "' item-number-id='" + telephone_id + "' item-number='" + telephone_number + "' data-toggle='tooltip' data-placement='right' title='TPS Status is unknown. Click to check it'></span>";
            $('select[name="tps"]').selectpicker('val', "");
        }
        $('.edit-tps').html(tps);
    }

    function changeTpsFunction() {
        var contact_id = $('.contact-phone-form').find('input[name="contact_id"]').val();
        var telephone_number = $('.contact-phone-form').find('input[name="telephone_number"]').val();
        var telephone_id = $('.contact-phone-form').find('input[name="telephone_id"]').val();
        var tps_option = $('.contact-phone-form').find('select[name="tps"]').val();
        var tps = "";
        if (telephone_number.length > 0) {
            if (tps_option.length == 0) {
                tps = "<span class='glyphicon glyphicon-question-sign black edit-tps-btn tt pointer' item-contact-id='" + contact_id + "' item-number-id='" + telephone_id + "' item-number='" + telephone_number + "' data-toggle='tooltip' data-placement='right' title='TPS Status is unknown. Click to check it'></span>";
            }
            else if (tps_option == 1) {
                tps = "<span class='glyphicon glyphicon-exclamation-sign red tt' data-toggle='tooltip' data-placement='right' title='This number IS TPS registered'></span>";
            }
            else {
                tps = "<span class='glyphicon glyphicon-ok-sign green tt' data-toggle='tooltip' data-placement='right' title='This number is NOT TPS registerd'></span>";
            }
            $tab.find('.edit-tps').html(tps);
        }
    }

    $(document).on('click', '.edit-tps-btn', function (e) {
        e.preventDefault();
        check_edit_tps();
    });

    function check_edit_tps() {
        var contact_id = $('.contact-phone-form').find('input[name="contact_id"]').val();
        var telephone_number = $('.contact-phone-form').find('input[name="telephone_number"]').val();
        var telephone_id = $('.contact-phone-form').find('input[name="telephone_id"]').val();
        var tps = '';

        $.ajax({
            url: helper.baseUrl + 'cron/check_tps',
            type: "POST",
            dataType: "JSON",
            data: {
                telephone_number: telephone_number,
                type: "tps",
                contact_id: contact_id
            }
        }).done(function (response) {
            flashalert.warning(response.msg);
            if (response.tps == 1) {
                tps = "<span class='glyphicon glyphicon-question-sign black edit-tps-btn tt pointer' item-contact-id='" + contact_id + "' item-number-id='" + telephone_id + "' item-number='" + telephone_number + "' data-toggle='tooltip' data-placement='right' title='TPS Status is unknown. Click to check it'></span>";
                $('.contact-phone-form').find('select[name="tps"]').selectpicker('val', 1);
                $tab.find('.edit-tps').html(tps);
            }
            else if (response.tps == 0) {
                tps = "<span class='glyphicon glyphicon-ok-sign green tt' data-toggle='tooltip' data-placement='right' title='This number is NOT TPS registerd'></span>";
                $('.contact-phone-form').find('select[name="tps"]').selectpicker('val', 0);
                $tab.find('.edit-tps').html(tps);
            }
        });
    }

    $(".dropdown-menu li a").click(function () {
        $('.contact-phone-form').find('input[name="description"]').val($(this).text());
    });

    //Get address

    var addresses;
    $('.address-select').hide();
    $('.contact-address-form').find('input[name="house_number"]').numeric();

    $(document).on('click', '.get-contact-address', function (e) {
        e.preventDefault();
        get_addresses();
    });

    function get_addresses() {
        var postcode = $('.contact-address-form').find('input[name="postcode"]').val();
        var house_number = $('.contact-address-form').find('input[name="house_number"]').val();
        $('.contact-address-form')[0].reset();
        $('.contact-address-form').find('input[name="postcode"]').val(postcode);
        $('.contact-address-form').find('input[name="house_number"]').val(house_number);
        $.ajax({
            url: helper.baseUrl + 'ajax/get_addresses_by_postcode',
            type: "POST",
            dataType: "JSON",
            data: {postcode: postcode, house_number: house_number}
        }).done(function (response) {
            if (response.success) {
                $('.contact-address-form').find('input[name="postcode"]').val(response.postcode);
                addresses = response.data;
                flashalert.warning("Addresses found, select one");
                var options = "<option value=''>Select one address...</option>";

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
                $('.contact-address-form').find('select[name="address"]')
                    .html(options)
                    .selectpicker('refresh');

                //If the house number is found set this option by default
                if (response.address_selected !== null && response.address_selected !== undefined) {
                    var address = addresses[response.address_selected];
                    $('.contact-address-form').find('input[name="add1"]').val(address.add1);
                    $('.contact-address-form').find('input[name="add2"]').val(address.add2);
                    $('.contact-address-form').find('input[name="add3"]').val(address.add3);
                    $('.contact-address-form').find('input[name="add4"]').val(address.add4);
                    $('.contact-address-form').find('input[name="locality"]').val(address.locality);
                    $('.contact-address-form').find('input[name="city"]').val(address.city);
                    $('.contact-address-form').find('input[name="county"]').val(address.county);
                    $('.contact-address-form').find('input[name="country"]').val(address.postcode_io.country);

                    $('.contact-address-form').find('select[name="address"]')
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
            $('.contact-address-form').find('input[name="add1"]').val(address.add1);
            $('.contact-address-form').find('input[name="add2"]').val(address.add2);
            $('.contact-address-form').find('input[name="add3"]').val(address.add3);
            $('.contact-address-form').find('input[name="add4"]').val(address.add4);
            $('.contact-address-form').find('input[name="locality"]').val(address.locality);
            $('.contact-address-form').find('input[name="city"]').val(address.city);
            $('.contact-address-form').find('input[name="county"]').val(address.county);
            $('.contact-address-form').find('input[name="country"]').val(address.postcode_io.country);

        });
    }


</script>