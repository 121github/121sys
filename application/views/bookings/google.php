<div class="loading-overlay">
    <div class="loader-img">
        <img src="<?php echo base_url(); ?>assets/img/ajax-loader-snake-black.gif" width="5%"/>
    </div>
</div>
<div id='calendar'></div>

<style>
    body {
    }

    .loading-overlay {
        position: absolute;
        width: 100%;
        height: 100%;
        background: #000;
        opacity: 0.4;
        filter: alpha(opacity=40);
        z-index: 10;
        top: 0;
        left: 0
    }

    .loading-overlay .loader-img {
        text-align: center;
        height: 10em;
        position: relative;
        top: 50%;
        left: 50%;
        opacity: 0.4;
        background: transparent;
        transform: translate(-50%, -50%);
    }

    .container-fluid {
    }

    .top-row {
        padding: 10px 10px 0;
    }

    .bottom-row {
        padding: 0px 10px 10px;
    }

    .panel-body {
        overflow: hidden
    }

    #view-container {
        margin: 0;
        padding: 0 0px;
        overflow-y: auto;
        height: 100%;
        overflow-x: hidden;
    }
</style>

<script type="text/javascript">


    var calendar = {
        init: function () {
            this.cal = $('#calendar').fullCalendar({
                timeFormat: 'H:mm',
                selectable: true,
                minTime: '07:00',
                maxTime: '20:00',
                slotDuration: '00:30:00',
                height: 700,
                header: {
                    left: 'prev,next today localevents siginEvent logoutEvent loadingEvent',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                defaultView: 'agendaWeek',
                eventDrop: function (event) {
                    console.log(event);
                    calendar.set_event_time(event.id, event.start, event.end)
                },
                eventAfterRender: function (event, element, view) {
                    $(element).attr("event-id", event._id).find('.fc-content').prepend('<span class="' + event.icon + '"></span> ');
                },
                loading: function(bool) {
                    if (bool){
                        $('.loading-overlay').fadeIn();
                    }
                    else {
                        $('.loading-overlay').fadeOut();
                    }
                },
                customButtons: {
                    localevents: {
                        text: 'Local Calendar',
                        click: function () {
                            window.location.href = helper.baseUrl + 'booking';
                        }
                    },
                    siginEvent: {
                        text: 'Sign In google',
                        click: function () {
                            calendar.signin();
                        }
                    },
                    logoutEvent: {
                        text: 'Logout google',
                        click: function () {
                            calendar.logout();
                        }
                    },
                    loadingEvent: {
                        text: 'Loading events...'
                    }
                },
                viewRender: function(view) {
                    if (typeof $('#datetimepicker2').data("DateTimePicker") != "undefined") {
                        var view_mode = "months";
                        var format = "YYYY-MM";
                        switch(view.name) {
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
                //events: helper.baseUrl + 'booking/google_events'
                eventSources: [
                    {
                        url: helper.baseUrl + 'booking/google_events',
                        type: 'POST',
                        data: function() { // a function that returns an object
                            var calendar_selected = (!$('.calendar-list').find('#calendar-select').selectpicker('val')?['none']: $('.calendar-list').find('#calendar-select').selectpicker('val').length>0 ? $('.calendar-list').find('#calendar-select').selectpicker('val') : []);
                            return {
                                calendars_selected: calendar_selected.join(',')
                            };
                        }
                    }
                ],
                eventClick: function(event) {
                    if (event.url) {
                        window.open(event.url);
                        return false;
                    }
                }
            });

            $('.fc-logoutEvent-button').css('color','green');
            $('.fc-siginEvent-button').css('color','red');


            $('#calendar .fc-toolbar .fc-right').prepend('<div class="calendar-list"><select title="Calendars" id="calendar-select" multiple><option value=""></option></select></div>');
            $('#calendar-select').selectpicker();
            $('.calendar-list').hide();

            $('#calendar .fc-toolbar .fc-right .fc-button-group').append(
                "<div class='input-group date' id='datetimepicker2' style='display:inline;width:50px;margin:5px 0 0 10px '>" +
                "<input type='text' name='cal_date' style='display:none' />" +
                "<span class='input-group-addon' style='display:inline; padding:0; background:none; border:none'>" +
                "<span class='fa fa-calendar'></span>" +
                "</span>" +
                "</div>"
            );
            $('#datetimepicker2').datetimepicker({
                defaultDate: moment(),
                format: 'YYYY-MM-DD',
                enabledHours:false,
                viewMode: 'days',
            }).on('dp.change', function(ev) {
                $('#calendar').fullCalendar('gotoDate',new Date($('input[name="cal_date"]').val()));
            });

            calendar.signin();

            $(document).on("click", '#google-login-btn', function (e) {
                e.preventDefault();
                calendar.login();
            });

            $(document).on("click", '.cancel-modal', function (e) {
                e.preventDefault();
                window.location.href = helper.baseUrl + 'booking';
            });

            $(document).on("change", ".calendar-list", function (e) {
                e.preventDefault();
                $('#calendar').fullCalendar( 'refetchEvents' );
            });

//            $('#datetimepicker2').change(function () {
//                console.log("asdsad");
//            });

//            $("#datetimepicker2").on("dp.change",function (e) {
//                e.preventDefault();
//                alert('changed');
//            });


//            $('#datetimepicker2').datepicker({
//                dateFormat: 'DD, d MM, yy',
//                onSelect: function(dateText,dp){
//                    $j('#calendar').fullCalendar('gotoDate',new Date(Date.parse(dateText)));
//                    $j('#calendar').fullCalendar('changeView','agendaDay');
//                }
//            });
        },
        signin: function() {
            $.ajax({
                url: helper.baseUrl + 'booking/google_auth',
                type: "POST",
                dataType: "JSON",
                beforeSend:function(){
                    //Show Loading button
                    $('.fc-logoutEvent-button').hide();
                    $('.fc-siginEvent-button').hide();
                    $('.fc-loadingEvent-button').show();
                }
            }).done(function (response) {
                if (response.success) {
                    //Show Logout button
                    $('.fc-logoutEvent-button').show();
                    $('.fc-loadingEvent-button').hide();
                    $('.fc-siginEvent-button').hide();

                    //Show calendar list
                    calendar.load_google_calendars(response.calendars);
                }
                else {
                    if (response.accessTokenExpired) {
                        calendar.login();
                    }
                    else {
                        $('.fc-logoutEvent-button').hide();
                        $('.fc-loadingEvent-button').hide();
                        $('.fc-siginEvent-button').show();

                        //Open modal to sign in
                        var mheader = "<i class='fa fa-google red'>oogle</i> Sign In";
                        var mbody = "You need to login into your google account"
                        var mfooter = '<button data-dismiss="modal" class="btn btn-default cancel-modal pull-left" type="button">Cancel</button>' +
                            ' <button class="btn btn-primary pull-right marl" id="google-login-btn">Login</button> ';

                        modals.load_modal(mheader, mbody, mfooter);
                        $('.modal-header .close').hide();
                    }
                }
            });
        },
        login: function() {
            window.location.href = helper.baseUrl + 'google/authenticate';
        },
        logout: function() {
            window.location.href = helper.baseUrl + 'google/logout';
        },
        load_google_calendars: function(calendar_ar) {
            var $options = "";
            $.each(calendar_ar, function (k, v) {
                $options += "<option value='" + v.id + "' selected>" + v.name + "</options>";
            });
            $('.calendar-list').find('#calendar-select').html($options).selectpicker('refresh');

           $('.calendar-list').show();
        }
    }

    $(document).ready(function () {
        calendar.init();
    })
</script>