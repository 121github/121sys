// JavaScript Document

var export_data = {
    init: function () {
        filters.init();
        /*table filter */
        $(document).on('keyup', '#filter', function () {
            var rex = new RegExp($(this).val(), 'i');
            $('.searchable tr').hide();
            $('.searchable tr').filter(function () {
                return rex.test($(this).text());
            }).show();
        })

        $('.search-form').on("keyup keypress", function (e) {
            var code = e.keyCode || e.which;
            if (code == 13) {
                e.preventDefault();
                return false;
            }
        });

        $('.daterange').daterangepicker({
                opens: "left",
                ranges: {
                    'Any Time': ["02/07/2014", moment()],
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract( 1,'days'), moment().subtract(1,'days')],
                    'Last 7 Days': [moment().subtract( 6,'days'), moment()],
                    'Last 30 Days': [moment().subtract(29,'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1,'month').startOf('month'), moment().subtract(1,'month').endOf('month')]
                },
                format: 'DD/MM/YYYY',
                minDate: "02/07/2014",
                maxDate: moment(),
                startDate: "02/07/2014",
                endDate: moment()
            },
            function (start, end, element) {
                var $btn = this.element;
                $btn.find('.date-text').html(start.format('MMMM D') + ' - ' + end.format('MMMM D'));
                $('.filter-form').find('input[name="date_from"]').val(start.format('YYYY-MM-DD'));
                $('.filter-form').find('input[name="date_to"]').val(end.format('YYYY-MM-DD'));
            });
        $(document).on("click", '.daterange', function (e) {
            e.preventDefault();
        });

        //optgroup
        $('li.dropdown-header').on('click', function (e) {
            setTimeout(function () {
                //Get outcomes by campaigns selected
                export_data.get_outcomes_filter();

                export_data.get_sources_filter();
                export_data.get_pots_filter();
            }, 500);
        });

        $(document).on("click", '#filter-submit', function (e) {
            e.preventDefault();
            export_data.load_filters();
            $('#filter-right').data("mmenu").close();
        });

        $(document).on("click", '.daterange', function (e) {
            e.preventDefault();
        });

        $(document).on("change", ".campaign-filter", function (e) {
            e.preventDefault();
            //Get outcomes by campaigns selected
            export_data.get_outcomes_filter();

            export_data.get_sources_filter();
            export_data.get_pots_filter();
        });

        $(document).on("click", '.new-export-btn', function (e) {
            e.preventDefault();
            export_data.new_custom_export();
        });

        $(document).on("click", '.edit-btn', function (e) {
            e.preventDefault();
            export_data.edit_export_form($(this));
        });

        $(document).on('click', '.close-edit-btn', function (e) {
            e.preventDefault();
            export_data.close_export_form();
        });

        $(document).on('click', '#save-edit-btn', function (e) {
            e.preventDefault();
            export_data.save_export_form();
        });

        $(document).on('click', '.del-btn', function (e) {
            e.preventDefault();
            export_data.delete_export_form($(this).attr('item-id'));
        });

        $(document).on('click', '#del-confirm-btn', function (e) {
            e.preventDefault();
            export_data.delete_export($(this).attr('item-id'));
        });

        $(document).on('click', '.save-export-graph-btn', function (e) {
            e.preventDefault();
            export_data.save_export_graph();
        });

        $(document).on('click', '.remove-export-graph-btn', function (e) {
            e.preventDefault();
            export_data.delete_export_graph($(this).attr('item-id'));
        });

        $(document).on('click', '.export-report-btn', function (e) {
            e.preventDefault();
            export_data.load_export_report_data($(this).attr('item-id'), $(this).attr('item-name'));
        });

        $(document).on('click', '.export-available-data-btn', function (e) {
            e.preventDefault();
            export_data.load_available_export_report_data($(this).attr('item-name'));
        });

        $(document).on('click', '.export-file-btn', function (e) {
            e.preventDefault();
            export_data.export_file($(this).attr('item-id'));
        });

        $(document).on('click', '.export-available-file-btn', function (e) {
            e.preventDefault();
            export_data.export_available_file($(this).attr('item-name'));
        });

        $(document).on('click', '.modal-export-file-btn', function (e) {
            e.preventDefault();
            export_data.export_file($(this).attr('item-id'));
        });

        $(document).on('click', '.modal-export-available-file-btn', function (e) {
            e.preventDefault();
            export_data.export_available_file($(this).attr('item-name'));
        });

        $(document).on('click', '.close-export-report', function (e) {
            e.preventDefault();
            export_data.close_export_report($(this));
        });

        $(document).on('change', 'textarea[name="query"], input[name="order_by"], input[name="group_by"]', function (e) {
            e.preventDefault();
            var query = $('.edit-export-form').find('textarea[name="query"]').val();
            var order_by = $('.edit-export-form').find('input[name="order_by"]').val();
            var group_by = $('.edit-export-form').find('input[name="group_by"]').val();

            $('.preview-qry').html(query+(group_by.length>""?" GROUP BY "+group_by:"")+(order_by.length>""?" ORDER BY "+order_by:""));
        });

        export_data.load_export_forms();
        export_data.load_filters();
    },

    load_filters: function () {
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

        //Data Pot
        var size = ($('.pot-filter  option:selected').size() > 0 ? "(" + $('.pot-filter  option:selected').size() + ")" : '');
        filters += "<h5 style='border-bottom: 1px solid #e2e2e2; padding-bottom: 4px;'><strong>Data Pots</strong> " + size + "</h5><ul>";
        $('.pot-filter  option:selected').each(function (index) {
            filters += "<li style='list-style-type:none'>" + $(this).text() + "</li>";
        });
        filters += "</ul>";

        //User
        var size = ($('.user-filter  option:selected').size() > 0 ? "(" + $('.user-filter  option:selected').size() + ")" : '');
        filters += "<h5 style='border-bottom: 1px solid #e2e2e2; padding-bottom: 4px;'><strong>Users</strong> " + size + "</h5><ul>";
        $('.user-filter  option:selected').each(function (index) {
            filters += "<li style='list-style-type:none'>" + $(this).text() + "</li>";
        });
        filters += "</ul>";

        $('#filters').html(filters);
        $('#filters-custom').html(filters);
    },

    load_export_forms: function () {
        $tbody = $('.export-data .ajax-table').find('tbody');
        $tbody.empty();

        $.ajax({
            url: helper.baseUrl + 'exports/get_export_forms',
            type: "POST",
            dataType: "JSON",
            async: false
        }).done(function (response) {
            if (response.success) {
                $.each(response.data, function (i, val) {
                    if (response.data.length) {
                        var edit_btn = (response.edit_permission ? "<span title='Edit export form' class='btn btn-default edit-btn btn-sm' item-id='" + val.export_forms_id + "'><span class='glyphicon pointer glyphicon-pencil'></span></span>" : "");
                        var del_btn = (response.edit_permission ? "<span title='Delete export form' class='btn btn-default del-btn btn-sm' item-id='" + val.export_forms_id + "'><span class='glyphicon pointer glyphicon-remove'></span></span>" : "");
                        $tbody
                            .append("<tr><td style='display: none'>"
                                + "<span class='export_forms_id' style='display: none'>" + (val.export_forms_id ? val.export_forms_id : '') + "</span>"
                                + "<span class='header' style='display: none'>" + (val.header ? val.header : '') + "</span>"
                                + "<span class='query' style='display: none'>" + (val.query ? val.query : '') + "</span>"
                                + "<span class='order_by' style='display: none'>" + (val.order_by ? val.order_by : '') + "</span>"
                                + "<span class='group_by' style='display: none'>" + (val.group_by ? val.group_by : '') + "</span>"
                                + "<span class='date_filter' style='display: none'>" + (val.date_filter ? val.date_filter : '') + "</span>"
                                + "<span class='campaign_filter' style='display: none'>" + (val.campaign_filter ? val.campaign_filter : '') + "</span>"
                                + "<span class='outcome_filter' style='display: none'>" + (val.outcome_filter ? val.outcome_filter : '') + "</span>"
                                + "<span class='source_filter' style='display: none'>" + (val.source_filter ? val.source_filter : '') + "</span>"
                                + "<span class='pot_filter' style='display: none'>" + (val.pot_filter ? val.pot_filter : '') + "</span>"
                                + "<span class='team_filter' style='display: none'>" + (val.team_filter ? val.team_filter : '') + "</span>"
                                + "<span class='agent_filter' style='display: none'>" + (val.agent_filter ? val.agent_filter : '') + "</span>"
                                + "<span class='user_filter' style='display: none'>" + (val.user_filter ? val.user_filter : '') + "</span>"
                                + "</td><td class='name'>"
                                + val.name
                                + "</td><td class='description'>"
                                + val.description
                                + "</td><td class='report-export-prog-" + val.export_forms_id + "'>"
                                + "</td><td style='text-align: right' width='20%'>" +
                                    "<div class='btn-group'>" +
                                    "<span title='Export to csv' class='btn btn-default xs-btn export-file-btn btn-sm' item-id='" + val.export_forms_id + "'><span class='glyphicon glyphicon-export pointer'></span></span>" +
                                    "<span title='View the data before export' class='btn btn-default export-report-btn btn-sm' item-id='" + val.export_forms_id + "' item-name='" + val.name + "'><span class='glyphicon glyphicon-eye-open pointer'></span></span>" +
                                    edit_btn +
                                    del_btn +
                                    "</div>"
                                + "</td></tr>");
                    }
                });
            }
            else {
                $tbody
                    .append("<tr><td>" + response.data + "</td></tr>");
            }
        });
    },
    export_file: function (export_forms_id) {
        $('.filter-form').find('input[name="export_forms_id"]').val(export_forms_id);
        $('.filter-form').attr('action',helper.baseUrl + "exports/data_export");
        $('.filter-form').trigger('submit');

        return true;
    },
    export_available_file: function (export_form_name) {

        var err = false;
        var msg = "";
        if ($.inArray( export_form_name, [ 'contacts-data', 'combo-data', 'dials-data'] ) >= 0) {
            if ($('.campaign-filter  option:selected').size() <= 0) {
                err = true
                msg = "You should select at least one campaign on the filter";
            }
        }

        if (err) {
            flashalert.danger(msg);
        }
        else {
            $('.filter-form').find('input[name="export_form_name"]').val(export_form_name);
            $('.filter-form').attr('action',helper.baseUrl + "exports/data_available_export");
            $('.filter-form').trigger('submit');
        }


        return true;
    },
    new_custom_export: function() {

        var mheader = "New Custom Export";
        var mbody = ""
        var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>' +
            ' <button class="btn btn-primary pull-right marl" id="save-edit-btn">Save</button> ';

        $.ajax({
            url: helper.baseUrl + 'modals/load_export_form',
            type: "POST",
            dataType: "HTML"
        }).done(function (response) {
            mbody = $(response);

            $.ajax({
                url: helper.baseUrl + 'exports/get_export_users',
                type: "POST",
                dataType: "JSON",
                data: {'export_forms_id': null}
            }).done(function (response) {
                if (response.success) {
                    modals.load_modal(mheader, mbody, mfooter);
                    //modal_body.css('overflow','visible');
                    //Load users
                    $.each(response.users, function (k, v) {
                        $('#user_select').prepend('<option value="' + v.id + '">' + v.name + '</option>');
                    });
                    $('#user_select').selectpicker('refresh');
                }
            });
        });
    },
    edit_export_form: function (btn) {
        var mheader = "Edit Custom Report";
        var mbody = "";
        var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>' +
            ' <button class="btn btn-primary pull-right marl" id="save-edit-btn">Save</button> ';

        var row = btn.closest('tr');

        var export_forms_id = row.find('.export_forms_id').text();
        var name = row.find('.name').text();
        var description = row.find('.description').text();
        var header = row.find('.header').text();
        var query = row.find('.query').text();
        var group_by = row.find('.group_by').text();
        var order_by = row.find('.order_by').text();
        var date_filter = row.find('.date_filter').text();
        var campaign_filter = row.find('.campaign_filter').text();
        var outcome_filter = row.find('.outcome_filter').text();
        var source_filter = row.find('.source_filter').text();
        var pot_filter = row.find('.pot_filter').text();
        var team_filter = row.find('.team_filter').text();
        var agent_filter = row.find('.agent_filter').text();
        var user_filter = row.find('.user_filter').text();

        $.ajax({
            url: helper.baseUrl + 'modals/load_export_form',
            type: "POST",
            dataType: "HTML"
        }).done(function (response) {
            mbody = $(response);

            mbody.find('input[name="export_forms_id"]').val(export_forms_id);
            mbody.find('input[name="name"]').val(name);
            mbody.find('input[name="description"]').val(description);
            mbody.find('textarea[name="query"]').val(query);
            mbody.find('textarea[name="header"]').val(header);
            mbody.find('input[name="group_by"]').val(group_by);
            mbody.find('input[name="order_by"]').val(order_by);
            mbody.find('input[name="date_filter"]').val(date_filter);
            mbody.find('input[name="campaign_filter"]').val(campaign_filter);
            mbody.find('input[name="outcome_filter"]').val(outcome_filter);
            mbody.find('input[name="source_filter"]').val(source_filter);
            mbody.find('input[name="pot_filter"]').val(pot_filter);
            mbody.find('input[name="team_filter"]').val(team_filter);
            mbody.find('input[name="agent_filter"]').val(agent_filter);
            mbody.find('input[name="user_filter"]').val(user_filter);

            mbody.find('.preview-qry').html(query+(order_by.length>""?" ORDER BY "+order_by:"")+(group_by.length>""?" GROUP BY "+group_by:""));

            //Get the graphs
            $.ajax({
                url: helper.baseUrl + 'exports/get_export_graphs',
                type: "POST",
                dataType: "JSON",
                data: {'export_forms_id': export_forms_id}
            }).done(function (response) {
                if (response.success) {
                    mbody.find('#export-graph-list').empty();
                    var graphs = "<table class='table small'>";
                    graphs += "<thead><tr><th>Name</th><th>Type</th><th>X Axis</th><th>Y Axis</th><th>Z Axis</th><th></th></tr></thead><tbody>";
                    $.each(response.graphs, function (k, v) {
                        var type_graph = "";
                        switch (v.type){
                            case "bars":
                                type_graph = ' <span class="fa fa-bar-chart"></span>';
                                break;
                            case "pie":
                                type_graph = ' <span class="fa fa-pie-chart"></span>';
                                break;
                            case "line":
                                type_graph = ' <span class="fa fa-line-chart"></span>';
                                break;
                            case "area":
                                type_graph = ' <span class="fa fa-area-chart"></span>';
                                break;
                            default:
                                break;
                        }
                        graphs += "<tr>" +
                                    "<td>"+v.name+"</td>" +
                                    "<td>"+v.type+type_graph+"</td>" +
                                    "<td>"+(v.x_value?v.x_value:'')+"</td>" +
                                    "<td>"+(v.y_value?v.y_value:'')+"</td>" +
                                    "<td>"+(v.z_value?v.z_value:'')+"</td>" +
                                    "<td><span class='btn btn-danger btn-xs pull-right pointer remove-export-graph-btn' item-id='"+ v.graph_id+"'><span class='fa fa-remove'></span> Remove</span></td>" +
                                  "</tr>";
                    });
                    graphs += "</tbody></table>";

                    mbody.find('#export-graph-list').html(graphs);
                }
                //Get the export users
                $.ajax({
                    url: helper.baseUrl + 'exports/get_export_users',
                    type: "POST",
                    dataType: "JSON",
                    data: {'export_forms_id': export_forms_id}
                }).done(function (response) {
                    if (response.success) {
                        modals.load_modal(mheader, mbody, mfooter);
                        //modal_body.css('overflow','visible');
                        //Load users
                        $.each(response.users, function (k, v) {
                            var selected = "";
                            if (inArray(v.id, response.data) && response.data) {
                                selected = "selected";
                            }
                            $('#user_select').prepend('<option ' + selected + ' value="' + v.id + '">' + v.name + '</option>');
                        });
                        $('#user_select').selectpicker('refresh');
                    }
                    else {
                        mbody = "<div>Error loading the custom export. Please contact with the administrator</div>";
                        mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>';
                        modals.load_modal(mheader, mbody, mfooter);
                    }

                }).fail(function () {
                    mbody = "<div>Error loading the custom export. Please contact with the administrator</div>";
                    mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>';

                    modals.load_modal(mheader, mbody, mfooter);
                });
            });
        });

    },
    save_export_form: function () {
        $.ajax({
            url: helper.baseUrl + 'exports/save_export_form',
            type: "POST",
            dataType: "JSON",
            data: $('.edit-export-form').serialize()
        }).done(function (response) {
            if (response.success) {
                //Reload exports table
                export_data.load_export_forms();
                //Close edit form
                //modal_body.css('overflow','auto');
                $('.close-modal').trigger('click');
                flashalert.success(response.msg);
            }
            else {
                flashalert.danger(response.msg);
            }
        });
    },

    delete_export_form: function (export_forms_id) {
        var mheader = "Confirm Delete";
        var mbody = "<span>Are you sure you want to delete this export form?</span>";
        var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>' +
            ' <button class="btn btn-primary pull-right marl" id="del-confirm-btn" item-id="'+export_forms_id+'">Delete</button> ';

        modals.load_modal(mheader, mbody, mfooter);
    },

    delete_export: function (export_forms_id) {

        $.ajax({
            url: helper.baseUrl + 'exports/delete_export_form',
            type: "POST",
            dataType: "JSON",
            data: {'export_forms_id': export_forms_id}
        }).done(function (response) {
            if (response.success) {
                //Reload exports table
                export_data.load_export_forms();

                $('.close-modal').trigger('click');
                flashalert.success(response.msg);
            }
            else {
                flashalert.danger(response.msg);
            }
        });
    },

    load_export_report_data: function (export_forms_id, name) {

        $('.report-export-prog-' + export_forms_id).html("<img src='" + helper.baseUrl + "assets/img/ajax-loader-bar.gif' />");

        $('.filter-form').find('input[name="export_forms_id"]').val(export_forms_id);

        var mheader = name;
        var mbody = "";
        var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>' +
            ' <span class="btn btn-primary pull-right marl modal-export-file-btn" item-id="'+export_forms_id+'">Export</span> ';

        $.ajax({
            url: helper.baseUrl + 'exports/load_export_report_data',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize()
        }).done(function (response) {
            mbody += '<ul class="nav nav-tabs" id="panel-tabs-'+export_forms_id+'" style=" background:#eee; width:100%;">';
            mbody += '<li class="data-tab active"><a href="#export-data-'+export_forms_id+'" class="tab" data-toggle="tab">Data</a></li>' +
                     '<li class="plots-tab"><a href="#export-graph-'+export_forms_id+'" class="tab" data-toggle="tab">Graphs</a></li>';
            mbody += '</ul>';
            mbody += '<div class="tab-content" style="padding: 0px;">';
            if (response.success && response.header) {
                //Data tab
                mbody += '<div class="tab-pane active" id="export-data-'+export_forms_id+'"  style="padding: 0px;">';
                mbody += "<div class='table-"+export_forms_id+" scroll'><table id='table-"+export_forms_id+"' class='table table-bordered table-hover table-striped small' style='min-height: 400px;'>";
                mbody += "<thead><tr>";
                $.each(response.header, function (i, val) {
                    if (response.header.length) {
                        mbody += "<th style='padding: 5px;'>" + val + "</th>";
                    }
                });
                mbody += "</tr></thead><tbody>";
                $.each(response.data, function (i, data) {
                    if (response.data.length) {
                        mbody += "<tr>";
                        $.each(data, function (k, val) {
                            mbody += "<td style='padding: 5px;'>" + val + "</td>";
                        });
                        mbody += "</tr>";
                    }
                });
                mbody += "</tbody></table></div></div>";

                //Graph tab
                mbody += '<div class="tab-pane" id="export-graph-'+export_forms_id+'"  style="padding: 0px; overflow-y: auto; overflow-x: hidden; max-height: 400px;">';
                mbody += '<div class="row">';
                if (response.graphs.length) {
                    $.each(response.graphs, function (i, graph) {
                        mbody += '<div class="col-lg-6"><div id="export-chart-'+graph.graph_id+'" style="text-shadow: none">' +
                                    '<p>'+graph.name+'</p>' +
                                    '<img src="' + helper.baseUrl + 'assets/img/ajax-loader-bar.gif" /> ' +
                                 '</div></div>';
                    });

                    //LOAD google graphs
                    google.load('visualization', '1', {
                        packages: ['corechart'], 'callback': function () {
                            $.each(response.graphs, function (i, graph) {
                                var rows = [];
                                var title = graph.name;

                                // Set chart options
                                var options = {
                                    'legend': {position: 'none'},
                                    'title': title,
                                    'width': 250,
                                    'height': 400,
                                    curveType: 'function',
                                    'hAxis': {direction:-1, slantedText:true, slantedTextAngle:45 },
                                    isStacked: (graph.z_value)?true:false
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
                                switch (graph.type){
                                    case "bars":
                                        var chart = new google.visualization.ColumnChart(document.getElementById('export-chart-'+graph.graph_id));
                                        chart.draw(data, options);
                                        break;
                                    case "pie":
                                        var chart = new google.visualization.PieChart(document.getElementById('export-chart-'+graph.graph_id));
                                        chart.draw(data, options);
                                        break;
                                    case "line":
                                        var chart = new google.visualization.LineChart(document.getElementById('export-chart-'+graph.graph_id));
                                        chart.draw(data, options);
                                        break;
                                    case "area":
                                        var chart = new google.visualization.AreaChart(document.getElementById('export-chart-'+graph.graph_id));
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
                    mbody += "<div class='col-lg-12' style='margin: 20px;'>No Graphs Created!</div>"
                }
                mbody += "</div></div>";

                //Open modal
                export_data.show_export_report(export_forms_id, mheader, mbody, mfooter);
            }
            else {
                mbody += "<div style='padding: 20px;'>" + response.data + "</div>";
                export_data.show_export_report(export_forms_id, mheader, mbody, mfooter);
                $(".modal-export-file-btn").attr('disabled', true);
            }

        }).fail(function () {
            mbody += "<div style='padding: 20px;'>There is something wrong with export</div>";
            export_data.show_export_report(export_forms_id, mheader, mbody, mfooter);
            $(".modal-export-file-btn").attr('disabled', true);

        });
    },

    load_available_export_report_data: function (export_form_name) {

        $('.report-available-export-prog-' + export_form_name).html("<img src='" + helper.baseUrl + "assets/img/ajax-loader-bar.gif' />");

        $('.filter-form').find('input[name="export_form_name"]').val(export_form_name);

        var mheader = export_form_name;
        var mbody = "";
        var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>' +
            ' <span class="btn btn-primary pull-right marl modal-export-available-file-btn" item-name="'+export_form_name+'">Export</span> ';

        $.ajax({
            url: helper.baseUrl + 'exports/load_available_export_report_data',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize()
        }).done(function (response) {
            if (response.success && response.header) {
                mbody += "<div class='table-"+export_form_name+" scroll'><table class='table table-bordered table-hover table-striped small' style='min-height: 400px;'>";
                mbody += "<thead><tr>";
                $.each(response.header, function (i, val) {
                    if (response.header.length) {
                        mbody += "<th style='padding: 5px;'>" + val + "</th>";
                    }
                });
                mbody += "</tr></thead><tbody>";
                $.each(response.data, function (i, data) {
                    if (response.data.length) {
                        mbody += "<tr>";
                        $.each(data, function (k, val) {
                            mbody += "<td style='padding: 5px;'>" + val + "</td>";
                        });
                        mbody += "</tr>";
                    }
                });
                mbody += "</tbody></table></div>";
                export_data.show_export_report(export_form_name, mheader, mbody, mfooter);
            }
            else {
                mbody += "<div style='padding: 20px;'>" + response.msg + "</div>";
                export_data.show_export_report(export_form_name, mheader, mbody, mfooter);
                $(".modal-export-available-file-btn").attr('disabled', true);
            }

        }).fail(function () {
            mbody += "<div style='padding: 20px;'> No Data </div>";
            export_data.show_export_report(export_form_name, mheader, mbody, mfooter);
            $(".modal-export-available-file-btn").attr('disabled', true);

        });
    },

    show_export_report: function (export_forms_id, mheader, mbody, mfooter) {

        modals.load_modal(mheader, mbody, mfooter);

        $('.modal-body').css('padding', '0px');
        $('.modal-body').css('max-height', '600px');

        $('#modal .table-'+export_forms_id).find('table').on('scroll', function () {
            $('#modal .table-'+export_forms_id).find("table > *").width($('#modal .table-'+export_forms_id).find('table').width() + $('#modal .table-'+export_forms_id).find('table').scrollLeft());
        });

        var dom_size = 6;
        $('#table-'+export_forms_id).DataTable({
            "dom": 'rt<"bottom-'+export_forms_id+' small"<"col-lg-'+dom_size+'"l><"col-lg-'+dom_size+'"f><"col-lg-'+dom_size+'"i><"col-lg-'+dom_size+'"p>><"clear">',
            "pagingType": "full"
        });
        $(".bottom-"+export_forms_id).css("min-height", "100px");
        if (dom_size == 12) {
            $(".bottom-"+export_forms_id).css("text-align", "right");
        }

        $('.report-available-export-prog-' + export_forms_id).html("");
        $('.report-export-prog-' + export_forms_id).html("");

    },
    close_export_report: function () {

        var export_forms_id = $('.filter-form').find('input[name="export_forms_id"]').val();
        $('.report-export-prog-' + export_forms_id).html("");

        $('.modal-backdrop.export-report').fadeOut();
        $('.export-report-container').fadeOut(500, function () {
            $('.export-report-content').show();
            $('.alert').addClass('hidden');
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

    get_pots_filter: function () {
        $.ajax({
            url: helper.baseUrl + 'reports/get_pots_filter',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize()
        }).done(function (response) {
            if (response.success) {
                var options = "";
                $.each(response.campaign_pots, function (i, val) {
                    options += "<option value=" + val.id + ">" + val.name + "</option>";
                });
                $('#pot-filter').html(options).selectpicker('refresh');
            }
        });
    },

    save_export_graph: function () {
        $.ajax({
            url: helper.baseUrl + 'exports/save_export_graph',
            type: "POST",
            dataType: "JSON",
            data: $('.edit-export-form').serialize()
        }).done(function (response) {
            if (response.success) {
                var export_forms_id = $('.edit-export-form').find('input[name="export_forms_id"]').val();
                $('.edit-export-form').find('input[name="graph_name"]').val('');
                $('.edit-export-form').find('input[name="x_value"]').val('');
                $('.edit-export-form').find('input[name="y_value"]').val('');
                $('.edit-export-form').find('input[name="z_value"]').val('');

                export_data.load_export_graph(export_forms_id);
                flashalert.success(response.msg);
            }
            else {
                flashalert.danger(response.msg);
            }
        });
    },

    delete_export_graph: function (graph_id) {

        $.ajax({
            url: helper.baseUrl + 'exports/delete_export_graph',
            type: "POST",
            dataType: "JSON",
            data: {'graph_id': graph_id}
        }).done(function (response) {
            if (response.success) {
                var export_forms_id = $('.edit-export-form').find('input[name="export_forms_id"]').val();
                export_data.load_export_graph(export_forms_id);
                flashalert.success(response.msg);
            }
            else {
                flashalert.danger(response.msg);
            }
        });
    },

    load_export_graph: function(export_forms_id) {
        $.ajax({
            url: helper.baseUrl + 'exports/get_export_graphs',
            type: "POST",
            dataType: "JSON",
            data: {'export_forms_id': export_forms_id}
        }).done(function (response) {
            $('#export-graph-list').empty();
            if (response.success) {
                var graphs = "<table class='table small'>";
                graphs += "<thead><tr><th>Name</th><th>Type</th><th>X Axis</th><th>Y Axis</th><th>Z Axis</th><th></th></tr></thead><tbody>";
                $.each(response.graphs, function (k, v) {
                    var type_graph = "";
                    switch (v.type){
                        case "bars":
                            type_graph = ' <span class="fa fa-bar-chart"></span>';
                            break;
                        case "pie":
                            type_graph = ' <span class="fa fa-pie-chart"></span>';
                            break;
                        case "line":
                            type_graph = ' <span class="fa fa-line-chart"></span>';
                            break;
                        case "area":
                            type_graph = ' <span class="fa fa-area-chart"></span>';
                            break;
                        default:
                            break;
                    }
                    graphs += "<tr>" +
                        "<td>"+v.name+"</td>" +
                        "<td>"+v.type+type_graph+"</td>" +
                        "<td>"+(v.x_value?v.x_value:'')+"</td>" +
                        "<td>"+(v.y_value?v.y_value:'')+"</td>" +
                        "<td>"+(v.z_value?v.z_value:'')+"</td>" +
                        "<td><span class='btn btn-danger btn-xs pull-right pointer remove-export-graph-btn' item-id='"+ v.graph_id+"'><span class='fa fa-remove'></span> Remove</span></td>" +
                        "</tr>";
                });
                graphs += "</tbody></table>";

                $('#export-graph-list').html(graphs);
            }
            else {
                $('#export-graph-list').html('<div class="col-lg-12" id="export-graph-list">No graphs added</div>');
            }
        });
    }

}