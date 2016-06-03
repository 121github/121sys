<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-primary">
            <div class="panel-body">
                <div id="loading-overlay"></div>
                <div class="row" style="padding:0; margin:0;">
                    <div class="col-xs-12">
                        <div id="audit-table"><img class="table-loading"
                                                      src='<?php echo base_url() ?>assets/img/ajax-loader-bar.gif'>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    body {
    }

    #loading-overlay {
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