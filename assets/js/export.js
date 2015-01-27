// JavaScript Document

var export_data = {
    init: function() {
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
                startDate: "02/07/2014",
                endDate: moment()
            },
            function(start, end, element) {
                var $btn = this.element;
                $btn.find('.date-text').html(start.format('MMMM D') + ' - ' + end.format('MMMM D'));
                $('.filter-form').find('input[name="date_from"]').val(start.format('YYYY-MM-DD'));
                $('.filter-form').find('input[name="date_to"]').val(end.format('YYYY-MM-DD'));
            });
        $(document).on("click", '.daterange', function(e) {
            e.preventDefault();
        });

        $(document).on("click", ".campaign-filter", function(e) {
            e.preventDefault();
            $icon = $(this).closest('ul').prev('button').find('span');
            $(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $('.filter-form').find('input[name="campaign"]').val($(this).attr('id'));
            $('.filter-form').find('input[name="campaign_name"]').val(($(this).html()));
            $(this).closest('ul').find('a').css("color","black");
            $(this).css("color","green");
        });

        $(document).on("click", '.new-export-btn', function(e) {
            e.preventDefault();
            $('.edit-export-form')[0].reset();
            $('.edit-export-form').find('input[name="export_forms_id"]').val("");
            $('.custom-exports').show();
        });

        $(document).on("click", '.edit-btn', function(e) {
            e.preventDefault();
            export_data.edit_export_form($(this));
        });

        $(document).on('click', '.close-edit-btn', function(e) {
            e.preventDefault();
            export_data.close_export_form();
        });

        $(document).on('click', '.save-edit-btn', function(e) {
            e.preventDefault();
            export_data.save_export_form();
        });

        $(document).on('click', '.del-btn', function(e) {
            e.preventDefault();
            modal.delete_template($(this).attr('item-id'));
        });

        $(document).on('click', '.export-report-btn', function(e) {
            e.preventDefault();
            export_data.load_export_report_data($(this).attr('item-id'), $(this).attr('item-name'));
        });

        $(document).on('click', '.close-export-report', function(e) {
            e.preventDefault();
            export_data.close_export_report($(this));
        });

        export_data.load_export_forms();
    },
    load_export_forms: function() {
        $tbody = $('.export-data .ajax-table').find('tbody');
        $tbody.empty();
        $.ajax({
            url: helper.baseUrl + 'exports/get_export_forms',
            type: "POST",
            dataType: "JSON",
            async: false
        }).done(function(response){
            if (response.success) {
                $.each(response.data, function(i, val) {
                    if (response.data.length) {
                        $tbody
                            .append("<tr><td style='display: none'>"
                                + "<span class='export_forms_id' style='display: none'>"+(val.export_forms_id?val.export_forms_id:'')+"</span>"
                                + "<span class='header' style='display: none'>"+(val.header?val.header:'')+"</span>"
                                + "<span class='query' style='display: none'>"+(val.query?val.query:'')+"</span>"
                                + "<span class='order_by' style='display: none'>"+(val.order_by?val.order_by:'')+"</span>"
                                + "<span class='group_by' style='display: none'>"+(val.group_by?val.group_by:'')+"</span>"
                                + "<span class='date_filter' style='display: none'>"+(val.date_filter?val.date_filter:'')+"</span>"
                                + "<span class='campaign_filter' style='display: none'>"+(val.campaign_filter?val.campaign_filter:'')+"</span>"
                            + "</td><td class='name'>"
                            + val.name
                            + "</td><td class='description'>"
                            + val.description
                            + "</td><td class='report-export-prog-"+val.export_forms_id+"'>"
                            + "</td><td class='pull-right'>" +
                                    "<button title='Export to csv' type='submit' class='btn btn-default report-btn' onclick='document.pressed=this.value' value='"+val.export_forms_id+"'><span class='glyphicon glyphicon-export pointer'></span></button>" +
                                    "<span title='View the data before export' class='btn btn-default export-report-btn' item-id='"+ val.export_forms_id+"' item-name='"+ val.name+"'><span class='glyphicon glyphicon-eye-open pointer'></span></span>" +
                                    "<span title='Edit export form' class='btn btn-default edit-btn' item-id='"+ val.export_forms_id+"'><span class='glyphicon glyphicon-pencil'></span></span>" +
                                    "<span title='Delete export form' class='btn btn-default del-btn' item-id='"+ val.export_forms_id+"'><span class='glyphicon glyphicon-remove'></span></span>"
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
    onsubmitform: function() {
        $('.filter-form').find('input[name="export_forms_id"]').val(document.pressed);
        document.myform.action = helper.baseUrl + "exports/data_export";

        return true;
    },
    edit_export_form: function(btn) {
        $('.custom-exports').show();

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

        $('.edit-export-form').find('input[name="export_forms_id"]').val(export_forms_id);
        $('.edit-export-form').find('input[name="name"]').val(name);
        $('.edit-export-form').find('input[name="description"]').val(description);
        $('.edit-export-form').find('textarea[name="query"]').val(query);
        $('.edit-export-form').find('textarea[name="header"]').val(header);
        $('.edit-export-form').find('input[name="group_by"]').val(group_by);
        $('.edit-export-form').find('input[name="order_by"]').val(order_by);
        $('.edit-export-form').find('input[name="date_filter"]').val(date_filter);
        $('.edit-export-form').find('input[name="campaign_filter"]').val(campaign_filter);
    },
    close_export_form: function() {

        $('.edit-export-form')[0].reset();
        $('.custom-exports').hide();
    },

    save_export_form: function() {
        $(".save-edit-btn").attr('disabled','disabled');
        $.ajax({
            url: helper.baseUrl + 'exports/save_export_form',
            type: "POST",
            dataType: "JSON",
            data: $('.edit-export-form').serialize()
        }).done(function(response) {
            if (response.success) {
                //Reload exports table
                export_data.load_export_forms();
                //Close edit form
                export_data.close_export_form();

                $(".save-edit-btn").attr('disabled',false);

                flashalert.success(response.msg);
            }
            else {
                flashalert.danger(response.msg);
            }
        });
    },

    delete_export_form: function(export_forms_id) {

        $.ajax({
            url: helper.baseUrl + 'exports/delete_export_form',
            type: "POST",
            dataType: "JSON",
            data: {'export_forms_id': export_forms_id}
        }).done(function(response) {
            if (response.success) {
                //Reload exports table
                export_data.load_export_forms();
                //Close edit form
                export_data.close_export_form();

                flashalert.success(response.msg);
            }
            else {
                flashalert.danger(response.msg);
            }
        });
    },

    load_export_report_data: function(export_forms_id, name) {

        $('.report-export-prog-'+export_forms_id).html("<img src='"+helper.baseUrl+"assets/img/ajax-loader-bar.gif' />");

        $('.filter-form').find('input[name="export_forms_id"]').val(export_forms_id);

        $thead = $('.export-report-content .ajax-table').find('thead');
        $thead.empty();
        $thead.append("<tr></tr>");
        $tbody = $('.export-report-content .ajax-table').find('tbody');
        $tbody.empty();

        $.ajax({
            url: helper.baseUrl + 'exports/load_export_report_data',
            type: "POST",
            dataType: "JSON",
            data: $('.filter-form').serialize()
        }).done(function(response) {
            if (response.header) {
                $.each(response.header, function(i, val) {
                    if (response.header.length) {
                        $thead
                            .append("<th style='padding: 5px;'>"+val+"</th>");
                    }
                });
            }
            if (response.success) {
                $.each(response.data, function(i, data) {
                    if (response.data.length) {
                        $tbody
                            .append("<tr>");
                        $.each(data, function(k, val) {
                            $tbody
                                .append("<td style='padding: 5px;'>"+val+"</td>");
                        });
                        $tbody
                            .append("</tr>");
                    }
                });
            }
            else {
                $tbody
                    .append("<tr><td>"+data+"</td></tr>");
            }
        });

        export_data.show_export_report(export_forms_id, name);
    },

    show_export_report: function(export_forms_id, name) {

        $('.export-report-name').html(name);

        $('<div class="modal-backdrop export-report in"></div>').appendTo(document.body).hide().fadeIn();
        $('.export-report-container').find('.export-report-panel').show();
        $('.export-report-content').show();
        $('.export-report-container').fadeIn()
        $('.export-report-container').animate({
            width: '95%',
            height: '70%',
            left: '2%',
            top: '10%'
        }, 1000);

    },
    close_export_report: function() {

        var export_forms_id = $('.filter-form').find('input[name="export_forms_id"]').val();
        $('.report-export-prog-'+export_forms_id).html("");

        $('.modal-backdrop.export-report').fadeOut();
        $('.export-report-container').fadeOut(500, function() {
            $('.export-report-content').show();
            $('.alert').addClass('hidden');
        });
    }
}

/* ==========================================================================
 MODALS ON THIS PAGE
 ========================================================================== */
var modal = {

    delete_template: function(export_forms_id) {
        $('.modal-title').text('Confirm Delete');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').text('Are you sure you want to delete this export form?');
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
            export_data.delete_export_form(export_forms_id);
            $('#modal').modal('toggle');
        });
    }
}