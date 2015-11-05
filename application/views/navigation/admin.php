<?php if (in_array("admin menu", $_SESSION['permissions'])) { ?>
                        <li><a href="#admin">Admin</a>
                            <ul id="admin">
                            
                            <?php if($_SESSION['session_name']=="121sys_prosales"){ ?>
								   <li><a href="#" id="del-data">Delete demo data</a></li>
                                   <script type="text/javascript">
								   $(document).on('click','#del-data',function(e){
									  e.preventDefault();
									  $.ajax({ url:helper.baseUrl+'data/clear_records',
									  type:"POST",
									  dataType:"HTML",
									  beforeSend:function(){
										modals.load_modal("Clearing data","<p>Please be patient while the system is reset</p><img src='"+helper.baseUrl+"assets/img/ajax-loader-bar.gif' />",'<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>'); 
									  }
								   }).done(function(response){
									   modal_body.html(response);
								   });
								    });
								   </script>
							<?php } ?>
                            
                                <?php if (in_array("system menu", $_SESSION['permissions'])) { ?>
                                    <?php if (in_array("edit templates", $_SESSION['permissions'])) { ?>
                                        <li <?php echo @($page == 'bulk-email' ? "class='Selected'" : "") ?>>
                                            <a href="<?php echo base_url() ?>email/bulk_email">Bulk Email Tool</a></li>
                                    <?php } ?>
                                    <?php if (in_array("send sms", $_SESSION['permissions'])) { ?>
                                        <li <?php echo @($page == 'bulk-sms' ? "class='Selected'" : "") ?>>
                                            <a href="<?php echo base_url() ?>sms/bulk_sms">Bulk Sms Tool</a></li>
                                    <?php } ?>
                                    <li><a href="#system">System Config</a>
                                        <ul id="system">
                                            <?php if (in_array("database", $_SESSION['permissions'])) { ?>
                                                <li <?php echo @($page == 'database' ? "class='Selected'" : "") ?>><a
                                                        href="<?php echo base_url() ?>database">Database</a></li>
                                            <?php } ?>
                                            <?php if (in_array("edit outcomes", $_SESSION['permissions'])) { ?>
                                                <li <?php echo @($page == 'outcomes' ? "class='Selected'" : "") ?>>
                                                    <a href="<?php echo base_url() ?>data/outcomes">Outcomes</a>
                                                </li>
                                            <?php } ?>
                                            <?php if (in_array("parkcodes", $_SESSION['permissions'])) { ?>
                                                <li <?php echo @($page == 'parkcode' ? "class='Selected'" : "") ?>>
                                                    <a href="<?php echo base_url() ?>data/parkcodes">Park
                                                        Codes</a></li>
                                            <?php } ?>
                                            <?php if ($_SESSION['role'] == 1) { ?>
                                                <li <?php echo @($page == 'users' ? "class='Selected'" : "") ?>>
                                                    <a href="<?php echo base_url() ?>admin/users">Users</a>
                                                </li>
                                                <li <?php echo @($page == 'roles' ? "class='Selected'" : "") ?>>
                                                    <a href="<?php echo base_url() ?>admin/roles">Roles</a>
                                                </li>
                                                <li <?php echo @($page == 'teams' ? "class='Selected'" : "") ?>>
                                                    <a href="<?php echo base_url() ?>admin/teams">Teams</a>
                                                </li>
                                                <li <?php echo @($page == 'groups' ? "class='Selected'" : "") ?>>
                                                    <a href="<?php echo base_url() ?>admin/groups">Groups</a>
                                                </li>
                                            <?php } ?>
                                            <?php if (in_array("view hours", $_SESSION['permissions'])) { ?>
                                            <li <?php echo @($page == 'default_time' ? "class='Selected'" : "") ?>>
                                                <a href="<?php echo base_url() ?>time/default_time">Default
                                                    Times</a></li>
                                            <li <?php echo @($page == 'default_hours' ? "class='Selected'" : "") ?>>
                                                <a href="<?php echo base_url() ?>hour/default_hours">Default
                                                    Hours</a></li>
                                           <?php } ?>
                                        </ul>
                                    </li>
                                <?php } ?>
                                <?php if (in_array("data menu", $_SESSION['permissions'])) { ?>
                                    <li>
                                        <a href="#data">Data Management</a>
                                        <ul id="data">
                                            <?php if (in_array("import data", $_SESSION['permissions'])) { ?>
                                                <li <?php echo @($page == 'import_data' ? "class='Selected'" : "") ?>>
                                                    <a href="<?php echo base_url() ?>import">Import Data</a>
                                                </li> <?php } ?>
                                            <?php if (in_array("export data", $_SESSION['permissions'])) { ?>
                                                <li <?php echo @($page == 'export_data' ? "class='Selected'" : "") ?>>
                                                    <a href="<?php echo base_url() ?>exports">Export Data</a>
                                                </li>
                                            <?php } ?>
                                            <?php if (in_array("reassign data", $_SESSION['permissions'])) { ?>
                                                <li <?php echo @($page == 'data_allocation' ? "class='Selected'" : "") ?>>
                                                    <a href="<?php echo base_url() ?>data/management">Data
                                                        Allocation</a></li>
                                            <?php } ?>

                                            <?php if (in_array("ration data", $_SESSION['permissions'])) { ?>
                                                <li <?php echo @($page == 'daily_ration' ? "class='Selected'" : "") ?>>
                                                    <a href="<?php echo base_url() ?>data/daily_ration">Daily
                                                        Ration</a></li>
                                            <?php } ?>
                                            <?php if (in_array("archive data", $_SESSION['permissions'])) { ?>
                                                <li <?php echo @($page == 'backup_restore' ? "class='Selected'" : "") ?>>
                                                    <a href="<?php echo base_url() ?>data/backup_restore">Archive
                                                        Manager</a>
                                                </li>
                                            <?php } ?>


                                            <?php if (in_array("duplicates", $_SESSION['permissions'])) { ?>
                                                <li <?php echo @($page == 'duplicates' ? "class='Selected'" : "") ?>>
                                                    <a href="<?php echo base_url() ?>data/duplicates">Duplicates</a>
                                                </li>
                                            <?php } ?>
                                            <?php if (in_array("suppression", $_SESSION['permissions'])) { ?>
                                                <li <?php echo @($page == 'suppression' ? "class='Selected'" : "") ?>>
                                                    <a href="<?php echo base_url() ?>data/suppression">Suppression
                                                    </a></li>
                                            <?php } ?>

                                        </ul>
                                    </li>
                                <?php } ?>
                                <?php if (in_array("campaign menu", $_SESSION['permissions'])) { ?>
                                    <li>
                                        <a href="#admin-campaigns">Campaign Setup</a>
                                        <ul id="admin-campaigns">
                                            <?php if (in_array("campaign access", $_SESSION['permissions'])) { ?>
                                                <li <?php echo @($page == 'campaign_access' ? "class='Selected'" : "") ?>>
                                                    <a href="<?php echo base_url() ?>admin/campaign_access">Campaign
                                                        Access</a></li>
                                            <?php } ?>

                                            <?php if (in_array("campaign setup", $_SESSION['permissions'])) { ?>
                                                <li <?php echo @($page == 'campaign_setup' ? "class='Selected'" : "") ?>>
                                                    <a
                                                        href="<?php echo base_url() ?>admin/campaigns">Campaign
                                                        Setup</a></li>
                                                        <li <?php echo @($page == 'campaign_permissions' ? "class='Selected'" : "") ?>>
                                                    <a
                                                        href="<?php echo base_url() ?>admin/campaign_permissions">Campaign Permissions
                                                        </a></li>
                                                        
                                                            <li <?php echo @($page == 'copy_campaign' ? "class='Selected'" : "") ?>>
                                                    <a href="<?php echo base_url() ?>admin/copy_campaign">Clone Campaign</a></li>
                                                <li <?php echo @($page == 'custom_fields' ? "class='Selected'" : "") ?>>
                                                    <a href="<?php echo base_url() ?>admin/campaign_fields">Campaign
                                                        Fields</a></li>
                                                <li <?php echo @($page == 'logos' ? "class='Selected'" : "") ?>>
                                                    <a href="<?php echo base_url() ?>logos">Campaign
                                                        Logos</a></li>
                                            <?php } ?>
                                            <?php if (in_array("edit templates", $_SESSION['permissions'])) { ?>
                                                <li <?php echo @($page == 'templates' ? "class='Selected'" : "") ?>>
                                                    <a href="<?php echo base_url() ?>templates">Email Templates</a>
                                                </li>
                                                <!--<li <?php echo @($page == 'templates' ? "class='Selected'" : "") ?>>
                                            <a href="<?php echo base_url() ?>smstemplates">SMS Templates</a>
                                        </li>-->
                                            <?php } ?>
                                            <?php if (in_array("edit templates", $_SESSION['permissions'])) { ?>
                                                <li <?php echo @($page == 'smstemplates' ? "class='Selected'" : "") ?>>
                                                    <a href="<?php echo base_url() ?>smstemplates">SMS Templates</a>
                                                </li>
                                            <?php } ?>
                                            <?php if (in_array("edit scripts", $_SESSION['permissions'])) { ?>
                                                <li <?php echo @($page == 'scripts' ? "class='Selected'" : "") ?>>
                                                    <a href="<?php echo base_url() ?>scripts">Scripts</a>
                                                </li>
                                            <?php } ?>
                                            <?php if (in_array("triggers", $_SESSION['permissions'])) { ?>
                                                <li <?php echo @($page == 'triggers' ? "class='Selected'" : "") ?>>
                                                    <a href="<?php echo base_url() ?>data/triggers">Triggers</a>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </li>
                                <?php } ?>
                                <?php if ($_SESSION['group'] == "1" && $_SESSION['role'] == "1") { ?>
                                    <li <?php echo @($page == 'files' ? "class='Selected'" : "") ?>>
                                        <a href="<?php echo base_url() ?>admin/files">Folder
                                            Access</a></li>

                                <?php } ?>

                                <?php if (in_array("view hours", $_SESSION['permissions'])) { ?>
                                    <li <?php echo @($page == 'agent_hours' ? "class='Selected'" : "") ?>>
                                        <a href="<?php echo base_url() ?>hour/hours">Agent
                                            Hours</a></li>

                                    <li>

                                    <li <?php echo @($page == 'agent_time' ? "class='Selected'" : "") ?>>
                                        <a href="<?php echo base_url() ?>time/agent_time">Agent
                                            Time</a></li>

                                <?php } ?>
                                <?php if (in_array("view logs", $_SESSION['permissions'])) { ?>
                                    <li>
                                        <a href="#logs">Logs</a>
                                        <ul id="logs">
                                            <li <?php echo @($page == 'logs' ? "class='Selected'" : "") ?>><a
                                                    href="<?php echo base_url() ?>admin/logs">Access Logs</a></li>
                                            <li <?php echo @($page == 'admin_audit' ? "class='Selected'" : "") ?>><a
                                                    href="<?php echo base_url() ?>audit">Data Logs</a></li>
                                        </ul>
                                    </li>
                                <?php } ?>
                                   </ul>
                        </li>
								   <?php } ?>