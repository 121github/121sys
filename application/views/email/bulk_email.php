<div class="page-header">
	<h2>
		Batch Email Tool
	</h2>
</div>

<div class="panel panel-primary email-panel">

<div class="panel-heading">
		<h4 class="panel-title">
			Setup bulk email send
		</h4>
	</div>
	<div class="panel-body">
    	<form>
        <div class="form-group">
        <label>Email Template</label><br>
        <select id="template_id" data-width="100%" class="selectpicker">
        <?php foreach($templates as $template){ ?>
        	<option value="<?php echo $template['id'] ?>" ><?php echo $template['name'] ?></option>
        <?php } ?>
        </select>
        </div>
        <div class="form-group">
        <label>URN List</label><br>
        <textarea id="urns" name="urns" class="form-control" placeholder="Paste in the record URNs you want to send to seperated by lines or commas"></textarea>
        </div>
        
         <div class="form-group">
    <button class="btn btn-primary" id="send">Send</button>
    <span id="wait" style="display:none"><img src="<?php echo base_url() ?>assets/img/ajax-loader.gif" /></span>
    </div>
        </form>
        
        
    </div>
           
    </div>
    <script>
	$(document).ready(function(){
		$(document).on('click','#send',function(e){
				e.preventDefault();
			if($('#urns').val()!==""){
		
		$.ajax({type:"POST",
		data:{ list:$('#urns').val() },
		dataType:"JSON"		
	}).done(function(list){
	$.ajax({
		url: helper.baseUrl+'search/send_email',
		type:"POST",
		dataType:"JSON",
		data:{ template_id:$('#template_id').val(),urn_list:list.urns }	,
		beforeSend:function(){ $('#send').hide(); $('#wait').show(); }
	}).done(function(response){
		if(response.success){
		flashalert.success(list.count+" emails are pending to be sent");
		 $('#send').show(); $('#wait').hide();	
		}
	});
		
	});
			} else {
			flashalert.danger("Please input some record URNs to the list");	
			}
		});
	});
	</script>