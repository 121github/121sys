<ul class="nav nav-tabs" id="referral-tabs" style=" background:#eee; width:100%;">
    <li class="active"><a href="#general" class="tab" data-toggle="tab">General</a></li>
    <li class="tab-alert">You must create the referral before adding addresses</li>
    <li class="address-tab"><a href="#address" class="tab" data-toggle="tab">Addresses</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
    <div class="tab-pane active" id="general">
        <form class="form-horizontal" id="referral-form">
            <input name="urn" type="hidden" value="">
            <input name="referral_id" type="hidden" value="">
            <div class="form-group input-group-sm">
                <label small style="width: 30%">Title</label>
                <input style="width: 70%" type="text" class="form-control pull-right" placeholder="Title" name="title" value="">
            </div>
            <div class="form-group input-group-sm">
                <label small style="width: 30%">Firstname</label>
                <input style="width: 70%" type="text" class="form-control pull-right" placeholder="Firstname" name="firstname" value="">
            </div>
            <div class="form-group input-group-sm">
                <label small style="width: 30%">Lastname</label>
                <input style="width: 70%" type="text" class="form-control pull-right" placeholder="Lastname" name="lastname" value="">
            </div>
            <div class="form-group input-group-sm">
                <label small style="width: 30%">Email</label>
                <input style="width: 70%" type="text" class="form-control pull-right" placeholder="Email Address" name="email" value="">
            </div>
            <div class="form-group input-group-sm">
                <label small style="width: 30%">Telephone Number</label>
                <input style="width: 70%" type="text" class="form-control pull-right" placeholder="Telephone Number" name="telephone_number" value="">
            </div>
            <div class="form-group input-group-sm">
                <label small style="width: 30%">Mobile Number</label>
                <input style="width: 70%" type="text" class="form-control pull-right" placeholder="Mobile Number" name="mobile_number" value="">
            </div>
            <div class="form-group input-group-sm">
                <label small style="width: 30%">Other Number</label>
                <input style="width: 70%" type="text" class="form-control pull-right" placeholder="Other Number" name="other_number" value="">
            </div>
        </form>
    </div>

    <div class="tab-pane" id="address">
        <div class="table-container">
            <p class="pull-right"><a href="#" class="referral-add-item">Add another address</a>
            <div class="clearfix"></div>
            </p>
            <p class="none-found">There are no addresses linked to this referral</p>
            <table class="table">
                <thead>
                <th></th>
                <th>Description</th>
                <th>Add1</th>
                <th>Postcode</th>
                <th>Primary</th>
                <th>Options</th>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <form class="form-horizontal" id="referral-address-form">
            <input class="item-id" name="address_id" type="hidden" value="">
            <input name="referral_id" type="hidden" value="">
            <div class="row">
                <div class="col-md-9">
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-default dropdown-toggle btn-sm"
                                        data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">Description <span
                                        class="caret"></span>
                                </button>
                                <ul class="dropdown-menu description-list">
                                    <li><a href="#">Home</a></li>
                                    <li><a href="#">Work</a></li>
                                    <li><a href="#">Other</a></li>
                                </ul>
                            </div>
                            <!-- /btn-group -->
                            <input type="text" class="form-control input-sm" placeholder="Address description"
                                   name="description" value="">
                        </div>
                        <!-- /input-group -->
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <div class="btn-group pull-right">
                            <input name="visible" value="0" type="hidden"/>
                            <input data-width="100px" checked style="display:none" type="checkbox" id="visible-toggle"
                                   data-on="<i class='glyphicon glyphicon-eye-open'></i> Visible"
                                   data-off="<i class='glyphicon glyphicon-eye-close'></i> Hidden" data-toggle="toggle">
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Enter the address details <span class="glyphicon glyphicon-info-sign tt" data-toggle="tooltip"
                                                       title="Enter the postcode and house number below to auto populate the address fields"></span></label>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <input type="text" class="form-control input-sm" style="width:95%" placeholder="Postcode"
                               name="postcode" value="">
                    </div>
                </div>
                <?php if (in_array("get address", $_SESSION['permissions'])) { ?>
                    <div class="col-md-3">
                        <div class="form-group">
                            <input type="text" class="form-control input-sm" style="width:95%"
                                   placeholder="House number" name="house_number" value="">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">

                            <button class="btn btn-sm btn-info get-referral-address" style="width:95%">Find Address
                            </button>

                        </div>
                    </div>
                <?php } ?>
                <div class="col-md-3">
                    <div class="form-group">
                        <div class="btn-group pull-right">
                            <input name="primary" value="0" type="hidden"/>
                            <input data-width="100px" style="display:none" type="checkbox" id="primary-toggle"
                                   data-on="<i class='glyphicon glyphicon-home'></i> Primary"
                                   data-off="<i class='glyphicon glyphicon-home'></i> Primary" data-toggle="toggle">
                        </div>
                    </div>
                </div>

            </div>
            <div class="form-group input-group-sm address-select" style="display:none;">
                <select class="form-control selectpicker" placeholder="Address" name="address">
                </select>
            </div>
            <hr/>
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
    }).show().change(function () {
        if ($('#primary-toggle').prop('checked') == true) {
            $tab.find('form input[name="primary"]').val('1');
        } else {
            $tab.find('form input[name="primary"]').val('0');
        }
    });

    $('#visible-toggle').bootstrapToggle({
        onstyle: 'success',
    }).show().change(function () {
        if ($('#visible-toggle').prop('checked') == true) {
            $tab.find('form input[name="visible"]').val('1');
        } else {
            $tab.find('form input[name="visible"]').val('0');
        }
    });

    //Get address

    $modal.find("#referral-address-form .description-list li a").click(function () {
        $('#referral-address-form').find('input[name="description"]').val($(this).text());
    });

    var addresses;
    $modal.find('.address-select').hide();
    $modal.find('#referral-address-form').find('input[name="house_number"]').numeric();

    $modal.find('#referral-form').find('input[name="telephone_number"]').numeric();
    $modal.find('#referral-form').find('input[name="mobile_number"]').numeric();
    $modal.find('#referral-form').find('input[name="other_number"]').numeric();

    $modal.find('.get-referral-address').click(function (e) {
        e.preventDefault();
        get_addresses();
    });

    function get_addresses() {
        var postcode = $('#referral-address-form').find('input[name="postcode"]').val();
        var house_number = $('#referral-address-form').find('input[name="house_number"]').val();
        var description = $('#referral-address-form').find('input[name="description"]').val();

        $('#referral-address-form')[0].reset();
        $('#referral-address-form').find('input[name="postcode"]').val(postcode);
        $('#referral-address-form').find('input[name="house_number"]').val(house_number);
        $('#referral-address-form').find('input[name="description"]').val(description);
        $.ajax({
            url: helper.baseUrl + 'ajax/get_addresses_by_postcode',
            type: "POST",
            dataType: "JSON",
            data: {postcode: postcode, house_number: house_number}
        }).done(function (response) {
            if (response.success) {
                $modal.find('#referral-address-form').find('input[name="postcode"]').val(response.postcode);
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
                $('#referral-address-form').find('select[name="address"]')
                    .html(options)
                    .selectpicker('refresh');

                //If the house number is found set this option by default
                if (response.address_selected !== null && response.address_selected !== undefined) {
                    var address = addresses[response.address_selected];
                    $('#referral-address-form').find('input[name="add1"]').val(address.add1);
                    $('#referral-address-form').find('input[name="add2"]').val(address.add2);
                    $('#referral-address-form').find('input[name="add3"]').val(address.add3);
                    $('#referral-address-form').find('input[name="add4"]').val(address.add4);
                    $('#referral-address-form').find('input[name="locality"]').val(address.locality);
                    $('#referral-address-form').find('input[name="city"]').val(address.city);
                    $('#referral-address-form').find('input[name="county"]').val(address.county);
                    $('#referral-address-form').find('input[name="country"]').val(address.postcode_io.country);

                    $('#referral-address-form').find('select[name="address"]')
                        .val(response.address_selected)
                        .selectpicker('refresh');
                }

                modal_body.css('overflow', 'auto');
                $modal.find('.address-select').show();
            }
            else {
                modal_body.css('overflow', 'auto');
                $modal.find('.address-select').hide();
                flashalert.danger("No address found");
            }
        });

        $modal.find('.address-select .selectpicker').change(function () {

            var selectedId = $(this).val();
            var address = addresses[selectedId];
            $('#referral-address-form').find('input[name="add1"]').val(address.add1);
            $('#referral-address-form').find('input[name="add2"]').val(address.add2);
            $('#referral-address-form').find('input[name="add3"]').val(address.add3);
            $('#referral-address-form').find('input[name="add4"]').val(address.add4);
            $('#referral-address-form').find('input[name="locality"]').val(address.locality);
            $('#referral-address-form').find('input[name="city"]').val(address.city);
            $('#referral-address-form').find('input[name="county"]').val(address.county);
            $('#referral-address-form').find('input[name="country"]').val(address.postcode_io.country);

        });
    }


</script>