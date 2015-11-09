
      <div class="row">
        <div class="col-lg-12">
          <h1 class="page-header">Logs Report</h1>
        </div>
        <!-- /.col-lg-12 --> 
      </div>
      <!-- /.row -->
      <div class="row">
        <div class="col-lg-12">
          
           <div class="panel panel-primary">
            <div class="panel-heading"> Logs
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body target-data">
             <table class="table"><thead><tr><th>Name</th><th>Username</th><th>Last Successful Login</th><th>Last Failed Login</th><th>Failed Attempts</th></tr></thead>
             <tbody>
             <?php foreach($logs as $log){ ?>
             <tr><td><?php echo $log['name'] ?></td>
             <td><?php echo $log['username'] ?></td>
             <td><?php echo ($log['last_login']?$log['last_login']:"-")  ?></td>
             <td><?php echo ($log['last_failed_login']?$log['last_failed_login']:"-") ?></td>
             <td><?php echo $log['failed_logins'] ?></td></tr>
             <?php } ?>
             </tbody>
             </table>
            </div>
            <!-- /.panel-body --> 
          </div>
          
        </div>

      <!-- /.row --> 

<script src="<?php echo base_url() ?>assets/js/plugins/metisMenu/jquery.metisMenu.js"></script> 
<!-- SB Admin Scripts - Include with every page --> 
<script src="<?php echo base_url() ?>assets/js/sb-admin.js"></script> 

