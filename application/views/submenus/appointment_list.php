<div class="navbar navbar-inverse navbar-fixed-top" style="margin-top:50px">
    <ul class="nav navbar-nav desktop-only">
        <p class="navbar-text" style="color:#fff; font-weight:700"><?php echo $title ?></p>
    </ul>
    <?php if (!isset($hide_filter)) { ?>
        <ul class="nav navbar-nav pull-right" id="submenu-filters">
            <li>
                <div class="navbar-btn">
                    <form class="filter-form">
                        <input type="hidden" value="<?php echo date('Y-m-d') ?>" name="date_from">
                        <input type="hidden" value="<?php echo date('Y-m-d', strtotime('+ 29 days')) ?>" name="date_to">

                        <div class="btn-group">
                            <a type="button" class="btn btn-default" data-modal="choose-columns"
                               data-table-id="1"><span
                                    class="fa fa-table"></span> Views
                            </a>
                        </div>
                        <?php
                        if ($this->uri->segment(2) == "mapview") {
                            $map_class = "btn-success";
                            $url = "appointments";
                        } else {
                            $map_class = "btn-default";
                            $url = "appointments/mapview";
                        } ?>

                        <div class="btn-group">
                            <a class="btn <?php echo $map_class ?>"
                               href="<?php echo base_url().$url; ?>"><span
                                    class="fa fa-globe"></span> Map
                            </a>
                        </div>
                        <div class="btn-group">
                            <a class="daterange btn btn-default" type="button">
                                <span class="glyphicon glyphicon-calendar"></span> <span
                                    class="date-text"> Next 30 Days </span>
                            </a>
                        </div>

                        <input type="hidden" name="group">
                    </form>
                </div>
            </li>

        </ul>
    <?php } ?>
</div>