// JavaScript Document
var modals = {
    init: function () {
		 modal_footer = $('#modal').find('.modal-footer');
		 modal_header = $('#modal').find('.modal-title');
		 modal_body = $('#modal').find('.modal-body');
		$(document).on('click', '[data-modal="merge-record"]', function (e) {
            e.preventDefault();
            modals.merge_record($(this).attr('data-urn'),$(this).attr('data-merge-target'));
        });
        $(document).on('click', '[data-modal="view-record"]', function (e) {
            e.preventDefault();
			var clicked_urn = $(this).attr('data-urn');
            setTimeout(function(){ 
			modals.view_record(clicked_urn);
			},500);
        });
		$(document).on('dblclick', '[data-modal="view-record"],[data-modal="view-appointment"]', function (e) {
            e.preventDefault();
           window.location.href= helper.baseUrl+'records/detail/'+$(this).attr('data-urn');
        });
        $(document).on('click', '[data-modal="edit-contact"]', function (e) {
            e.preventDefault();
            modals.contacts.contact_form('edit', $(this).attr('data-id'), 'general');
        });
        $(document).on('click', '[data-modal="add-contact"]', function (e) {
            e.preventDefault();
            modals.contacts.contact_form('add', $(this).attr('data-urn'), 'general');
        });
        $(document).on('click', '[data-modal="edit-company"]', function (e) {
            e.preventDefault();
            modals.companies.company_form('edit', $(this).attr('data-id'), 'general');
        });
        $(document).on('click', '[data-modal="add-company"]', function (e) {
            e.preventDefault();
            modals.companies.company_form('add', $(this).attr('data-urn'), 'general');
        });
        $(document).on('click', '.modal-set-location', function (e) {
            e.preventDefault();
            modals.set_location();
        });
        $(document).on('click', '.save-planner', function (e) {
            e.preventDefault();
            modals.save_planner($(this).attr('data-urn'));
        });
        $(document).on('click', '.remove-from-planner', function (e) {
            e.preventDefault();
            modals.remove_from_planner($(this).attr('data-urn'));
        });
        $(document).on('click', '#save-appointment', function (e) {
            e.preventDefault();
            modals.save_appointment($('#appointment-form').serialize());
        });
        $('#cal-slide-box').on('click', 'a', function (e) {
            e.preventDefault();
            modals.view_appointment($(this).attr('data-id'));
        });
        $(document).on('click', '[data-modal="view-appointment"]', function (e) {
            e.preventDefault();
			var clicked_id = $(this).attr('data-id');
            setTimeout(function(){ 
			modals.view_appointment(clicked_id);
			},500);
            
        });
        $(document).on('click', '[data-modal="edit-appointment"]', function (e) {
            e.preventDefault();
            modals.view_appointment($(this).attr('data-id'), true);
        });
        $(document).on('click', '[data-modal="delete-appointment"]', function (e) {
            e.preventDefault();
            modals.delete_appointment_html($(this).attr('data-id'), true);
        });

        $(document).on('click', '[data-modal="create-appointment"]', function (e) {
            e.preventDefault();
            modals.create_appointment($(this).attr('data-urn'));
        });
        $(document).on('click', '#modal #cancel-add-address', function (e) {
            ;
            e.preventDefault();
            $('#add-appointment-address').hide();
            $('#select-appointment-address').show();
            $('.addresspicker').selectpicker('val', $('#addresspicker option:first').val());
        });
        $(document).on('change', '.addresspicker', function (e) {
            if ($(this).val() == "Other") {
                $('#add-appointment-address').show();
                $('#select-appointment-address').hide();
                $('.addresspicker').val('53');
            } else {
                $('#add-appointment-address').hide();
                $('#select-appointment-address').show();
            }
        });
        $(document).on('click', '#confirm-add-address', function (e) {
            e.preventDefault();
            modals.confirm_other_appointment_address();
        });
		 $(document).on('click', '#load-preview', function () {
            modals.preview_merge();
			modal_body.css('overflow', 'auto');
        });
		 $(document).on('click', '#load-options', function (e) {
         modal_body.css('overflow', 'visible');
        });
		 $(document).on('click', '#start-merge', function (e) {
            e.preventDefault();
            modals.start_merge();
        });
        $(document).on('click', '.delete-appointment', function (e) {
            var cancellation_reason = $('.appointment-cancellation-form').find('textarea[name="cancellation_reason"]').val();
            var id = $('#modal #appointment-id').val();
            if (cancellation_reason.length < 5) {
                flashalert.danger("You must enter a cancellation reason!");
            } else {
                modals.delete_appointment(id, cancellation_reason);
                $('#modal').modal('toggle');
            }
        });
    },
	merge_record:function(urn,target){
		$.ajax({
            url: helper.baseUrl + 'modals/merge_record',
            data: {urn:urn,target:target},
            type: "POST",
            dataType: "HTML"
        }).done(function (html) {
                var mheader = "Merge data";
				var mbody = html;
				var mfooter = '<button class="btn btn-default pull-left" data-modal="view-record" data-urn="' + urn + '" >Back</button> <button class="btn btn-primary pull-right" id="start-merge" disabled>Start Merge</button> ';;
				modals.load_modal(mheader,mbody,mfooter);
				$('[name="source"]').val(urn);
				$('[name="target"]').val(target);
				modal_body.css('padding', '0').css('overflow', 'visible');
        });
	},
	start_merge:function(){
		$.ajax({
            url: helper.baseUrl + 'merge/merge_launch',
            data: $('#merge-form').serialize(),
            type: "POST",
            dataType: "JSON"
        }).done(function (response) {
			if(response.success){
				if(typeof record !== "undefined"){
				record.company_panel.load_panel(record.urn);	
				record.contact_panel.load_panel(record.urn);	
			}
				flashalert.success('Merge Successful');	
				$('#modal').modal('hide');
				
			};
		})
	},
		preview_merge:function(){
		$.ajax({
            url: helper.baseUrl + 'merge/merge_preview',
            data: $('#merge-form').serialize(),
            type: "POST",
            dataType: "JSON"
        }).done(function (response) {
			var contents = '';
			if(response.length==0){
				contents = 'There is nothing to merge at the moment. All fields are in sync';
			} else {
			
			$.each(response,function(type,actions){
				
				$.each(actions,function(action,data){
					var this_type = type;
					if(type=="companies"&&data.length=="1"){
					this_type="company";	
					} else if(type=="contacts"&&data.length=="1"){
					this_type="contact";	
					}
				contents += '<h4>'+data.length+' '+this_type+' will be '+action+'</h4>';
				
				$.each(data,function(k,v){
					contents += '<ul>'
					$.each(v,function(field,value){
						if(field=="telephone_number"){
						field=v.description;
						delete v.description;
						}
					contents += '<li>'+field + ' => ' + value + '</li>';
				});
					contents += '</ul>'
				});
			
			});
			});
			}
			$('#merge-preview').html(contents);
			$('#start-merge').prop('disabled',false);
        });
	},
    save_appointment: function (data) {
        $.ajax({
            url: helper.baseUrl + 'records/save_appointment',
            data: data,
            type: "POST",
            dataType: "JSON"
        }).done(function (response) {
            if (response.success) {
                flashalert.success('Appointment was saved');
                $('.close-modal').trigger('click');
                if (typeof record !== "undefined") {
                    record.appointment_panel.load_appointments();
                }
            } else {
                flashalert.danger(response.msg);
            }
        });
    },
    delete_appointment: function (id, cancellation_reason) {
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
    view_appointment: function (id, edit) {
        $.ajax({
            url: helper.baseUrl + 'modals/view_appointment',
            type: "POST",
            data: {
                id: id
            },
            dataType: 'JSON'
        }).done(function (response) {
            if (response.success) {
                if (edit) {
                    modals.edit_appointment_html(response.data);
					modal_body.css('overflow', 'visible');
                } else {
                    modals.view_appointment_html(response.data);
                }
            } else {
                flashalert.danger(response.msg);
            }
        });
    },
    view_appointment_html: function (data) {
        platform = navigator.platform,
            mapLink = 'http://maps.google.com/';
        if (platform === 'iPad' || platform === 'iPhone' || platform === 'iPod') {
            mapLink = 'comgooglemaps://';
        }

        if (data.attendee_names.length > 0) {
            var attendees = "";
            $.each(data.attendee_names, function (i, val) {
                if (i > 0) {
                    attendees += ", "
                }
                attendees += val;
            });
        }
        var mheader = "Appointment #" + data.appointment_id + " <small>" + data.campaign_name + "</small>";
        var mbody = "<table class='table'><tbody><tr><th>Company</th><td>" + data.coname + "</td></tr><tr><th>Date</th><td>" + data.starttext + "</td></tr><tr><th>Title</th><td>" + data.title + "</td></tr><tr><th>Notes</th><td>" + data.text + "</td></tr><tr><th>Attendees</th><td>" + attendees + "</td></tr><tr><th>Type</th><td>" + data.appointment_type + "</td></tr>";
        if (data.distance && helper.current_postcode) {
            mbody += "<tr><th>Distance</th><td>" + Number(data.distance).toFixed(2) + " Miles from " + helper.current_postcode + "</td></tr>";
        }
        mbody += "</tbody></table>";
        mbody += "This appointment was set by <b>" + data.created_by + "</b> on <b>" + data.date_added + "</b>";
        var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button> <a class="btn btn-primary pull-right" data-modal="edit-appointment" data-id="' + data.appointment_id + '" >Edit Appointment</a> ';
        if (data.urn != $('#urn').val()) {
            mfooter += ' <a class="btn btn-primary pull-right" href="' + helper.baseUrl + 'records/detail/' + data.urn + '">View Record</a>';
        }
        if (getCookie('current_postcode')) {
            mfooter += '<a target="_blank" class="btn btn-info pull-right" href="' + mapLink + '?q=' + data.postcode + '",+UK">View Map</a>';
        }
        if (data.distance && getCookie('current_postcode')) {
            mfooter += '<a target="_blank" class="btn btn-info pull-right" href="' + mapLink + '?zoom=2&saddr=' + helper.current_postcode + '&daddr=' + data.postcode + '">Navigate</a>';
        }
        modals.load_modal(mheader, mbody, mfooter)
    },
    edit_appointment_html: function (data) {
        $.ajax({
            url: helper.baseUrl + 'modals/edit_appointment',
            type: 'POST',
            dataType: 'html',
            data: {
                urn: data.urn
            }
        }).done(function (response) {
            var mheader = "Edit Appointment #" + data.appointment_id;
            var mbody = '<div class="row"><div class="col-lg-12">' + response + '</div></div>';
            var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button> <button class="btn btn-primary pull-right" id="save-appointment" type="button">Save</button> <button class="btn btn-danger pull-right" data-modal="delete-appointment" data-id="' + data.appointment_id + '" type="button">Delete</button>';
            $mbody = $(mbody);
            //check if the appointment address is already in the dropdown and if not, add it.
            var option_exists = false;
            $.each($mbody.find('#addresspicker option'), function () {
                if ($(this).val() == data.address + '|' + data.postcode) {
                    option_exists = true;
                }
            });
            if (!option_exists) {
                $mbody.find('#addresspicker').prepend('<option value="' + data.address + '|' + data.postcode + '">' + data.address + '</option>');
            }
            //cycle through the rest of the fields and set them in the form
            $.each(data, function (k, v) {
                $mbody.find('[name="' + k + '"]').val(v);
                if (k == "type") {
                    $mbody.find('[name="appointment_type_id"]').val(v);
                }
                if (k == "attendees") {
                    $.each(v, function (i, user_id) {
                        $mbody.find('#attendee-select option[value="' + user_id + '"]').prop('selected', true);
                    });
                }
                $mbody.find('#addresspicker option[value="' + data.address + '|' + data.postcode + '"]').prop('selected', true);
            });
            modals.load_modal(mheader, $mbody, mfooter);
			modals.appointment_contacts(data.urn,data.contact_id);
        });
    },
	appointment_contacts:function(urn,contact_id){
		$.ajax({ url: helper.baseUrl + 'appointments/get_contacts',
		data:{urn:urn },
		dataType:"JSON",
		type:"POST",
		beforeSend:function(){
			$('#contact-select').hide();
		},
		error:function(){
			$('#contact-select').parent().append('<p class="text-error">Unable to find contacts</p>');
		},
		}).done(function(result){
			$('#contact-select').show();
			$.each(result,function(k,v){
				var selected = "";
				if(v.id==contact_id){ selected = "selected"; }
				$('#contact-select').append('<option '+selected+' value="'+v.id+'">'+v.name+'</option>');
			});
			$('#contact-select').append('<option value="other">Other</option>');
			$('#contact-select').selectpicker();
		});
			
	},
    create_appointment: function (urn) {
        $.ajax({
            url: helper.baseUrl + 'modals/edit_appointment',
            type: 'POST',
            dataType: 'html',
            data: {
                urn: urn
            }
        }).done(function (response) {
            var mheader = "Create Appointment";
            var mbody = '<div class="row"><div class="col-lg-12">' + response + '</div></div>';
            var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button> <button class="btn btn-primary pull-right" id="save-appointment" type="button">Save</button>';
            modals.load_modal(mheader, mbody, mfooter);
			modals.appointment_contacts(urn);
        });
    },
    appointment_outcome_html: function (id) {
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
        var mbody = '<form class="form-horizontal appointment-cancellation-form" style="padding:0 20px"><div class="row"><div class="col-lg-12"><input type="hidden" id="appointment-id" value="' + id + '" /><div class="form-group"><label>Are you sure you want to cancel this appointment?</label><textarea class="form-control" name="cancellation_reason" style="height:50px" placeholder="Please give a reason for the cancellation"/></textarea></div></div></form>';
        var mfooter = '<button data-modal="edit-appointment" data-id="' + id + '" class="btn btn-default pull-left"  type="button">Back</button> <button class="btn btn-primary pull-right delete-appointment" type="button">Confirm</button>';
        modals.load_modal(mheader, mbody, mfooter);
    },
    confirm_other_appointment_address: function () {
        var new_postcode = $('#modal').find('[name="new_postcode"]').val();
        $.ajax({
            url: helper.baseUrl + 'ajax/validate_postcode',
            data: {
                postcode: new_postcode
            },
            dataType: 'JSON',
            type: 'POST'
        }).done(function (response) {
            //if postcode is valid
            if (response.success) {
                var new_address = "";
                //if the first line of address is complete
                if ($('#modal').find('[name="add1"]').val() != '') {
                    new_address += $('#modal').find('[name="add1"]').val();
                    if ($('#modal').find('[name="add2"]').val() != '') {
                        new_address += ', ' + $('#modal').find('[name="add2"]').val();
                    }
                    if ($('#modal').find('[name="add3"]').val() != '') {
                        new_address += ', ' + $('#modal').find('[name="add3"]').val();
                    }
                    if ($('#modal').find('[name="county"]').val() != '') {
                        new_address += ', ' + $('#modal').find('[name="county"]').val();
                    }
                    if ($('#modal').find('[name="new_postcode"]').val() != '') {
                        new_address += ', ' + response.postcode;
                    }
                    $('#addresspicker').prepend('<option value="' + new_address + '|' + response.postcode + '">' + new_address + '</option>');
                    $('.addresspicker').selectpicker('refresh').selectpicker('val', $('#addresspicker option:first').val());
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
        modal_footer.empty();
    },
    load_modal: function (mheader, mbody, mfooter, fixed_modal) {
		if(fixed_modal&&$("#modal").hasClass('ui-draggable')){
				$("#modal").draggable('disable')
		} else {
			$("#modal").draggable({
    handle: ".modal-header,.modal-footer"
			});
		modals.set_size();
				
		}
		
        modal_body.css('padding', '20px');
        modal_header.html(mheader);
       modal_body.html(mbody);
        modal_footer.html(mfooter);
        if (!$('#modal').hasClass('in')) {
            modals.show_modal();
        }
        $('#modal').find('.selectpicker').selectpicker();
        $('#modal').find('.tt').tooltip();
        $('#modal').find('.datetime').datetimepicker({
            format: 'DD/MM/YYYY HH:mm'
        });
        $('#modal').find('.dateonly').datetimepicker({
            format: 'DD/MM/YYYY',
            pickTime: false
        });
		$('#modal').find('.dateyears').datetimepicker({
        pickTime: false,
        viewMode: 'years',
        format: 'DD/MM/YYYY'
    	});
        //this function automatically sets the end date for the appointment 1 hour ahead of the start date
        $(".startpicker").on("dp.hide", function (e) {
            var m = moment(e.date, "DD\MM\YYYY HH:mm");
            $('.endpicker').data("DateTimePicker").setMinDate(e.date);
            $('.endpicker').data("DateTimePicker").setDate(m.add('hours', 1).format('DD\MM\YYYY HH:mm'));
        });
        $("#modal").find("#tabs").tab();
    },
	set_size:function(){
		//this will make the modals mobile responsive :)
		if($('#modal').hasClass('in')){
			var height = $(window).height()-20;
			var mheight = $('.modal-dialog').height();
			if(mheight>height){
				modal_body.css('height', 'auto');
				$('body').removeClass('modal-open');
				$('#modal').css('position','absolute');
				$('.container-fluid').css('height',mheight+50+'px').css('overflow','hidden');
			} else {
			$('#modal').css('position','fixed');
			$('body').addClass('modal-open');
			$('.container-fluid').css('height','100%').css('overflow','auto');
		}
		}
	},
    columns: function (columns) {
        modals.default_buttons();
        modal_header.text('Select columns to display');
        modal_body.html($form);

        if (!$('#modal').hasClass('in')) {
            modals.show_modal();
        }
    },
    set_location: function () {

        modals.default_buttons();
        modal_header.text('Set location');
         modal_body.html('<p>You must set a location to calculate distances and journey times</p><div class="form-group"><label>Enter Postcode</label><div class="input-group"><input type="text" class="form-control current_postcode_input" placeholder="Enter a postcode..."><div class="input-group-addon pointer btn locate-postcode"><span class="glyphicon glyphicon-map-marker"></span> Use my location</div></div>');
        $(".confirm-modal").off('click');
        $('.confirm-modal').on('click', function (e) {
            var postcode_saved = location.store_location($('.current_postcode_input').val());
            if (postcode_saved) {
                $('#modal').modal('toggle');
            }
        });
        if (!$('#modal').hasClass('in')) {
            modal.show_modal();
        }

    },
    save_planner: function (urn) {
        $.ajax({
            url: helper.baseUrl + 'planner/add_record',
            data: {
                urn: urn,
                date: $('#planner_date').val(),
                postcode: $('#planner_address').val()
            },
            type: "POST",
            dataType: "JSON"
        }).done(function (response) {
            if (response.success) {
                flashalert.success(response.msg);
                $('#modal').find('#planner_status').text('This record is in your journey planner. You can remove or reschedule it below').addClass('text-success');
                $('#modal').find('.remove-from-planner').show();
            } else {
                flashalert.danger(response.msg);
            }
        });
    },
    remove_from_planner: function (urn) {
        $.ajax({
            url: helper.baseUrl + 'planner/remove_record',
            data: {
                urn: urn
            },
            type: "POST",
            dataType: "JSON"
        }).done(function (response) {
            if (response.success) {
                flashalert.success(response.msg);
                $('#modal').find('#planner_status').text('This record is not in your journey planner. You can add it below').removeClass('text-success');
                $('#modal').find('.remove-from-planner').hide();
            }
        });
    },
    reset_table: function () {
        modals.default_buttons();
        modal_header.text('Reset table');
         modal_body.html('<p>This will clear any filters that have been set on the table</p><p>Are you sure you want to reset the table filters?</p>');

        if (!$('#modal').hasClass('in')) {
            modal.show_modal();
        }
    },
    view_record: function (urn) {
        $.ajax({
            url: helper.baseUrl + 'modals/view_record',
            type: "POST",
            dataType: "JSON",
            data: {
                urn: urn
            }
        }).done(function (response) {
            modals.view_record_html(response.data);
        });
    },
    default_buttons: function () {
        modal_footer.empty();
      modal_footer.append('<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>');
        modal_footer.append('<button class="btn btn-primary confirm-modal" type="button">Confirm</button>');
    },
    update_footer: function (content) {
       modal_footer.empty();
       modal_footer.html(content);
    },
    view_record_html: function (data) {
        var mheader = "View Record #" + data.urn;
        var mbody = '<ul id="tabs" class="nav nav-tabs" role="tablist"><li class="active"><a role="tab" data-toggle="tab" href="#tab-records">Record</a></li><li><a role="tab" data-toggle="tab" href="#tab-history">History</a></li><li><a role="tab" data-toggle="tab" href="#tab-apps">Appointments</a></li>';

        if (data.custom_info.length > 0) {
            mbody += '<li><a role="tab" data-toggle="tab" href="#tab-custom">' + data.custom_panel_name + '</a></li>';
        }
        if (helper.permissions['planner'] > 0) {
            mbody += '<li><a role="tab" data-toggle="tab" href="#tab-planner">Planner</a></li>';
        }

        mbody += '</ul><div class="tab-content">';
        //records tab
        mbody += '<div role="tabpanel" class="tab-pane active" id="tab-records"><div class="row"><div class="col-sm-6"><h4>Details</h4><table class="table small"><tr><th>Campaign</th><td>' + data.campaign_name + '</td></tr><tr><th>Name</th><td>' + data.name + '</td></tr><tr><th>Ownership</th><td>' + data.ownership + '</td></tr><tr><th>Comments</th><td>' + data.comments + '</td></tr></table></div><div class="col-sm-6"><h4>Status</h4><table class="table small"><tr><th>Record Status</th><td>' + data.status_name + '</td></tr><tr><th>Parked Status</th><td>' + data.parked + '</td></tr><tr><th>Last Outcome</th><td>' + data.outcome + '</td></tr><tr><th>Last Action</th><td>' + data.lastcall + '</td></tr><tr><th>Next Action</th><td>' + data.nextcall + '</td></tr></table></div></div></div>';
        //history tab
        mbody += '<div role="tabpanel" class="tab-pane" id="tab-history">'
        if (data.history.length > 0) {
            mbody += '<table class="table table-striped table-condensed"><thead><tr><th>Outcome</th><th>Date</th><th>User</th><th>Comments</th></tr></thead><tbody>';
            $.each(data.history, function (k, row) {
                mbody += '<tr class="small"><td>' + row.outcome + '</td><td>' + row.contact + '</td><td>' + row.name + '</td><td>' + row.comments + '</td></tr>';
            });
            mbody += '</tbody></table>';
        } else {
            mbody += '<p>This record has not been updated yet</p>';
        }
        mbody += '</div>'
        //appointments tab
        mbody += '<div role="tabpanel" class="tab-pane" id="tab-apps">';
        if (data.appointments.length > 0) {
            mbody += '<table class="table table-striped table-condensed"><thead><tr><th>Date</th><th>Time</th><th>Title</th><th>Set by</th><th>Status</th></tr></thead><tbody>';
            $.each(data.appointments, function (k, row) {
                mbody += '<tr class="small"><td>' + row.date + '</td><td>' + row.time + '</td><td>' + row.title + '</td><td>' + row.name + '</td><td>' + row.status + '</td></tr>';

            });
            mbody += '</tbody></table>';
        } else {
            mbody += '<p>No appointments have been set</p>';
        }
        mbody += '</div>';

        if (data.custom_info.length > 0) {
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
            if (data.addresses.length > 0) {
                planner_form = '<div class="form-group"><label>Select Address</label><br>';
                planner_form += '<select class="selectpicker" data-width="100%" id="planner_address">';
                $.each(data.addresses, function (k, address) {
                    if (data.planner_postcode == address.postcode) {
                        var selected = "selected";
                    } else {
                        var selected = "";
                    }
                    planner_form += '<option ' + selected + ' value="' + address.postcode + '">' + address.address + '</option>';
                });
                planner_form += '<select></div>';

                planner_form += '<div class="form-group"><label>Select Date</label><input value="' + data.planner_date + '" class="form-control dateonly" id="planner_date" placeholder="Choose date..." /></div>';
                planner_form += ' <button class="marl btn btn-info pull-right save-planner" data-urn="' + data.urn + '" href="#">Save to planner</button> ';
            } else {
                planner_form += '<p class="text-danger">You cannot add this record to the journey planner because it has no address</p>'
            }

            mbody += '<div role="tabpanel" class="tab-pane" id="tab-planner">';
            if (data.planner_id) {
                mbody += '<p id="planner_status" class="text-success">This record is in your journey planner. You can remove or reschedule it below</p>';
                mbody += planner_form;
                mbody += ' <button class="btn btn-danger pull-right remove-from-planner" data-urn="' + data.urn + '" href="#">Remove from planner</button> ';
            } else {
                mbody += '<p id="planner_status">This record is not in your journey planner. You can add it below</p>';
                mbody += planner_form;
                mbody += ' <button style="display:none" class="btn btn-danger pull-right remove-from-planner" data-urn="' + data.urn + '" href="#">Remove from planner</button> ';
            }

        }


        mbody += '</div>';
		merge_btn = "";
		if(typeof record !== "undefined"){
		merge_btn = ' <button class="btn btn-info pull-right" data-modal="merge-record" data-urn="'+data.urn+'" data-merge-target="'+record.urn+'">Merge</button>';	
		}
        var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button> <a class="btn btn-primary pull-right" href="' + helper.baseUrl + 'records/detail/' + data.urn + '">View Record</a>' + merge_btn;
        modals.load_modal(mheader, mbody, mfooter);
		modal_body.css('padding:0px');
    },


    contacts: {

        init: function () {
            $(document).on('click', '.save-contact-general', function (e) {
                e.preventDefault();
                modals.contacts.save_contact();
            });
            /* loads the form for a new phone or address to be added*/
            $(document).on('click', '.contact-add-item', function (e) {
                e.preventDefault();
                modals.contacts.new_item_form();
            });
            /*save the new phone or address*/
            $(document).on('click', '.save-contact-phone,.save-contact-address', function (e) {
                e.preventDefault();
                var action = $(this).attr('data-action');
                modals.contacts.save_item(action);
            });
            /*when a tab is changed we should reset the tab content*/
            $(document).on('click', '#modal .nav-tabs .phone-tab a,#modal .nav-tabs .address-tab a', function (e) {
                e.preventDefault();
                var tabname = $(this).attr('href');
                modals.contacts.change_tab(tabname);
            });
            /*initialize the delete item buttons for phone */
            $(document).on('click', '[data-modal="delete-contact-phone"]', function (e) {
                e.preventDefault();
                var id = $(this).attr('data-id');
                var cid = $(this).attr('contact-id');
                modals.contacts.confirm_delete_phone(id, cid);
            });
            /*initialize the delete item buttons for address*/
            $(document).on('click', '[data-modal="delete-contact-address"]', function (e) {
                e.preventDefault();
                var id = $(this).attr('data-id');
                var cid = $(this).attr('contact-id');
                modals.contacts.confirm_delete_address(id, cid);
            });
            /* go back to the phone tab for the contact if they cancel the delete action */
            $(document).on('click', '.cancel-delete-phone', function (e) {
                e.preventDefault();
                var id = $(this).attr('data-id');
                modals.contacts.contact_form('edit', id, 'phone');
            });
            /* go back to the ddress for the contact if they cancel the delete action */
            $(document).on('click', '.cancel-delete-address', function (e) {
                e.preventDefault();
                var id = $(this).attr('data-id');
                modals.contacts.contact_form('edit', id, 'address');
            });
            $(document).on('click', '.contact-item-btn', function (e) {
                e.preventDefault();
                var id = $(this).attr('data-id');
                var action = $(this).attr('data-action');
                modals.contacts.edit_item_form(id, action);
            });
            /*initialize the cancel button on the add/edit contact phone/address form*/
            $(document).on('click', '.cancel-add-item', function (e) {
                e.preventDefault();
                $tab = $('#modal').find('.tab-pane.active');
                $tab.find('form').hide();
                $tab.find('.table-container').show();
                //swap the buttons back
                modals.update_footer('<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>');
            });
        },
        edit_item_form: function (id, action) {
            $tab = $('#modal').find('.tab-pane.active');
            $tab.find('.item-id').val(id);
            if (action == "edit_address") {
                var page = "get_contact_address";
            } else if (action == "edit_phone") {
                var page = "get_contact_number";
            }
            $.ajax({
                url: helper.baseUrl + 'ajax/' + page,
                type: "POST",
                dataType: "JSON",
                data: {
                    id: id
                }
            }).done(function (response) {
                var mfooter = '<button class="btn btn-default cancel-add-item pull-left" type="button">Cancel</button>';
                if (action == "edit_phone") {
                    mfooter += '<button type="submit" class="btn btn-primary save-contact-phone" data-action="edit_phone">Save Number</button>';
                } else if (action == "edit_address") {
                    mfooter += '<button type="submit" class="btn btn-primary save-contact-address" data-action="edit_address">Save Address</button>';
                }
                modals.update_footer(mfooter);

                if (response.success) {
                    $.each(response, function (key, val) {
                        $tab.find('form input[name="' + key + '"]').val(val);
                        $tab.find('select[name="' + key + '"]').selectpicker('val', val);
                    });
                    $tab.find('.table-container').hide();
                    $tab.find('form').show();
                    if (action == "edit_phone") {
                        //Set the telephone number input as a number
                        $tab.find('form').find('input[name="telephone_number"]').numeric();

                        var tps_option = $tab.find('select[name="tps"]').val();
                        var contact_id = $tab.find('form').find('input[name="contact_id"]').val();
                        var telephone_id = $tab.find('form input[name="telephone_id"]').val();
                        var telephone_number = $tab.find('form').find('input[name="telephone_number"]').val();
                        var tps = "";
                        if (tps_option.length == 0) {
                            tps = "<span class='glyphicon glyphicon-question-sign black edit-tps-btn tt pointer' item-contact-id='" + contact_id + "' item-number-id='" + telephone_id + "' item-number='" + telephone_number + "' data-toggle='tooltip' data-placement='right' title='TPS Status is unknown. Click to check it'></span>";
                        }
                        else if (tps_option == 1) {
                            tps = "<span class='glyphicon glyphicon-exclamation-sign red tt' data-toggle='tooltip' data-placement='right' title='This number IS TPS registered'></span>";
                        }
                        else {
                            tps = "<span class='glyphicon glyphicon-ok-sign green tt' data-toggle='tooltip' data-placement='right' title='This number is NOT TPS registerd'></span>";
                        }
                        $tab.find('.edit-tps').html(tps);
                    }
                } else {
                    flashalert.danger(response.msg);
                }
            });

        },
        confirm_delete_phone: function (phone_id, contact_id) {
            var mheader = "Delete phone number";
            var mbody = "Are you sure you want to delete this number?";
            var mfooter = '<button class="btn btn-default pull-left cancel-delete-phone" data-id="' + contact_id + '" type="button">Cancel</button> <button class="btn btn-danger confirm-delete" type="button">Delete</button>';

            modals.load_modal(mheader, mbody, mfooter);
            $('.confirm-delete').click(function () {
                modals.contacts.delete_item(phone_id, contact_id, 'delete_phone');
            });
        },
        confirm_delete_address: function (address_id, contact_id) {
            var mheader = "Delete contact address";
            var mbody = "Are you sure you want to delete this address?";
            var mfooter = '<button class="btn btn-default pull-left cancel-delete-address" data-id="' + contact_id + '" type="button">Cancel</button> <button class="btn btn-danger confirm-delete" type="button">Delete</button>';

            modals.load_modal(mheader, mbody, mfooter);
            $('.confirm-delete').click(function () {
                modals.contacts.delete_item(address_id, contact_id, 'delete_address');
            });
        },
        delete_item: function (id, contact_id, action) {
            $.ajax({
                url: helper.baseUrl + 'ajax/' + action,
                type: "POST",
                dataType: "JSON",
                data: {
                    id: id,
                    contact: contact_id
                }
            }).done(function (response) {
                modals.contacts.contact_form('edit', contact_id, response.type);
                flashalert.success("Contact details were updated");
                if (typeof record !== "undefined") {
                    record.contact_panel.load_panel(record.urn, response.id);
                }
            });
        },
        new_item_form: function () {
            $tab = $('#modal').find('.tab-pane.active');
            var type = $tab.attr('id');
            $tab.find('.table-container').hide();
            $tab.find('form')[0].reset();
            $tab.find('form').show();
            //reset the item id
            $tab.find('.item-id').val('');

            //Set the telephone number input as a number
            $tab.find('form').find('input[name="telephone_number"]').numeric();
            $tab.find('.edit-tps').html("");
            //this will need changing for a back button
            var mfooter = '<button class="btn btn-default cancel-add-item pull-left" type="button">Cancel</button>';
            if (type == "phone") {
                mfooter += '<button type="submit" class="btn btn-primary save-contact-phone" data-action="add_phone">Add Number</button>';
            } else if (type == "address") {
                mfooter += '<button type="submit" class="btn btn-primary save-contact-address" data-action="add_address">Add Address</button>';
            }
            modals.update_footer(mfooter);

        },
        save_item: function (action) {
            $.ajax({
                url: helper.baseUrl + 'ajax/' + action,
                type: "POST",
                dataType: "JSON",
                data: $('#modal .tab-content .tab-pane.active').find('form').serialize()
            }).done(function (response) {
                if (response.success) {
                    modals.contacts.load_tabs(response.id, response.type);
                    if (typeof record !== "undefined") {
                        record.contact_panel.load_panel(record.urn, response.id);
                    }
                } else {
                    flashalert.danger(response.msg);
                }
            });
        },
        contact_form: function (type, id, tab) {
            $.ajax({
                url: helper.baseUrl + 'modals/load_contact_form',
                type: "POST",
                dataType: "HTML"
            }).done(function (response) {
                if (type == "edit") {
                    var mheader = "Edit contact";
                } else {
                    var mheader = "Create contact";
                }

                $mbody = $(response);

                if (type == "edit") {
                    $mbody.find('.tab-alert').hide();
                    $mbody.find('tbody').empty();
                    $mbody.find('.phone-tab,.address-tab').show();
                    $mbody.find('input[name="contact_id"]').val(id);
                    var mfooter = "";
                } else {
                    $mbody.find('input[name="urn"]').val(id);
                    $mbody.find('.phone-tab,.address-tab').hide();
                    $mbody.find('.tab-alert').show();
                    $mbody.find('.table-container').hide();
                    var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button><button type="submit" class="btn btn-primary save-contact-general">Save changes</button>';
                }

                modals.load_modal(mheader, $mbody, mfooter);
                //dont want padding with tabs
               modal_body.css('padding', '0px');
                if (type == "edit") {
                    modals.contacts.load_tabs(id, tab);
                }

            });
        },
        load_tabs: function (id, item_form) {
            var $panel = $('#modal');
            if (item_form !== "general") {
	 var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>';
                $panel.find('#' + item_form + ' form').hide();
                $panel.find('#' + item_form + ' .table-container').show();
            } else {
							   var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button><button type="submit" class="btn btn-primary save-contact-general">Save changes</button>';
                $panel.find('#phone form, #address form').hide();
                $panel.find('#phone .table-container,#address .table-container').show();
				$panel.find('.save-contact-general').remove();
            }
			  modals.update_footer(mfooter);
            $.ajax({
                url: helper.baseUrl + "ajax/get_contact",
                type: "POST",
                dataType: "JSON",
                data: {
                    id: id
                }
            }).done(function (response) {
                if (response.success) {

                    $.each(response.data.general, function (key, val) {
                        $panel.find('#general [name="' + key + '"]').val(val);
                    });

                    if (response.data.telephone) {
                        $panel.find('#phone tbody').empty();
                        $panel.find('#phone .table-container,#phone .table-container table').show();
                        $panel.find('#phone .none-found').hide();
                        $.each(response.data.telephone, function (key, val) {
                            if (val.tel_tps == "0") {
                                var $tps = "<span style='color:green' class='glyphicon glyphicon-ok-sign tt'  data-toggle='tooltip' data-placement='right' title='This number is NOT TPS registerd'></span>";
                            } else if (val.tel_tps == "1") {
                                var $tps = "<span style='color:red' class='glyphicon glyphicon-exclamation-sign tt'  data-toggle='tooltip' data-placement='right' title='This number IS TPS registered'></span>";
                            } else {
                                var $tps = "<span class='glyphicon glyphicon-question-sign tt'  data-toggle='tooltip' data-placement='right' title='TPS Status is unknown'></span>"
                            }
                            $phone = "<tr><td>" + val.tel_name + "</td><td>" + val.tel_num + "</td><td>" + $tps + "</td><td><span class='glyphicon glyphicon-trash pointer pull-right' data-modal='delete-contact-phone' contact-id='" + response.data.general.contact_id + "' data-id='" + val.tel_id + "'></span><span class='glyphicon glyphicon-pencil pointer pull-right contact-item-btn' data-action='edit_phone' data-id='" + val.tel_id + "'></span></td></tr>";
                            $panel.find('#phone tbody').append($phone);
                        });
                    } else {
                        $panel.find('#phone .table-container table').hide();
                        $panel.find('#phone .none-found').show();
                    }
                    if (response.data.address) {
                        $panel.find('#address tbody').empty();
                        $panel.find('#address .table-container, #address .table-container table').show();
                        $panel.find('#address .none-found').hide();
                        $.each(response.data.address, function (key, val) {
                            if (val.primary == 1) {
                                var $primary = "<span class='glyphicon glyphicon-ok-sign'></span>";
                            } else {
                                $primary = "";
                            }
                            $address = "<tr><td>" + val.add1 + "</td><td>" + val.postcode + "</td><td>" + $primary + "</td><td><span class='glyphicon glyphicon-trash pointer pull-right del-item-btn' data-modal='delete-contact-address' contact-id='" + response.data.general.contact_id + "' data-id='" + val.address_id + "'></span><span class='glyphicon glyphicon-pencil pointer pull-right contact-item-btn' data-action='edit_address' data-id='" + val.address_id + "'></span></td></tr>"
                            $panel.find('#address tbody').append($address);
                        });
                    } else {
                        $panel.find('#address .table-container table').hide();
                        $panel.find('#address .none-found').show();
                    }
                }
                $('.tt').tooltip();
                $panel.find('.tab[href="#' + item_form + '"]').tab('show');
            });

        },
        save_contact: function () {
            var $form = $('#modal #general').find('form');
            if ($form.find('input[name="contact_id"]').val() == "") {
                var action = "add_contact";
            } else {
                var action = "save_contact";
            }
            $.ajax({
                url: helper.baseUrl + "ajax/" + action,
                type: "POST",
                dataType: "JSON",
                data: $form.serialize()
            }).done(function (response) {
                flashalert.success("Contact details saved");
                //change the add box to an edit box
                if (action == "add_contact") {
                    $('#modal').find('input[name="contact_id"]').val(response.id);
                    $('.phone-tab,.address-tab').show();
                    $('#phone,#address').find('.table-container table').hide();
                    $('.tab-alert').hide();
                }
                record.contact_panel.load_panel(record.urn, response.id);
            });

        },
        change_tab: function (tab) {
            modals.clear_footer();
            var buttons = "";
            if (tab == "#general") {
                buttons = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button><button type="submit" class="btn btn-primary save-contact-general">Save changes</button>';
                modals.update_footer(buttons);
            } else {
                buttons = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>';
                $('#modal').find('.table-container').show();
                $('#modal').find('.contact-phone-form,.contact-address-form').hide();
                modals.update_footer(buttons);
            }

        }


    },

    companies: {
        init: function () {
            $(document).on('click', '.save-company-general', function (e) {
                e.preventDefault();
                modals.companies.save_company();
            });
            /* loads the form for a new phone or address to be added*/
            $(document).on('click', '.company-add-item', function (e) {
                e.preventDefault();
                modals.companies.new_item_form();
            });
            /*save the new phone or address*/
            $(document).on('click', '.save-company-phone,.save-company-address', function (e) {
                e.preventDefault();
                var action = $(this).attr('data-action');
                modals.companies.save_item(action);
            });
            
            /*initialize the delete item buttons for phone */
            $(document).on('click', '[data-modal="delete-company-phone"]', function (e) {
                e.preventDefault();
                var id = $(this).attr('data-id');
                var cid = $(this).attr('company-id');
                modals.companies.confirm_delete_phone(id, cid);
            });
            /*initialize the delete item buttons for address*/
            $(document).on('click', '[data-modal="delete-company-address"]', function (e) {
                e.preventDefault();
                var id = $(this).attr('data-id');
                var cid = $(this).attr('company-id');
                modals.companies.confirm_delete_address(id, cid);
            });
            /* go back to the phone tab for the company if they cancel the delete action */
            $(document).on('click', '.cancel-delete-cophone', function (e) {
                e.preventDefault();
                var id = $(this).attr('data-id');
                modals.companies.company_form('edit', id, 'phone');
            });
            /* go back to the ddress for the company if they cancel the delete action */
            $(document).on('click', '.cancel-delete-coaddress', function (e) {
                e.preventDefault();
                var id = $(this).attr('data-id');
                modals.companies.company_form('edit', id, 'address');
            });
            $(document).on('click', '.company-item-btn', function (e) {
                e.preventDefault();
                var id = $(this).attr('data-id');
                var action = $(this).attr('data-action');
                modals.companies.edit_item_form(id, action);
            });
            /*initialize the cancel button on the add/edit company phone/address form*/
            $(document).on('click', '.cancel-add-item', function (e) {
                e.preventDefault();
                $tab = $('#modal').find('.tab-pane.active');
                $tab.find('form').hide();
                $tab.find('.table-container').show();
                //swap the buttons back
                modals.update_footer('<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>');
            });
        },
        edit_item_form: function (id, action) {
            $tab = $('#modal').find('.tab-pane.active');
            $tab.find('.item-id').val(id);
            if (action == "edit_coaddress") {
                var page = "get_company_address";
            } else if (action == "edit_cophone") {
                var page = "get_company_number";
            }
            $.ajax({
                url: helper.baseUrl + 'ajax/' + page,
                type: "POST",
                dataType: "JSON",
                data: {
                    id: id
                }
            }).done(function (response) {
                var mfooter = '<button class="btn btn-default cancel-add-item pull-left" type="button">Cancel</button>';
                if (action == "edit_cophone") {
                    mfooter += '<button type="submit" class="btn btn-primary save-company-phone" data-action="edit_cophone">Save Number</button>';
                } else if (action == "edit_coaddress") {
                    mfooter += '<button type="submit" class="btn btn-primary save-company-address" data-action="edit_coaddress">Save Address</button>';
                }
                modals.update_footer(mfooter);

                if (response.success) {
                    $.each(response, function (key, val) {
                        $tab.find('form input[name="' + key + '"]').val(val);
                        $tab.find('select[name="' + key + '"]').selectpicker('val', val);
                    });
                    $tab.find('.table-container').hide();
                    $tab.find('form').show();
                    if (action == "edit_cophone") {
                        //Set the telephone number input as a number
                        $tab.find('form').find('input[name="telephone_number"]').numeric();

                        var tps_option = $tab.find('select[name="ctps"]').val();
                        var company_id = $tab.find('form').find('input[name="company_id"]').val();
                        var telephone_id = $tab.find('form input[name="telephone_id"]').val();
                        var telephone_number = $tab.find('form').find('input[name="telephone_number"]').val();
                        var tps = "";
                        if (tps_option.length == 0) {
                            tps = "<span class='glyphicon glyphicon-question-sign black edit-tps-btn tt pointer' item-company-id='" + company_id + "' item-number-id='" + telephone_id + "' item-number='" + telephone_number + "' data-toggle='tooltip' data-placement='right' title='TPS Status is unknown. Click to check it'></span>";
                        }
                        else if (tps_option == 1) {
                            tps = "<span class='glyphicon glyphicon-exclamation-sign red tt' data-toggle='tooltip' data-placement='right' title='This number IS TPS registered'></span>";
                        }
                        else {
                            tps = "<span class='glyphicon glyphicon-ok-sign green tt' data-toggle='tooltip' data-placement='right' title='This number is NOT TPS registerd'></span>";
                        }
                        $tab.find('.edit-tps').html(tps);
                    }
                } else {
                    flashalert.danger(response.msg);
                }
            });

        },
        confirm_delete_phone: function (phone_id, company_id) {
            var mheader = "Delete phone number";
            var mbody = "Are you sure you want to delete this number?";
            var mfooter = '<button class="btn btn-default pull-left cancel-delete-phone" data-id="' + company_id + '" type="button">Cancel</button> <button class="btn btn-danger confirm-delete" type="button">Delete</button>';

            modals.load_modal(mheader, mbody, mfooter);
            $('.confirm-delete').click(function () {
                modals.companies.delete_item(phone_id, company_id, 'delete_cophone');
            });
        },
        confirm_delete_address: function (address_id, company_id) {
            var mheader = "Delete company address";
            var mbody = "Are you sure you want to delete this address?";
            var mfooter = '<button class="btn btn-default pull-left cancel-delete-address" data-id="' + company_id + '" type="button">Cancel</button> <button class="btn btn-danger confirm-delete" type="button">Delete</button>';

            modals.load_modal(mheader, mbody, mfooter);
            $('.confirm-delete').click(function () {
                modals.companies.delete_item(address_id, company_id, 'delete_coaddress');
            });
        },
        delete_item: function (id, company_id, action) {
            $.ajax({
                url: helper.baseUrl + 'ajax/' + action,
                type: "POST",
                dataType: "JSON",
                data: {
                    id: id,
                    company: company_id
                }
            }).done(function (response) {
                modals.companies.company_form('edit', company_id, response.type);
                flashalert.success("Company details were updated");
                if (typeof record !== "undefined") {
                    record.company_panel.load_panel(record.urn, response.id);
                }
            });
        },
        new_item_form: function () {
            $tab = $('#modal').find('.tab-pane.active');
            var type = $tab.attr('id');
            $tab.find('.table-container').hide();
            $tab.find('form')[0].reset();
            $tab.find('form').show();
            //reset the item id
            $tab.find('.item-id').val('');

            //Set the telephone number input as a number
            $tab.find('form').find('input[name="telephone_number"]').numeric();
            $tab.find('.edit-tps').html("");
            //this will need changing for a back button
            var mfooter = '<button class="btn btn-default cancel-add-item pull-left" type="button">Cancel</button>';
            if (type == "phone") {
                mfooter += '<button type="submit" class="btn btn-primary save-company-phone" data-action="add_cophone">Add Number</button>';
            } else if (type == "address") {
                mfooter += '<button type="submit" class="btn btn-primary save-company-address" data-action="add_coaddress">Add Address</button>';
            }
            modals.update_footer(mfooter);

        },
        save_item: function (action) {
            $.ajax({
                url: helper.baseUrl + 'ajax/' + action,
                type: "POST",
                dataType: "JSON",
                data: $('#modal .tab-content .tab-pane.active').find('form').serialize()
            }).done(function (response) {
                if (response.success) {
                    modals.companies.load_tabs(response.id, response.type);
                    if (typeof record !== "undefined") {
                        record.company_panel.load_panel(record.urn, response.id);
                    }
                } else {
                    flashalert.danger(response.msg);
                }
            });
        },
        company_form: function (type, id, tab) {
            $.ajax({
                url: helper.baseUrl + 'modals/load_company_form',
                type: "POST",
                dataType: "HTML"
            }).done(function (response) {
                if (type == "edit") {
                    var mheader = "Edit company";
                } else {
                    var mheader = "Create company";
                }

                $mbody = $(response);

                if (type == "edit") {
                    $mbody.find('.tab-alert').hide();
                    $mbody.find('tbody').empty();
                    $mbody.find('.phone-tab,.address-tab').show();
                    $mbody.find('input[name="company_id"]').val(id);
                    var mfooter = "";
                } else {
                    $mbody.find('input[name="urn"]').val(id);
                    $mbody.find('.phone-tab,.address-tab').hide();
                    $mbody.find('.tab-alert').show();
                    $mbody.find('.table-container').hide();
                    var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button><button type="submit" class="btn btn-primary save-company-general">Save changes</button>';
                }

                modals.load_modal(mheader, $mbody, mfooter);
                //dont want padding with tabs
               modal_body.css('padding', '0px');
                if (type == "edit") {
                    modals.companies.load_tabs(id, tab);
                }					

            });
        },
        load_tabs: function (id, item_form) {
            var $panel = $('#modal');
            if (item_form !== "general") {
				 var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>';
                $panel.find('#' + item_form + ' form').hide();
                $panel.find('#' + item_form + ' .table-container').show();
            } else {
			var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button><button type="submit" class="btn btn-primary save-company-general">Save changes</button>';
                $panel.find('#phone form, #address form').hide();
                $panel.find('#phone .table-container,#address .table-container').show();
            }
			modals.update_footer(mfooter);
            $.ajax({
                url: helper.baseUrl + "ajax/get_company",
                type: "POST",
                dataType: "JSON",
                data: {
                    id: id
                }
            }).done(function (response) {
                if (response.success) {
                    $.each(response.data.general, function (key, val) {
                        $panel.find('#general [name="' + key + '"]').val(val);
                    });

                    if (response.data.telephone) {
                        $panel.find('#phone tbody').empty();
                        $panel.find('#phone .table-container,#phone .table-container table').show();
                        $panel.find('#phone .none-found').hide();
                        $.each(response.data.telephone, function (key, val) {
                            if (val.tel_tps == "0") {
                                var $tps = "<span style='color:green' class='glyphicon glyphicon-ok-sign tt'  data-toggle='tooltip' data-placement='right' title='This number is NOT TPS registerd'></span>";
                            } else if (val.tel_tps == "1") {
                                var $tps = "<span style='color:red' class='glyphicon glyphicon-exclamation-sign tt'  data-toggle='tooltip' data-placement='right' title='This number IS TPS registered'></span>";
                            } else {
                                var $tps = "<span class='glyphicon glyphicon-question-sign tt'  data-toggle='tooltip' data-placement='right' title='TPS Status is unknown'></span>"
                            }
                            $phone = "<tr><td>" + val.tel_name + "</td><td>" + val.tel_num + "</td><td>" + $tps + "</td><td><span class='glyphicon glyphicon-trash pointer pull-right' data-modal='delete-company-phone' company-id='" + response.data.general.company_id + "' data-id='" + val.tel_id + "'></span><span class='glyphicon glyphicon-pencil pointer pull-right company-item-btn' data-action='edit_cophone' data-id='" + val.tel_id + "'></span></td></tr>";
                            $panel.find('#phone tbody').append($phone);
                        });
                    } else {
                        $panel.find('#phone .table-container table').hide();
                        $panel.find('#phone .none-found').show();
                    }
                    if (response.data.address) {
                        $panel.find('#address tbody').empty();
                        $panel.find('#address .table-container, #address .table-container table').show();
                        $panel.find('#address .none-found').hide();
                        $.each(response.data.address, function (key, val) {
                            if (val.primary == 1) {
                                var $primary = "<span class='glyphicon glyphicon-ok-sign'></span>";
                            } else {
                                $primary = "";
                            }
                            $address = "<tr><td>" + val.add1 + "</td><td>" + val.postcode + "</td><td>" + $primary + "</td><td><span class='glyphicon glyphicon-trash pointer pull-right del-item-btn' data-modal='delete-company-address' company-id='" + response.data.general.company_id + "' data-id='" + val.address_id + "'></span><span class='glyphicon glyphicon-pencil pointer pull-right company-item-btn' data-action='edit_coaddress' data-id='" + val.address_id + "'></span></td></tr>"
                            $panel.find('#address tbody').append($address);
                        });
                    } else {
                        $panel.find('#address .table-container table').hide();
                        $panel.find('#address .none-found').show();
                    }
                }
                $('.tt').tooltip();
                $panel.find('.tab[href="#' + item_form + '"]').tab('show');
            });

        },
        save_company: function () {
            var $form = $('#modal #general').find('form');
            if ($form.find('input[name="company_id"]').val() == "") {
                var action = "add_company";
            } else {
                var action = "save_company";
            }
            $.ajax({
                url: helper.baseUrl + "ajax/" + action,
                type: "POST",
                dataType: "JSON",
                data: $form.serialize()
            }).done(function (response) {
                flashalert.success("Company details saved");
                //change the add box to an edit box
                if (action == "add_company") {
                    $('#modal').find('input[name="company_id"]').val(response.id);
                    $('.phone-tab,.address-tab').show();
                    $('#phone,#address').find('.table-container table').hide();
                    $('.tab-alert').hide();
                }
                record.company_panel.load_panel(record.urn, response.id);
            });

        },
        change_tab: function (tab) {
            modals.clear_footer();
            var buttons = "";
            if (tab == "#general") {
                buttons = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button><button type="submit" class="btn btn-primary save-company-general">Save changes</button>';
                modals.update_footer(buttons);
            } else {
                buttons = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>';
                $('#modal').find('.table-container').show();
                $('#modal').find('.company-phone-form,.company-address-form').hide();
                modals.update_footer(buttons);
            }

        }
    },


    company_tel: function () {
        /* add the modal code here */
    },
    contact_tel: function () {
        /* add the modal code here */
    },
    company_address: function () {
        /* add the modal code here */
    },
    contact_address: function () {
        /* add the modal code here */
    },
    calendar: function () {
        /* add the modal code here */
    },
}