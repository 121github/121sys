<div class="row dashboard-area"></div>
<!-- /.row -->

<script>
    $(document).ready(function () {
        dashboard.init();
        dashboard.load_dash(<?php echo $dashboard['dashboard_id']; ?>);
		if($('form').find('input[name="date_from"]').val()!==""){
        var start = moment($('form').find('input[name="date_from"]').val(),"YYYY-MM-DD");
        var end = moment($('form').find('input[name="date_to"]').val(),"YYYY-MM-DD");
        $('.daterange').find('.date-text').html(start.format('MMMM D') + ' - ' + end.format('MMMM D'));
		}
    });
</script>

<style>
    .top-row { padding:10px; margin-bottom: 40px; }
    .bottom-row { padding:0px 10px 10px;}
</style>