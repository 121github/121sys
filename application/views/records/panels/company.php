<div class="panel panel-primary company-panel">
    <div class="panel-heading">
        <h4 class="panel-title"> Company Details
            <?php if (in_array("add companies", $_SESSION['permissions'])){ ?><!--Not using this feature yet. Need to clear the company id value in form if we start using it--><span
                class="glyphicon glyphicon-plus pointer pull-right" data-modal="add_company" data-urn="<?php echo $details['record']["urn"] ?>" style="display:none"></span><?php } ?>
        </h4>
    </div>
    <!-- List group -->
    <?php if (isset($details['company'])) { ?>
        <ul class="list-group company-list">
            <?php $x = 0;
            foreach ($details['company'] as $id => $company): $x++; ?>
                <li class="list-group-item" item-id="<?php echo $id ?>">
                    <a data-toggle="collapse" data-parent="#accordion" href="#com-collapse-<?php echo $id ?>"> <?php echo $company["Company Name"]; ?></a>
                    <!-- <span class="glyphicon glyphicon-trash pull-right del-company-btn" data-target="#modal" item-id="--><?php //echo $id ?><!--"></span>-->
                    <?php if (in_array("edit companies", $_SESSION['permissions'])) { ?>
                        <span class="glyphicon glyphicon-search pointer pull-right marl" data-urn = "<?php echo $details['record']["urn"] ?>" data-id="<?php echo $id ?>" data-modal="search-company"></span>
                        <span class="glyphicon glyphicon-pencil pointer pull-right" data-modal="edit-company" data-id="<?php echo $id ?>"></span>
              
                    <?php } ?>
                    <div id="com-collapse-<?php echo $id ?>" class="panel-collapse collapse <?php if ($x == 1) {
                        echo "in";
                    } ?>">
                        <dl class="dl-horizontal company-detail-list">
                            <?php foreach ($company['visible'] as $key => $val) {
                                if (!empty($val) && $key != "Address") { ?>
                                    <dt><?php echo $key ?></dt>
                                    <dd><?php if ($key == "Website") { ?><a target="blank"
                                                                            href="<?php echo "http://" . str_replace("http://", "", $val) ?>"><?php echo $val ?></a><?php } else if ($key == "Company #") { ?>
                                            <a href='http://companycheck.co.uk/company/<?php echo $val ?>'
                                               target='blank'><?php echo $val ?></a><?php } else if ($key == "Linkedin") { ?>
                                            <a href='https://www.linkedin.com/profile/view?id=<?php echo $val ?>'
                                               target='blank'><?php echo $val ?></a><?php } else {
                                            echo $val;
                                        } ?></dd>
                                <?php }
                                if ($key == "Address") {
                                    ?>
                                    <dt><?php echo $key ?></dt>
                                    <dd>
                                        <?php foreach ($val as $address_part) {
                                            echo(!empty($address_part) ? $address_part . "<br>" : "");
                                        }?>          <a class="pointer pull-right" style="margin-top:-20px" target="_blank"
                                           href="https://maps.google.com/maps?q=<?php echo $val['postcode'] ?>,+UK"><span
                                                class="glyphicon glyphicon-map-marker"></span>Map</a>
                                    </dd>
                                <?php
                                }
                            } ?>
                            <?php foreach ($company['telephone'] as $number_id => $number): $btn =($number['tel_name']=="Transfer"?"btn btn-info pull-right":""); $style = ($number['tel_name']=="Transfer"?"style='margin-top:10px'":"");$number_text = ($number['tel_name']=="Transfer"?"Transfer":$number['tel_num']); ?>
                                <dt><?php echo ($number_text=="Transfer"?"":$number['tel_name']) ?></dt>
                                <dd>
                                   <a  <?php echo $style ?>
                          href="#" class="startcall <?php echo $btn ?> <?php if(in_array("use timer",$_SESSION['permissions'])){ echo "starttimer"; } ?>" item-url="callto:<?php echo $number['tel_num'] ?>"><?php echo $number_text ?>
                      </a>
                                    <?php if (strlen($number['tel_num'])>0): ?>
                                        <?php if ($number['tel_tps'] == NULL): ?>
                                            <span <?php echo(empty($btn)?"":"style='visibility:hidden'") ?> class='glyphicon glyphicon-question-sign black ctps-btn tt pointer' item-company-id='<?php echo $id ?>' item-number-id='<?php echo $number_id ?>' item-number='<?php echo $number['tel_num'] ?>' data-toggle='tooltip' data-placement='right' title='CTPS Status is unknown. Click to check it'></span>
                                        <?php else: ?>
                                            <span <?php echo(empty($btn)?"":"style='visibility:hidden'") ?> class='glyphicon <?php echo ($number['tel_tps']?"glyphicon-exclamation-sign red":"glyphicon-ok-sign green") ?> tt'  data-toggle='tooltip' data-placement='right' title='<?php echo ($number['tel_tps']?"This number IS CTPS registered":"This number is NOT CTPS registerd") ?>'></span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </dd>
                            <?php endforeach; ?>
                        </dl>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php } else { ?>
        <ul class="list-group">
            <li class="list-group-item">This record has no company details</li>
        </ul><?php } ?>

</div>