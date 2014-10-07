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
                    
                    <div class="accordion-inner">
                   <a href="<?php echo base_url() ?>dashboard/client" <?php echo @($dashboard=='client'?"class='active'":"") ?>>Client Dash</a>
                    </div>
                    <?php if($_SESSION['group']=="1"){ ?>
                    <div class="accordion-inner">
                   <a href="<?php echo base_url() ?>dashboard/agent" <?php echo @($dashboard=='agent'?"class='active'":"") ?>>Advisor Dash</a>
                    </div>
                    <?php } ?>
                    <div class="accordion-inner">
                       <a href="<?php echo base_url() ?>dashboard/management" <?php echo @($dashboard=='management'?"class='active'":"") ?>>Management Dash</a>
                    </div>
                </div>
            </div>
        </div>
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
			                        <a href="<?php echo base_url() ?>reports/campaigntransfer" <?php echo @($inner=='campaigntransfer'?"class='active'":"") ?>>Campaign Transfer</a>
			                    </div>
			                    <div class="accordion-inner">
			                        <a href="<?php echo base_url() ?>reports/campaignappointment" <?php echo @($inner=='campaignappointment'?"class='active'":"") ?>>Campaign Appointment</a>
			                    </div>
			                    <div class="accordion-inner">
			                        <a href="<?php echo base_url() ?>reports/campaignsurvey" <?php echo @($inner=='campaignsurvey'?"class='active'":"") ?>>Campaign Survey</a>
			                    </div>
			                </div>
			            </div>
			        </div>
			        <!-- End Campaign 3 -->
                    <div class="accordion-inner">
                       <a href="<?php echo base_url() ?>reports/individual" <?php echo @($reports=='individual'?"class='active'":"") ?>>Individual</a>
                    </div>
                    <div class="accordion-inner">
                       <a href="<?php echo base_url() ?>reports/individualdaily" <?php echo @($reports=='individualdaily'?"class='active'":"") ?>>Individual Daily Comparison</a>
                    </div>
                    <div class="accordion-inner">
                       <a href="<?php echo base_url() ?>reports/agentdials" <?php echo @($reports=='agentdials'?"class='active'":"") ?>>Agent Dials</a>
                    </div>
                    <div class="accordion-inner">
                       <a href="<?php echo base_url() ?>reports/campaigndials" <?php echo @($reports=='campaigndials'?"class='active'":"") ?>>Campaign Dials</a>
                    </div>
                </div>
            </div>
        </div>

         		<?php if($_SESSION['group']=="1"){ ?>
        <div class="accordion-group panel">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse" data-parent="#leftMenu" href="#collapseThree">
                    <i class="glyphicon glyphicon-cog"></i> Administration
                </a>
            </div>
            <div id="collapseThree" class="accordion-body collapse <?php echo (!empty($admin)?"in":"") ?>">
                <div class="accordion-group">
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
                      <a href="<?php echo base_url() ?>data/management" <?php echo @($admin=='management'?"class='active'":"") ?>>Data Management</a>
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
                    <div class="accordion-inner">
                       <a href="<?php echo base_url() ?>admin/logs" <?php echo @($admin=='logs'?"class='active'":"") ?>>Logs</a>
                    </div>
                    <div class="accordion-inner">
                      <a href="<?php echo base_url() ?>templates" <?php echo @($admin=='templates'?"class='active'":"") ?>>Templates</a>
                    </div>
                    <div class="accordion-inner">
                      <a href="<?php echo base_url() ?>scripts" <?php echo @($admin=='scripts'?"class='active'":"") ?>>Scripts</a>
                    </div>       
                </div>
            </div>
        </div>
<?php } ?>
    </div>
</div>
