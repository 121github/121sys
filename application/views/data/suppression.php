<div id="wrapper">
    <div id="sidebar-wrapper">
        <?php $this->view('dashboard/navigation.php', $page) ?>
    </div>
    <div id="page-content-wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Suppresion Numbers </h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <form id="record-form">
                        <div class="panel panel-primary">
                            <div class="panel-heading"><i class="fa fa-bar-chart-o fa-fw"></i>Suppresion Numbers
                            </div>
                            <!-- /.panel-heading -->
                            <div class="panel-body">

                            </div>
                    </form>
                </div>

                <!-- /.row -->
            </div>
            <!-- /#page-wrapper -->
        </div>
    </div>

    <script>
        $(document).ready(function () {
            suppression.init();
        });
    </script>