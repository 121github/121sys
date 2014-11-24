<div class="sidebar-nav">
    <div class="accordion" id="leftMenu">
        <div class="accordion-group panel">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#leftMenu" href="#collapseOne">
                    <i class="glyphicon glyphicon-home"></i> Dashboard
                </a>
            </div>
            <div id="collapseOne" class="accordion-body collapse <?php echo (!empty($dashboard)?"in":"") ?>">
                <div class="accordion-group">
                    <div class="accordion-inner">
                        <a href="<?php echo base_url() ?>dashboard/" <?php echo @($dashboard=='overview'?"class='active'":"") ?>>Overview</a>
                    </div>
                    <?php if(in_array("client dash",$_SESSION['permissions'])){ ?>
                    <div class="accordion-inner">
                   <a href="<?php echo base_url() ?>dashboard/client" <?php echo @($dashboard=='client'?"class='active'":"") ?>>Client Dash</a>
                    </div>
                    <?php } ?>
                    <!-- Advisor Dash -->
                    <?php if(in_array("agent dash",$_SESSION['permissions'])){ ?>
                    <div class="accordion-group panel">
			            <div class="accordion-inner">
			                <a href="<?php echo base_url() ?>dashboard/agent" <?php echo @($dashboard=='agent'?"class='active'":"") ?>>Advisor Dash</a>
			            </div>
			            <div id="collapseTwoManagementDash" class="accordion-body <?php echo @($dashboard=='agent'?"":"collapse") ?>">
			                <div class="accordion-group submenu">
			                    <div class="accordion-inner">
			                    	<span id="my_favorites">My Favorites</span>
			                    </div>
			                    <div class="accordion-inner">
			                        <span id="callbacks">Callbacks</span>
			                    </div>
			                    <div class="accordion-inner">
			                        <span id="missed_callbacks">Missed Callbacks</span>
			                    </div>
			                    <div class="accordion-inner">
			                        <span id="upcoming_callbacks">Upcoming Callbacks</span>
			                    </div>
			                </div>
			            </div>
			        </div>
                    <?php } ?>
                    <!-- End Advisor -->
                    <!-- Management Dash -->
                    <?php if(in_array("management dash",$_SESSION['permissions'])){ ?>
                    <div class="accordion-group panel">
			            <div class="accordion-inner">
			                <a href="<?php echo base_url() ?>dashboard/management" <?php echo @($dashboard=='management'?"class='active'":"") ?>>Management Dash</a>
			            </div>
			            <div id="collapseTwoManagementDash" class="accordion-body <?php echo @($dashboard=='management'?"":"collapse") ?>">
			                <div class="accordion-group submenu">
			                    <div class="accordion-inner">
			                    	<span id="agent_activity">Agent Activity</span>
			                    </div>
			                    <div class="accordion-inner">
			                        <span id="agent_success_rate">Agent Success Rates</span>
			                    </div>
			                    <div class="accordion-inner">
			                        <span id="agent_data">Agent Data</span>
			                    </div>
			                    <div class="accordion-inner" style="display: none">
			                        <span id="agent_current_hours">Agent Current Hours</span>
			                    </div>
			                </div>
			            </div>
			        </div>
                    <?php } ?>
                    <!-- End Management -->
                </div>
            </div>
        </div>
        <?php if(in_array("view reports",$_SESSION['permissions'])){ ?>
        <div class="accordion-group panel">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#leftMenu" href="#collapseTwo">
                    <i class="glyphicon glyphicon-file"></i> Reports
                </a>
            </div>
            <div id="collapseTwo" class="accordion-body collapse <?php echo @(!empty($reports)?"in":"") ?>">
                <div class="accordion-group">
                <!--
                    <div class="accordion-inner">
                      <a href="<?php echo base_url() ?>reports/targets" <?php echo @($reports=='targets'?"class='active'":"") ?>>Targets</a>
                    </div>
                    -->
                    <div class="accordion-inner">
                     <a href="<?php echo base_url() ?>reports/answers" <?php echo @($reports=='answers'?"class='active'":"") ?>>Survey Answers</a>
                    </div>
                    <div class="accordion-inner">
                       <a href="<?php echo base_url() ?>reports/activity" <?php echo @($reports=='activity'?"class='active'":"") ?>>Activity</a>
                    </div>
                    <!-- Transfers -->
                    <div class="accordion-group panel">
			            <div class="accordion-inner">
			                <a class="accordion-toggle" data-toggle="collapse" data-parent="#collapseTwo" href="#collapseTwoCampaign">
			                    Transfers
			                </a>
			            </div>
			            <div id="collapseTwoCampaign" class="accordion-body <?php echo @($reports=='transfers'?"":"collapse") ?>">
			                <div class="accordion-group submenu">
			                    <div class="accordion-inner">
			                        <a href="<?php echo base_url() ?>reports/campaigntransfer" <?php echo @($inner=='campaigntransfer'?"class='active'":"") ?>>By Campaign</a>
			                    </div>
			                    <div class="accordion-inner">
			                        <a href="<?php echo base_url() ?>reports/agenttransfer" <?php echo @($inner=='agenttransfer'?"class='active'":"") ?>>By Agent</a>
			                    </div>
			                    <div class="accordion-inner">
			                        <a href="<?php echo base_url() ?>reports/dailytransfer" <?php echo @($inner=='dailytransfer'?"class='active'":"") ?>>By Date</a>
			                    </div>
                                <div class="accordion-inner">
                                    <a href="<?php echo base_url() ?>reports/timetransfer" <?php echo @($inner=='dailytransfer'?"class='active'":"") ?>>By Time</a>
                                </div>
			                </div>
			            </div>
			        </div>
			        <!-- End transfers -->
			        <!-- Outcomes -->
                    <div class="accordion-group panel">
			            <div class="accordion-inner">
			                <a class="accordion-toggle" data-toggle="collapse" data-parent="#collapseTwo" href="#collapseTwoAgent">
			                    Outcomes
			                </a>
			            </div>
			            <div id="collapseTwoAgent" class="accordion-body <?php echo @($reports=='outcomes'?"":"collapse") ?>">
			                <div class="accordion-group submenu">
			                    <div class="accordion-inner">
			                       <a href="<?php echo base_url() ?>reports/outcomes/campaign/1" <?php echo @($inner=='campaign'?"class='active'":"") ?>>By Campaign</a>
			                    </div>
			                    <div class="accordion-inner">
			                       <a href="<?php echo base_url() ?>reports/outcomes/agent/1" <?php echo @($inner=='agent'?"class='active'":"") ?>>By Agent</a>
			                    </div>
			                    <div class="accordion-inner">
			                       <a href="<?php echo base_url() ?>reports/outcomes/date/1" <?php echo @($inner=='date'?"class='active'":"") ?>>By Date</a>
			                    </div>
                                <div class="accordion-inner">
                                    <a href="<?php echo base_url() ?>reports/outcomes/time/1" <?php echo @($inner=='time'?"class='active'":"") ?>>By Time</a>
                                </div>
			                </div>
			            </div>
			        </div>
			        <!-- End Agent 3 -->
                </div>
            </div>
        </div>
<?php } ?>
<?php if(in_array("admin nav",$_SESSION['permissions'])){ ?>
        <div class="accordion-group panel">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#leftMenu" href="#collapseThree">
                    <i class="glyphicon glyphicon-cog"></i> Administration
                </a>
            </div>
            <div id="collapseThree" class="accordion-body collapse <?php echo (!empty($admin)?"in":"") ?>">
            
            
             <div class="accordion-group panel">
			            <div class="accordion-inner">
			                <a class="accordion-toggle" data-toggle="collapse" data-parent="#collapseThree" href="#collapseThreeData">
			                    Data
			                </a>
			            </div>
			            <div id="collapseThreeData" class="accordion-body <?php echo @($admin=='data'?"":"collapse") ?>">
			                <div class="accordion-group submenu">
			                    <div class="accordion-inner">
			                        <a href="<?php echo base_url() ?>import" <?php echo @($inner=='import'?"class='active'":"") ?>>Import</a>
			                    </div>
			                    <div class="accordion-inner">
			                       <a href="<?php echo base_url() ?>exports" <?php echo @($inner=='export'?"class='active'":"") ?>>Export</a>
			                    </div>
                                   <?php if(in_array("reassign data",$_SESSION['permissions'])){ ?>
			                    <div class="accordion-inner">
			                         <a href="<?php echo base_url() ?>data/management" <?php echo @($inner=='management'?"class='active'":"") ?>>Data Management</a>
			                    </div>
			                    <div class="accordion-inner">
			                         <a href="<?php echo base_url() ?>data/add_record" <?php echo @($inner=='add_record'?"class='active'":"") ?>>Add Record</a>
			                    </div>
                                <?php } ?>
			                </div>
			            </div>
			        </div>
            
             		<div class="accordion-group panel">
			            <div class="accordion-inner">
			                <a class="accordion-toggle" data-toggle="collapse" data-parent="#collapseThree" href="#collapseThreeCampaigns">
			                    Campaigns
			                </a>
			            </div>
			            <div id="collapseThreeCampaigns" class="accordion-body <?php echo @($admin=='campaign'?"":"collapse") ?>">
			                <div class="accordion-group submenu">
                                      <?php if(in_array("campaign access",$_SESSION['permissions'])){ ?>
			                    <div class="accordion-inner">          
			                      <a href="<?php echo base_url() ?>admin/campaigns" <?php echo @($inner=='campaign'?"class='active'":"") ?>>Campaign Setup</a>
			                    </div>
                                <?php } ?>
			                    <div class="accordion-inner">
			                          <a href="<?php echo base_url() ?>admin/campaign_fields" <?php echo @($inner=='custom_fields'?"class='active'":"") ?>>Campaign Fields</a>
			                    </div>
                                   <?php if(in_array("edit templates",$_SESSION['permissions'])){ ?>
			                    <div class="accordion-inner">
			                            <a href="<?php echo base_url() ?>templates" <?php echo @($inner=='templates'?"class='active'":"") ?>>Templates</a>
			                    </div>
                                <?php } ?>
                                                    <?php if(in_array("edit scripts",$_SESSION['permissions'])){ ?>
			                    <div class="accordion-inner">
			                                   <a href="<?php echo base_url() ?>scripts" <?php echo @($inner=='scripts'?"class='active'":"") ?>>Scripts</a>
			                    </div>
                                <?php } ?>
			                </div>
			            </div>
			        </div>
            
            
            
                <div class="accordion-group">
                	<?php if($_SESSION['group']=="1"&&$_SESSION['role']=="1"){ ?>
                    <div class="accordion-inner">
                       <a href="<?php echo base_url() ?>admin/users" <?php echo @($admin=='users'?"class='active'":"") ?>>Users</a>
                    </div>
                     <div class="accordion-inner">
                       <a href="<?php echo base_url() ?>admin/roles" <?php echo @($admin=='roles'?"class='active'":"") ?>>Roles</a>
                    </div>
                     <div class="accordion-inner">
                       <a href="<?php echo base_url() ?>admin/teams" <?php echo @($admin=='teams'?"class='active'":"") ?>>Teams</a>
                    </div>
                       <div class="accordion-inner">
                    <a href="<?php echo base_url() ?>admin/groups" <?php echo @($admin=='groups'?"class='active'":"") ?>>Groups</a>
                    </div>
                     <?php } ?>
                    <?php if(in_array("view logs",$_SESSION['permissions'])){ ?>
                    <div class="accordion-inner">
                       <a href="<?php echo base_url() ?>admin/logs" <?php echo @($admin=='logs'?"class='active'":"") ?>>Logs</a>
                    </div>
                    <?php } ?> 
                    <?php if(in_array("view hours",$_SESSION['permissions'])){ ?>
                   	<div class="accordion-group panel">
                    	<div class="accordion-inner">
			                <a class="accordion-toggle" data-toggle="collapse" data-parent="#collapseThree" href="#collapseThreeHours">
			                    Hours
			                </a>
			            </div>
			            <div id="collapseThreeHours" class="accordion-body <?php echo @($admin=='hours'?"":"collapse") ?>">
			                <div class="accordion-group submenu">
			                	<div class="accordion-inner">          
				                	<a href="<?php echo base_url() ?>hour/default_hours" <?php echo @($inner=='default_hours'?"class='active'":"") ?>>Default Hours</a>
								</div>
                                <div class="accordion-inner">          
				                	<a href="<?php echo base_url() ?>hour/hours" <?php echo @($inner=='hours'?"class='active'":"") ?>>Agent Hours</a>
								</div>
			                </div>
			            </div>
                        <div class="accordion-inner">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#collapseThree" href="#collapseThreeTime">
                                Time
                            </a>
                        </div>
                        <div id="collapseThreeTime" class="accordion-body <?php echo @($admin=='time'?"":"collapse") ?>">
                            <div class="accordion-group submenu">
                                <div class="accordion-inner">
                                    <a href="<?php echo base_url() ?>time/default_time" <?php echo @($inner=='default_time'?"class='active'":"") ?>>Default Time</a>
                                </div>
                                <div class="accordion-inner">
                                    <a href="<?php echo base_url() ?>time/agent_time" <?php echo @($inner=='agent_time'?"class='active'":"") ?>>Agent Time</a>
                                </div>
                            </div>
                        </div>
			        </div>
                    <?php } ?>
                      
                </div>
            </div>
        </div>
  <?php } ?>  
    </div>
</div>
