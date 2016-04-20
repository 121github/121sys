<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">User Admin</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">

        <div class="panel panel-primary users-panel">
            <div class="panel-heading">User Admin
                <div class="pull-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-xs dropdown-toggle add-btn"
                                data-toggle="dropdown"><span class="glyphicon glyphicon-plus"></span> Add User
                        </button>
                    </div>
                </div>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body user-data">
                <?php $this->view('forms/edit_users_form.php', $options); ?>
                <table class="table ajax-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Group</th>
                        <th>Team</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Options</th>
                        <?php if (in_array("google sync", $_SESSION['permissions'])) { ?>
                            <th style="text-align: center" colspan="2">Google Account</th>
                        <?php } ?>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td colspan="3"><img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif"/></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <!-- /.panel-body -->
        </div>

    </div>


    <script src="<?php echo base_url() ?>assets/js/plugins/metisMenu/jquery.metisMenu.js"></script>


    <!-- SB Admin Scripts - Include with every page -->
    <script src="<?php echo base_url() ?>assets/js/sb-admin.js"></script>
    <script>
        $(document).ready(function () {
            admin.init();
            admin.users.init();
        });
    </script>