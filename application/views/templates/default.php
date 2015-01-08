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
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<title><?php echo $title; ?></title>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.css">
<!-- Optional theme -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/themes/<?php echo (isset($_SESSION['theme_folder'])?$_SESSION['theme_folder']:"default"); ?>/bootstrap-theme.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/plugins/dataTables/css/dataTables.bootstrap.css">
<!-- Latest compiled and minified JavaScript -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-datetimepicker.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/datepicker3.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-select.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/slider.css">
<link rel="stylesheet"  href="<?php echo base_url(); ?>assets/css/default.css">
<!-- Set the baseUrl in the JavaScript helper -->
<?php  //load specific javascript files set in the controller
		if(isset($css)): 
		foreach($css as $file): ?>
<link rel="stylesheet"  href="<?php echo base_url(); ?>assets/css/<?php echo $file ?>">
<?php endforeach;
		endif;  ?>
<link rel="shortcut icon" href="<?php echo base_url(); ?>assets/themes/<?php echo (isset($_SESSION['theme_folder'])?$_SESSION['theme_folder']:"default"); ?>/icon.png">
<script src="<?php echo base_url(); ?>assets/js/lib/jquery.min.js"></script>
<!--Need to make a new icon for this
          <link rel="apple-touch-icon" href="<?php echo base_url(); ?>assets/img/apple-touch-icon.png" />-->
</head>
<body>
<div id="<?php echo $pageId; ?>" class="page">
  <div class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
      <div class="navbar-header">
        <button data-target=".navbar-collapse" data-toggle="collapse" class="navbar-toggle collapsed" type="button"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
        <?php if(!isset($_SESSION['permissions'])||count($_SESSION['campaign_access']['array'])<2||in_array("search campaigns",$_SESSION['permissions'])){ ?>
        <a href="#" class="navbar-brand"><img style="margin-top:-5px;" src="<?php echo base_url(); ?>assets/themes/<?php echo (isset($_SESSION['theme_folder'])?$_SESSION['theme_folder']:"default"); ?>/logo.png"></a>
        <?php } else { ?>
        <span style="position:absolute;top:8px"><img style="margin-top:-10px; margin-right:5px;" src="<?php echo base_url(); ?>assets/themes/<?php echo (isset($_SESSION['theme_folder'])?$_SESSION['theme_folder']:"default"); ?>/small-logo.png">
        <?php if(isset($campaign_access)&&count($campaign_access)>0){ ?>
        <select class="selectpicker" id="campaign-select">
          <option value="">Select a campaign to begin</option>
          <?php foreach($campaign_access as $client => $camp_array){ ?>
          <optgroup label="<?php echo $client ?>">
          <?php foreach($camp_array as $camp){ ?>
          <option <?php if(isset($_SESSION['current_campaign'])&&$_SESSION['current_campaign']==$camp['id']){ echo "selected"; } ?> value="<?php echo $camp['id'] ?>"><?php echo $camp['name'] ?></option>
          <?php } ?>
          </optgroup>
          <?php } ?>
        </select>
        
        <img style="margin-top:-5px; display:none" id="campaign-loading-icon" src="<?php echo base_url(); ?>assets/img/ajax-loading.gif"/></span>
        <?php } ?>
        <?php } ?>
      </div>
      <div class="navbar-collapse collapse pull-right">
        <ul class="nav navbar-nav">
          <?php if(isset($_SESSION['user_id'])): ?>
              <?php if(in_array("admin nav",$_SESSION['permissions'])){ ?>
                  <li class="dropdown <?php if($this->uri->segment(1)=="admin"){ echo "active"; } ?>" > <a data-toggle="dropdown" class="dropdown-toggle" href="<?php echo base_url(); ?>survey/view">Admin <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="trigger right-caret">Data</a>
                            <ul class="dropdown-menu sub-menu">
                                <li> <a href="<?php echo base_url() ?>import" <?php echo @($inner=='import'?"class='active'":"") ?>>Import</a></li>
                                <li><a href="<?php echo base_url() ?>exports" <?php echo @($inner=='export'?"class='active'":"") ?>>Export</a></li>
                                <?php if(in_array("reassign data",$_SESSION['permissions'])){ ?>
                                    <li><a href="<?php echo base_url() ?>data/management" <?php echo @($inner=='management'?"class='active'":"") ?>>Data Management</a></li>
                                <?php } ?>
                                <li><a href="<?php echo base_url() ?>data/add_record" <?php echo @($inner=='add_record'?"class='active'":"") ?>>Add Record</a></li>
                                <li><a href="<?php echo base_url() ?>data/daily_ration" <?php echo @($inner=='daily_ration'?"class='active'":"") ?>>Daily Ration</a></li>
                                <li><a href="<?php echo base_url() ?>data/backup_restore" <?php echo @($inner=='backup_restore'?"class='active'":"") ?>>Backup and Restore</a></li>
                            </ul>
                        </li>
                        <li>
                            <a class="trigger right-caret">Campaigns</a>
                            <ul class="dropdown-menu sub-menu">
                                <?php if(in_array("campaign access",$_SESSION['permissions'])){ ?>
                                    <li><a href="<?php echo base_url() ?>admin/campaigns" <?php echo @($inner=='campaign'?"class='active'":"") ?>>Campaign Setup</a></li>
                                <?php } ?>
                                <li><a href="<?php echo base_url() ?>admin/campaign_fields" <?php echo @($inner=='custom_fields'?"class='active'":"") ?>>Campaign Fields</a></li>
                                <?php if(in_array("edit templates",$_SESSION['permissions'])){ ?>
                                    <li><a href="<?php echo base_url() ?>templates" <?php echo @($inner=='templates'?"class='active'":"") ?>>Templates</a></li>
                                <?php } ?>
                                <?php if(in_array("edit scripts",$_SESSION['permissions'])){ ?>
                                    <li><a href="<?php echo base_url() ?>scripts" <?php echo @($inner=='scripts'?"class='active'":"") ?>>Scripts</a></li>
                                <?php } ?>
                            </ul>
                        </li>
                        <?php if($_SESSION['group']=="1"&&$_SESSION['role']=="1"){ ?>
                            <li><a href="<?php echo base_url() ?>admin/users" <?php echo @($admin=='users'?"class='active'":"") ?>>Users</a></li>
                            <li><a href="<?php echo base_url() ?>admin/roles" <?php echo @($admin=='roles'?"class='active'":"") ?>>Roles</a></li>
                            <li><a href="<?php echo base_url() ?>admin/teams" <?php echo @($admin=='teams'?"class='active'":"") ?>>Teams</a></li>
                            <li><a href="<?php echo base_url() ?>admin/groups" <?php echo @($admin=='groups'?"class='active'":"") ?>>Groups</a></li>
                        <?php } ?>
                        <?php if(in_array("view logs",$_SESSION['permissions'])){ ?>
                            <li><a href="<?php echo base_url() ?>admin/logs" <?php echo @($admin=='logs'?"class='active'":"") ?>>Logs</a></li>
                        <?php } ?>
                        <li>
                            <a class="trigger right-caret">Hours</a>
                            <ul class="dropdown-menu sub-menu">
                                <?php if(in_array("view hours",$_SESSION['permissions'])){ ?>
                                <li><a href="<?php echo base_url() ?>hour/default_hours" <?php echo @($inner=='default_hours'?"class='active'":"") ?>>Default Hours</a></li>
                                <li><a href="<?php echo base_url() ?>hour/hours" <?php echo @($inner=='hours'?"class='active'":"") ?>>Agent Hours</a></li>
                            </ul>
                        </li>
                        <li>
                            <a class="trigger right-caret">Time</a>
                            <ul class="dropdown-menu sub-menu">
                                <li><a href="<?php echo base_url() ?>time/default_time" <?php echo @($inner=='default_time'?"class='active'":"") ?>>Default Time</a></li>
                                <li><a href="<?php echo base_url() ?>time/agent_time" <?php echo @($inner=='agent_time'?"class='active'":"") ?>>Agent Time</a></li>
                                <?php } ?>
                            </ul>
                        </li>
                    </ul>
                  </li>
              <?php } ?>
              <?php if(in_array("view reports",$_SESSION['permissions'])&&in_array("agent reporting",$_SESSION['permissions'])){ ?>
                  <li class="dropdown <?php if($this->uri->segment(1)=="report"){ echo "active"; } ?>" > <a data-toggle="dropdown" class="dropdown-toggle" href="<?php echo base_url(); ?>survey/view">Reports <b class="caret"></b></a>
                      <ul class="dropdown-menu">
                          <li><a href="<?php echo base_url() ?>reports/answers" <?php echo @($reports=='answers'?"class='active'":"") ?>>Survey Answers</a></li>
                          <li><a href="<?php echo base_url() ?>reports/activity" <?php echo @($reports=='activity'?"class='active'":"") ?>>Activity</a></li>
                          <li>
                              <a class="trigger right-caret">Transfers</a>
                              <ul class="dropdown-menu sub-menu">
                                  <li> <a href="<?php echo base_url() ?>reports/campaigntransfer" <?php echo @($inner=='campaigntransfer'?"class='active'":"") ?>>By Campaign</a></li>
                                  <li><a href="<?php echo base_url() ?>reports/agenttransfer" <?php echo @($inner=='agenttransfer'?"class='active'":"") ?>>By Agent</a></li>
                                  <li><a href="<?php echo base_url() ?>reports/dailytransfer" <?php echo @($inner=='dailytransfer'?"class='active'":"") ?>>By Date</a></li>
                                  <li><a href="<?php echo base_url() ?>reports/timetransfer" <?php echo @($inner=='dailytransfer'?"class='active'":"") ?>>By Time</a></li>
                              </ul>
                          </li>
                          <li>
                              <a class="trigger right-caret">Outcomes</a>
                              <ul class="dropdown-menu sub-menu">
                                  <li><a href="<?php echo base_url() ?>reports/outcomes/campaign/1" <?php echo @($inner=='campaign'?"class='active'":"") ?>>By Campaign</a></li>
                                  <li><a href="<?php echo base_url() ?>reports/outcomes/agent/1" <?php echo @($inner=='agent'?"class='active'":"") ?>>By Agent</a></li>
                                  <li><a href="<?php echo base_url() ?>reports/outcomes/date/1" <?php echo @($inner=='date'?"class='active'":"") ?>>By Date</a></li>
                                  <li><a href="<?php echo base_url() ?>reports/outcomes/time/1" <?php echo @($inner=='campaign'?"class='active'":"") ?>>By Time</a></li>
                              </ul>
                          </li>
                          <li>
                              <a class="trigger right-caret">Emails</a>
                              <ul class="dropdown-menu sub-menu">
                                  <li> <a href="<?php echo base_url() ?>reports/email/campaign/1" <?php echo @($inner=='campaign'?"class='active'":"") ?>>By Campaign</a></li>
                                  <li><a href="<?php echo base_url() ?>reports/email/agent/1" <?php echo @($inner=='agent'?"class='active'":"") ?>>By Agent</a></li>
                                  <li><a href="<?php echo base_url() ?>reports/email/date/1" <?php echo @($inner=='date'?"class='active'":"") ?>>By Date</a></li>
                                  <li><a href="<?php echo base_url() ?>reports/email/time/1" <?php echo @($inner=='time'?"class='active'":"") ?>>By Time</a></li>
                              </ul>
                          </li>
                      </ul>
                  </li>
              <?php } ?>
              <li class="dropdown <?php if($this->uri->segment(1)=="dashboard"){ echo "active"; } ?>" > <a data-toggle="dropdown" class="dropdown-toggle" href="<?php echo base_url(); ?>survey/view">Dashboard <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="<?php echo base_url(); ?>dashboard">Overview</a></li>
                  <?php if(in_array("client dash",$_SESSION['permissions'])){ ?>
                  <li><a href="<?php echo base_url(); ?>dashboard/client">Client Dash</a></li>
                  <?php } ?>
                  <?php if(in_array("agent dash",$_SESSION['permissions'])){ ?>
                  <li><a href="<?php echo base_url(); ?>dashboard/agent">Agent Dash</a></li>
                  <?php } ?>
                  <?php if(in_array("management dash",$_SESSION['permissions'])){ ?>
                  <li><a href="<?php echo base_url(); ?>dashboard/management">Management Dash</a></li>
                  <?php } ?>
                </ul>
              </li>
              <?php if(isset($_SESSION['current_campaign'])&&@in_array("set call outcomes",$_SESSION['permissions'])||@in_array("search campaigns",$_SESSION['permissions'])){  ?>
              <li <?php if($this->uri->segment(1)=="records"&&!isset($automatic)){ echo "class='active'"; } ?>><a href="<?php echo base_url(); ?>records/view" >List Records</a></li>
              <?php } ?>
              <?php if(in_array("search records",$_SESSION['permissions'])&&isset($_SESSION['current_campaign'])||in_array("search campaigns",$_SESSION['permissions'])||@in_array("Search Page",$_SESSION['campaign_features'])){ ?>
              <li <?php if($this->uri->segment(1)=="search"){ echo "class='active'"; } ?>><a href="<?php echo base_url(); ?>search" class="hreflink">Search Records</a></li>
              <?php } ?>
              <?php if(isset($_SESSION['current_campaign'])&&in_array("set call outcomes",$_SESSION['permissions'])){  ?>
              <li <?php if($this->uri->segment(2)=="detail"){ echo "class='active'"; } ?>><a href="<?php echo base_url(); ?>records/detail" >Start Calling</a></li>
              <?php } ?>
              <?php if(in_array('calendar nav',$_SESSION['permissions'])&&in_array('search campaigns',$_SESSION['permissions'])||isset($_SESSION['current_campaign'])&&isset($_SESSION['campaign_features'])&&in_array('Appointment Setting',$_SESSION['campaign_features'])&&in_array("calendar nav",$_SESSION['permissions'])){ ?>
              <li <?php if($this->uri->segment(1)=="calendar"){ echo "class='active'"; } ?>><a href="<?php echo base_url(); ?>calendar" >Calendar</a></li>
              <?php } ?>
              <?php if(isset($_SESSION['current_campaign'])&&isset($_SESSION['campaign_features'])&&in_array('Surveys',$_SESSION['campaign_features'])&&in_array("search surveys",$_SESSION['permissions'])){ ?>
              <li class="dropdown <?php if($this->uri->segment(1)=="survey"){ echo "active"; } ?>" > <a data-toggle="dropdown" class="dropdown-toggle" href="<?php echo base_url(); ?>survey/view">Surveys <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="<?php echo base_url(); ?>survey/view">View Surveys</a></li>
                </ul>
              </li>
              <?php } ?>

              <li class="dropdown"><a data-toggle="dropdown" class="dropdown-toggle" href="#" style="color:#fff">Hello, <?php echo $_SESSION['name'] ?> <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="<?php echo base_url(); ?>user/account" class="hreflink">My Account</a></li>
                  <li><a href="<?php echo base_url(); ?>user/logout" class="hreflink">Logout</a></li>
                </ul>
              </li>

          <?php endif; ?>
        </ul>
      </div>
      <!--/.nav-collapse --> 
    </div>
  </div>
</div>
<div  class="img-circle" id="timerclosed" style="display: none;">
    <span class="glyphicon glyphicon-earphone opentimer pointer"></span>
    <span class="glyphicon glyphicon-stop stoptimer pointer"></span>
</div>
<div class="img-circle" id="timeropened" style="display: none;">
    <div id="defaultCountdown"></div>
    <span class="glyphicon glyphicon-earphone closetimer pointer"></span>
    <span class="glyphicon glyphicon-stop stoptimer pointer"></span>
</div>
<?php if($show_footer){ ?>
<div class="navbar-inverse footer-stats">
  <div>Current Rate: <span id="rate_box">0</span></div>
  <div><span id="transfers">Transfers</span>: <span id="transfers_box">0</span></div>
  <div>Records worked: <span id="worked_box">0</span></div>
  <div>Time on this campaign: <span id="time_box">00:00:00</span></div>
</div>
<?php } ?>
<div class="container-fluid" <?php if($show_footer){ ?>style="padding-bottom:50px"<?php } ?>> <?php echo $body; ?></div>

<!-- /content --> 

<!-- Modal -->
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Please confirm</h4>
      </div>
      <div class="modal-body"> </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default close-modal pull-left" data-dismiss="modal">Close</button>
        <button type="button" style="display: none;" class="btn btn-danger discard-modal">No</button>
        <button type="button" style="display: none;" class="btn btn-primary save-modal">Save</button>
        <button type="button" class="btn btn-primary confirm-modal">Confirm</button>
      </div>
    </div>
  </div>
</div>
<div class="page-success alert alert-success hidden alert-dismissable"><span class="alert-text"></span><span class="close close-alert">&times;</span> </div>
<div class="page-info alert alert-info hidden alert-dismissable"><span class="alert-text"></span><span class="close close-alert">&times;</span> </div>
<div class="page-warning alert alert-warning hidden alert-dismissable"><span class="alert-text"></span><span class="close close-alert">&times;</span> </div>
<div class="page-danger alert alert-danger hidden alert-dismissable"><span class="alert-text"></span><span class="close close-alert">&times;</span> </div>
<script src="<?php echo base_url(); ?>assets/js/lib/bootstrap.min.js"></script> 
<script src="<?php echo base_url(); ?>assets/js/lib/moment.js"></script> 
<script src="<?php echo base_url(); ?>assets/js/lib/bootstrap-datetimepicker.js"></script> 
<script src="<?php echo base_url(); ?>assets/js/lib/bootstrap-select.min.js"></script> 
<script src="<?php echo base_url(); ?>assets/js/lib/bootstrap-slider.js"></script> 
<script src="<?php echo base_url(); ?>assets/js/plugins/DataTables/js/jquery.dataTables.min.js"></script> 
<script src="<?php echo base_url(); ?>assets/js/plugins/DataTables/js/dataTables.bootstrap.js"></script> 
<script src="<?php echo base_url(); ?>assets/js/main.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script> 
<script type="text/javascript"> helper.baseUrl = '<?php echo base_url(); ?>' + ''; 
<?php if(isset($_SESSION['user_id'])){ ?>
check_session();	
var refreshIntervalId;
function check_session(){
	if (refreshIntervalId) {
		clearInterval(refreshIntervalId);	
	}
$.getJSON(helper.baseUrl+'user/check_session',function(response){
	<?php if($show_footer&&isset($_SESSION['current_campaign'])){ ?>
		if(response.positive_outcome.length>0){
		$('#transfers_box').text(response.transfers);
		$('#worked_box').text(response.worked);
		$('#rate_box').text(response.rate+ ' per hour');
		}
		var start = new Date;
	
		refreshIntervalId = setInterval(function() {
		  elapsed_seconds = ((new Date - start)/1000)+Number(response.duration)
		  $('#time_box').text(get_elapsed_time_string(elapsed_seconds));
		  rate = response.transfers/(elapsed_seconds/60/60);
		  $('#rate_box').text(rate.toFixed(2)+ ' per hour');
		}, 1000);
		
		$('#time_box').fadeIn(800);
		$('#rate_box').fadeIn(800);
	<?php } ?>
});	
	}
$(document).on('change','#campaign-select',function(){
	$('#campaign-loading-icon').show();
	$.get(helper.baseUrl+'user/current_campaign/'+$(this).val(),function(response){
			<?php if($this->uri->segment(2)=="detail"||$this->uri->segment(1)=="error"){ ?>
			window.location = helper.baseUrl+'records/detail';
			<?php } else { ?>
			location.reload();
			<?php } ?>
	});
});
<?php } ?>
</script>
<?php  //load specific javascript files set in the controller
		if(isset($javascript)): 
		foreach($javascript as $file): ?>
<script src="<?php echo base_url(); ?>assets/js/<?php echo $file ?>"></script>
<?php endforeach;
		endif;  ?>
</body>
</html>
