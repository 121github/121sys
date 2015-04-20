$(document).ready(function () {
    "use strict";

    //Show appointment rules button
    $(document).on('mouseenter', '.cal-month-day', function () {
        var day = $(this).attr('item-day');
        $(this).find('.block-day-btn.'+day).show();
    });

    //Hide appointment rules button
    $(document).on('mouseleave', '.cal-month-day', function () {
        var day = $(this).attr('item-day');
        var has_rules = $(this).find('.block-day-btn').attr('item-rules');
        if (!has_rules) {
            $(this).find('.block-day-btn.'+day).hide();
        }
    });

    //Add appointment rule
    $(document).on('click', '.block-day-btn', function () {
        modal.addAppointmentRule($(this).attr('item-day'));
    });

    //Remove appointment rule
    $(document).on('click', '.del-rule-btn', function () {
        appointment_rules.delAppointmentRules($(this).attr('item-id'),$(this).attr('item-date'));
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
                }
            });
            return events;
        },
        //modal: "#events-modal",
        view: 'month',
        tmpl_path: helper.baseUrl + 'assets/tmpls/',
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

    var appointment_rules = {
        loadAppointmentRules: function() {
            $.ajax({
                url: helper.baseUrl + 'calendar/get_appointment_rules',
                type: "POST",
                dataType: "JSON"
            }).done(function (response) {
                if (response.success) {
                    $.each(response.data, function (key, value) {
                        var title = '<table>';
                        $.each(value, function (i, rule) {
                            title += "<tr>" +
                                "<td style='text-align: left;'>"+rule.name+"</td>" +
                            "</tr>";
                        });
                        title += '</table>';

                        $('.cal-month-day').find('.block-day-btn.'+key).css('color','red').attr('item-rules',value.length).attr('data-original-title',title).show();
                        $('.cal-week-box').find('.block-day-btn.'+key).css('color','red').attr('item-rules',value.length).attr('data-original-title',title).show();
                        $('#cal-day-box').find('.block-day-btn.'+key).css('color','red').attr('item-rules',value.length).attr('data-original-title',title).show();
                    });
                }
            });
        },
        loadAppointmentRulesByDate: function(block_day) {
            $.ajax({
                url: helper.baseUrl + 'calendar/get_appointment_rules_by_date',
                type: "POST",
                dataType: "JSON",
                data: {date: block_day}
            }).done(function (response) {
                if (response.success) {
                    var rules = '<table class="table ajax-table"><thead><tr><th>Attendee</th><th>Reason</th><th>Options</th></tr></thead><tbody>';
                    $.each(response.data, function (key, value) {
                        rules +=
                            '<tr>' +
                            '<td>' + value.name + '</td>' +
                            '<td>' + value.reason + '</td>' +
                            '<td><span class="glyphicon glyphicon-remove del-rule-btn" item-id="' + value.appointment_rules_id + '" item-date="' + block_day + '"></span></td>' +
                            '</tr>';
                    });
                    rules += '</tbody></table>';
                    $('#modal').find('.rules-per-day').html(rules);
                }
                else {
                    $('#modal').find('.rules-per-day').html("No rules created...");
                    $('#modal').find('.nav-tabs a[href="#addrule"]').tab('show');
                    $('.cal-month-day').find('.block-day-btn.'+block_day).css('color','black').attr('item-rules','').attr('data-original-title','').hide();
                    $('.cal-week-box').find('.block-day-btn.'+block_day).css('color','black').attr('item-rules','').attr('data-original-title','').show();
                    $('#cal-day-box').find('.block-day-btn.'+block_day).css('color','black').attr('item-rules','').attr('data-original-title','').show();

                }
            });
        },
        loadAppointmentRulesReasons: function() {
            $.ajax({
                url: helper.baseUrl + 'calendar/get_appointment_rule_reasons',
                type: "POST",
                dataType: "JSON"
            }).done(function (response) {
                $('#modal').find('.reason-select').empty();
                var $options = '<option value="">Choose a reason...</option>';
                $.each(response.data, function (k, v) {
                    $options += "<option value='" + v.id + "'>" + v.name + "</options>";
                });
                $('#modal').find('.reason-select').html($options).selectpicker('refresh');
            });
        },
        loadAppointmentRulesAttendees: function() {
            $.ajax({
                url: helper.baseUrl + 'calendar/get_calendar_users',
                type: "POST",
                dataType: "JSON",
                data: {campaigns: $('#campaign-cal-select').val()}
            }).done(function (response) {
                $('#modal').find('.attendee-select').empty();
                var $options = "";
                $.each(response.data, function (k, v) {
                    $options += "<option value='" + v.id + "'>" + v.name + "</options>";
                });
                $('#modal').find('.attendee-select').html($options).selectpicker('refresh');
            });
        },
        delAppointmentRules: function(appointment_rules_id,date) {
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
        }
    };

    var calendar = $('#calendar').calendar(options);

    appointment_rules.loadAppointmentRules();

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
    $('#events-modal .modal-header, #events-modal .modal-footer').click(function (e) {
        //e.preventDefault();
        //e.stopPropagation();
    });

    $(document).on('change', '#user-select', function (e) {
        e.preventDefault();
        calendar.view();
    });


    $(document).on('change', '#campaign-cal-select', function () {
        $.ajax({
            url: helper.baseUrl + 'calendar/get_calendar_users',
            type: "POST",
            dataType: "JSON",
            data: {campaigns: $(this).val()}
        }).done(function (response) {
            $('#user-select').empty();
            var $options = "";
            $.each(response.data, function (k, v) {
                $options += "<option value='" + v.id + "'>" + v.name + "</options>";
            });
            $('#user-select').html($options).selectpicker('refresh');
            calendar.view();
        })
    });

    $(document).on('click', '#distance-cal-button', function (e) {
        e.preventDefault();
        modal.distance();
    });


    var modal = {
        distance: function () {
            $('.modal-title').text('Set maximum distance');
            $('#modal').modal({
                backdrop: 'static',
                keyboard: false
            }).find('.modal-body').empty().html($('#dist-form').html());
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
        addAppointmentRule: function(block_day) {
            $('.modal-title').text('Add appointment rule');
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
                            '<p><label>Block Day<span class="block-day-error" style="color: red; display: none"> Select a block day</span></label>' +
                            '<input name="block_day" value="" placeholder="Add the block day..." class="form-control block-day"/>' +
                            '</p>' +
                            '<p><label>Reason<span class="reason-error" style="color: red; display: none"> Select a reason</span></label>' +
                            '<select name="reason_id" class="reason-select" title="Choose the reason..." data-width="100%" required>' +
                            '</select>' +
                            '</p>' +
                            '<p class="other_reason"><label>Other Reason</label>' +
                            '<input name="other_reason" placeholder="Other reason..." value="" class="form-control"/>' +
                            '</p>' +
                            '<p>' +
                            '<label>Attendees<span class="attendee-error" style="color: red; display: none"> Select an agent</span></label>' +
                            '<select name="attendees[]" class="attendee-select" title="Select attendees" multiple data-width="100%" required>' +
                            '</select>' +
                            '</p>' +
                            '</form>' +
                        '</div>' +
                    '</div>'
            );

            //Add the rules
            appointment_rules.loadAppointmentRulesByDate(block_day);

            //Get the reasons options
            appointment_rules.loadAppointmentRulesReasons();

            //Get the attendees options
            appointment_rules.loadAppointmentRulesAttendees();

            $('#modal').find('.block-day').datetimepicker({
                pickTime: false,
                format: 'DD/MM/YYYY'
            });

            $('#modal').find('input[name="block_day"]').val(timestamp_to_uk(new Date(block_day)));
            $('#modal').find('.other_reason').hide();

            $('#modal').find('.reason-select').on('change', function () {
                var selected = $('#modal').find('.reason-select option:selected').val();
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
                if(!block_day) {
                    $('#modal').find('.block-day-error').show();
                }
                else {
                    $('#modal').find('.block-day-error').hide();
                }
                if(!reason_id) {
                    $('#modal').find('.reason-error').show();
                }
                else {
                    $('#modal').find('.reason-error').hide();
                }
                if(!attendee) {
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
                            appointment_rules.loadAppointmentRules();
                            appointment_rules.loadAppointmentRulesByDate(block_day);
                            $('#modal').find('.nav-tabs a[href="#apprules"]').tab('show');
                        }
                        else {
                            flashalert.danger(response.msg);
                        }
                    });
                }
            });
        }
    }
});
