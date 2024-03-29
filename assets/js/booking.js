var fullcalendar;
var temp_attendee = false;
var temp_type = false;
if (typeof quick_planner == "undefined") {
    var quick_planner = {};
}
if (typeof minTime == "undefined") {
    var minTime = '07:00';
    var maxTime = '20:00';
}
if (typeof hiddenDays == "undefined") {
    var hiddenDays = [];
}
if (typeof slot_duration == "undefined") {
    var slot_duration = '00:30:00'
}

var calendar = {
    init: function (view) {
        this.click_date = "";
        if (view) {
            this.view = view
        } else {
            this.view = "agendaWeek";
        }
        $modal.on('click', '[data-toggle="tab"]', function (e) {
            if ($(this).attr('href') == "#apprules") {
                $modal.find('#save-rule-btn').hide();
            } else {
                $modal.find('#save-rule-btn').show();
            }
        });
        $modal.on('change', '.attendee-select', function () {
            calendar.get_slots_in_group($(this).val());
        });
        $modal.on('click', '#save-rule-btn', function () {
            calendar.save_rules();
        });
        $modal.on('click', '.del-rule-btn', function () {
            calendar.delete_rule($(this).attr('item-id'), $(this).attr('item-date'));
        });
        $('#calendar').on('change', '#attendee-select, #status-select, #type-select', function () {
            calendar.load_rules();
            $('#calendar').fullCalendar('refetchEvents');
        });
        this.left_buttons = 'today,prev,next,month,agendaWeek,agendaDay';
        if (helper.permissions['slot availability'] > 0) {
            this.left_buttons += ' rulesButton';
        }
        if (helper.permissions['google calendar'] > 0) {
            this.left_buttons += ' googleButton';
        }

        this.calendarOptions = {
            timeFormat: 'H:mm',
            columnFormat: 'ddd D/M',
            minTime: minTime,
            maxTime: maxTime,
            hiddenDays: hiddenDays,
            slotDuration: slot_duration,
            //height: 700,
            header: {
                left: calendar.left_buttons,
                right: 'title',
               // right: 'month,agendaWeek,agendaDay'
            },
            defaultView: calendar.view,
            eventDrop: function (event) {
                calendar.set_event_time(event.id, event.start, event.end)
            },
            eventResize: function (event) {
                calendar.set_event_time(event.id, event.start, event.end)
            },
            dayClick: function (date, jsEvent, view) {
                calendar.click_date = date;
                calendar.view = view;
                return false;
            },
            eventAfterRender: function (event, element, view) {
                var cancelled = "";
                //Disable drag and drop if the event is cancelled
                if (event.status == 0) {
                    event.editable = false;
                    cancelled = "fa fa-ban"
                }
                var event_extra = '<span class="' + event.icon + '"></span><span class="' + cancelled + ' red pull-right"></span> ';
                if (typeof event.distance !== "undefined") {
                    event_extra += event.distance;
                } else {
                    $(element).find('.fc-time').css('display', 'inline');
                }

                $(element).attr("data-id", event._id).attr('data-modal', 'view-appointment').find('.fc-content').prepend(event_extra);
            },
            viewRender: function (event, element, view) {
                calendar.load_rules();

                if (typeof $('#datetimepicker2').data("DateTimePicker") != "undefined") {
                    var view_mode = "months";
                    var format = "YYYY-MM";
                    switch (view.name) {
                        case "month":
                            view_mode = "months";
                            format = "YYYY-MM";
                            break;
                        case "agendaWeek":
                            view_mode = "days";
                            format = "YYYY-MM-DD";
                            break;
                        case "agendaDay":
                            view_mode = "days";
                            format = "YYYY-MM-DD";
                            break;
                        default:
                            view_mode = "months";
                            format = "YYYY-MM";
                            break;
                    }

                    $('#datetimepicker2').data("DateTimePicker").format(format).viewMode(view_mode);
                }
            },
            eventAfterAllRender: function (event, element, view) {
                $('#calendar .fc-row td,#calendar .fc-time-grid .fc-widget-content').addClass('context-menu-one');
            },
            editable: true,
            loading: function (bool) {
                if (bool) {
                    $('#loading-overlay').fadeIn();
                }
                else {
                    $('#loading-overlay').fadeOut();
                }
            },
            customButtons: {
                googleButton: {
                    text: 'Google Calendar',
                    click: function () {
                        window.location.href = helper.baseUrl + 'booking/google';
                    }
                },
                rulesButton: {
                    text: 'Rules',
                    click: function () {
                        calendar.rule_modal(false, false);
                        calendar.show_rules_in_day();
                    }
                }
            },
            eventSources: [
                {
                    url: helper.baseUrl + 'booking/events',
                    type: 'POST',
                    data: function () { // a function that returns an object
                        var attendee = temp_attendee ? temp_attendee : $('#calendar').find('#attendee-select').val();
                        var appointment_type = temp_type ? temp_type : $('#calendar').find('#type-select').val();
                        var status = (typeof $('#status-select').val() != "undefined" ? $('#status-select').val() : 1);
                        var postcode = false;

                        if (typeof quick_planner.company_postcode !== "undefined") {
                            postcode = quick_planner.company_postcode;
                        } else if (typeof quick_planner.contact_postcode !== "undefined") {
                            postcode = quick_planner.contact_postcode
                        }
                        return {
                            attendee: attendee,
                            status: status,
                            appointment_type: appointment_type,
                            postcode: postcode
                        };
                    }
                }
            ]
        }
        //create the calendar
        this.fullCalendar = $('#calendar').fullCalendar(calendar.calendarOptions);
        //Style
		$('#calendar .fc button').css('height','').css('padding','');
		$('#calendar .fc-button-group').addClass('btn-group').removeClass('fc-button-group');
		$('#calendar .fc-button').addClass('btn btn-default').removeClass('fc-button fc-state-default');
        $('#calendar .fc-toolbar').addClass('navbar navbar-inverse navbar-fixed-top');
        $('#calendar .fc-left').addClass('navbar-btn');
        $('#calendar .fc-right').addClass('navbar-btn');
        $('#calendar .fc-center').addClass('navbar-btn');
        $('#calendar .fc-toolbar').css('margin-top', '50px');
        $('#calendar .fc-toolbar h2').css('color', 'white');
        $('#calendar .fc-toolbar h2').css('font-size', '22px');
        $('#calendar .fc-toolbar').css('padding-left', '10px');
        $('#calendar').css('margin-top', '50px');

        //add the filters to the top
        calendar.build_options();
        //initialize the popup menu on left click of a calendar cell
        calendar.init_context_menu();

    },
    build_options: function (attendee, type) {
        calendar.type_filter(type);
        if (helper.permissions['own appointments'] > 0) {
            //no attendee filter
        } else {
            calendar.attendee_filter(attendee);
        }
        calendar.month_filter();
        //calendar.status_filter();
    },
    destroy: function () {
        $('#calendar').fullCalendar('destroy');
    },
    init_context_menu: function () {
        $.contextMenu({
            autoHide: true,
            trigger: 'left',
            selector: '.context-menu-one',
            callback: function (key, options) {
                var elem = options.$trigger;
                var cell = elem.closest('td');
                var cellIndex = cell[0].cellIndex
                if (key == "view") {
                    var date = moment(calendar.click_date).format('YYYY-MM-DD')
                    $('#calendar').fullCalendar('changeView', 'agendaDay');
                    $('#calendar').fullCalendar('gotoDate', date);
                }
                if (key == "create") {
                    var date = moment(calendar.click_date).format('YYYY-MM-DD HH:mm');
                    var attendee = $('#calendar').find('#attendee-select').val();
                    var type = $('#calendar').find('#type-select').val();
                    if (typeof record == "undefined") {
                        modals.search_records("create-appointment", date);
                    } else {
                        modals.create_appointment(record.urn, date, attendee, type);
                    }
                }
                if (key == "rule") {
                    var date = moment(calendar.click_date).format('YYYY-MM-DD')
                    calendar.rule_modal(date, 'create');
                    calendar.show_rules_in_day(date);
                }
            },
            items: {
                "view": {
                    name: "View Day",
                    icon: function (opt, $itemElement, itemKey, item) {
                        $itemElement.html('<span class="fa fa-calendar"></span> View Day');
                        return 'context-menu-icon-updated';
                    }
                },
                "create": {
                    name: "Create Event",
                    icon: function (opt, $itemElement, itemKey, item) {
                        $itemElement.html('<span class="fa fa-plus"></span> Create Event');
                        return 'context-menu-icon-updated';
                    }
                },
                "rule": {
                    name: "Create Rule",
                    icon: function (opt, $itemElement, itemKey, item) {
                        $itemElement.html('<span class="fa fa-edit"></span> Create Rule');
                        return 'context-menu-icon-updated';
                    }
                },
                /* "sep1": "---------",
                 "quit": {name: "Cancel", icon: function(){
                 return 'context-menu-icon context-menu-icon-quit';
                 }} */
            }
        });
    },
    set_event_time: function (id, start, end) {
        $.ajax({
            url: helper.baseUrl + 'booking/set_event_time',
            type: "POST",
            dataType: "JSON",
            data: {
                id: id,
                start: start.format("YYYY-MM-DD HH:mm"),
                end: end.format("YYYY-MM-DD HH:mm")
            }
        }).done(function () {
            flashalert.success("Appointment was updated");
            //Set appointmnt in google calendar if the attendee has a google account
            var description = '';
            $.ajax({
                url: helper.baseUrl + 'modals/view_appointment',
                data: {
                    id: id
                },
                type: "POST",
                dataType: "JSON"
            }).done(function (view_appointment_response) {
                if (view_appointment_response.success) {
                    $.each(view_appointment_response.data.appointment, function (title, column) {
                        description += '<h3><b>' + title + '</b></h3>\n';
                        $.each(column.fields, function (name, data) {
                            description += name + ' - ' + data.value.replaceAll("<br>", "\n") + '\n';
                        });
                        description += '\n\n';
                    });
                }

                $.ajax({
                    url: helper.baseUrl + 'booking/add_google_event',
                    data: {
                        appointment_id: id,
                        event_status: "confirmed",
                        description: description
                    },
                    type: "POST",
                    dataType: "JSON"
                });
            });
        })
    },
    attendee_filter: function (selected) {
        $('#calendar .fc-toolbar .fc-left').append('<div><select title="All Attendees" id="attendee-select"><option value="">Loading...</option></select></div>');
        var elem = $('#calendar').find('#attendee-select');
        elem.selectpicker();
        if (typeof selected == "undefined") {
            selected = helper.user_id;
        }
        calendar.load_attendees(elem, selected);
    },
    type_filter: function (selected) {
        $('#calendar .fc-toolbar .fc-left').append('<div style="display:none"><select title="All Types" id="type-select"><option value=""></option></select></div>');
        var elem = $('#calendar').find('#type-select');
        elem.selectpicker();
        calendar.load_appointment_types(elem, selected);
    },
    status_filter: function () {
        $('#calendar .fc-toolbar .fc-left').append('<div><select title="All Events" id="status-select">' +
            '<option value="">All Events</option>' +
            '<option value="1" selected>Confirmed</option>' +
            '<option value="0">Cancelled</option>' +
            '</select></div>');
        var elem = $('#calendar').find('#status-select');
        elem.selectpicker();
    },
    month_filter: function () {
         $('#calendar .fc-toolbar .fc-right').append(
            "<div class='input-group date' id='datetimepicker2' style='display:inline;width:50px;margin:5px 0 0 10px '>" +
            "<input type='text' name='cal_date' style='display:none' />" +
            "<span class='input-group-addon calendar-icon' style='color:white; display:inline; padding:0; background:white; border:none'>" +
            "<span class='fa fa-calendar-o'></span>" +
            "</span>" +
            "</div>"
        );
        $('#datetimepicker2').datetimepicker({
            defaultDate: moment(),
            format: 'YYYY-MM-DD',
            enabledHours: false,
            viewMode: 'days'
        }).on('dp.change', function (ev) {
            $('#calendar').fullCalendar('gotoDate', new Date($('input[name="cal_date"]').val()));
        });

    },
    load_attendees: function (elem, selected) {
        var postcode = false;
        if (typeof quick_planner.company_postcode !== "undefined") {
            postcode = quick_planner.contact_postcode;
        } else if (typeof quick_planner.contact_postcode !== "undefined") {
            postcode = quick_planner.contact_postcode
        }
        $.ajax({
            url: helper.baseUrl + 'calendar/get_calendar_users',
            type: "POST",
            dataType: "JSON",
            data: {
                campaigns: $('#campaign-cal-select').val(),
                postcode: postcode,
                appointment_type: $('#calendar').find('#type-select').val()
            }
        }).done(function (response) {
            var $options = "<option value=''>All Attendees</options>";
            $.each(response.data, function (k, v) {
                var distance = typeof v.distance !== "undefined" ? v.distance : "";
                $options += "<option data-subtext='" + distance + "' " + (v.id == selected ? "selected" : "") + " value='" + v.id + "'>" + v.name + "</options>";
            });
            elem.html($options).selectpicker('refresh');
        });
    },
    load_appointment_types: function (elem, selected) {
        $.ajax({
            url: helper.baseUrl + 'calendar/get_calendar_types',
            type: "POST",
            dataType: "JSON",
            data: {
                campaigns: $('#campaign-cal-select').val()
            }
        }).done(function (response) {
            var type_count = 0;
            var $options = "<option value=''>All Types</options>";
            $.each(response.data, function (k, v) {
                type_count++;
                $options += "<option " + (v.id == selected ? "selected" : "") + " value='" + v.id + "'>" + v.name + "</options>";
            });
            elem.html($options).selectpicker('refresh');
            if (type_count > 1) {
                elem.closest('div').show();
            }
        });
    },
    load_rules: function () {
        $.ajax({
            url: helper.baseUrl + 'calendar/get_appointment_rules/by_user',
            type: "POST",
            dataType: "JSON",
            data: {
                users: $('#calendar').find('#attendee-select').val()
            }
        }).done(function (response) {
            if (response.success) {
                calendar.clear_calendar_rules();
                $.each(response.data, function (key, value) {
                    var title = '';
                    $.each(value, function (i, rule) {
                        var reason = "";
                        if (rule.reason.length > 0) {
                            var reason = ": " + rule.reason;
                        }
                        title += rule.name + ": " + rule.reason + "</br>";
                    });
                    var daynumber = $('.fc-body').find('.fc-day-number[data-date="' + key + '"]').text();
                    var tt = '<span class="pointer" data-toggle="tooltip" title="' + title + '">' + daynumber + '</span>';
                    if ($('#calendar').find('.fc-month-view').length > 0) {
                        $('#calendar').find('.fc-day-number[data-date="' + key + '"]').html($(tt).addClass('tt-month'));
                    }
                    if ($('#calendar').find('.fc-agendaWeek-view').length > 0 || $('#calendar').find('.fc-agendaDay-view').length > 0) {
                        $('#calendar').find('.fc-day-header[data-date="' + key + '"]').append($(tt).addClass('marl glyphicon glyphicon-info-sign tt-week'));
                    }

                });
                setInterval(function () {
                    $('[data-toggle="tooltip"]').tooltip({
                        container: 'body',
                        placement: 'bottom',
                        html: true
                    });
                }, 1000);
            }
        });
    },
    clear_calendar_rules: function () {
        $.each($('#calendar').find('.tt-month'), function (i, k) {
            $(this).tooltip('destroy');
            $(this).removeClass('tt-month');
        });
        $.each($('#calendar').find('.tt-week'), function (i, k) {
            $(this).tooltip('destroy');
            $(this).remove();
        });

    },
    rule_modal: function (date, create) {
        var mheader = "Appointment Rules";
        var mbody = '<ul class="nav nav-tabs" role="tablist">' +
            '<li role="presentation" ' + (!create ? 'class="active"' : '') + '><a href="#apprules" aria-controls="apprules" role="tab" data-toggle="tab">Rules</a></li>' +
            '<li role="presentation"  ' + (create ? 'class="active"' : '') + '><a href="#addrule" aria-controls="addrule" role="tab" data-toggle="tab">Add rule</a></li>' +
            '</ul>' +

            '<div class="tab-content">' +
            '<div role="tabpanel" class="tab-pane ' + (!create ? 'active' : '') + '" id="apprules">' +
            '<div class="rules-per-day"></div>' +
            '</div>' +
            '<div role="tabpanel" class="tab-pane ' + (create ? 'active' : '') + '" id="addrule">' +
            '<form class="appointment-rule-form form-horizontal">' +
            '<p><label>Create new rule<span class="block-day-error" style="color: red; display: none">Select the date(s) to block</span></label>' +
            '<div class="row">' +
            '<div class="col-md-6">' +
            '<label>Start Date</label>' +
            '<input name="date_from" value="" placeholder="Add the start block day..." class="form-control block-day"/>' +
            '</div>' +
            '<div class="col-md-6">' +
            '<label>End Date</label>' +
            '<input name="date_to" value="" placeholder="Add the end block day..." class="form-control block-day-end"/>' +
            '</div>' +
            '</div>' +
            '</p>' +
            '<p>' +
            '<label>Attendee<span class="attendee-error" style="color: red; display: none"> Select a user</span></label>' +
            '<select name="user_id" class="attendee-select" title="Select attendee" data-width="100%" required>' +
            '</select>' +
            '</p>' +
            '<p><label>Time Slot(s)</label>' +
            '<select name="slot_id[]" data-actions-box="true" multiple disabled class="appointment-slot-select" id="appointment-slot-select" title="Select the slot to restrict" data-width="100%" required>' +
            '</select>' +
            '</p>' +
            '<p class="max_apps"><label>Max Slots</label>' +
            '<input name="max_apps" placeholder="Max appointments in the selected slot(s)" value="" class="form-control numeric"/>' +
            '</p>' +
            '<p class="notes"><label>Reason</label>' +
            '<input name="notes" placeholder="Enter comments..." value="" class="form-control"/>' +
            '</p>' +
            '</form>' +
            '</div>' +
            '</div>';
        var mfooter = '<button type="submit" class="btn btn-primary pull-right marl" id="save-rule-btn" ' + (!create ? 'style="display:none"' : '') + '>Save</button> <button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>';
        modals.load_modal(mheader, mbody, mfooter);
        modal_body.css('overflow', 'visible');
        modal_body.css('padding', '0');
        var attendee = $('#calendar').find('#attendee-select').val();
        calendar.load_attendees($modal.find('.attendee-select'), attendee);
        if (attendee > 0) {
            calendar.get_slots_in_group(attendee);
        }
        $('#appointment-slot-select').selectpicker();
        $modal.find('.block-day').datetimepicker({
            format: 'DD/MM/YYYY'
        }).on("dp.hide", function (e) {
            $modal.find('.block-day-end').data("DateTimePicker").minDate(e.date);
        });
        $modal.find('.block-day-end').datetimepicker({
            format: 'DD/MM/YYYY'
        }).on("dp.hide", function (e) {
            $modal.find('.block-day').data("DateTimePicker").maxDate(e.date);
        });
        if (date) {
            $modal.find('input[name="date_from"]').val(timestamp_to_uk(new Date(date)));
            $modal.find('input[name="date_to"]').val(timestamp_to_uk(new Date(date)));
        }
    },
    show_rules_in_day: function (date) {
        $.ajax({
            url: helper.baseUrl + 'booking/get_appointment_rules_by_date',
            type: "POST",
            dataType: "JSON",
            data: {
                date: date,
                user_id: $('#calendar').find('#attendee-select').val()
            }
        }).done(function (response) {
            if (response.data.length > 0) {
                var rules = "",
                    date_col = "";
                scroller_class = "";
                date_col += '<th>Date</th>';
                rules += '<div id="scroller-div"><table class="table ajax-table small"><thead><tr><th>Date</th><th>Attendee</th><th>Slot</th><th>Availability</th><th>Reason</th><th>Remove</th></tr></thead><tbody>';
                $.each(response.data, function (key, value) {
                    rules +=
                        '<tr>' +
                        '<td>' + value.uk_date + '</td>' +
                        '<td>' + value.name + '</td>' +
                        '<td>' + value.slot_name + '</td>' +
                        '<td>' + value.max_slots + '</td>' +
                        '<td>' + value.notes + '</td>' +
                        '<td><button class="btn btn-default btn-xs del-rule-btn pointer" item-id="' + value.slot_override_id + '" item-date="' + value.date + '">Remove</button></td>' +
                        '</tr>';
                });
                rules += '</tbody></table></div>';
                $modal.find('.rules-per-day').html(rules);
                $('#scroller-div').css('overflow', 'auto').css('max-height', '350px');
            } else {
                $modal.find('.rules-per-day').html("No calendar rules have been created yet.");
                //$modal.find('.nav-tabs a[href="#addrule"]').tab('show');
            }
        });
    },
    get_slots_in_group: function (id) {
        var slot_select = $('#appointment-slot-select');
        slot_select.prop('disabled', true).selectpicker('refresh');
        $.ajax({
            url: helper.baseUrl + 'calendar/get_slots_for_attendee',
            type: "POST",
            dataType: "JSON",
            data: {
                id: id
            }
        }).done(function (response) {
            slot_select.html('')
            if (response.length > 0) {
                $.each(response, function (i, row) {
                    //data-subtext="' + row.slot_description + '"
                    slot_select.append('<option value="' + row.appointment_slot_id + '">' + row.slot_name + '</option>');
                });
                slot_select.prop('disabled', false).selectpicker('refresh');
            } else {
                slot_select.append('<option value="4">All day</option>');
                slot_select.prop('disabled', false).selectpicker('refresh').selectpicker('selectAll');
            }

        });
    },
    save_rules: function () {
        $.ajax({
            url: helper.baseUrl + 'admin/save_date_slots',
            type: "POST",

            dataType: "JSON",
            data: $modal.find('form').serialize()
        }).done(function (response) {
            if (response.success) {
                calendar.load_rules();
                flashalert.success("Rule was saved");
                $modal.modal('toggle');
            } else {
                flashalert.danger(response.error);
            }
        });
    },
    delete_rule: function (id, date) {
        $.ajax({
            url: helper.baseUrl + 'admin/delete_date_slots',
            type: "POST",
            dataType: "JSON",
            data: {
                id: id
            }
        }).done(function (response) {
            if (response.success) {
                flashalert.success("Rule was deleted");
                calendar.show_rules_in_day(date);
                calendar.load_rules();
            } else {
                flashalert.danger(response.msg);
            }
        });
    },
    remove_button_triggers: function () {
        $modal.off('click', '[data-toggle="tab"]')
        $modal.off('change', '.attendee-select')
        $modal.off('click', '#save-rule-btn')
        $modal.off('click', '.del-rule-btn')
        $('#calendar').off('change', '#attendee-select, #status-select, #type-select')
    }
}