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

        $(document).on("click", '.dials-export', function(e) {
            e.preventDefault();
            //export_data.dials_export();
            window.location.href= helper.baseUrl + 'export/dials_export';
        });

        $(document).on("click", '.contacts-added-export', function(e) {
            e.preventDefault();
            export_data.contacts_added_export();
        });
    },
    onsubmitform: function() {

        if(document.pressed == 'Dials') {
            document.myform.action = helper.baseUrl + "exports/dials_export";
        }
        else if(document.pressed == 'Contacts') {
            document.myform.action = helper.baseUrl + "exports/contacts_added_export";
        }

        return true;
    }
}