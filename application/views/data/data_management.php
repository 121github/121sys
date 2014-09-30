
<div id="wrapper">
<div id="sidebar-wrapper">
 <?php  $this->view('dashboard/navigation.php',$page) ?>
</div>
<div id="page-content-wrapper">
<div id="page-wrapper">
  <div class="row">
    <div class="col-lg-12">
      <h1 class="page-header">Data Management</h1>
    </div>
    <!-- /.col-lg-12 --> 
  </div>
  <!-- /.row -->
  <div class="row">
    <div class="col-lg-12">
      <form id="data-form">
        <div class="panel panel-primary">
        <div class="panel-heading"> <i class="fa fa-bar-chart-o fa-fw"></i>Data Management
          <div class="pull-right">
          <?php if(count($campaigns)>0){ ?>
            <select name="campaign" id="campaign">
              <?php  foreach($campaigns as $row){ ?>
              <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
              <?php } ?>
            </select>
            <select name="state" id="state-select">
              <option value="1">Virgin Data</option>
              <option value="2">In Progress Data</option>
              <option value="3">Call backs</option>
            </select>
            <?php } ?>
          </div>
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">
          <div class="row">
            <div class="col-lg-6">
              <ul id="sliders">
              <?php if(count($campaigns)>0){ ?>
                Please select a campaign and the record status that you wish to reassign. All reassignments will be randomised between the selected users.
                <?php } else { ?>
                You do not have access to any campaigns
                <?php } ?>
              </ul>
              <button type="button" class="btn btn-default" id="reassign-btn" style="display:none">Reassign data</button>
              <!-- /.panel-body --> 
            </div>
            <div class="col-lg-6" id="data-stats" style="display:none">
              <h4>Data stats</h4>
              <ul id="stats-list">
              <li>
              Total records available: <span id="total-records"></span></li>
              <li>
              Assigned records: <span id="assigned-records"></span></li>
              <li>
              Unassigned records: <span id="unassigned-records"></span></li>
                <li>
              Parked records: <span id="parked-records"></span></li>
              </ul>
            </div>
          </div>
        </div>
      </form>
      </div>
      <!-- /.row --> 
    </div>
    <!-- /#page-wrapper --></div>
</div>
<script>
    $(document).ready(function() {
        data_manager.init();
    });
</script>