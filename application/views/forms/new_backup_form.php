<div class="panel-heading">
    Backup
    <span class="glyphicon glyphicon-remove pull-right close-backup"></span>
</div>
<div class="panel-body">
    <div class="backup-panel">
        <div class="backup-content">
            <form style="padding:10px 20px;" class="form-horizontal new-backup-form">
                <input type="hidden" name="campaign_id">
                <input type="hidden" name="update_date_from">
                <input type="hidden" name="update_date_to">
                <input type="hidden" name="renewal_date_from">
                <input type="hidden" name="renewal_date_to">
                <input type="hidden" name="num_records">
                <div class="form-group input-group-sm">
                    <div class="input-group">
                        <p>Backup File Name</p>
                        <input name="name" type="text" class="form-control">
                    </div>
                </div>
                <p>
                    If you continue with the backup, <span style="font-weight: bold" class="num_records_new"></span> <span style="font-weight: bold">records will be stored</span> and <span style="color: red; font-weight: bold">removed</span> from the current database. <span style="color: red; font-weight: bold">Do you want to continue?</span>
                </p>
                <div class="form-actions pull-right">
                    <button class="marl btn btn-default close-backup">Cancel</button>
                    <button type="submit" class="marl btn btn-primary continue-backup">Continue</button>
                </div>
            </form>
        </div>
    </div>
</div>
