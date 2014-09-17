<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<title><?php echo $title; ?></title>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.css">
<!-- Optional theme -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-theme.css">
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
<link rel="shortcut icon" href="<?php echo base_url(); ?>assets/img/logo.ico">
<script src="<?php echo base_url(); ?>assets/js/lib/jquery.min.js"></script>

<!-- Only load these files when they are needed ??!! DONT FORGET ME -->
<!--Need to make a new icon for this
          <link rel="apple-touch-icon" href="<?php echo base_url(); ?>assets/img/apple-touch-icon.png" />-->
</head>
<body>
<div id="<?php echo $pageId; ?>" class="page">
<div class="navbar navbar-inverse">
  <div class="container">
    <div class="navbar-header">
      <button data-target=".navbar-collapse" data-toggle="collapse" class="navbar-toggle collapsed" type="button"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
      <a href="#" class="navbar-brand"><img style="margin-top:-10px;" src="<?php echo base_url(); ?>assets/img/logo.png"></a> </div>
    <div class="navbar-collapse collapse pull-right">
      <ul class="nav navbar-nav">
        <?php if(isset($_SESSION['user_id'])): ?>
        <li <?php if($this->uri->segment(1)=="dashboard"){ echo "class='active'"; } ?>><a  href="<?php echo base_url(); ?>dashboard">Dashboard</a></li>
        <li <?php if($this->uri->segment(1)=="records"){ echo "class='active'"; } ?>><a href="<?php echo base_url(); ?>records/view" class="hreflink">List Records</a></li>
        <li><a href="<?php echo base_url(); ?>search" class="hreflink">Search Records</a></li>
        <li class="dropdown"> <a data-toggle="dropdown" class="dropdown-toggle" href="<?php echo base_url(); ?>survey/view">Surveys <b class="caret"></b></a>
          <ul class="dropdown-menu">
            <li><a href="<?php echo base_url(); ?>survey/view">View Surveys</a></li>
          </ul>
        </li>
        <li><a href="<?php echo base_url(); ?>user/account" class="hreflink">My Account</a></li>
        <li><a href="<?php echo base_url(); ?>user/logout" class="hreflink">Logout</a></li>
        <?php endif; ?>
      </ul>
    </div>
    <!--/.nav-collapse --> 
  </div>
</div>
<div class="container-fluid"> <?php echo $body; ?> </div>
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
        <button type="button" class="btn btn-default close-modal" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary confirm-modal">Confirm</button>
      </div>
    </div>
  </div>
</div>
<div class="page-success alert alert-success hidden alert-dismissable"><span class="alert-text"></span><span class="close close-alert">&times;</span>
</div>
<div class="page-info alert alert-info hidden alert-dismissable"><span class="alert-text"></span><span class="close close-alert">&times;</span>
</div>
<div class="page-warning alert alert-warning hidden alert-dismissable"><span class="alert-text"></span><span class="close close-alert">&times;</span>
</div>
<div class="page-danger alert alert-danger hidden alert-dismissable"><span class="alert-text"></span><span class="close close-alert">&times;</span>
</div>

<script src="<?php echo base_url(); ?>assets/js/lib/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/lib/moment.js"></script>
<script src="<?php echo base_url(); ?>assets/js/lib/bootstrap-datetimepicker.js"></script>
<script src="<?php echo base_url(); ?>assets/js/lib/bootstrap-select.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/lib/bootstrap-slider.js"></script>
<script src="<?php echo base_url(); ?>assets/js/plugins/DataTables/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/plugins/DataTables/js/dataTables.bootstrap.js"></script>
<script src="<?php echo base_url(); ?>assets/js/main.js"></script>
<script type="text/javascript"> helper.baseUrl = '<?php echo base_url(); ?>' + ''; </script>
<?php  //load specific javascript files set in the controller
		if(isset($javascript)): 
		foreach($javascript as $file): ?>
<script src="<?php echo base_url(); ?>assets/js/<?php echo $file ?>"></script>
<?php endforeach;
		endif;  ?>
</body>
</html>
