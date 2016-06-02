//js file contains common report functions
var common_report_functions = {
    init: function() {
        common_report_functions.buttons()
    },
    buttons: function() {
        $('#filter-right .daterange').daterangepicker({
                opens: "left",
                ranges: {
                    'Any Time': ["01/01/2015", moment()],
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract('month', 1).endOf('month')]
                },
                format: 'DD/MM/YYYY',
                minDate: "01/01/2015",
                maxDate: moment(),
                startDate: moment(),
                endDate: moment(),
            },
            function(start, end, element) {
                var $btn = this.element;
                if (element == "Any Time") {
                    $btn.find('.date-text').html('Any time');
                    $btn.closest('form').find('input[name="date_from"],input[name="date_to"]').val('');
                } else {
                    $btn.find('.date-text').html(start.format('MMMM D') + ' - ' + end.format('MMMM D'));
                    $btn.closest('form').find('input[name="date_from"]').val(start.format('YYYY-MM-DD'));
                    $btn.closest('form').find('input[name="date_to"]').val(end.format('YYYY-MM-DD'));
                }
            });


        //optgroup
        $('li.dropdown-header').on('click', function(e) {
            setTimeout(function() {
                //Get outcomes by campaigns selected
                common_report_functions.get_outcomes_filter();
                common_report_functions.get_sources_filter();
                common_report_functions.get_pots_filter();
            }, 500);
        });

        $('#filter-right').on("click", '#filter-submit', function(e) {
            e.preventDefault();
            report.load_panel();
            $('#filter-right').data("mmenu").close();
        });

        $('#filter-right').on("click", '.daterange', function(e) {
            e.preventDefault();
        });

        $('#filter-right').on("change", "[name='campaign_id[]']", function(e) {
            e.preventDefault();
            //Get outcomes by campaigns selected
            common_report_functions.get_outcomes_filter();
            common_report_functions.get_sources_filter();
            common_report_functions.get_pots_filter();
        });

        $('div.navbar').on("click", ".refresh-data", function(e) {
            e.preventDefault();
            report.load_panel();
        });
    },
    filters: function() {
        var filters = "";

        filters += "<span class='btn btn-default btn-xs clear-filters pull-right'>" +
            "<span class='glyphicon glyphicon-remove' style='padding-left:3px; color:black;'></span> Clear" +
            "</span>";
        if ($("#filter-form").find("input[name='date_from']").val() !== "") {
            //Date
            filters += "<h5><strong>Date </strong></h5>" +
                "<ul>" +
                "<li style='list-style-type:none'>" + $('#filter-right').find('.daterange .date-text').html() + "</li>" +
                "</ul>";
        }
        $.each($('#filter-right select'), function() {
            $ele = $(this).find('option:selected');
            var size = ($ele.size() > 0 ? "(" + $ele.size() + ")" : '');
			if(size!==""){
            filters += "<h5 style='border-bottom: 1px solid #e2e2e2; padding-bottom: 4px;'><strong>" + $(this).siblings('label').text() + "</strong> " + size + "</h5><ul>";
            $ele.each(function(index) {
                filters += "<li style='list-style-type:none'>" + $(this).text() + "</li>";
            });
            filters += "</ul>";
			}
        });

        $('#filters').html(filters);

    },
    get_outcomes_filter: function() {
        $.ajax({
            url: helper.baseUrl + 'reports/get_outcomes_filter',
            type: "POST",
            dataType: "JSON",
            data: $('#filter-form').serialize()
        }).done(function(response) {
            if (response.success) {
                var options = "";
                $.each(response.campaign_outcomes, function(type, data) {
                    options += "<optgroup label=" + type + ">";
                    $.each(data, function(i, val) {
                        options += "<option value=" + val.id + ">" + val.name + "</option>";
                    });
                    options += "</optgroup>";
                });
                $('#filter-right [name="outcome_id[]"]').html(options).selectpicker('refresh');
            }
        });
    },

    get_sources_filter: function() {
        $.ajax({
            url: helper.baseUrl + 'reports/get_sources_filter',
            type: "POST",
            dataType: "JSON",
            data: $('#filter-form').serialize()
        }).done(function(response) {
            if (response.success) {
                var options = "";
                $.each(response.campaign_sources, function(i, val) {
                    options += "<option value=" + val.id + ">" + val.name + "</option>";
                });
                $('#filter-right [name="source_id[]"]').html(options).selectpicker('refresh');
            }
        });
    },

    get_pots_filter: function() {
        $.ajax({
            url: helper.baseUrl + 'reports/get_pots_filter',
            type: "POST",
            dataType: "JSON",
            data: $('#filter-form').serialize()
        }).done(function(response) {
            if (response.success) {
                var options = "";
                $.each(response.campaign_pots, function(i, val) {
                    options += "<option value=" + val.id + ">" + val.name + "</option>";
                });
                $('#filter-right [name="pot_id[]"]').html(options).selectpicker('refresh');
            }
        });
    }

}