<div class="page-header">
  <h2>Create Survey <small>URN: <?php echo $urn ?> <?php echo (!empty($campaign['campaign_name'])?" [". $campaign['campaign_name']."]":"") ?></small> <span class="pull-right"><?php echo $contact['fullname'] ?></span></h2>
</div>

<div class="panel panel-primary contact-panel">
  <!-- Default panel contents -->
  <div class="panel-heading"><h4 class="panel-title">
Questions<span class="glyphicon glyphicon-question-sign pull-right tt" data-toggle="tooltip" data-html="true" data-placement="top" title="Please complete as many questions as possible and leave notes where applicable. When you are finished click the save button below"></span>
      </h4></div>
      <div class="panel-body">

            <form role="form" role="form">
            <input type="hidden" name="urn" value="<?php echo $urn ?>" />
            <input type="hidden" name="contact_id" value="<?php echo $contact['contact_id'] ?>" />
            <input type="hidden" name="survey_info_id" value="<?php echo $survey_info_id ?>" />
            
<table class="table table-striped table-responsive" >
      <?php foreach($questions as $cat_id => $id){ 
		  if($cat_id){ ?>
          <tr><td colspan="3"><?php echo $categories[$cat_id] ?></td></tr>
		   <?php } else { ?>
           <tr><td colspan="3">General Questions</td></tr>
			<?php    
		   }
	  foreach($id as $question){
	  
	  $slider= false; ?>
<tr><td width="40%">
  <?php if(!empty($question['guide'])||!empty($question['other'])){ ?>
        <span class="padl pull-right glyphicon glyphicon-info-sign tt" data-toggle="tooltip" data-html="true" data-placement="top" title="<?php echo ($question['guide']?$question['guide']."<br>":"");  echo ($question['other']?"<span style='color:red'>".$question['other']."</span>":"") ?>"></span>
        <?php } ?>
        <?php if(!empty($question['script'])){ ?>
    <span class="padl pull-right glyphicon glyphicon glyphicon-comment tt" data-html="true" data-toggle="tooltip" data-html="true" data-placement="top" title="<?php echo $question['script']; ?>"></span>  
    <?php } ?>
          <labeL><?php echo $question['question'] ?></labeL><br>
    <?php if(count($question['options'])>1){ ?>
    <select class="selectpicker form-control" data-width="100%" data-title="Please Select" <?php if($question['multiple']=="1"):echo "multiple"; endif ?> name="answers[<?php echo $question['question_id'] ?>][answer][]">
 <?php foreach($question['options'] as $option_id => $option): ?>
 <option value="<?php echo $option_id ?>"><?php echo $option ?></option>
 <?php endforeach; ?>
 </select>
    <?php } else { $slider= true;?>
    <input name="answers[<?php echo $question['question_id'] ?>][answer][]" type="text" class="slider" value="0" data-slider-min="0" data-slider-max="10" data-slider-step="1" data-slider-value="0" data-slider-orientation="horizontal" data-slider-selection="before" data-slider-formater="zerona">
      <input type="hidden" name="answers[<?php echo $question['question_id'] ?>][slider]" value="1"/>
    <?php } ?>  
</td><td width="40">

  <label>&nbsp;</label><br>
  <input style="width:40px" <?php if(!$slider){ echo "disabled readonly"; } else { echo "value='na'"; } ?> class="form-control slider-value" data-slider-tooltip="hide" />
</td>
<td>
  <label>Notes</label><br>
    <input class="form-control" style="width:100%" note-id="<?php echo $question['question_id'] ?>" name="answers[<?php echo $question['question_id'] ?>][notes]"/>
</td></tr>
      <?php } 
	  } 
	  ?>

</table>
<div class="form-actions pull-right">
         
         <button class="marl btn btn-default close-survey">Back</button>
         <button type="submit" class="marl btn btn-primary save-survey">Save</button>
         <button name="complete" value="complete" type="submit" class="btn marl btn-danger complete-survey">Complete</button>
        </div>
      </form>
      </div>
      </div>
      
      <script type="text/javascript">
    $(document).ready(function () {   
        var urn = '<?php echo $urn ?>';
            survey.init(urn);
			$('.slider').slider({tooltip:"hide"});
			 $('.slider').on('slide', function (ev){
				var newval = ev.value;	
				if(ev.value=="0"){
				newval="na";	
				}
				$(this).closest('td').next('td').find('.slider-value').val(newval);
                if (ev.value < 7) {
                    $(this).find('.slider-selection').css('background', '#FF8282');
                }
                if (ev.value === 7 || ev.value == 8) {
                    $(this).find('.slider-selection').css('background', '#FF9900');
                }
                if (ev.value > 8) {
                    $(this).find('.slider-selection').css('background', '#428041');
                }
            });	
    });
</script>