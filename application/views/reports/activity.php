
<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-heading clearfix"><i class="fa fa-bar-chart-o fa-fw"></i>
            <span class="mobile-only"><?php echo $title ?></span>
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body table-responsive" style="padding: 0px;">
                <div class="row">
                    <div class="col-md-8">
                        <div class="panel-body report-panel">
                            <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif"/>
                        </div>
                    </div>
                    <div class="col-md-4 desktop-only">
                        <ul class="nav nav-tabs" style=" background:#eee; width:100%;">
                            <li class="plots-tab"><a href="#chart_div" class="tab" data-toggle="tab">Graphs</a></li>
                            <li class="filters-tab active"><a href="#filters" class="tab" data-toggle="tab">Filters</a></li>
                            <li class="searches-tab"><a href="#searches" class="tab" data-toggle="tab">Saved
                                    Searches</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="filters"></div>
                            <div class="tab-pane" id="chart_div">
                                <div id="chart_div_1"></div>
                                <div id="chart_div_2"></div>
                            </div>
                            <div class="tab-pane" id="searches"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.panel-body -->
        </div>
    </div>

    <!-- /.row -->


