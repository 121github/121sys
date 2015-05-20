// JavaScript Document
var modals = {
	init:function(){
			$(document).on('click','[data-modal="view-record"]',function(e){
		e.preventDefault();
		modals.view_record($(this).attr('data-urn'));
	});
		$(document).on('click','.modal-set-location',function(e){
			e.preventDefault();
		modals.set_location();
	});
	$(document).on('click','.save-planner',function(e){
			e.preventDefault();
		modals.save_planner($(this).attr('data-urn'));
	});
		$(document).on('click','.remove-from-planner',function(e){
			e.preventDefault();
		modals.remove_from_planner($(this).attr('data-urn'));
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
		modals.view_appointment($(this).attr('data-id'));
	});
	$(document).on('click','[data-modal="view-appointment"]',function(e){
		e.preventDefault();
		modals.view_appointment($(this).attr('data-id'));
	});
	$(document).on('click','[data-modal="edit-appointment"]',function(e){
		e.preventDefault();
		modals.view_appointment($(this).attr('data-id'),true);
	});
	$(document).on('click','[data-modal="delete-appointment"]',function(e){
		e.preventDefault();
		modals.delete_appointment_html($(this).attr('data-id'),true);
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
		$('.close-modal').trigger('click');
		if(record.urn){
		record.appointment_panel.load_appointments();
		}
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
		flashalert.danger(response.msg);	
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
	var mbody = "<table class='table'><tbody><tr><th>Company</th><td>"+data.coname+"</td></tr><tr><th>Date</th><td>"+data.starttext+"</td></tr><tr><th>Title</th><td>"+data.title+"</td></tr><tr><th>Notes</th><td>"+data.text+"</td></tr><tr><th>Attendees</th><td>"+attendees+"</td></tr><tr><th>Type</th><td>"+data.appointment_type+"</td></tr>";
	if(data.distance&&data.current_postcode){
	mbody += "<tr><th>Distance</th><td>"+Number(data.distance).toFixed(2)+" Miles from "+data.current_postcode+"</td></tr>";
	}
	mbody += "</tbody></table>";
	mbody += "This appointment was set by <b>"+data.created_by+"</b> on <b>"+data.date_added+"</b>";
	var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button> <a class="btn btn-primary pull-right" data-modal="edit-appointment" data-id="'+data.appointment_id+'" >Edit Appointment</a> <a target="_blank" class="btn btn-info pull-right" href="'+ mapLink + '?q=' + data.postcode + '",+UK">View Map</a>';
	if(data.urn != $('#urn').val()){
	' <a class="btn btn-primary pull-right" href="'+helper.baseUrl+'records/detail/'+data.urn+'">View Record</a>';
	}
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
		var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button> <button class="btn btn-primary pull-right save-appointment" type="button">Save</button> <button class="btn btn-danger pull-right" data-modal="delete-appointment" data-id="'+data.appointment_id+'" type="button">Delete</button>';
		$mbody = $(mbody);
		//check if the appointment address is already in the dropdown and if not, add it.
		var option_exists = false;
		$.each($mbody.find('#addresspicker option'),function(){
			if($(this).val()==data.address+'|'+data.postcode){
				option_exists = true;
			}
		});
		if(!option_exists){
		$mbody.find('#addresspicker').prepend('<option value="'+data.address+'|'+data.postcode+'">'+data.address+'</option>');
		}
		//cycle through the rest of the fields and set them in the form
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
			$mbody.find('#addresspicker option[value="'+data.address+'|'+data.postcode+'"]').prop('selected',true);	
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
appointment_outcome_html: function(id){
	/*
	$.ajax({url:helper.baseUrl+'ajax/appointment_outcome_options',
	data:"POST",
	dataType:"JSON",
	data:{id:id}
	}).done(function(response){
		var outcome_options = "";
		$.each(response.outcomes,function(k,v){
		 outcome_options += '<option value="'+k+'">'+v+'</option>';	
		});
		
	var mbody = '<form class="form-horizontal appointment-outcome-form" style="padding:0 20px"><div class="row"><div class="col-lg-12"><input type="hidden" id="appointment-id" value="'+id+'" /><div class="form-group"><label>Please select the outcome of the appointment?</label><select class="selectpicker" name="appointment_outcome">'+outcome_options+'</select></div>';
	
	mbody += '<div class="form-group"><label>Please leave comments or feedback on the appointment</label><textarea class="form-control" name="cancellation_reason" style="height:50px" placeholder="How"/></textarea></div></div></form>';
	var mfooter = '<button data-modal="edit-appointment" data-event-id="'+id+'" class="btn btn-default pull-left"  type="button">Back</button> <button class="btn btn-primary pull-right delete-appointment" type="button">Confirm</button>';
	modals.load_modal(mheader,mbody,mfooter);
	
	});*/
            },
delete_appointment_html: function (id) {
	var mheader = 'Confirm Cancellation';
	var mbody = '<form class="form-horizontal appointment-cancellation-form" style="padding:0 20px"><div class="row"><div class="col-lg-12"><input type="hidden" id="appointment-id" value="'+id+'" /><div class="form-group"><label>Are you sure you want to cancel this appointment?</label><textarea class="form-control" name="cancellation_reason" style="height:50px" placeholder="Please give a reason for the cancellation"/></textarea></div></div></form>';
	var mfooter = '<button data-modal="edit-appointment" data-id="'+id+'" class="btn btn-default pull-left"  type="button">Back</button> <button class="btn btn-primary pull-right delete-appointment" type="button">Confirm</button>';
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
		 $('#modal').find('.datepicker').datetimepicker({format: 'DD/MM/YYYY',  pickTime: false});
		 	 //this function automatically sets the end date for the appointment 1 hour ahead of the start date
            $(".startpicker").on("dp.hide", function (e) {
                var m = moment(e.date, "DD\MM\YYYY HH:mm");
                $('.endpicker').data("DateTimePicker").setMinDate(e.date);
                $('.endpicker').data("DateTimePicker").setDate(m.add('hours', 1).format('DD\MM\YYYY HH:mm'));
            });
		$("#modal").find("#tabs").tab();
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
save_planner:function(urn){
	$.ajax({ url: helper.baseUrl+'planner/add_record',
	data:{urn:urn, date:$('#planner_date').val(), postcode:$('#planner_address').val() },
	type:"POST",
	dataType:"JSON"
	}).done(function(response){
		if(response.success){
		flashalert.success(response.msg);	
		$('#modal').find('#planner_status').text('This record is in your journey planner. You can remove or reschedule it below').addClass('text-success');
		$('#modal').find('.remove-from-planner').show();
		} else {
		flashalert.danger(response.msg);	
		}
	});
},
remove_from_planner:function(urn){
		$.ajax({ url: helper.baseUrl+'planner/remove_record',
	data:{urn:urn},
	type:"POST",
	dataType:"JSON"
	}).done(function(response){
		if(response.success){
		flashalert.success(response.msg);
		$('#modal').find('#planner_status').text('This record is not in your journey planner. You can add it below').removeClass('text-success');
		$('#modal').find('.remove-from-planner').hide();
		}
	});
},
reset_table:function(){
	modals.default_buttons();
	 $('.modal-title').text('Reset table');
        $('#modal').find('.modal-body').html('<p>This will clear any filters that have been set on the table</p><p>Are you sure you want to reset the table filters?</p>');

        if (!$('#modal').hasClass('in')) {
            modal.show_modal();
        }
},
view_record:function(urn){
	$.ajax({ url:helper.baseUrl+'modals/view_record',
	type:"POST",
	dataType:"JSON",
	data:{urn:urn}
	}).done(function(response){
		modals.view_record_html(response.data);
	});
},
view_record_html:function(data){
	var mheader = "View Record #"+data.urn;
	var mbody = '<ul id="tabs" class="nav nav-tabs" role="tablist"><li class="active"><a role="tab" data-toggle="tab" href="#tab-records">Record</a></li><li><a role="tab" data-toggle="tab" href="#tab-history">History</a></li><li><a role="tab" data-toggle="tab" href="#tab-apps">Appointments</a></li>';
	
	if(data.custom_info.length>0){
	mbody += '<li><a role="tab" data-toggle="tab" href="#tab-custom">'+data.custom_panel_name+'</a></li>';
	}
	if (helper.permissions['planner'] > 0) {
	mbody += '<li><a role="tab" data-toggle="tab" href="#tab-planner">Planner</a></li>';	
	}
	
	mbody += '</ul><div class="tab-content">';
	//records tab
	mbody += '<div role="tabpanel" class="tab-pane active" id="tab-records"><div class="row"><div class="col-sm-6"><h4>Details</h4><table class="table small"><tr><th>Campaign</th><td>'+data.campaign_name+'</td></tr><tr><th>Name</th><td>'+data.name+'</td></tr><tr><th>Ownership</th><td>'+data.ownership+'</td></tr><tr><th>Comments</th><td>'+data.comments+'</td></tr></table></div><div class="col-sm-6"><h4>Status</h4><table class="table small"><tr><th>Record Status</th><td>'+data.status_name+'</td></tr><tr><th>Parked Status</th><td>'+data.parked+'</td></tr><tr><th>Last Outcome</th><td>'+data.outcome+'</td></tr><tr><th>Last Action</th><td>'+data.lastcall+'</td></tr><tr><th>Next Action</th><td>'+data.nextcall+'</td></tr></table></div></div></div>';
	//history tab
	mbody += '<div role="tabpanel" class="tab-pane" id="tab-history">'
	if(data.history.length>0){
	mbody += '<table class="table table-striped table-condensed"><thead><tr><th>Outcome</th><th>Date</th><th>User</th><th>Comments</th></tr></thead><tbody>';
	$.each(data.history,function(k,row){
	mbody += '<tr class="small"><td>'+row.outcome+'</td><td>'+row.contact+'</td><td>'+row.name+'</td><td>'+row.comments+'</td></tr>';
	});
	mbody += '</tbody></table>';
	} else {
	mbody += '<p>This record has not been updated yet</p>';	
	}
	mbody += '</div>'
	//appointments tab	
	mbody += '<div role="tabpanel" class="tab-pane" id="tab-apps">';
	if(data.appointments.length>0){
	mbody += '<table class="table table-striped table-condensed"><thead><tr><th>Date</th><th>Time</th><th>Title</th><th>Set by</th><th>Status</th></tr></thead><tbody>';
	$.each(data.appointments,function(k,row){
		mbody += '<tr class="small"><td>'+row.date+'</td><td>'+row.time+'</td><td>'+row.title+'</td><td>'+row.name+'</td><td>'+row.status+'</td></tr>';
	
	});
	mbody += '</tbody></table>';
	} else {
	mbody += '<p>No appointments have been set</p>';	
	}
	mbody += '</div>';
	
	if(data.custom_info.length>0){
	mbody += '<div role="tabpanel" class="tab-pane" id="tab-custom">';
	//build the custom table
	  var table = "<div class='table-responsive'><table class='table table-striped table-condensed'>";
            var thead, detail_id;
            var tbody = "<tbody>";
            var contents = "";
            $.each(data.custom_info, function (k, detail) {
                tbody += "<tr>";
                thead = "<thead><tr>";
                $.each(detail, function (i, row) {
                    thead += "<th>" + row.name + "</th>";
                    if (row.formatted) {
                        tbody += "<td class='" + row.code + "'>" + row.formatted + "</td>";
                    } else {
                        tbody += "<td class='" + row.code + "'>" + row.value + "</td>";
                    }
                    detail_id = row.id;
                });
	tbody += "</tr>";
	
	});
	  table += thead + '</thead>' + tbody + '<tbody></table></div>';
      mbody += table;
	mbody += '</div>';
	}
	if (helper.permissions['planner'] > 0) {
		var planner_form = "";
		if(data.addresses.length>0){
			planner_form = '<div class="form-group"><label>Select Address</label><br>';
			planner_form += '<select class="selectpicker" data-width="100%" id="planner_address">';
			$.each(data.addresses,function(k,address){
				if(data.planner_postcode==address.postcode){
					var selected = "selected";
				} else {
				var selected = "";	
				}
			planner_form += '<option '+selected+' value="'+address.postcode+'">'+address.address+'</option>';	
			});
			planner_form += '<select></div>';
			
			planner_form += '<div class="form-group"><label>Select Date</label><input value="'+data.planner_date+'" class="form-control datepicker" id="planner_date" placeholder="Choose date..." /></div>';
			planner_form +=	' <button class="marl btn btn-info pull-right save-planner" data-urn="'+data.urn+'" href="#">Save to planner</button> ';
			} else {
			planner_form += '<p class="text-danger">You cannot add this record to the journey planner because it has no address</p>'	
			}
		
		mbody += '<div role="tabpanel" class="tab-pane" id="tab-planner">';
			if(data.planner_id){
				mbody += '<p id="planner_status" class="text-success">This record is in your journey planner. You can remove or reschedule it below</p>';
				mbody += planner_form;
				mbody += ' <button class="btn btn-danger pull-right remove-from-planner" data-urn="'+data.urn+'" href="#">Remove from planner</button> ';
			} else {
			mbody += '<p id="planner_status">This record is not in your journey planner. You can add it below</p>';
			mbody += planner_form;
			mbody += ' <button style="display:none" class="btn btn-danger pull-right remove-from-planner" data-urn="'+data.urn+'" href="#">Remove from planner</button> ';
			}
		
	}
	
	
	 mbody += '</div>';
	var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button> <a class="btn btn-primary pull-right" href="'+helper.baseUrl+'records/detail/'+data.urn+'">View Record</a>';
		modals.load_modal(mheader,mbody,mfooter);
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
