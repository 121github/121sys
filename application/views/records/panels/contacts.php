 <div class="panel panel-primary contact-panel">
      <div class="panel-heading">
        <h4 class="panel-title"> Contact Details<?php if(in_array("add contatcs",$_SESSION['permissions'])){ ?><span class="glyphicon glyphicon-plus pull-right add-contact-btn"></span><?php } ?></h4>
      </div>
      <div class="form-container">
        <?php $this->view('forms/edit_contact_form.php',array("urn"=>$details['record']["urn"])) ?>
      </div>
      <!-- List group -->
      <?php if(isset($details['contacts'])){ ?>
      <ul class="list-group contacts-list">
        <?php $x=0; foreach($details['contacts']  as $id=>$contact): $x++; ?>
        <li  class="list-group-item" item-id="<?php echo $id ?>"><a data-toggle="collapse" data-parent="#accordion" href="#con-collapse-<?php echo $id ?>"> <?php echo ($contact['name']["use_full"]?$contact['name']['fullname']:$contact['name']['title'] ." ".$contact['name']['firstname']." ".$contact['name']['lastname']); ?> </a> <?php if(in_array("delete contacts",$_SESSION['permissions'])){ ?><span class="glyphicon glyphicon-trash pull-right del-contact-btn" data-target="#modal" item-id="<?php echo $id ?>" ></span><?php } ?> <?php if(in_array("edit contacts",$_SESSION['permissions'])){ ?><span class="glyphicon glyphicon-pencil pull-right edit-contact-btn"  item-id="<?php echo $id ?>"></span><?php } ?>
          <div id="con-collapse-<?php echo $id ?>" class="panel-collapse collapse <?php if($x==1){ echo "in"; } ?>">
            <dl class="dl-horizontal contact-detail-list">
              <?php foreach($contact['visible'] as $key=>$val){ if(!empty($val)&&$key!="Address"){ ?>
              <dt><?php echo $key ?></dt>
              <dd><?php echo $val ?></dd>
              <?php }
		  if($key=="Address"){
			 ?>
              <dt><?php echo $key ?></dt>
              <dd><a class="pull-right pointer" target="_blank" href="https://maps.google.com/maps?q=<?php echo $val['postcode'] ?>,+UK"><span class="glyphicon glyphicon-map-marker"></span> Map</a>
                <?php foreach($val as $address_part){ echo (!empty($address_part)?$address_part."<br>":"");  }?>
              </dd>
              <?php
		  }
	  } ?>
              <?php foreach($contact['telephone']  as $id=>$number): ?>
              <dt><?php echo $number['tel_name'] ?></dt>
              <dd><a href="callto:<?php echo $number['tel_num'] ?>"><?php echo $number['tel_num'] ?></a></dd>
              <?php endforeach; ?>
            </dl>
          </div>
        </li>
        <?php endforeach; ?>
      </ul>
      <?php } else { ?><ul class="list-group"><li class="list-group-item">This record has no contacts</li></ul><?php } ?>
    </div>