<?php if (in_array("admin menu", $_SESSION['permissions'])) { ?>
          
         <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Admin <span class="caret"></span></a>
         <ul class="dropdown-menu">
            <?php if ($_SESSION['session_name'] == "121sys_prosales"&&$_SESSION['user_id']==1) { ?>
                <li><a href="#" id="del-data">Delete demo data</a></li>
                <script type="text/javascript">
                    $(document).on('click', '#del-data', function (e) {
                        e.preventDefault();
                        $.ajax({
                            url: helper.baseUrl + 'data/clear_records',
                            type: "POST",
                            dataType: "HTML",
                            beforeSend: function () {
                                modals.load_modal("Clearing data", "<p>Please be patient while the system is reset</p><img src='" + helper.baseUrl + "assets/img/ajax-loader-bar.gif' />", '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>');
                            }
                        }).done(function (response) {
                            modal_body.html(response);
                        });
                    });
                </script>
            <?php } ?>
             <?php if(in_array("campaign menu",$_SESSION['permissions'])){ ?>
				   <li><a href="<?php echo base_url() ?>admin/index/campaigns">Campaigns</a></li>
                   <?php } ?>
                    <?php if(in_array("data menu",$_SESSION['permissions'])){ ?>
			       <li><a href="<?php echo base_url() ?>admin/index/data">Data</a></li>
                    <?php } ?>
                     <?php if(in_array("view hours",$_SESSION['permissions'])){ ?>
                   <li><a href="<?php echo base_url() ?>admin/index/hours">Hours</a></li> 
                   <?php } ?>
                   <?php if(in_array("edit templates",$_SESSION['permissions'])){ ?>
			       <li><a href="<?php echo base_url() ?>admin/index/marketing">Marketing</a></li>
                   <?php } ?>
                    <?php if(in_array("admin shop",$_SESSION['permissions'])){ ?>
                   <li><a href="<?php echo base_url() ?>admin_shop">Shop</a></li> 
                   <?php } ?>        
                      <?php if(in_array("system menu",$_SESSION['permissions'])){ ?>
                   <li><a href="<?php echo base_url() ?>admin/index/system">System Config</a></li>
                    <?php } ?>  
                   <?php if(in_array("admin users",$_SESSION['permissions'])){ ?>      
                   <li><a href="<?php echo base_url() ?>admin/index/users">Users</a></li>
                    <?php } ?>  
                   </ul>
                        </li>  
				   
          
<?php } ?>
								   
                                  