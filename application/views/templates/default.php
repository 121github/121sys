<?php
$show_footer = false;
if (isset($_SESSION['current_campaign']) && in_array("show footer", $_SESSION['permissions'])) {
    $show_footer = true;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
<title><?php echo $title; ?></title>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.css">
<!-- Optional theme -->
<link rel="stylesheet"
          href="<?php echo base_url(); ?>assets/themes/<?php echo(isset($_SESSION['theme_folder']) ? $_SESSION['theme_folder'] : "default"); ?>/bootstrap-theme.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/plugins/dataTables/css/dataTables.bootstrap.css">
<!-- Latest compiled and minified JavaScript -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-datetimepicker.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/datepicker3.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-select.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/slider.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/default.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/plugins/mmenu/jquery.mmenu.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/plugins/mmenu/addons/jquery.mmenu.labels.css">
<style>
.navbar-toggle {
	display: block;
}
.navbar-toggle {
	float: left;
	margin-left: 15px;
}
</style>
<!-- Set the baseUrl in the JavaScript helper -->
<?php //load specific javascript files set in the controller
    if (isset($css)):
        foreach ($css as $file): ?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/<?php echo $file ?>">
<?php endforeach;
    endif; ?>
<link rel="shortcut icon"
          href="<?php echo base_url(); ?>assets/themes/<?php echo(isset($_SESSION['theme_folder']) ? $_SESSION['theme_folder'] : "default"); ?>/icon.png">
<script src="<?php echo base_url(); ?>assets/js/lib/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/lib/jquery-ui-1.9.2.custom.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/lib/wavsurfer.js"></script>
<!--Need to make a new icon for this
          <link rel="apple-touch-icon" href="<?php echo base_url(); ?>assets/img/apple-touch-icon.png" />-->
</head>
<body>
<div class="img-circle" id="timerclosed" style="display: none;"> <span class="glyphicon glyphicon-earphone opentimer pointer"></span> <span class="glyphicon glyphicon-stop stoptimer pointer"></span> </div>
<div class="img-circle" id="timeropened" style="display: none;">
  <div id="defaultCountdown"></div>
  <span class="glyphicon glyphicon-earphone closetimer pointer"></span> <span class="glyphicon glyphicon-stop stoptimer pointer"></span> </div>
<?php if ($show_footer) { ?>
<div class="navbar-inverse footer-stats" style="z-index:1"> 
  <!--ajax generated footer stats go here --> 
</div>
<?php } ?>
<div class="navbar navbar-default navbar-fixed-top">
  <?php if(isset($_SESSION['permissions'])){ ?>
  <a href="#menu" class="navbar-toggle"> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span></a>
  <?php } ?>
  
   <?php if (!isset($_SESSION['permissions']) || count($_SESSION['campaign_access']['array']) < 3 || in_array("mix campaigns", $_SESSION['permissions'])) { ?>
                    <a href="#" class="navbar-brand pull-right"><img style="margin-top:-5px;"
                                                          src="<?php echo base_url(); ?>assets/themes/<?php echo(isset($_SESSION['theme_folder']) ? $_SESSION['theme_folder'] : "default"); ?>/logo.png"></a>
                <?php } else { ?>
                    <span style="position:absolute;top:8px; right:20px">
                             <img id="small-logo" style="margin-top:-10px; margin-right:5px;"
                                                                 src="<?php echo base_url(); ?>assets/themes/<?php echo(isset($_SESSION['theme_folder']) ? $_SESSION['theme_folder'] : "default"); ?>/small-logo.png">
                    <?php if (isset($campaign_access) && count($campaign_access) > 0) { ?>
                        <select class="selectpicker" id="campaign-select">
                            <option value="">Select a campaign to begin</option>
                            <?php foreach ($campaign_access as $client => $camp_array) { ?>
                                <optgroup label="<?php echo $client ?>">
                                    <?php foreach ($camp_array as $camp) { ?>
                                        <option <?php if (isset($_SESSION['current_campaign']) && $_SESSION['current_campaign'] == $camp['id']) {
                                            echo "Selected";
                                        } ?> value="<?php echo $camp['id'] ?>"><?php echo $camp['name'] ?></option>
                                    <?php } ?>
                                </optgroup>
                            <?php } ?>
                        </select>

                        </span>
                    <?php } ?>
                <?php } ?>
  
</div>
<nav id="menu" class="mm-menu mm--horizontal mm-offcanvas" >
  <?php if(isset($_SESSION['permissions'])){ ?>
  <ul>
    <li><a href="#">Home</a></li>
    <?php if(in_array("use callpot", $_SESSION['permissions'])){ 
	if (isset($_SESSION['current_campaign'])) { ?>
    <li><a href="<?php echo base_url(); ?>records/detail">Start Calling</a></li>
    <?php } else { ?>
    <li><a href="#" style="color:red">You must select a campaign</a></li>
    <?php } } ?>
    <li><a href="#mm-1">Dashboard</a>
      <ul>
        <li <?php echo @($page == 'favorites_dash' ? "class=Selected'" : "") ?>> <a href="<?php echo base_url() ?>dashboard/favorites" >Favorites</a></li>
         <li <?php echo @($page == 'overview' ? "class=Selected'" : "") ?>> <a href="<?php echo base_url() ?>dashboard/" >Overview</a></li>
        <?php if (in_array("client dash", $_SESSION['permissions'])) { ?>
        <li <?php echo @($page == 'client_dash' ? "class=Selected'" : "") ?>><a href="<?php echo base_url() ?>dashboard/client" >Client Dashboard</a></li>
        <?php } ?>

        <?php if (in_array("nbf dash", $_SESSION['permissions'])) { ?>
        <li <?php echo @($page == 'nbf_dash' ? "class=Selected'" : "") ?>><a href="<?php echo base_url() ?>dashboard/nbf" >New Business</a></li>
        <?php } ?>
        <?php if (in_array("agent dash", $_SESSION['permissions'])) { ?>
        <li <?php echo @($page == 'callback_dash' ? "class=Selected'" : "") ?>> <a href="<?php echo base_url() ?>dashboard/callbacks" >Callbacks</a></li>
        <?php } ?>
        <?php if (in_array("management dash", $_SESSION['permissions'])) { ?>
        <li <?php echo @($page == 'management_dash' ? "class=Selected'" : "") ?>><a href="<?php echo base_url() ?>dashboard/management" >Management Dash</a></li>
        <?php } ?>
      </ul>
    </li>
    <li><a href="#records">Records</a>
    <ul id="records">
    <?php if (@in_array("search records", $_SESSION['permissions']) || isset($_SESSION['current_campaign']) && isset($_SESSION['search records'])) { ?>
    <li <?php if ($page == "search") {
                                echo "class='Selected'";
                            } ?>><a href="<?php echo base_url(); ?>search" class="hreflink">Search Records</a></li>
    <?php } ?>
        <li <?php if ($page == "list_records") {
                                echo "class='Selected'";
                            } ?>><a href="<?php echo base_url(); ?>records/view">List Records</a></li>
      <?php if (in_array("add records", $_SESSION['permissions'])) { ?>
      <li <?php echo @($page == 'add_record' ? "class='Selected'" : "") ?>>
                                                        <a href="<?php echo base_url() ?>data/add_record" >Create
                                                            Record</a></li>
                                                <?php } ?>
  
    </ul>
    </li>
                                         
    
    
    

        <?php if (in_array("view appointments", $_SESSION['permissions'])) { ?>
        <li <?php echo @($page == 'appointments' ? "class=Selected'" : "") ?>> <a href="<?php echo base_url() ?>appointments" >Appointments</a></li>
        <?php } ?>
     <?php if (@in_array('Surveys', $_SESSION['campaign_features']) && in_array("search surveys", $_SESSION['permissions'])) { ?>
    <li <?php if ($this->uri->segment(1) == "survey") {
                                echo "Selected";
                            } ?>><a href="<?php echo base_url(); ?>survey/view">View Surveys</a></li>
    <?php } ?>
                      <?php if (@in_array('full calendar', $_SESSION['permissions']) && in_array("mix campaigns", $_SESSION['permissions']) || @in_array('Appointment Setting', $_SESSION['campaign_features'])) { ?>
    <li <?php if ($this->uri->segment(1) == "calendar") {
                                echo "class='Selected'";
                            } ?>><a href="<?php echo base_url(); ?>calendar">Calendar</a></li>
    <!--<li><a href="<?php echo base_url(); ?>planner">Journey Planner</a></li> 
    <li><a href="#">Maps</a></li>-->
    <?php } ?>
     <?php if ($_SESSION['role']==1) { ?>
    <li><a href="#admin">Admin</a>
       <ul id="admin">
       <li><a href="#system">System Config</a>
       <ul id="system">
            <?php if (in_array("database", $_SESSION['permissions'])) { ?>
       <li  <?php echo @($page == 'database' ? "class='Selected'" : "") ?>><a href="<?php echo base_url() ?>database">Database</a></li>
       <?php } ?>
                                                       <?php if (in_array("edit outcomes", $_SESSION['permissions'])) { ?>
                                                    <li <?php echo @($page == 'outcomes' ? "class='Selected'" : "") ?>>
                                                        <a href="<?php echo base_url() ?>data/outcomes" >Outcomes</a>
                                                    </li>
                                                <?php } ?>
                                                                                             <?php if (in_array("parkcodes", $_SESSION['permissions'])) { ?>
                                                    <li  <?php echo @($page == 'parkcode' ? "class='Selected'" : "") ?>>
                                                        <a href="<?php echo base_url() ?>data/parkcodes">Park
                                                            Codes</a></li>
                                                <?php } ?>
                                                                                        <li <?php echo @($page == 'users' ? "class='Selected'" : "") ?>>
                                            <a href="<?php echo base_url() ?>admin/users" >Users</a>
                                        </li>
                                        <li <?php echo @($page == 'roles' ? "class='Selected'" : "") ?>>
                                            <a href="<?php echo base_url() ?>admin/roles" >Roles</a>
                                        </li>
                                        <li <?php echo @($page == 'teams' ? "class='Selected'" : "") ?>>
                                            <a href="<?php echo base_url() ?>admin/teams" >Teams</a>
                                        </li>
                                        <li <?php echo @($page == 'groups' ? "class='Selected'" : "") ?>>
                                            <a href="<?php echo base_url() ?>admin/groups" >Groups</a>
                                        </li>
                                       <li <?php echo @($page == 'default_time' ? "class='Selected'" : "") ?>>
                                                    <a href="<?php echo base_url() ?>time/default_time" >Default
                                                        Times</a></li>
                                                                                                  <li <?php echo @($page == 'default_hours' ? "class='Selected'" : "") ?>>
                                                    <a href="<?php echo base_url() ?>hour/default_hours" >Default
                                                        Hours</a></li>
       </ul>
       </li>
                                    <?php if (in_array("data menu", $_SESSION['permissions'])) { ?>
                                        <li>
                                            <a href="#data">Data Management</a>
                                            <ul id="data">
                                                <?php if (in_array("import data", $_SESSION['permissions'])) { ?>
                                                    <li <?php echo @($page == 'import_data' ? "class='Selected'" : "") ?>>
                                                        <a href="<?php echo base_url() ?>import" >Import Data</a>
                                                    </li> <?php } ?>
                                                <?php if (in_array("export data", $_SESSION['permissions'])) { ?>
                                                    <li <?php echo @($page == 'export_data' ? "class='Selected'" : "") ?>>
                                                        <a href="<?php echo base_url() ?>exports" >Export Data</a>
                                                    </li>
                                                <?php } ?>
                                                <?php if (in_array("reassign data", $_SESSION['permissions'])) { ?>
                                                    <li <?php echo @($page == 'data_allocation' ? "class='Selected'" : "") ?>>
                                                        <a href="<?php echo base_url() ?>data/management" >Data
                                                            Allocation</a></li>
                                                <?php } ?>
         
                                                <?php if (in_array("ration data", $_SESSION['permissions'])) { ?>
                                                    <li  <?php echo @($page == 'daily_ration' ? "class='Selected'" : "") ?>>
                                                        <a href="<?php echo base_url() ?>data/daily_ration">Daily
                                                            Ration</a></li>
                                                <?php } ?>
                                                <?php if (in_array("archive data", $_SESSION['permissions'])) { ?>
                                                    <li <?php echo @($page == 'backup_restore' ? "class='Selected'" : "") ?>>
                                                        <a href="<?php echo base_url() ?>data/backup_restore" >Archive Manager</a></li>
                                                <?php } ?>


                                                <?php if (in_array("duplicates", $_SESSION['permissions'])) { ?>
                                                    <li <?php echo @($page == 'duplicates' ? "class='Selected'" : "") ?>>
                                                        <a href="<?php echo base_url() ?>data/duplicates" >Duplicates</a>
                                                    </li>
                                                <?php } ?>
                                                <?php if (in_array("suppression", $_SESSION['permissions'])) { ?>
                                                    <li <?php echo @($page == 'suppression' ? "class='Selected'" : "") ?>>
                                                        <a href="<?php echo base_url() ?>data/suppression" >Suppression
                                                            </a></li>
                                                <?php } ?>
   
                                            </ul>
                                        </li>
                                    <?php } ?>
                                    <?php if (in_array("campaign menu", $_SESSION['permissions'])) { ?>
                                        <li>
                                            <a href="#admin-campaigns">Campaign Setup</a>
                                            <ul id="admin-campaigns">
                                                <?php if ($_SESSION['role'] == "1") { ?>
                                                    <li <?php echo @($page == 'campaign_setup' ? "class='Selected'" : "") ?>>
                                                        <a href="<?php echo base_url() ?>admin/campaigns" <>Campaign
                                                            Setup</a></li>
                                                    <li <?php echo @($page == 'custom_fields' ? "class='Selected'" : "") ?>>
                                                        <a href="<?php echo base_url() ?>admin/campaign_fields" >Campaign
                                                            Fields</a></li>
                                                    <li <?php echo @($page == 'logos' ? "class='Selected'" : "") ?>>
                                                        <a href="<?php echo base_url() ?>logos" >Campaign
                                                            Logos</a></li>
                                                <?php } ?>
                                                <?php if (in_array("edit templates", $_SESSION['permissions'])) { ?>
                                                    <li <?php echo @($page == 'templates' ? "class='Selected'" : "") ?>>
                                                        <a href="<?php echo base_url() ?>templates" >Email Templates</a>
                                                    </li>
                                                <?php } ?>
                                                <?php if (in_array("edit scripts", $_SESSION['permissions'])) { ?>
                                                    <li <?php echo @($page == 'scripts' ? "class='Selected'" : "") ?>>
                                                        <a href="<?php echo base_url() ?>scripts" >Scripts</a>
                                                    </li>
                                                <?php } ?>
                                                                                                <?php if (in_array("triggers", $_SESSION['permissions'])) { ?>
                                                    <li <?php echo @($page == 'triggers' ? "class='Selected'" : "") ?>>
                                                        <a href="<?php echo base_url() ?>data/triggers" >Triggers</a>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </li>
                                    <?php } ?>
                                    <?php if ($_SESSION['group'] == "1" && $_SESSION['role'] == "1") { ?>
                                        <li <?php echo @($page == 'files' ? "class='Selected'" : "") ?>>
                                            <a href="<?php echo base_url() ?>admin/files" >Folder
                                                Access</a></li>

                                    <?php } ?>

                                    <?php if (in_array("view hours", $_SESSION['permissions'])) { ?>     
                                                <li <?php echo @($page == 'agent_hours' ? "class='Selected'" : "") ?>>
                                                    <a href="<?php echo base_url() ?>hour/hours" >Agent
                                                        Hours</a></li>
       
                                        <li>

                                                <li <?php echo @($page == 'agent_time' ? "class='Selected'" : "") ?>>
                                                    <a href="<?php echo base_url() ?>time/agent_time" >Agent
                                                        Time</a></li>

                                    <?php } ?>
                                                                        <?php if (in_array("view logs", $_SESSION['permissions'])) { ?>
                                        <li>
                                            <a href="#logs">Logs</a>
                                            <ul id="logs">
                                            <li <?php echo @($page == 'logs' ? "class='Selected'" : "") ?>><a href="<?php echo base_url() ?>admin/logs">Access Logs</a></li>
                                             <li <?php echo @($page == 'admin_audit' ? "class='Selected'" : "") ?>><a href="<?php echo base_url() ?>audit">Data Logs</a></li>
                                            </ul>
                                        </li>
                                    <?php } ?>
                                </ul>
                           
                      
    </li>
    <?php } ?>
    
  <?php if (in_array("reports menu", $_SESSION['permissions'])) { ?>
                            <li><a href="#reports">Reports</a>
                                <ul id="reports">
                                    <?php if (in_array("survey answers", $_SESSION['permissions'])) { ?>
                                        <li <?php echo @($page == 'answers' ? "class='Selected'" : "") ?>>
                                            <a href="<?php echo base_url() ?>reports/answers" >Survey
                                                Answers</a></li>  <?php } ?>
                                    <?php if (in_array("activity", $_SESSION['permissions'])) { ?>
                                        <li <?php echo @($page == 'activity' ? "class='Selected'" : "") ?>>
                                            <a href="<?php echo base_url() ?>reports/activity" >Activity</a>
                                        </li>
                                    <?php } ?>
                                        <li <?php echo @($page == 'audit' ? "class='Selected'" : "") ?>><a href="<?php echo base_url() ?>audit">Data Capture Logs</a></li>
                                    <li>
                                        <a href="#reports-outcomes">Outcomes</a>
                                        <ul id="reports-outcomes">
                                            <li <?php echo @($page == 'outcome_report_campaign' ? "class='Selected'" : "") ?>>
                                                <a href="<?php echo base_url() ?>reports/outcomes/campaign/1" >By
                                                    Campaign</a></li>
                                            <?php if (in_array("by agent", $_SESSION['permissions'])) { ?>
                                                <li <?php echo @($page == 'outcome_report_agent' ? "class='Selected'" : "") ?>>
                                                    <a href="<?php echo base_url() ?>reports/outcomes/agent/1" >By
                                                        Agent</a></li>
                                            <?php } ?>
                                            <li <?php echo @($page == 'outcome_report_date' ? "class='Selected'" : "") ?>>
                                                <a href="<?php echo base_url() ?>reports/outcomes/date/1" >By
                                                    Date</a></li>
                                            <li <?php echo @($page == 'outcome_report_time' ? "class='Selected'" : "") ?>>
                                                <a href="<?php echo base_url() ?>reports/outcomes/time/1" >By
                                                    Time</a></li>
                                        </ul>
                                    </li>
                                    <?php if (in_array("productivity", $_SESSION['permissions'])) { ?>
                                        <li <?php echo @($page == 'productivity' ? "class='Selected'" : "") ?>>
                                            <a href="<?php echo base_url() ?>reports/productivity" >
                                                Productivity
                                            </a>
                                        </li>
                                    <?php } ?>
                                    <?php if (in_array("email", $_SESSION['permissions'])) { ?>
                                        <li>

                                            <a href="#reports-emails">Emails</a>
                                            <ul id="reports-emails">
                                                <li <?php echo @($page == 'email_report_campaign' ? "class='Selected'" : "") ?>>
                                                    <a href="<?php echo base_url() ?>reports/email/campaign/1" >By
                                                        Campaign</a></li>
                                                <?php if (in_array("by agent", $_SESSION['permissions'])) { ?>
                                                    <li <?php echo @($page == 'email_report_agent' ? "class='Selected'" : "") ?>>
                                                        <a href="<?php echo base_url() ?>reports/email/agent/1" >By
                                                            Agent</a></li>
                                                <?php } ?>
                                                <li <?php echo @($page == 'email_report_date' ? "class='Selected'" : "") ?>>
                                                    <a href="<?php echo base_url() ?>reports/email/date/1" >By
                                                        Date</a></li>
                                                <li <?php echo @($page == 'email_report_time' ? "class='Selected'" : "") ?>>
                                                    <a href="<?php echo base_url() ?>reports/email/time/1" >By
                                                        Time</a></li>
                                            </ul>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </li>
                        <?php } ?>
    


    
                        <li  class="Spacer"><a href="<?php echo base_url(); ?>user/account" class="hreflink">My Account</a></li>
                                <li><a href="<?php echo base_url(); ?>user/logout" class="hreflink">Logout</a></li>
  </ul>
  <?php } ?>
              
</nav>
<div class="container-fluid"> <?php echo $body; ?></div>
<!-- /content --> 

<!-- Modal -->
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Please confirm</h4>
      </div>
      <div class="modal-body"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default close-modal pull-left" data-dismiss="modal">Close</button>
        <button type="button" style="display: none;" class="btn btn-danger discard-modal">No</button>
        <button type="button" style="display: none;" class="btn btn-primary save-modal">Save</button>
        <button type="button" class="btn btn-primary confirm-modal">Confirm</button>
      </div>
    </div>
  </div>
</div>
<div class="page-success alert alert-success hidden alert-dismissable"><span class="alert-text"></span><span
        class="close close-alert">&times;</span></div>
<div class="page-info alert alert-info hidden alert-dismissable"><span class="alert-text"></span><span
        class="close close-alert">&times;</span></div>
<div class="page-warning alert alert-warning hidden alert-dismissable"><span class="alert-text"></span><span
        class="close close-alert">&times;</span></div>
<div class="page-danger alert alert-danger hidden alert-dismissable"><span class="alert-text"></span><span
        class="close close-alert">&times;</span></div>
<script src="<?php echo base_url(); ?>assets/js/lib/bootstrap.min.js"></script> 
<script src="<?php echo base_url(); ?>assets/js/lib/moment.js"></script> 
<script src="<?php echo base_url(); ?>assets/js/lib/bootstrap-datetimepicker.js"></script> 
<script src="<?php echo base_url(); ?>assets/js/lib/bootstrap-select.min.js"></script> 
<script src="<?php echo base_url(); ?>assets/js/lib/bootstrap-slider.js"></script> 
<script src="<?php echo base_url(); ?>assets/js/plugins/DataTables/js/jquery.dataTables.min.js"></script> 
<script src="<?php echo base_url(); ?>assets/js/plugins/DataTables/js/dataTables.bootstrap.js"></script> 
<script src="<?php echo base_url(); ?>assets/js/plugins/mmenu/jquery.mmenu.min.all.js"></script> 
<script src="<?php echo base_url(); ?>assets/js/plugins/browser/jquery.browser.min.js"></script> 
<script src="<?php echo base_url(); ?>assets/js/main.js"></script> 
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script> 
<script type="text/javascript"> helper.baseUrl = '<?php echo base_url(); ?>' + '';
    <?php if(isset($_SESSION['user_id'])){ ?>
    check_session();
    var refreshIntervalId;
    function check_session() {
        if (refreshIntervalId) {
            clearInterval(refreshIntervalId);
        }
        $.getJSON(helper.baseUrl + 'user/check_session', function (response) {
            <?php if($show_footer&&isset($_SESSION['current_campaign'])){ ?>
            $('.footer-stats').empty();
            $.each(response, function (name, count) {
                $('.footer-stats').append('<div>' + name + ': ' + count + '</div>');
            });
            //var start = new Date;
            /* we are not using the live rate features on the system
             refreshIntervalId = setInterval(function() {
             elapsed_seconds = ((new Date - start)/1000)+Number(response.duration)
             $('#time_box').text(get_elapsed_time_string(elapsed_seconds));
             rate = response.transfers/(elapsed_seconds/60/60);
             $('#rate_box').text(rate.toFixed(2)+ ' per hour');
             }, 1000);

             $('#time_box').fadeIn(800);
             $('#rate_box').fadeIn(800);
             */
            <?php } ?>
        });
    }
           $(document).on('change', '#campaign-select', function () {
        $.ajax({url:helper.baseUrl + 'user/current_campaign/' + $(this).val(),
		beforeSend:function(){
		        $('[data-id="campaign-select"]').append('<span style="position:absolute; right:5px;" ><img src="'+helper.baseUrl+'assets/img/small-loading.gif" /></span>');
		$('[data-id="campaign-select"]').find('.caret').hide();	
		}
		}).done(function (response) {
            if (response.location == "dashboard") {
                window.location = helper.baseUrl + 'dashboard';
            } else {
                <?php if($this->uri->segment(2)=="detail"||$this->uri->segment(1)=="error"){ ?>
                window.location = helper.baseUrl + 'records/detail';
                <?php } else { ?>
                location.reload();
                <?php } ?>
            }
        });
    });
    <?php } ?>
</script> 
<script>
    $(document).ready(function(){
        browser.init()
    });
</script> 
<script type="text/javascript">
			$(function() {
				$('nav#menu').mmenu();

			});
		</script>
<?php //load specific javascript files set in the controller
if (isset($javascript)):
    foreach ($javascript as $file): ?>
<script src="<?php echo base_url(); ?>assets/js/<?php echo $file ?>"></script>
<?php endforeach;
endif; ?>
</body>
</html>
