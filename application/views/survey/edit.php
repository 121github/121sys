

<div class="panel panel-primary contact-panel">
  <!-- Default panel contents -->
  <div class="panel-heading"><h4 class="panel-title">
Questions<span class="glyphicon glyphicon-question-sign pull-right tt <?php if($locked){ echo "red"; } ?>" data-toggle="tooltip" data-html="true" data-placement="top" title="<?php if($locked){ echo "Only the user that created the survey or an administrator can edit this survey"; } else { echo "Please complete as many questions as possible and leave notes where applicable. When you are finished click the save button below";}  ?>"></span>
      </h4></div>
      <div class="panel-body">

            <form role="form" id="survey-form">
            <input type="hidden" name="urn" value="<?php echo $urn ?>" />
            <input type="hidden" name="contact_id" value="<?php echo $contact_id ?>" />
            <input type="hidden" name="survey_id" value="<?php echo $survey_id ?>" />
            <input type="hidden" name="survey_info_id" value="<?php echo $survey_info_id ?>" />
<table  class="table table-striped table-responsive" >
          <?php foreach($questions as $id => $cat){
		  if($id){ ?>
          <tr><td colspan="3"><?php echo $categories[$id] ?></td></tr>
		   <?php } else { ?>
           <tr><td colspan="3">General Questions</td></tr>
			<?php    
		   }
	  foreach($cat as $question){
	  
	  $slider= false; ?>
<tr><td width="40%">
  <?php if(!empty($question['guide'])||!empty($question['other'])){ ?>
        <span class="padl pull-right glyphicon glyphicon-info-sign tt" data-toggle="tooltip" data-html="true" data-placement="top" title="<?php echo ($question['guide']?$question['guide']."<br>":"");  echo ($question['other']?"<span style='color:red'>".$question['other']."</span>":"") ?>"></span>
        <?php } ?>
        <?php if(!empty($question['script'])){ ?>
    <span class="padl pull-right glyphicon glyphicon-comment tt" data-html="true" data-toggle="tooltip" data-placement="top" title="<?php echo $question['script']; ?>"></span>  
    <?php } ?>
          <label><?php echo $question['question'] ?></labeL><br>
    <?php if(isset($question['options'])){ ?>
    <select class="selectpicker form-control" data-width="100%" data-title="Please Select" <?php if($question['multiple']=="1"):echo "multiple"; endif ?> name="answers[<?php echo $question['question_id'] ?>][answer][]">
 <?php foreach($question['options'] as $option_id => $option): ?>
 <option <?php if(in_array($option_id,$question['answers']['options'])){  echo "selected"; } ?> value="<?php echo $option_id ?>"><?php echo $option ?></option>
 <?php endforeach; ?>
 </select>
    <?php } else { $slider= true;?>
    <input name="answers[<?php echo $question['question_id'] ?>][answer][]" type="text" class="slider" value="<?php echo ($question['answers']['answer']?$question['answers']['answer']:"0") ?>" data-slider-min="0" data-slider-max="10" data-slider-step="1" data-slider-value="<?php echo ($question['answers']['answer']?$question['answers']['answer']:"0") ?>" data-slider-orientation="horizontal" data-slider-selection="before" data-slider-formater="zerona" data-slider-tooltip="hide">
      <input type="hidden" name="answers[<?php echo $question['question_id'] ?>][slider]" value="1"/>
    <?php } ?>  
</td><td width="40">

  <label>&nbsp;</label><br>
  <input style="width:40px" <?php if(!$slider){ echo "disabled readonly"; } else {  echo "value='".$question['answers']['answer']."'"; } ?> class="form-control slider-value"/>
</td>
<td>
  <label>Notes</label><br>
    <input class="form-control" value="<?php echo (isset($question['answers']['note'])?$question['answers']['note']:"") ?>" style="width:100%" note-id="<?php echo $question['question_id'] ?>" name="answers[<?php echo $question['question_id'] ?>][notes]"/>
</td></tr>
      <?php }
		  } ?>

</table>
<div class="form-actions pull-right">
         
         <button class="marl btn btn-default close-survey">Back</button>
         <?php if(!$locked){ ?>
         <button type="submit" class="marl btn btn-primary save-survey">Save</button>
         <button name="complete" value="complete" type="submit" class="btn marl btn-danger complete-survey">Complete</button>
         <?php } ?>
        </div>
      </form>
      </div>
      </div>
      
      <script type="text/javascript">
    $(document).ready(function () {   
        var urn = '<?php echo $urn ?>';
            survey.init(urn);
			survey.set_sliders();	
    });
</script>