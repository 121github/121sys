<div class="navbar navbar-inverse navbar-fixed-top" style="margin-top:50px">
    <ul class="nav navbar-nav desktop-only">
    <p class="navbar-text" style="color:#fff; font-weight:700"><?php echo $title ?></p>
    </ul>
    <?php if(!isset($hide_filter)){ ?>
       <ul class="nav navbar-nav pull-right">
             <li>
             <div class="navbar-btn">
                <div class="btn-group">
                    <span class="btn btn-default btn show-charts" data-item="0" charts="chart_div" data="filters">
                        <span class="fa fa-bar-chart-o fa-fw" style="color:black;"></span>
                    </span>
                    <span class="btn btn-default btn refresh-data">
                        <span class="glyphicon glyphicon-refresh" style="padding-left:3px; color:black;"></span>
                    </span>
                    

                        <?php $filter_class = "btn-default";  ?>
                        <?php if (isset($_SESSION['filter']['values'])) { ?>
                            <?php if (@array_key_exists("pot_id", $_SESSION['report-filter']['values']) || @array_key_exists("source_id", $_SESSION['report-filter']['values']) || @array_key_exists("outcome_id", $_SESSION['report-filter']['values']) || @array_key_exists("postcode", $_SESSION['report-filter']['values'])|| @array_key_exists("pot_id", $_SESSION['report-filter']['values'])|| @array_key_exists("parked_code", $_SESSION['report-filter']['values'])|| @array_key_exists("user_id", $_SESSION['report-filter']['values'])|| @array_key_exists("record_status", $_SESSION['report-filter']['values'])|| @array_key_exists("campaign_id", $_SESSION['report-filter']['values'])) { 

                                $filter_class = "btn-success";
                            } ?>
                        <?php } ?>
                        <div class="btn-group">
                            <a href="#filter-right" class="btn <?php echo $filter_class ?>" id="submenu-filter-btn">
                        <span class="glyphicon glyphicon-filter" style="padding-left:3px; color:black;"></span> Filter
                    </a>
                        </div>
         
                  
                    <button data-toggle="dropdown" class="btn btn-default dropdown-toggle"><span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a class='view-filters' href="#">View current filters</a></li>
                        <li><a class='clear-filters' href="#">Set default filters</a></li>
                    </ul>
                </div>
                </div>
            </li>
            </ul>
            <?php } ?>
            </div>