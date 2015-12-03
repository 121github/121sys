// JavaScript Document
var admin = {
        //initialize all the generic javascript datapickers etc for this page
        init: function() {

            this.slot_options = $('#slot-options-panel');
            this.slot_days = $('#slot-day-panel');
            this.slot_date = $('#slot-date-panel');
            this.slot_date_form = $('#slot-date-form');
            $(document).on('click', '.close-btn', function(e) {
                e.preventDefault();
                admin.hide_edit_form();
            });

            $('.timepicker').datetimepicker({
                format: 'HH:mm:ss'
            });

        },
        hide_edit_form: function() {
            $('#form-container').fadeOut(1000, function() {
                $('#table-container').fadeIn();
            });
        },
        slots: {
            //initalize the group specific buttons 
            init: function() {
                admin.slot_options.on('change', '#slot-campaign-select', function() {
                    admin.slots.load_users($(this).val());
                });
                admin.slot_options.on('change', '#slot-user-select', function() {
                    admin.slots.get_user_slot_group($(this).val());
                });
                admin.slot_options.on('change', '#slot-select', function() {
                    admin.slots.load_day_slots($(this).val());
                    admin.slots.load_date_slots($('#slot-user-select').val());
                    admin.slots.get_slots_in_group($(this).val());
                });
                admin.slot_days.on('click', '#save-day-slots', function(e) {
                    e.preventDefault();
                    admin.slots.save_day_slots();
                });
                $('.date').datetimepicker({
                    format: 'DD/MM/YYYY',
                    pickTime: false
                });
                $('#date-from').on("dp.hide", function(e) {
                    $('#date-to').val($('#date-from').val());
                });
                admin.slot_date_form.on('click', '#add-date-rule', function(e) {
                    e.preventDefault();
                    admin.slots.save_date_slots();
                });
                admin.slot_date.on('click', '.delete-slot-override', function(e) {
                    e.preventDefault();
                    admin.slots.delete_date_slots($(this).attr('data-id'));
                });
                //start the function to load the groups into the table
                admin.slots.load_slots();
            },
            delete_date_slots: function(id) {
                $.ajax({
                    url: helper.baseUrl + 'admin/delete_date_slots',
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        id: id
                    }
                }).done(function(response) {
                    if (response.success) {
                        flashalert.success("Date slot deleted");
                        admin.slots.load_date_slots($('#slot-user-select').val());
                    }
                })
            },
            save_day_slots: function() {
                $.ajax({
                    url: helper.baseUrl + 'admin/save_day_slots',
                    type: "POST",
                    dataType: "JSON",
                    data: $('#slot-form').serialize()
                }).done(function(response) {
                    if (response.success) {
                        flashalert.success("Default slot configuration saved");
                    }
                }).fail(function() {
                    flashalert.danger("There was an error saving the configuration");
                });
            },
            save_date_slots: function() {
                $.ajax({
                    url: helper.baseUrl + 'admin/save_date_slots',
                    type: "POST",
                    dataType: "JSON",
                    data: $('#slot-date-form').serialize() + '&user_id=' + $('#slot-user-select').val()
                }).done(function(response) {
                    if (response.success) {
                        flashalert.success("Date slot override added");
                        admin.slots.load_date_slots($('#slot-user-select').val());
                    } else {
						  flashalert.danger(response.error);
					}
                }).fail(function() {
                    flashalert.danger("There was an error saving the configuration");
                });
            },
            load_date_slots: function(id) {
                $.ajax({
                    url: helper.baseUrl + 'admin/get_date_slots',
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        id: id
                    }
                }).done(function(response) {
                    var table = "<table class='table table-striped'><thead><th>Date</th><th>Slot</th><th>Times</th><th>Max Slots</th><th></th></thead><tbody>";
                    if (response.length > 0) {
                        $.each(response, function(i, row) {
                            table += "<tr><td>" + row.date + "</td><td>" + row.slot_name + "</td><td>" + row.slot_start + " til " + row.slot_end + "</td><td>" + row.max_slots + "</td><td><button class='btn btn-default btn-xs delete-slot-override' data-id=" + row.slot_override_id + ">Delete</button></td></tr>";
                        });
                        table += "</tbody></table>";
                    } else {
                        table = "No date slots have been configured for this user";
                    }
                    admin.slot_date.find('.panel-body').html(table);
                });
            },
            load_day_slots: function(id) {
                $.ajax({
                    url: helper.baseUrl + 'admin/get_day_slots',
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        id: id
                    }
                }).done(function(response) {
                    var table = "<table class='table table-striped'><thead><th>Slot</th><th>Times</th><th>Monday</th><th>Tuesday</th><th>Wednesday</th><th>Thursday</th><th>Friday</th><th>Saturday</th><th>Sunday</th></thead><tbody>";
                    if (response.length > 0) {
                        $.each(response, function(i, row) {
                            table += "<tr><td>" + row.slot_name + "</td><td>" + row.slot_start + " til " + row.slot_end + "</td><td><input type='text' class='form-control' name='Mon[" + row.appointment_slot_id + "]' /></td><td><input type='text' class='form-control' name='Tue[" + row.appointment_slot_id + "]' /></td><td><input type='text' class='form-control'  name='Wed[" + row.appointment_slot_id + "]' /></td><td><input type='text' class='form-control' name='Thu[" + row.appointment_slot_id + "]' /></td><td><input type='text' class='form-control'  name='Fri[" + row.appointment_slot_id + "]' /></td><td><input type='text' class='form-control'  name='Sat[" + row.appointment_slot_id + "]' /></td><td><input type='text' class='form-control'  name='Sun[" + row.appointment_slot_id + "]' /></td></tr>"
                        });
                        table += "</tbody></table>";
                        table += '<div class="form-group"><button class="btn btn-primary" id="save-day-slots">Save Slot Config</button></div>'
                    } else {
                        table = "No slot group has been assigned user";
                    }
                    admin.slot_days.find('.panel-body').html(table);
                    admin.slots.configure_days();
                });

            },
            configure_days: function() {
                $.ajax({
                    url: helper.baseUrl + 'admin/get_user_day_slots',
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        id: $('#slot-user-select').val()
                    }
                }).done(function(response) {
                    $.each(response, function(i, row) {
                        if (row.day_name == "default") {
                            $('[name*="[' + row.appointment_slot_id + ']"]').val(row.max_slots);
                        } else {
                            $('[name="' + row.day_name + '[' + row.appointment_slot_id + ']"]').val(row.max_slots);
                        }
                    });
                });
            },
            load_users: function(id) {
                var user_select = $('#slot-user-select');
                $.ajax({
                    url: helper.baseUrl + 'admin/get_attendees',
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        id: id
                    }
                }).done(function(response) {
                    user_select.html('')
                    $.each(response, function(i, row) {
                        user_select.append('<option value="' + row.user_id + '">' + row.name + '</option>');
                    });
                    user_select.prop('disabled', false).selectpicker('refresh');
                    admin.slots.get_user_slot_group($('#slot-user-select').val());
                })

            },
            get_slots_in_group: function(id) {
                var slot_select = $('#slot-date-rule-select');
                slot_select.prop('disabled', true).selectpicker('refresh');
                $.ajax({
                    url: helper.baseUrl + 'admin/get_slots_in_group',
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        id: id
                    }
                }).done(function(response) {
                    if (response.data.length > 0) {
                        slot_select.html('')
                        $.each(response.data, function(i, row) {
                            slot_select.append('<option value="' + row.appointment_slot_id + '">' + row.slot_name + '</option>');
                        });
                        $('#date-slots-form-notice').hide();
                        admin.slot_date_form.show();
                        slot_select.prop('disabled', false).selectpicker('refresh');
                    } else {
                        $('#date-slots-form-notice').show();
                        $('#date-slots-form').hide();
                    }
                });
            },

            get_user_slot_group: function(id) {
                var slot_select = $('#slot-select');
                $.ajax({
                    url: helper.baseUrl + 'admin/get_user_slot_group',
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        id: id
                    }
                }).done(function(response) {
                    slot_select.find('option').removeAttr('selected');
                    $.each(response.data, function(i, row) {
                        slot_select.selectpicker('val', row.slot_group_id);
                    });
                    slot_select.prop('disabled', false).selectpicker('refresh');
                    admin.slots.load_day_slots(slot_select.val());
                    admin.slots.load_date_slots($('#slot-user-select').val());
                    admin.slots.get_slots_in_group(slot_select.val());
                    $('.slot-rule-user').text(response.name);
                });
            },
            //this function reloads the groups into the table body
            load_slots: function() {
                $.ajax({
                    url: helper.baseUrl + 'admin/get_all_slots',
                    type: "POST",
                    dataType: "JSON",
                    beforeSend: function() {
                        $('#form-container').hide();
                        $('#table-container').html('<img src="' + helper.baseUrl + 'assets/img/ajax-loader-bar.gif" />').show();
                    }
                }).done(function(response) {
                    var table = '<table class="table"><thead><tr><th>ID</th><th>Name</th><th>Start</th><th>End</th><th>Description</th><th>Options</th></tr></thead><tbody>';
                    if (response.length > 0) {
                        $.each(response, function(i, val) {
                            table += "<tr><td>" + val.appointment_slot_id + "</td><td>" + val.slot_name + "</td><td>" + val.slot_start + "</td><td>" + val.slot_end + "</td><td>" + val.slot_description + "</td><td><button data-id='" + val.appointment_slot_id + "' class='btn btn-default btn-xs edit-btn'>Edit</button> <button class='btn btn-default btn-xs del-btn' data-id='" + val.appointment_slot_id + "'>Delete</button></td></tr>";
                        });
                        table += '</body></table>';
                        $('#table-container').html(table);
                    } else {
                        $('#form-container').hide();
                        $('#table-container').html('No slots were found').show();
                    }
                });
            }

        }
    }
    /* ==========================================================================
    MODALS ON THIS PAGE
     ========================================================================== */
var modal = {
    delete_slot: function(id) {
        $('.modal-title').text('Confirm Delete');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').text('Are you sure you want to delete this slot?');
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
            admin.slots.remove(id);
            $('#modal').modal('toggle');
        });
    }
}