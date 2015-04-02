<div class="panel panel-primary company-panel">
    <div class="panel-heading">
        <h4 class="panel-title"> Company Details
            <?php if (in_array("add companies", $_SESSION['permissions'])){ ?><!--Not using this feature yet. Need to clear the company id value in form if we start using it--><span
                class="glyphicon glyphicon-plus pull-right add-company-btn" style="display:none"></span><?php } ?>
        </h4>
    </div>
    <div class="form-container">
        <?php $this->view('forms/edit_company_form.php', array("urn" => $details['record']["urn"])) ?>
    </div>
    <div class="search-container">
        <?php $this->view('forms/search_company_form.php', array("urn" => $details['record']["urn"])) ?>
    </div>
    <div class="get-company-container">
        <?php $this->view('forms/get_company_form.php', array("urn" => $details['record']["urn"])) ?>
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
                        <span class="glyphicon glyphicon-search pointer pull-right search-company-btn" item-id="<?php echo $id ?>"></span>
                        <span class="glyphicon glyphicon-pencil pull-right edit-company-btn" item-id="<?php echo $id ?>"></span>
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
                                    <dd><a class="pull-right pointer" target="_blank"
                                           href="https://maps.google.com/maps?q=<?php echo $val['postcode'] ?>,+UK"><span
                                                class="glyphicon glyphicon-map-marker"></span> Map</a>
                                        <?php foreach ($val as $address_part) {
                                            echo(!empty($address_part) ? $address_part . "<br>" : "");
                                        }?>
                                    </dd>
                                <?php
                                }
                            } ?>
                            <?php foreach ($company['telephone'] as $number_id => $number): ?>
                                <dt><?php echo $number['tel_name'] ?></dt>
                                <dd>
                                    <a href="#"
                                       class="startcall <?php if (in_array("use timer", $_SESSION['permissions'])) {
                                           echo "starttimer";
                                       } ?>"
                                       item-url="callto:<?php echo $number['tel_num'] ?>"><?php echo $number['tel_num'] ?>
                                    </a>
                                    <?php if ($number['tel_tps'] == NULL): ?>
                                        <span class='glyphicon glyphicon-question-sign black ctps-btn tt pointer' item-company-id='<?php echo $id ?>' item-number-id='<?php echo $number_id ?>' item-number='<?php echo $number['tel_num'] ?>' data-toggle='tooltip' data-placement='right' title='CTPS Status is unknown. Click to check it'></span>
                                    <?php else: ?>
                                        <span class='glyphicon <?php echo ($number['tel_tps']?"glyphicon-exclamation-sign red":"glyphicon-ok-sign green") ?> tt'  data-toggle='tooltip' data-placement='right' title='<?php echo ($number['tel_tps']?"This number IS CTPS registered":"This number is NOT CTPS registerd") ?>'></span>
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