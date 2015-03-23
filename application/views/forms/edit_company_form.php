<ul class="nav nav-tabs" style=" background:#eee; width:100%;">
    <li class="active"><a href="#cogeneral" class="tab" data-toggle="tab">General</a></li>
    <li class="tab-alert">You must create the company before adding phone numbers</li>
    <li class="phone-tab"><a href="#cophone" class="tab" data-toggle="tab">Phone Numbers</a></li>
    <li class="address-tab"><a href="#coaddress" class="tab" data-toggle="tab">Addresses</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
    <div class="tab-pane active" id="cogeneral">
        <form class="form-horizontal">
            <input name="urn" type="hidden" value="<?php echo $urn ?>">
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
                    <input name="date_of_creation" placeholder="Date of Creation" type='text' class="form-control date" value=""/>
                </div>
            </div>
            <div class="form-actions pull-right">
                <span class="alert-success hidden">Company details saved</span>
                <button class="btn btn-primary save-company-general">Save changes</button>
                <button class="btn btn-default close-company-btn">Close</button>
            </div>
        </form>
    </div>
    <div class="tab-pane" id="cophone">
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
        <form class="form-horizontal">
            <input name="company_id" type="hidden" value="">
            <input class="item-id" name="telephone_id" type="hidden" value="">

            <p>Enter the phone number details</p>

            <div class="form-group input-group-sm">
                <input type="text" class="form-control" placeholder="Description. Eg: Reception" name="description"
                       value="">
            </div>
            <div class="form-group input-group-sm">
                <input type="text" class="form-control" placeholder="Phone number" name="telephone_number" value="">
            </div>
            <div class="form-group input-group-sm">
                <select class="form-control selectpicker" placeholder="Is this number CTPS registered?" name="ctps">

                    <option value="">Is this number CTPS registered</option>
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                    <option value="">Don't know</option>
                </select>
            </div>
            <div class="form-actions pull-right">
                <button type="submit" class="btn btn-primary save-company-phone" action="add_cophone">Add number
                </button>
                <button class="btn btn-default close-company-btn">Close</button>
            </div>
        </form>
    </div>
    <div class="tab-pane" id="coaddress">
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
        <form class="form-horizontal">
            <input class="item-id" name="address_id" type="hidden" value="">
            <input name="company_id" type="hidden" value="">

            <p>Enter the address details</p>

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
                <input type="text" class="form-control" placeholder="County" name="county" value="">
            </div>
            <div class="form-group input-group-sm">
                <input type="text" class="form-control" placeholder="Country" name="country" value="">
            </div>
            <div class="form-group input-group-sm">
                <input type="text" class="form-control" placeholder="Postcode" name="postcode" value="">
            </div>
            <div class="form-group input-group-sm">
                <select class="form-control selectpicker" placeholder="Is this the primary address?" name="primary">
                    <option value="">Is this the primary address?</option>
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
            <div class="form-actions pull-right">
                <button type="submit" class="btn btn-primary save-company-address" action="add_coaddress">Add Address
                </button>
                <button class="btn btn-default close-company-btn">Close</button>
            </div>
        </form>
    </div>
</div>
