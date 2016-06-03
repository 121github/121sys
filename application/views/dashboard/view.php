<div class="row dashboard-area"></div>
<!-- /.row -->

<script>
    $(document).ready(function () {
        dashboard.init();

        //Set the default filter if exists, if not, set any time by default
        $('.daterange').trigger("click");
        $('.daterangepicker .ranges').find('li:contains("<?php echo ((isset($filters['date']))?$filters['date']['values'][0]:"Any Time"); ?>")').trigger("click");

        dashboard.load_dash(<?php echo $dashboard['dashboard_id']; ?>);
    });
</script>

<style>
    .top-row {
        padding: 10px;
        margin-bottom: 40px;
    }

    .bottom-row {
        padding: 0px 10px 10px;
    }
</style>