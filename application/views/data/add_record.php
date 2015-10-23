<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Add Record <span id="campaign-name-title" class="small"></span> <span class="small"
                                                                                                      id="campaign-type-title"></span>
        </h1>
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
                        <form class="record-form" id="record-form">
                            <div class="form-group input-group-sm" <?php if (count($campaigns) == "1") {
                                echo "style='display:none'";
                            } ?> >
                                <p><label>Campaign</label></p>
                                <select name="campaign_id" class="selectpicker" id="campaign">
                                    <option value="">Select the campaign</option>
                                    <?php foreach ($campaigns as $row) { ?>
                                        <option <?php if (isset($_SESSION['current_campaign']) && $_SESSION['current_campaign'] == $row['id']) {
                                            echo "selected";
                                        } ?> ctype="<?php echo $row['type'] ?>" value="<?php echo $row['id'] ?>">
                                            <?php echo $row['name'] ?>
                                        </option>
                                    <?php } ?>
                                </select>

                                <p id="ctype-text" class="green" style="display:none"></p>
                            </div>

                            <div id="company" style="display: none;">
                                <div class="form-group input-group-sm">
                                    <p><label>Company Name</label></p>
                                    <input type="text" name="company_name" id="company_name" class="form-control"
                                           placeholder="Enter the name of the company"/>
                                </div>

                                <p>
                                    <label>Postcode</label>
                                    <?php if (in_array("get address", $_SESSION['permissions'])) { ?>
                                    <span class="btn btn-xs btn-success get-company-address">Get Address</span>

                                <div class="form-group input-group-sm company-address-select" style="display:none;">
                                    <select class="form-control selectpicker" placeholder="Address"
                                            name="company_address"></select>
                                </div>
                                <?php } ?>
                                </p>

                                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                    <div class="panel panel-default">
                                        <div class="panel-heading" role="tab" id="headingOne">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group input-group-sm">
                                                        <input type="text" name="company_postcode" id="company_postcode"
                                                               class="form-control" placeholder="Postcode"/>
                                                    </div>
                                                </div>
                                                <?php if (in_array("get address", $_SESSION['permissions'])) { ?>
                                                    <div class="col-md-6">
                                                        <div class="form-group input-group-sm">
                                                            <input type="text" class="form-control"
                                                                   placeholder="House number"
                                                                   name="company_house_number"
                                                                   value="">
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <a role="button" data-toggle="collapse" data-parent="#accordion"
                                               href="#collapseCompany" aria-expanded="true" aria-controls="collapseOne">
                                                See complete address...
                                            </a>

                                            <div id="collapseCompany" class="panel-collapse collapse">
                                                <div class="form-group input-group-sm">
                                                    <input type="text" name="company_add1" id="company_add1"
                                                           class="form-control"
                                                           placeholder="First line of address"/>
                                                </div>
                                                <div class="form-group input-group-sm">
                                                    <input type="text" name="company_add2" id="company_add2"
                                                           class="form-control"
                                                           placeholder="Second line of address"/>
                                                </div>
                                                <div class="form-group input-group-sm">
                                                    <input type="text" name="company_add3" id="company_add3"
                                                           class="form-control"
                                                           placeholder="Third line of address"/>
                                                </div>
                                                <div class="form-group input-group-sm">
                                                    <input type="text" name="company_add4" id="company_add4"
                                                           class="form-control"
                                                           placeholder="Fourth line of address"/>
                                                </div>
                                                <div class="form-group input-group-sm">
                                                    <input type="text" name="company_locality" id="company_locality"
                                                           class="form-control"
                                                           placeholder="Locality"/>
                                                </div>
                                                <div class="form-group input-group-sm">
                                                    <input type="text" name="company_city" id="company_city"
                                                           class="form-control"
                                                           placeholder="Town/City"/>
                                                </div>
                                                <div class="form-group input-group-sm">
                                                    <input type="text" name="company_county" id="company_county"
                                                           class="form-control"
                                                           placeholder="County"/>
                                                </div>
                                                <div class="form-group input-group-sm">
                                                    <input type="text" name="company_country" id="company_country"
                                                           class="form-control"
                                                           placeholder="Country"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group input-group-sm">
                                    <p><label>Telephone (optional)</label></p>
                                    <input type="text" name="company_telephone" id="company_telephone"
                                           class="form-control" placeholder="Enter the telephone"/>
                                </div>
                            </div>

                            <div id="contact" style="display: none;">
                                <div class="form-group input-group-sm">
                                    <p><label>Contact Name</label></p>
                                    <input type="text" name="contact_name" id="contact_name" class="form-control"
                                           placeholder="Enter the name of the contact"/>
                                </div>

                                <p>
                                    <label>Address</label>
                                    <?php if (in_array("get address", $_SESSION['permissions'])) { ?>
                                    <span class="btn btn-xs btn-success get-contact-address">Get Address</span>

                                <div class="form-group input-group-sm contact-address-select" style="display:none;">
                                    <select class="form-control selectpicker" placeholder="Address"
                                            name="contact_address"></select>
                                </div>
                                <?php } ?>
                                </p>
                                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                    <div class="panel panel-default">
                                        <div class="panel-heading" role="tab" id="headingOne">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group input-group-sm">
                                                        <input type="text" name="contact_postcode" id="contact_postcode"
                                                               class="form-control" placeholder="Postcode"/>
                                                    </div>
                                                </div>
                                                <?php if (in_array("get address", $_SESSION['permissions'])) { ?>
                                                    <div class="col-md-6">
                                                        <div class="form-group input-group-sm">
                                                            <input type="text" class="form-control"
                                                                   placeholder="House no."
                                                                   name="contact_house_number"
                                                                   value="">
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <a role="button" data-toggle="collapse" data-parent="#accordion"
                                               href="#collapseContact" aria-expanded="true" aria-controls="collapseOne">
                                                See complete address...
                                            </a>

                                            <div id="collapseContact" class="panel-collapse collapse">
                                                <div class="form-group input-group-sm">
                                                    <input type="text" name="contact_add1" id="contact_add1"
                                                           class="form-control"
                                                           placeholder="First line of address"/>
                                                </div>
                                                <div class="form-group input-group-sm">
                                                    <input type="text" name="contact_add2" id="contact_add2"
                                                           class="form-control"
                                                           placeholder="Second line of address"/>
                                                </div>
                                                <div class="form-group input-group-sm">
                                                    <input type="text" name="contact_add3" id="contact_add3"
                                                           class="form-control"
                                                           placeholder="Third line of address"/>
                                                </div>
                                                <div class="form-group input-group-sm">
                                                    <input type="text" name="contact_add4" id="contact_add4"
                                                           class="form-control"
                                                           placeholder="Fourth line of address"/>
                                                </div>
                                                <div class="form-group input-group-sm">
                                                    <input type="text" name="contact_locality" id="contact_locality"
                                                           class="form-control"
                                                           placeholder="Locality"/>
                                                </div>
                                                <div class="form-group input-group-sm">
                                                    <input type="text" name="contact_city" id="contact_city"
                                                           class="form-control"
                                                           placeholder="Town/City"/>
                                                </div>
                                                <div class="form-group input-group-sm">
                                                    <input type="text" name="contact_county" id="contact_county"
                                                           class="form-control"
                                                           placeholder="County"/>
                                                </div>
                                                <div class="form-group input-group-sm">
                                                    <input type="text" name="contact_country" id="contact_country"
                                                           class="form-control"
                                                           placeholder="Country"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group input-group-sm">
                                    <p><label>Telephone (optional)</label></p>
                                    <input type="text" name="contact_telephone" id="contact_telephone"
                                           class="form-control" placeholder="Enter the telephone"/>
                                </div>
                            </div>


                            <!-- SUBMIT AND CANCEL BUTTONS -->
                            <div class="form-actions pull-right">
                                <button type="submit" class="btn btn-info marl" id="continue-btn">Check Data</button>
                            </div>
                            <div class="form-actions pull-right">
                                <button type="submit" class="btn btn-primary marl" id="save-btn" style="display:none">
                                    Create New
                                </button>
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
    $(document).ready(function () {
        add_record.init();
    });
</script>