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
            <table class="table ajax-table calendars-table">
                <thead>
                    <tr>
                        <th>Campaign</th>
                        <th>Calendar</th>
                        <th>Options</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($userCalendars as $row): ?>
                        <tr class="google-calendar-id-<?php echo $row['google_calendar_id'] ?>">
                            <td><?php echo $row['campaign_name'] ?></td>
                            <td><?php echo $row['calendar_name'] ?></td>
                            <td><button class="tt pull-left btn btn-default btn-xs remove-google-calendar" data-id="<?php echo $row['google_calendar_id'] ?>" data-calendar-id="<?php echo $row['calendar_id'] ?>" data-toggle="tooltip" data-placement="top" title="Detatch the Google calendar from this user"><i class="fa fa-remove info red"></i> Delete</button><button class="tt pull-left marl btn btn-default btn-xs sync-google-calendar" data-user-id="<?php echo $row['user_id'] ?>" data-id="<?php echo $row['google_calendar_id'] ?>" data-toggle="tooltip" data-placement="top" title="Sync with google calendar (add the Google calendar events to the local system calendar for this user)"><i class="fa fa-refresh info green"></i> Sync</button></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

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