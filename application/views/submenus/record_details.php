<div class="navbar navbar-inverse navbar-inverse navbar-fixed-top" style="margin-top:50px">
    <ul class="nav navbar-nav mobile-only">
 <p class="navbar-text" style="color:#fff; font-weight:700">#<?php echo $data['record']['urn'] ?></p> 
</ul>
    <ul class="nav navbar-nav desktop-only">
        <p class="navbar-text" style="color:#fff; font-weight:700"><?php echo $title ?>
    <small>URN: <?php echo $data['record']['urn'] ?></small>
    <?php if(!empty($details['record']['client_ref'])){ ?><small id="client-ref">Ref: <?php echo $details['record']['client_ref'] ?></small><?php } ?>
    <small><?php echo(!empty($details['record']['campaign']) ? " / " . $details['record']['campaign'] : "") ?> <?php echo(!empty($details['record']['pot_name']) ? " / " . $details['record']['pot_name'] : "") ?></small>
    </p>
    </ul>
    <?php if(!isset($hide_filter)){ ?>
       <ul class="nav navbar-nav pull-right">
             <li>
             <div class="navbar-btn">
             <?php if(in_array("record options",$_SESSION['permissions'])){  ?>
<div class="btn-group">
  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
   <span class="glyphicon glyphicon-cog"></span> Options <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" id="record-options">
  <?php if(in_array("planner",$_SESSION['permissions'])){ ?>
       <li><a href="#" data-tab="tab-planner" data-modal="view-record" data-urn="<?php echo $details['record']['urn'] ?>">Add to planner</a></li>
         <li role="separator" class="divider"></li>
    <?php } ?>   
   <?php if(in_array("change color",$_SESSION['permissions'])){ ?>
    <li><a href="#" data-tab="color">Change Color</a></li>
      <?php } ?>
     <?php if(in_array("change icon",$_SESSION['permissions'])){ ?>
    <li><a href="#" data-tab="icon">Change Icon</a></li>
      <?php } ?>
     <?php if(in_array("change source",$_SESSION['permissions'])){ ?>
    <li><a href="#" data-tab="source">Change Source</a></li>
      <?php } ?>
     <?php if(in_array("change pot",$_SESSION['permissions'])){ ?>
    <li><a href="#" data-tab="pot">Change Pot</a></li>
      <?php } ?>
     <?php if(in_array("change campaign",$_SESSION['permissions'])){ ?>
    <li><a href="#" data-tab="campaign">Change Camapign</a></li>
          <li role="separator" class="divider"></li>
      <?php } ?>
    <?php if(in_array("park records",$_SESSION['permissions'])){ ?>
    <li><a href="#" data-tab="other">Remove Record</a></li>
    <?php } ?>
  </ul>
</div>
<?php } ?>
             
             
                   <?php if ($global_filter) {   $filter_class = "btn-default"; ?>
            <?php if (isset($_SESSION['filter']['values'])){ ?>
			<? if(@array_key_exists("pot_id",$_SESSION['filter']['values'])||@array_key_exists("source_id",$_SESSION['filter']['values'])||@array_key_exists("outcome_id",$_SESSION['filter']['values'])||@array_key_exists("postcode",$_SESSION['filter']['values'])) {    
           
                $filter_class = "btn-success";
            } ?>
             <?php } ?>  
                  <div class="btn-group">
                    <a href="#global-filter" id="submenu-filter-btn" class="btn <?php echo $filter_class ?>"><span
                            class="fa fa-filter"></span> Filter
                    </a>
              
                </div>
             <?php } ?>  
                
                              <?php //this is the agent navigation which brings single records in they can only go +/-1 record at a time and they must update the record before they can move on
          if (isset($_SESSION['current_campaign'])):
              if ($automatic || empty($nav['next']) && in_array("use callpot", $_SESSION['permissions'])): ?>
                  <?php if (isset($_SESSION['prev']) && !empty($_SESSION['prev']) && $_SESSION['prev'] != $details['record']['urn']): ?>
                      <a type="button" class="btn btn-success"
                         href="<?php echo base_url() . "records/detail/" . $_SESSION['prev'] ?>">Previous</a>
                  <?php endif ?>
            <a type="button"
               class="btn btn-success <?php if (!isset($_SESSION['next']) && !$allow_skip || empty($_SESSION['next']) && !$allow_skip) {
                   echo "nav-btn";
               } ?>"
               href="<?php echo base_url() . "records/detail/" . (isset($_SESSION['next']) ? $_SESSION['next'] : "0") ?>">Next</a>
              <?php endif;
          endif; ?>
 
                </div>
            </li>
            </ul>
            <?php } ?>
            </div>