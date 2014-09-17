    <div class="panel panel-primary company-panel">
      <div class="panel-heading">
        <h4 class="panel-title"> Company Details<span class="glyphicon glyphicon-plus pull-right add-company-btn"></span> </h4>
      </div>
      <div class="form-container">
        <?php $this->view('forms/edit_company_form.php',array("urn"=>$details['record']["urn"])) ?>
      </div>
      <!-- List group -->
      <ul class="list-group company-list">
        <?php $x=0; foreach($details['company']  as $id=>$company): $x++; ?>
        <li  class="list-group-item" item-id="<?php echo $id ?>"><a data-toggle="collapse" data-parent="#accordion" href="#com-collapse-<?php echo $id ?>"> <?php echo $company["Company Name"]; ?></a> <span class="glyphicon glyphicon-trash pull-right del-company-btn" data-target="#modal" item-id="<?php echo $id ?>" ></span> <span class="glyphicon glyphicon-pencil pull-right edit-company-btn"  item-id="<?php echo $id ?>"></span>
          <div id="com-collapse-<?php echo $id ?>" class="panel-collapse collapse <?php if($x==1){ echo "in"; } ?>">
            <dl class="dl-horizontal contact-detail-list">
              <?php foreach($company['visible'] as $key=>$val){ if(!empty($val)&&$key!="Address"){ ?>
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
              <?php foreach($company['telephone']  as $id=>$number): ?>
              <dt><?php echo $number['tel_name'] ?></dt>
              <dd><a href="callto:<?php echo $number['tel_num'] ?>"><?php echo $number['tel_num'] ?></a></dd>
              <?php endforeach; ?>
            </dl>
          </div>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>