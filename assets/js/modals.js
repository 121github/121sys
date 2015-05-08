// JavaScript Document
var modals = {
	init:function(){
		$(document).on('click','.modal-set-location',function(e){
			e.preventDefault();
		modals.set_location();
	});
		$(document).on('click','.save-appointment',function(e){
		e.preventDefault();
		modals.save_appointment($('#appointment-form').serialize());
	});
	$(document).on('click','.modal-set-columns',function(e){
		e.preventDefault();
		modals.set_columns();
	});
	$(document).on('click','.modal-reset-table',function(e){
		e.preventDefault();
		modals.reset_table();
	});	
	$('#cal-slide-box').on('click','a',function(e){
		e.preventDefault();
		modals.view_appointment($(this).attr('data-event-id'));
	});
	$(document).on('click','[data-modal="edit-appointment"]',function(e){
		e.preventDefault();
		modals.view_appointment($(this).attr('data-event-id'),true);
	});
	$(document).on('click','[data-modal="delete-appointment"]',function(e){
		e.preventDefault();
		modals.delete_appointment_html($(this).attr('data-event-id'),true);
	});
	$(document).on('click','[data-modal="create-appointment"]',function(e){
		e.preventDefault();
		modals.create_appointment($(this).attr('data-urn'));
	});
	$(document).on('click','#modal #cancel-add-address',function(e){;
		e.preventDefault();
		$('#add-appointment-address').hide();
		$('#select-appointment-address').show();
		$('.addresspicker').selectpicker('val',$('#addresspicker option:first').val());
	});
	$(document).on('change','.addresspicker',function(e){
			if($(this).val()=="Other"){
				$('#add-appointment-address').show();
				$('#select-appointment-address').hide();
				$('.addresspicker').val('53');
			} else {
				$('#add-appointment-address').hide();
				$('#select-appointment-address').show();
			}
		});
		$(document).on('click','#modal #confirm-add-address',function(e){
		e.preventDefault();
		modals.confirm_other_appointment_address();
		});
		 $(document).on('click','.delete-appointment', function (e) {
            var cancellation_reason = $('.appointment-cancellation-form').find('textarea[name="cancellation_reason"]').val();			
			var id = $('#modal #appointment-id').val();
			if(cancellation_reason.length<5){
			flashalert.danger("You must enter a cancellation reason!");
			} else {
            modals.delete_appointment(id, cancellation_reason);
            $('#modal').modal('toggle');
			}
        });
	},
	save_appointment:function(data){
	$.ajax({ url:helper.baseUrl+'records/save_appointment',
		data:data,
		type:"POST",
		dataType:"JSON"
	}).done(function(response){
		if(response.success){
		flashalert.success('Appointment was saved');	
		} else {
		flashalert.danger('Appointment was not saved');		
		}
		});	
},
delete_appointment:function(id,cancellation_reason){
 $.ajax({
                url: helper.baseUrl + 'records/delete_appointment',
                type: "POST",
                dataType: "JSON",
                data: {
                    appointment_id: id,
                    cancellation_reason: cancellation_reason,
                    urn: record.urn
                }
            }).done(function (response) {
                record.appointment_panel.load_appointments();
                if (response.success) {
                    flashalert.success("Appointment was cancelled");
                } else {
                    flashalert.danger("Unable to cancel the appointment. Contact administrator");
                }
            }).fail(function (response) {
                flashalert.danger("Unable to cancel the appointment. Contact administrator");
            });
},
view_appointment:function(id,edit){
	$.ajax({ url: helper.baseUrl+'modals/view_appointment',
	type:"POST",
	data: {id:id},
	dataType: 'JSON'
	}).done(function(response){
		if(response.success){
			if(edit){
		modals.edit_appointment_html(response.data);		
			} else {
		modals.view_appointment_html(response.data);
			}
		} else {
		flashalert.dancer(response.msg);	
		}
	});
},
view_appointment_html:function(data){
              platform = navigator.platform,
              mapLink = 'http://maps.google.com/';
      if (platform === 'iPad' || platform === 'iPhone' || platform === 'iPod') {
        mapLink = 'comgooglemaps://';
      }      
	
	if(data.attendee_names.length>0){
		var attendees = "";
	$.each(data.attendee_names,function(i,val){
		if(i>0){
			attendees += ", "
		}
		attendees += val;
	});
	}
	var mheader = "Appointment #"+data.appointment_id;
	var mbody = "<table class='table'><tbody><tr><th>Company</th><td>"+data.coname+"</td></tr><tr><th>Date</th><td>"+data.starttext+"</td></tr><tr><th>Title</th><td>"+data.title+"</td></tr><tr><th>Notes</th><td>"+data.text+"</td></tr><tr><th>Attendees</th><td>"+attendees+"</td></tr>";
	if(data.distance&&data.current_postcode){
	mbody += "<tr><th>Distance</th><td>"+Number(data.distance).toFixed(2)+" Miles from "+data.current_postcode+"</td></tr>";
	}
	mbody += "</tbody></table>";
	mbody += "This appointment was set by <b>"+data.created_by+"</b> on <b>"+data.date_added+"</b>";
	var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button> <a class="btn btn-primary pull-right" data-modal="edit-appointment" data-event-id="'+data.appointment_id+'" >Edit Appointment</a> <a class="btn btn-primary pull-right" href="'+helper.baseUrl+'records/detail/'+data.urn+'">View Record</a> <a target="_blank" class="btn btn-info pull-right" href="'+ mapLink + '?q=' + data.postcode + '",+UK">View Map</a>';
	if(data.distance){
	mfooter += '<a target="_blank" class="btn btn-info pull-right" href="'+mapLink + '?zoom=2&saddr=' + data.current_postcode + '&daddr=' + data.postcode+'">Navigate</a>';
	}
	modals.load_modal(mheader,mbody,mfooter)
},
edit_appointment_html:function(data){
	$.ajax({ url:helper.baseUrl+'modals/edit_appointment',
	type:'POST',
	dataType:'html',
	data: { urn:data.urn }
	}).done(function(response){
		var mheader = "Edit Appointment #"+data.appointment_id;
		var mbody = '<div class="row"><div class="col-lg-12">'+response+'</div></div>';
		var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button> <button class="btn btn-primary pull-right save-appointment" type="button">Save</button> <button class="btn btn-danger pull-right" data-modal="delete-appointment" data-event-id="'+data.appointment_id+'" type="button">Delete</button>';
		$mbody = $(mbody);
		$.each(data,function(k,v){
			$mbody.find('[name="'+k+'"]').val(v);
			if(k=="type"){
			$mbody.find('[name="appointment_type_id"]').val(v);	
			}
			if(k=="attendees"){
				$.each(v,function(i,user_id){
				$mbody.find('#attendee-select option[value="'+user_id+'"]').prop('selected',true);
				});
			}	
		});
		modals.load_modal(mheader,$mbody,mfooter);
	});	
},
create_appointment:function(urn){
	$.ajax({ url:helper.baseUrl+'modals/edit_appointment',
	type:'POST',
	dataType:'html',
	data: { urn:urn }
	}).done(function(response){
		var mheader = "Create Appointment";	
		var mbody = '<div class="row"><div class="col-lg-12">'+response+'</div></div>';
		var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button> <button class="btn btn-primary pull-right save-appointment" type="button">Save</button>';
		modals.load_modal(mheader,mbody,mfooter);
	});	
},
delete_appointment_html: function (id) {
	var mheader = 'Confirm Cancellation';
	var mbody = '<form class="form-horizontal appointment-cancellation-form" style="padding:0 20px"><div class="row"><div class="col-lg-12"><input type="hidden" id="appointment-id" value="'+id+'" /><div class="form-group"><label>Are you sure you want to cancel this appointment?</label><textarea class="form-control" name="cancellation_reason" style="height:50px" placeholder="Please give a reason for the cancellation"/></textarea></div></div></form>';
	var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button> <button class="btn btn-primary pull-right delete-appointment" type="button">Confirm</button>';
	modals.load_modal(mheader,mbody,mfooter);
            },
confirm_other_appointment_address:function(){
	var new_postcode = $('#modal').find('[name="new_postcode"]').val();
		$.ajax({ url: helper.baseUrl+'ajax/validate_postcode',
		data: {postcode:new_postcode},
		dataType: 'JSON',
		type:'POST' }).done(function(response){
			//if postcode is valid
			if(response.success){	
		var new_address = "";
		//if the first line of address is complete
		if($('#modal').find('[name="add1"]').val()!=''){
		new_address += $('#modal').find('[name="add1"]').val();
		if($('#modal').find('[name="add2"]').val()!=''){
		new_address += ', '+$('#modal').find('[name="add2"]').val();
		}
		if($('#modal').find('[name="add3"]').val()!=''){
		new_address += ', '+$('#modal').find('[name="add3"]').val();
		}
		if($('#modal').find('[name="county"]').val()!=''){
		new_address += ', '+$('#modal').find('[name="county"]').val();
		}
		if($('#modal').find('[name="new_postcode"]').val()!=''){
		new_address += ', '+response.postcode;
		}
		$('#addresspicker').prepend('<option value="'+new_address+'|'+response.postcode+'">'+new_address+'</option>');
		$('.addresspicker').selectpicker('refresh').selectpicker('val',$('#addresspicker option:first').val());
		} else {
		//first line not complete
		flashalert.danger("Please enter the first line of the address");	
		}
			} else {
			//postcode is not valid
			flashalert.danger(response.msg);	
			}
		});
},	
	   show_modal: function () {
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        })
    },
 clear_footer: function () {
        $('#modal').find('.modal-footer').empty();
    },
load_modal:function(mheader,mbody,mfooter){
		$('#modal').find('.modal-title').html(mheader);
        $('#modal').find('.modal-body').html(mbody);
		$('#modal').find('.modal-footer').html(mfooter);
     	 if (!$('#modal').hasClass('in')) {
            modals.show_modal();
        }
		 $('#modal').find('.selectpicker').selectpicker();
         $('#modal').find('.tt').tooltip();
		 $('#modal').find('.datetime').datetimepicker({format: 'DD/MM/YYYY HH:mm'});
		 	 //this function automatically sets the end date for the appointment 1 hour ahead of the start date
            $(".startpicker").on("dp.hide", function (e) {
                var m = moment(e.date, "DD\MM\YYYY HH:mm");
                $('.endpicker').data("DateTimePicker").setMinDate(e.date);
                $('.endpicker').data("DateTimePicker").setDate(m.add('hours', 1).format('DD\MM\YYYY HH:mm'));
            });
},
	
columns:function(columns){
	 modals.default_buttons();
        $('.modal-title').text('Select columns to display');
        $('#modal').find('.modal-body').html($form);

        if (!$('#modal').hasClass('in')) {
            modals.show_modal();
        }
},
set_location:function(){
	
	modals.default_buttons();
	 $('.modal-title').text('Set location');
        $('#modal').find('.modal-body').html('<p>You must set a location to calculate distances and journey times</p><div class="form-group"><label>Enter Postcode</label><div class="input-group"><input type="text" class="form-control current_postcode_input" placeholder="Enter a postcode..."><div class="input-group-addon pointer btn locate-postcode"><span class="glyphicon glyphicon-map-marker"></span> Use my location</div></div>');
		$(".confirm-modal").off('click');
        $('.confirm-modal').on('click', function (e) {
          var postcode_saved = location.store_location($('.current_postcode_input').val()); 
           if(postcode_saved){
			    $('#modal').modal('toggle');
		   }
        });
        if (!$('#modal').hasClass('in')) {
            modal.show_modal();
        }
	
},
reset_table:function(){
	modals.default_buttons();
	 $('.modal-title').text('Reset table');
        $('#modal').find('.modal-body').html('<p>This will clear any filters that have been set on the table</p><p>Are you sure you want to reset the table filters?</p>');

        if (!$('#modal').hasClass('in')) {
            modal.show_modal();
        }
},
record:function(){
	/* add the modal code here */
},
company:function(){
	/* add the modal code here */
},
contact:function(){
	/* add the modal code here */
},
company_tel:function(){
	/* add the modal code here */
},
contact_tel:function(){
	/* add the modal code here */
},
company_address:function(){
	/* add the modal code here */
},
contact_address:function(){
	/* add the modal code here */
},
calendar:function(){
	/* add the modal code here */
},
}

modals.init();