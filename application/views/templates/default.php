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
          href="<?php echo base_url(); ?>assets/themes/<?php echo(isset($_SESSION['theme_folder']) ? $_SESSION['theme_folder'] : $theme); ?>/bootstrap-theme.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/plugins/dataTables/datatables.min.css">
    <!-- Latest compiled and minified JavaScript -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-datetimepicker.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/datepicker3.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap-select.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/slider.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/default.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/plugins/mmenu2/core/css/jquery.mmenu.all.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/plugins/dataTables/css/font-awesome.css">
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
<div class="navbar navbar-default navbar-fixed-top">
       
    <?php if (isset($_SESSION['permissions'])) { ?>
        <a href="#menu" class="navbar-toggle"> <span class="icon-bar"></span> <span class="icon-bar"></span> <span
                class="icon-bar"></span></a>
        <?php if (isset($_SESSION['current_campaign']) && isset($campaign_pots) && count($campaign_pots) > 0 && isset($global_filter)) { ?>
            <?php if (isset($_SESSION['current_pot'])) {
                $filter_style = "color:#fff;background:#a94442";
            } else {
                $filter_style = "";
            } ?>
            <a href="#menu-right" class="navbar-toggle navbar-right"
               style="height:34px;width:42px;<?php echo $filter_style ?>"><span class="glyphicon glyphicon-filter"
                                                                                style="padding-left:3px"></span></a>
        <?php } ?>
    <?php } ?>


    <?php if (isset($campaign_access)) { ?>
    <div id="top-campaign-container" style="padding-top:8px; width:230px; display:none">
        <select id="top-campaign-select" class="selectpicker" data-width="230px">
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
    <?php if ($_SESSION['environment'] == 'demo') { ?>
        <span style="color: red; margin-left: 10%; background-color: yellow">This is a demo system. The data added could be deleted at any time!! </span>
    <?php } ?>
    
    <a href="#" class="navbar-brand pull-right"><img id="small-logo" style="margin-top:-10px;margin-right:5px;"
                                                 src="<?php echo base_url(); ?>assets/themes/<?php echo(isset($_SESSION['theme_folder']) ? $_SESSION['theme_folder'] : "default"); ?>/small-logo.png"><img
        id="big-logo" style="margin-top:-5px; width:100%"
        src="<?php echo base_url(); ?>assets/themes/<?php echo(isset($_SESSION['theme_folder']) ? $_SESSION['theme_folder'] : "default"); ?>/logo.png"></a>
</div>
<?php } ?>
</div>
<?php if (isset($_SESSION['current_campaign']) && isset($campaign_pots) && count($campaign_pots) > 0) { ?>
    <nav id="menu-right" class="mm-menu mm--horizontal mm-offcanvas">
        <div style="padding:30px 20px 3px">
            <form id="global-filter-form">
                <label>Data Pot <span class="glyphicon glyphicon-info-sign pointer tt" data-toggle="tooltip"
                                      data-placement="right" data-title="A group of specific records within a campaign"
                                      data-html="true"></span></label>
                <select name="data_pot" class="selectpicker" data-width="100%">
                    <option value="">-- Any data pot --</option>
                    <?php foreach ($campaign_pots as $campaign => $pot_data) { ?>
                        <optgroup label="<?php echo $campaign ?>">
                            <?php foreach ($pot_data as $pot) { ?>
                                <option <?php if (isset($_SESSION['current_pot']) && $_SESSION['current_pot'] == $pot['id']) {
                                    echo "Selected";
                                } ?> value="<?php echo $pot['id'] ?>"><?php echo $pot['name'] ?></option>
                            <?php } ?>
                        </optgroup>
                    <?php } ?>
                </select>
                <label>Data Source <span class="glyphicon glyphicon-info-sign pointer tt" data-toggle="tooltip"
                                         data-placement="right" data-title="The source of the data"
                                         data-html="true"></span></label>
                <select name="data_source" class="selectpicker" data-width="100%">
                    <option value="">-- Any data source --</option>
                    <?php foreach ($campaign_sources as $campaign => $data_source) { ?>
                        <optgroup label="<?php echo $campaign ?>">
                            <?php foreach ($data_source as $source) { ?>
                                <option <?php if (isset($_SESSION['current_source']) && $_SESSION['current_source'] == $source['id']) {
                                    echo "Selected";
                                } ?> value="<?php echo $source['id'] ?>"><?php echo $source['name'] ?></option>
                            <?php } ?>
                        </optgroup>
                    <?php } ?>
                </select>

                <button id="global-filter-submit" class="btn btn-primary pull-right">Submit</button>
            </form>
        </div>
    </nav>
<?php } ?>
<nav id="menu" class="mm-menu mm--horizontal mm-offcanvas">
    <?php if (isset($_SESSION['permissions'])) { ?>
        <ul>
            <li><a class="mm-title">
                    <small><span class="text-primary"><?php echo date('l jS F') ?></span> -
                        Welcome <?php echo $_SESSION['name'] ?></small>
                </a></li>
            <?php if (isset($campaign_access) && count($_SESSION['campaign_access']['array']) > "2") { ?>
                <li style="padding:0 20px;">
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

                <?php if (in_array("files only", $_SESSION['permissions'])) { 
				$this->view('navigation/files_only.php');
             } else if (in_array("survey only", $_SESSION['permissions'])) { 
				$this->view('navigation/survey_only.php');
             } else if(in_array("mix campaigns", $_SESSION['permissions']) || isset($_SESSION['current_campaign'])){
			  ?>
              <?php 
			  //The system will give the agents the records that need dialing
			  if (in_array("use callpot", $_SESSION['permissions'])) { ?>
              <li><a href="<?php echo base_url(); ?>records/detail">Start Calling</a></li>
              <?php } ?>
                    <?php if (!isset($page)) { $page = ""; }
						$page = array("page"=>$page); 
						$this->view('navigation/dashboards.php',$page); 
						$this->view('navigation/records.php',$page) ;
						$this->view('navigation/files.php',$page);
						$this->view('navigation/appointments.php',$page) ;
						$this->view('navigation/planner.php',$page) ;
						$this->view('navigation/surveys.php',$page);
						$this->view('navigation/calendar.php',$page) ;
						$this->view('navigation/admin.php',$page) ;
						$this->view('navigation/reports.php',$page); 
						$this->view('navigation/search.php',$page); 
			 } else { ?>
              <li><a href="#" style="color:red">Please select a campaign to begin</a></li>
              <li><select id="side-campaign-select" class="selectpicker" data-width="100%">
                        <?php if(in_array("mix campaigns", $_SESSION['permissions']) || (!isset($_SESSION['current_campaign']) && !in_array("mix campaigns", $_SESSION['permissions']))) { ?>
                            <option value=""><?php echo(in_array("mix campaigns", $_SESSION['permissions']) ? "Campaign Filter" : "Select a campaign to begin"); ?></option>
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
                    </select></li>

            <?php } ?>
        </ul>
    <?php } ?>
</nav>
<div class="container-fluid" id="container-fluid">
    <?php echo $body; ?></div>
<!-- /content -->

<!-- Modal -->
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
<script src="<?php echo base_url(); ?>assets/js/plugins/mmenu2/core/js/jquery.mmenu.min.all.js"></script>
<script src="<?php echo base_url(); ?>assets/js/plugins/browser/jquery.browser.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/modals.js?v2.0"></script>
<script src="<?php echo base_url(); ?>assets/js/main.js?v2.1"></script>
<script type="text/javascript"> helper.baseUrl = '<?php echo base_url(); ?>' + '';
    <?php if(isset($_SESSION['permissions'])){ ?>
    helper.permissions = $.parseJSON('<?php echo json_encode(array_flip($_SESSION['permissions'])) ?>');
    helper.roles = $.parseJSON('<?php echo $_SESSION['role'] ?>');
    helper.current_postcode = false;
    <?php } ?>
    <?php if(@!empty($_SESSION['current_postcode'])){ ?>
    helper.current_postcode = "<?php echo $_SESSION['current_postcode'] ?>";
    <?php } ?>
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
        <?php if($this->session->flashdata('success')){ ?>
        flashalert.success('<?php echo $this->session->flashdata('success') ?>');
        <?php } ?>
        <?php if($this->session->flashdata('danger')){ ?>
        flashalert.success('<?php echo $this->session->flashdata('danger') ?>');
        <?php } ?>
        <?php if($this->session->flashdata('info')){ ?>
        flashalert.success('<?php echo $this->session->flashdata('info') ?>');
        <?php } ?>
        <?php if($this->session->flashdata('warning')){ ?>
        flashalert.success('<?php echo $this->session->flashdata('warning') ?>');
        <?php } ?>
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
						"<a href='"+helper.baseUrl+"dashboard'><span class='fa fa-home'></span> Home</a>",
						"<a href='"+helper.baseUrl+"user/account'><span class='fa fa-user'></span> Account</a>",
						"<a class='mm-next' data-target='#searchnav' href='#searchnav'><span class='fa fa-search'></span> Search</a>"
                     ]
                  },
				  {
                     "position": "bottom",
                     "content": [
                        "<a onclick=\"javascript:alert('Coming Soon')\" href='#'><span class='fa fa-book'></span> Docs</a>",
						"<a data-modal='contact-us' href='#'><span class='fa fa-phone'></span> Contact</a>",
						"<a href='"+helper.baseUrl+"user/logout'><span class='fa fa-sign-out'></span> Logout</a>"
                     ]
                  }
               ]
            ,"extensions": ["pageshadow","effect-menu-slide", "effect-listitems-slide","pagedim-black"]});

        <?php if(isset($global_filter)){ ?>
        $('nav#menu-right').mmenu({
            navbar: {
                title: "Search &amp; Filter <span class='text-primary'><?php echo @$_SESSION['current_campaign_name'] ?></span>"
            },
            extensions: ["pageshadow", "effect-menu-slide", "effect-listitems-slide", "pagedim-black"],
            offCanvas: {
                position: "right",
            }
        });
        <?php } ?>
        $(document).on('click', '#global-filter-submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: helper.baseUrl + 'user/set_data',
                data: $('#global-filter-form').serialize(),
                type: "POST"
            }).done(function () {
                var right_mmenu = $("nav#menu-right").data("mmenu");
                right_mmenu.close();
                if (typeof view_records !== "undefined") {
                    map_table_reload()
                } else {
                    window.location = helper.baseUrl + 'records/detail/0';
                }
            });
        });

    });
</script>
<?php //load specific javascript files set in the controller
if (isset($javascript)):
    foreach ($javascript as $file): ?>
        <script src="<?php echo base_url(); ?>assets/js/<?php echo $file ?>"></script>
    <?php endforeach;
endif; ?>
<?php if (@in_array("map.js", $javascript) || @in_array("location.js", $javascript)) { ?>
<?php if (@in_array("map.js", $javascript)){ $callback = "&callback=initializemaps"; } ?>
<script
 type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3&sensor=false<?php echo $callback ?>"></script>
<?php } ?>  
</body>
</html>
