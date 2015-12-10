<div class="row">
    <div class="col-lg-8">
        <h1 class="page-header">Favorites</h1>
    </div>
    <div class="col-lg-4 page-header" style="text-align: right;">
        <ul class="nav">
            <li>
                <div class="btn-group">
                    <span class="btn btn-default btn show-charts" data-item="0"
                          charts="chart-div-system,chart-div-email,chart-div-sms,chart-div-outcome"
                          data="data-system,data-email,data-sms,data-outcome" disabled>
                        <span class="fa fa-bar-chart-o fa-fw" style="color:black;"></span>
                    </span>
                    <span class="btn btn-default btn refresh-favorites-data">
                        <span class="glyphicon glyphicon-refresh" style="padding-left:3px; color:black;"></span>
                    </span>
                    <a href="#filter-right" class="btn btn-default btn">
                        <span class="glyphicon glyphicon-filter" style="padding-left:3px; color:black;"></span> Filter
                    </a>
                    <button data-toggle="dropdown" class="btn btn-default dropdown-toggle"><span class="caret"></span>
                    </button>
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
<!-- /.row -->

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
                <label style="margin-top: 5%;">Agent</label>
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

            <button id="filter-favorite-submit" class="btn btn-primary pull-right" style="margin-top: 5%;">Submit</button>
        </form>
    </div>
</nav>

<nav id="filter-view" class="mm-menu mm--horizontal mm-offcanvas">
    <div id="filters"></div>
</nav>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary" id="a_favorites">
            <div class="panel-heading clearfix"><i class="fa fa-star fa-fw"></i> Favorites
                <div class="pull-right" style="border:0px solid black;">
                    <a href="#filter-right" class="btn btn-default btn-xs">
                        <span class="glyphicon glyphicon-filter" style="padding-left:3px; color:black;"></span> Filter
                    </a>
                </div>
            </div>
            <div class="panel-body favorites-panel"><img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif"/>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url() ?>assets/js/plugins/metisMenu/jquery.metisMenu.js"></script>

<!-- Page-Level Plugin Scripts - Dashboard -->
<script src="<?php echo base_url() ?>assets/js/plugins/morris/raphael-2.1.0.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/plugins/morris/morris.js"></script>

<!-- SB Admin Scripts - Include with every page -->
<script src="<?php echo base_url() ?>assets/js/sb-admin.js"></script>
<script>
    $(document).ready(function () {
        dashboard.init();
        dashboard.favorites_panel();

        $('.daterange').daterangepicker({
                opens: "left",
                ranges: {
                    'Todays': [moment(), moment()],
                    'Tomorrow': [moment().add('days', 1), moment().add('days', 1)],
                    'Missed': [moment('2014-01-01'), moment()],
                    'Upcoming': [moment(), moment('2025-01-01')]
                },
                format: 'DD/MM/YYYY HH:mm',
                minDate: "02/07/2014",
                startDate: moment(),
                timePicker: true,
                timePickerSeconds: false
            },
            function (start, end, element) {
                var $btn = this.element;
                var btntext = start.format('MMMM D') + ' - ' + end.format('MMMM D');
                console.log(start.format('YYYY-MM-DD'));
                if (start.format('YYYY-MM-DD') == '2014-07-02') {
                    var btntext = "Missed";
                }
                if (end.format('YYYY-MM-DD') == '2025-01-01') {
                    var btntext = "Upcoming";
                }
                $btn.find('.date-text').html(btntext);
                $btn.closest('form').find('input[name="date_from"]').val(start.format('YYYY-MM-DD HH:mm'));
                $btn.closest('form').find('input[name="date_to"]').val(end.format('YYYY-MM-DD HH:mm'));
                dashboard.callbacks_panel();
            });
        $(document).on("click", '.daterange', function (e) {
            e.preventDefault();
        });

        $("#my_favorites").on("click", function () {
            $("html,body").animate(
                {scrollTop: $("#a_favorites").offset().top},
                1500
            );
        });
    });
</script>