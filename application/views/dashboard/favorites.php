<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary" id="a_favorites">
            <div class="panel-heading clearfix">
                <i class="fa fa-star fa-fw"></i> Favorites
            </div>
            <div class="panel-body favorites-panel"><img src="<?php echo base_url(); ?>assets/img/ajax-loader-bar.gif"/>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url() ?>assets/js/plugins/metisMenu/jquery.metisMenu.js"></script>

<!-- Page-Level Plugin Scripts - Dashboard -->
<script src="<?php echo base_url() ?>assets/js/plugins/morris/raphael-2.1.0.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/plugins/morris/morris.js"></script>

<!-- SB Admin Scripts - Include with every page -->
<script src="<?php echo base_url() ?>assets/js/sb-admin.js"></script>
<script>
    $(document).ready(function () {
        dashboard.init();
        dashboard.favorites_panel();

        $('.daterange').daterangepicker({
                opens: "left",
                ranges: {
                    'Todays': [moment(), moment()],
                    'Tomorrow': [moment().add('days', 1), moment().add('days', 1)],
                    'Missed': [moment('2014-01-01'), moment()],
                    'Upcoming': [moment(), moment('2025-01-01')]
                },
                format: 'DD/MM/YYYY HH:mm',
                minDate: "02/07/2014",
                startDate: moment(),
                timePicker: true,
                timePickerSeconds: false
            },
            function (start, end, element) {
                var $btn = this.element;
                var btntext = start.format('MMMM D') + ' - ' + end.format('MMMM D');
                console.log(start.format('YYYY-MM-DD'));
                if (start.format('YYYY-MM-DD') == '2014-07-02') {
                    var btntext = "Missed";
                }
                if (end.format('YYYY-MM-DD') == '2025-01-01') {
                    var btntext = "Upcoming";
                }
                $btn.find('.date-text').html(btntext);
                $btn.closest('form').find('input[name="date_from"]').val(start.format('YYYY-MM-DD HH:mm'));
                $btn.closest('form').find('input[name="date_to"]').val(end.format('YYYY-MM-DD HH:mm'));
                dashboard.callbacks_panel();
            });
        $(document).on("click", '.daterange', function (e) {
            e.preventDefault();
        });

        $("#my_favorites").on("click", function () {
            $("html,body").animate(
                {scrollTop: $("#a_favorites").offset().top},
                1500
            );
        });
    });
</script>