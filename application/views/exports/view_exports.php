<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Exports</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>

<nav id="filter-right" class="mm-menu mm--horizontal mm-offcanvas">
    <div style="padding:30px 20px 3px">
        <form class="filter-form" method="post">
            <input type="hidden" name="date_from" value="<?php echo "2014-02-07" ?>">
            <input type="hidden" name="date_to" value="<?php echo date('Y-m-d') ?>">
            <input type="hidden" name="export_forms_id">
            <input type="hidden" name="export_form_name">

            <div style="margin-bottom: 5%;">
                <button type="button" class="daterange btn btn-default" data-width="100%">
                    <span class="glyphicon glyphicon-calendar"></span>
                    <span class="date-text"> <?php echo "Any Time"; ?> </span>
                </button>
            </div>

            <label style="margin-top: 5%;">Campaign</label>
            <select name="campaigns[]" class="selectpicker campaign-filter" multiple data-width="100%"
                    data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                <?php foreach ($campaigns_by_group as $type => $data) { ?>
                    <optgroup label="<?php echo $type ?>">
                        <?php foreach ($data as $row) { ?>
                            <option <?php if (isset($_SESSION['current_campaign']) && $row['id'] == $_SESSION['current_campaign']) {
                                echo "Selected";
                            } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                        <?php } ?>
                    </optgroup>
                <?php } ?>
            </select>

            <?php if (count($campaign_outcomes) > 0) { ?>
                <label style="margin-top: 5%;">Outcome</label>
                <select name="outcomes[]" class="selectpicker outcome-filter" id="outcome-filter" multiple
                        data-width="100%" data-live-search="true" data-live-search-placeholder="Search"
                        data-actions-box="true">
                    <?php foreach ($campaign_outcomes as $type => $data) { ?>
                        <optgroup label="<?php echo $type ?>">
                            <?php foreach ($data as $row) { ?>
                                <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                            <?php } ?>
                        </optgroup>
                    <?php } ?>
                </select>
            <?php } ?>

            <?php if (in_array("by team", $_SESSION['permissions'])) { ?>
                <label style="margin-top: 5%;">Team</label>
                <select name="teams[]" class="selectpicker team-filter" multiple data-width="100%"
                        data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                    <?php foreach ($team_managers as $row) { ?>
                        <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                    <?php } ?>
                </select>
            <?php } ?>

            <?php if (in_array("by agent", $_SESSION['permissions'])) { ?>
                <label style="margin-top: 5%;">Agent</label>
                <select name="agents[]" class="selectpicker agent-filter" multiple data-width="100%"
                        data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                    <?php foreach ($agents as $row) { ?>
                        <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                    <?php } ?>
                </select>
            <?php } ?>

            <label style="margin-top: 5%;">Source</label>
            <select name="sources[]" class="selectpicker source-filter" multiple data-width="100%"
                    data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                <?php foreach ($sources as $row) { ?>
                    <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                <?php } ?>
            </select>

            <label style="margin-top: 5%;">Data Pot</label>
            <select name="sources[]" class="selectpicker pot-filter" multiple data-width="100%"
                    data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                <?php foreach ($pots as $row) { ?>
                    <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                <?php } ?>
            </select>

            <button id="filter-submit" class="btn btn-success pull-right" style="margin-top: 5%;">Select</button>
        </form>
    </div>
</nav>

<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading"><i class="fa fa-table fa-fw"></i> Available Exports
                <div class="pull-right" style="border:0px solid black;">
                    <a href="#filter-right" class="btn btn-default btn-xs">
                        <span class="glyphicon glyphicon-filter" style="padding-left:3px; color:black;"></span> Filter
                    </a>
                </div>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body available-export-data">
                <div class="row">
                    <div class="col-lg-8">
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
                                <td class="report-available-export-prog-<?php echo "contacts-data"; ?>">
                                <td style="text-align: right">
                                    <div class="btn-group">
                                        <button title='Export to csv' type='submit' class='btn btn-default btn-sm export-available-file-btn'
                                                item-name='contacts-data'><span
                                                class='glyphicon glyphicon-export pointer'></span></button>
                                        <span title='View the data before export'
                                          class='btn btn-default btn-sm export-available-data-btn' item-name='contacts-data'><span
                                            class='glyphicon glyphicon-eye-open pointer'></span></span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>Combo Data</td>
                                <td>Hours + Positive outcomes</td>
                                <td class="report-available-export-prog-<?php echo "combo-data"; ?>">
                                <td style="text-align: right">
                                    <div class="btn-group">
                                        <button title='Export to csv' type='submit' class='btn btn-default btn-sm export-available-file-btn'
                                                item-name='combo-data'><span
                                                class='glyphicon glyphicon-export pointer'></span></button>
                                        <span title='View the data before export'
                                              class='btn btn-default btn-sm export-available-data-btn' item-name='combo-data'><span
                                                class='glyphicon glyphicon-eye-open pointer'></span></span>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-4">
                        <ul class="nav nav-tabs" style=" background:#eee; width:100%;">
                            <li class="filters-tab active"><a href="#filters-custom" class="tab" data-toggle="tab">Filters</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="filters-custom"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.panel-body -->
        </div>
    </div>
    <!-- /.row -->
</div>
<!-- /.row -->

<div class="row" style="margin-bottom: 10px;">
    <div class="col-lg-12">
        <?php if (in_array("edit export", $_SESSION['permissions'])): ?>
            <button class="btn btn-success new-export-btn">New Export Form</button>
        <?php endif ?>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <form name="myform" method="post" class="search-form" onsubmit="return export_data.onsubmitform();">
            <div class="panel panel-primary">
                <div class="panel-heading"><i class="fa fa-table fa-fw"></i>Custom Exports
                    <div class="pull-right" style="border:0px solid black;">
                        <a href="#filter-right" class="btn btn-default btn-xs">
                            <span class="glyphicon glyphicon-filter" style="padding-left:3px; color:black;"></span> Filter
                        </a>
                    </div>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body export-data">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="input-group"><span class="input-group-addon"><span
                                        class="glyphicon glyphicon-search"></span> Filter</span>
                                <input id="filter" type="text" class="form-control" placeholder="Type here...">
                            </div>
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
                                <tbody class="searchable">
                                <tr>
                                    <td colspan="3"><img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif"/></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-lg-4">
                            <ul class="nav nav-tabs" style=" background:#eee; width:100%;">
                                <li class="filters-tab active"><a href="#filters" class="tab" data-toggle="tab">Filters</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="filters"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
        </form>
    </div>
    <!-- /.row -->
</div>
<div class="row" style="margin-bottom: 10px;">
    <div class="col-lg-12">
        <?php if (in_array("edit export", $_SESSION['permissions'])): ?>
            <button class="btn btn-success new-export-btn">New Export Form</button>
        <?php endif ?>
    </div>
</div>

<script>
    $(document).ready(function () {
        export_data.init();
    });
</script>

