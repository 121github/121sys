/*the class below is for the data import page. It gets initialized by the data.php view*/
var importer = {
    init: function () {
        $(document).on('change', '#source', function (e) {
            importer.check_source($(this));
        });

        $(document).on('change', '#campaign', function () {
            importer.show_campaign_type();
        });

        $(document).on('click', '.goto-step-2', function (e) {
            e.preventDefault();
            importer.load_step_2();
        });
        $(document).on("click", ".goto-step-3", function (e) {
            e.preventDefault();
            importer.load_step_3();
        });
        $(document).on("click", ".goto-step-1", function (e) {
            e.preventDefault();
            importer.load_step_1();
        });
        $(document).on("click", "#import", function (e) {
            e.preventDefault();
            importer.check_import();
        });

        //initialize the upload widget
        $('#fileupload').fileupload({
            maxNumberOfFiles: 1,
            dataType: 'json',
            acceptFileTypes: /(\.|\/)(csv)$/i,
            progressall: function (e, data) {
                var progress = parseInt(data.loaded / data.total * 100, 10);
                $('#progress .progress-bar').css(
                    'width',
                    progress + '%'
                );
            },
            always: function (e, data) {
                $('#files').find('#file-status').text("File uploaded").removeClass('text-danger').addClass('text-success');
                $('.goto-step-3').show();
            }
        }).on('fileuploaddone', function (e, data) {
            var file = data.result.files[0];
            $('#files').find('#filename').text(file.name);
            $('#files').find('#file-status').text('');
        }).on('fileuploadprocessalways', function (e, data) {
            var file = data.files[0];
            if (file.error) {
                $('#files').find('#file-status').text(file.error).removeClass('text-success').addClass('text-danger');
            }
        }).prop('disabled', !$.support.fileInput)
            .parent().addClass($.support.fileInput ? undefined : 'disabled');


    },
    check_import: function () {
        var ctype = $('#campaign option:selected').attr('ctype');
        var error, urn, coname, tel, cotel, name;
        var fields = new Array();
        $('.import-field').each(function () {
            if ($.inArray($(this).val(), fields) != -1) {
                var error = "Field <b>" + $(this).val() + "</b> was selected more than once";
            }

            fields.push($(this).val());
            if ($(this).val() == "urn") {
                urn = true;
            }
            if ($(this).val() == "name") {
                coname = true;
            }
            if ($(this).val() == "fullname" || $(this).val() == "firstname" || $(this).val() == "lastname") {
                name = true;
            }
            if ($(this).val() == "telephone_Tel") {
                cotel = true;
            }
            if ($(this).val() == "telephone_Landline" ||
                $(this).val() == "telephone_Mobile" ||
                $(this).val() == "telephone_Work" ||
                $(this).val() == "telephone_Telephone") {
                tel = true;
            }
        });
        if (ctype == "B2B" && !coname) {
            var error = "You cannot add data to a B2B campaign without a company name";
        }
        if (ctype == "B2C" && !name) {
            var error = "You cannot add data to a B2C campaign without a contact name";
        }
        if (ctype == "B2C" && !tel) {
            var error = "You must add at least one type of contact telephone number for B2C";
        }
        if (ctype == "B2B") {
            if (!cotel) {
                if (!tel && !name) {
                    var error = "You must add a company telephone number or a contact for a B2B campaign";
                }
            }
        }
        if (urn && $('#urn-options').val() == 1) {
            var error = "You cannot use auto increment if you have selected a URN column";
        }
        if ($('#urn-options').val() == 2 && !urn) {
            var error = "You have not selected a URN column. You should use auto increment.";
        }
        if ($('#dupe-options').val() == 2 && !urn || $('#dupe-options').val() == 3 && !urn) {
            var error = "Overwriting and updating records is only possible with a URN column";
        }


        if (error) {
            flashalert.danger(error);
        } else {
            importer.start_import();
        }

    },
    show_progress: function (first) {
        $.ajax({
            url: helper.baseUrl + 'data/get_progress',
            type: "POST",
            dataType: "JSON",
            data: {first: first}
        }).done(function (response) {
            if (!response.success && $('#import').text() != "Stop") {
                $('#import-progress').text(response.progress);
                importer.show_progress();
            } else {
                $('#import-progress').html("<span class='green'>Import was completed</span>");
                flashalert.success("Import was completed");
            }
        });
    },
    start_import: function () {
        $('#import-progress').text("Preparing data...");
        importer.show_progress(1);
        $.ajax({
            url: helper.baseUrl + 'data/start_import',
            type: "POST",
            dataType: "JSON",
            data: $('#data-form').serialize() + '&filename=' + encodeURIComponent($('#filename').text()) + '&campaign=' + $('#campaign').val() + '&source=' + $('#source').val() + '&type=' + $('#campaign option:selected').attr('ctype')
        }).done(function () {
        });
    },
    show_campaign_type: function () {
        var ctype = $('#campaign option:selected').attr('ctype');
        $('#ctype-text').text("This is a " + ctype + " campaign.").show();
    },
    check_source: function ($btn) {
        if ($btn.val() == "other") {
            $btn.closest('.form-group').find('input[type="text"]').show()
        } else {
            $btn.closest('.form-group').find('input[type="text"]').val('').hide()
        }
    },
    load_step_1: function () {
        $('#step-2').slideUp(1000, function () {
            $('#step-1').slideDown(1000);
        });
    },
    load_step_2: function ($btn) {
        var incomplete;
        if ($('#campaign').val() == "") {
            var incomplete = "Please select a campaign";
        } else if ($('#source').val() == "") {
            var incomplete = "Please select a data source";
        } else if ($('#source').val() == "other" && $('#new_source').val() == "") {
            var incomplete = "Please enter the new source name";
        }

        if (incomplete) {
            flashalert.danger(incomplete)
        } else {
            $('#step-1,#step-3').slideUp(1000, function () {
                $('#step-2').slideDown(1000);
            });
            $('#campaign-name-title').text($('#campaign option:selected').text());
            $('#campaign-type-title').text($('#campaign option:selected').attr('ctype'));
        }

    },
    load_step_3: function () {
        $('#step-2').slideUp(1000, function () {
            $.ajax({
                url: helper.baseUrl + 'data/get_sample',
                type: "POST",
                dataType: "JSON",
                data: {
                    file: $('#filename').text()
                }
            }).fail(function () {
                $('#step-3').find('#sample-table thead').empty().append("<tr><th>Error</th></tr>");
                $('#step-3').find('#sample-table tbody').empty().append("<tr><td>The was a problem with the supplied CSV file. Please open it in excel and save as a comma seperated file and try again.</td></tr>");
                $('#step-3').slideDown(1000);
            }).done(function (response) {
                $('#import-progress').html('&nbsp;');
                var thead, tbody;
                $.each(response, function (i, row) {
                    tbody += "<tr>"
                    thead = "<tr>"
                    $.each(row, function (key, val) {
                        thead += "<th></th>";
                        tbody += "<td>" + val + "</td>";
                    });
                    tbody += "</tr>"
                    thead += "</tr>"
                });
                $('#step-3').find('#sample-table thead').empty().append(thead);
                $('#step-3').find('#sample-table tbody').empty().append(tbody);
                $('#step-3').slideDown(1000);
                $('.goto-step-3').show();
                importer.create_field_options();
            });
        });
    },

    create_field_options: function () {
        var ctype = $('#campaign option:selected').attr('ctype');
        var camp = $('#campaign').val();
        $.ajax({
            url: helper.baseUrl + 'data/import_fields',
            type: "POST",
            dataType: "JSON",
            data: {
                type: ctype,
                campaign: camp
            }
        }).done(function (response) {
            var $select = "<select class='import-field'><option value=''>Select field</option>";
            $.each(response, function (table, fields) {
                $select += "<optgroup label='" + table + "'>";

                $.each(fields, function (column, name) {
                    $select += "<option value='" + column + "'>" + name + "</option>";
                });
                $select += "</optgroup>";
            });
            $select += "</select>";
            $('#step-3').find('#sample-table th').each(function (i) {
                $(this).append($select);
                $(this).find('select').attr('name', 'field[' + i + ']');
            });
        });
    }
}

/*the class below is for the data management page. It gets initialized by the data_management.php view*/
var data_manager = {
    init: function () {
        $(document).on('change', '#campaign', function (e) {
            if ($('#state-select').val() != "") {
                data_manager.get_user_data();
            }
        });
        $(document).on('change', '#state-select', function (e) {
            if ($(this).val() != "") {
                data_manager.get_user_data();
            }
        });

        $(document).on('click', '#reassign-btn', function (e) {
            e.preventDefault();
            data_manager.reassign_data();
        });
        data_manager.load_sliders();
    },
    get_user_data: function () {
        $.ajax({
            url: helper.baseUrl + 'data/get_user_data',
            type: "POST",
            dataType: "JSON",
            data: {
                campaign: $('#campaign').val(),
                state: $('#state-select').val()
            }
        }).done(function (response) {
            $('#data-stats').show();
            $('#total-records').text(response.total);
            $('#assigned-records').text(response.assigned);
            $('#unassigned-records').text(response.unassigned);
            $('#parked-records').text(response.parked);
            if (response.total > 0) {
                data_manager.load_html(response);
            } else {
                $('#sliders').text("No data could be found");
                $('#reassign-btn').hide();
            }
        });

    },
    load_html: function (response) {
        $('#sliders').empty();
        if (response.n > 0) {
            $.each(response.data, function (k, row) {
                $slider = '<li><input type="text" name="user[' + k + ']" value="' + row.count + '" /><div class="slider" value="' + row.pc + '"/>' + row.name + ': <span class="value"></span>% <span class="pull-right">' + row.count + ' records</span></li>';
                $('#sliders').append($slider);
            });
            $('#reassign-btn').show();
            data_manager.load_sliders(response.n, response.total);
        } else {
            $('#sliders').append("Can not continue. There must be at least 2 users assigned to a campaign for the reassignment tool to work.");
            $('#reassign-btn').hide();
        }

    },
    reassign_data: function () {
        $.ajax({
            url: helper.baseUrl + 'data/reassign_data',
            type: "POST",
            dataType: "JSON",
            data: $('#data-form').serialize()
        }).done(function (response) {
            if (response.success) {
                data_manager.get_user_data();
                flashalert.success("Records were reassigned");
            }
        });
    },
    load_sliders: function (n, total_records) {
        var sliders = $("#sliders .slider");
        var availableTotal = 100;

        sliders.each(function () {
            var init_value = parseInt($(this).attr('value'));
            $(this).siblings('.value').text($(this).attr('value'));
            $(this).slider({
                value: init_value,
                min: 0,
                max: 100,
                range: "max",
                tooltip: "hide",
                step: 2,
                animate: 0
            }).on('slide', function (event) {
                // Get current total
                var gval = event.value;
                $(this).closest('.slider-horizontal').siblings('.value').text(gval);
                $(this).closest('li').find('input').val(Math.round(total_records * (gval / 100)));
                var total = 0;
                sliders.not(this).each(function () {
                    total += Number($(this).val());
                    $(this).closest('.slider-horizontal').siblings('.value').text($(this).val());
                });
                total += gval;
                var delta = availableTotal - total;

                // Update each slider
                sliders.not(this).each(function () {
                    var t = $(this),
                        value = t.val();

                    var num = (Number(n) - 1);
                    var new_value = Math.floor(value + (delta / num));


                    if (new_value < 0 || gval == 100)
                        new_value = 0;
                    if (new_value > 100)
                        new_value = 100;

                    t.closest('li').find('input').val(Math.round(total_records * (new_value / 100)));
                    t.closest('.slider-horizontal').siblings('.value').text(new_value);
                    t.slider('setValue', Number(new_value));
                });

            });
        });
    }
}

/*the class below is for the daily ration page. It gets initialized by the daily_ration.php view*/
var daily_ration = {
    init: function () {
        $(document).on("click", ".campaign-filter", function(e) {
            e.preventDefault();
            $icon = $(this).closest('ul').prev('button').find('span');
            $(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('form').find('input[name="campaign"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            daily_ration.daily_ration_panel();
        });

        daily_ration.daily_ration_panel();
    },
    daily_ration_panel: function() {
        var url = helper.baseUrl + 'search/custom/records/';
        $.ajax({
            url: helper.baseUrl + 'data/daily_ration_data',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize()
        }).done(function(response) {
            var $row = "";
            $thead = $('.daily_ration_data .ajax-table').find('thead');
            $thead.empty();
            $tbody = $('.daily_ration_data .ajax-table').find('tbody');
            $tbody.empty();
            if (response.success) {
                var $parked_reasons = "";
                $.each(response.parked_codes, function(k, parked_code) {
                    $parked_reasons += "<th>"+parked_code.name+"</th>";
                });

                $thead.append("<tr>" +
                    "<th>Campaign</th>" +
                    "<th>Total Records</th>" +
                    $parked_reasons +
                    "<th>Total Parked Records</th>" +
                    "<th>Daily Data</th>" +
                    "</tr>");

                $.each(response.data, function(i, val) {
                    if (val.campaign_name) {

                        var $parked_codes = "";
                        $.each(response.parked_codes, function(k, parked_code) {
                            if (val[parked_code.id]) {
                                $parked_codes += "</td><td class='" + parked_code.name + "'>"
                                + "<a href='" + val[parked_code.id].url + "'>" + val[parked_code.id].count + "</a>";
                            }
                            else {
                                $parked_codes += "</td><td class='" + parked_code.name + "'>"
                                + "<a href='"+url+"campaign/"+i+"/parked/yes/parked-code/"+parked_code.id+"'>0</a>";
                            }
                        });

                        $tbody
                            .append("<tr><td class='campaign'>"
                            + val.campaign_name
                            + "</td><td class='total_records'>"
                            + 	"<a href='" + val.total_records_url + "'>" + val.total_records + "</a>"
                            + $parked_codes
                            + "</td><td class='total_parked'>"
                            + 	"<a href='" + val.total_parked_url + "'>" + val.total_parked + "</a>"
                            + "</td><td class='daily_data'>"
                            + "<input class='' value='" +	val.daily_data + "' name='" + val.campaign_id + "' onblur='daily_ration.set_daily_data("+val.campaign_id+")'>"
                            + "</td></tr>");
                    }
                    $('form').find('input[name="'+ val.campaign_id +'"]').numeric();
                });
            } else {
                $tbody
                    .append("<tr><td colspan='6'>"
                    + response.msg
                    + "</td></tr>");
            }
        });
    },
    set_daily_data: function(campaign_id) {
        var daily_data = $('form').find('input[name="'+ campaign_id +'"]').val();
        $.ajax({
            url: helper.baseUrl + 'data/set_daily_ration',
            type: "POST",
            dataType: "JSON",
            data: {'campaign_id' : campaign_id, 'daily_data':daily_data}
        }).done(function(response) {
            if (response.success) {
                daily_ration.daily_ration_panel();
                flashalert.success(response.msg);
            }
            else {
                flashalert.danger(response.msg);
            }
        });
    }
}

/*the class below is for the backup_restore page. It gets initialized by the backup_restore.php view*/
var backup_restore = {
    init: function () {
        $(document).on("click", ".backup-campaign-filter", function(e) {
            e.preventDefault();
            $icon = $(this).closest('ul').prev('button').find('span');
            $(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('form').find('input[name="campaign"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            backup_restore.backup_panel();
        });
        $(document).on("click", ".backup-history-filter-campaign", function(e) {
            e.preventDefault();
            $icon = $(this).closest('ul').prev('button').find('span');
            $(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('form').find('input[name="campaign"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            backup_restore.backup_history_panel();
        });

        $(document).on("click", ".backup-history-filter-restored", function(e) {
            e.preventDefault();
            $icon = $(this).closest('ul').prev('button').find('span');
            $(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('form').find('input[name="restored"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
            backup_restore.backup_history_panel();
        });

        $(document).on("click", ".btn-new-backup", function (e) {
            e.preventDefault();
            backup_restore.new_backup($(this));
        });

        $(document).on("click", ".continue-backup", function (e) {
            e.preventDefault();
            backup_restore.save_backup($(this));
        });

        $(document).on('click', '.close-backup', function(e) {
            e.preventDefault();
            backup_restore.close_backup($(this));
        });

        $(document).on('click', '.clear-input', function(e) {
            e.preventDefault();
            backup_restore.update_records($(this));
        });

        $(document).on("click", ".btn-restore-backup", function (e) {
            e.preventDefault();
            modal.restore_campaign_backup($(this));
        });

        $('.backup-container').hide();
        backup_restore.backup_panel();
        backup_restore.backup_history_panel();
    },
    backup_panel: function() {
        $.ajax({
            url: helper.baseUrl + 'data/backup_data',
            type: "POST",
            dataType: "JSON",
            data: $('.backup-filter-form').serialize()
        }).done(function(response) {
            var body = "";
            if (response.success) {
                var $tbody = $('.backup_data .ajax-table').find('tbody');
                $tbody.empty();
                $.each(response.data, function(i, val) {
                    var update_date_from = (val.update_date_from?val.update_date_from:"");
                    var update_date_to = (val.update_date_to?val.update_date_to:"");
                    var renewal_date_from = (val.renewal_date_field!=""?(val.renewal_date_from?val.renewal_date_from:""):"");
                    var renewal_date_to = (val.renewal_date_field!=""?(val.renewal_date_to?val.renewal_date_to:""):"");
                    if (val.campaign_name) {
                        $.ajax({
                            url: helper.baseUrl + 'data/backup_data_by_campaign',
                            type: "POST",
                            dataType: "JSON",
                            data: {
                                    'campaign_id': val.campaign_id,
                                    'update_date_from': update_date_from,
                                    'update_date_to': update_date_to,
                                    'renewal_date_from': renewal_date_from,
                                    'renewal_date_to': renewal_date_to,
                                    'renewal_date_field': (val.renewal_date_field!= ""?val.renewal_date_field:null)
                            }
                        }).done(function(response) {
                            if (response.success) {
                                    var disabled = (val.renewal_date_field=="")?"disabled title='Renewal date field is not defined' style='color:red'":"";
                                    $tbody.append(
                                        "<tr><td class='campaign'>"
                                        + val.campaign_name
                                                + "<span style='display:none' class='campaign_id'>"+val.campaign_id+"</span>"
                                                + "<span style='display:none' class='renewal_date_field'>"+val.renewal_date_field+"</span>"
                                        + "</td><td class='update_date_from'>"
                                        + "<div class='input-group'>"
                                        +       "<input data-date-format='DD/MM/YYYY' value='"+update_date_from+"' name='update_date_from_"+val.campaign_id+"' type='text' class='form-control date' onblur='backup_restore.update_records($(this))'>"
                                        +       "<span class='input-group-btn'>"
                                        +           "<button class='btn btn-default clear-input' type='button'>X</button>"
                                        +       "</span>"
                                        + "</div>"
                                        + "</td><td class='update_date_to'>"
                                        + "<div class='input-group'>"
                                        +       "<input data-date-format='DD/MM/YYYY' value='"+update_date_to+"' name='update_date_to_"+val.campaign_id+"' type='text' class='form-control date' onblur='backup_restore.update_records($(this))'>"
                                        +       "<span class='input-group-btn'>"
                                        +           "<button class='btn btn-default clear-input' type='button'>X</button>"
                                        +       "</span>"
                                        + "</div>"
                                        + "</td><td class='renewal_date_from'>"
                                        + "<div class='input-group'>"
                                        +       "<input data-date-format='DD/MM/YYYY' value='"+renewal_date_from+"' name='renewal_date_from_"+val.campaign_id+"' type='text' class='form-control date' onblur='backup_restore.update_records($(this))' "+disabled+">"
                                        +       "<span class='input-group-btn'>"
                                        +           "<button class='btn btn-default clear-input' type='button' "+disabled+">X</button>"
                                        +       "</span>"
                                        + "</div>"
                                        + "</td><td class='renewal_date_to'>"
                                        + "<div class='input-group'>"
                                        +       "<input data-date-format='DD/MM/YYYY' value='"+renewal_date_to+"' name='renewal_date_to_"+val.campaign_id+"' type='text' class='form-control date' onblur='backup_restore.update_records($(this))' "+disabled+">"
                                        +       "<span class='input-group-btn'>"
                                        +           "<button class='btn btn-default clear-input' type='button' "+disabled+">X</button>"
                                        +       "</span>"
                                        + "</div>"
                                        + "</td><td class='records_num'>"
                                        + "<a class='records_url' href='" + response.records_url + "'><span class='records_val'>" + response.records_num + "</span></a>"
                                        + "</td><td class=''>"
                                        + "<span class='glyphicon glyphicon-save btn-new-backup pointer'></span>"
                                        + "</td></tr>");
                                $('.date').datetimepicker({
                                    pickTime: false,
                                    maxDate: new Date()
                                });
                            }
                        });
                    }
                });
            } else {
                $tbody
                    .append("<tr><td colspan='6'>"
                    + response.msg
                    + "</td></tr>");
            }
        });
    },
    backup_history_panel: function() {
        $.ajax({
            url: helper.baseUrl + 'data/backup_history_data',
            type: "POST",
            dataType: "JSON",
            data: $('.backup-history-filter-form').serialize()
        }).done(function(response) {
            var $tbody = $('.backup_history_data .ajax-table').find('tbody');
            $tbody.empty();
            if (response.success) {
                $.each(response.data, function(i, val) {
                        if (val.restored == 0) {
                            restore_btn = "<span class='glyphicon glyphicon-open btn-restore-backup pointer'></span>";
                            style_color = "";
                        }
                        else {
                            restore_btn = "Restored";
                            style_color = "success";
                        }
                        $tbody
                            .append("<tr class='"+style_color+"'><td class='campaign'>"
                            + val.campaign_name
                                + "<span style='display:none' class='name'>"+val.name+"</span>"
                                + "<span style='display:none' class='backup_campaign_id'>"+val.backup_campaign_id+"</span>"
                                + "<span style='display:none' class='path'>"+val.path+"</span>"
                            + "</td><td class='backup_date'>"
                            +       val.backup_date
                            + "</td><td class='backup_user'>"
                            +       val.user_name
                            + "</td><td class='update_date_from'>"
                            +       val.update_date_from
                            + "</td><td class='update_date_to'>"
                            +       val.update_date_to
                            + "</td><td class='renewal_date_from'>"
                            +       val.renewal_date_from
                            + "</td><td class='renewal_date_to'>"
                            +       val.renewal_date_to
                            + "</td><td class='records_num' "+(val.restored == 1?"style='font-weight: bold; color: green'":"")+">"
                            + val.num_records
                            + "</td><td class=''>"
                            + restore_btn
                            + "</td></tr>");
                });
            } else {
                $tbody
                    .append("<tr><td colspan='6'>"
                    + response.msg
                    + "</td></tr>");
            }
        });
    },
    update_records: function($btn) {
        var row = $btn.closest('tr');
        var campaign_id = row.find('.campaign_id').text();
        var update_date_from = row.find('input[name="update_date_from_'+campaign_id+'"]').val();
        var update_date_to = row.find('input[name="update_date_to_'+campaign_id+'"]').val();
        var renewal_date_from = row.find('input[name="renewal_date_from_'+campaign_id+'"]').val();
        var renewal_date_to = row.find('input[name="renewal_date_to_'+campaign_id+'"]').val();
        var renewal_date_field = row.find('.renewal_date_field').text();

        $.ajax({
            url: helper.baseUrl + 'data/backup_data_by_campaign',
            type: "POST",
            dataType: "JSON",
            data: {
                'campaign_id': campaign_id,
                'update_date_from': update_date_from,
                'update_date_to': update_date_to,
                'renewal_date_from': renewal_date_from,
                'renewal_date_to': renewal_date_to,
                'renewal_date_field': renewal_date_field
            }
        }).done(function(response) {
            if (response.success) {
                row.find('.records_val').text(response.records_num);
                row.find('.records_url').attr("href",response.records_url);
            }
        });
    },
    new_backup: function($btn){
        var row = $btn.closest('tr');
        var records_num = row.find('.records_num').text();
        var campaign_name = row.find('.campaign').text();
        var campaign_id = row.find('.campaign_id').text();
        var update_date_from = row.find('input[name="update_date_from_'+campaign_id+'"]').val();
        var update_date_to = row.find('input[name="update_date_to_'+campaign_id+'"]').val();
        var renewal_date_from = row.find('input[name="renewal_date_from_'+campaign_id+'"]').val();
        var renewal_date_to = row.find('input[name="renewal_date_to_'+campaign_id+'"]').val();

        $('.new-backup-form').find('input[name="name"]').prop('disabled', false);
        $('.file-name').css('display', 'block');
        $('.continue-backup').prop('disabled', false);
        $('.close-backup').prop('disabled', false);
        $('.backup-running').css('display', 'none');

        $('.num_records_new').text(records_num);

        var d = new Date();
        $('.new-backup-form').find('input[name="name"]').val(campaign_name+"_"+ d.getFullYear()+ (d.getMonth()+1).toString().replace(/(^.$)/,"0$1") + d.getDate().toString().replace(/(^.$)/,"0$1")+ d.getHours().toString().replace(/(^.$)/,"0$1")+ d.getMinutes().toString().replace(/(^.$)/,"0$1")+ d.getSeconds().toString().replace(/(^.$)/,"0$1"));

        $('.new-backup-form').find('input[name="campaign_id"]').val(campaign_id);
        $('.new-backup-form').find('input[name="update_date_from"]').val(update_date_from);
        $('.new-backup-form').find('input[name="update_date_to"]').val(update_date_to);
        $('.new-backup-form').find('input[name="renewal_date_from"]').val(renewal_date_from);
        $('.new-backup-form').find('input[name="renewal_date_to"]').val(renewal_date_to);
        $('.new-backup-form').find('input[name="num_records"]').val(records_num);

        var pagewidth = $(window).width() / 2;
        var moveto = pagewidth - 250;
        $('<div class="modal-backdrop backup in"></div>').appendTo(document.body).hide().fadeIn();
        $('.backup-container').find('.backup-panel').show();
        $('.backup-content').show();
        $('.backup-container').fadeIn()
        $('.backup-container').animate({
            width: '600px',
            left: moveto,
            top: '10%'
        }, 1000);

        $('.new-backup-form').find('input[name="name"]').blur(function(){
            if ($('.new-backup-form').find('input[name="name"]').val().length <= 0) {
                $('.continue-backup').prop('disabled', true);
            }
            else {
                $('.continue-backup').prop('disabled', false);
            }
        });

        if (records_num <= 0) {
            $('.new-backup-form').find('input[name="name"]').prop('disabled', true);
            $('.continue-backup').prop('disabled', true);
        }
    },
    close_backup: function() {
        $('.modal-backdrop.backup').fadeOut();
        $('.backup-container').fadeOut(500, function() {
            $('.backup-content').show();
            $('.new-backup-form')[0].reset();
            $('.alert').addClass('hidden');
        });
    },
    save_backup: function($btn){
        $('.continue-backup').prop('disabled', true);
        $('.close-backup').prop('disabled', true);
        $('.backup-running').css('display', 'block');
        $('.file-name').css('display', 'none');
        $.ajax({
            url: helper.baseUrl + 'data/save_backup',
            type: "POST",
            dataType: "JSON",
            data: $('.new-backup-form').serialize()
        }).done(function(response) {
            if (response.success) {
                $('.modal-backdrop.backup').fadeOut();
                $('.backup-container').hide();
                backup_restore.backup_panel();
                backup_restore.backup_history_panel();
                flashalert.success(response.msg);
            }
            else {
                flashalert.danger(response.msg);
            }
            $('.backup-running').css('display', 'none');
        });
    },
    restore_backup: function($btn) {
        var row = $btn.closest('tr');

        var pagewidth = $(window).width() / 2;
        var moveto = pagewidth;
        $('<div class="modal-backdrop restore in"></div>').appendTo(document.body).hide().fadeIn();
        $('.restore-container').find('.restore-panel').show();
        $('.restore-content').show();
        $('.restore-container').fadeIn();
        $('.restore-container').animate({
            width: '50px',
            left: moveto,
            top: '50%'
        }, 1000);

        $.ajax({
            url: helper.baseUrl + 'data/restore_backup',
            type: "POST",
            dataType: "JSON",
            data: {
                'backup_campaign_id': row.find('.backup_campaign_id').text(),
                'path': row.find('.path').text()
            }
        }).done(function(response) {
            if (response.success) {
                flashalert.success(response.msg);
                backup_restore.backup_panel();
                backup_restore.backup_history_panel();
            }
            else {
                flashalert.danger(response.msg);
            }
            $('.modal-backdrop.restore').fadeOut();
            $('.restore-container').fadeOut(500, function() {
                $('.restore-content').show();
            });
        });
    }
}

/*the class below is for the add_record page. It gets initialized by the add_record.php view*/
var add_record = {
    init: function () {
        $(document).on('change', '#campaign', function () {
            add_record.show_campaign_type();
        });
        $(document).on("click", ".save-btn", function (e) {
            e.preventDefault();
            add_record.save($(this));
        });
    },
    show_campaign_type: function () {
        var ctype = $('#campaign option:selected').attr('ctype');
        $('#ctype-text').text("This is a " + ctype + " campaign.").show();

        if (ctype == 'B2B') {
            $('#company').show();
            $('#contact').hide();
        }
        else if (ctype == 'B2C') {
            $('#contact').show();
            $('#company').hide();
        }
    },
    //save a record
    save: function ($btn) {

        $("button[type=submit]").attr('disabled', 'disabled');
        $.ajax({
            url: helper.baseUrl + 'data/save_record',
            type: "POST",
            dataType: "JSON",
            data: $('form').serialize()
        }).done(function (response) {
            if (response.success) {
                //Redirect to the record panel
                window.location.href = '../records/detail/' + response.record_id;
                flashalert.success("Record saved");
            }
            else {
                $("button[type=submit]").attr('disabled', false);
                flashalert.danger("Error saving the record");
            }

        });
    }
}

var outcomes = {
    init: function () {
        $(document).on("click", '.new-outcome-btn', function(e) {
            e.preventDefault();
            outcomes.new_outcome();
        });

        $(document).on("click", '.edit-outcome-btn', function(e) {
            e.preventDefault();
            outcomes.edit_outcome($(this));
        });

        $(document).on('click', '.close-outcome-btn', function(e) {
            e.preventDefault();
            outcomes.close_outcome();
        });

        $(document).on('click', '.save-outcome-btn', function(e) {
            e.preventDefault();
            outcomes.save_outcome();
        });

        $(document).on('click', '.del-outcome-btn', function(e) {
            e.preventDefault();
            modal.remove_outcome($(this).attr('item-id'));
        });

        outcomes.load_outcomes();
    },
    load_outcomes: function() {
        $tbody = $('.outcome-data .ajax-table').find('tbody');
        $tbody.empty();
        $.ajax({
            url: helper.baseUrl + 'data/get_outcomes',
            type: "POST",
            dataType: "JSON"
        }).done(function (response) {
            if (response.success) {
                $.each(response.data, function(i, val) {
                    if (response.data.length) {
                        $tbody
                            .append("<tr><td style='display: none'>"
                                + "<span class='outcome_id' style='display: none'>"+val.outcome_id+"</span>"
                                + "<span class='outcome' style='display: none'>"+(val.outcome?val.outcome:"")+"</span>"
                                + "<span class='set_status' style='display: none'>"+val.set_status+"</span>"
                                + "<span class='set_progress' style='display: none'>"+val.set_progress+"</span>"
                                + "<span class='disabled' style='display: none'>"+val.disabled+"</span>"
                                + "<span class='sort' style='display: none'>"+(val.sort?val.sort:"")+"</span>"
                                + "<span class='delay_hours' style='display: none'>"+(val.delay_hours?val.delay_hours:"")+"</span>"
                                + "<span class='positive' style='display: none'>"+val.positive+"</span>"
                                + "<span class='dm_contact' style='display: none'>"+val.dm_contact+"</span>"
                                + "<span class='enable_select' style='display: none'>"+val.enable_select+"</span>"
                                + "<span class='force_comment' style='display: none'>"+val.force_comment+"</span>"
                                + "<span class='force_nextcall' style='display: none'>"+val.force_nextcall+"</span>"
                                + "<span class='no_history' style='display: none'>"+val.no_history+"</span>"
                                + "<span class='keep_record' style='display: none'>"+val.keep_record+"</span>"

                            + "</td><td class='' style='vertical-align: middle'>"
                            + "<input id="+val.outcome_id+" type='checkbox' "+(val.disabled?"":"checked")+" item-id='"+val.outcome_id+"' onclick='outcomes.disable($(this), this.checked ? 0 : 1)'>"
                            + "</td><td style='vertical-align: middle'>"
                            + val.outcome
                            + "</td><td class='status' style='vertical-align: middle'>"
                            + (val.status_name?val.status_name:'-')
                            + "</td><td class='progress_description' style='vertical-align: middle'>"
                            + (val.description?val.description:'-')
                            + "</td><td style='vertical-align: middle'>"
                            + (val.positive==1?"<span class='glyphicon glyphicon-ok btn-sm'></span>":'-')
                            + "</td><td style='vertical-align: middle'>"
                            + (val.dm_contact==1?"<span class='glyphicon glyphicon-ok btn-sm'></span>":'-')
                            + "</td><td style='vertical-align: middle'>"
                            + (val.sort?val.sort:'-')
                            + "</td><td style='vertical-align: middle'>"
                            + (val.enable_select==1?"<span class='glyphicon glyphicon-ok btn-sm'></span>":'-')
                            + "</td><td style='vertical-align: middle'>"
                            + (val.force_comment==1?"<span class='glyphicon glyphicon-ok btn-sm'></span>":'-')
                            + "</td><td style='vertical-align: middle'>"
                            + (val.force_nextcall==1?"<span class='glyphicon glyphicon-ok btn-sm'></span>":'-')
                            + "</td><td style='vertical-align: middle'>"
                            + (val.delay_hours?val.delay_hours:'-')
                            + "</td><td style='vertical-align: middle'>"
                            + (val.no_history==1?"<span class='glyphicon glyphicon-ok btn-sm'></span>":'-')
                            + "</td><td" +
                            " style='vertical-align: middle'>"
                            + (val.keep_record==1?"<span class='glyphicon glyphicon-ok btn-sm'></span>":'-')
                            + "</td><td class=''>" +
                                "<span title='Edit export form' class='btn edit-outcome-btn glyphicon glyphicon-pencil btn-sm' item-id='"+ val.outcome_id+"'></span>" +
                                "<span title='Delete export form' class='btn del-outcome-btn glyphicon glyphicon-remove btn-sm' item-id='"+ val.outcome_id+"'></span>"
                            + "</td></tr>");
                    }
                });
            }
            else {
                $tbody
                    .append("<tr><td>"+response.data+"</td></tr>");
            }
        });
    },
    new_outcome: function() {
        $(".save-outcome-btn").attr('disabled',false);

        $('#outcome-form')[0].reset();
        $('.status_select').selectpicker('val',[]).selectpicker('render');
        $('.progress_select').selectpicker('val',[]).selectpicker('render');
        $('#outcome-form').find('input[name="outcome_id"]').val("");

        var pagewidth = $(window).width() / 2;
        var moveto = pagewidth - 250;

        $('<div class="modal-backdrop outcome in"></div>').appendTo(document.body).hide().fadeIn();
        $('.outcome-container').find('.outcome-panel').show();
        $('.outcome-content').show();
        $('.outcome-container').fadeIn()
        $('.outcome-container').animate({
            width: '500px',
            left: moveto,
            top: '10%'
        }, 1000);

    },
    edit_outcome: function(btn) {
        $(".save-outcome-btn").attr('disabled',false);

        var row = btn.closest('tr');
        $('#outcome-form').find('input[name="outcome_id"]').val(row.find('.outcome_id').text());
        $('#outcome-form').find('input[name="outcome"]').val(row.find('.outcome').text());
        $('#outcome-form').find('input[name="outcome"]').val(row.find('.outcome').text());
        $('.status_select').selectpicker('val',row.find('.set_status').text()).selectpicker('render');
        $('.progress_select').selectpicker('val',row.find('.set_progress').text()).selectpicker('render');
        $('#outcome-form').find('input[name="disabled"]').prop( "checked", (row.find('.disabled').text()==1) );
        $('#outcome-form').find('input[name="disabled"]').val( (row.find('.disabled').text()==1?1:0) );
        $('#outcome-form').find('input[name="sort"]').val(row.find('.sort').text());
        $('#outcome-form').find('input[name="delay_hours"]').val(row.find('.delay_hours').text());
        $('#outcome-form').find('input[name="keep_record"]').prop( "checked", (row.find('.keep_record').text()==1) );
        $('#outcome-form').find('input[name="keep_record"]').val( (row.find('.keep_record').text()==1)?1:0 );
        $('#outcome-form').find('input[name="force_comment"]').prop( "checked", (row.find('.force_comment').text()==1) );
        $('#outcome-form').find('input[name="force_comment"]').val( (row.find('.force_comment').text()==1)?1:0 );
        $('#outcome-form').find('input[name="force_nextcall"]').prop( "checked", (row.find('.force_nextcall').text()==1) );
        $('#outcome-form').find('input[name="force_nextcall"]').val( (row.find('.force_nextcall').text()==1)?1:0 );
        $('#outcome-form').find('input[name="positive"]').prop( "checked", (row.find('.positive').text()==1) );
        $('#outcome-form').find('input[name="positive"]').val( (row.find('.positive').text()==1)?1:0 );
        $('#outcome-form').find('input[name="dm_contact"]').prop( "checked", (row.find('.dm_contact').text()==1) );
        $('#outcome-form').find('input[name="dm_contact"]').val( (row.find('.dm_contact').text()==1)?1:0 );
        $('#outcome-form').find('input[name="enable_select"]').prop( "checked", (row.find('.enable_select').text()==1) );
        $('#outcome-form').find('input[name="enable_select"]').val( (row.find('.enable_select').text()==1)?1:0 );



        var pagewidth = $(window).width() / 2;
        var moveto = pagewidth - 250;

        $('<div class="modal-backdrop outcome in"></div>').appendTo(document.body).hide().fadeIn();
        $('.outcome-container').find('.outcome-panel').show();
        $('.outcome-content').show();
        $('.outcome-container').fadeIn()
        $('.outcome-container').animate({
            width: '500px',
            left: moveto,
            top: '10%'
        }, 1000);

    },
    close_outcome: function() {

        $('.modal-backdrop.outcome').fadeOut();
        $('.outcome-container').fadeOut(500, function() {
            $('.outcome-content').show();
            $('.alert').addClass('hidden');
        });
    },

    save_outcome: function() {
        $(".save-details-btn").attr('disabled','disabled');
        $.ajax({
            url: helper.baseUrl + 'data/save_outcome',
            type: "POST",
            dataType: "JSON",
            data: $('#outcome-form').serialize()
        }).done(function(response) {
            if (response.success) {
                //Reload details table
                outcomes.load_outcomes();
                //Close edit form
                outcomes.close_outcome();

                flashalert.success(response.msg);
            }
            else {
                flashalert.danger(response.msg);
            }
        });
    },
    disable: function(item, disabled) {
        var outcome_id = item.attr('item-id');

        $.ajax({
            url: helper.baseUrl + 'data/disable_outcome',
            type: "POST",
            dataType: "JSON",
            data: {'outcome_id': outcome_id, 'disabled': disabled}
        }).done(function(response) {
            if (response.success) {
                //Reload details table
                outcomes.load_outcomes();

                flashalert.success(response.msg);
            }
            else {
                flashalert.danger(response.msg);
            }
        });
    },
    delete_outcome: function(outcome_id) {
        $.ajax({
            url: helper.baseUrl + 'data/delete_outcome',
            type: "POST",
            dataType: "JSON",
            data: {'outcome_id': outcome_id}
        }).done(function(response) {
            if (response.success) {
                //Reload details table
                outcomes.load_outcomes();

                flashalert.success(response.msg);
            }
            else {
                flashalert.danger(response.msg);
            }
        });
    }
}

var triggers = {
    init: function () {

        //##########################################################################################
        //############################### EMAIL TRIGGERS ###########################################
        //##########################################################################################
        $(document).on("click", ".email-campaign-filter", function (e) {
            e.preventDefault();
            $icon = $(this).closest('ul').prev('button').find('span');
            $(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $('.email-filter-form').find('input[name="campaign"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color", "black");
            $(this).css("color", "green");

            triggers.load_email_triggers();
        });
        $(document).on("click", ".email-outcome-filter", function (e) {
            e.preventDefault();
            $icon = $(this).closest('ul').prev('button').find('span');
            $(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $('.email-filter-form').find('input[name="outcome"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color", "black");
            $(this).css("color", "green");

            triggers.load_email_triggers();
        });
        $(document).on("click", ".email-template-filter", function (e) {
            e.preventDefault();
            $icon = $(this).closest('ul').prev('button').find('span');
            $(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $('.email-filter-form').find('input[name="template"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color", "black");
            $(this).css("color", "green");

            triggers.load_email_triggers();
        });

        $(document).on("click", '.new-email-trigger-btn', function(e) {
            e.preventDefault();
            triggers.new_email_trigger();
        });

        $(document).on("click", '.edit-email-trigger-btn', function(e) {
            e.preventDefault();
            triggers.edit_email_trigger($(this));
        });

        $(document).on('click', '.close-email-trigger-btn', function(e) {
            e.preventDefault();
            triggers.close_email_trigger();
        });

        $(document).on('click', '.save-email-trigger-btn', function(e) {
            e.preventDefault();
            triggers.save_email_trigger();
        });

        $(document).on('click', '.del-email-trigger-btn', function(e) {
            e.preventDefault();
            modal.remove_email_trigger($(this).attr('item-id'));
        });

        //Load the email triggers
        triggers.load_email_triggers();

        //##########################################################################################
        //############################### OWNERSHIP TRIGGERS #######################################
        //##########################################################################################
        $(document).on("click", ".ownership-campaign-filter", function (e) {
            e.preventDefault();
            $icon = $(this).closest('ul').prev('button').find('span');
            $(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $('.ownership-filter-form').find('input[name="campaign"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color", "black");
            $(this).css("color", "green");

            triggers.load_ownership_triggers();
        });
        $(document).on("click", ".ownership-outcome-filter", function (e) {
            e.preventDefault();
            $icon = $(this).closest('ul').prev('button').find('span');
            $(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $('.ownership-filter-form').find('input[name="outcome"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color", "black");
            $(this).css("color", "green");

            triggers.load_ownership_triggers();
        });

        $(document).on("click", '.new-ownership-trigger-btn', function(e) {
            e.preventDefault();
            triggers.new_ownership_trigger();
        });

        $(document).on("click", '.edit-ownership-trigger-btn', function(e) {
            e.preventDefault();
            triggers.edit_ownership_trigger($(this));
        });

        $(document).on('click', '.close-ownership-trigger-btn', function(e) {
            e.preventDefault();
            triggers.close_ownership_trigger();
        });

        $(document).on('click', '.save-ownership-trigger-btn', function(e) {
            e.preventDefault();
            triggers.save_ownership_trigger();
        });

        $(document).on('click', '.del-ownership-trigger-btn', function(e) {
            e.preventDefault();
            modal.remove_ownership_trigger($(this).attr('item-id'));
        });

        //Load the ownership triggers
        triggers.load_ownership_triggers();
    },
    //##########################################################################################
    //############################### EMAIL TRIGGERS FUNCTIONS #################################
    //##########################################################################################
    load_email_triggers: function() {
        var $tbody = $('.email-triggers .ajax-table').find('tbody');
        $tbody.empty();
        $.ajax({
            url: helper.baseUrl + 'data/get_email_triggers',
            type: "POST",
            dataType: "JSON",
            data: $('.email-filter-form').serialize()
        }).done(function (response) {
            if (response.success) {
                $.each(response.data, function(i, val) {
                    if (response.data.length) {
                        $tbody
                            .append("<tr><td style='display: none'>"
                            + "<span class='email_trigger_id' style='display: none'>"+val.trigger_id+"</span>"
                            + "<span class='email_campaign_id' style='display: none'>"+val.campaign_id+"</span>"
                            + "<span class='email_outcome_id' style='display: none'>"+val.outcome_id+"</span>"
                            + "<span class='email_template_id' style='display: none'>"+val.template_id+"</span>"
                            + "</td><td class='email_campaign' style='vertical-align: middle'>"
                            + (val.campaign?val.campaign:'-')
                            + "</td><td class='email_outcome' style='vertical-align: middle'>"
                            + (val.outcome?val.outcome:'-')
                            + "</td><td class='email_template' style='vertical-align: middle'>"
                            + (val.template?val.template:'-')
                            + "</td><td class=''  style='text-align: right'>" +
                            "<span title='Edit email trigger' class='btn edit-email-trigger-btn glyphicon glyphicon-pencil btn-sm' item-id='"+ val.trigger_id+"'></span>" +
                            "<span title='Delete email trigger' class='btn del-email-trigger-btn glyphicon glyphicon-remove btn-sm' item-id='"+ val.trigger_id+"'></span>"
                            + "</td></tr>");
                    }
                });
            }
            else {
                $tbody
                    .append("<tr><td>"+response.data+"</td></tr>");
            }
        });
    },
    load_ownership_triggers: function() {
        var $tbody = $('.ownership-triggers .ajax-table').find('tbody');
        $tbody.empty();
        $.ajax({
            url: helper.baseUrl + 'data/get_ownership_triggers',
            type: "POST",
            dataType: "JSON",
            data: $('.ownership-filter-form').serialize()
        }).done(function (response) {
            if (response.success) {
                $.each(response.data, function(i, val) {
                    if (response.data.length) {
                        $tbody
                            .append("<tr><td style='display: none'>"
                            + "<span class='ownership_trigger_id' style='display: none'>"+val.trigger_id+"</span>"
                            + "<span class='ownership_campaign_id' style='display: none'>"+val.campaign_id+"</span>"
                            + "<span class='ownership_outcome_id' style='display: none'>"+val.outcome_id+"</span>"
                            + "</td><td class='ownership_campaign' style='vertical-align: middle'>"
                            + (val.campaign?val.campaign:'-')
                            + "</td><td class='ownership_outcome' style='vertical-align: middle'>"
                            + (val.outcome?val.outcome:'-')
                            + "</td><td class=''  style='text-align: right'>" +
                            "<span title='Edit ownership trigger' class='btn edit-ownership-trigger-btn glyphicon glyphicon-pencil btn-sm' item-id='"+ val.trigger_id+"'></span>" +
                            "<span title='Delete ownership trigger' class='btn del-ownership-trigger-btn glyphicon glyphicon-remove btn-sm' item-id='"+ val.trigger_id+"'></span>"
                            + "</td></tr>");
                    }
                });
            }
            else {
                $tbody
                    .append("<tr><td>"+response.data+"</td></tr>");
            }
        });
    },
    new_email_trigger: function() {
        $(".save-email-trigger-btn").attr('disabled',false);

        $('#email-trigger-form')[0].reset();
        $('#email-trigger-form').find('.campaign_select').selectpicker('val',[]).selectpicker('render');
        $('#email-trigger-form').find('.outcome_select').selectpicker('val',[]).selectpicker('render');
        $('#email-trigger-form').find('.template_select').selectpicker('val',[]).selectpicker('render');
        $('#email-trigger-form').find('.user_select').selectpicker('val',[]).selectpicker('render');
        $('#email-trigger-form').find('input[name="trigger_id"]').val("");

        var pagewidth = $(window).width() / 2;
        var moveto = pagewidth - 250;

        $('<div class="modal-backdrop email-trigger in"></div>').appendTo(document.body).hide().fadeIn();
        $('.email-trigger-container').find('.email-trigger-panel').show();
        $('.email-trigger-content').show();
        $('.email-trigger-container').fadeIn()
        $('.email-trigger-container').animate({
            width: '500px',
            left: moveto,
            top: '10%'
        }, 1000);

    },
    edit_email_trigger: function(btn) {
        $(".save-email-trigger-btn").attr('disabled',false);

        var row = btn.closest('tr');

        $('#email-trigger-form').find('input[name="trigger_id"]').val(row.find('.email_trigger_id').text());
        $('#email-trigger-form').find('.campaign_select').selectpicker('val',row.find('.email_campaign_id').text()).selectpicker('render');
        $('#email-trigger-form').find('.outcome_select').selectpicker('val',row.find('.email_outcome_id').text()).selectpicker('render');
        $('#email-trigger-form').find('.template_select').selectpicker('val',row.find('.email_template_id').text()).selectpicker('render');

        $.ajax({
            url: helper.baseUrl + 'data/get_email_trigger_recipients',
            type: "POST",
            dataType: "JSON",
            data: {'trigger_id': row.find('.email_trigger_id').text()}
        }).done(function(response) {
            if (response.success) {
                $('#email-trigger-form').find('.user_select').selectpicker('val',response.data).selectpicker('render');
            }
        });

        var pagewidth = $(window).width() / 2;
        var moveto = pagewidth - 250;

        $('<div class="modal-backdrop email-trigger in"></div>').appendTo(document.body).hide().fadeIn();
        $('.email-trigger-container').find('.email-trigger-panel').show();
        $('.email-trigger-content').show();
        $('.email-trigger-container').fadeIn()
        $('.email-trigger-container').animate({
            width: '500px',
            left: moveto,
            top: '10%'
        }, 1000);

    },
    close_email_trigger: function() {

        $('.modal-backdrop.email-trigger').fadeOut();
        $('.email-trigger-container').fadeOut(500, function() {
            $('.email-trigger-content').show();
            $('.alert').addClass('hidden');
        });

        $('#email-trigger-form').find('.campaign_label').css('color', 'black');
        $('#email-trigger-form').find('.outcome_label').css('color', 'black');
        $('#email-trigger-form').find('.template_label').css('color', 'black');
        $('#email-trigger-form').find('.users_label').css('color', 'black');
        $('#email-trigger-form').find('.validation_msg').hide();
    },
    save_email_trigger: function() {
        $(".save-email-trigger-btn").attr('disabled','disabled');

        if (triggers.email_trigger_validation_form()) {
            $.ajax({
                url: helper.baseUrl + 'data/save_email_trigger',
                type: "POST",
                dataType: "JSON",
                data: $('#email-trigger-form').serialize()
            }).done(function(response) {
                if (response.success) {
                    //Reload details table
                    triggers.load_email_triggers();
                    //Close edit form
                    triggers.close_email_trigger();

                    flashalert.success(response.msg);
                }
                else {
                    flashalert.danger(response.msg);
                }
            });
        }
        else {
            flashalert.danger("There is at least one mandatory field empty");
            $('#email-trigger-form').find('.validation_msg').html("There is at least one mandatory field empty").css('color', 'red').show();
            $(".save-email-trigger-btn").attr('disabled',false);
        }
    },
    email_trigger_validation_form: function() {
        var validation = true;

        var campaign_id = $('#email-trigger-form').find('.campaign_select').val();
        var outcome_id = $('#email-trigger-form').find('.outcome_select').val();
        var template_id = $('#email-trigger-form').find('.template_select').val();
        var users = $('#email-trigger-form').find('.user_select').val();

        $('#email-trigger-form').find('.campaign_label').css('color', 'black');
        $('#email-trigger-form').find('.outcome_label').css('color', 'black');
        $('#email-trigger-form').find('.template_label').css('color', 'black');
        $('#email-trigger-form').find('.users_label').css('color', 'black');
        $('#email-trigger-form').find('.validation_msg').hide();

        if (!campaign_id) {
            validation = false;
            $('#email-trigger-form').find('.campaign_label').css('color', 'red');
        }
        if (!outcome_id) {
            validation = false;
            $('#email-trigger-form').find('.outcome_label').css('color', 'red');
        }
        if (!template_id) {
            validation = false;
            $('#email-trigger-form').find('.template_label').css('color', 'red');
        }
        if (!users) {
            validation = false;
            $('#email-trigger-form').find('.users_label').css('color', 'red');
        }

        return validation;
    },
    delete_email_trigger: function(trigger_id) {
        $.ajax({
            url: helper.baseUrl + 'data/delete_email_trigger',
            type: "POST",
            dataType: "JSON",
            data: {'trigger_id': trigger_id}
        }).done(function(response) {
            if (response.success) {
                //Reload details table
                triggers.load_email_triggers();

                flashalert.success(response.msg);
            }
            else {
                flashalert.danger(response.msg);
            }
        });
    },
    //##########################################################################################
    //############################### OWNERSHIP TRIGGERS FUNCTIONS #############################
    //##########################################################################################
    new_ownership_trigger: function() {
        $(".save-ownership-trigger-btn").attr('disabled',false);

        $('#ownership-trigger-form')[0].reset();
        $('#ownership-trigger-form').find('.campaign_select').selectpicker('val',[]).selectpicker('render');
        $('#ownership-trigger-form').find('.outcome_select').selectpicker('val',[]).selectpicker('render');
        $('#ownership-trigger-form').find('.user_select').selectpicker('val',[]).selectpicker('render');
        $('#ownership-trigger-form').find('input[name="trigger_id"]').val("");

        var pagewidth = $(window).width() / 2;
        var moveto = pagewidth - 250;

        $('<div class="modal-backdrop ownership-trigger in"></div>').appendTo(document.body).hide().fadeIn();
        $('.ownership-trigger-container').find('.ownership-trigger-panel').show();
        $('.ownership-trigger-content').show();
        $('.ownership-trigger-container').fadeIn()
        $('.ownership-trigger-container').animate({
            width: '500px',
            left: moveto,
            top: '10%'
        }, 1000);

    },
    edit_ownership_trigger: function(btn) {
        $(".save-ownership-trigger-btn").attr('disabled',false);

        var row = btn.closest('tr');

        $('#ownership-trigger-form').find('input[name="trigger_id"]').val(row.find('.ownership_trigger_id').text());
        $('#ownership-trigger-form').find('.campaign_select').selectpicker('val',row.find('.ownership_campaign_id').text()).selectpicker('render');
        $('#ownership-trigger-form').find('.outcome_select').selectpicker('val',row.find('.ownership_outcome_id').text()).selectpicker('render');

        $.ajax({
            url: helper.baseUrl + 'data/get_ownership_trigger_recipients',
            type: "POST",
            dataType: "JSON",
            data: {'trigger_id': row.find('.ownership_trigger_id').text()}
        }).done(function(response) {
            if (response.success) {
                $('#ownership-trigger-form').find('.user_select').selectpicker('val',response.data).selectpicker('render');
            }
        });

        var pagewidth = $(window).width() / 2;
        var moveto = pagewidth - 250;

        $('<div class="modal-backdrop ownership-trigger in"></div>').appendTo(document.body).hide().fadeIn();
        $('.ownership-trigger-container').find('.ownership-trigger-panel').show();
        $('.ownership-trigger-content').show();
        $('.ownership-trigger-container').fadeIn()
        $('.ownership-trigger-container').animate({
            width: '500px',
            left: moveto,
            top: '10%'
        }, 1000);

    },
    close_ownership_trigger: function() {

        $('.modal-backdrop.ownership-trigger').fadeOut();
        $('.ownership-trigger-container').fadeOut(500, function() {
            $('.ownership-trigger-content').show();
            $('.alert').addClass('hidden');
        });
    },

    save_ownership_trigger: function() {
        $(".save-ownership-trigger-btn").attr('disabled','disabled');

        if (triggers.ownership_trigger_validation_form()) {
            $.ajax({
                url: helper.baseUrl + 'data/save_ownership_trigger',
                type: "POST",
                dataType: "JSON",
                data: $('#ownership-trigger-form').serialize()
            }).done(function(response) {
                if (response.success) {
                    //Reload details table
                    triggers.load_ownership_triggers();
                    //Close edit form
                    triggers.close_ownership_trigger();

                    flashalert.success(response.msg);
                }
                else {
                    flashalert.danger(response.msg);
                }
            });
        }
        else {
            flashalert.danger("There is at least one mandatory field empty");
            $('#ownership-trigger-form').find('.validation_msg').html("There is at least one mandatory field empty").css('color', 'red').show();
            $(".save-ownership-trigger-btn").attr('disabled',false);
        }
    },
    ownership_trigger_validation_form: function() {
        var validation = true;

        var campaign_id = $('#ownership-trigger-form').find('.campaign_select').val();
        var outcome_id = $('#ownership-trigger-form').find('.outcome_select').val();
        var users = $('#ownership-trigger-form').find('.user_select').val();

        $('#ownership-trigger-form').find('.campaign_label').css('color', 'black');
        $('#ownership-trigger-form').find('.outcome_label').css('color', 'black');
        $('#ownership-trigger-form').find('.users_label').css('color', 'black');
        $('#ownership-trigger-form').find('.validation_msg').hide();

        if (!campaign_id) {
            validation = false;
            $('#ownership-trigger-form').find('.campaign_label').css('color', 'red');
        }
        if (!outcome_id) {
            validation = false;
            $('#ownership-trigger-form').find('.outcome_label').css('color', 'red');
        }
        if (!users) {
            validation = false;
            $('#ownership-trigger-form').find('.users_label').css('color', 'red');
        }

        return validation;
    },
    delete_ownership_trigger: function(trigger_id) {
        $.ajax({
            url: helper.baseUrl + 'data/delete_ownership_trigger',
            type: "POST",
            dataType: "JSON",
            data: {'trigger_id': trigger_id}
        }).done(function(response) {
            if (response.success) {
                //Reload details table
                triggers.load_ownership_triggers();

                flashalert.success(response.msg);
            }
            else {
                flashalert.danger(response.msg);
            }
        });
    }
}

var duplicates = {
    init: function () {

        $(document).on("click", ".campaign-filter", function(e) {
            e.preventDefault();
            $icon = $(this).closest('ul').prev('button').find('span');
            $(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $('.filter-form').find('input[name="campaign"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");

            duplicates.load_duplicates();
        });

        $(document).on("click", ".duplicates-filter", function(e) {
            e.preventDefault();
            $icon = $(this).closest('ul').prev('button').find('span');
            $(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $('.filter-form').find('input[name="filter_field"]').val($(this).attr('id'));
            $('.filter-form').find('input[name="filter_name"]').val(($(this).html()));
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");

            duplicates.load_duplicates();
        });

        $('.selectpicker').on('change', function(){
            duplicates.load_duplicates();
        });

        $('.filter-form').find('input[name="filter_input"]').blur(function(){
            duplicates.load_duplicates();
        });

        $('.filter-form').submit(function(){
            duplicates.load_duplicates();
            return false;
        });

        $(document).on("click", '.del-duplicates-btn', function(e) {
            e.preventDefault();
            modal.remove_duplicates($(this));
        });

        duplicates.load_duplicates();
    },
    load_duplicates: function() {
        var field_ar = $('.duplicates-filter').val();

        var filter_name = $('.filter-form').find('input[name="filter_name"]').val();
        var filter_field = $('.filter-form').find('input[name="filter_field"]').val();

        var img_loader = helper.baseUrl+"assets/img/ajax-loader-bar.gif";

        $thead = $('.filter-table .ajax-table').find('thead');
        $thead.empty();
        $tbody = $('.filter-table .ajax-table').find('tbody');
        $tbody.empty();

        if (field_ar) {
            $('.filter-form').find('input[name="filter_input"]').attr("disabled", false);
            $tbody
                .append("<tr><td colspan='3'><img src='"+img_loader+"'></td>");
            $.ajax({
                url: helper.baseUrl + 'data/get_duplicates',
                type: "POST",
                dataType: "JSON",
                data: $('.filter-form').serialize(),
                async: false
            }).done(function(response){
                $tbody.empty();
                if (response.success) {
                    if (response.data.length) {
                        $('.del-duplicates-btn').show().attr('disabled', false);
                        var thead_fields = "";
                        $.each(field_ar, function (k, field) {
                            thead_fields += "<th>"+field+"</th>";
                        });
                        $thead
                            .append(
                            thead_fields +
                            "<th>Duplicates count</th>" +
                            "<th></th>");
                        $.each(response.data, function (i, val) {
                            var tbody_fields = "";
                            var search_url = helper.baseUrl + "search/custom/records";
                            $.each(field_ar, function (k, field) {
                                var field_name = (field == "coname"?"name":field);
                                var field_value = (field == "coname"?btoa(val[field_name]):val[field_name]);
                                tbody_fields += "<td>"+val[field_name]+"</td>";
                                search_url += "/"+(field.replace("_","-"))+"/"+field_value;
                                if ($('.filter-form').find('input[name="campaign"]').val()) {
                                    search_url += "/campaign/"+$('.filter-form').find('input[name="campaign"]').val();
                                }
                            });
                            $tbody
                                .append("<tr>"
                                + tbody_fields
                                + "<td class='duplicates-count'>"
                                + val.duplicates_count
                                + "</td><td style='text-align: right'>" +
                                "<a href='"+search_url+"'><span class='marl btn btn-success view-duplicates-btn btn-sm'>View duplicate records</span></a>"
                                + "</td></tr>");
                        });
                    }
                }
                else {
                    $('.del-duplicates-btn').hide().attr('disabled', false);
                    $tbody
                        .append("<tr><td>"+response.data+"</td></tr>");
                }
            });
        }
        else {
            $('.filter-form').find('input[name="filter_input"]').attr("disabled", true);
            $('.del-duplicates-btn').hide().attr('disabled', false);
            $tbody
                .append("<tr><td>Please, select a filter in order to search the duplicates records</td>");
        }
    },

    delete_duplicates: function(btn) {
        $(".del-duplicates-btn").attr('disabled','disabled');

        $.ajax({
            url: helper.baseUrl + 'data/delete_duplicates',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize()
        }).done(function(response) {
            if (response.success) {
                $thead = $('.filter-table .ajax-table').find('thead');
                $thead.empty();
                $thead
                    .append(
                    "<th>Duplicates removed</th>" +
                    "<th></th>");
                $tbody = $('.filter-table .ajax-table').find('tbody');
                $tbody.empty();
                $tbody
                    .append("<tr>"
                    + "<td>"
                    + response.num_records+" duplicate record(s) removed"
                    + "</td></tr>");

                flashalert.success(response.msg);
            }
            else {
                flashalert.danger(response.msg);
            }
        });
    }
}

var suppression = {
    init: function () {

        $('.daterange').daterangepicker({
                opens: "left",
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
                    'Last 7 Days': [moment().subtract('days', 6), moment()],
                    'Last 30 Days': [moment().subtract('days', 29), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
                },
                format: 'DD/MM/YYYY',
                minDate: "02/07/2014",
                maxDate: moment(),
                startDate: moment(),
                endDate: moment()
            },
            function(start, end, element) {
                var $btn = this.element;
                $btn.find('.date-text').html(start.format('MMMM D') + ' - ' + end.format('MMMM D'));
                $btn.closest('.filter-form').find('input[name="date_from"]').val(start.format('YYYY-MM-DD'));
                $btn.closest('.filter-form').find('input[name="date_to"]').val(end.format('YYYY-MM-DD'));
                suppression.load_suppression();
            });
        $(document).on("click", '.daterange', function(e) {
            e.preventDefault();
        });

        $(document).on("click", ".campaign-filter", function (e) {
            e.preventDefault();
            $icon = $(this).closest('ul').prev('button').find('span');
            $(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $('.filter-form').find('input[name="campaign"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color", "black");
            $(this).css("color", "green");

            suppression.load_suppression();
        });

        $(document).on("click", '.new-suppression-btn', function(e) {
            e.preventDefault();
            suppression.new_suppression($(this));
        });

        $(document).on('click', '.close-suppression-btn', function(e) {
            e.preventDefault();
            suppression.close_suppression();
        });

        $(document).on('click', '.save-suppression-btn', function(e) {
            e.preventDefault();
            suppression.save_suppression();
        });

        $('#suppression-form').find('input[name="telephone_number"]').blur(function(){
            suppression.load_new_suppression_form();
        });

        $('#suppression-form').submit(function(){
            suppression.load_new_suppression_form();
            return false;
        });

        $(document).on('click','input[name="all_campaigns"]',function(e){
            if ($('#suppression-form').find('input[name="all_campaigns"]').is(":checked")) {
                $('.suppression_campaign_select').attr('disabled', true).trigger("chosen:updated");
            }
            else {
                $('.suppression_campaign_select').attr('disabled', false).trigger("chosen:updated")
            }
        });

        suppression.load_suppression();
    },
    load_suppression: function() {
        var $tbody = $('.filter-table .ajax-table').find('tbody');
        $tbody.empty();
        $.ajax({
            url: helper.baseUrl + 'data/get_suppression_numbers',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize()
        }).done(function (response) {
            if (response.success) {
                $.each(response.data, function (i, val) {
                    if (response.data.length) {
                        $tbody
                            .append("<tr><td style='display: none'>"
                            + "<span class='suppression_id' style='display: none'>" + val.suppression_id + "</span>"
                            + "</td><td class='telephone_number' style='vertical-align: middle'>"
                            + val.telephone_number
                            + "</td><td class='date_added' style='vertical-align: middle'>"
                            + (val.date_added ? val.date_added : '-')
                            + "</td><td class='date_updated' style='vertical-align: middle'>"
                            + (val.date_updated ? val.date_updated : '-')
                            + "</td><td class='campaign_list' style='vertical-align: middle'>"
                            + (val.campaign_list ? val.campaign_list : 'All')
                            + "</td><td class='reason' style='vertical-align: middle'>"
                            + (val.reason ? val.reason : '-')
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
    load_new_suppression_form: function() {
        var telephone_number = $('#suppression-form').find('input[name="telephone_number"]').val();
        $.ajax({
            url: helper.baseUrl + 'data/get_suppression_by_telephone_number',
            type: "POST",
            dataType: "JSON",
            data: {'telephone_number': telephone_number}
        }).done(function (response) {
            if (response.success) {
                $('.suppression_exist').show();
                $('#suppression-form').find('input[name="suppression_id"]').val(response.data.suppression_id);
                $('#suppression-form').find('textarea[name="reason"]').val(response.data.reason);
                if (response.data.campaign_id_list.length>0 && response.data.campaign_id_list[0].length>0) {
                    $('.suppression_campaign_select').attr('disabled', false).trigger("chosen:updated");
                    $('.suppression_campaign_select').selectpicker('val',response.data.campaign_id_list).selectpicker('render');
                    $('#suppression-form').find('input[name="all_campaigns"]').prop('checked', false);
                }
                else {
                    $('.suppression_campaign_select').selectpicker('deselectAll');
                    $('.suppression_campaign_select').attr('disabled', true).trigger("chosen:updated");
                    $('#suppression-form').find('input[name="all_campaigns"]').prop('checked', true);
                }
            }
            else {
                $('.suppression_exist').hide();
                $('.suppression_campaign_select').selectpicker('deselectAll');
                $('#suppression-form').find('input[name="all_campaigns"]').prop('checked', false);
                $('#suppression-form').find('input[name="suppression_id"]').val('');
            }
        });
    },
    new_suppression: function() {
        $(".save-suppression-btn").attr('disabled',false);
        $('.suppression_exist').hide();
        $('.suppression_campaign_select').selectpicker('deselectAll');
        $('#suppression-form').find('input[name="all_campaigns"]').prop('checked', false);
        $('#suppression-form').find('input[name="suppression_id"]').val('');
        $('.campaign-error').hide();
        $('.telephone-error').hide();

        $('#suppression-form')[0].reset();
        $('.status_select').selectpicker('val',[]).selectpicker('render');
        $('.progress_select').selectpicker('val',[]).selectpicker('render');
        $('#suppression-form').find('input[name="suppression_id"]').val("");

        var pagewidth = $(window).width() / 2;
        var moveto = pagewidth - 250;

        $('<div class="modal-backdrop suppression in"></div>').appendTo(document.body).hide().fadeIn();
        $('.suppression-container').find('.suppression-panel').show();
        $('.suppression-content').show();
        $('.suppression-container').fadeIn()
        $('.suppression-container').animate({
            width: '500px',
            left: moveto,
            top: '10%'
        }, 1000);
    },
    close_suppression: function() {

        $('.modal-backdrop.suppression').fadeOut();
        $('.suppression-container').fadeOut(500, function() {
            $('.suppression-content').show();
            $('.alert').addClass('hidden');
        });
        $('#suppression-form').find('input[name="all_campaigns"]').prop('checked', false);
        $('.suppression_campaign_select').attr('disabled', false).trigger("chosen:updated");
        $('.suppression_campaign_select').selectpicker('deselectAll');
        $('.suppression_exist').hide();
        $('#suppression-form')[0].reset();
        $('.campaign-error').hide();
        $('.telephone-error').hide();
    },

    save_suppression: function() {
        $(".save-suppression-btn").attr('disabled','disabled');

        var all_campaigns = ($('#suppression-form').find('input[name="all_campaigns"]').is(":checked")?1:0);
        var suppression_campaigns = $('.suppression_campaign_select').val();
        var telephone_number = $('#suppression-form').find('input[name="telephone_number"]').val();

        if (!telephone_number) {
            $('.telephone-error').html("Please set the telephone number");
            $(".save-suppression-btn").attr('disabled',false);
            $('.telephone-error').show();
        }
        else if (!all_campaigns && !suppression_campaigns) {
            $('.telephone-error').hide();
            $('.campaign-error').html("Please select a campaign before or click on \"Check for all campaigns\"");
            $(".save-suppression-btn").attr('disabled',false);
            $('.campaign-error').show();
        }
        else {
            $('.campaign-error').hide();
            $.ajax({
                url: helper.baseUrl + 'data/save_suppression',
                type: "POST",
                dataType: "JSON",
                data: $('#suppression-form').serialize()
            }).done(function(response) {
                if (response.success) {
                    flashalert.success(response.msg);
                    //Reload suppression table
                    suppression.load_suppression();
                    //Close suppression form
                    suppression.close_suppression();


                }
                else {
                    flashalert.danger(response.msg);
                }
            });
        }
    }
}

/* ==========================================================================
 MODALS ON THIS PAGE
 ========================================================================== */
var modal = {

    restore_campaign_backup: function($btn) {
        var row = $btn.closest('tr');
        $('.modal-title').text('Confirm Restore Backup - '+row.find('.name').text()+'');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').text('You are going to restore '+row.find('.records_num').text()+' records. Are you sure you want to restore this backup?');
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
            $('#modal').modal('toggle');
            backup_restore.restore_backup($btn);
        });
    },

    remove_outcome: function(outcome_id) {
        $('.modal-title').text('Confirm Delete');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').text('Are you sure you want to delete this outcome?');
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
            $('#modal').modal('toggle');
            outcomes.delete_outcome(outcome_id);
        });
    },

    remove_email_trigger: function(trigger_id) {
        $('.modal-title').text('Confirm Delete');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').text('Are you sure you want to delete this email trigger?');
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
            $('#modal').modal('toggle');
            triggers.delete_email_trigger(trigger_id);
        });
    },

    remove_ownership_trigger: function(trigger_id) {
        $('.modal-title').text('Confirm Delete');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').text('Are you sure you want to delete this ownership trigger?');
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
            $('#modal').modal('toggle');
            triggers.delete_ownership_trigger(trigger_id);
        });
    },

    remove_duplicates: function(btn) {
        $('.modal-title').text('Confirm Delete');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').text('Are you sure you want to delete all the duplicate records and keep the old one?');
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
            $('#modal').modal('toggle');
            duplicates.delete_duplicates(btn);
        });
    }
}