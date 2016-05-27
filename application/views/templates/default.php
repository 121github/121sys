<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
    <title><?php echo $title; ?></title>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/bootstrap.css">
    <!-- Optional theme -->
    <link id="theme-css" type="text/css" rel="stylesheet"
          href="<?php echo base_url(); ?>assets/themes/colors/<?php echo(isset($_SESSION['theme_color']) ? $_SESSION['theme_color'] : $theme); ?>/bootstrap-theme.css">
    <link rel="stylesheet" type="text/css"
          href="<?php echo base_url(); ?>assets/css/plugins/dataTables/datatables.min.css">
    <!-- Latest compiled and minified JavaScript -->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/bootstrap-datetimepicker.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/datepicker3.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/bootstrap-select.css?v1.1">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/slider.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/default.css">
    <link rel="stylesheet" type="text/css"
          href="<?php echo base_url(); ?>assets/js/plugins/mmenu2/core/css/jquery.mmenu.all.css">
    <link rel="stylesheet" type="text/css"
          href="<?php echo base_url(); ?>assets/css/plugins/dataTables/css/font-awesome.css">
    <!-- Set the baseUrl in the JavaScript helper -->
    <?php //load specific css files set in the controller
    if (isset($css)):
        foreach ($css as $file): ?>
            <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/css/<?php echo $file ?>">
        <?php endforeach;
    endif; ?>
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

    <link rel="shortcut icon"
          href="<?php echo base_url(); ?>assets/themes/images/<?php echo(isset($_SESSION['theme_images']) ? $_SESSION['theme_images'] : "default"); ?>/icon.png">
    <script src="<?php echo base_url(); ?>assets/js/lib/jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/lib/jquery-ui-1.9.2.custom.min.js"></script>
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

<?php if (isset($_SESSION['permissions'])) { ?>

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
</div>
<script src="<?php echo base_url(); ?>assets/js/lib/wavsurfer.js"></script>
<script type="text/javascript"
        src="<?php echo base_url() ?>assets/js/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/lib/bootstrap.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/lib/moment.js"></script>
<script src="<?php echo base_url(); ?>assets/js/lib/bootstrap-datetimepicker.js"></script>
<script src="<?php echo base_url(); ?>assets/js/lib/bootstrap-select.js"></script>
<script src="<?php echo base_url(); ?>assets/js/plugins/mmenu2/core/js/jquery.mmenu.min.all.js"></script>
<script src="<?php echo base_url(); ?>assets/js/plugins/browser/jquery.browser.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/browser.js"></script>
<script src="<?php echo base_url() . "assets/js/modals.js?v" . $this->config->item('project_version'); ?>"></script>
<?php if (isset($_SESSION['user_id'])) { ?>
    <script src="<?php echo base_url() . "assets/js/main.js?v" . $this->config->item('project_version'); ?>"></script>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <!-- Campaign triggers-->
<?php if (!empty($campaign_triggers)) { ?>
    <?php foreach ($campaign_triggers as $script) { ?>
    <script
        src="<?php echo base_url() . "custom_scripts/" . $script['path'] . "?v" . $this->config->item('project_version'); ?>"></script>
<?php } ?>
<?php } else { ?>
    <script type="text/javascript">campaign_functions = {};</script>
<?php } ?>
    <!-- End of campaign triggers-->
    <script type="text/javascript">
        modals.init();
        <?php if(isset($_SESSION['user_id'])){ ?>
        var custom_appointment_modal = false;
        var custom_record_modal = false;
        var refreshIntervalId;
        check_session();
        <?php } ?>
    </script>
    <script
        src="<?php echo base_url() . "assets/js/record_update.js?v" . $this->config->item('project_version'); ?>"></script>
<?php } ?>
<?php
//load specific javascript files set in the controller
if (isset($javascript)):
foreach ($javascript as $file): ?>
    <script src="<?php echo base_url(); ?>assets/js/<?php echo $file ?>"></script>
<?php endforeach;
?>

<?php if (@in_array("map.js?v" . $this->config->item('project_version'), $javascript) || @in_array("location.js?v" . $this->config->item('project_version'), $javascript)) { ?>
<?php if (@in_array("map.js?v" . $this->config->item('project_version'), $javascript)) {
    $callback = "&callback=initializemaps";
}
?>
    <script
        type="text/javascript"
        src="https://maps.googleapis.com/maps/api/js?v=3<?php echo isset($callback) ? $callback : "" ?>"></script>
<?php }
endif;
?>


<?php if (isset($_SESSION['user_id'])) { ?>
    <div id="color-box" class="Fixed">
        <a href="#"><span class="glyphicon glyphicon-cog color-btn"></span></a>
    </div>
    <script type="text/javascript" src="<?php echo base_url() ?>assets/js/preferences.js"></script>
<?php } ?>

<div id="quick-actions-box" class="Fixed">
    <a href="#quick-actions-right">
        <span class="fa fa-caret-left quick-actions-btn"></span>
    </a>
</div>

</body>
</html>