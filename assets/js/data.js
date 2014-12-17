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
        $(document).on("click", ".backup-history-filter", function(e) {
            e.preventDefault();
            $icon = $(this).closest('ul').prev('button').find('span');
            $(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('form').find('input[name="campaign"]').val($(this).attr('id'));
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
                    if (val.campaign_name) {
                        $.ajax({
                            url: helper.baseUrl + 'data/backup_data_by_campaign',
                            type: "POST",
                            dataType: "JSON",
                            data: {
                                    'campaign_id': val.campaign_id,
                                    'update_date_from': (val.update_date_from?val.update_date_from:""),
                                    'update_date_to': (val.update_date_to?val.update_date_to:""),
                                    'renewal_date_from': (val.renewal_date_from?val.renewal_date_from:""),
                                    'renewal_date_to': (val.renewal_date_to?val.renewal_date_to:"")
                            }
                        }).done(function(response) {
                            if (response.success) {
                                    $tbody.append(
                                        "<tr><td class='campaign'>"
                                        + val.campaign_name + "<span style='display:none' class='campaign_id'>"+val.campaign_id+"</span>"
                                        + "</td><td class='update_date_from'>"
                                        + "<div class='input-group'>"
                                        +       "<input data-date-format='DD/MM/YYYY' value='"+val.update_date_from+"' name='update_date_from_"+val.campaign_id+"' type='text' class='form-control date' onblur='backup_restore.update_records($(this))'>"
                                        +       "<span class='input-group-btn'>"
                                        +           "<button class='btn btn-default clear-input' type='button'>X</button>"
                                        +       "</span>"
                                        + "</div>"
                                        + "</td><td class='update_date_to'>"
                                        + "<div class='input-group'>"
                                        +       "<input data-date-format='DD/MM/YYYY' value='"+val.update_date_to+"' name='update_date_to_"+val.campaign_id+"' type='text' class='form-control date' onblur='backup_restore.update_records($(this))'>"
                                        +       "<span class='input-group-btn'>"
                                        +           "<button class='btn btn-default clear-input' type='button'>X</button>"
                                        +       "</span>"
                                        + "</div>"
                                        + "</td><td class='renewal_date_from'>"
                                        + "<div class='input-group'>"
                                        +       "<input data-date-format='DD/MM/YYYY' value='"+val.renewal_date_from+"' name='renewal_date_from_"+val.campaign_id+"' type='text' class='form-control date' onblur='backup_restore.update_records($(this))'>"
                                        +       "<span class='input-group-btn'>"
                                        +           "<button class='btn btn-default clear-input' type='button'>X</button>"
                                        +       "</span>"
                                        + "</div>"
                                        + "</td><td class='renewal_date_to'>"
                                        + "<div class='input-group'>"
                                        +       "<input data-date-format='DD/MM/YYYY' value='"+val.renewal_date_to+"' name='renewal_date_to_"+val.campaign_id+"' type='text' class='form-control date' onblur='backup_restore.update_records($(this))'>"
                                        +       "<span class='input-group-btn'>"
                                        +           "<button class='btn btn-default clear-input' type='button'>X</button>"
                                        +       "</span>"
                                        + "</div>"
                                        + "</td><td class='records_num'>"
                                        + "<a class='records_url' href='" + response.records_url + "'><span class='records_val'>" + response.records_num + "</span></a>"
                                        + "</td><td class=''>"
                                        + "<span class='glyphicon glyphicon-save btn-new-backup pointer'></span>"
                                        + "</td></tr>");
                                $('.date').datetimepicker({
                                    pickTime: false
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
                        $tbody
                            .append("<tr><td class='campaign'>"
                            + val.campaign_name
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
                            + "</td><td class='records_num' style='font-weight: bold; color: green'>"
                            + val.num_records
                            + "</td><td class=''>"
                            + "<span class='glyphicon glyphicon-open btn-restore-backup pointer'></span>"
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

        $.ajax({
            url: helper.baseUrl + 'data/backup_data_by_campaign',
            type: "POST",
            dataType: "JSON",
            data: {
                'campaign_id': campaign_id,
                'update_date_from': update_date_from,
                'update_date_to': update_date_to,
                'renewal_date_from': renewal_date_from,
                'renewal_date_to': renewal_date_to
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

/* ==========================================================================
 MODALS ON THIS PAGE
 ========================================================================== */
var modal = {

    restore_campaign_backup: function(id) {
        $('.modal-title').text('Confirm Restore Backup');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').text('Are you sure you want to restore this backup?');
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
            backup_restore.restore_backup($(this));
            $('#modal').modal('toggle');
        });
    }
}