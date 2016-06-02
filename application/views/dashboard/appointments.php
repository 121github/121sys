<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-body">
                <div class="loading-overlay"></div>
                <div class="row" style="padding:0; margin:0;">
                    <div class="col-xs-12">
                        <div id="view-container">
                            <img class="table-loading" src='<?php echo base_url() ?>assets/img/ajax-loader-bar.gif'>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<style>
body { }
.loading-overlay {  position:absolute; width:100%; height:100%; background:#000; opacity: 0.4; filter: alpha(opacity=40); z-index:10; top:0; left:0 }
.container-fluid { }
.top-row { padding:10px 10px 0; }
.bottom-row { padding:0px 10px 10px; }
.panel-body { overflow:hidden }
#view-container { margin:0; padding:0 0px; overflow-y:auto; height:100%; overflow-x:hidden; }
</style>

<script type="text/javascript">
var process_url = 'appointments/appointment_data';
var page_name ="appointment";
var table_id = 3; //the records table
var table_columns = <?php echo json_encode($columns) ?>; //the columns in this view

    $(document).ready(function () {
        view.init();
        view.has_filter = "<?php echo ((isset($_SESSION['filter']['values']) && !empty($_SESSION['filter']['values']))?true:false) ?>";

		 $('.daterange').daterangepicker({
                opens: "left",
                ranges: {
					'Any Time': ["01/01/2015",moment()],
					'Last Month': [moment().add(-1,'month').startOf('month'), moment().add(-1,'month').endOf('month')],
					'Last 7 Days': [moment().add(-7,'days'), moment()],
					'Yesterday': [moment().add(-1,'days'), moment()],
                    'Today': [moment(), moment()],
                    'Tomorrow': [moment().add( 1,'days'), moment().add(1,'days')],
                    'Next 7 Days': [moment(), moment().add(6,'days')],
                    'Next 30 Days': [moment(), moment().add(29,'days')],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Next Month': [moment().add(1,'month').startOf('month'), moment().add(1,'month').endOf('month')]
                },
                format: 'DD/MM/YYYY',
                minDate: "01/01/2015",
                startDate: moment(),
                endDate: moment().add(29,'days')
            },
            function (start, end, element) {
                var $btn = this.element;
				if(element=='Any Time'){
				$btn.find('.date-text').html('Any Time');
                $btn.closest('form').find('input[name="date_from"]').val('');
                $btn.closest('form').find('input[name="date_to"]').val('');	
				} else {
                $btn.find('.date-text').html(start.format('D/M/Y') + ' - ' + end.format('D/M/Y'));
                $btn.closest('form').find('input[name="date_from"]').val(start.format('YYYY-MM-DD'));
                $btn.closest('form').find('input[name="date_to"]').val(end.format('YYYY-MM-DD'));
				}
                view.reload_table();
            });

        $(document).on("click", '.daterange', function (e) {
            e.preventDefault();
        });

    });

</script>