<div class="page-header">
	<h2>
		Unsubscribe
	</h2>
    <p>We sometimes send out marketing emails from companies that wish to tell you about their new products or services and it looks like you recieved one of them.  If you wish to be excluded from future marketing emails please enter your email address below.
</div>
<div class="row">
<div class="col-sm-12">
<form id="form">
<div class="form-group">
<label>Email Address</label>
<input class="form-control" name="email_address" placeholder="Enter your email address..."/>
</div>
<input type="submit" class="btn btn-primary" id="unsubscribe-btn" value="Unsubscribe!"/>
<input type="hidden" name="client_id" value="<?php echo $client_id ?>"/>
<input type="hidden" name="urn" value="<?php echo $urn ?>"/>
</form>
</div>
</div>
<br />
<div class="result-text"></div>

<script>
$(document).ready(function(){
	$('#unsubscribe-btn').click(function(e){
		e.preventDefault();
		$.ajax({url: helper.baseUrl+'email/unsubscribe',
		type:"POST",
		dataType:"JSON",
		data: $('#form').serialize()
		}).done(function(response){
			if(response.success){
			$('.result-text').removeClass('alert-danger').addClass('alert-success').text(response.msg);	
			$('#unsubscribe-btn').prop('disabled',true);
			} else {
			$('.result-text').removeClass('alert-success').addClass('alert-danger').text(response.msg);
			}
		});
	});
	
});

</script>