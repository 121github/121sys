 <div class="panel panel-primary contact-panel">
      <div class="panel-heading clearfix">
        <h4 class="panel-title"> Contact Details<?php if(in_array("add contacts",$_SESSION['permissions'])){ ?><span class="glyphicon glyphicon-plus pointer pull-right" data-modal="add-contact" data-urn="<?php echo $details['record']["urn"] ?>"></span><?php } ?></h4>
      </div>
      <!-- List group -->
      <?php if(isset($details['contacts'])){ ?>
      <ul class="list-group contacts-list">
        <?php $x=0; foreach($details['contacts']  as $id=>$contact): $x++; ?>
        <li  class="list-group-item" item-id="<?php echo $id ?>"><a data-toggle="collapse" data-parent="#accordion" href="#con-collapse-<?php echo $id ?>"> <?php echo $contact['name']['fullname']; ?> </a> <?php if(in_array("delete contacts",$_SESSION['permissions'])){ ?><span class="glyphicon glyphicon-trash marl pointer pull-right del-contact-btn" data-target="#modal" item-id="<?php echo $id ?>" ></span> <?php } ?> <?php if(in_array("edit contacts",$_SESSION['permissions'])){ ?> <span class="glyphicon glyphicon-pencil pointer pull-right" data-modal="edit-contact" data-id="<?php echo $id ?>"></span><?php } ?>
          <div id="con-collapse-<?php echo $id ?>" class="panel-collapse collapse <?php if($x==1&&$campaign['campaign_type_id']==1||$x==1&&isset($expand_contacts)){ echo "in"; } ?>">
            <dl class="dl-horizontal contact-detail-list">
              <?php foreach($contact['visible'] as $key=>$val){ if(!empty($val)&&$key!="Address"){ ?>
              <dt><?php echo $key ?></dt>
              <dd <?php if($key=="Notes"){ echo "style='color:red'"; } ?>><?php if($key=="Website"){ ?><a target="blank" href="<?php echo "http://".str_replace("http://","",$val) ?>"><?php echo $val ?></a><?php } else if($key=="Linkedin"){ ?><a target="blank" href="https://www.linkedin.com/profile/view?id=<?php echo $val ?>">View Profile</a><?php } else { echo $val; } ?></dd>
              <?php }
		  if($key=="Address"){
			 ?>
              <dt><?php echo $key ?></dt>
              <dd><a class="pull-right pointer" target="_blank" href="https://maps.google.com/maps?q=<?php echo $val['postcode'] ?>,+UK"><span class="glyphicon glyphicon-map-marker"></span> Map</a>
              <input type="hidden" name="contact_postcode" value="<?php echo $val['postcode'] ?>" />
                <?php foreach($val as $address_part){ echo (!empty($address_part)?$address_part."<br>":"");  }?>
              </dd>
              <?php
		  }
	  } ?>
              <?php foreach($contact['telephone']  as $number_id=>$number): 
			  $btn =(strpos($number['tel_name'],"Transfer")!==false?"btn btn-info pull-right":""); 
			  $style = (strpos($number['tel_name'],"Transfer")!==false?"style='margin-top:-20px'":"");
			  $number_text = (strpos($number['tel_name'],"Transfer")!==false?$number['tel_name']:$number['tel_num']); ?>
              <dt><?php echo (strpos($number_text,"Transfer")!==false?"":$number['tel_name']) ?></dt>
                  <dd>
                      <a  <?php echo $style ?>
                          href="#" class="startcall <?php echo $btn ?> <?php if(in_array("use timer",$_SESSION['permissions'])){ echo "starttimer"; } ?>" item-url="<?php echo $details['record']["telephone_protocol"].$details['record']["telephone_prefix"] ?><?php echo $number['tel_num'] ?>"><?php echo $number_text ?>
                      </a>
                      <?php if (strlen($number['tel_num'])>0&&!preg_match('/^Transfer/',$number['tel_name'])): ?>
                          <?php if ($number['tel_tps'] == NULL): ?>
                              <span class='glyphicon glyphicon-question-sign black tps-btn tt pointer' item-contact-id='<?php echo $id ?>' item-number-id='<?php echo $number_id ?>' item-number='<?php echo $number['tel_num'] ?>' data-toggle='tooltip' data-placement='right' title='TPS Status is unknown. Click to check it'></span>
                          <?php else: ?>
                              <span class='glyphicon <?php echo ($number['tel_tps']?"glyphicon-exclamation-sign red":"glyphicon-ok-sign green") ?> tt'  data-toggle='tooltip' data-placement='right' title='<?php echo ($number['tel_tps']?"This number IS TPS registered":"This number is NOT TPS registerd") ?>'></span>
                          <?php endif; ?>
                      <?php endif; ?>
                  </dd>
              <?php endforeach; ?>
            </dl>
          </div>
        </li>
        <?php endforeach; ?>
      </ul>
      <?php } else { ?><ul class="list-group"><li class="list-group-item">This record has no contacts</li></ul><?php } ?>
    </div>