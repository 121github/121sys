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
    <link id="theme-css" rel="stylesheet"
          href="<?php echo base_url(); ?>assets/themes/colors/<?php echo(isset($_SESSION['theme_color']) ? $_SESSION['theme_color'] : $theme); ?>/bootstrap-theme.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/plugins/dataTables/datatables.min.css">
    <!-- Latest compiled and minified JavaScript -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-datetimepicker.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/datepicker3.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-select.css?v1.1">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/slider.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/default.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/plugins/mmenu2/core/css/jquery.mmenu.all.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/plugins/dataTables/css/font-awesome.css">
    <!-- Set the baseUrl in the JavaScript helper -->
    <?php //load specific css files set in the controller
    if (isset($css)):
        foreach ($css as $file): ?>
            <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/<?php echo $file ?>">
        <?php endforeach;
    endif; ?>
    <?php if(isset($submenu)){ ?>
    <style>
	.container-fluid {
    padding: 110px 15px 60px;
	}
	.navbar-nav {
	margin: 0 !important;	
	}
	.navbar-nav .navbar-text { margin-left:15px !important }
	</style>
    <?php } ?>
    
    <link rel="shortcut icon"
          href="<?php echo base_url(); ?>assets/themes/images/<?php echo(isset($_SESSION['theme_images']) ? $_SESSION['theme_images'] : "default"); ?>/icon.png">
    <script src="<?php echo base_url(); ?>assets/js/lib/jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/lib/jquery-ui-1.9.2.custom.min.js"></script>
    <script type="text/javascript">
	helper = [];
	helper.baseUrl = '<?php echo base_url(); ?>' + '';
    <?php if(isset($_SESSION['permissions'])){ ?>
	helper.user_id = '<?php echo $_SESSION['user_id'] ?>';
    helper.permissions = $.parseJSON('<?php echo json_encode(array_flip($_SESSION['permissions'])) ?>');
    helper.role = '<?php echo $_SESSION['role'] ?>';
    helper.current_postcode = false;
    <?php } ?>
    <?php if(@!empty($_SESSION['current_postcode'])){ ?>
    helper.current_postcode = "<?php echo $_SESSION['current_postcode'] ?>";
    <?php } ?>
	</script>
             </head>
<body>
<div class="img-circle" id="timerclosed" style="display: none;"><span
        id="opentimer" class="glyphicon glyphicon-earphone pointer"></span> <span
        class="glyphicon glyphicon-stop stoptimer pointer"></span></div>
<div class="img-circle" id="timeropened" style="display: none;">
    <div id="defaultCountdown"></div>
    <span id="closetimer" class="glyphicon glyphicon-earphone pointer"></span> <span
        id="stoptimer" class="glyphicon glyphicon-stop pointer"></span></div>
<?php if ($show_footer) { ?>
    <div class="navbar-inverse footer-stats" style="z-index:1">
        <!--ajax generated footer stats go here -->
    </div>
<?php } ?>
<?php if(isset($submenu)){ ?>
       <?php $this->view('submenus/'.$submenu['file'],$submenu); ?>
<?php } ?>
 <?php if (isset($_SESSION['permissions'])) { ?>
<div class="navbar navbar-default navbar-fixed-top" style="padding-left:15px; z-index: 9999">
          <a href="#menu" id="nav-menu-btn" class="btn btn-default navbar-toggle mobile-only"> <span class="icon-bar"></span> <span class="icon-bar"></span> <span
                class="icon-bar"></span></a>
    <?php } ?>
    <?php if (isset($campaign_access)) { ?>
    <?php if(is_array($campaign_access)){ ?>
        <div id="top-campaign-container" <?php if(count($_SESSION['campaign_access']['array'])<3){ echo 'class="hidden"'; } ?> style="padding-top:8px; width:160px; display:none; float:left">
        <select id="top-campaign-select" class="selectpicker" data-width="160px">
            <?php if (in_array("mix campaigns", $_SESSION['permissions']) || (!isset($_SESSION['current_campaign']) && !in_array("mix campaigns", $_SESSION['permissions']))) { ?>
                <option
                    value=""><?php echo(in_array("mix campaigns", $_SESSION['permissions']) ? "Campaign Filter" : "Select a campaign to begin"); ?></option>
            <?php } ?>
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
    </div>
     <?php } ?>
    <ul class="nav navbar-nav desktop-only" id="desktop-nav">
    <?php if(in_array("search records",$_SESSION['permissions'])){ ?>
		 <li><a href="#" id='open-quicksearch'><i class="fa fa-search"></i> Search</a></li>
         <?php } ?>
           <?php if(in_array("use callpot",$_SESSION['permissions'])&&isset($_SESSION['current_campaign'])){ ?>
		 <li><a href="<?php echo base_url() ?>records/detail/0"><i class="fa fa-phone"></i> Start</a></li>
         <?php } ?>
        <?php if(in_array("add records",$_SESSION['permissions'])){ ?>
		 <li><a href="<?php echo base_url() ?>data/add_record"><i class="fa fa-plus"></i> Create</a></li>
         <?php } ?>
          <?php if(is_array($campaign_access)){ ?>
        <?php $this->view('navigation/topbar/dashboards.php', $page); ?>
        <?php } ?>
        <?php $this->view('navigation/topbar/view.php', $page); ?> 
             <?php $this->view('navigation/topbar/reports.php', $page); ?>  
               <?php $this->view('navigation/topbar/admin.php', $page); ?>  
               <?php $this->view('navigation/topbar/account.php', $page); ?>           
      </ul>
     
    <?php if ($_SESSION['environment'] == 'demo') { ?>
        <span style="color: red; margin-left: 10%; background-color: yellow">This is a demo system. The data added could be deleted at any time!!</span>
    <?php } ?>
    <a href="#" class="navbar-brand pull-right"><img id="small-logo" style="margin-top:-5px;margin-right:5px;"
                                                     src="<?php echo base_url(); ?>assets/themes/images/<?php echo(isset($_SESSION['theme_images']) ? $_SESSION['theme_images'] : "default"); ?>/small-logo.png"><img
            id="big-logo" style="margin-top:-5px; width:100%"
            src="<?php echo base_url(); ?>assets/themes/images/<?php echo(isset($_SESSION['theme_images']) ? $_SESSION['theme_images'] : "default"); ?>/logo.png"></a>
</div>

</div>
<?php } ?>
<?php if (isset($global_filter)) { 
 $this->view('forms/global_filter.php', $global_filter); 
  } ?>
<nav id="menu" class="mm-menu mm--horizontal mm-offcanvas">
    <?php if (isset($_SESSION['permissions'])) { ?>
        <ul>
            <li><a class="mm-title">
                    <small><span class="text-primary"><?php echo date('l jS F') ?></span> -
                        Welcome <?php echo $_SESSION['name'] ?></small>
                </a></li>
            <?php if (isset($campaign_access) && count($_SESSION['campaign_access']['array']) > "2") { ?>
                <li style="padding:0;">
                    <select id="side-campaign-select" class="form-control">
                        <?php if (in_array("mix campaigns", $_SESSION['permissions']) || (!isset($_SESSION['current_campaign']) && !in_array("mix campaigns", $_SESSION['permissions']))) { ?>
                            <option
                                value=""><?php echo(in_array("mix campaigns", $_SESSION['permissions']) ? "Campaign Filter" : "Select a campaign to begin"); ?></option>
                        <?php } ?>
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
                </li>
            <?php } ?>
            <?php if (in_array("files only", $_SESSION['permissions'])) {
                $this->view('navigation/sidebar/files_only.php');
            } else if (in_array("survey only", $_SESSION['permissions'])) {
                $this->view('navigation/sidebar/survey_only.php');
            } else if (in_array("mix campaigns", $_SESSION['permissions']) || isset($_SESSION['current_campaign'])) {
                ?>
                <?php
                //The system will give the agents the records that need dialing
                if (in_array("use callpot", $_SESSION['permissions'])) { ?>
                    <li><a href="<?php echo base_url(); ?>records/detail">Start Calling</a></li>
                <?php } ?>
                <?php if (!isset($page)) {
                    $page = "";
                }
				/* make the menu start on the first panel - ignore selected page BF march 2016*/
				$page = "";
				/* end */
                $this->view('navigation/sidebar/dashboards.php', $page);
                $this->view('navigation/sidebar/records.php', $page);
                $this->view('navigation/sidebar/files.php', $page);
                $this->view('navigation/sidebar/appointments.php', $page);
                $this->view('navigation/sidebar/planner.php', $page);
                $this->view('navigation/sidebar/surveys.php', $page);
                $this->view('navigation/sidebar/calendar.php', $page);
                $this->view('navigation/sidebar/admin.php', $page);
                $this->view('navigation/sidebar/reports.php', $page);
                $this->view('navigation/sidebar/search.php', $page);
            } else { ?>
                <li><a href="#" style="color:red">Please select a campaign to begin</a></li>
                <li>
                    <select id="side-campaign-select" class="selectpicker" data-width="100%">
                        <?php if (in_array("mix campaigns", $_SESSION['permissions']) || (!isset($_SESSION['current_campaign']) && !in_array("mix campaigns", $_SESSION['permissions']))) { ?>
                            <option
                                value=""><?php echo(in_array("mix campaigns", $_SESSION['permissions']) ? "Campaign Filter" : "Select a campaign to begin"); ?></option>
                        <?php } ?>
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
                </li>
            <?php } ?>
        </ul>
    <?php } ?>
</nav>
<div class="container-fluid" id="container-fluid"> 
        <?php if($this->session->flashdata('success')){ ?>
        <div class="alert alert-success alert-dismissable" style="margin-top:10px">  
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>  
  <span class="glyphicon glyphicon-ok"></span> <?php echo $this->session->flashdata('success'); ?>  
	</div>  
    <?php } ?>
        <?php if($this->session->flashdata('danger')){ ?>
        <div class="alert alert-danger alert-dismissable" style="margin-top:10px">  
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>  
   <span class="glyphicon glyphicon-alert"></span> <?php echo $this->session->flashdata('danger'); ?>  
	</div>  
    <?php } ?>
        <?php if($this->session->flashdata('info')){ ?>
        <div class="alert alert-info alert-dismissable" style="margin-top:10px">  
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>  
  <span class="glyphicon glyphicon-info-sign"></span> <?php echo $this->session->flashdata('info'); ?>  
	</div>  
    <?php } ?>
        <?php if($this->session->flashdata('warning')){ ?>
        <div class="alert alert-warning alert-dismissable" style="margin-top:10px">  
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>  
  <span class="glyphicon glyphicon-exclamation-sign"></span> <?php echo $this->session->flashdata('warning'); ?>  
	</div>  
    <?php } ?>

<?php echo $body; ?></div>
<!-- /content -->

<!-- Modal -->
<<<<<<< HEAD
<div class="Fixed">
<?php  $this->view('misc/alerts.php');  ?>
<?php  $this->view('misc/modal.php');  ?>
=======
<div class="isFixed">
<div class="modal fade" id="modal" style="overflow:hidden" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Please confirm</h4>
            </div>
            <div class="modal-body" style="max-height: 500px; overflow: auto;"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default close-modal pull-left" data-dismiss="modal">Close</button>
                <button type="button" style="display: none;" class="btn btn-danger discard-modal">No</button>
                <button type="button" style="display: none;" class="btn btn-primary save-modal">Save</button>
                <button type="button" class="btn btn-primary confirm-modal">Confirm</button>
            </div>
        </div>
    </div>
</div>
<div id="page-success" class="alert alert-success hidden alert-dismissable"><span class="alert-text"></span><span
        class="close close-alert">&times;</span></div>
<div id="page-info" class="alert alert-info hidden alert-dismissable"><span class="alert-text"></span><span
        class="close close-alert">&times;</span></div>
<div id="page-warning" class="alert alert-warning hidden alert-dismissable"><span class="alert-text"></span><span
        class="close close-alert">&times;</span></div>
<div id="page-danger" class="alert alert-danger hidden alert-dismissable"><span class="alert-text"></span><span
        class="close close-alert">&times;</span></div>
>>>>>>> 005925b1417e7438f806e5f580835e253859d36a
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
<script src="<?php echo base_url() . "assets/js/modals.js?v" . $this->config->item('project_version'); ?>"></script>
<script src="<?php echo base_url() . "assets/js/main.js?v" . $this->config->item('project_version'); ?>"></script>
<?php if(isset($_SESSION['user_id'])){ ?>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<!-- Campaign triggers-->
<?php if (!empty($campaign_triggers)) { ?>
    <?php foreach($campaign_triggers as $script) { ?>
        <script src="<?php echo base_url()."custom_scripts/".$script['path']."?v".$this->config->item('project_version'); ?>"></script>
    <?php } ?>
<?php } else { ?>
<script type="text/javascript">campaign_functions = {};</script>
<?php } ?>
<!-- End of campaign triggers-->
<script type="text/javascript">
	var custom_appointment_modal = false;
	var custom_record_modal = false;
    modals.init();
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
    $(document).on('change', '#top-campaign-select,#side-campaign-select', function () {
        $.ajax({
            url: helper.baseUrl + 'user/current_campaign/' + $(this).val(),
            type: "POST",
            data: {campaign: $(this).val(), pot: $('#top-pot-filter select').val()},
            dataType: "JSON",
            beforeSend: function () {
                $('[data-id="campaign-select"]').append('<span style="position:absolute; right:5px;" ><img src="' + helper.baseUrl + 'assets/img/small-loading.gif" /></span>');
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
<script type="text/javascript">
    $(document).ready(function () {
        $('.dropdown-menu ul').addClass('mm-nolistview');
        $('nav#menu').mmenu({
            "navbars": [
                {
                    "position": "top",
                    "content": [
                        "prev",
                        "title",
                        "close"
                    ]
                },
                {
                    "position": "top",
                    "content": [
                        "<a href='" + helper.baseUrl + "<?php echo @$_SESSION['home'] ?>'><span class='fa fa-home'></span> Home</a>",
                        "<a href='" + helper.baseUrl + "user/account'><span class='fa fa-user'></span> Account</a>"
                        <?php if(@in_array('search records',$_SESSION['permissions'])){ ?>,"<a class='mm-next' data-target='#searchnav' href='#searchnav' id='quicksearch-btn'><span class='fa fa-search'></span> Search</a>"<?php } ?>
                    ]
                },
                {
                    "position": "bottom",
                    "content": [
                        "<a onclick=\"javascript:alert('121 Customer Insight. Version: <?php echo $this->config->item('project_version');?>')\" href='#'><span class='fa fa-book'></span> About</a>",
                        "<a data-modal='contact-us' href='#'><span class='fa fa-phone'></span> Contact</a>",
                        "<a href='" + helper.baseUrl + "user/logout'><span class='fa fa-sign-out'></span> Logout</a>"
                    ]
                }
            ]
            , "extensions": ["pageshadow", "effect-menu-slide", "effect-listitems-slide", "pagedim-black"]
        });
		 menu_api = $("nav#menu").data( "mmenu" );
        <?php if(isset($global_filter)){ ?>
        $('nav#global-filter').mmenu({
            navbar: {
                title: "Filter Records <span class='text-primary'><?php echo (isset($_SESSION['current_campaign_name'])?$_SESSION['current_campaign_name']:"") ?></span>"
            },
            extensions: ["pageshadow", "effect-menu-slide", "effect-listitems-slide", "pagedim-black"],
            offCanvas: {
                position: "right",
            }
        }, {
classNames: {
fixedElements: {
fixed: "isFixed"
}
}
});
 filter_api = $("nav#global-filter").data( "mmenu" );
$('nav#global-filter').on('click', '#global-filter-submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: helper.baseUrl + 'user/set_data',
                data: $('#global-filter-form').serialize(),
                type: "POST"
            }).done(function () {
                var right_mmenu = $("nav#global-filter").data("mmenu");
                right_mmenu.close();
                if (typeof view_records !== "undefined") {
                    map_table_reload()
                } else {
                    window.location = helper.baseUrl + 'records/detail/0';
                }
            });
        });
<?php } ?>
      

    });
</script>
<script src="<?php echo base_url() . "assets/js/record_update.js?v" . $this->config->item('project_version'); ?>"></script>
<?php } ?>
<?php 
//load specific javascript files set in the controller
if (isset($javascript)):
    foreach ($javascript as $file):?>
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
        src="https://maps.googleapis.com/maps/api/js?v=3<?php echo isset($callback)?$callback:"" ?>"></script>
<?php } 
endif;
?>


<?php if (isset($_SESSION['user_id'])) { ?>
    <div id="color-box" class="Fixed">
        <a href="#"><span class="glyphicon glyphicon-cog color-btn"></span></a>
    </div>
    <script>
        $(document).ready(function () {

			$('#open-quicksearch').on('click',function(e){
				e.preventDefault();
				$('#nav-menu-btn').trigger('click');
				$('#quicksearch-btn').trigger('click');
			});
			

			
			
            $('#color-box').on('click', '.color-btn', function () {
                var mheader = "Appearance";
                var report_btn= '' ;
                if (helper.permissions['export data'] > 0) {
                    report_btn = '<span type="button" class="btn btn-default report-settings-btn">' +
                                    '<p>Report Settings</p>' +
                                    '<span class="fa fa-area-chart fa-3x"></span>' +
                                 '</span>';
                }

                var last_messages= '' ;
                var display_messages = (helper.role == "1" ?"":"display:none");

                if (localStorage.getItem("messages")) {
                    $.each(JSON.parse(localStorage.getItem("messages")), function (i, val) {
                        var date = new Date(val[3]);
                        var date_show = date.toLocaleString().replace(",","");
                        var title = val[1];
                        var msg = val[2];
                        var msg_short = (msg && msg.length>40?msg.substring(0,40)+"...":msg);
                        var tooltip = '<h3>'+title+'</h3><div>'+date_show+'</div><div>'+msg+'</div>'
                        last_messages += '<tr class="'+(val[0]?"success":"danger")+' pointer last-messages" data-toggle="tooltip" data-placement="top" title="'+tooltip+'">' +
                            '<td style="font-weight: bold">'+title+'</td>' +
                            '<td style="word-break:break-all">'+msg_short+'</td>' +
                            '<td style="text-align: right">'+date_show+'</td>' +
                            '</tr>';
                    });
                }

				<?php if(isset($_SESSION['current_campaign'])){ ?>
				var layout_panel =   '<input type="hidden" name="current_camp" value="<?php echo $_SESSION['current_campaign'] ?>"><span type="button" class="btn btn-default layout-settings-btn" data-layout="2col.php">' +
                                            '<p>Grid View</p>' +
                                            '<span class="fa fa-th-large fa-3x"></span>' +
                                        '</span>' +
										 '<span type="button" class="btn btn-default layout-settings-btn" data-layout="accordian.php">' +
                                            '<p>List View</p>' +
                                            '<span class="fa fa-bars fa-3x"></span>' +
                                        '</span>' +
											 '<span type="button" class="btn btn-default layout-settings-btn" data-layout="default">' +                                            '<p>Default View</p>' +
                                            '<span class="fa fa-th fa-3x"></span>' +
                                        '</span>';
										<?php } else { ?>
					var layout_panel = "<p>You must select a campaign to set the layout</p>";
										<?php } ?>
                var navtabs = '<ul id="tabs" class="nav nav-tabs" role="tablist"><li class="active"><a role="tab" data-toggle="tab" href="#theme-tab">Theme</a></li><?php if(!in_array("change layout",$_SESSION['permissions'])){ ?><li><a role="tab" data-toggle="tab" href="#layout-tab"> Layout</a><?php } ?></li><li><a role="tab" data-toggle="tab" href="#dashboards-tab"> Dashboard</a></li><li style="'+display_messages+'"><a role="tab" data-toggle="tab" href="#last-messages-tab">Last actions</a></li></ul>';
                var tabpanels = '<div class="tab-content" style="overflow-y: scroll; max-height: 400px">' +
                                    '<div role="tabpanel" class="tab-pane active" id="theme-tab">' +
                                        '<p>Fancy something different? Pick a new colour!</p>' +
                                        '<select id="color-changer" class="color-changer selectpicker">' +
                                            '<option value="<?php echo $_SESSION["theme_color"] ?>">--Change color--</option>' +
                                            '<option value="voice">Bright Blue</option>' +
                                            '<option value="hsl">Deep Blue</option>' +
                                            '<option value="coop">Dark Blue</option>' +
                                            '<option value="smartprospector">Green</option>' +
                                            '<option value="default">Orange</option>' +
                                            '<option value="pelican">Red</option>' +
                                            '<option value="eldon">Purple</option>' +
                                        '</select>' +
                                    '</div>' +
									         '<div role="tabpanel" class="tab-pane" id="layout-tab">' +
                                      layout_panel +
                                        
                                    '</div>' +
                                    '<div role="tabpanel" class="tab-pane" id="dashboards-tab">' +
                                        '<span type="button" class="btn btn-default dashboard-settings-btn">' +
                                            '<p>Dashboard Settings</p>' +
                                            '<span class="fa fa-dashboard fa-3x"></span>' +
                                        '</span>' +
                                        report_btn +
                                    '</div>' +
                                    '<div role="tabpanel" class="tab-pane" id="last-messages-tab">' +
                                        '<table class="table table-hover small">' +
                                            '<thead>' +
                                                '<th>Title</th>' +
                                                '<th>Message</th>' +
                                                '<th>Date</th>' +
                                            '</thead>' +
                                            '<tbody>' +
                                                last_messages +
                                            '</tbody>' +
                                        '</table>' +
                                    '</div>' +
                                '</div>';
                var mbody = navtabs+tabpanels;
                var mfooter = '<button data-dismiss="modal" class="btn btn-primary close-modal pull-left">OK</button>'

                modals.load_modal(mheader, mbody, mfooter);
                modal_body.css('overflow', 'visible')
                $modal.find('.color-changer').change(function () {
                    var value = $(this).val();
                    $('#theme-css').attr('href', helper.baseUrl + 'assets/themes/colors/' + value + '/bootstrap-theme.css');
                    $.post(helper.baseUrl + 'ajax/change_theme', {theme: value});
                    if (device_type !== "default") {
                        window.location.reload();
                    }
                });

                $('.last-messages').tooltip({
                    html: true
                });
            });

            $modal.on("click",".dashboard-settings-btn",function()
            {
                window.location = helper.baseUrl + 'dashboard/settings';
            });

            $modal.on("click",".report-settings-btn",function()
            {
                window.location = helper.baseUrl + 'exports';
            });
			
			  $modal.on("click",".layout-settings-btn",function()
            {	var campaign = $modal.find('input[name="current_camp"]').val();
				var layout = $(this).attr('data-layout');
                $.ajax({ url: helper.baseUrl+'user/layout',
				type:"POST",
				dataType:"JSON",
				data:{layout:layout,campaign:campaign }
            }).done(function(response){
				location.reload();
			});
  });
        });
    </script>
<?php } ?>
</body>
</html>