<nav id="filter-right" class="mm-menu mm--horizontal mm-offcanvas">
  <div style="padding:30px 20px 3px">
    <form class="filter-form">
      <input type="hidden" name="date_from" value="<?php echo date('Y-m-d') ?>">
      <input type="hidden" name="date_to" value="<?php echo date('Y-m-d') ?>">
      <input type="hidden" name="group" value="<?php echo $group ?>">
      <div style="margin-bottom: 5%;">
        <button type="button" class="daterange btn btn-default" data-width="100%"> <span class="glyphicon glyphicon-calendar"></span> <span class="date-text"> <?php echo "Today"; ?> </span> </button>
      </div>
      <label style="margin-top: 5%;">Template</label>
      <select name="templates[]" class="selectpicker template-filter" multiple data-width="100%"
                    data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
        <?php foreach ($templates_by_campaign_group as $type => $data) { ?>
        <optgroup label="<?php echo $type ?>">
        <?php foreach ($data as $row) { ?>
        <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
        <?php } ?>
        </optgroup>
        <?php } ?>
      </select>
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
      <select name="sources[]" class="selectpicker source-filter" id="source-filter" multiple data-width="100%"
                    data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
        <?php foreach ($sources as $row) { ?>
        <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
        <?php } ?>
      </select>
      <label style="margin-top: 5%;">Data Pot</label>
      <select name="pots[]" class="selectpicker pot-filter" id="pot-filter" multiple data-width="100%"
                    data-live-search="true" data-live-search-placeholder="Search" data-actions-box="true">
        <?php foreach ($data_pot as $row) { ?>
        <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
        <?php } ?>
      </select>
      <button id="filter-submit" class="btn btn-primary pull-right" style="margin-top: 5%;">Submit</button>
    </form>
  </div>
</nav>

<!-- /.row -->
<div class="row">
<div class="col-lg-12">
  <div class="panel panel-primary">
    <div class="panel-heading clearfix"><i class="fa fa-bar-chart-o fa-fw"></i> <span class="mobile-only"><?php echo $title ?></span> </div>
    <!-- /.panel-heading -->
    <div class="panel-body email-data table-responsive">
      <div class="row">
        <div class="col-lg-8">
          <table class="table ajax-table">
            <thead>
              <tr>
                <th class="info">#</th>
                <th class="info">Name</th>
                <th class="info">Sent</th>
                <th class="success">Read</th>
                <th class="success">Read(%)</th>
                <th class="warning">Pending</th>
                <th class="warning">Pending(%)</th>
                <th class="danger">Unsent</th>
                <th class="danger">Unsent(%)</th>
                <th class="danger"></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="3"><img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif"/></td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="col-lg-4">
          <ul class="nav nav-tabs" style=" background:#eee; width:100%;">
            <li class="plots-tab"><a href="#chart_div" class="tab" data-toggle="tab">Graphs</a></li>
            <li class="filters-tab active"><a href="#filters" class="tab" data-toggle="tab">Filters</a></li>
            <li class="searches-tab"><a href="#searches" class="tab" data-toggle="tab">Saved Searches</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="filters"></div>
            <div class="tab-pane" id="chart_div">
              <div id="chart_div_sent"></div>
              <div id="chart_div_read"></div>
              <div id="chart_div_pending"></div>
              <div id="chart_div_unsent"></div>
            </div>
            <div class="tab-pane" id="searches"></div>
          </div>
        </div>
      </div>
    </div>
    <!-- /.panel-body --> 
  </div>
</div>
