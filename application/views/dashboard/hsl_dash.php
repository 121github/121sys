<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Hsl Dashboard</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>




<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <i class="fa fa-clock-o fa-fw"></i>
                Next 6 weeks appointments
                <div class="pull-right">
                    <form id="appointments-filter" data-func="appointments_panel">
                        <input type="hidden" name="date_from" value="">

                        <div class="btn-group">
                            <button type="button" class="daterange btn btn-default btn-xs" style="display: none"><span
                                    class="glyphicon glyphicon-calendar"></span> <span
                                    class="date-text"> Date from </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="panel-body" id="appointments-panel">
                <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif"/>
            </div>
        </div>
    </div>
</div>

<nav id="filter-right" class="mm-menu mm--horizontal mm-offcanvas">
    <div style="padding:30px 20px 3px">
        <form class="webform-filter">
            <input type="hidden" name="date_from" value="<?php echo "2014-01-01" ?>">
            <input type="hidden" name="date_to" value="<?php echo date('Y-m-d') ?>">

            <div style="margin-bottom: 5%;">
                <button type="button" class="daterange btn btn-default" data-width="100%">
                    <span class="glyphicon glyphicon-calendar"></span>
                    <span class="date-text"> <?php echo "Any Time"; ?> </span>
                </button>
            </div>

            <button id="webform-filter-submit" class="btn btn-primary pull-right" style="margin-top: 5%;">Submit</button>
        </form>
    </div>
</nav>

<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                Webform report
                <span class="webform-date-range" style="padding-left: 10px;"></span>
                <div class="pull-right" style="border:0px solid black;">
                    <a href="#filter-right" class="btn btn-default btn-xs">
                        <span class="glyphicon glyphicon-filter" style="padding-left:3px; color:black;"></span> Filter
                    </a>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                Completed Webform
                            </div>
                            <div class="panel-body" id="completed-panel">
                                <table class='table table-striped table-condensed'>
                                    <thead>
                                    <tr>
                                        <th>Completed</th>
                                        <th>Uncompleted</th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                Where did you hear about us?
                            </div>
                            <div class="panel-body" id="hear-panel">
                                <table class='table table-striped table-condensed'></table>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                Origin/source of the lead
                            </div>
                            <div class="panel-body" id="source-panel">
                                <table class='table table-striped table-condensed'>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="<?php echo base_url() ?>assets/js/filter.js"></script>


<script>
    $(document).ready(function () {
        hsl.init();
    });
</script>