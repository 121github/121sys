<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <title><?php echo $title; ?></title>
  <link rel="shortcut icon"
          href="<?php echo base_url(); ?>assets/themes/images/<?php echo(isset($_SESSION['theme_images']) ? $_SESSION['theme_images'] : "default"); ?>/icon.png">
<?php
// array[PATH]=>array(FILE);
$css_path = "assets/css";
$css_files=array("bootstrap.css",
"bootstrap-datetimepicker.css",
"datepicker3.css",
"bootstrap-select.css",
"slider.css",
"default.css",
"plugins/mmenu2/jquery.mmenu.all.css",
"plugins/dataTables/datatables.min.css",
"plugins/dataTables/css/font-awesome.css",
);
 if (isset($css)):
        foreach ($css as $file): 
		 $css_files[] = $file;
		endforeach;
endif;
?>
 <link rel="stylesheet" type="text/css" href="<?php echo minify($css_path,$css_files)?>" />
<?php $css_path = "assets";
$css_files=array("themes/colors/".(isset($_SESSION['theme_color']) ? $_SESSION['theme_color'] : $theme)."/bootstrap-theme.css");
?>
 <link rel="stylesheet" type="text/css" href="<?php echo minify($css_path,$css_files)?>" />
    <?php if (isset($submenu)) { ?>
        <style>
            .container-fluid {
                padding: 110px 15px 60px;
            }

            .navbar-nav {
                margin: 0 !important;
            }

            .navbar-nav .navbar-text {
                margin-left: 15px !important
            }
        </style>
    <?php } ?>
    <?php 
$js_path = "assets/js";
$js_files=array("lib/jquery.min.js",
"lib/jquery-ui-1.9.2.custom.min.js"
);
?>
<script src="<?php echo minify($js_path,$js_files) ?>"></script>
    <script type="text/javascript">
        helper = [];
        helper.baseUrl = '<?php echo base_url(); ?>';
        <?php if(isset($_SESSION['user_id'])){ ?>
        helper.home = '<?php echo $_SESSION['home'] ?>';
        helper.campaign_name = '<?php echo isset($_SESSION['current_campaign_name']) ? $_SESSION['current_campaign_name'] : "" ?>';
        helper.campaign_id = '<?php echo isset($_SESSION['current_campaign']) ? $_SESSION['current_campaign'] : false ?>';
        helper.user_id = '<?php echo $_SESSION['user_id'] ?>';
        helper.theme_color = '<?php echo $_SESSION['theme_color'] ?>';
        helper.permissions = $.parseJSON('<?php echo json_encode(array_flip($_SESSION['permissions'])) ?>');
        helper.role = '<?php echo $_SESSION['role'] ?>';
        helper.current_postcode = false;
        <?php if(isset($_SESSION['current_postcode'])){ ?>
        helper.current_postcode = "<?php echo $_SESSION['current_postcode'] ?>";
        <?php } ?>
        <?php } ?>
    </script>
  

</head>
<body>

<?php if (isset($_SESSION['user_id'])) { ?>

<nav id="quick-actions-right" style="display:none; text-align:center; z-index: 110" class="mm-menu mm--horizontal mm-offcanvas">
     <?php $this->view('misc/quick_actions.php'); ?>
</nav>
    <?php $this->view('misc/timer.php'); ?>
    <?php if (isset($submenu)) { ?>
        <?php $this->view('submenus/' . $submenu['file'], $submenu); ?>
    <?php } ?>

    <?php $this->view('navigation/topbar/main.php', array("campaign_access" => $campaign_access, "page" => $page)); ?>

    <?php $this->view('navigation/sidebar/main.php', array("campaign_access" => $campaign_access, "page" => $page)); ?>
    <?php if (isset($global_filter)) {
        $this->view('forms/global_filter.php', $global_filter);
    } ?>
     <?php if (isset($report_filter)) {
        $this->view('forms/report_filter.php', $report_filter);
    } ?>
       <?php if (isset($dashboard_filter)) {
        $this->view('forms/dashboard_filter.php', $dashboard_filter);
    } ?>
<?php } ?>


<div class="container-fluid" id="container-fluid">
    <?php $this->view('misc/top_alerts.php'); ?>
    <?php echo $body; ?>
</div>
<!-- /content -->
<!-- Modal -->
<div class="isFixed">
    <?php $this->view('misc/alerts.php'); ?>
    <?php $this->view('misc/modal.php'); ?>
     <?php $this->view('misc/footer.php'); ?>
     <?php if(isset($_SESSION['user_id'])){ $this->view('misc/side_boxes.php'); } ?>
</div>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">campaign_functions = {};</script>
<?php if(isset($_SESSION['user_id'])){ 
$js_files=array("lib/wavsurfer.js",
"plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js",
"lib/bootstrap.min.js",
"lib/moment.js",
"lib/bootstrap-datetimepicker.js",
"lib/bootstrap-select.js",
"plugins/mmenu2/core/js/jquery.mmenu.min.all.js",
"modals.js",
"main.js",
"record_update.js",
"preferences.js",
);
if (!empty($campaign_triggers)) { 
     foreach ($campaign_triggers as $script) {
		$js_files[]=$script; 
	 } 
}
if (isset($javascript)) { 
     foreach ($javascript as $script) {
		$js_files[]=$script; 
	 } 
}
?>
<script src="<?php echo minify($js_path,$js_files) ?>"></script>
<script type="text/javascript">
        modals.init();
        var custom_appointment_modal = false;
        var custom_record_modal = false;
        var refreshIntervalId;
        check_session();
    </script>

<?php if (@in_array("map.js?v" . $this->config->item('project_version'), $javascript) || @in_array("location.js?v" . $this->config->item('project_version'), $javascript)) { ?>
<?php if (@in_array("map.js?v" . $this->config->item('project_version'), $javascript)) {
    $callback = "&callback=initializemaps";
}
?>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3<?php echo isset($callback) ? $callback : "" ?>"></script>
<?php } ?>
<div id="quick-actions-box" class="Fixed">
    <a href="#quick-actions-right">
        <span class="fa fa-caret-left quick-actions-btn"></span>
    </a>
</div>
    <div id="color-box" class="Fixed">
        <a href="#"><span class="glyphicon glyphicon-cog color-btn"></span></a>
    </div>

<?php } ?>


</body>
</html>