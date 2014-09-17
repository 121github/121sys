// JavaScript Document

$(document).ready(function(){
	    $('.datetimepicker').datetimepicker();
		$('.selectpicker').selectpicker();
		$('.collapse').collapse();
$('.tab').click(function (e) {
  e.preventDefault()
  $(this).tab('show')
})

/* if a cancel button is pressed while looking at the phone or address input forms go back to the table view */
$(document).on('click','.hide-phone-form',function(e){
	e.preventDefault();
	$tab = $(this).closest('.tab-pane'); 
	$tab.find('form').hide();
	$tab.find('.table-container').show();
});
/*
This show the new form for the associated tab (phone or address)
*/
 		 $(document).on('click','.contact-add-item',function(e){
			 e.preventDefault();
			$tab = $(this).closest('.tab-pane'); 
			$tab.find('.table-container').hide();
			$tab.find('form')[0].reset();
			$tab.find('form').show();
			$tab.find('.close-contact-btn').removeClass('close-contact-btn').addClass('hide-phone-form');
			$tab.find('.save-contact-phone').attr('action','add_phone');
			$tab.find('.save-contact-address').attr('action','add_address');
		 });
						/*
			Save a phone number
			*/
				 $(document).on('click','.save-contact-phone',function(e){
					 e.preventDefault();
					 var $btn = $(this);
					 var action = $(this).attr('action')
					 var $form = $(this).closest('form');
					 var $alert = $(this).prev('span');
					$.ajax({
				url:helper.baseUrl+"ajax/"+action,
				type:"POST",
				dataType:"JSON",
				data: $form.serialize()
			}).done(function(response){
				 $alert.removeClass('hidden').fadeIn(1000).delay('2000').fadeOut();
			});
				 });
			/*
			Clicking the save contact button saves the details on the general tab. if its a new contact it will unhide the other tabs if the insert is successfull so that numbers and addresses can be added
			*/
				 $(document).on('click','.save-contact-general',function(e){
					 e.preventDefault();
					 var $btn = $(this);
					 var action = $(this).attr('action')
					 var $form = $(this).closest('form');
					 var $alert = $(this).prev('span');
					$.ajax({
				url:helper.baseUrl+"ajax/"+action,
				type:"POST",
				dataType:"JSON",
				data: $form.serialize()
			}).done(function(response){
				 $alert.removeClass('hidden').fadeIn(1000).delay('2000').fadeOut();
				 //change the add box to an edit box
				 if(action=="add_contact"){
				 $btn.attr('action','save_contact');
				 $form.closest('.form-container').find('input[name="contact_id"]').val(response.id);
				 $('.phone-tab,.address-tab').show();
				 $('.tab-alert').hide();
				 }
			});
				 });
			 


		  		

		 		  $(document).on('click','.close-contact-btn',function(e){
			  	e.preventDefault();
			 	 var $panel = $(this).closest('.panel');
				 var width = $('.record-panel').css('width');
				 $panel.find('.form-container').fadeOut(500,function(){ 
				 $panel.css('position','static').css('z-index','10').css('width',width).css('left','auto').css('top','auto');
				 $panel.find('.list-group').fadeIn(500);
				 $('.modal-backdrop').fadeOut();
				 })
				 $panel.find('.panel-title span').removeClass('glyphicon-remove close-contact-btn').addClass('glyphicon-plus add-contact-btn');
		  });
		  /*end contact section*/ 
		  
		  /*ownership section*/ 
		 $(document).on('click','.edit-owner',function(e){
			var $panel = $(this).closest('.panel');
			e.preventDefault();
			$('<div class="modal-backdrop in"></div>').appendTo(document.body).hide().fadeIn();
			$(this).removeClass('glyphicon-pencil edit-owner').addClass('glyphicon-remove close-owner');
			var width = $('.record-panel').css('width');
			$panel.css('position','absolute').css('z-index','99999').css('width',width);
			$panel.find('.panel-content').fadeOut(1000,function(){  $panel.find('.edit-panel').fadeIn(1000); });
			$('<div/>').addClass('panel panel-primary tempdiv').css('height',$('.ownership-panel').height()).css('visibility','hidden').insertAfter($panel);
		 });
		 
		 
		  $(document).on('click','.close-owner',function(e){
			  e.preventDefault();
			 	 var $panel = $(this).closest('.panel');
				 var width = $('.record-panel').css('width');
			  	$(this).removeClass('glyphicon-remove close-owner').addClass('glyphicon-pencil edit-owner');
				 $panel.find('.edit-panel').fadeOut(500,function(){ 
				 $panel.css('position','static').css('z-index','10').css('width',width).css('left','auto').css('top','auto');
				 $panel.find('.panel-content').fadeIn(500);
				 $('.tempdiv').remove();
				 $('.modal-backdrop').fadeOut();
				 }); 
		  });
		  /*end ownership section*/ 
		  
		  /*survey section*/ 
		 	$(document).on('click','.new-survey',function(e){
			  e.preventDefault();
			var pagewidth = $(window).width()/2;
			var moveto = pagewidth - 250;
				$('<div class="modal-backdrop in"></div>').appendTo(document.body).hide().fadeIn();
				$('.survey-panel').find('.edit-panel').show();
				$('.survey-content-1').show();
				$('.survey-content-2').hide();
				$('.survey-panel').fadeIn()
				$('.survey-panel').animate({width:'500px',left:moveto,top:100},1000);
				$('.surveypicker').selectpicker();
		  }); 
		 
		
		 $(document).on('click','.close-survey',function(e){
  				e.preventDefault();
			 	 var $panel = $(this).closest('.panel');
				 $('.modal-backdrop').fadeOut();
				 $('.survey-select-form')[0].reset();
				 $('.survey-content-1').show();
				 $('.survey-content-2').hide();
				 $('.alert').addClass('hidden');
				 $panel.fadeOut();
		  }); 
		  
		  $(document).on('click','.continue-survey',function(e){
			  e.preventDefault();
				$.ajax({
					url:helper.baseUrl+'survey/get_questions',
					type:"POST",
					dataType:"JSON",
					data: $('.survey-select-form').serialize()
				}).done(function(result){
					$('.survey-content-1').fadeOut('fast',function(){
						 $('.survey-content-2').fadeIn();
					 });
					
					$.each(result.data,function(k,v){
					$('.survey-content-2').append('<div class="form-group"><label>'+v.question_name+'</label><br><input type="text" data-id="test-brad" class="slider form-control" value="0" data-slider-min="0" data-slider-max="10" data-slider-step="1" data-slider-value="1" data-slider-orientation="horizontal" data-slider-selection="before"></div>');	
					});
					
					$('.survey-content-2').append('<div class="form-actions pull-right"><button type="submit" class="btn btn-primary continue-survey">Continue</button><button class="btn">Cancel</button></div>');
					$('.slider').slider();
					$('.slider').on('slide', function(ev){ 
					if(ev.value<7){ $(this).find('.slider-selection').css('background','#FF8282'); }
					if(ev.value===7||ev.value==8){ $(this).find('.slider-selection').css('background','#FF9900'); }
					if(ev.value>8){ $(this).find('.slider-selection').css('background','#428041'); }
					 });
				});
		  }); 
		  /*end survey section*/
		  	  $(document).on('click','.save-notes',function(e){
			  e.preventDefault();
			  $alert = $(this).prev('span');
			  $.ajax({
					url:helper.baseUrl+'records/save_notes',
					type:"POST",
					dataType:"JSON",
					data: {notes:$('.sticky-notes').val(),urn:$('#urn').text() }
				}).done(function(result){
					if(result.success){
						 $alert.removeClass('hidden').fadeIn(1000).delay('2000').fadeOut();
					} else {
						alert('Notes could not be saved. Please contact support@121customerinsight.co.uk');	
					}
				});
			  });
	
			  	  $(document).on('click','.save-contact',function(e){
			  e.preventDefault();
			  $.ajax({
					url:helper.baseUrl+'records/save_contact',
					type:"POST",
					dataType:"JSON",
					data: $(this).closest('form').serialize()
				}).done(function(result){
					if(result.success){
						$('.close-contact').trigger('click');
					} else {
						alert('Notes could not be saved. Please contact support@121customerinsight.co.uk');	
					}
				});
			  });
		 

		 
		  
		  function load_contact_tabs($panel,contact){
			$.ajax({
				url:helper.baseUrl+"ajax/get_contact",
				type:"POST",
				dataType:"JSON",
				data: {id:contact}
			}).done(function(response){
			
				
			});  
			  
		  }
		  
});