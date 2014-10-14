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
                    <?php if(in_array("agent dash",$_SESSION['permissions'])){ ?>
                    <div class="accordion-inner">
                   <a href="<?php echo base_url() ?>dashboard/agent" <?php echo @($dashboard=='agent'?"class='active'":"") ?>>Advisor Dash</a>
                    </div>
                    <?php } ?>
                    <?php if(in_array("management dash",$_SESSION['permissions'])){ ?>
                    <div class="accordion-inner">
                       <a href="<?php echo base_url() ?>dashboard/management" <?php echo @($dashboard=='management'?"class='active'":"") ?>>Management Dash</a>
                    </div>
                    <?php } ?>
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
                    <div class="accordion-inner">
                      <a href="<?php echo base_url() ?>reports/targets" <?php echo @($reports=='targets'?"class='active'":"") ?>>Targets</a>
                    </div>
                    <div class="accordion-inner">
                     <a href="<?php echo base_url() ?>reports/answers" <?php echo @($reports=='answers'?"class='active'":"") ?>>Answers</a>
                    </div>
                    <div class="accordion-inner">
                       <a href="<?php echo base_url() ?>reports/activity" <?php echo @($reports=='activity'?"class='active'":"") ?>>Activity</a>
                    </div>
                    <!-- Campaign -->
                    <div class="accordion-group panel">
			            <div class="accordion-inner">
			                <a class="accordion-toggle" data-toggle="collapse" data-parent="#collapseTwo" href="#collapseTwoCampaign">
			                    Campaign
			                </a>
			            </div>
			            <div id="collapseTwoCampaign" class="accordion-body <?php echo @($reports=='campaign'?"":"collapse") ?>">
			                <div class="accordion-group submenu">
			                    <div class="accordion-inner">
			                        <a href="<?php echo base_url() ?>reports/campaigntransfer" <?php echo @($inner=='campaigntransfer'?"class='active'":"") ?>>Transfers</a>
			                    </div>
			                    <div class="accordion-inner">
			                        <a href="<?php echo base_url() ?>reports/campaignappointment" <?php echo @($inner=='campaignappointment'?"class='active'":"") ?>>Appointments</a>
			                    </div>
			                    <div class="accordion-inner">
			                        <a href="<?php echo base_url() ?>reports/campaignsurvey" <?php echo @($inner=='campaignsurvey'?"class='active'":"") ?>>Surveys</a>
			                    </div>
			                    <div class="accordion-inner">
			                       <a href="<?php echo base_url() ?>reports/campaigndials" <?php echo @($inner=='campaigndials'?"class='active'":"") ?>>Dials</a>
			                    </div>
			                </div>
			            </div>
			        </div>
			        <!-- End Campaign 3 -->
			        
			        <!-- Agent -->
                    <div class="accordion-group panel">
			            <div class="accordion-inner">
			                <a class="accordion-toggle" data-toggle="collapse" data-parent="#collapseTwo" href="#collapseTwoAgent">
			                    Agent
			                </a>
			            </div>
			            <div id="collapseTwoAgent" class="accordion-body <?php echo @($reports=='agent'?"":"collapse") ?>">
			                <div class="accordion-group submenu">
			                    <div class="accordion-inner">
			                       <a href="<?php echo base_url() ?>reports/agenttransfer" <?php echo @($inner=='agenttransfer'?"class='active'":"") ?>>Transfers</a>
			                    </div>
			                    <div class="accordion-inner">
			                       <a href="<?php echo base_url() ?>reports/agentappointment" <?php echo @($inner=='agentappointment'?"class='active'":"") ?>>Appointments</a>
			                    </div>
			                    <div class="accordion-inner">
			                       <a href="<?php echo base_url() ?>reports/agentsurvey" <?php echo @($inner=='agentsurvey'?"class='active'":"") ?>>Surveys</a>
			                    </div>
			                    <div class="accordion-inner">
			                       <a href="<?php echo base_url() ?>reports/agentdials" <?php echo @($inner=='agentdials'?"class='active'":"") ?>>Dials</a>
			                    </div>
			                </div>
			            </div>
			        </div>
			        <!-- End Agent 3 -->
			        
                    <!-- Daily Comparison -->
                    <div class="accordion-group panel">
			            <div class="accordion-inner">
			                <a class="accordion-toggle" data-toggle="collapse" data-parent="#collapseTwo" href="#collapseTwoDaily">
			                    Daily Comparison
			                </a>
			            </div>
			            <div id="collapseTwoDaily" class="accordion-body <?php echo @($reports=='daily'?"":"collapse") ?>">
			                <div class="accordion-group submenu">
			                    <div class="accordion-inner">
			                       <a href="<?php echo base_url() ?>reports/dailytransfer" <?php echo @($reports=='dailytransfer'?"class='active'":"") ?>>Transfers</a>
			                    </div>
			                    <div class="accordion-inner">
			                       <a href="<?php echo base_url() ?>reports/dailyappointment" <?php echo @($reports=='dailyappointment'?"class='active'":"") ?>>Appointments</a>
			                    </div>
			                    <div class="accordion-inner">
			                       <a href="<?php echo base_url() ?>reports/dailysurvey" <?php echo @($reports=='dailysurvey'?"class='active'":"") ?>>Surveys</a>
			                    </div>
			                </div>
			            </div>
			        </div>
			        <!-- End Daily Comparison 3 -->
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
                <div class="accordion-group">
                	<?php if($_SESSION['group']=="1"&&$_SESSION['role']=="1"){ ?>
                    <div class="accordion-inner">
                     <a href="<?php echo base_url() ?>admin/campaigns" <?php echo @($admin=='campaign'?"class='active'":"") ?>>Campaigns</a>
                    </div>
                    <div class="accordion-inner">
                    <a href="<?php echo base_url() ?>admin/groups" <?php echo @($admin=='groups'?"class='active'":"") ?>>Groups</a>
                    </div>
                    <div class="accordion-inner">
                      <a href="<?php echo base_url() ?>data" <?php echo @($admin=='data'?"class='active'":"") ?>>Import</a>
                    </div>
                     <div class="accordion-inner">
                      <a href="<?php echo base_url() ?>exports" <?php echo @($admin=='exports'?"class='active'":"") ?>>Export</a>
                    </div>
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
                     <?php if(in_array("reassign data",$_SESSION['permissions'])){ ?>
                     <div class="accordion-inner">
                     <a href="<?php echo base_url() ?>data/management" <?php echo @($admin=='management'?"class='active'":"") ?>>Data Management</a>
                    </div>
                    <?php } ?>
                    <?php if(in_array("view logs",$_SESSION['permissions'])){ ?>
                    <div class="accordion-inner">
                       <a href="<?php echo base_url() ?>admin/logs" <?php echo @($admin=='logs'?"class='active'":"") ?>>Logs</a>
                    </div>
                    <?php } ?> 
                    <?php if(in_array("edit templates",$_SESSION['permissions'])){ ?>
                    <div class="accordion-inner">
                      <a href="<?php echo base_url() ?>templates" <?php echo @($admin=='templates'?"class='active'":"") ?>>Templates</a>
                    </div>
                    <?php } ?> 
                    <?php if(in_array("edit scripts",$_SESSION['permissions'])){ ?>
                    <div class="accordion-inner">
                      <a href="<?php echo base_url() ?>scripts" <?php echo @($admin=='scripts'?"class='active'":"") ?>>Scripts</a>
                    </div>
                    <?php } ?> 
                    <?php if(in_array("view hours",$_SESSION['permissions'])){ ?>
                    <div class="accordion-inner">
                      <a href="<?php echo base_url() ?>admin/hours" <?php echo @($admin=='hours'?"class='active'":"") ?>>Hours</a>
                    </div>   
                    <?php } ?>    
                </div>
            </div>
        </div>
  <?php } ?>  
    </div>
</div>
