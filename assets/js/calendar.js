window.onresize = function (event) {
    verifyConflictEvents();
};


$(document).on('click', '#switch-cal-view', function (e) {
    e.preventDefault();
    $.ajax({
        url: helper.baseUrl + 'calendar/switch_view',
        type: "POST",
        data: {view: $('#switch-cal-view').attr('data-cal-view')}
    }).done(function () {
        location.reload();
    });

});

function verifyConflictEvents(resize) {
    var cal_width = $('#cal-day-box').width() - 60;
    var complete = [];
    var box_width, boxes = 0;
    $(".pull-left.day-event.day-highlight").each(function () {
        $(this).css('position', 'relative');
        $(this).css('margin-left', '0');
        $(this).find('.event-counts').remove();
        var event = $(this);
        box_width = event.width();

        var schedule = $(this).children("span").html();
        var elements = [];
        var resize = false;
        var events = $('.day-event:contains("' + schedule + '")');
        var events_count = events.length;
        var hover_dots = '';
        $.each(events, function (i, row) {

            hover_dots += '<a href="#" data-modal="view-appointment" data-id="' + $(this).attr('data-id') + '" data-event-id="" data-event-class="" class="pull-left pointer" data-toggle="tooltip" data-html="true" title="' + $(this).attr('data-tt') + '" data-title-original="' + $(this).attr('data-tt') + '" ><span style="background-color:' + $(this).attr('data-color') + '" class="event"></span></a>';
        });

        var boxes = $(".pull-left.day-event.day-highlight").not(':hidden').length;
        if (complete.indexOf(schedule) == -1) {
            event.children("span").after('<div class="day-text event-counts">' + hover_dots + '</div>');

            $(this).siblings().each(function (i) {
                if ($(this).children("span").html() == schedule) {
                    //event.css("position", "absolute");
                    $(this).hide();
                    complete.push(schedule);
                }
            });

        }
    });
    $('[data-toggle="tooltip"]').tooltip();
    //number of boxes

    var boxes = $(".pull-left.day-event.day-highlight").not(':hidden').length;
    //get the number of boxes possible ina row
    var boxes_allowed = Math.floor(cal_width / (box_width + 10));
    box_width = box_width + 15;
    var c = 0;
    $(".pull-left.day-event.day-highlight").not(':hidden').each(function (i) {
        if (!(c % boxes_allowed)) {
            c = 0;
        }
        if (i >= boxes_allowed) {
            $(this).css('position', 'absolute');
            $(this).css('margin-left', c * box_width + 'px');
        }
        c++;
    });

}

function resizeEvents(events) {

}


var appointment_rules = {
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
                    slot_select.append('<option data-subtext="' + row.slot_description + '" value="' + row.appointment_slot_id + '">' + row.slot_name + '</option>');
                });
                slot_select.prop('disabled', false).selectpicker('refresh');
            } else {
                slot_select.append('<option value="">All day</option>');
                slot_select.prop('disabled', false).selectpicker('refresh').selectpicker('selectAll');
            }

        });
    },
    loadAppointmentRules: function () {
        $.ajax({
            url: helper.baseUrl + 'calendar/get_appointment_rules/by_user',
            type: "POST",
            dataType: "JSON",
            data: {users: $('#user-select').val()}
        }).done(function (response) {
            if (response.success) {
                $.each(response.data, function (key, value) {
                    var title = '<table>';
                    $.each(value, function (i, rule) {
                        var reason = "";
                        if (rule.reason.length > 0) {
                            var reason = ": " + rule.reason;
                        }
                        title += "<tr><td style='text-align: left;'>" + rule.name + ": </td><td>" + rule.reason + "</td></tr>";
                    });
                    title += '</table>';
                    $('.cal-month-day').find('[data-cal-date="' + key + '"].rule-tooltip').css('border-radius', '10px').css('padding', '3px 5px').css('color', '#fff').css('background-color', 'red').attr('data-html', 'true').attr('data-original-title', "<small>"+title+"</small>").tooltip();

                    $('.cal-week-box').find('.rule-tooltip[data-cal-date="' + key + '"]').css('color', 'red').attr('item-rules', value.length).attr('data-original-title', title).show();
                    $('#cal-day-box').find('.rule-tooltip[data-cal-date="' + key + '"]').css('color', 'red').attr('item-rules', value.length).attr('data-original-title', title).show();
                });
            }
        });
    },
    loadAppointmentRulesByDate: function (block_day) {
        if (block_day == "0") {
            var block_day = false;
        }
        $.ajax({
            url: helper.baseUrl + 'calendar/get_appointment_rules_by_date',
            type: "POST",
            dataType: "JSON",
            data: {date: block_day}
        }).done(function (response) {
            if (response.success) {
                var rules = "", date_col = "";
                scroller_class = "";
                if (block_day) {
                    rules += '<div><h4>' + block_day + '</h4></div>';
                } else {
                    date_col += '<th>Date</th>';
                }
                rules += '<div id="scroller-div"><table class="table ajax-table small"><thead><tr>' + date_col + '<th>Attendee</th><th>Reason</th><th>Slot</th><th>Remove</th></tr></thead><tbody>';
                $.each(response.data, function (key, value) {
                    var date_val = "";
                    if (!block_day) {
                        var date_val = '<td>' + value.uk_date + '</td>';
                    }
                    rules +=
                        '<tr>' +
                        date_val +
                        '<td>' + value.name + '</td>' +
                        '<td>' + value.reason + '<div style="font-size: 10px;">' + value.other_reason + '</div></td>' +
                        '<td>' + (value.slot_name ? value.slot_name : 'All day') + '</td>' +
                        '<td><span class="glyphicon glyphicon-remove del-rule-btn pointer" item-id="' + value.appointment_rules_id + '" item-date="' + block_day + '"></span></td>' +
                        '</tr>';
                });
                rules += '</tbody></table></div>';
                $('#modal').find('.rules-per-day').html(rules);
                $('#scroller-div').css('overflow', 'auto').css('max-height', '350px');
            }
            else {
                $('#modal').find('.rules-per-day').html("No calendar rules have been created yet.");
                $('#modal').find('.nav-tabs a[href="#addrule"]').tab('show');
                $('.cal-month-day').find('.block-day-btn.' + block_day).css('color', 'black').attr('item-rules', '').attr('data-original-title', '').hide();
                $('.cal-week-box').find('.block-day-btn.' + block_day).css('color', 'black').attr('item-rules', '').attr('data-original-title', '').show();
                $('#cal-day-box').find('.block-day-btn.' + block_day).css('color', 'black').attr('item-rules', '').attr('data-original-title', '').show();

            }
        });
    },
    loadAppointmentSlots: function () {
        $.ajax({
            url: helper.baseUrl + 'calendar/get_appointment_slots',
            type: "POST",
            dataType: "JSON"
        }).done(function (response) {
            $('#modal').find('#appointment-slot-select').empty();
            var $options = '<option value="">All day</option>';
            $.each(response.data, function (k, v) {
                $options += "<option value='" + v.id + "'>" + v.name + "</options>";
            });
            $('#modal').find('#appointment-slot-select').html($options).selectpicker('refresh');
        });
    },
    loadAppointmentRulesReasons: function () {
        $.ajax({
            url: helper.baseUrl + 'calendar/get_appointment_rule_reasons',
            type: "POST",
            dataType: "JSON"
        }).done(function (response) {
            $('#modal').find('#reason-select').empty();
            var $options = '<option value="">Choose a reason...</option>';
            $.each(response.data, function (k, v) {
                $options += "<option value='" + v.id + "'>" + v.name + "</options>";
            });
            $('#modal').find('#reason-select').html($options).selectpicker('refresh');
        });
    },
    loadAppointmentRulesAttendees: function () {
        $.ajax({
            url: helper.baseUrl + 'calendar/get_calendar_users',
            type: "POST",
            dataType: "JSON",
            data: {campaigns: $('#campaign-cal-select').val()}
        }).done(function (response) {
            $('#modal').find('#attendee-select').empty();
            var $options = '<option value="">Choose an attendee...</option>';
            $.each(response.data, function (k, v) {
                $options += "<option value='" + v.id + "'>" + v.name + "</options>";
            });
            $('#modal').find('#attendee-select').html($options).selectpicker('refresh');
        });
    },
    delAppointmentRules: function (appointment_rules_id, date) {
        $.ajax({
            url: helper.baseUrl + 'calendar/delete_appointment_rule',
            type: "POST",
            dataType: "JSON",
            data: {appointment_rules_id: appointment_rules_id}
        }).done(function (response) {
            if (response.success) {
                flashalert.success(response.msg);
                appointment_rules.loadAppointmentRulesByDate(date);
                appointment_rules.loadAppointmentRules();
            }
            else {
                flashalert.danger(response.msg);
            }
        });
    },
    get_calendar_users: function (reload_cal) {
		console.log("Reloading attendees");
        $.ajax({
            url: helper.baseUrl + 'calendar/get_calendar_users',
            type: "POST",
            dataType: "JSON",
            data: {campaigns: $('#campaign-cal-select').val()}
        }).done(function (response) {
            $('#user-select').empty();
            var $options = "";
            $.each(response.data, function (k, v) {
                $options += "<option value='" + v.id + "'>" + v.name + "</options>";
            });
            $('#user-select').html($options).selectpicker('refresh');
            if (reload_cal) {
                calendar.view();
            }
        })
    }
};


var calendar_modals = {
    distance: function () {
        modals.default_buttons();
        modal_header.text('Set maximum distance');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        })
        modal_body.empty().html($('#dist-form').html());
        $('#modal').find('.distance-select').selectpicker();
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function (e) {
            $(this).button('loading');
            var postcode = $(this).closest('#modal').find('.current_postcode_input').val();
            var distance = $(this).closest('#modal').find('.distance-select').selectpicker('val');
            $('#modal').modal('toggle');
            //have to use attr because val isnt working, maybe because think its because its hidden?
            $('#dist-form').find('.current_postcode_input').val(postcode).attr('value', postcode);
            $('#dist-form').find('.distance-select option').each(function () {
                $(this).removeAttr('selected');
            });
            $('#dist-form').find('.distance-select').val(distance).children('option[value="' + distance + '"]').attr('selected', true);
            calendar.view();
            $(this).button('reset');
        });
    },
    addAppointmentRule: function (block_day) {
        modals.default_buttons();

        modal_header.text('Add appointment rule');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').empty().html(
            '<ul class="nav nav-tabs" role="tablist">' +
            '<li role="presentation" class="active"><a href="#apprules" aria-controls="apprules" role="tab" data-toggle="tab">Rules</a></li>' +
            '<li role="presentation"><a href="#addrule" aria-controls="addrule" role="tab" data-toggle="tab">Add rule</a></li>' +
            '</ul>' +

            '<div class="tab-content">' +
            '<div role="tabpanel" class="tab-pane active" id="apprules">' +
            '<div class="rules-per-day"></div>' +
            '</div>' +
            '<div role="tabpanel" class="tab-pane" id="addrule">' +
            '<form class="appointment-rule-form form-horizontal">' +
            '<p><label>Block Slots<span class="block-day-error" style="color: red; display: none">Select the date(s) to block</span></label>' +
            '<div class="row">' +
            '<div class="col-md-6">' +
            '<label>Start Date</label>' +
            '<input name="block_day" value="" placeholder="Add the start block day..." class="form-control block-day"/>' +
            '</div>' +
            '<div class="col-md-6">' +
            '<label>End Date</label>' +
            '<input name="block_day_end" value="" placeholder="Add the end block day..." class="form-control block-day-end"/>' +
            '</div>' +
            '</div>' +
            '</p>' +
            '<p>' +
            '<label>Attendee<span class="attendee-error" style="color: red; display: none"> Select a user</span></label>' +
            '<select name="user_id" class="attendee-select" id="attendee-select" title="Select attendee" data-width="100%" required>' +
            '</select>' +
            '</p>' +
            '<p><label>Time Slot(s)</label>' +
            '<select name="appointment_slot_id[]" data-actions-box="true" multiple disabled class="appointment-slot-select" id="appointment-slot-select" title="Select the slot to restrict" data-width="100%" required>' +
            '</select>' +
            '</p>' +
            '<p><label>Reason<span class="reason-error" style="color: red; display: none"> Select a reason</span></label>' +
            '<select name="reason_id" class="reason-select" id="reason-select" title="Choose the reason..." data-width="100%" required>' +
            '</select>' +
            '</p>' +
            '<p class="other_reason"><label>Other Reason</label>' +
            '<input name="other_reason" placeholder="Other reason..." value="" class="form-control"/>' +
            '</p>' +
            '</form>' +
            '</div>' +
            '</div>'
        );
        modal_body.css('overflow', 'visible');
        modal_body.css('padding', '0');
        //Add the rules
        appointment_rules.loadAppointmentRulesByDate(block_day);


        //Get the reasons options
        appointment_rules.loadAppointmentRulesReasons();

        //Get the slots options
        //appointment_rules.loadAppointmentSlots();
        $('#appointment-slot-select').selectpicker();

        //Get the attendees options
        appointment_rules.loadAppointmentRulesAttendees();

        $(document).on('change', '#attendee-select', function () {
            appointment_rules.get_slots_in_group($(this).val());
        });

        $('#modal').find('.block-day').datetimepicker({
            format: 'DD/MM/YYYY'
        }).on("dp.hide", function (e) {
            $('#modal').find('.block-day-end').data("DateTimePicker").minDate(e.date);
        });
        $('#modal').find('.block-day-end').datetimepicker({
            format: 'DD/MM/YYYY'
        }).on("dp.hide", function (e) {
            $('#modal').find('.block-day').data("DateTimePicker").maxDate(e.date);
        });
        if (block_day) {
            $('#modal').find('input[name="block_day"]').val(timestamp_to_uk(new Date(block_day)));
            $('#modal').find('input[name="block_day_end"]').val(timestamp_to_uk(new Date(block_day)));
        }
        $('#modal').find('.other_reason').hide();

        $('#modal').find('.reason-select').on('change', function () {
            var selected = $('#modal').find('.reason-select option:selected').val();
            $('#modal').find('.appointment-rule-form').find('input[name="other_reason"]').val('');
            if (selected == 3) {
                $('#modal').find('.other_reason').show();
            }
            else {
                $('#modal').find('.other_reason').hide();
            }
        });

        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function (e) {
            var block_day = $('#modal').find('input[name="block_day"]').val();
            var reason_id = $('#modal').find('.reason-select').selectpicker('val');
            var other_reason = $('#modal').find('input[name="other_reason"]').val();
            var attendee = $('#modal').find('.attendee-select').selectpicker('val');
            if (!block_day) {
                $('#modal').find('.block-day-error').show();
            }
            else {
                $('#modal').find('.block-day-error').hide();
            }
            if (!reason_id) {
                $('#modal').find('.reason-error').show();
            }
            else {
                $('#modal').find('.reason-error').hide();
            }
            if (!attendee) {
                $('#modal').find('.attendee-error').show();
            }
            else {
                $('#modal').find('.agent-error').hide();
            }

            if (block_day && reason_id && attendee) {
                $.ajax({
                    url: helper.baseUrl + 'calendar/add_appointment_rule',
                    type: "POST",
                    dataType: "JSON",
                    data: $('#modal').find('.appointment-rule-form').serialize()
                }).done(function (response) {
                    if (response.success) {
                        flashalert.success(response.msg);
                        //$('#modal').modal('toggle');
                        appointment_rules.loadAppointmentRules(block_day);
                        appointment_rules.loadAppointmentRulesByDate(block_day);
                        $('#modal').find('.nav-tabs a[href="#apprules"]').tab('show');
                    }
                    else {
                        flashalert.danger(response.msg);
                    }
                });
            }
        });
    },

    add_appointment: function () {
        modals.search_records("create-appointment");

    },
}
var rules = [];
var calendar;
$(document).ready(function () {
    "use strict";
    //Show appointment rules button
    /*
     $(document).on('mouseenter', '.cal-month-day', function () {
     var day = $(this).attr('item-day');
     $(this).find('.block-day-btn.' + day).show();
     });



     //Hide appointment rules button
     $(document).on('mouseleave', '.cal-month-day', function () {
     var day = $(this).attr('item-day');
     var has_rules = $(this).find('.block-day-btn').attr('item-rules');
     if (!has_rules) {
     $(this).find('.block-day-btn.' + day).hide();
     }
     });
     */
    $(document).on('click', '.events-list a', function (e) {
        e.preventDefault();
    });

    //load the available attendee options when the page loads
	if($('#user-select').val()==null){
    appointment_rules.get_calendar_users(false);
	}
	
    //Add appointment rule
    $(document).on('click', '.block-day-btn', function () {
        calendar_modals.addAppointmentRule($(this).attr('item-day'));
    });

    //Remove appointment rule
    $(document).on('click', '.del-rule-btn', function () {
        appointment_rules.delAppointmentRules($(this).attr('item-id'), $(this).attr('item-date'));
    });

    $(document).on('click', 'a[href="#addrule"]', function () {
        modal_body.css('overflow', 'visible');
    });
    $(document).on('click', '#set-rules', function () {
        calendar_modals.addAppointmentRule(0)
    });

    var options = {
        events_source: function (start, end) {
            var events = [];
            $.ajax({
                url: helper.baseUrl + 'calendar/get_events',
                dataType: 'JSON',
                type: 'POST',
                async: false,
                data: {
                    startDate: start.getTime(),
                    endDate: end.getTime(),
                    campaigns: $('#campaign-cal-select').selectpicker('val'),
                    users: $('#user-select').selectpicker('val'),
                    postcode: $('#dist-form').find('.current_postcode_input').val(),
                    distance: $('#dist-form').find('.distance-select').val()
                }
            }).done(function (json) {
                if (!json.success) {
                    $.error(json.error);
                }
                if (json.result) {
                    events = json.result;
                    rules = json.rules;
                }
            });
            return events;
        },
        //modal: "#events-modal",
        view: 'month',
        tmpl_path: helper.baseUrl + 'assets/tmpls/' + calendar_view + '/',
        tmpl_cache: false,
        day: 'now',
        onAfterEventsLoad: function (events) {
            if (!events) {
                return;
            }
            var list = $('#eventlist');
            list.html('');
            $.each(events, function (key, val) {
                $(document.createElement('li'))
                    .html('<a href="' + val.url + '">' + val.title + '</a>')
                    .appendTo(list);
            });
        },
        onAfterViewLoad: function (view) {
            console.log(rules);
            if (view == 'day') {
                verifyConflictEvents();
            }
            $('.page-header h3').text(this.getTitle());
            $('.btn-group button').removeClass('active');
            $('button[data-calendar-view="' + view + '"]').addClass('active');
            appointment_rules.loadAppointmentRules();
        },
        classes: {
            months: {
                general: 'label'
            }
        }
    };


    calendar = $('#calendar').calendar(options);

    //appointment_rules.loadAppointmentRules();

    $('.btn-group button[data-calendar-nav]').each(function () {
        var $this = $(this);
        $this.click(function () {
            $this.button('loading');
            calendar.navigate($this.data('calendar-nav'));
            $this.button('reset');
        });
    });

    $('.btn-group button[data-calendar-view]').each(function () {
        var $this = $(this);
        $this.click(function () {
            $this.button('loading');
            calendar.view($this.data('calendar-view'));
            $this.button('reset')
        });
    });

    $('#first_day').change(function () {
        var value = $(this).val();
        value = value.length ? parseInt(value) : null;
        calendar.setOptions({
            first_day: value
        });
        calendar.view();
    });

    $('#language').change(function () {
        calendar.setLanguage($(this).val());
        calendar.view();
    });

    $('#events-in-modal').change(function () {
        var val = $(this).is(':checked') ? $(this).val() : null;
        calendar.setOptions({
            modal: val
        });
    });

    $(document).on('change', '#user-select', function (e) {
        e.preventDefault();
        calendar.view();
    });


    $(document).on('change', '#campaign-cal-select', function () {
        appointment_rules.get_calendar_users(true);
    });


    $(document).on('click', '#distance-cal-button', function (e) {
        e.preventDefault();
        calendar_modals.distance();
        modal_body.css('overflow', 'visible');
    });

    $(document).on('click', '#import-appointment-btn', function (e) {
        e.preventDefault();
        calendar_modals.import_appointment();
        modal_body.css('overflow', 'visible');
    });

    $(document).on('click', '.add-cal-appointment-btn', function (e) {
        e.preventDefault();
        calendar_modals.add_appointment();
        modal_body.css('overflow', 'visible');
    });

});
