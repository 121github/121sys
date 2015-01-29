<div class="page-header">
    <h2>My account</h2>
</div>


<div class="row">
    <div class="col-md-8 col-sm-12">
        <div class="panel panel-primary" >
            <div class="panel-heading">
                <i class="fa fa-bar-chart-o fa-fw"></i>
                Details
                <div class="pull-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span> Select other user</button>
                        <ul class="dropdown-menu pull-right" role="menu">
                            <li><a class="campaign-filter" ref="#" style="color: green;">Select other user</a> </li>
                            <?php foreach($users as $row): ?>
                                <li><a href="#" class="user-filter" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
                            <?php endforeach ?>
                            <li class="divider"></li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-3" style="text-align: center">
                        <button><span class="glyphicon glyphicon-user btn-lg" style="font-size: 75px;"></span></button>
                        <div style="margin-top: 5px;" class="main-name"></div>
                        <div style="margin-top: 5px;" class="main-role"></div>
                    </div>
                    <div class="col-lg-9">
                        <div style="border-bottom: 1px solid grey; margin-bottom: 10px; margin-top: 10px; font-weight: bold">
                            DETAILS:
                        </div>
                        <table class="table ajax-table">
                            <tr>
                                <td style="font-weight: bold">Username</td>
                                <td class="username"></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Name</td>
                                <td class="name"></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Role</td>
                                <td class="role"></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Group</td>
                                <td class="group"></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Team</td>
                                <td class="team"></td>
                            </tr>
                        </table>

                        <div style="border-bottom: 1px solid grey; margin-bottom: 10px; margin-top: 10px; font-weight: bold">
                            CONTACT DETAILS:
                            <div class="pull-right">
                                <span class="btn btn-sm edit-details-btn">Edit</span>
                            </div>
                        </div>
                        <table class="table ajax-table">
                            <tr>
                                <td style="font-weight: bold">Email</td>
                                <td class="email"></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Telephone</td>
                                <td class="telephone"></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Ext</td>
                                <td class="ext"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-12">
        <div class="panel panel-primary" >
            <div class="panel-heading"> <i class="fa fa-bar-chart-o fa-fw"></i>Change Your Password</div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form id="pass-form">
                    <div class="form-group">
                        <label for="current_pass">Current Password:</label>
                        <input type="password" name="current_pass" id="current_pass" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label for="new_pass">New Password:</label>
                        <input type="password" data-clear-btn="true" name="new_pass" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label for="conf_pass">Confirm Password:</label>
                        <input type="password" data-clear-btn="true" name="conf_pass" class="form-control"/>
                    </div>

                    <div class="pull-right">
                        <button type="submit" id="change-pass" name="change-pass" class="btn-success form-control">Change Password</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>

<div class="panel panel-primary details-container"">
    <div class="panel-heading">
        <div style="font-size: 18px;">
            Change Contact Details
            <span class="glyphicon glyphicon-remove pull-right close-details-btn"></span>
        </div>
    </div>
    <!-- /.panel-heading -->
    <div class="panel-body details-panel">
        <div class="details-content">
            <form id="details-form">
                <div class="form-group">
                    <input type="hidden" name="user_id" value="<?php echo $user_id ?>">
                    <div>
                        <label>Email:</label>
                        <input type="text" name="email_form" class="form-control"/>
                    </div>
                    <div>
                        <label>Telephone:</label>
                        <input type="text" name="telephone_form" class="form-control"/>
                    </div>
                    <div>
                        <label>Ext:</label>
                        <input type="text" name="ext_form" class="form-control"/>
                    </div>
                </div>
                <div class="pull-right">
                    <span class="marl btn btn-default close-details-btn">Cancel</span>
                    <span class="marl btn btn-success save-details-btn">Save</span>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">

    $(document).ready(function () {
        details.init();
        password.init();
        $('button[type="submit"]').on('click', function (e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                dataType: "JSON",
                data: $('#pass-form').serialize()
            }).done(function (response) {
                if (response.success) {
                    flashalert.success("Password was changed");
                    $('form')[0].reset();
                } else {
                    flashalert.danger(response.msg);
                }
            });
        });
    });

</script>
