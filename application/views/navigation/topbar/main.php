<div class="navbar navbar-default navbar-fixed-top" style="padding-left:15px">
          <a href="#menu" id="nav-menu-btn" class="btn btn-default navbar-toggle mobile-only"> <span class="icon-bar"></span> <span class="icon-bar"></span> <span
                class="icon-bar"></span></a>
    <?php if (isset($campaign_access)) { ?>
    <?php if(is_array($campaign_access)){ ?>
        <div id="top-campaign-container" <?php if(count($_SESSION['campaign_access']['array'])<3){ echo 'class="hidden"'; } ?> style="padding-top:8px; width:160px; display:none; float:left">
        <select id="top-campaign-select" class="selectpicker" data-width="160px">
            <?php if ($_SESSION['data_access']['mix_campaigns'] || (!isset($_SESSION['current_campaign']) && !$_SESSION['data_access']['mix_campaigns'])) { ?>
                <option
                    value=""><?php echo($_SESSION['data_access']['mix_campaigns'] ? "Campaign Filter" : "Select a campaign to begin"); ?></option>
            <?php } ?>
            <?php foreach ($campaign_access as $client => $camp_array) { ?>
                <optgroup label="<?php echo $client ?>">
                    <?php foreach ($camp_array as $camp) { ?>
                        <option <?php if (isset($_SESSION['current_campaign']) && $_SESSION['current_campaign'] == $camp['id']) {
                            echo "Selected";
                        } ?> value="<?php echo $camp['id'] ?>"><?php echo $camp['name'] ?></option>
                    <?php } ?>
                </optgroup>
            <?php } ?>
        </select>
    </div>
      <?php } ?>
     <?php } ?>
    <ul class="nav navbar-nav desktop-only" id="desktop-nav">
    <?php if(in_array("quick search",$_SESSION['permissions'])){ ?>
		 <li><a href="#" id='open-quicksearch'><i class="fa fa-search"></i> Search</a></li>
         <?php } ?>
           <?php if(in_array("use callpot",$_SESSION['permissions'])&&isset($_SESSION['current_campaign'])){ ?>
		 <li><a href="<?php echo base_url() ?>records/detail/0"><i class="fa fa-phone"></i> Start</a></li>
         <?php } ?>
        <?php if(in_array("add records",$_SESSION['permissions'])){ ?>
		 <li><a href="<?php echo base_url() ?>data/add_record"><i class="fa fa-plus"></i> Create</a></li>
         <?php } ?>
          <?php if(is_array($campaign_access)){ ?>
        <?php $this->view('navigation/topbar/dashboards.php', $page); ?>
        <?php } ?>
        <?php $this->view('navigation/topbar/view.php', $page); ?> 
             <?php $this->view('navigation/topbar/reports.php', $page); ?>  
               <?php $this->view('navigation/topbar/admin.php', $page); ?>  
               <?php $this->view('navigation/topbar/account.php', $page); ?>           
      </ul>
     
    <?php if ($_SESSION['environment'] == 'demo') { ?>
        <span style="color: red; margin-left: 10%; background-color: yellow">This is a demo system. The data added could be deleted at any time!!</span>
    <?php } ?>
    <a href="<?php echo (isset($_SESSION['home'])?base_url().$_SESSION['home']:'#') ?>" class="navbar-brand pull-right"><img id="small-logo" style="margin-top:-5px;margin-right:5px;"
                                                     src="<?php echo base_url(); ?>assets/themes/images/<?php echo(isset($_SESSION['theme_images']) ? $_SESSION['theme_images'] : "default"); ?>/small-logo.png"><img
            id="big-logo" style="margin-top:-5px; width:100%"
            src="<?php echo base_url(); ?>assets/themes/images/<?php echo(isset($_SESSION['theme_images']) ? $_SESSION['theme_images'] : "default"); ?>/logo.png"></a>
</div>