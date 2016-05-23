<div class="custom-exports">
    <ul id="tabs" class="nav nav-tabs" role="tablist">
        <li class="active"><a role="tab" data-toggle="tab" href="#add-calendar-tab"> Add</a></li>
        <li><a role="tab" data-toggle="tab" href="#view-calendars-tab"> Calendars</a></li>
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="add-calendar-tab">
            <form name="add-google-calendar-form" method="post" class="add-google-calendar-form">
                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                <input type="hidden" name="api_id" value="<?php echo $api_id; ?>">
                <input type="hidden" name="campaign_name">
                <input type="hidden" name="calendar_name">
                <div class="row">
                    <div class="col-lg-6 col-sm-12" style="<?php echo ((count($campaigns) == 1)?'display:none':''); ?>">
                        <label>
                            Campaign
                            <i class="fa fa-info-circle info" data-toggle="tooltip" data-placement="top" title="Select the campaign that will be associated to the calendar selected for the user for the calendar events"></i>
                        </label>
                        <select title='Campaign' name="campaign_id" class="selectpicker" id='campaign-select'>
                            <?php foreach ($campaigns as $row): ?>
                                <option value="<?php echo $row['id'] ?>" <?php echo ((count($campaigns) == 1)?"selected":"") ?>><?php echo $row['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-lg-6 col-sm-12">
                        <label>
                            Calendar
                            <i class="fa fa-info-circle info" data-toggle="tooltip" data-placement="top" title="Select which Google calendar appointments should be linked to for this user"></i>
                        </label>
                        <select title='Calendar' name="calendar_id" class="selectpicker" id='calendar-select' disabled></select>
                    </div>
                </div>
            </form>
        </div>
        <div role="tabpanel" class="tab-pane" id="view-calendars-tab">
            <form name="change-sync-google-calendar-form" method="post" class="change-sync-google-calendar-form">
                <table class="table ajax-table calendars-table small">
                    <thead>
                        <tr>
                            <th>Campaign</th>
                            <th>Calendar</th>
                            <th>Options</th>
                            <th>Sync</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {

        if (<?php echo count($campaigns); ?> == 1) {
            var campaign_selected = $('.add-google-calendar-form').find('#campaign-select option:selected').text();
            $('.add-google-calendar-form').find('input[name="campaign_name"]').val(campaign_selected);

            modals.users.load_add_calendar_tab($('.add-google-calendar-form').find('input[name="user_id"]').val());
        }
		$modal.find('.tt').tooltip();
    });

</script>