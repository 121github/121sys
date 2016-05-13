<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Campaign Admin</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">

        <div class="panel panel-primary campaign-panel">
            <div class="panel-heading">Campaigns
                <div class="pull-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-xs dropdown-toggle add-btn"
                                data-toggle="dropdown"> Add Campaign
                        </button>
                    </div>
                </div>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <?php $this->view('forms/edit_campaign_form.php', $options); ?>
                <table class="table ajax-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Client</th>
                        <th>Status</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Options</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td colspan="8"><img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif"/></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <!-- /.panel-body -->
        </div>


        <div class="panel panel-primary campaign-access">
            <div class="panel-heading">Campaign User Access
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <table class="table">
                    <tr>
                        <td colspan="4">
                            <select id="campaign-select-options" class="selectpicker campaign-select">
                                <option value="">Select a campaign</option>
                                <?php foreach ($options['campaigns'] as $row) { ?>
                                    <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Group Filter
                        </td>
                        <td>
                            Users
                        </td>
                        <td></td>
                        <td>Campaign Access</td>
                    </tr>
                    <tr>
                        <td><select disabled style="height:200px;" class="form-control group-select" size="20">
                                <?php foreach ($options['groups'] as $row) { ?>
                                    <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                                <?php } ?>
                            </select></td>
                        <td><select disabled multiple style="height:200px" class="form-control user-select">
                                <option value="">Select a group first</option>
                            </select></td>
                        <td width="110">
                            <div class="btn-group-vertical">
                                <button type="button" disabled class="btn btn-default access-add">Add <span
                                        class="glyphicon glyphicon-chevron-right"></span></button>
                                <br>
                                <button type="button" disabled class="btn btn-default access-del"><span
                                        class="glyphicon glyphicon-chevron-left"></span> Remove
                                </button>
                            </div>
                        </td>
                        <td><select disabled style="height:200px" multiple class="form-control access-select">
                            </select></td>
                    </tr>
                </table>

            </div>
        </div>
        <div class="panel panel-primary campaign-access">
            <div class="panel-heading">Campaign Outcomes
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <table class="table">
                    <tr>
                        <td colspan="4">
                            Use the form below to setup the available campaign outcome options
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Campaign
                        </td>
                        <td>
                            Outcomes
                        </td>
                        <td></td>
                        <td>Campaign outcomes</td>
                    </tr>
                    <tr>
                        <td><select style="height:200px;" class="form-control campaignlist-select" size="20">
                                <?php foreach ($options['campaigns'] as $row) { ?>
                                    <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                                <?php } ?>
                            </select></td>
                        <td><select disabled multiple style="height:200px" class="form-control outcome-select">
                                <option value="">Select a campaign first</option>
                            </select></td>
                        <td width="110">
                            <div class="btn-group-vertical">
                                <button type="button" disabled class="btn btn-default outcome-add">Add <span
                                        class="glyphicon glyphicon-chevron-right"></span></button>
                                <br>
                                <button type="button" disabled class="btn btn-default outcome-del"><span
                                        class="glyphicon glyphicon-chevron-left"></span> Remove
                                </button>
                            </div>
                        </td>
                        <td><select disabled style="height:200px" multiple class="form-control camp-outcome-select">
                         
              
                            </select></td>
                    </tr>
                </table>
            </div>
            <!-- /.panel-body -->
        </div>


    </div>

    <!-- /.row -->

    <script src="<?php echo base_url() ?>assets/js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <!-- SB Admin Scripts - Include with every page -->
    <script src="<?php echo base_url() ?>assets/js/sb-admin.js"></script>
    <script>
        $(document).ready(function () {
            admin.init();
            admin.campaigns.init();
        });
    </script>
