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
            var export_forms = response.data;
            if (response.success) {
                $.each(response.data, function(i, val) {
                    if (response.data.length) {
                        $tbody
                            .append("<tr><td class='export_forms_id'>"
                            + val.export_forms_id
                            + "</td><td class='name'>"
                            + val.name
                            + "</td><td class='description'>"
                            + val.description
                            + "</td><td class='pull-right'>" +
                                    "<button type='submit' class='btn btn-default report-btn' onclick='document.pressed=this.value' value='"+val.export_forms_id+"'><span class='glyphicon glyphicon-export pointer'></span></button>" +
                                    "<button class='btn btn-default report-btn'><span class='glyphicon glyphicon-eye-open pointer'></span></button>" +
                                    "<button class='btn btn-default del-btn' item-id='"+ val.export_forms_id+"'><span class='glyphicon glyphicon-remove'></span></button>"
                            + "</td></tr>");
                    }
                });
            }
            else {
                $tbody
                    .append("<tr><td>"+export_forms+"</td></tr>");
            }
        });
    },
    onsubmitform: function() {
        $('.filter-form').find('input[name="export_forms_id"]').val(document.pressed);
        document.myform.action = helper.baseUrl + "exports/data_export";

        return true;
    }
}