<div class="loading-overlay">
    <div class="loader-img">
        <img src="<?php echo base_url(); ?>assets/img/ajax-loader-snake-black.gif" width="5%"/>
    </div>
</div>
<div id='calendar'></div>
<style>
    .tooltip-inner {
        text-align: left !important;
        max-width: 400px;
    }

    #calendar .tt-month {
        color: #fff;
        background: #FF0000;
        border-radius: 5px;
        padding: 2px 4px
    }

    #calendar .tt-week {
        color: #FF0000;
    }

    .fc-toolbar .bootstrap-select {
        text-align: left;
        width: auto !important;
        max-width: 200px
    }

    .context-menu-icon-updated {
        padding-left: 8px !important
    }
</style>
<style>
    body {
    }

    .loading-overlay {
        position: absolute;
        width: 100%;
        height: 100%;
        background: #000;
        opacity: 0.4;
        filter: alpha(opacity=40);
        z-index: 10;
        top: 0;
        left: 0
    }

    .loading-overlay .loader-img {
        text-align: center;
        height: 10em;
        position: relative;
        top: 50%;
        left: 50%;
        opacity: 0.4;
        background: transparent;
        transform: translate(-50%, -50%);
    }

    .container-fluid {
    }

    .top-row {
        padding: 10px 10px 0;
    }

    .bottom-row {
        padding: 0px 10px 10px;
    }

    .panel-body {
        overflow: hidden
    }

    #view-container {
        margin: 0;
        padding: 0 0px;
        overflow-y: auto;
        height: 100%;
        overflow-x: hidden;
    }
</style>
<script type="text/javascript">
    $(document).ready(function () {
        calendar.init();
        //$('#calendar').fullCalendar('chnageView','agendaWeek');
    })
</script>