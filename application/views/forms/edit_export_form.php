<div class="custom-exports">
    <form name="edit-export-form" method="post" class="export-form edit-export-form">
        <input type="hidden" name="export_forms_id">

        <div class="row">
            <div class="col-lg-6">
                <div class="btn-group">
                    <div class="form-group input-group-sm">
                        <p>Name</p>
                        <input type="text" class="form-control" name="name" placeholder="Enter the name"
                               required/>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="btn-group">
                    <div class="form-group input-group-sm">
                        <p>Description</p>
                        <input type="text" class="form-control" name="description"
                               placeholder="Enter the description"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="btn-group">
                    <div class="form-group input-group-sm">
                        <p>Header</p>
                        <textarea rows="4" cols="115" class="form-control" name="header"
                                  placeholder="Enter the header separated by ;" required></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="btn-group">
                    <div class="form-group input-group-sm">
                        <p>Query</p>
                        <textarea class="form-control" cols="115" rows="3" name="query"
                                  placeholder="Enter the query" required></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4">
                <div class="btn-group">
                    <div class="form-group input-group-sm">
                        <p>Order By</p>
                        <input type="text" class="form-control" name="order_by"
                               placeholder="Enter the field if you need to order by"/>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="btn-group">
                    <div class="form-group input-group-sm">
                        <p>Group By</p>
                        <input type="text" class="form-control" name="group_by"
                               placeholder="Enter the field if you need to group by"/>
                    </div>
                </div>
            </div>
            <div class="col-lg-4"></div>
        </div>
        <div style="border-bottom: 1px solid grey; margin-bottom: 20px; font-weight: bold">FITLERS</div>
        <div class="row">
            <div class="col-lg-3">

                <div class="form-group input-group-sm">
                    <p>Date Filter</p>
                    <input type="text" class="form-control" name="date_filter"
                           placeholder="Enter the field if you need to filter by date"/>
                </div>

            </div>
            <div class="col-lg-3">

                <div class="form-group input-group-sm">
                    <p>Campaign Filter</p>
                    <input type="text" class="form-control" name="campaign_filter"
                           placeholder="Enter the field if you need to filter by campaign. Eg. campaign_id"/>

                </div>
            </div>
            <div class="col-lg-3">

                <div class="form-group input-group-sm">
                    <p>Source Filter</p>
                    <input type="text" class="form-control" name="source_filter"
                           placeholder="Enter the field if you need to filter by source. Eg. source_id"/>

                </div>
            </div>
            <div class="col-lg-3">

                <div class="form-group input-group-sm">
                    <p>Pot Filter</p>
                    <input type="text" class="form-control" name="pot_filter"
                           placeholder="Enter the field if you need to filter by pot. Eg. pot_id"/>

                </div>
            </div>
        </div>
        <div style="border-bottom: 1px solid grey; margin-bottom: 20px; font-weight: bold">USERS</div>
        <div class="row">
            <div class="col-lg-12">
                <div class="btn-group">
                    <div class="form-group input-group-sm">
                        <p>Select the users that will recieve this data by email</p>
                        <select id="user_select" class="selectpicker user_select" data-size="7" name="user_id[]" multiple></select>
                    </div>
                </div>
            </div>
            <div class="col-lg-4"></div>
        </div>
        <br />
    </form>
</div>