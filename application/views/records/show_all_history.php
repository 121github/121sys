<div class="panel-heading">History<span class="glyphicon glyphicon-remove pointer pull-right close-history-all"></span></div>
<div class="panel-body" style="overflow: scroll; height: 500px;">
    <div class="history-all-panel">
        <?php $this->view('forms/edit_history_record.php'); ?>
        <div class="history-all-content">
            <table class="table table-striped table-responsive history-all-table">
                <thead></thead>
                <tbody>
                <td>
                    <img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif" />
                </td>
                </tbody>
            </table>
            <div class="form-actions pull-right">
                <button class="marl btn btn-primary close-history-all">Ok</button>
            </div>
        </div>
    </div>
</div>
