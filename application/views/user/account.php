<div class="page-header">
    <div class="row">
        <div class="col-lg-10">
            <h2>My account</h2>
        </div>
        <div class="col-lg-2">
            <?php if($_SESSION['role'] == 1): ?>
                <div class="">
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span> Select other user</button>
                        <ul class="dropdown-menu pull-right" role="menu">
                            <li><a class="campaign-filter" ref="#" style="color: green;">Select other user</a> </li>
                            <?php foreach($users as $row): ?>
                                <li><a href="#" class="user-filter" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
                            <?php endforeach ?>
                            <li class="divider"></li>
                        </ul>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 col-sm-12">
    
        <div class="panel panel-primary" >
            <div class="panel-heading">
                <i class="fa fa-user fa-fw"></i>
                Details
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

                        <div style="border-bottom: 1px solid grey; margin-bottom: 10px; padding-bottom:10px; margin-top: 10px; font-weight: bold">
                            CONTACT DETAILS 

                                <span class="glyphicon glyphicon-edit pointer marl pull-right" id="edit-details-btn" data-id="<?php echo $user_id ?>"></span>
                     
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
                            <tr>
                                <td style="font-weight: bold">Default Postcode</td>
                                <td class="home_postcode"></td>
                            </tr>
                        </table>

                        <div style="border-bottom: 1px solid grey; margin-bottom: 10px; padding-bottom:10px; margin-top: 10px; font-weight: bold">
                            ADDRESSES
                            <span class="glyphicon glyphicon-plus pointer marl pull-right" id="add-user-address" data-modal="add-user-address" data-user-id="<?php echo $user_id ?>" data-address-id=""></span>
                        </div>
                        <!-- Addresses -->
                        <div class="user-addresses"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <i class="fa fa-google">oogle Account</i>
                <div class="google-account pull-right"></div>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div class="col-lg-3" >
                    <div style="margin-top: 5px;" class="google-picture"></div>
                    <div style="margin-top: 5px;" class="google-name"></div>
                </div>
                <div class="col-lg-9 google-content">
                    <div class="google-login-msg" style="display: none">
                        Login into your google account
                    </div>
                    <div class="google-data">
                        <div style="margin-bottom: 10px; margin-top: 10px; font-weight: bold">
                            DETAILS
                        </div>
                        <table class="table google-details ajax-table"></table>

                        <div style="margin-bottom: 10px; margin-top: 10px; font-weight: bold">
                            CALENDARS
                        </div>
                        <div style="margin-bottom: 10px; margin-top: 10px; font-weight: bold">
                            <table class="table google-calendars ajax-table"></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-sm-12">
        <?php if ($_SESSION['environment'] != 'demo') { ?>
            <div class="panel panel-primary">
                <div class="panel-heading"><i class="fa fa-unlock-alt fa-fw"></i> Change Your Password</div>
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
                            <button type="submit" id="change-pass" name="change-pass" class="btn-success form-control">
                                Change Password
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        <?php } ?>
        <?php if($_SESSION['role'] == 1): ?>
        <div class="panel panel-primary" >
            <div class="panel-heading"> <i class="fa fa-bell fa-fw"></i>Activity</div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <table class="table ajax-table">
                    <tr>
                        <td style="font-weight: bold">User status</td>
                        <td class="user_status"></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold">Login mode</td>
                        <td class="login_mode"></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold">Last login</td>
                        <td class="last_login"></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold">Failed logins</td>
                        <td class="failed_logins"></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold">Last failed login</td>
                        <td class="last_failed_login"></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold">Reload session</td>
                        <td class="reload_session"></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold">Token</td>
                        <td class="token"></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold">Attendee</td>
                        <td class="attendee"></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold">Pass changed</td>
                        <td class="pass_changed"></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold">Change pass required</td>
                        <td class="reset_pass_token"></td>
                    </tr>
                </table>
            </div>
        </div>
        <?php endif ?>
    </div>
</div>
<?php echo form_close(); ?>


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
