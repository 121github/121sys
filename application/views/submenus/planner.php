<div class="navbar navbar-inverse navbar-fixed-top" style="margin-top:50px">
    <ul class="nav navbar-nav desktop-only">
        <p class="navbar-text" style="color:#fff; font-weight:700"><i class="fa fa-map-marker fa-fw"></i> Journey <?php echo $title ?></p>
    </ul>
    <?php if (!isset($hide_filter)) { ?>
        <ul class="nav navbar-nav pull-right" id="submenu-filters">
            <li>
                <div class="navbar-btn">
                    <form class="filter-form">
                        <input type="hidden" name="user" value="<?php echo $user_id ?>">
                        <input type="hidden" name="date" value="<?php echo date('Y-m-d') ?>">

                        <div class="btn-group">
                            <?php if(in_array("admin planner", $_SESSION['permissions'])) { ?>
                                <?php if(!empty($drivers)){ ?>
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                        <span id="user-filter-name">Drivers </span> <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a href="#" class="user-filter" data-id="<?php echo $_SESSION['user_id'] ?>">My Planner</a></li>      <li class="divider"></li>
                                        <?php foreach($drivers as $row){ ?>

                                            <li><a href="#" class="user-filter" data-id="<?php echo $row['user_id'] ?>"><?php echo $row['region_name'] ?>: <?php echo $row['name'] ?> </a></li>
                                        <?php } ?>

                                    </ul>
                                <?php } else { ?>
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                        <span id="user-filter-name">Users </span> <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a href="#" class="user-filter" data-id="<?php echo $_SESSION['user_id'] ?>">My Planner</a></li>    <li class="divider"></li>
                                        <?php foreach($attendees as $row){ ?>

                                            <li><a href="#" class="user-filter" data-id="<?php echo $row['user_id'] ?>"><?php echo $row['name'] ?> </a></li><?php } ?>
                                    </ul>
                                <?php } ?>
                            <?php } ?>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="daterange btn btn-default"><span
                                    class="glyphicon glyphicon-calendar"></span> <span
                                    class="date-text"> <?php echo "Today"; ?> </span></button>
                        </div>

                        <input type="hidden" name="group">
                    </form>
                </div>
            </li>

        </ul>
    <?php } ?>
</div>