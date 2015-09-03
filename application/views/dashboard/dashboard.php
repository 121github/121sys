<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Dashboard
            <small><?php if (isset($_SESSION['current_campaign_name'])) { echo @$_SESSION['current_campaign_name']; } ?></small>
        </h1>
    </div>
    <!-- /.col-lg-12 -->
</div>
<!-- /.row -->
<div class="row">
    <div class="col-lg-8">

        <!-- /.panel -->
        <div class="panel panel-primary call-history">
            <div class="panel-heading clearfix" >
                <i class="fa fa-history fa-fw"></i> Recent Activity</span>
                <div class="pull-right">
                    <form class="history-filter" data-func="history_panel">
                        <input type="hidden" name="date_from" value="">
                        <input type="hidden" name="date_to" value="">
                        <input type="hidden" name="campaign">
                        <input type="hidden" name="team">
                        <input type="hidden" name="agent">
                        <input type="hidden" name="source">
                        <?php if (!isset($_SESSION['current_campaign'])) { ?>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-xs dropdown-toggle"
                                        data-toggle="dropdown"><span class="caret"></span> Campaign
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <?php foreach ($campaigns as $row): ?>
                                        <li><a href="#" class="filter" data-ref="campaign"
                                               id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a></li>
                                    <?php endforeach ?>
                                    <li class="divider"></li>
                                    <li><a class="filter" data-ref="campaign" style="color: green;">All campaigns</a>
                                    </li>
                                </ul>
                            </div>
                        <?php } ?>
                        <?php if (in_array("by agent", $_SESSION['permissions'])) { ?>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-xs dropdown-toggle"
                                        data-toggle="dropdown">Agent <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <?php foreach ($agents as $row): ?>
                                        <li><a href="#" class="filter" data-ref="agent"
                                               id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a></li>
                                    <?php endforeach ?>
                                    <li class="divider"></li>
                                    <li><a class="filter" data-ref="agent" style="color: green;">All Agents</a></li>
                                </ul>
                            </div>
                        <?php } ?>
                        <?php if (in_array("by team", $_SESSION['permissions'])) { ?>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-xs dropdown-toggle"
                                        data-toggle="dropdown">Team <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <?php foreach ($team_managers as $row): ?>
                                        <li><a href="#" class="filter" data-ref="team"
                                               id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a></li>
                                    <?php endforeach ?>
                                    <li class="divider"></li>
                                    <li><a class="filter" data-ref="team" style="color: green;">All Teams</a></li>
                                </ul>
                            </div>
                        <?php } ?>
                    </form>
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
                    <form class="comments-filter" data-func="comments_panel">
                        <input type="hidden" name="date_from" value="">
                        <input type="hidden" name="date_to" value="">
                        <input type="hidden" name="campaign">
                        <input type="hidden" name="team">
                        <input type="hidden" name="agent">
                        <input type="hidden" name="source">
                        <input type="hidden" name="comments">
                        <?php if (!isset($_SESSION['current_campaign'])) { ?>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-xs dropdown-toggle"
                                        data-toggle="dropdown"><span class="caret"></span> Campaign
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <?php foreach ($campaigns as $row): ?>
                                        <li><a href="#" class="filter" data-ref="campaign"
                                               id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a></li>
                                    <?php endforeach ?>
                                    <li class="divider"></li>
                                    <li><a class="filter" data-ref="campaign" style="color: green;">All campaigns</a>
                                    </li>
                                </ul>
                            </div>
                        <?php } ?>
                        <?php if (in_array("by agent", $_SESSION['permissions'])) { ?>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-xs dropdown-toggle"
                                        data-toggle="dropdown">Agent <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <?php foreach ($agents as $row): ?>
                                        <li><a href="#" class="filter" data-ref="agent"
                                               id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a></li>
                                    <?php endforeach ?>
                                    <li class="divider"></li>
                                    <li><a class="filter" data-ref="agent" style="color: green;">All Agents</a></li>
                                </ul>
                            </div>
                        <?php } ?>
                        <?php if (in_array("by team", $_SESSION['permissions'])) { ?>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-xs dropdown-toggle"
                                        data-toggle="dropdown">Team <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <?php foreach ($team_managers as $row): ?>
                                        <li><a href="#" class="filter" data-ref="team"
                                               id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a></li>
                                    <?php endforeach ?>
                                    <li class="divider"></li>
                                    <li><a class="filter" data-ref="team" style="color: green;">All Teams</a></li>
                                </ul>
                            </div>
                        <?php } ?>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                <i class="glyphicon glyphicon-comment"></i></button>
                            <ul class="dropdown-menu slidedown">
                                <li><a href="#" id="1" class="filter" data-ref="comments"> <i
                                            class="fa fa-comment fa-fw"></i> Survey Comments </a></li>
                                <li><a href="#" id="2" class="filter" data-ref="comments"> <i
                                            class="fa fa-comment fa-fw"></i> Record Comments </a></li>
                                <li><a href="#" id="3" class="filter" data-ref="comments"> <i
                                            class="fa fa-comment fa-fw"></i> Sticky Notes </a></li>
                                <li class="divider"></li>
                                <li><a class="filter" data-ref="comments">Show All</a></li>
                            </ul>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <ul class="chat">
                    <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif"/>
                </ul>
            </div>
        </div>
    </div>
    <!-- /.col-lg-8 -->

    <div class="col-lg-4">
        <?php if (in_array("by agent", $_SESSION['permissions'])) { ?>
            <div class="panel panel-primary">
                <div class="panel-heading clearfix"><i class="fa fa-list-alt fa-fw"></i> System Statistics
                    <div class="pull-right">
                        <form class="stats-filter" data-func="system_stats">
                            <input type="hidden" name="date_from" value="">
                            <input type="hidden" name="date_to" value="">
                            <input type="hidden" name="campaign">
                            <input type="hidden" name="team">
                            <input type="hidden" name="agent">
                            <input type="hidden" name="source">
                            <?php if (!isset($_SESSION['current_campaign'])) { ?>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-xs dropdown-toggle"
                                            data-toggle="dropdown"><span class="caret"></span>
                                        Campaign
                                    </button>
                                    <ul class="dropdown-menu pull-right" role="menu">
                                        <?php foreach ($email_campaigns as $row): ?>
                                            <li><a href="#" class="filter" data-ref="campaign"
                                                   id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a></li>
                                        <?php endforeach ?>
                                        <li class="divider"></li>
                                        <li><a class="filter" data-ref="campaign" style="color: green;">All
                                                campaigns</a></li>
                                    </ul>
                                </div>
                            <?php } ?>
                            <!-- doesnt fit on panel
                  <?php if (in_array("by agent", $_SESSION['permissions'])) { ?>
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="caret"></span> Agent</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach ($agents as $row): ?>
	                    <li><a href="#" class="filter" data-ref="agent" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="filter" data-ref="agent" style="color: green;">All Agents</a> </li>
	                  </ul>
                  </div>
                  <?php } ?>
                    <?php if (in_array("by team", $_SESSION['permissions'])) { ?>
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="caret"></span>Team</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach ($team_managers as $row): ?>
	                    <li><a href="#" class="filter" data-ref="team" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="filter" data-ref="team" style="color: green;">All Teams</a> </li>
	                  </ul>
                  </div>
                 <?php } ?>
                 -->
                        </form>
                    </div>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <ul class="timeline">
                        <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif"/>
                    </ul>
                </div>
                <!-- /.panel-body -->

            </div>
        <?php } ?>
        <?php if (in_array("email", $_SESSION['permissions'])) { ?>
            <div class="panel panel-primary">
                <div class="panel-heading clearfix"><i class="fa fa-envelope-o fa-fw"></i> Email Statistics
                    <div class="pull-right">
                        <form class="emails-filter" data-func="emails_panel">
                            <input type="hidden" name="date_from" value="">
                            <input type="hidden" name="date_to" value="">
                            <input type="hidden" name="campaign">
                            <input type="hidden" name="team">
                            <input type="hidden" name="agent">
                            <input type="hidden" name="source">
                            <?php if (!isset($_SESSION['current_campaign'])) { ?>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-xs dropdown-toggle"
                                            data-toggle="dropdown"><span class="caret"></span>
                                        Campaign
                                    </button>
                                    <ul class="dropdown-menu pull-right" role="menu">
                                        <?php foreach ($email_campaigns as $row): ?>
                                            <li><a href="#" class="filter" data-ref="campaign"
                                                   id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a></li>
                                        <?php endforeach ?>
                                        <li class="divider"></li>
                                        <li><a class="filter" data-ref="campaign" style="color: green;">All
                                                campaigns</a></li>
                                    </ul>
                                </div>
                            <?php } ?>
                            <!-- doesnt fit on panel
                  <?php if (in_array("by agent", $_SESSION['permissions'])) { ?>
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="caret"></span> Agent</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach ($agents as $row): ?>
	                    <li><a href="#" class="filter" data-ref="agent" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="filter" data-ref="agent" style="color: green;">All Agents</a> </li>
	                  </ul>
                  </div>
                  <?php } ?>
                    <?php if (in_array("by team", $_SESSION['permissions'])) { ?>
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="caret"></span>Team</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach ($team_managers as $row): ?>
	                    <li><a href="#" class="filter" data-ref="team" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="filter" data-ref="team" style="color: green;">All Teams</a> </li>
	                  </ul>
                  </div>
                 <?php } ?>
                 -->
                        </form>
                    </div>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="email-stats">
                        <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif"/>
                    </div>
                </div>
                <!-- /.panel-body -->

            </div>
        <?php } ?>
        <?php if (in_array("sms", $_SESSION['permissions'])) { ?>
            <div class="panel panel-primary">
                <div class="panel-heading clearfix"><i class="glyphicon glyphicon-phone"></i> Sms Statistics
                    <div class="pull-right">
                        <form class="sms-filter" data-func="sms_panel">
                            <input type="hidden" name="campaign">
                            <?php if (!isset($_SESSION['current_campaign'])) { ?>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-xs dropdown-toggle"
                                            data-toggle="dropdown"><span class="caret"></span>
                                        Campaign
                                    </button>
                                    <ul class="dropdown-menu pull-right" role="menu">
                                        <?php foreach ($sms_campaigns as $row): ?>
                                            <li><a href="#" class="filter" data-ref="campaign"
                                                   id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a></li>
                                        <?php endforeach ?>
                                        <li class="divider"></li>
                                        <li><a class="filter" data-ref="campaign" style="color: green;">All
                                                campaigns</a></li>
                                    </ul>
                                </div>
                            <?php } ?>
                        </form>
                    </div>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="sms-stats">
                        <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif"/>
                    </div>
                </div>
                <!-- /.panel-body -->

            </div>
        <?php } ?>
        <div class="panel panel-primary">
            <div class="panel-heading clearfix"><i class="fa fa-bell fa-fw"></i> Todays Outcomes
                <div class="pull-right">
                    <form class="outcomes-filter" data-func="outcomes_panel">
                        <input type="hidden" name="date_from" value="">
                        <input type="hidden" name="date_to" value="">
                        <input type="hidden" name="campaign">
                        <input type="hidden" name="team">
                        <input type="hidden" name="agent">
                        <input type="hidden" name="source">
                        <?php if (!isset($_SESSION['current_campaign'])) { ?>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-xs dropdown-toggle"
                                        data-toggle="dropdown"><span class="caret"></span> Campaign
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <?php foreach ($campaigns as $row): ?>
                                        <li><a href="#" class="filter" data-ref="campaign"
                                               id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a></li>
                                    <?php endforeach ?>
                                    <li class="divider"></li>
                                    <li><a class="filter" data-ref="campaign" style="color: green;">All campaigns</a>
                                    </li>
                                </ul>
                            </div>
                        <?php } ?>
                        <!-- doesnt fit on panel
                  <?php if (in_array("by agent", $_SESSION['permissions'])) { ?>
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="caret"></span> Agent</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach ($agents as $row): ?>
	                    <li><a href="#" class="filter" data-ref="agent" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="filter" data-ref="agent" style="color: green;">All Agents</a> </li>
	                  </ul>
                  </div>
                  <?php } ?>
                    <?php if (in_array("by team", $_SESSION['permissions'])) { ?>
                  <div class="btn-group">
	                  <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="caret"></span>Team</button>
	                  <ul class="dropdown-menu pull-right" role="menu">
	                    <?php foreach ($team_managers as $row): ?>
	                    <li><a href="#" class="filter" data-ref="team" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
	                    <?php endforeach ?>
	                    <li class="divider"></li>
	                    <li><a class="filter" data-ref="team" style="color: green;">All Teams</a> </li>
	                  </ul>
                  </div>
                 <?php } ?>
                 -->
                    </form>
                </div>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body"
                >
                <div class="outcome-stats"><img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif"/></div>
                <!--<a href="#" class="btn btn-default btn-block">View Details</a> </div>--></div>
            <!-- /.list-group -->
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
        dashboard.emails_panel();
        dashboard.sms_panel();
    });
</script>
