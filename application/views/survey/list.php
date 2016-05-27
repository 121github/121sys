<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-body">
                <div class="loading-overlay"></div>
                <div class="row" style="padding:0; margin:0;">
                    <div class="col-xs-12">
                        <div id="view-container"><img class="table-loading"
                                                      src='<?php echo base_url() ?>assets/img/ajax-loader-bar.gif'>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    var process_url = 'survey/process_view';
    var page_name = "survey";
    var table_id = 5; //the surveys table
    var table_columns = <?php echo json_encode($columns) ?>; //the columns in this view

    $(document).ready(function () {
        view.init();
        view.has_filter = "<?php echo((isset($_SESSION['filter']['values']) && !empty($_SESSION['filter']['values'])) ? true : false) ?>";
    });

</script>