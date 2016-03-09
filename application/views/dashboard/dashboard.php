<div class="row">
    <div class="col-lg-8">
        <h1 class="page-header">
            Dashboard
            <small><?php if (isset($_SESSION['current_campaign_name'])) { echo @$_SESSION['current_campaign_name']; } ?></small>
        </h1>
    </div>
    <div class="col-lg-4 page-header" style="text-align: right;">
        <ul class="nav">
            <li>
                <div class="btn-group">
                    <span class="btn btn-default btn show-charts" data-item="0" charts="chart-div-system,chart-div-email,chart-div-sms,chart-div-outcome" data="data-system,data-email,data-sms,data-outcome">
                        <span class="fa fa-bar-chart-o fa-fw" style="color:black;"></span>
                    </span>
                    <span class="btn btn-default btn refresh-overview-data">
                        <span class="glyphicon glyphicon-refresh" style="padding-left:3px; color:black;"></span>
                    </span>
                    <a href="#filter-right" class="btn btn-default btn">
                        <span class="glyphicon glyphicon-filter" style="padding-left:3px; color:black;"></span> Filter
                    </a>
                    <button data-toggle="dropdown" class="btn btn-default dropdown-toggle"><span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a href="#filter-view">View current filters</a></li>
                        <li><a class='clear-filters' href="#">Set default filters</a></li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
    <!-- /.col-lg-12 -->
</div>

<nav id="filter-right" class="mm-menu mm--horizontal mm-offcanvas">
    <div style="padding:30px 20px 3px">
        <form class="filter-form">
            <input type="hidden" name="comments">
            <label style="margin-top: 5%;">Campaign</label>
            <select name="campaigns[]" class="selectpicker campaign-filter" multiple data-width="100%"
                    data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                <?php foreach ($campaigns_by_group as $type => $data) { ?>
                    <optgroup label="<?php echo $type ?>">
                        <?php foreach ($data as $row) { ?>
                            <option <?php if (isset($_SESSION['current_campaign']) && $row['id'] == $_SESSION['current_campaign']) {
                                echo "Selected";
                            } ?> value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                        <?php } ?>
                    </optgroup>
                <?php } ?>
            </select>

            <?php if (count($campaign_outcomes) > 0) { ?>
                <label style="margin-top: 5%;">Outcome</label>
                <select name="outcomes[]" class="selectpicker outcome-filter" id="outcome-filter" multiple
                        data-width="100%" data-live-search="true" data-live-search-placeholder="Search"
                        data-actions-box="true">
                    <?php foreach ($campaign_outcomes as $type => $data) { ?>
                        <optgroup label="<?php echo $type ?>">
                            <?php foreach ($data as $row) { ?>
                                <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                            <?php } ?>
                        </optgroup>
                    <?php } ?>
                </select>
            <?php } ?>

            <?php if (in_array("by team", $_SESSION['permissions'])) { ?>
                <label style="margin-top: 5%;">Team</label>
                <select name="teams[]" class="selectpicker team-filter" multiple data-width="100%"
                        data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                    <?php foreach ($team_managers as $row) { ?>
                        <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                    <?php } ?>
                </select>
            <?php } ?>

            <?php if (in_array("by agent", $_SESSION['permissions'])) { ?>
                <label style="margin-top: 5%;">User</label>
                <select name="agents[]" class="selectpicker agent-filter" multiple data-width="100%"
                        data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                    <?php foreach ($agents as $row) { ?>
                        <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                    <?php } ?>
                </select>
            <?php } ?>

            <label style="margin-top: 5%;">Source</label>
            <select name="sources[]" class="selectpicker source-filter" multiple data-width="100%"
                    data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
                <?php foreach ($sources as $row) { ?>
                    <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                <?php } ?>
            </select>

            <button id="filter-overview-submit" class="btn btn-primary pull-right" style="margin-top: 5%;">Submit</button>
        </form>
    </div>
</nav>

<nav id="filter-view" class="mm-menu mm--horizontal mm-offcanvas">
    <div id="filters"></div>
</nav>

<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
   <div class="panel panel-primary">
            <div class="panel-heading clearfix" >
               Quick Actions
            </div>
 <div class="panel-body">

                <p>Hello <?php echo $_SESSION['name'] ?>, what do you want to do?</p>
                       <?php if(in_array("use callpot",$_SESSION['permissions'])){ ?>
                       <a type="button" class="btn btn-default" href="records/detail" style="margin:0 3px 10px"><p>Start Calling</p><span class="fa fa-phone fa-3x"></span></a>
                <?php } ?>
                    <?php if(in_array("add records",$_SESSION['permissions'])&&isset($_SESSION['current_campaign'])){ ?>
            <a type="button" class="btn btn-default" href="data/add_record<?php echo isset($_SESSION['current_campaign'])?"/".$_SESSION['current_campaign']:"" ?>" style="margin:0 3px 10px"><p>Create Record</p><span class="fa fa-plus fa-3x"></span></a>
              <?php } ?>
                 <?php if(in_array("list records",$_SESSION['permissions'])){ ?>
                 <a type="button" class="btn btn-default" href="records/view" style="margin:0 3px 10px"><p>View Records</p><span class="fa fa-table fa-3x"></span></a>
                 <?php } ?>
                      <?php if(in_array("view appointments",$_SESSION['permissions'])){ ?>
                      <a type="button" class="btn btn-default" href="appointments" style="margin:0 3px 10px"><p>View Appointments</p><span class="fa fa-clock-o fa-3x"></span></a>
                     <?php } ?>
                          <?php if(in_array("full calendar",$_SESSION['permissions'])){ ?> 
                        <a type="button" class="btn btn-default" href="calendar" style="margin:0 3px 10px"><p>View Calendar</p><span class="fa fa-calendar fa-3x"></span></a>
                        <?php } ?>
                         <?php if(in_array("view surveys",$_SESSION['permissions'])){ ?>
                 <a type="button" class="btn btn-default" href="survey/view" style="margin:0 3px 10px"><p>View Surveys</p><span class="fa fa-clipboard fa-3x"></span></a>
                 <?php } ?>
                             <?php if(in_array("search records",$_SESSION['permissions'])){ ?>
                          <a type="button" class="btn btn-default" href="search" style="margin:0 3px 10px"><p>Search Records</p><span class="fa fa-search fa-3x"></span></a>
            <?php } ?>
            </div>
</div>
</div>
</div>

<div class="row">
    <div class="col-lg-8">

        <!-- /.panel -->
        <div class="panel panel-primary call-history">
            <div class="panel-heading clearfix" >
                <i class="fa fa-history fa-fw"></i> Recent Activity</span>
                <div class="pull-right" style="border:0px solid black;">
                    <a href="#filter-right" class="btn btn-default btn-xs">
                        <span class="glyphicon glyphicon-filter" style="padding-left:3px; color:black;"></span> Filter
                    </a>
                </div>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body" id="latest-history">

                <p><img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif"/></p>
                <!-- /.panel-body -->
            </div>
        </div>
        <!-- /.panel -->
        <div class="panel panel-primary">
            <div class="panel-heading clearfix"><i class="fa fa-comments fa-fw"></i> Latest Comments
                <div class="pull-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                            <i class="glyphicon glyphicon-comment"></i></button>
                        <ul class="dropdown-menu slidedown">
                            <li><a href="#" id="1" class="comment-filter" data-ref="comments"> <i
                                        class="fa fa-comment fa-fw"></i> Survey Comments </a></li>
                            <li><a href="#" id="2" class="comment-filter" data-ref="comments"> <i
                                        class="fa fa-comment fa-fw"></i> Record Comments </a></li>
                            <li><a href="#" id="3" class="comment-filter" data-ref="comments"> <i
                                        class="fa fa-comment fa-fw"></i> Sticky Notes </a></li>
                            <li class="divider"></li>
                            <li><a class="comment-filter" data-ref="comments">Show All</a></li>
                        </ul>
                    </div>
                    <div class="btn-group">
                        <a href="#filter-right" class="btn btn-default btn-xs">
                            <span class="glyphicon glyphicon-filter" style="padding-left:3px; color:black;"></span> Filter
                        </a>
                    </div>
                </div>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body" id="comment-panel">
                    <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif"/>
            </div>
        </div>
    </div>
    <!-- /.col-lg-8 -->

    <div class="col-lg-4">
            <div class="panel panel-primary">
                <div class="panel-heading clearfix"><i class="fa fa-list-alt fa-fw"></i>Statistics
                    <div class="pull-right">
                        <div class="pull-right" style="border:0px solid black;">
                            <a href="#filter-right" class="btn btn-default btn-xs">
                                <span class="glyphicon glyphicon-filter" style="padding-left:3px; color:black;"></span> Filter
                            </a>
                        </div>
                    </div>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body" style="padding: 0px;">
                    <ul class="nav nav-tabs" style=" background:#eee; width:100%;">
                        <li class="data-tab active"><a href="#data-system" class="tab" data-toggle="tab">Data</a></li>
                        <li class="plots-tab"><a href="#chart-div-system" class="tab" data-toggle="tab">Graphs</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="data-system">
                            <div id="system-stats">
                                <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif"/>
                            </div>
                        </div>
                        <div class="tab-pane" id="chart-div-system" style="margin: -15px">
                            <div id="carousel-system-generic" class="carousel slide" data-ride="carousel">
                                <!-- Indicators -->
                                <ol class="carousel-indicators">
                                    <li data-target="#carousel-system-generic" data-slide-to="0" class="active"></li>
                                    <li data-target="#carousel-system-generic" data-slide-to="1"></li>
                                </ol>

                                <!-- Wrapper for slides -->
                                <div class="carousel-inner" role="listbox">
                                    <div class="item active">
                                        <img src="" alt="" style="height: 400px;">
                                        <div class="carousel-caption">
                                            <div id="campaign-stats-chart" style="text-shadow: none"></div>
                                        </div>
                                    </div>
                                    <div class="item">
                                        <img src="" alt="" style="height: 400px;">
                                        <div class="carousel-caption">
                                            <div id="survey-stats-chart" style="text-shadow: none"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Controls -->
                                <a class="left carousel-control" href="#carousel-system-generic" role="button" data-slide="prev" style="background-image: none;">
                                    <span class="glyphicon glyphicon-chevron-left" style="color: black" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="right carousel-control" href="#carousel-system-generic" role="button" data-slide="next" style="background-image: none;">
                                    <span class="glyphicon glyphicon-chevron-right" style="color: black" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
        <?php if (in_array("email", $_SESSION['permissions'])) { ?>
            <div class="panel panel-primary">
                <div class="panel-heading clearfix"><i class="fa fa-envelope-o fa-fw"></i> Email Statistics
                    <div class="pull-right" style="border:0px solid black;">
                        <a href="#filter-right" class="btn btn-default btn-xs">
                            <span class="glyphicon glyphicon-filter" style="padding-left:3px; color:black;"></span> Filter
                        </a>
                    </div>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body" style="padding: 0px;">
                    <ul class="nav nav-tabs" style=" background:#eee; width:100%;">
                        <li class="data-tab active"><a href="#data-email" class="tab" data-toggle="tab">Data</a></li>
                        <li class="plots-tab"><a href="#chart-div-email" class="tab" data-toggle="tab">Graphs</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="data-email">
                            <div id="email-stats">
                                <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif"/>
                            </div>
                        </div>
                        <div class="tab-pane" id="chart-div-email" style="margin: -15px">
                            <div id="carousel-email-generic" class="carousel slide" data-ride="carousel">
                                <!-- Indicators -->
                                <ol class="carousel-indicators">
                                    <li data-target="#carousel-email-generic" data-slide-to="0" class="active"></li>
                                    <li data-target="#carousel-email-generic" data-slide-to="1"></li>
                                </ol>

                                <!-- Wrapper for slides -->
                                <div class="carousel-inner" role="listbox">
                                    <div class="item active">
                                        <img src="" alt="" style="height: 400px;">
                                        <div class="carousel-caption">
                                            <div id="email-today-chart" style="text-shadow: none"></div>
                                        </div>
                                    </div>
                                    <div class="item">
                                        <img src="" alt="" style="height: 400px;">
                                        <div class="carousel-caption">
                                            <div id="email-all-chart" style="text-shadow: none"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Controls -->
                                <a class="left carousel-control" href="#carousel-email-generic" role="button" data-slide="prev" style="background-image: none;">
                                    <span class="glyphicon glyphicon-chevron-left" style="color: black" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="right carousel-control" href="#carousel-email-generic" role="button" data-slide="next" style="background-image: none;">
                                    <span class="glyphicon glyphicon-chevron-right" style="color: black" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.panel-body -->

            </div>
        <?php } ?>
        <?php if (in_array("sms", $_SESSION['permissions'])) { ?>
            <div class="panel panel-primary">
                <div class="panel-heading clearfix"><i class="glyphicon glyphicon-phone"></i> Sms Statistics
                    <div class="pull-right" style="border:0px solid black;">
                        <a href="#filter-right" class="btn btn-default btn-xs">
                            <span class="glyphicon glyphicon-filter" style="padding-left:3px; color:black;"></span> Filter
                        </a>
                    </div>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body" style="padding: 0px;">
                    <ul class="nav nav-tabs" style=" background:#eee; width:100%;">
                        <li class="data-tab active"><a href="#data-sms" class="tab" data-toggle="tab">Data</a></li>
                        <li class="plots-tab"><a href="#chart-div-sms" class="tab" data-toggle="tab">Graphs</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="data-sms">
                            <div id="sms-stats">
                                <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif"/>
                            </div>
                        </div>
                        <div class="tab-pane" id="chart-div-sms" style="margin: -15px">
                            <div id="carousel-sms-generic" class="carousel slide" data-ride="carousel">
                                <!-- Indicators -->
                                <ol class="carousel-indicators">
                                    <li data-target="#carousel-sms-generic" data-slide-to="0" class="active"></li>
                                    <li data-target="#carousel-sms-generic" data-slide-to="1"></li>
                                </ol>

                                <!-- Wrapper for slides -->
                                <div class="carousel-inner" role="listbox">
                                    <div class="item active">
                                        <img src="" alt="" style="height: 400px;">
                                        <div class="carousel-caption">
                                            <div id="sms-today-chart" style="text-shadow: none"></div>
                                        </div>
                                    </div>
                                    <div class="item">
                                        <img src="" alt="" style="height: 400px;">
                                        <div class="carousel-caption">
                                            <div id="sms-all-chart" style="text-shadow: none"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Controls -->
                                <a class="left carousel-control" href="#carousel-sms-generic" role="button" data-slide="prev" style="background-image: none;">
                                    <span class="glyphicon glyphicon-chevron-left" style="color: black" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="right carousel-control" href="#carousel-sms-generic" role="button" data-slide="next" style="background-image: none;">
                                    <span class="glyphicon glyphicon-chevron-right" style="color: black" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.panel-body -->

            </div>
        <?php } ?>
        <div class="panel panel-primary">
            <div class="panel-heading clearfix"><i class="fa fa-bell fa-fw"></i> Todays Outcomes
                <div class="pull-right" style="border:0px solid black;">
                    <a href="#filter-right" class="btn btn-default btn-xs">
                        <span class="glyphicon glyphicon-filter" style="padding-left:3px; color:black;"></span> Filter
                    </a>
                </div>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body" style="padding: 0px;">
                <ul class="nav nav-tabs" style=" background:#eee; width:100%;">
                    <li class="data-tab active"><a href="#data-outcome" class="tab" data-toggle="tab">Data</a></li>
                    <li class="plots-tab"><a href="#chart-div-outcome" class="tab" data-toggle="tab">Graphs</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="data-outcome">
                        <div class="outcome-stats">
                            <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif"/>
                        </div>
                    </div>
                    <div class="tab-pane" id="chart-div-outcome">
                        <div id="outcome-chart" style="text-shadow: none"></div>
                    </div>
                </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->

        <!-- /.panel -->

        <!-- /.panel -->
        <!-- /.panel .chat-panel -->
    </div>
    <!-- /.col-lg-4 -->
</div>
<!-- /.row -->


<script>
    $(document).ready(function () {
        dashboard.init();
        dashboard.history_panel();
        dashboard.outcomes_panel();
        dashboard.system_stats();
        dashboard.comments_panel();
		 <?php if (in_array("email", $_SESSION['permissions'])) { ?>
        dashboard.emails_panel();
		<?php } ?>
		 <?php if (in_array("sms", $_SESSION['permissions'])) { ?>
        dashboard.sms_panel();
		<?php } ?>
    });
</script>
