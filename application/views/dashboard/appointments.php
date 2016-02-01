<div class="row">
<div class="col-xs-12">
<div class="panel panel-primary">
<div class="panel-heading">Appointments List
<div class="pull-right">
            <form class="filter-form">
                <div class="btn-group">
                    <a type="button" class="btn btn-default btn-xs" data-modal="choose-columns"
                            data-table-id="3"><span
                            class="fa fa-table"></span> Columns
                    </a>
                </div>
                <div class="btn-group">
                    <a class="btn btn-default btn-xs" href="<?php echo base_url() ?>appointments/mapview">Show Map
                    </a>
                </div>
                
                <input type="hidden" name="group"> 
            </form>
            </div>
</div>
<div class="panel-body" style="padding:0; margin:0; position:relative">
  <div class="loading-overlay"></div>
        <div class="row" style="padding:0; margin:0;">
        <div class="col-xs-12">
        <div id="view-container">                <img class="table-loading"
                     src='<?php echo base_url() ?>assets/img/ajax-loader-bar.gif'>
            </div>
            </div>
 
        </div>
    </div>

</div>
</div>
</div>
</div>
<style>
body { }
.loading-overlay {  position:absolute; width:100%; height:100%; background:#000; opacity: 0.4; filter: alpha(opacity=40); z-index:10; top:0; left:0 }
.container-fluid { }
.top-row { padding:10px 10px 0; }
.bottom-row { padding:0px 10px 10px; }
.panel-body { overflow:hidden }
#view-container { margin:0; padding:0 0px; overflow-y:auto; height:100%; overflow-x:hidden; }
</style>

<script type="text/javascript">
var process_url = 'appointments/appointment_data';
var page_name ="appointment";
var table_id = 3; //the records table
var table_columns = <?php echo json_encode($columns) ?>; //the columns in this view

    $(document).ready(function () {
        view.init();
        view.has_filter = "<?php echo ((isset($_SESSION['filter']['values']) && !empty($_SESSION['filter']['values']))?true:false) ?>";
    });

</script>