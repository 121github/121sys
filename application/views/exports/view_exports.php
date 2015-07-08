
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Exports</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <form name="availableform" method="post" class="available-exports-filter-form" onsubmit="return export_data.onsubmitavailableform();">
                        <div class="panel panel-primary">
                            <div class="panel-heading"> <i class="fa fa-table fa-fw"></i> Available Exports
                                <div class="pull-right">
                                    <div class="btn-group">
                                        <input type="hidden" name="campaign" value="<?php echo $campaigns[0]['id']; ?>">
                                        <input type="hidden" name="campaign_name" value="<?php echo $campaigns[0]['name']; ?>">
                                        <input type="hidden" name="export_form_name">
                                        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span><?php echo $campaigns[0]['name']; ?></button>
                                        <ul class="dropdown-menu pull-right" role="menu">
                                            <?php foreach($campaigns as $row): ?>
                                                <li><a href="#" class="campaign-available-filter" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
                                            <?php endforeach ?>
                                            <li class="divider"></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- /.panel-heading -->
                            <div class="panel-body available-export-data">
                                <table class="table ajax-table">
                                    <thead>
                                    <tr>
                                        <th style="display: none"></th>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th></th>
                                        <th style="text-align: right">Options</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>Contacts Data</td>
                                        <td>Contacts Data by Campaign</td>
                                        <td colspan="2" style="text-align: right">
                                            <button title='Export to csv' type='submit' class='btn btn-default' onclick='document.pressed=this.value' value='contacts-data'><span class='glyphicon glyphicon-export pointer'></span></button>
                                            <span title='View the data before export' class='btn btn-default export-available-data-btn' item-name='contacts-data'><span class='glyphicon glyphicon-eye-open pointer'></span></span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.panel-body -->
                        </div>
                    </form>
                </div>
                <!-- /.row -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                   <form name="myform" method="post" class="filter-form" onsubmit="return export_data.onsubmitform();">
                        <div class="panel panel-primary">
                            <div class="panel-heading"> <i class="fa fa-table fa-fw"></i>Custom Exports
                                <div class="pull-right">
                                    <div class="btn-group">
                                        <input type="hidden" name="date_from">
                                        <input type="hidden" name="date_to">
                                        <input type="hidden" name="campaign">
                                        <input type="hidden" name="campaign_name">
                                        <input type="hidden" name="export_forms_id">

                                        <button type="button" class="daterange btn btn-default btn-xs"><span class="glyphicon glyphicon-calendar"></span> <span class="date-text"> <?php echo "2nd Jul - Today"; ?> </span></button>
                                    </div>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span> Campaign</button>
                                        <ul class="dropdown-menu pull-right" role="menu">
                                            <?php foreach($campaigns as $row): ?>
                                                <li><a href="#" class="campaign-filter" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
                                            <?php endforeach ?>
                                            <li class="divider"></li>
                                            <li><a class="campaign-filter" ref="#" style="color: green;">All Campaigns</a> </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- /.panel-heading -->
                            <div class="panel-body export-data">
                                <table class="table ajax-table">
                                    <thead>
                                        <tr>
                                            <th style="display: none"></th>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th></th>
                                            <th style="text-align: right">Options</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="3"><img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" /></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        <!-- /.panel-body -->
                        </div>
                    </form>
                </div>
            <!-- /.row -->
            </div>
            <div class="row" style="margin-bottom: 10px;">
                <div class="col-lg-12">
                    <?php if(in_array("edit export",$_SESSION['permissions'])): ?>
                        <button class="btn btn-success new-export-btn">New Export Form</button>
                    <?php endif ?>
                </div>
            </div>
            <div class="row custom-exports" style="display: none">
                <div class="col-lg-12">
                    <form name="edit-export-form" method="post" class="filter-form edit-export-form">
                        <div class="panel panel-primary">
                            <div class="panel-heading"> Custom Exports
                            </div>
                            <!-- /.panel-heading -->
                            <div class="panel-body">
                                <input type="hidden" name="export_forms_id">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="btn-group">
                                            <div class="form-group input-group-sm">
                                                <p>Name</p>
                                                <input type="text" class="form-control" name="name" placeholder="Enter the name" required />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="btn-group">
                                            <div class="form-group input-group-sm">
                                                <p>Description</p>
                                                <input type="text" class="form-control" name="description" placeholder="Enter the description" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="btn-group">
                                            <div class="form-group input-group-sm">
                                                <p>Header</p>
                                                <textarea rows="4" cols="115" class="form-control" name="header" placeholder="Enter the header separated by ;" required ></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="btn-group">
                                            <div class="form-group input-group-sm">
                                                <p>Query</p>
                                                <textarea class="form-control" cols="115" rows="3" name="query" placeholder="Enter the query" required ></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="btn-group">
                                            <div class="form-group input-group-sm">
                                                <p>Order By</p>
                                                <input type="text" class="form-control" name="order_by" placeholder="Enter the field if you need to order by" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="btn-group">
                                            <div class="form-group input-group-sm">
                                                <p>Group By</p>
                                                <input type="text" class="form-control" name="group_by" placeholder="Enter the field if you need to group by" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4"></div>
                                </div>
                                <div style="border-bottom: 1px solid grey; margin-bottom: 20px; font-weight: bold">FITLERS</div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="btn-group">
                                            <div class="form-group input-group-sm">
                                                <p>Date Filter</p>
                                                <input type="text" class="form-control" name="date_filter" placeholder="Enter the field if you need to filter by date" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="btn-group">
                                            <div class="form-group input-group-sm">
                                                <p>Campaign Filter</p>
                                                <input type="text" class="form-control" name="campaign_filter" placeholder="Enter the field if you need to filter by campaign" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4"></div>
                                </div>
                                <div style="border-bottom: 1px solid grey; margin-bottom: 20px; font-weight: bold">USERS</div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="btn-group">
                                            <div class="form-group input-group-sm">
                                                <p>Select the users that will recieve this data by email</p>
                                                <select class="selectpicker user_select" name="user_id[]" multiple>
                                                    <?php foreach($users as $user): ?>
                                                        <option value="<?php echo $user['id'] ?>"><?php echo $user['name'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4"></div>
                                </div>
                                <div class="pull-right">
                                    <span class="marl btn btn-default close-edit-btn">Cancel</span>
                                    <span class="marl btn btn-primary save-edit-btn">Save</span>
                                </div>

                            </div>
                            <!-- /.panel-body -->
                        </div>
                    </form>
                </div>
            <!-- /.row -->
            </div>
            <div class="export-report-container">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div style="font-size: 18px;"><span class="export-report-name"></span><span class="glyphicon glyphicon-remove pull-right close-export-report"></span></div>
                </div>
                <div class="panel-body" style="max-height:500px;overflow:auto">
                  <div id="export-report" class="table-responsive">
                            <table class="table table-striped table-responsive" id="export-report-table">
                                <thead></thead>
                                <tbody></tbody>
</table>
</div>
</div>
                </div>
</div>

    <script>
        $(document).ready(function () {
            export_data.init();
        });
    </script>

