<div id="wrapper">
    <div id="sidebar-wrapper">
        <?php $this->view('dashboard/navigation.php', $page) ?>
    </div>
    <div id="page-content-wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Special Exports</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading"><i class="fa fa-bar-chart-o fa-fw"></i>Available Exports
                            <div class="pull-right">
                                <div class="btn-group">
                                    <button class="daterange btn btn-default btn-xs" style="margin-right:5px"><span
                                            class="glyphicon glyphicon-calendar"></span> <span
                                            class="date-text"> <?php echo "2nd Jul - Today"; ?> </span>
                                    </button>
                                </div>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"> <span class="glyphicon glyphicon-filter"></span> Campaign</button>
                                    <ul class="dropdown-menu pull-right" role="menu">
                                        <?php foreach($campaigns as $row): ?>
                                            <li><a href="#" class="campaign-filter" id="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></a> </li>
                                        <?php endforeach ?>
                                        <li class="divider"></li>
                                        <li><a class="campaign-filter" ref="#" style="color: green;">All Campaigns</a> </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <?php if (count($campaigns) > 0) { ?>
                                <table class="table">
                                    <tr>
                                        <td>
                                            <span class='tt' data-placement='left' data-toggle='tooltip' title='The number of dials by date for the selected campaigns'>
                                                <span class='glyphicon glyphicon-info-sign'></span>
                                            </span>
                                            Dials By Date
                                        </td>
                                        <td>
                                            <form method="post"
                                                  action="<?php echo base_url() . "exports/dials_export" ?>">
                                                <input type="hidden" name="date_from">
                                                <input type="hidden" name="date_to">
                                                <span class='tt' data-placement='right' data-toggle='tooltip' title='Click to export the data to csv'><button>Export Now</button></span>
                                            </form>
                                        </td>
                                    <tr>
                                    <tr>
                                        <td>
                                            <span class='tt' data-placement='left' data-toggle='tooltip' title='The contacts added for the selected campaigns'>
                                                <span class='glyphicon glyphicon-info-sign'></span>
                                            </span>
                                            Contacts Added
                                        </td>
                                        <td>
                                            <form method="post"
                                                  action="<?php echo base_url() . "exports/contacts_added_export" ?>">
                                                <input type="hidden" name="date_from">
                                                <input type="hidden" name="date_to">
                                                <span class='tt' data-placement='right' data-toggle='tooltip' title='Click to export the data to csv'><button>Export Now</button></span>
                                            </form>
                                        </td>
                                    <tr>
                                </table>
                            <?php } else { ?>
                                <p>You do not have access to any campaigns</p>
                            <?php } ?>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                </div>

                <!-- /.row -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading"><i class="fa fa-bar-chart-o fa-fw"></i>Custom Exports

                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">

                        </div>
                        <!-- /.panel-body -->
                    </div>
                </div>

                <!-- /.row -->
            </div>
        <!-- /#page-wrapper -->
        </div>
    </div>
    <script>
        $(document).ready(function () {
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
                    startDate: "02/07/2014",
                    endDate: moment()
                },
                function (start, end, element) {
                    var $btn = this.element;
                    $btn.find('.date-text').html(start.format('MMMM D') + ' - ' + end.format('MMMM D'));
                    $btn.closest('.panel').find('input[name="date_from"]').val(start.format('YYYY-MM-DD'));
                    $btn.closest('.panel').find('input[name="date_to"]').val(end.format('YYYY-MM-DD'));
                });


        });
    </script>