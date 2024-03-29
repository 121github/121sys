// JavaScript Document
var dashboard = {
    init: function () {
        filters.init();

        $('.daterange').daterangepicker({
                opens: "left",
                ranges: {
                    'Any Time': ["01/01/2014", moment()],
                    'Today': [moment(), moment()],
                    'Tomorrow': [moment(), moment().add(1, 'days')],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Next 7 Days': [moment(), moment().add(6, 'days')],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'Next 30 Days': [moment(), moment().add(29, 'days')],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'Next Month': [moment().startOf('month'), moment().add(1, 'month').endOf('month')],
                    'Future Days': [moment(), moment().add(5, 'years')],
                    'Past Days': [moment().subtract(5, 'years'), moment()]

                },
                format: 'DD/MM/YYYY',
                minDate: "02/07/2014",
                startDate: "02/07/2014",
                endDate: moment(),
            },
            function (start, end, element) {
                var $btn = this.element;
                if (element == "Any Time") {
                    $btn.find('.date-text').html("Any time");
                    $btn.closest('form').find('input[name="date_from"]').val('');
                    $btn.closest('form').find('input[name="date_to"]').val('');
                }
                else if (element == "Future Days") {
                    $btn.find('.date-text').html("Future Days");
                        $btn.closest('form').find('input[name="date_from"]').val(start.format('YYYY-MM-DD'));
                        $btn.closest('form').find('input[name="date_to"]').val(end.format('YYYY-MM-DD'));
                }
                else if (element == "Past Days") {
                    $btn.find('.date-text').html("Past Days");
                    $btn.closest('form').find('input[name="date_from"]').val(start.format('YYYY-MM-DD'));
                    $btn.closest('form').find('input[name="date_to"]').val(end.format('YYYY-MM-DD'));
                } else {
                    $btn.find('.date-text').html(start.format('MMMM D') + ' - ' + end.format('MMMM D'));
                    $btn.closest('form').find('input[name="date_from"]').val(start.format('YYYY-MM-DD'));
                    $btn.closest('form').find('input[name="date_to"]').val(end.format('YYYY-MM-DD'));
                }
            });

        $(document).on("click", '#filter-submit', function (e) {
            e.preventDefault();
            dashboard.load_dash($(this).attr('item-id'));
            $('#filter-right').data("mmenu").close();
        });

        $(document).on("click", '.daterange', function (e) {
            e.preventDefault();
        });

        $(document).on("click", ".comment-filter", function (e) {
            e.preventDefault();
            $icon = $(this).closest('ul').prev('button').find('span');
            $(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $('.filter-form').find('input[name="comments"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color", "black");
            $(this).css("color", "green");
            dashboard.comments_panel();
        });

        //optgroup
        $('li.dropdown-header').on('click', function (e) {
            setTimeout(function () {
                //Get outcomes by campaigns selected
                dashboard.get_outcomes_filter();
                //Get sources by campaigns selected
                dashboard.get_sources_filter();
            }, 500);
        });

        $(document).on("click", '#filter-overview-submit', function (e) {
            e.preventDefault();
            dashboard.refresh_panels();
            $('#filter-right').data("mmenu").close();
        });

        $(document).on("click", '#filter-favorite-submit', function (e) {
            e.preventDefault();
            dashboard.filter_panel();
            dashboard.favorites_panel();
            $('#filter-right').data("mmenu").close();
        });

        $(document).on("click", '.daterange', function (e) {
            e.preventDefault();
        });

        $(document).on("change", ".campaign-filter", function (e) {
            e.preventDefault();
            //Get outcomes by campaigns selected
            dashboard.get_outcomes_filter();
            //Get sources by campaigns selected
            dashboard.get_sources_filter();
        });


        $(document).on("click", ".refresh-data", function (e) {
			e.preventDefault();
			if($(this).hasClass('refresh-favorites-data')){
            dashboard.filter_panel();
            dashboard.favorites_panel();
			}
			if($(this).hasClass('refresh-overview-data')){
            dashboard.refresh_panels();
			}
			if($(this).hasClass('refresh-dashboard-data')){
             dashboard.load_dash($(this).attr('dashboard-id'));
            $('.show-charts').removeClass('btn-success').addClass('btn-default');
            $('.show-charts').attr('data-item', 0);
			}
        });

        $(document).on("click", ".new-report", function (e) {
            e.preventDefault();
            dashboard.select_report($(this).attr('data-item'));
        });

        $(document).on("click", "#add-report-btn", function (e) {
            e.preventDefault();
            dashboard.add_report();
        });

        $(document).on("click", ".remove-dashreport-btn", function (e) {
            e.preventDefault();
            dashboard.remove_report($(this).attr('data-dashboard-id'), $(this).attr('data-report-id'));
        });

        $(document).on('click', '.export-dashreport-btn', function (e) {
            e.preventDefault();
            export_data.export_file($(this).attr('data-report-id'));
        });

        $(document).on("click", ".move-dashreport-btn", function (e) {
            e.preventDefault();
            dashboard.move_report($(this).attr('data-dashboard-id'), $(this).attr('data-report-id'), $(this).attr('current-position'), $(this).attr('next-position'));
        });

        $(document).on("click", ".resize-dashreport-btn", function (e) {
            e.preventDefault();
            dashboard.resize_report($(this).attr('data-dashboard-id'), $(this).attr('data-report-id'), $(this).attr('column-size'));
        });

        dashboard.filter_panel();
    },

    settings: function () {
        $(document).on("click", ".new-dashboard-btn", function (e) {
            e.preventDefault();
            dashboard.new_dashboard();
        });

        $(document).on("click", ".edit-dashboard-btn", function (e) {
            e.preventDefault();
            dashboard.new_dashboard($(this));
        });

        $(document).on("click", "#save-dashboard-btn", function (e) {
            e.preventDefault();
            dashboard.save_dashboard();
        });

        $(document).on("click", ".view-dashboard-btn", function (e) {
            e.preventDefault();
            window.location.replace(helper.baseUrl + 'dashboard/view/' + $(this).attr('item-id'));
        });
    },

    refresh_panels: function () {
        dashboard.filter_panel();
        dashboard.history_panel();
        dashboard.comments_panel();
        dashboard.system_stats();
        dashboard.emails_panel();
        dashboard.sms_panel();
        dashboard.outcomes_panel();

        //numbers.animate($('.tile_stats_count'));
    },

    filter_panel: function () {
        //////////////////////////////////////////////////////////
        //Filters/////////////////////////////////////////////////
        //////////////////////////////////////////////////////////
        var filters = "";

        filters += "<span class='btn btn-default btn-xs clear-filters pull-right'>" +
            "<span class='glyphicon glyphicon-remove' style='padding-left:3px; color:black;'></span> Clear" +
            "</span>";

        //Date
        filters += "<h5><strong>Date </strong></h5>" +
            "<ul>" +
            "<li style='list-style-type:none'>" + $(".filter-form").find("input[name='date_from']").val() + "</li>" +
            "<li style='list-style-type:none'>" + $(".filter-form").find("input[name='date_to']").val() + "</li>" +
            "</ul>";

        //Campaigns
        var size = ($('.campaign-filter  option:selected').size() > 0 ? "(" + $('.campaign-filter  option:selected').size() + ")" : '');
        filters += "<h5 style='border-bottom: 1px solid #e2e2e2; padding-bottom: 4px;'><strong>Campaigns</strong> " + size + "</h5><ul>";
        $('.campaign-filter  option:selected').each(function (index) {
            filters += "<li style='list-style-type:none'>" + $(this).text() + "</li>";
        });
        filters += "</ul>";

        //Outcomes
        var size = ($('.outcome-filter  option:selected').size() > 0 ? "(" + $('.outcome-filter  option:selected').size() + ")" : '');
        filters += "<h5 style='border-bottom: 1px solid #e2e2e2; padding-bottom: 4px;'><strong>Outcomes</strong> " + size + "</h5><ul>";
        $('.outcome-filter option:selected').each(function (index) {
            var color = "black";
            if ($(this).parent().attr('label') === 'positive') {
                color = "green";
            }
            filters += "<li style='list-style-type:none'><span style='color: " + color + "'>" + $(this).text() + "</span></li>";
        });
        filters += "</ul>";

        //Teams
        var size = ($('.team-filter  option:selected').size() > 0 ? "(" + $('.team-filter  option:selected').size() + ")" : '');
        filters += "<h5 style='border-bottom: 1px solid #e2e2e2; padding-bottom: 4px;'><strong>Teams</strong> " + size + "</h5><ul>";
        $('.team-filter  option:selected').each(function (index) {
            filters += "<li style='list-style-type:none'>" + $(this).text() + "</li>";
        });
        filters += "</ul>";


        //Agents
        var size = ($('.agent-filter  option:selected').size() > 0 ? "(" + $('.agent-filter  option:selected').size() + ")" : '');
        filters += "<h5 style='border-bottom: 1px solid #e2e2e2; padding-bottom: 4px;'><strong>Agents</strong> " + size + "</h5><ul>";
        $('.agent-filter  option:selected').each(function (index) {
            filters += "<li style='list-style-type:none'>" + $(this).text() + "</li>";
        });
        filters += "</ul>";


        //Sources
        var size = ($('.source-filter  option:selected').size() > 0 ? "(" + $('.source-filter  option:selected').size() + ")" : '');
        filters += "<h5 style='border-bottom: 1px solid #e2e2e2; padding-bottom: 4px;'><strong>Sources</strong> " + size + "</h5><ul>";
        $('.source-filter  option:selected').each(function (index) {
            filters += "<li style='list-style-type:none'>" + $(this).text() + "</li>";
        });
        filters += "</ul>";

        $('#filters').html(filters);
    },
    /* the function for the history panel on the main dashboard */
    history_panel: function () {
        $.ajax({
            url: helper.baseUrl + 'dashboard/get_history',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize(),
            beforeSend: function () {
                $('#latest-history').html('<img src="' + helper.baseUrl + 'assets/img/ajax-loader-bar.gif" /> ');
            }
        }).done(function (response) {
            if (response.data.length > 0) {
                var tbody = "";
                $.each(response.data, function (i, val) {
                    tbody += "<tr class='pointer' data-modal='view-record' data-urn='" + val.urn + "'><td>" + val.campaign_name + "</td><td>" + val.cname + "</td><td>" + val.date + "</td><td>" + val.time + "</td><td>" + val.name + "</td><td>" + val.outcome + "</td></tr>";
                });
                var table = '<div class="table-responsive"><table class="table table-bordered table-hover table-striped"><thead><tr><th>Campaign</th><th>Name</th><th>Date</th><th>Time</th><th>User</th><th>Outcome</th> </tr></thead><tbody>' + tbody + '</tbody></table></div>'
                $('#latest-history').html(table);
            } else {
                $('#latest-history').html('<p>No records have been updated yet</p>');
            }
        });
    },
    emails_panel: function () {
        $.ajax({
            url: helper.baseUrl + 'dashboard/get_email_stats',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize(),
            beforeSend: function () {
                $('#email-stats').html('<img src="' + helper.baseUrl + 'assets/img/ajax-loader-bar.gif" /> ');
            }
        }).done(function (response) {
            $('#email-stats').html("<div><a class='count' href='" + response.data.new_url + "'>" + response.data.new + "</a> new emails read<br><a class='count' href='" + response.data.read_url + "'>" + response.data.read + "</a> emails read today " + "<br><a class='count' href='" + response.data.all_url + "'>" + response.data.all + "</a> emails sent today<br><a class='count' href='" + response.data.pending_url + "'>" + response.data.pending + "</a> emails pending today<br><a class='count' href='" + response.data.unsent_url + "'>" + response.data.unsent + "</a> failed emails today<br><a class='count' href='" + response.data.sentall_url + "'>" + response.data.sentall + "</a> emails sent anytime<br><a class='count' href='" + response.data.readall_url + "'>" + response.data.readall + "</a> emails read anytime</div>");
            //numbers.animate($('#email-stats'));

            //GRAPHS
            google.load('visualization', '1', {
                packages: ['corechart'], 'callback': function () {

                    //Today email stats
                    if (response.data.all > 0) {
                        var rows = [];
                        var title = 'Today Email stats';

                        // Set chart options
                        var options = {
                            'legend': {position: 'none'},
                            'title': title,
                            'width': 200,
                            'height': 350,
                            curveType: 'function',
                            'hAxis': {direction: -1, slantedText: true, slantedTextAngle: 45}
                        };

                        var data = new google.visualization.DataTable();
                        data.addColumn('string', 'Topping');
                        data.addColumn('number', 'Emails');
                        rows.push(["failed today", parseInt(response.data.unsent)]);
                        rows.push(["pending today", parseInt(response.data.pending)]);
                        rows.push(["sent today", parseInt(response.data.all)]);
                        rows.push(["read today", parseInt(response.data.read)]);
                        rows.push(["new today", parseInt(response.data.new)]);
                        data.addRows(rows);
                        var chart = new google.visualization.ColumnChart(document.getElementById('email-today-chart'));
                        chart.draw(data, options);
                    }
                    else {
                        $('#email-today-chart').html("<span style='color:black;'>No emails sent today</span>");
                    }


                    //All email stats
                    if (response.data.sentall > 0) {
                        var rows = [];
                        var title = 'All Email stats';

                        // Set chart options
                        var options = {
                            'legend': {position: 'none'},
                            'title': title,
                            'width': 200,
                            'height': 350,
                            curveType: 'function',
                            'hAxis': {direction: -1, slantedText: true, slantedTextAngle: 45}
                        };

                        var data = new google.visualization.DataTable();
                        data.addColumn('string', 'Topping');
                        data.addColumn('number', 'Emails');
                        rows.push(["read anytime", parseInt(response.data.readall)]);
                        rows.push(["sent anytime", parseInt(response.data.sentall)]);
                        data.addRows(rows);
                        var chart = new google.visualization.ColumnChart(document.getElementById('email-all-chart'));
                        chart.draw(data, options);
                    }
                    else {
                        $('#email-all-chart').html("<span style='color:black;'>No emails sent</span>");
                    }
                }
            });
        });
    },
    sms_panel: function () {
        $.ajax({
            url: helper.baseUrl + 'dashboard/get_sms_stats',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize(),
            beforeSend: function () {
                $('#sms-stats').html('<img src="' + helper.baseUrl + 'assets/img/ajax-loader-bar.gif" /> ');
            }
        }).done(function (response) {
            $('#sms-stats').html("<ul>" +
                "<div><a class='count' href='" + response.data.today_url + "'>" + response.data.today_sms + "</a> sms sent today <br><a class='count' href='" + response.data.today_delivered_url + "'>" + response.data.today_delivered_sms + "</a> sms delivered today " + "<br><a class='count' href='" + response.data.today_undelivered_url + "'>" + response.data.today_undelivered_sms + "</a> sms undelivered today " + "<br><a class='count' href='" + response.data.today_pending_url + "'>" + response.data.today_pending_sms + "</a> sms pending today " + "<br><a class='count' href='" + response.data.today_unknown_url + "'>" + response.data.today_unknown_sms + "</a> sms unknown today " + "<br><a class='count' href='" + response.data.today_error_url + "'>" + response.data.today_error_sms + "</a> sms error today " + "<br><a class='count' href='" + response.data.all_url + "'>" + response.data.all_sms + "</a> sms sent anytime " + "<br><a class='count' href='" + response.data.all_delivered_url + "'>" + response.data.all_delivered_sms + "</a> sms delivered anytime " + "<br><a class='count' href='" + response.data.all_undelivered_url + "'>" + response.data.all_undelivered_sms + "</a> sms undelivered anytime " + "<br><a class='count' href='" + response.data.all_pending_url + "'>" + response.data.all_pending_sms + "</a> sms pending anytime " + "<br><a class='count' href='" + response.data.all_unknown_url + "'>" + response.data.all_unknown_sms + "</a> sms unknown anytime " + "<br><a class='count' href='" + response.data.all_error_url + "'>" + response.data.all_error_sms + "</a> sms error anytime " + "</div>");
            //numbers.animate($('#sms-stats'));

            //GRAPHS
            google.load('visualization', '1', {
                packages: ['corechart'], 'callback': function () {

                    //Today Sms stats
                    if (response.data.today_sms > 0) {
                        var rows = [];
                        var title = 'Today Sms stats';

                        // Set chart options
                        var options = {
                            'legend': {position: 'none'},
                            'title': title,
                            'width': 200,
                            'height': 350,
                            curveType: 'function',
                            'hAxis': {direction: -1, slantedText: true, slantedTextAngle: 45}
                        };

                        var data = new google.visualization.DataTable();
                        data.addColumn('string', 'Topping');
                        data.addColumn('number', 'Sms');
                        rows.push(["error", parseInt(response.data.today_error_sms)]);
                        rows.push(["unknown", parseInt(response.data.today_unknown_sms)]);
                        rows.push(["pending", parseInt(response.data.today_pending_sms)]);
                        rows.push(["undelivered", parseInt(response.data.today_undelivered_sms)]);
                        rows.push(["delivered", parseInt(response.data.today_delivered_sms)]);
                        rows.push(["sent", parseInt(response.data.today_sms)]);
                        data.addRows(rows);
                        var chart = new google.visualization.ColumnChart(document.getElementById('sms-today-chart'));
                        chart.draw(data, options);
                    }
                    else {
                        $('#sms-today-chart').html("<span style='color:black;'>No sms sent today</span>");
                    }


                    //All sms stats
                    if (response.data.all_sms > 0) {
                        var rows = [];
                        var title = 'All Sms stats';

                        // Set chart options
                        var options = {
                            'legend': {position: 'none'},
                            'title': title,
                            'width': 200,
                            'height': 350,
                            curveType: 'function',
                            'hAxis': {direction: -1, slantedText: true, slantedTextAngle: 45}
                        };

                        var data = new google.visualization.DataTable();
                        data.addColumn('string', 'Topping');
                        data.addColumn('number', 'Sms');
                        rows.push(["error", parseInt(response.data.all_error_sms)]);
                        rows.push(["unknown", parseInt(response.data.all_unknown_sms)]);
                        rows.push(["pending", parseInt(response.data.all_pending_sms)]);
                        rows.push(["undelivered", parseInt(response.data.all_undelivered_sms)]);
                        rows.push(["delivered", parseInt(response.data.all_delivered_sms)]);
                        rows.push(["sent", parseInt(response.data.all_sms)]);
                        data.addRows(rows);
                        var chart = new google.visualization.ColumnChart(document.getElementById('sms-all-chart'));
                        chart.draw(data, options);
                    }
                    else {
                        $('#sms-all-chart').html("<span style='color:black;'>No sms sent</span>");
                    }
                }
            });
        });
    },
    /* the function for the outcomes panel on the main dashboard */
    outcomes_panel: function (campaign_id) {
        $.ajax({
            url: helper.baseUrl + 'dashboard/get_outcomes',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize(),
            beforeSend: function () {
                $('#outcome-stats').html('<img src="' + helper.baseUrl + 'assets/img/ajax-loader-bar.gif" /> ');
            }
        }).done(function (response) {
            $('.outcome-stats').empty();
            var $outcomes = "";
            var campaign = "";
            if ($('.outcomes-filter').find('[name="campaigns"]').val() != '') {
                //var campaign = "/campaign/" + $('.outcomes-filter').find('[name="campaign"]').val();
            }
            if (response.data.length > 0) {
                var rows = [];
                $.each(response.data, function (i, val) {
                    $outcomes += '<a href="' + helper.baseUrl + 'search/custom/history/outcome/' + val.outcome + '/contact-from/' + response.date + campaign + '" class="list-group-item">' + val.outcome + '<span class="pull-right text-muted small"><em class="count">' + val.count + '</em></span></a>';
                    rows.push([val.outcome, parseInt(val.count)]);
                });
                $('.outcome-stats').append('<div class="list-group">' + $outcomes + '</div>');
                //numbers.animate($('.outcome-stats'));

                //GRAPHS
                google.load('visualization', '1', {
                    packages: ['corechart'], 'callback': function () {

                        //Today email stats
                        var title = 'Today Outcomes';

                        // Set chart options
                        var options = {
                            //'legend': {position: 'none'},
                            'title': title,
                            'width': 330,
                            'height': 350,
                            curveType: 'function',
                        };

                        var data = new google.visualization.DataTable();
                        data.addColumn('string', 'Topping');
                        data.addColumn('number', 'Emails');
                        data.addRows(rows);
                        var chart = new google.visualization.PieChart(document.getElementById('outcome-chart'));
                        chart.draw(data, options);
                    }
                });
            } else {
                $('.outcome-stats').append('<div class="list-group">No calls made today</div>');
                $('#outcome-chart').html("<span style='color:black;'>No calls made today</span>");
            }
        });
    },
    /* the function for the stats panel on the main dashboard */
    system_stats: function () {
        $.ajax({
            url: helper.baseUrl + 'dashboard/system_stats',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize(),
            beforeSend: function () {
                $('#system-stats').html('<img src="' + helper.baseUrl + 'assets/img/ajax-loader-bar.gif" /> ');
            }
        }).done(function (response) {
            $('#system-stats').empty();
            $contents = '<div><h4>Campaign Stats</h4><p><a class="count" href="' + response.data.virgin_url + '">' + response.data.virgin + '</a> records have yet to be called.'
            if (helper.permissions['view live'] > 0) {
                $contents += '<br><a class="count" href="' + response.data.active_url + '">' + response.data.active + '</a> records are in progress'
            }
            if (helper.permissions['view parked'] > 0) {
                $contents += '<br><a class="count" href="' + response.data.parked_url + '">' + response.data.parked + '</a> records have been parked'
            }
            if (helper.permissions['view dead'] > 0) {
                '<br><a class="count" href="' + response.data.dead_url + '">' + response.data.dead + '</a> records are dead</p></div>';
            }
            if (helper.permissions['set progress'] > 0) {
                $contents += '<div><h4>Follow up Stats</h4></div><div><p><a class="count" href="' + response.data.pending_url + '">' + response.data.pending + '</a> records are pending.<br><a href="' + response.data.in_progress_url + '">' + response.data.in_progress + '</a> records are in progress<br><a href="' + response.data.completed_url + '">' + response.data.completed + '</a> records have been completed</div>';
            }
            if (response.data.surveys > 0) {
                $contents += '<div><h4>Survey Stats</h4></div><div><p class="count">' + response.data.surveys + ' surveys have been compeleted<br>' + response.data.failures + ' surveys scored less than 6 on the NPS question<br>' + response.data.average + ' is the average NPS score</p></div>';
            }
            $('#system-stats').append($contents);
            //numbers.animate($('#system-stats'));

            //GRAPHS
            google.load('visualization', '1', {
                packages: ['corechart'], 'callback': function () {

                    //Campaign stats
                    var rows = [];
                    var title = 'Campaign stats';

                    // Set chart options
                    var options = {
                        'legend': {position: 'none'},
                        'title': title,
                        'width': 200,
                        'height': 350,
                        curveType: 'function',
                        'hAxis': {direction: -1, slantedText: true, slantedTextAngle: 45}
                    };

                    var data = new google.visualization.DataTable();
                    data.addColumn('string', 'Topping');
                    data.addColumn('number', 'Count');
                    rows.push(["dead", parseInt(response.data.dead)]);
                    rows.push(["parked", parseInt(response.data.parked)]);
                    rows.push(["in progress", parseInt(response.data.active)]);
                    rows.push(["to be called", parseInt(response.data.virgin)]);
                    data.addRows(rows);
                    var chart = new google.visualization.ColumnChart(document.getElementById('campaign-stats-chart'));
                    chart.draw(data, options);

                    //Surveys stats
                    if (response.data.surveys > 0) {
                        var rows = [];
                        var title = 'Survey stats';

                        // Set chart options
                        var options = {
                            'legend': {position: 'none'},
                            'title': title,
                            'width': 200,
                            'height': 350,
                            curveType: 'function',
                            'hAxis': {direction: -1, slantedText: true, slantedTextAngle: 45}
                        };

                        var data = new google.visualization.DataTable();
                        data.addColumn('string', 'Topping');
                        data.addColumn('number', 'Count');
                        rows.push(["completed", parseInt(response.data.average)]);
                        rows.push(["< 6 NPS question", parseInt(response.data.failures)]);
                        rows.push(["avg NPS score", parseInt(response.data.surveys)]);
                        data.addRows(rows);
                        var chart = new google.visualization.ColumnChart(document.getElementById('survey-stats-chart'));
                        chart.draw(data, options);
                    }
                    else {
                        $('#survey-stats-chart').html("<span style='color:black;'>No surveys stats</span>");
                    }

                }
            });
        });
    },
    /* the function for the comments panel on the main dashboard */
    comments_panel: function () {
        $.ajax({
            url: helper.baseUrl + 'dashboard/get_comments',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize(),
            beforeSend: function () {
                $('#comment-panel').html('<img src="' + helper.baseUrl + 'assets/img/ajax-loader-bar.gif" /> ');
            }
        }).done(function (response) {
            $('#comment-panel').empty();
            var comments = "";
            $.each(response.data, function (i, val) {
                comments += '<li class="left clearfix"><div class="chat-body clearfix"><div class="header"><strong class="primary-font"><a href="' + helper.baseUrl + 'records/detail/' + val.urn + '">' + val.name + '</a></strong><small class="pull-right text-muted"><i class="fa fa-clock-o fa-fw"></i>' + val.date + '</small></div><p>' + val.comment + '</p></div></li>';
            });
            $('#comment-panel').html("<ul class='chat'>" + comments + '</ul>');
        });
    },
    /* the function for the urgent panel on the client dashboard */
    urgent_panel: function (filter) {
        $.ajax({
            url: helper.baseUrl + 'dashboard/get_urgent',
            type: "POST",
            dataType: "JSON",
            data: $('.urgent-filter').serialize(),
        }).done(function (response) {
            $('.urgent-panel').empty();
            var $urgents = "";
            if (response.data.length > 0) {
                $.each(response.data, function (i, val) {
                    $urgents += '<li><a class="red tt pointer" data-toggle="tooltip" data-placement="left" title="' + val.last_comment + '"" href="' + helper.baseUrl + 'records/detail/' + val.urn + '">' + val.fullname + '</a><br><span class="small">Last Updated on ' + val.date_updated + '</span></li>';
                });
                $('.urgent-panel').append('<ul>' + $urgents + '</ul>');
                $('.tt').tooltip();
            } else {
                $('.urgent-panel').append('<p>' + response.msg + '</p>');
            }
        });
    },

    /* the function for the urgent panel on the client dashboard */
    pending_panel: function (filter) {
        $.ajax({
            url: helper.baseUrl + 'dashboard/get_pending',
            type: "POST",
            dataType: "JSON",
            data: $('.pending-filter').serialize(),
        }).done(function (response) {
            $('.pending-panel').empty();
            var $pending = "";
            if (response.data.length > 0) {
                $.each(response.data, function (i, val) {
                    $pending += '<li><a class="tt pointer" data-toggle="tooltip" data-placement="left" title="' + val.last_comment + '"" href="' + helper.baseUrl + 'records/detail/' + val.urn + '">' + val.fullname + '</a><br><span class="small">Last Updated on ' + val.date_updated + '</span></li>';
                });
                $('.pending-panel').append('<ul>' + $pending + '</ul>');
                $('.tt').tooltip();
            } else {
                $('.pending-panel').append('<p>' + response.msg + '</p>');
            }
        });
    },
    /* the function for the urgent panel on the client dashboard */
    appointments_panel: function (filter) {
        $.ajax({
            url: helper.baseUrl + 'dashboard/get_appointments',
            type: "POST",
            dataType: "JSON",
            data: $('.appointments-filter').serialize(),
        }).done(function (response) {
            $('.appointments-panel').empty();
            var $appointments = "";
            if (response.data.length > 0) {
                $.each(response.data, function (i, val) {
                    $appointments += '<li><a class="tt pointer" data-toggle="tooltip" data-placement="left" title="' + val.last_comment + '"" href="' + helper.baseUrl + 'records/detail/' + val.urn + '">' + val.fullname + '</a><br><span class="small">Start time: ' + val.start_date + '</span></li>';
                });
                $('.appointments-panel').append('<ul>' + $appointments + '</ul>');
                $('.tt').tooltip();
            } else {
                $('.appointments-panel').append('<p>' + response.msg + '</p>');
            }
        });
    },
    /* the function for the outcomes panel on the agent/client dashboard */
    favorites_panel: function (filter) {
        $.ajax({
            url: helper.baseUrl + 'dashboard/get_favorites',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize(),
            beforeSend: function () {
                $('.favorites-panel').html('<img src="' + helper.baseUrl + 'assets/img/ajax-loader-bar.gif" /> ');
            }
        }).done(function (response) {
            $('.favorites-panel').empty();
            var $table = "";
            var $tbody = "";
            if (response.data.length > 0) {
                $table = "<div class='table-responsive'><table class='table'><thead><tr><th>Campaign</th><th>Name</th><th>Last Update</th><th>Nextcall</th><th>Outcome</th><th>View</th></tr></thead><tbody>";
                $.each(response.data, function (i, val) {

                    $tbody += '<tr data-modal="view-record" data-urn="' + val.urn + '"><td>' + val.campaign_name + '</td><td>' + val.fullname + '</td><td>' + val.date_updated + '</td><td>' + val.nextcall + '</td><td>' + val.outcome + '</td><td><span class="glyphicon glyphicon-comment tt pointer" title="" data-toggle="tooltip" data-placement="left" data-original-title="' + val.comments + '"></span></td></tr>';

                });
                $table += $tbody;
                $table += "</tbody></table></div>";
                $('.favorites-panel').append($table);
            } else {
                $('.favorites-panel').append('<p>' + response.msg + '</p>');
            }
        });
    },
    /* the function for the missed call backs panel on the agent dashboard */
    callbacks_panel: function (filter) {
        $.ajax({
            url: helper.baseUrl + 'dashboard/all_callbacks',
            type: "POST",
            dataType: "JSON",
            data: $('.callbacks-filter').serialize(),
            beforeSend: function () {
                $('.callbacks').html('<img src="' + helper.baseUrl + 'assets/img/ajax-loader-bar.gif" /> ');
            }
        }).done(function (response) {
            $('.callbacks').empty();
            var $row = "";
            if (response.data.length > 0) {
                $.each(response.data, function (i, val) {
                    if (val.last_comments.length) {
                        comments = '<span class="glyphicon glyphicon-comment tt pointer" data-placement="left" data-toggle="tooltip" title="' + val.last_comments + '"></span>';
                    }
                    else {
                        comments = '<span class="glyphicon glyphicon-comment" style="opacity: 0.4; filter: alpha(opacity=40);"></span>';
                    }
                    $row += '<tr class="pointer" data-modal="view-record" data-urn="' + val.urn + '"><td>' + val.outcome + '</td><td>' + val.contact + '</td><td>' + val.name + '</td><td>' + val.campaign + '</td><td>' + val.date + '</td><td>' + val.time + '</td><td>' + comments + '</td></tr>';
                });
                $('.callbacks').html('<div class="table-responsive"><table class="table table-hover table-striped table-responsive"><thead><th>Type</th><th>Name</th><th>User</th><th>Campaign</th><th>Date</th><th>Time</th><th>Comments</th></thead><tbody>' + $row + '</tbody></table></div>');
                $('.tt').tooltip();
            } else {
                $('.callbacks').html('<p>' + response.msg + '</p>');
            }
        });
    },
    /* the function for the progress panel on the client dashboard */
    progress_panel: function (filter) {
        $.ajax({
            url: helper.baseUrl + 'dashboard/client_progress',
            type: "POST",
            dataType: "JSON",
            data: $('.progress-filter').serialize(),
            beforeSend: function () {
                $('.progress-panel').html('<img src="' + helper.baseUrl + 'assets/img/ajax-loader-bar.gif" /> ');
            }
        }).done(function (response) {
            $('.progress-panel').empty();
            var $row = "";
            if (response.data.length > 0) {
                $.each(response.data, function (i, val) {
                    var $urgent = "";
                    if (val.urgent == "1") {
                        $urgent = "class='red'";
                    }
                    $row += '<tr data-modal="view-record" data-urn="' + val.urn + '" ><td>' + val.name + '</td><td>' + val.campaign + '</td><td>' + val.date + '</td><td>' + val.time + '</td><td ' + $urgent + '>' + val.status + '</td><td><span class="glyphicon glyphicon-comment tt pointer" data-toggle="tooltip" data-placement="top" title="' + val.comments + '"></span></td></tr>';
                });
                $('.progress-panel').append('<div class="table-responsive"><table class="table table-striped table-responsive" style="max-height:600px"><thead><th>Name</th><th>Campaign</th><th>Date</th><th>Time</th><th>Status</th><th>View</th></thead><tbody>' + $row + '</tbody></table></div>');
                $('.tt').tooltip();
            } else {
                $('.progress-panel').append('<p>' + response.msg + '</p>');
            }
        });
    },
    /* the function for the progress panel on the client dashboard */
    interest_panel: function (filter) {
        $.ajax({
            url: helper.baseUrl + 'dashboard/nbf_progress',
            type: "POST",
            dataType: "JSON",
            data: $('.interest-filter').serialize(),
            beforeSend: function () {
                $('.interest-panel').html('<img src="' + helper.baseUrl + 'assets/img/ajax-loader-bar.gif" /> ');
            }
        }).done(function (response) {
            $('.interest-panel').empty();
            var $row = "";
            if (response.data.length > 0) {
                $.each(response.data, function (i, val) {
                    var $urgent = "";
                    if (val.urgent == "1") {
                        $urgent = "class='red'";
                    }
                    $row += '<tr data-modal="view-record" data-urn="' + val.urn + '" ><td>' + val.name + '</td><td>' + val.date + '</td><td>' + val.time + '</td><td ' + $urgent + '>' + val.status + '</td><td><span class="glyphicon glyphicon-comment tt pointer" data-toggle="tooltip" data-placement="top" title="' + val.comments + '"></span></td></tr>';
                });
                $('.interest-panel').append('<div class="table-responsive"><table class="table table-striped" style="max-height:600px"><thead><th>Name</th><th>Date</th><th>Time</th><th>Status</th><th>View</th></thead><tbody>' + $row + '</tbody></table></div>');
                $('.tt').tooltip();
            } else {
                $('.interest-panel').append('<p>' + response.msg + '</p>');
            }
        });
    },
    /* the function for the latest activity panel on the management dashboard */
    agent_activity: function (filter) {
        $.ajax({
            url: helper.baseUrl + 'dashboard/agent_activity',
            type: "POST",
            dataType: "JSON",
            data: $('.agent-activity-filter').serialize(),
            beforeSend: function () {
                $('.agent-activity').html('<img src="' + helper.baseUrl + 'assets/img/ajax-loader-bar.gif" /> ');
            }
        }).done(function (response) {
            $('.agent-activity').empty();
            var $row = "";
            if (response.data.length > 0) {
                $.each(response.data, function (i, val) {
                    $row += '<tr data-modal="view-record" data-urn="' + val.urn + '" ><td>' + val.name + '</td><td>' + val.campaign + '</td><td>' + val.outcome + '</td><td>' + val.when + '</td><td>' + val.outcome_date + '</td></tr>';
                });
                $('.agent-activity').append('<div class="table-responsive"><table class="table table-striped table-condensed"><thead><th>Name</th><th>Campaign</th><th>Last Outcome</th><th>Last Update</th><th>Last Positive</th></thead><tbody>' + $row + '</tbody></table></div>');
            } else {
                $('.agent-activity').append('<p>' + response.msg + '</p>');
            }
        });
    },
    /* the function for the agent success on the management dashboard */
    agent_success: function () {
        $.ajax({
            url: helper.baseUrl + 'dashboard/agent_success',
            type: "POST",
            dataType: "JSON",
            data: $('.success-filter').serialize(),
            beforeSend: function () {
                $('.agent-success').html('<img src="' + helper.baseUrl + 'assets/img/ajax-loader-bar.gif" /> ');
            }
        }).done(function (response) {
            $('.agent-success').empty();
            var $row = "";
            if (response.data.length > 0) {
                $.each(response.data, function (i, val) {
                    $row += '<tr><td>' + val.name + '</td>';
                    if (val.campaign) {
                        $row += "<td>" + val.campaign + "</td>	";
                    } else {
                        $row += "<td>All</td>	";
                    }
                    $row += '<td>' + val.dials + '</td><td>' + val.positives + '</td><td>' + val.rate + '</td></tr>';
                });
                $('.agent-success').append('<div class="table-responsive"><table class="table table-striped table-condensed"><thead><th>Name</th><th>Campaign</th><th>Dials</th><th>Positives</th><th>Success Rate</th></thead><tbody>' + $row + '</tbody></table></div>');
            } else {
                $('.agent-success').append('<p>' + response.msg + '</p>');
            }
        });
    },
    /* the function for the agent success on the management dashboard */
    agent_data: function (filter) {
        $.ajax({
            url: helper.baseUrl + 'dashboard/agent_data',
            type: "POST",
            dataType: "JSON",
            data: $('.agent-data-filter').serialize(),
            beforeSend: function () {
                $('.agent-data').html('<img src="' + helper.baseUrl + 'assets/img/ajax-loader-bar.gif" /> ');
            }
        }).done(function (response) {
            $('.agent-data').empty();
            var $row = "";
            if (response.data.length > 0) {
                $.each(response.data, function (i, val) {
                    $row += '<tr><td>' + val.name + '</td>';
                    if (val.campaign) {
                        $row += "<td>" + val.campaign + "</td>	";
                    } else {
                        $row += "<td>All</td>	";
                    }
                    $row += '<td>' + val.total + '</td><td>' + val.virgin + '</td><td>' + val.in_progress + '</td><td>' + val.completed + '</td></tr>';
                });
                $('.agent-data').append('<div class="table-responsive"><table class="table table-striped table-responsive table-condensed"><thead><th>Name</th><th>Campaign</th><th>Total</th><th>New</th><th>In Progress</th><th>Completed</th></thead><tbody>' + $row + '</tbody></table></div>');
            } else {
                $('.agent-data').append('<p>' + response.msg + '</p>');
            }
        });
    },
    /* the function for the agent current hours on the management dashboard */
    agent_current_hours: function (filter) {
        $.ajax({
            url: helper.baseUrl + 'dashboard/agent_current_hours',
            type: "POST",
            dataType: "JSON",
            data: $('.hours-filter').serialize(),
            beforeSend: function () {
                $('.agent-current-hours').html('<img src="' + helper.baseUrl + 'assets/img/ajax-loader-bar.gif" /> ');
            }
        }).done(function (response) {
            $('.agent-current-hours').empty();
            var $row = "";
            if (response.success) {
                var start = new Date;
                $.each(response.data, function (agent, campaign) {
                    $row += '<tr id="duration"><td>' + agent + '</td>';
                    //$.each(campaigns, function (campaign, duration) {
                    elapsed_seconds = ((new Date - start) / 1000) + Number(campaign['duration']);
                    $row += "<td>" + campaign['campaign'] + "</td>	";
                    $row += '<td style="font-weight:bold;" id="time_box_date_' + agent + '">' + get_elapsed_time_string(elapsed_seconds) + '</td><td style="display:none;" id=time_box_seconds_' + agent + '">' + campaign['duration'] + '</td>';
                    $row += "<td>" + campaign['worked'] + "</td>";
                    $row += '<td id="transfers_box_' + agent + '">' + campaign['transfers'] + '</td>';
                    $row += '<td id="rate_box_' + agent + '">' + campaign['rate'] + ' per hour' + '</td>';
                    $row += "</tr>";
                    //});
                });
                $('.agent-current-hours').append('<div class="table-responsive"><table class="table table-striped table-responsive"><thead><th>Agent</th><th>Campaign</th><th>Time on this campaign</th><th>Records worked</th><th>Transfers</th><th>Current Rate</th><th style="display:none;"></th></thead><tbody>' + $row + '</tbody></table></div>');
            } else {
                $('.agent-current-hours').append('<p>' + response.msg + '</p>');
            }
        });
    },

    get_outcomes_filter: function () {
        $.ajax({
            url: helper.baseUrl + 'reports/get_outcomes_filter',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize()
        }).done(function (response) {
            if (response.success) {
                var options = "";
                $.each(response.campaign_outcomes, function (type, data) {
                    options += "<optgroup label=" + type + ">";
                    $.each(data, function (i, val) {
                        options += "<option value=" + val.id + ">" + val.name + "</option>";
                    });
                    options += "</optgroup>";
                });
                $('#outcome-filter').html(options).selectpicker('refresh');
            }
        });
    },

    get_sources_filter: function () {
        $.ajax({
            url: helper.baseUrl + 'reports/get_sources_filter',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize()
        }).done(function (response) {
            if (response.success) {
                var options = "";
                $.each(response.campaign_sources, function (i, val) {
                    options += "<option value=" + val.id + ">" + val.name + "</option>";
                });
                $('#source-filter').html(options).selectpicker('refresh');
            }
        });
    },

    /* Show the custom dashboards */
    custom_dash_panel: function () {
        $.ajax({
            url: helper.baseUrl + 'dashboard/get_dashboards_to_manage',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize(),
            beforeSend: function () {
                $('#custom-dash').html('<img src="' + helper.baseUrl + 'assets/img/ajax-loader-bar.gif" /> ');
            }
        }).done(function (response) {
            $('#custom-dash').empty();
            var dash_ar = "";
            if (response.success) {
                var dashboards = "";
                var reports = "";
                var others = "";
                $.each(response.dashboards, function (i, val) {
                    var element = '<li class="left clearfix">' +
                        '<div class="chat-body clearfix">' +
                        '<div class="header">' +
                        '<strong class="primary-font">' +
                        '<a href="#" class="view-dashboard-btn" item-id="' + val.dashboard_id + '" >' +
                        val.name +
                        '</a>' +
                        '</strong>' +
                        '<small class="pull-right text-muted">' +
                        '<span class="btn btn-default btn-xs pull-right marl edit-dashboard-btn" ' +
                        'item-id="' + val.dashboard_id + '"' +
                        'item-name="' + val.name + '"' +
                        'item-description="' + val.description + '"' +
                        'item-viewers="' + val.viewers + '"' +
                        'item-campaigns="' + val.campaigns + '"' +
                        'item-type="' + val.dash_type + '"' +
                        '>' +
                        '<span class="glyphicon glyphicon-pencil"></span>' +
                        ' Edit' +
                        '</span>' +
                        '<span class="btn btn-default btn-xs pull-right marl view-dashboard-btn" ' +
                        'item-id="' + val.dashboard_id + '"' +
                        '>' +
                        '<span class="glyphicon glyphicon-eye-open"></span>' +
                        ' View' +
                        '</span>' +
                        '</small>' +
                        '</div>' +
                        '<p>' + val.description + '</p>' +
                        '</div>' +
                        '</li>';

                    switch (val.dash_type) {
                        case "Dashboard":
                            dashboards += element;
                            break;
                        case "Report":
                            reports += element;
                            break;
                        default:
                            others += element;
                            break;
                    }
                });
                dash_ar = (dashboards.length > 0 ? "<h1>Dashboards</h1> <ul class='chat'>" + dashboards + "</ul>" : "") +
                    (reports.length > 0 ? "<h1>Reports</h1> <ul class='chat'>" + reports + "</ul>" : "") +
                    (others.length > 0 ? "<h1>Others</h1> <ul class='chat'>" + others + "</ul>" : "");
            }
            else {
                dash_ar = "No dashboards created yet";
            }
            $('#custom-dash').html("<ul class='chat'>" + dash_ar + '</ul>');
        });
    },

    //Create new dashboard
    new_dashboard: function (btn) {
        var dashboard_id, dashboard_name, dashboard_description, dashboard_type, dashboard_viewers, dashboard_campaigns;
        dashboard_id = dashboard_name = dashboard_description = dashboard_viewers = dashboard_campaigns = '';

        var filter_form = '';

        if (typeof btn !== 'undefined') {
            dashboard_id = (typeof btn.attr('item-id') === 'undefined') ? '' : btn.attr('item-id');
            dashboard_name = (typeof btn.attr('item-name') === 'undefined') ? '' : btn.attr('item-name');
            dashboard_description = (typeof btn.attr('item-description') === 'undefined') ? '' : btn.attr('item-description');
            dashboard_type = (typeof btn.attr('item-type') === 'undefined') ? '' : btn.attr('item-type');
            dashboard_viewers = (typeof btn.attr('item-viewers') === 'undefined') ? '' : btn.attr('item-viewers').split(",");
            dashboard_campaigns = (typeof btn.attr('item-campaigns') === 'undefined') ? '' : btn.attr('item-campaigns').split(",");
        }

        //get the viewers
        $.ajax({
            url: helper.baseUrl + 'dashboard/get_dash_viewers',
            type: "POST",
            dataType: "JSON"
        }).done(function (response) {
            var options = "";
            if (response.success) {
                //Viewers
                $.each(response.viewers, function (role, users) {
                    options += "<optgroup label='" + role + "'>";
                    $.each(users, function (i, val) {
                        var selected = "";
                        if (jQuery.inArray(val.id[0], dashboard_viewers) >= 0) {
                            selected = "selected";
                        }
                        options += "<option value='" + val.id + "' " + selected + ">" + val.name + "</option>";
                    });
                    options += "</optgroup>";
                });
            }
            $.ajax({
                url: helper.baseUrl + 'dashboard/get_dash_filters',
                type: "POST",
                dataType: "HTML",
                data: {dashboard_id: dashboard_id}
            }).done(function (response) {
                filter_form = response;

                var select = "";
                if (helper.permissions['dashboard viewers'] > 0) {
                    select +=
                        "<p>Allowed users <i data-toggle='tooltip' data-title='The users that have access to this dashboard' class='tt pointer glyphicon glyphicon-info-sign'></i>'</p>" +
                        "<select name='viewers[]' class='selectpicker' multiple id='viewer_select' data-width='100%' data-size='5' data-live-search='true' data-live-search-placeholder='Search' data-actions-box='true'>" +
                        options +
                        "</select>";
                }

                var mheader = "Dashboard";
                var mbody = "<ul class='nav nav-tabs' id='panel-tabs-" + dashboard_id + "' style='background:#eee; width:100%;'>" +
                    "<li class='data-tab active'><a href='#dashboard-general-" + dashboard_id + "' class='tab' data-toggle='tab'>General</a></li>" +
                    "<li class='data-tab'><a href='#dashboard-filters-" + dashboard_id + "' class='tab' data-toggle='tab'>Filters</a></li>" +
                    "</ul>" +
                    "<div class='tab-content'>" +
                    "<div class='tab-pane active' id='dashboard-general-" + dashboard_id + "'>" +
                    "<form id='new-dashboard-form' >" +
                    "<input type='hidden' name='dashboard_id' value='" + dashboard_id + "'/>" +
                    "<div class='form-group input-group-sm'>" +
                    "<div class='row'>" +
                    "<div class='col-xs-6'>" +
                    "<p>Dashboard Name </p>" +
                    "<input type='text' name='name' value='" + dashboard_name + "' class=''form-control' style='min-width: 100%' required/>" +
                    "</div>" +
                    "<div class='col-xs-6'>" +
                    select +
                    "</div>" +
                    "</div>" +
                    "</div>" +
                    "<div class='form-group input-group-sm'>" +
                    "<p>Description </p>" +
                    "<textarea name='description' class='form-control' style='min-width: 100%; min-height: 200px;'>" + dashboard_description + "</textarea>" +
                    "</div>" +
                    "<p>Should this page be included in the dashboard menu or the report menu?</p><div class='form-group input-group-sm'>" +
                    "<input type='checkbox' id='dash-type' data-toggle='toggle' data-width='200' data-onstyle='success' data-offstyle='info' data-on='Dashboard' data-off='Report'>" +
                    "<input type='hidden' name='dash_type' value='Dashboard'>" +
                    "</div>" +
                    "</form>" +
                    "</div>" +
                    "<div class='tab-pane' id='dashboard-filters-" + dashboard_id + "'>" +
                    filter_form +
                    "</div>" +
                    "</div>";

                var mfooter = '<button type="submit" class="btn btn-primary pull-right marl" id="save-dashboard-btn">Save</button>' +
                    '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Cancel</button>';

                modals.load_modal(mheader, mbody, mfooter);
                $modal.find('.tt').tooltip();
                dashboard.set_dashboard_type(dashboard_type);
            });
        });
    },

    set_dashboard_type: function (dash_type) {
        modal_body.find('#dash-type').bootstrapToggle();

        if (dash_type === "Dashboard") {
            $('#dash-type').bootstrapToggle('on');
            modal_body.find('input[name="dash_type"]').val("Dashboard");
        } else if (dash_type === "Report") {
            $('#dash-type').bootstrapToggle('off');
            modal_body.find('input[name="dash_type"]').val("Report");
        }
        else {
            $('#dash-type').bootstrapToggle('on');
            modal_body.find('input[name="dash_type"]').val("Dashboard");
        }
        $('#dash-type').on('change', function (e) {
            if ($(this).prop("checked")) {
                modal_body.find('input[name="dash_type"]').val("Dashboard");
            } else {
                modal_body.find('input[name="dash_type"]').val("Report");
            }
        });
    },

    save_dashboard: function () {
        $.ajax({
            url: helper.baseUrl + 'dashboard/save_dashboard',
            data: $('#new-dashboard-form').serialize(),
            type: "POST",
            dataType: "JSON"
        }).done(function (response) {
            if (response.success) {
                flashalert.success(response.msg);
                $('.dashboard-filter-form').find('input[name="dashboard_id"]').val(response.dashboard_id);
                $.ajax({
                    url: helper.baseUrl + 'dashboard/save_dashboard_filters',
                    data: $('.dashboard-filter-form').serialize(),
                    type: "POST",
                    dataType: "JSON"
                }).done(function (response) {
                    if (response.success) {
                        flashalert.success(response.msg);
                    }
                    else {
                        flashalert.danger(response.msg);
                    }

                });
                $('.close-modal').trigger('click');
                dashboard.custom_dash_panel();
            }
            else {
                flashalert.danger(response.msg);
            }
        });
    },

    select_report: function (dashboard_id) {
        $.ajax({
            url: helper.baseUrl + 'dashboard/get_export_forms',
            type: "POST",
            dataType: "JSON",
            data: {'dashboard_id': dashboard_id}
        }).done(function (response) {
            var options = "";

            options += "<option value=''> Select one report </option>";
            $.each(response.data, function (i, val) {
                options += "<option data-subtext='" + val.description + "' value='" + val.export_forms_id + "'>" + val.name + "</option>";
            });

            var select_report =
                "<select name='report_id' class='selectpicker' id='report_select' data-size='5' data-live-search='true' data-live-search-placeholder='Search' data-actions-box='true'>" +
                options +
                "</select>";

            var mheader = "Add report panel";

            var mbody = "<form id='add-report-form' >" +
                "<input type='hidden' name='dashboard_id' value='" + dashboard_id + "'/>" +
                "<input type='hidden' name='position' value='" + response.position + "'/>" +
                "<div class='form-group input-group-sm'>" +
                '<div class="row">' +
                '<div class="col-lg-12">' +
                '<p>Select the report panel</p>' +
                select_report +
                '</div>' +
                '</div>' +
                '<div class="row">' +
                '<div class="col-lg-6">' +
                '<div class=""form-group input-group-sm">' +
                '<p>Select the panel size</p>' +
                '<select name="column_size" class="selectpicker" id="report_select" data-size="5">' +
                '<option value="12">100%</option>' +
                '<option value="9">75%</option>' +
                '<option value="6" Selected>50%</option>' +
                '<option value="4">33%</option>' +
                '<option value="3">25%</option>' +
                '</select>' +
                '</div>' +
                '</div>' +
                '<div class="col-lg-6">' +
                '<div class="btn-group show-default-graphs" data-toggle="buttons">' +
                '<p>What do you want to show by default?</p>' +
                '<label class="btn btn-default active">' +
                '<input type="radio" value="data" checked="checked" aria-label="..." name="show_default"> Data' +
                '</label>' +
                '<label class="btn btn-default">' +
                '<input type="radio" value="graphs" aria-label="..." name="show_default"> Graphs' +
                '</label>' +
                '</div>' +
                '</div>' +
                '</div>' +
                "</div>" +
                "</form>";

            var mfooter = '<button type="submit" class="btn btn-primary pull-right marl" id="add-report-btn">Add</button>' +
                '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Cancel</button>';

            modals.load_modal(mheader, mbody, mfooter);
            modal_body.css('overflow', 'visible');


        });
    },

    add_report: function () {
        $.ajax({
            url: helper.baseUrl + 'dashboard/add_report',
            data: $('#add-report-form').serialize(),
            type: "POST",
            dataType: "JSON"
        }).done(function (response) {
            if (response.success) {
                flashalert.success(response.msg);
                $('.close-modal').trigger('click');
                dashboard.load_dash(response.dashboard_id);
            }
            else {
                flashalert.danger(response.msg);
            }
        });
    },

    remove_report: function (dashboard_id, report_id) {
        $.ajax({
            url: helper.baseUrl + 'dashboard/remove_report',
            data: {'dashboard_id': dashboard_id, 'report_id': report_id},
            type: "POST",
            dataType: "JSON"
        }).done(function (response) {
            if (response.success) {
                flashalert.success(response.msg);
                dashboard.load_dash(response.dashboard_id);
            }
            else {
                flashalert.danger(response.msg);
            }
        });
    },

    move_report: function (dashboard_id, report_id, current_position, next_position) {
        $.ajax({
            url: helper.baseUrl + 'dashboard/move_report',
            data: {
                'dashboard_id': dashboard_id,
                'report_id': report_id,
                'current_position': current_position,
                'next_position': next_position
            },
            type: "POST",
            dataType: "JSON"
        }).done(function (response) {
            if (response.success) {
                flashalert.success(response.msg);
                dashboard.load_dash(response.dashboard_id);
            }
            else {
                flashalert.danger(response.msg);
            }
        });
    },

    resize_report: function (dashboard_id, report_id, column_size) {
        $.ajax({
            url: helper.baseUrl + 'dashboard/resize_report',
            data: {'dashboard_id': dashboard_id, 'report_id': report_id, 'column_size': column_size},
            type: "POST",
            dataType: "JSON"
        }).done(function (response) {
            if (response.success) {
                flashalert.success(response.msg);
                dashboard.load_dash(response.dashboard_id);
            }
            else {
                flashalert.danger(response.msg);
            }
        });
    },

    load_dash: function (dashboard_id) {
        $.ajax({
            url: helper.baseUrl + 'dashboard/get_dashboard_reports_by_id',
            data: {'dashboard_id': dashboard_id},
            type: "POST",
            dataType: "JSON"
        }).done(function (response) {
            $('.dashboard-area').empty();
            if (response.success) {
                var panels = "";
                var data_divs = [];
                var charts_divs = [];
                //Build the panels
                $.each(response.reports, function (i, report) {
                    var columns = "col-lg-" + (report.column_size);
                    panels += '<div class="' + columns + '">' +
                        '<div class="panel panel-primary">' +
                        '<div class="panel-heading clearfix">' + report.name + ' <span class="glyphicon glyphicon-info-sign pointer" data-toggle="tooltip" title="' + report.description + '"></span>' +
                        '</div>' +
                        '<div class="panel-body" id="' + report.report_id + '-panel" style="padding: 0px;">' +
                        '<ul class="nav nav-tabs" id="panel-tabs-' + report.report_id + '" style=" background:#eee; width:100%;">' +
                        '<li class="data-tab ' + (report.show_default === 'data' ? 'active' : '') + '"><a href="#data-system-' + report.report_id + '" class="tab" data-toggle="tab">Data</a></li>' +
                        '<li class="plots-tab ' + (report.show_default === 'graphs' ? 'active' : '') + '"><a href="#chart-div-system-' + report.report_id + '" class="tab" data-toggle="tab">Graphs</a></li>' +
                        '<li class="dropdown pull-right">' +
                        '<a id="options-' + report.report_id + '" class="dropdown-toggle" aria-controls="options-contents-' + report.report_id + '" data-toggle="dropdown" href="#" aria-expanded="true">' +
                        'Options' +
                        '<span class="caret"></span>' +
                        '</a>' +
                        '<ul id="options-contents-' + report.report_id + '" class="dropdown-menu slidedown pull-right" aria-labelledby="options-' + report.report_id + '">' +
                        '<li><a href="#" id="1" class="remove-dashreport-btn" data-report-id="' + report.report_id + '" data-dashboard-id="' + dashboard_id + '" data-ref="remove"><i class="fa fa-trash pointer"></i> Remove</a></li>' +
                        '<li><a href="#" id="1" class="export-dashreport-btn" data-report-id="' + report.report_id + '" data-ref="export"><i class="glyphicon glyphicon-floppy-save pointer"></i> Export </a></li>' +
                        '<li class="divider"></li>' +
                        '<li class="' + (parseInt(report.position) <= 0 ? "disabled" : "") + '"><a href="#" id="1" class="move-dashreport-btn" data-report-id="' + report.report_id + '" data-dashboard-id="' + dashboard_id + '" current-position="' + report.position + '" next-position="' + (0) + '"><i class="glyphicon glyphicon-export pointer"></i> Move First </a></li>' +
                        '<li class="' + (parseInt(report.position) <= 0 ? "disabled" : "") + '"><a href="#" id="1" class="move-dashreport-btn" data-report-id="' + report.report_id + '" data-dashboard-id="' + dashboard_id + '" current-position="' + report.position + '" next-position="' + (parseInt(report.position) - 1) + '"><i class="glyphicon glyphicon-arrow-left pointer"></i> Move Previous </a></li>' +
                        '<li class="' + (parseInt(report.position) >= response.reports.length - 1 ? "disabled" : "") + '"><a href="#" id="1" class="move-dashreport-btn" data-report-id="' + report.report_id + '" data-dashboard-id="' + dashboard_id + '" current-position="' + report.position + '" next-position="' + (parseInt(report.position) + 1) + '"><i class="glyphicon glyphicon-arrow-right pointer"></i> Move Next </a></li>' +
                        '<li class="' + (parseInt(report.position) >= response.reports.length - 1 ? "disabled" : "") + '"><a href="#" id="1" class="move-dashreport-btn" data-report-id="' + report.report_id + '" data-dashboard-id="' + dashboard_id + '" current-position="' + report.position + '" next-position="' + (parseInt(response.reports.length) - 1) + '"><i class="glyphicon glyphicon-import pointer"></i> Move Last </a></li>' +
                        '<li class="divider"></li>' +
                        '<li class="' + (parseInt(report.column_size) == 3 ? "disabled" : "") + '"><a href="#" column-size="3" class="resize-dashreport-btn" data-report-id="' + report.report_id + '" data-dashboard-id="' + dashboard_id + '"><i class="fa fa-expand pointer"></i> 25% </a></li>' +
                        '<li class="' + (parseInt(report.column_size) == 4 ? "disabled" : "") + '"><a href="#" column-size="4" class="resize-dashreport-btn" data-report-id="' + report.report_id + '" data-dashboard-id="' + dashboard_id + '"><i class="fa fa-expand pointer"></i> 33% </a></li>' +
                        '<li class="' + (parseInt(report.column_size) == 6 ? "disabled" : "") + '"><a href="#" column-size="6" class="resize-dashreport-btn" data-report-id="' + report.report_id + '" data-dashboard-id="' + dashboard_id + '"><i class="fa fa-expand pointer"></i> 50% </a></li>' +
                        '<li class="' + (parseInt(report.column_size) == 9 ? "disabled" : "") + '"><a href="#" column-size="9" class="resize-dashreport-btn" data-report-id="' + report.report_id + '" data-dashboard-id="' + dashboard_id + '"><i class="fa fa-expand pointer"></i> 75% </a></li>' +
                        '<li class="' + (parseInt(report.column_size) == 12 ? "disabled" : "") + '"><a href="#" column-size="12" class="resize-dashreport-btn" data-report-id="' + report.report_id + '" data-dashboard-id="' + dashboard_id + '"><i class="fa fa-expand pointer"></i> 100% </a></li>' +
                        '</ul>' +
                        '</li>' +
                        '</ul>' +
                        '<div class="tab-content" style="padding: 0px;">' +
                        '<div class="tab-pane ' + (report.show_default === 'data' ? 'active' : '') + '" id="data-system-' + report.report_id + '"  style="padding: 0px;">' +
                        '<img src="' + helper.baseUrl + "assets/img/ajax-loader-bar.gif" + '"/>' +
                        '</div>' +
                        '<div class="tab-pane ' + (report.show_default === 'graphs' ? 'active' : '') + '" id="chart-div-system-' + report.report_id + '" style="padding: 0px; overflow-y: auto; overflow-x: hidden; max-height: 400px;">' +
                        '<div style="padding: 20px;">No graphs added</div>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '</div>' +
                        '</div>';

                    data_divs.push('data-system-' + report.report_id);
                    charts_divs.push('chart-div-system-' + report.report_id);

                });
                $('.dashboard-area').append(panels);
                $('[data-toggle="tooltip"]').tooltip();
                //Set the charts on the show-charts class in order to be shown when we click on this button
                $('.show-charts').attr('data', data_divs.join());
                $('.show-charts').attr('charts', charts_divs.join());

                //Get the data content of the panels
                $.each(response.reports, function (i, report) {
                    var show_first = true;
                    var order_by;
                    var order_by_dir;
                    $('.filter-form').find('input[name="export_forms_id"]').val(report.report_id);
                    $.ajax({
                        url: helper.baseUrl + 'exports/load_export_report_data',
                        data: $('.filter-form').serialize(),
                        type: "POST",
                        dataType: "JSON"
                    }).done(function (resp) {
                        if (resp.success && resp.header) {
                            var body = "<div class='table-" + report.report_id + " scroll table-responsive'><table id='table-" + report.report_id + "' class='table table-bordered table-hover table-striped small' ></table></div>";
                            // removed style='min-height: 400px;'
                            $('#data-system-' + report.report_id).empty();
                            $('#data-system-' + report.report_id).append(body);

                            //var width = ($('.table-'+report.report_id).find('table').width()/resp.header.length);

                            body = "<thead><tr>";
                            $.each(resp.header, function (i, val) {
                                if (resp.header.length) {
                                    body += "<th>" + val + "</th>";
                                }
                            });
                            body += "</tr></thead><tbody>";
                            $.each(resp.data, function (i, data) {
                                if (resp.data.length) {
                                    body += "<tr>";
                                    var j=0;
                                    $.each(data, function (k, val) {
                                        if ((typeof order_by == 'undefined') && resp.order_by[0] == k) {
                                            order_by = j;
                                            order_by_dir = resp.order_by[1];
                                        }
                                        body += "<td>" + val + "</td>";
                                        j++;
                                    });
                                    body += "</tr>";
                                }
                            });
                            body += "</tbody>";

                            $('.table-' + report.report_id).find('table').append(body);
                            /*
                             $('.table-'+report.report_id).find('table').on('scroll', function () {
                             var table = $(this).find('table');
                             $('.table-'+report.report_id).find("table > *").width($('.table-'+report.report_id).find('table').width() + $('.table-'+report.report_id).find('table').scrollLeft());
                             });
                             */

                            var dom_size = (report.column_size < 6 ? 12 : 6);
                            $('#table-' + report.report_id).DataTable({
                                buttons: [
                                    'copy', 'csv', 'excel', 'print'
                                ],
                                "dom": '<"top top-row small" <"col-lg-' + dom_size + '"<"pull-left"f>><"col-lg-' + dom_size + '"<"pull-right"B>>>rt<"bottom-' + report.report_id + ' bottom-row small"<"col-lg-' + dom_size + '"l><"col-lg-' + dom_size + '"<"pull-right"i>><"col-lg-' + dom_size*2 + '"p>><"clear">',
                                "pagingType": "full",
                                "order": [[ (typeof order_by != 'undefined'?order_by:0), (typeof order_by_dir != 'undefined'?order_by_dir:"asc") ]],
                                "createdRow": function (row, data, dataIndex) {

                                    if (resp.header[0] == "URN") {
                                        show_first = false;
                                        $(row).attr('data-urn', data[0]);
                                        $(row).attr('data-modal', 'view-record');
                                        $(row).addClass('pointer');
                                    } else if (resp.header[0] == "Appointment ID") {
                                        show_first = false;
                                        var appointment_id = data[0];
                                        //Get urn if exists on the table (for double click action)
                                        if (jQuery.inArray("URN", resp.header) >= 0) {
                                            var appointment_urn = data[jQuery.inArray("URN", resp.header)];
                                            $(row).attr('data-urn', appointment_urn);
                                        }
                                        $(row).attr('data-id', appointment_id);
                                        $(row).attr('data-modal', 'view-appointment');
                                        $(row).addClass('pointer');
                                    }
                                },
                                "columnDefs": [
                                    {"visible": show_first, "targets": 0}
                                ]
                            });
                            //$(".bottom-"+report.report_id).css("min-height", "160px");
                            if (dom_size == 12) {
                                $(".bottom-" + report.report_id).css("text-align", "right");
                            }
                        }
                        else {
                            $('#data-system-' + report.report_id).html("<div style='padding:20px;'>No results found!</div>");
                        }

                        if (resp.success && resp.graphs) {
                            $('#chart-div-system-' + report.report_id).empty();
                            var body = '<div class="row">';
                            if (resp.graphs.length) {
                                $.each(resp.graphs, function (i, graph) {
                                    body += '<div class="col-lg-' + (12 / Math.round(report.column_size / 3)) + '"><div id="export-chart-' + graph.graph_id + '" style="text-shadow: none">' +
                                        '<p>' + graph.name + '</p>' +
                                        '<img src="' + helper.baseUrl + 'assets/img/ajax-loader-bar.gif" /> ' +
                                        '</div></div>';
                                });

                                //LOAD google graphs
                                google.load('visualization', '1', {
                                    packages: ['corechart'], 'callback': function () {
                                        $.each(resp.graphs, function (i, graph) {
                                            var rows = [];
                                            var title = graph.name;

                                            // Set chart options
                                            var options = {
                                                'legend': {position: 'none'},
                                                'title': title,
                                                'width': 250,
                                                'height': 400,
                                                curveType: 'function',
                                                'hAxis': {direction: -1, slantedText: true, slantedTextAngle: 45},
                                                isStacked: (graph.z_value) ? true : false
                                            };

                                            if (graph.z_value) {
                                                var x_arr = [];
                                                var z_arr = [];
                                                $.each(graph.data, function (x_value, z_value) {
                                                    z_arr = [graph.z_value];
                                                    var aux = [];
                                                    aux.push(x_value);
                                                    $.each(z_value, function (i, y_value) {
                                                        z_arr.push(i);
                                                        aux.push(y_value);
                                                    });
                                                    x_arr.push(aux);
                                                });

                                                var data_arr = [z_arr];
                                                $.each(x_arr, function (k, v) {
                                                    data_arr.push(v);
                                                });
                                                var data = google.visualization.arrayToDataTable(data_arr);
                                            }
                                            else {
                                                var data = new google.visualization.DataTable();
                                                data.addColumn('string', 'Topping');
                                                data.addColumn('number', 'Count');
                                                $.each(graph.data, function (i, v) {
                                                    rows.push([i, parseInt(v)]);
                                                });
                                                data.addRows(rows);
                                            }

                                            //Draw the graph
                                            switch (graph.type) {
                                                case "bars":
                                                    var chart = new google.visualization.ColumnChart(document.getElementById('export-chart-' + graph.graph_id));
                                                    chart.draw(data, options);
                                                    break;
                                                case "pie":
                                                    var chart = new google.visualization.PieChart(document.getElementById('export-chart-' + graph.graph_id));
                                                    chart.draw(data, options);
                                                    break;
                                                case "line":
                                                    var chart = new google.visualization.LineChart(document.getElementById('export-chart-' + graph.graph_id));
                                                    chart.draw(data, options);
                                                    break;
                                                case "area":
                                                    var chart = new google.visualization.AreaChart(document.getElementById('export-chart-' + graph.graph_id));
                                                    chart.draw(data, options);
                                                    break;
                                                default:
                                                    break;
                                            }
                                        });
                                    }
                                });
                            }
                            else {
                                body += "<div class='col-lg-12' style='margin: 20px;'>No Graphs Created!</div>"
                            }
                            body += "</div></div>";
                            $('#chart-div-system-' + report.report_id).html(body);
                        }
                    });
                });
            }
            else {
                topalert.danger(response.msg);
            }
        });
    }
}