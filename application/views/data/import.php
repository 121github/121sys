<div id="wrapper">
    <div id="sidebar-wrapper">
        <?php $this->view('dashboard/navigation.php',$page) ?>
    </div>
    <div id="page-content-wrapper">
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Data Admin <span id="campaign-name-title" class="small"></span> <span class="small" id="campaign-type-title"></span></h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <form id="data-form">
                        <div class="panel panel-primary">
                            <div class="panel-heading"> <i class="fa fa-bar-chart-o fa-fw"></i>Import Data
                            </div>
                            <!-- /.panel-heading -->
                            <div class="panel-body">
                                <div class="step-container" id="step-1">
                                    <p>What campaign should the data be assigned to?</p>
                                    <div class="form-group input-group-sm">
                                        <select class="selectpicker" id="campaign">
                                            <option value="">Select the campaign</option>
                                            <?php foreach($campaigns as $row){ ?>
                                            <option ctype="<?php echo $row['type'] ?>" value="<?php echo $row['id'] ?>">
                                                <?php echo $row[ 'name'] ?>
                                            </option>
                                            <?php } ?>
                                        </select>
                                         <p id="ctype-text" class="green" style="display:none"></p>
                                    </div>
                                    
                                    <p>Where is this data from?</p>
                                    <div class="form-group input-group-sm">
                                        <select class="selectpicker pull-left" id="source">
                                            <option value="">Select the data source</option>
                                            <?php foreach($sources as $row){ ?>
                                            <option value="<?php echo $row['id'] ?>">
                                                <?php echo $row[ 'name'] ?>
                                            </option>
                                            <?php } ?>
                                            <option value="other">Other</option>
                                        </select>
                                        <input type="text" name="new_source" id="new_source" class="form-control pull-left marl" style="width:200px; display:none" placeholder="Enter the name of the data source" />
                                    </div>
                                    <div class="clearfix"></div>
                                    <button class="btn btn-success pull-right goto-step-2">Continue</button>

                                </div>




                                <div id="step-2" class="step-container" style="display:none">
                                    <p>
                                        Import your data from a CSV file</p>

                                    <span class="btn btn-default fileinput-button">
        <i class="glyphicon glyphicon-plus"></i>
        <span>Select file...</span>
                                    <!-- The file input field used as target for the file upload widget -->
                                    <input id="fileupload" type="file" name="files[]" data-url="<?php echo base_url()."import/import_file"; ?>">
                                    </span>
                                    <br>
                                    <br>
                                    <!-- The global progress bar -->
                                    <div id="progress" class="progress">
                                        <div class="progress-bar progress-bar-success"></div>
                                    </div>
                                    <!-- The container for the uploaded files -->
                                    <div id="files" class="files pull-left">
                                        <span id="filename"></span>
                                        <br>
                                        <span id="file-status"></span>
                                    </div>
                                    <button style="display:none" class="btn btn-success pull-right marl goto-step-3">Continue</button> <button class="btn btn-default pull-right goto-step-1">Back</button>
                                </div>
                                <div style="display:none" class="step-container" id="step-3">



                                    <div class="form-group input-group-sm pull-left">
                                        <p>URN Options</p>
                                        <select class="selectpicker" name="autoincrement" id="urn-options" data-width="150px">
                                            <option value="1">Use auto increment</option>
                                            <option value="2">Use CSV column</option>
                                        </select>
                                    </div>

                                    <div class="form-group input-group-sm pull-left marl">
                                        <p>Duplicate Options</p>
                                        <select name="duplicates" class="selectpicker" id="dupe-options" data-width="150px">
                                            <option value="1">Ignore (skip duplicates)</option>
                                            <option value="2">Overwrite (delete and recreate record!)</option>
                                            <option value="3">Update (update the selected fields)</option>
                                        </select>
                                    </div>
                                    <!--<div class="form-group input-group-sm pull-left marl">
                                        <p>Header options</p>
                                        <select name="header" class="selectpicker" id="more-options" data-width="150px">
                                            <option value="1">Ignore first row</option>
                                            <option value="0">Include first row</option>
                                        </select>
                                    </div>
                                    <div class="form-group input-group-sm pull-left marl">
                                        <p>Date formats</p>
                                        <select value="dateformat" name="dateformat" class="selectpicker" id="date-options" data-width="150px">
                                            <option value="DD/MM/YYYY">DD/MM/YYYY</option>
                                            <option value="DD/MM/YY">DD/MM/YY</option>
                                            <option value="YYYY-MM-DD">YYYY-MM-DD</option>
                                            <option value="YY-MM-DD">YY-MM-DD</option>
                                        </select>
                                    </div>-->
                                    <div class="form-group input-group-sm pull-left">
                                        <p id='import-progress'>&nbsp;</p>
                                        <button class="btn btn-success pull-right pull-left marl" id="import">Import</button> <button class="btn btn-default pull-left goto-step-2 marl">Back</button>
                                    </div>
                                    <div style="clear:both"></div>
                                    <div style="overflow-x:scroll">
                                        <table id="sample-table" class="table table-striped table-bordered">
                                            <thead>

                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- this is hidden until a file is uploaded -->
                                </div>

                            </div>
                            <!-- /.panel-body -->
                        </div>

                    </form>
                </div>

                <!-- /.row -->
            </div>
            <!-- /#page-wrapper -->
        </div>
    </div>

    <script src="<?php echo base_url() ?>assets/js/plugins/jqfileupload/vendor/jquery.ui.widget.js"></script>
    <!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
    <script src="<?php echo base_url() ?>assets/js/plugins/jqfileupload/jquery.iframe-transport.js"></script>
    <!-- The basic File Upload plugin -->

    <script src="<?php echo base_url() ?>assets/js/plugins/jqfileupload/jquery.fileupload.js"></script>
    <script src="<?php echo base_url() ?>assets/js/plugins/jqfileupload/jquery.fileupload-process.js"></script>
    <script src="<?php echo base_url() ?>assets/js/plugins/jqfileupload/jquery.fileupload-validate.js"></script>
       <script>
        $(document).ready(function() {				
					importer.init();
					});
    </script>