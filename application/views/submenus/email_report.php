<div class="navbar navbar-inverse navbar-fixed-top" style="margin-top:50px">
    <ul class="nav navbar-nav desktop-only">
    <p class="navbar-text" style="color:#fff; font-weight:700"><?php echo $title ?></p>
            <!--<li>
                <p class="navbar-btn">
                    <a href="#" class="btn btn-primary">I'm a link button!</a>
                </p>
            </li>-->
            <li <?php if($submenu['page']=="campaign"){ echo 'class="active"'; } ?>><a href="<?php echo base_url() ?>reports/email/campaign">Campaign</a></li>
            <li <?php if($submenu['page']=="user"){ echo 'class="active"'; } ?>><a href="<?php echo base_url() ?>reports/email/user">User</a></li>
            <li <?php if($submenu['page']=="date"){ echo 'class="active"'; } ?>><a href="<?php echo base_url() ?>reports/email/date">Date</a></li>
            <li <?php if($submenu['page']=="time"){ echo 'class="active"'; } ?>><a href="<?php echo base_url() ?>reports/email/time">Time</a></li>
                     <li <?php if($submenu['page']=="template"){ echo 'class="active"'; } ?>><a href="<?php echo base_url() ?>reports/email/template">Template</a></li>
        </ul>
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
                    <a href="#filter-right" class="btn btn-default btn">
                        <span class="glyphicon glyphicon-filter" style="padding-left:3px; color:black;"></span> Filter
                    </a>
                    <button data-toggle="dropdown" class="btn btn-default dropdown-toggle"><span class="caret"></span></button>
                    <ul class="dropdown-menu">
                        <li><a class='view-filters' href="#">View current filters</a></li>
                        <li><a class='clear-filters' href="#">Set default filters</a></li>
                    </ul>
                </div>
                </div>
            </li>
            </ul>
</div>