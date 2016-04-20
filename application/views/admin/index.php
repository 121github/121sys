<div class="panel panel-primary">
<div class="panel-heading">Admin</div>
<div class="panel-body" style="padding:0">
<ul class="nav nav-tabs" style=" background:#eee; width:100%;">
  <?php if(in_array("campaign menu",$_SESSION['permissions'])){ ?>
  <li class="campaigns-tab <?php echo $tab=="campaigns"||!$tab?"active":"" ?>"><a href="#campaigns" class="tab" data-toggle="tab">Campaigns</a></li>
  <?php } ?>
  <?php if(in_array("data menu",$_SESSION['permissions'])){ ?> 
  <li class="data-tab <?php echo $tab=="data"||!$tab?"active":"" ?>"><a href="#data" class="tab" data-toggle="tab">Data</a></li>
   <?php } ?>
      <?php if(in_array("view hours",$_SESSION['permissions'])){ ?>
  <li class="hours-tab <?php echo $tab=="hours"||!$tab?"active":"" ?>"><a href="#hours" class="tab" data-toggle="tab">Hours</a></li>
  <?php } ?>
  <?php if(in_array("edit templates",$_SESSION['permissions'])){ ?>
  <li class="marketing-tab <?php echo $tab=="marketing"||!$tab?"active":"" ?>"><a href="#marketing" class="tab" data-toggle="tab">Marketing</a></li>
    <?php } ?>
      <?php if(in_array("system menu",$_SESSION['permissions'])){ ?>
  <li class="system-tab <?php echo $tab=="system"||!$tab?"active":"" ?>"><a href="#system" class="tab" data-toggle="tab">System</a></li>
      <?php } ?>
       <?php if(in_array("admin users",$_SESSION['permissions'])){ ?>  
  <li class="users-tab <?php echo $tab=="users"||!$tab?"active":"" ?>"><a href="#users" class="tab" data-toggle="tab">Users</a></li>
   <?php } ?>
</ul>
<div class="tab-content">
  <div class="tab-pane <?php echo $tab=="campaigns"||!$tab?"active":"" ?>" id="campaigns">
    <h4>Campaign Setup</h4>
    <div class="frame_note">
      <p class="small"> From here you can manage all the campaigns on the system</p>
      <hr/>
      <ul class="pull-left">
          <?php if(in_array("campaign setup",$_SESSION['permissions'])){ ?>  
        <li><a href="<?php echo base_url(); ?>admin/campaigns">Setup campaigns</a> </li>
           <?php } ?>
           <?php if(in_array("campaign access",$_SESSION['permissions'])){ ?>  
        <li><a href="<?php echo base_url() ?>admin/campaign_access">Campaign Access</a></li>
           <?php } ?>
           <?php if($_SESSION['role']==1){ ?>  
        <li><a href="<?php echo base_url() ?>admin/campaign_permissions">Campaign Permissions</a></li>
           <?php } ?>
              <?php if(in_array("admin groups",$_SESSION['permissions'])){ ?>  
        <li><a href="<?php echo base_url() ?>admin/campaign_groups">Campaign Groups</a></li>
        <?php } ?>
 <?php if(in_array("campaign setup",$_SESSION['permissions'])){ ?>    
        <li><a href="<?php echo base_url() ?>admin/copy_campaign">Clone Campaign</a></li>
        <?php } ?>
         <?php if(in_array("campaign fields",$_SESSION['permissions'])){ ?>  
        <li><a href="<?php echo base_url() ?>admin/campaign_fields">Custom Fields</a></li>
         <?php } ?>
          <?php if(in_array("campaign setup",$_SESSION['permissions'])){ ?>  
        <li><a href="<?php echo base_url() ?>logos">Campaign Logos</a></li>
        <?php } ?>
          <?php if(in_array("edit scripts",$_SESSION['permissions'])){ ?>  
        <li> <a href="<?php echo base_url() ?>scripts">Scripts</a></li>
         <?php } ?>
           <?php if(in_array("triggers",$_SESSION['permissions'])){ ?>  
        <li> <a href="<?php echo base_url() ?>data/triggers">Triggers</a></li>
         <?php } ?>
      </ul>
      <?php if(in_array("edit templates",$_SESSION['permissions'])){ ?>  
      <ul class="pull-left">
       <?php if(in_array("send email",$_SESSION['permissions'])){ ?>  
        <li> <a href="<?php echo base_url() ?>templates">Email Templates</a></li>
          <?php } ?>
           <?php if(in_array("send sms",$_SESSION['permissions'])){ ?>  
        <li> <a href="<?php echo base_url() ?>smstemplates">SMS Templates</a></li>
          <?php } ?>
      </ul>
       <?php } ?>
      <div class="clearfix"></div>
    </div>
  </div>
  <div class="tab-pane <?php echo $tab=="data"?"active":"" ?>" id="data">
    <h4>Data</h4>
    <div class="frame_note">
      <p class="small"> Data tools and settings</p>
      <hr/>
      <ul>
        <?php if(in_array("import data",$_SESSION['permissions'])){ ?> 
        <li><a href="<?php echo base_url() ?>import">Import Data</a></li>
         <?php } ?>
         <?php if(in_array("export data",$_SESSION['permissions'])){ ?> 
        <li><a href="<?php echo base_url() ?>exports">Export Data</a></li>
         <?php } ?>
            <?php if(in_array("reassign data",$_SESSION['permissions'])){ ?> 
        <li><a href="<?php echo base_url() ?>data/management">Data Allocation</a></li>
                 <?php } ?>
 <?php if(in_array("ration data",$_SESSION['permissions'])){ ?> 
        <li><a href="<?php echo base_url() ?>data/daily_ration">Daily Ration</a></li>
          <?php } ?>
          <?php if(in_array("archive data",$_SESSION['permissions'])){ ?> 
        <li><a href="<?php echo base_url() ?>data/backup_restore">Archive Manager</a></li>
            <?php } ?>
             <?php if(in_array("duplicates",$_SESSION['permissions'])){ ?> 
        <li><a href="<?php echo base_url() ?>data/duplicates">Duplicates</a></li>
         <?php } ?>
                      <?php if(in_array("suppression",$_SESSION['permissions'])){ ?> 
        <li><a href="<?php echo base_url() ?>data/suppression">Suppression</a></li>
            <?php } ?>
      </ul>
    </div>
  </div>
  <div class="tab-pane <?php echo $tab=="hours"?"active":"" ?>" id="hours">
    <h4>Hours and Exceptions</h4>
    <div class="frame_note">
      <p class="small">Manage staff hours and exceptions for reporting purposes</p>
      <hr/>
      <ul class="pull-left">
        <li><a href="<?php echo base_url() ?>time/default_time">Default Times</a></li>
        <li><a href="<?php echo base_url() ?>hour/default_hours">Default Hours</a></li>
      </ul>
      <ul class="pull-left">
        <li><a href="<?php echo base_url() ?>time/agent_time">Agent Time</a></li>
        <li><a href="<?php echo base_url() ?>hour/hours">Agent Hours</a></li>
      </ul>
      <div class="clearfix"></div>
    </div>
  </div>
  <div class="tab-pane <?php echo $tab=="marketing"?"active":"" ?>" id="marketing">
    <h4>Marketing</h4>
    <div class="frame_note">
      <p class="small">Email and SMS text messaging service configuration</p>
      <hr/>
      <ul class="pull-left">
       <?php if(in_array("send email",$_SESSION['permissions'])){ ?> 
        <li><a href="<?php echo base_url() ?>email/bulk_email">Bulk Email Tool</a></li>
        <?php } ?>
         <?php if(in_array("send sms",$_SESSION['permissions'])){ ?> 
        <li><a href="<?php echo base_url() ?>sms/bulk_sms">Bulk Sms Tool</a></li>
           <?php } ?>
      </ul>
        <?php if(in_array("edit templates",$_SESSION['permissions'])){ ?> 
      <ul class="pull-left">
        <?php if(in_array("send email",$_SESSION['permissions'])){ ?> 
        <li><a href="<?php echo base_url() ?>templates">Email Templates</a></li>
             <?php } ?>
          <?php if(in_array("send sms",$_SESSION['permissions'])){ ?> 
        <li> <a href="<?php echo base_url() ?>smstemplates">SMS Templates</a></li>
          <?php } ?>
      </ul>
        <?php } ?>
      <div class="clearfix"></div>
    </div>
  </div>
  <div class="tab-pane <?php echo $tab=="system"?"active":"" ?>" id="system">
    <h4>System</h4>
    <div class="frame_note">
      <p class="small">General system configuration</p>
      <hr/>
      <ul class="pull-left">
         <?php if(in_array("database",$_SESSION['permissions'])){ ?> 
        <li><a href="<?php echo base_url() ?>database">Database</a></li>
        <?php } ?>
           <?php if(in_array("outcomes",$_SESSION['permissions'])){ ?> 
        <li><a href="<?php echo base_url() ?>data/outcomes">Outcomes</a></li>
        <?php } ?>
        <?php if(in_array("parkcodes",$_SESSION['permissions'])){ ?> 
        <li><a href="<?php echo base_url() ?>data/parkcodes">Park Codes</a></li>
        <?php } ?>
        <li><a href="<?php echo base_url() ?>dashboard/settings">Dashboard Settings</a></li>
        <li><a href="<?php echo base_url() ?>panels/settings">Panel Settings</a></li>
        <li><a href="<?php echo base_url() ?>reports/settings">Report Settings</a></li>
         <?php if(in_array("slot config",$_SESSION['permissions'])){ ?> 
       		<li><a href="<?php echo base_url() ?>admin/slots">Slot setup</a></li>
        <?php } ?>
      </ul>
      <ul class="pull-left">
           <?php if(in_array("view logs",$_SESSION['permissions'])){ ?> 
        <li><a href="<?php echo base_url() ?>admin/logs">Access Logs</a></li>
        <li><a href="<?php echo base_url() ?>audit">Data Logs</a></li>
         <?php } ?>
      </ul>
      <div class="clearfix"></div>
    </div>
  </div>
  <div class="tab-pane <?php echo $tab=="users"?"active":"" ?>" id="users">
    <h4>Users</h4>
    <div class="frame_note">
      <p class="small">The user account setup pages</p>
      <hr/>
      <ul>
        <?php if(in_array("admin users",$_SESSION['permissions'])){ ?> 
        <li><a href="<?php echo base_url() ?>admin/users">Users</a></li>
         <?php } ?>
          <?php if(in_array("admin roles",$_SESSION['permissions'])){ ?> 
        <li><a href="<?php echo base_url() ?>admin/roles">Roles</a> </li>
         <?php } ?>
          <?php if(in_array("admin teams",$_SESSION['permissions'])){ ?> 
        <li><a href="<?php echo base_url() ?>admin/teams">Teams</a> </li>
         <?php } ?>
          <?php if(in_array("admin groups",$_SESSION['permissions'])){ ?> 
        <li><a href="<?php echo base_url() ?>admin/groups">Groups</a></li>
         <?php } ?>
             <?php if(in_array("admin files",$_SESSION['permissions'])){ ?> 
        <li><a href="<?php echo base_url() ?>admin/files">Folder Access</a></li>
         <?php } ?>
         <?php if(in_array("slot availability",$_SESSION['permissions'])){ ?> 
        <li><a href="<?php echo base_url() ?>admin/availability">Availabilty</a></li>
          <?php } ?>
      </ul>
    </div>
  </div>
</div>
