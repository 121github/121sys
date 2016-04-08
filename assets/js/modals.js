// JavaScript Document
var $modal = $('#modal');
var modals = {
    init: function () {
        modal_footer = $modal.find('.modal-footer');
        modal_header = $modal.find('.modal-title');
        modal_body = $modal.find('.modal-body');

        appointmentFormData = [];

        modal_dialog = $modal.find('.modal-dialog');
		$(document).on('click','[data-toggle="tab"]',function(e){
			$('#company-address-form,#company-phone-form,#contact-address-form,#contact-phone-form,#referral-address-form').hide();
			var tab = $(this).attr('href');
			if(tab=="#tab-planner"||tab=="#phone"||tab=="#pot"||tab=="#source"||tab=="#campaign"||tab=="#other"||"#create-view"){
			modal_body.css('overflow','visible');
			} else {
			modal_body.css('overflow','auto');
			}
		});
		$modal.on('click','#record-suppress',function(e){
			e.preventDefault();
			modals.suppress_record();
		});
		$modal.on('click','#save-record-options',function(e){
			e.preventDefault();
			modals.save_record_options();
		});
		$modal.on('change', '.typepicker', function () {
            var type = $modal.find('.typepicker').val()!==""?$(this).find('option:selected').text():"Appointment";
			var title = $('#contact-select option:selected').length>0?type+' with '+$('#contact-select option:selected').text():type;
            $modal.find('[name="title"]').val(title);
        }); 
		$(document).on('click','[data-modal="contact-us"]',function(e){
			  modals.contact_us();
		});
		$(document).on('click','[data-dismiss="modal"]',function(e){
			$('.container-fluid').removeAttr('style');
		});
        $(document).on('click', '[data-modal="merge-record"]', function (e) {
            e.preventDefault();
            modals.merge_record($(this).attr('data-urn'), $(this).attr('data-merge-target'));
        });
        $(document).on('click', '[data-modal="view-record"]', function (e) {
            e.preventDefault();
			var that = $(this);
			var tab = that.attr('data-tab');
            var clicked_urn = that.attr('data-urn');			
            setTimeout(function () {
				$modal.one('hidden.bs.modal', function () {
    			that.attr('data-modal','view-record');
				})
				that.removeAttr('data-modal');
                modals.view_record(clicked_urn,tab);
            }, 300);
		
        });
        $(document).on('dblclick', '[data-modal="view-record"],[data-modal="view-appointment"]', function (e) {
            e.preventDefault();
            window.location.href = helper.baseUrl + 'records/detail/' + $(this).attr('data-urn');
        });

        $(document).on('click', '[data-modal="edit-contact"]', function (e) {
            e.preventDefault();
            modals.contacts.contact_form('edit', $(this).attr('data-id'), 'general');
        });
        $(document).on('click', '[data-modal="add-contact"]', function (e) {
            e.preventDefault();
            modals.contacts.contact_form('add', $(this).attr('data-urn'), 'general');
        });
        $(document).on('click', '[data-modal="edit-referral"]', function (e) {
            e.preventDefault();
            modals.referrals.referral_form('edit', $(this).attr('data-id'), 'general');
        });
        $(document).on('click', '[data-modal="add-referral"]', function (e) {
            e.preventDefault();
            modals.referrals.referral_form('add', $(this).attr('data-urn'), 'general');
        });
        $(document).on('click', '[data-modal="edit-company"]', function (e) {
            e.preventDefault();
            modals.companies.company_form('edit', $(this).attr('data-id'), 'general');
        });
        $(document).on('click', '[data-modal="add-company"]', function (e) {
            e.preventDefault();
            modals.companies.company_form('add', $(this).attr('data-urn'), 'general');
        });
        $modal.on('click', '.modal-set-location', function (e) {
            e.preventDefault();
            modals.set_location();
        });
        $modal.on('click', '#save-planner', function (e) {
            e.preventDefault();
            modals.save_planner($(this).attr('data-urn'));
        });
        $modal.on('click', '#remove-from-planner-confirm', function (e) {
            e.preventDefault();
            modals.confirm_remove_from_planner($(this).attr('data-urn'));
        });
		$modal.on('click', '#remove-from-planner', function (e) {
            e.preventDefault();
            modals.remove_from_planner($(this).attr('data-urn'));
        });
        $modal.on('click', '#save-appointment', function (e) {
            e.preventDefault();
			var $form = $('#appointment-form');
            if (helper.permissions['check overlap'] > 0) {
                    $.ajax({
                        url: helper.baseUrl + 'appointments/check_overlap_appointments',
                        data: $('#appointment-form').serialize(),
                        type: "POST",
                        dataType: "JSON"
                    }).done(function (response) {
                        if (response.success) {
							 modals.save_appointment($form.serialize());
						} else if(response.overlapped){
							  if (helper.permissions['overlap appointment'] > 0) {
                                var appointment_id = $form.find('input[name="appointment_id"]').val();
                                var urn = $form.find('input[name="urn"]').val();
                                modals.confirm_overlap_appointment(urn, appointment_id, $form.serialize());
                            }
                            else {
                              flashalert.danger(response.msg);
                            }
							
						} else if(response.error){
						flashalert.danger(response.msg);	
						}

                    });
            }
            else {
                modals.save_appointment($('#appointment-form').serialize());
            }
        });
        $(document).on('click', '[data-modal="view-appointment"]', function (e) {
            e.preventDefault();
            var clicked_id = $(this).attr('data-id');
            setTimeout(function () {
                modals.view_appointment(clicked_id);
            }, 500);

        });
		$(document).on('click', '[data-modal="choose-columns"]', function (e) {
            e.preventDefault();
             modals.show_table_views($(this).attr('data-table-id'));
        });
		   $modal.on('click', '#save-columns', function (e) {
            e.preventDefault();
            modals.save_columns($(this).attr('data-table-id'));
        })
		
		
        $(document).on('click', '[data-modal="edit-appointment"]', function (e) {
            e.preventDefault();
            modals.view_appointment($(this).attr('data-id'), true);
        });
		/*
        $(document).on('click', '[data-modal="add-appointment"]', function (e) {
            e.preventDefault();
            modals.add_appointment_html($(this).attr('data-urn'));
        });
		*/
        $(document).on('click', '[data-modal="delete-appointment"]', function (e) {
            e.preventDefault();
            modals.delete_appointment_html($(this).attr('data-id'), $(this).attr('data-urn'));
        });

        $(document).on('click', '[data-modal="create-appointment"]', function (e) {
            e.preventDefault();
			var start = false;
			if(typeof $(this).attr('data-start') !== "undefined"){
			start = $(this).attr('data-start');	
			}
            modals.create_appointment($(this).attr('data-urn'),start);
        });
        $modal.on('click', '#cancel-add-address', function (e) {
            ;
            e.preventDefault();
            $('#add-appointment-address').hide();
            $('#select-appointment-address').show();
            $modal.find('.addresspicker').selectpicker('val', $('#addresspicker option:first').val());
        });
        $modal.on('change', '.addresspicker', function (e) {
            if ($(this).val() == "Other") {
                $('#add-appointment-address').show();
                $('#select-appointment-address').hide();
                $modal.find('.addresspicker').val('53');
            } else {
                $('#add-appointment-address').hide();
                $('#select-appointment-address').show();
            }
        });
        $(document).on('click', '#confirm-add-address', function (e) {
            e.preventDefault();
            modals.confirm_other_appointment_address();
        });

        $modal.on('click', '#cancel-add-access-address', function (e) {
            ;
            e.preventDefault();
            $('#add-appointment-access-address').hide();
            $('#select-appointment-access-address').show();
            $modal.find('.accessaddresspicker').selectpicker('val', $('#accessaddresspicker option:first').val());
        });
        $modal.on('change', '.accessaddresspicker', function (e) {
            if ($(this).val() == "Other") {
                $('#add-appointment-access-address').show();
                $('#select-appointment-access-address').hide();
                $modal.find('.accessaddresspicker').val('53');
            } else {
                $('#add-appointment-access-address').hide();
                $('#select-appointment-access-address').show();
            }
        });
        $(document).on('click', '#confirm-add-access-address', function (e) {
            e.preventDefault();
            modals.confirm_other_appointment_access_address();
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
            var cancellation_reason = $modal.find('.appointment-cancellation-form').find('textarea[name="cancellation_reason"]').val();
            var id = $modal.find('#appointment-id').val();
            var urn = $modal.find('#urn').val();
            if (cancellation_reason.length < 5) {
                flashalert.danger("You must enter a cancellation reason!");
            } else {
                modals.delete_appointment(id, cancellation_reason, urn);
                $modal.modal('toggle');
            }
        });

        $(document).on('click', '.modal-show-filter-options', function (e) {
            e.preventDefault();
            modals.view_filter_options();
        });

        $modal.on('click', '.remove-filter-option', function() {
            var field = $(this).attr('data-field');
            modals.remove_filter(field);
        });
        $modal.on('click', '.clear-filters-btn', function() {
            modals.clear_filters();
        });
        $(document).on('click', '[data-modal="import-appointment-btn"]', function (e) {
            e.preventDefault();
            var clicked_urn = $(this).attr('data-urn');
            setTimeout(function () {
                modals.import_appointments();
            }, 500);
        });
		  $modal.on('change','#map-icon', function(e) {
                var map_icon = ((e.icon.length > 0 && e.icon != "empty") ? e.icon : '');
                $('form').find('input[name="map_icon"]').val(map_icon);
        });
        $modal.on('click','#load-view-btn', function(e) {
          modals.set_user_view();
        });
        $modal.on('click','#edit-view-btn', function(e) {
          modals.edit_user_view();
        });
        $modal.on('click','#save-view-btn',function(e){
			if($('#column-picker-form').find('[name="view_name"]').val()==""){
			flashalert.danger("You must give the view a name");
			return false;	
			}
            modals.save_columns();
 
        });
        $modal.on('click','.create-view-tab .tab',function(e){
             var mfooter = '<button type="submit" class="btn btn-primary pull-right marl" id="save-view-btn">Save</button> <button id="view-back-btn" class="btn btn-default pull-left" type="button">Back</button>';
             modal_footer.html(mfooter);
             //reset all the fields
             $('#create-view .selectpicker').selectpicker('val',[]);
			 $('#create-view input[name="view_name"]').val('');
             $('#create-view input[name="view_id"]').val('');
        });
        $modal.on('click','#view-back-btn,.load-view-tab .tab',function(e){
            $modal.find('#load-view').addClass('active');
            $modal.find('#create-view').removeClass('active');
			 $modal.find('.load-view-tab').addClass('active');
            $modal.find('.create-view-tab').removeClass('active');
             var mfooter = '<button type="submit" class="btn btn-primary pull-right marl" id="load-view-btn">Load View</button> <button type="submit" class="btn btn-primary pull-right" id="edit-view-btn">Edit View</button> <button type="submit" class="btn btn-danger pull-right" id="delete-view-btn">Delete View</button> <button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Cancel</button>';
            modal_footer.html(mfooter);
        });

        $modal.on('click','#search-records-btn',function(e){
            e.preventDefault();
            modals.search_records_func($(this).attr('data-action'),$(this).attr('data-start'));
        });

        $modal.on('click','#get-address',function(e){
            e.preventDefault();
            modals.get_addresses();
        });
		    $modal.on('click','#delete-view-btn',function(e){
            e.preventDefault();
            modals.confirm_delete_view($('#load-view').find('select').val(),$modal.find('input[name="table"]').val());
        });		
    },
	 confirm_delete_view: function (view_id,table) {
            var mheader = "Delete View";
            var mbody = "Are you sure you want to delete this view?";
            var mfooter = '<button class="btn btn-default pull-left cancel-delete-view" type="button">Back</button> <button class="btn btn-danger confirm-delete-view" type="button">Delete</button>';

            modals.load_modal(mheader, mbody, mfooter);
              $modal.find('.confirm-delete-view').click(function () {
                modals.delete_view(view_id,table);
            });
			  $modal.find('.cancel-delete-view').click(function () {
                 modals.show_table_views(table);
            });
        },
	delete_view:function(view,table){
		$.ajax({
            url: helper.baseUrl + 'ajax/delete_view',
			data: { id:view },
            type: "POST",
            dataType: "JSON"
        }).done(function(response) {
			flashalert.success("View was deleted");
			 modals.show_table_views(table);
		});
	},
	set_user_view:function(id){
		$.ajax({
            url: helper.baseUrl + 'datatables/set_user_view',
			data: { id:$('#load-view select').val(),table:table_id },
            type: "POST",
            dataType: "JSON"
        }).done(function(response) {
			table_columns = response.columns;
			view.view_id = response.view_id;
			full_table_reload();
		});
	},
	
	save_record_options:function(){
		$.ajax({
            url: helper.baseUrl + 'ajax/save_record_options',
			data: $('#record-options-form').serialize(),
            type: "POST",
            dataType: "JSON"
        }).done(function(response) {
			 modals.record_options();
			 flashalert.success("Record options saved");
		}).fail(function(response){
			 flashalert.danger("An error occured while saving");
		});
		
	},
	record_options:function(tab){
		$.ajax({
            url: helper.baseUrl + 'modals/record_options',
			data: { urn:record.urn },
            type: "POST",
            dataType: "HTML"
        }).done(function(response) {
			 var mheader = "Record options";
			 var mbody = response;
			 var mfooter = '<button class="btn btn-primary pull-right" id="save-record-options">Save</button> <button data-dismiss="modal" class="btn btn-default close-modal pull-left">Cancel</button>';
			 modals.load_modal(mheader,mbody,mfooter);
			 modal_body.css('overflow', 'visible');
			  $modal.find('.nav-tabs a[href="#'+tab+'"]').tab('show');
		});
	},
	show_table_views:function(table_id){
		 $.ajax({
            url: helper.baseUrl + 'datatables/get_user_views',
			data: { table:table_id },
            type: "POST",
            dataType: "JSON"
        }).done(function (response) {
			if(response.views.length==0){
			location.reload();	
			return false;
			}
			 var mheader = "View Editor";
			 var create_view = "<form id='column-picker-form' ><input type='hidden' name='table' value='"+table_id+"' /><input type='hidden' name='view_id' value='' />";
			 var select_views = "<div class='form-group'><h4>Select a view</h4><select class='selectpicker'>";
			$.each(response.views,function(i,row){
			 select_views += "<option value='"+row.view_id+"'>"+row.view_name+"</option>";
		});
			 select_views += "</select></div>";
			 create_view += "<div class='form-group'><h4>View Name</h4><input class='form-control' name='view_name' placeholder='Enter the view name' /></div><hr/>";
			 var mfooter = '<button type="submit" class="btn btn-primary pull-right marl" id="load-view-btn">Load View</button> <button type="submit" class="btn btn-primary pull-right" id="edit-view-btn">Edit View</button> <button type="submit" class="btn btn-danger pull-right"  id="delete-view-btn">Delete View</button> <button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Cancel</button>';
			 create_view += "<div class='row'><div class='col-xs-6'>";
			 var i=0; $.each(response.columns,function(group,row){ i++;
			    var subtext ="";
				create_view += i==4?"</div><div class='col-xs-6'>":"";
				create_view += "<h4>"+group+"</h4>";
				var colpicker = "<select class='selectpicker column-select' name='columns[]' multiple data-width='100%'>";
				 $.each(row,function(i,column){
					colpicker += "<option "+subtext+" value='"+column.columns.datafield_id+"'>"+column.columns.datafield_title+"</option>";
				 })
				 colpicker += "</select>";
				 create_view += colpicker;
				 })
			 create_view += "</div></form>";
			 
			 var mbody = '<ul class="nav nav-tabs"><li class="load-view-tab active"><a href="#load-view" class="tab" data-toggle="tab">My Views</a></li><li class="create-view-tab"><a href="#create-view" class="tab" data-toggle="tab">Create View</a></li></ul><div class="tab-content"><div class="tab-pane active" id="load-view">'+select_views+'</div><div class="tab-pane" id="create-view">'+create_view+'</div></div>'
			 modals.load_modal(mheader, mbody, mfooter, true);
			 modal_body.css('overflow', 'visible');
            });
		
	},
	edit_user_view:function(){
		  $.ajax({
            url: helper.baseUrl + 'datatables/get_columns',
			data: { id:$('#load-view select').val() },
            type: "POST",
            dataType: "JSON"
        }).done(function (response) {
					$('#create-view .column-select').selectpicker('val',response);
			$('form#column-picker-form input[name="view_name"]').val($('#load-view select option:selected').text());
			$('form#column-picker-form input[name="view_id"]').val($('#load-view select').val());
			$('#load-view').removeClass('active');
			$('#create-view').addClass('active');
			 var mfooter = '<button type="submit" class="btn btn-primary pull-right marl" id="save-view-btn">Save</button> <button id="view-back-btn" class="btn btn-default pull-left" type="button">Back</button>';
			modal_footer.html(mfooter);
            });
		
	},
	save_columns:function(){
		 $.ajax({
            url: helper.baseUrl + 'datatables/save_columns',
            data: $('#column-picker-form').serialize(),
            type: "POST",
            dataType: "JSON"
        }).done(function(response) {
			if(response.success){
			table_columns = response.columns;
			if(response.action == "create"){
			$('#load-view select').children().prop('selected',false)
			$('#load-view select').append('<option value="'+response.id+'" selected>'+response.name+'</option>').selectpicker('refresh');
			} 
			if(response.action == "update"){
			$('#load-view select option[value="'+response.id+'"]').text(response.name).closest('select').selectpicker('refresh');
			}
			$('#create-view form')[0].reset();
			$modal.find('.load-view-tab a').trigger('click');
			} else {
			flashalert.danger(response.msg);	
			}
		});
	},
    merge_record: function (urn, target) {
        $.ajax({
            url: helper.baseUrl + 'modals/merge_record',
            data: {urn: urn, target: target},
            type: "POST",
            dataType: "HTML"
        }).done(function (html) {
            var mheader = "Merge data";
            var mbody = html;
            var mfooter = '<button class="btn btn-default pull-left" data-modal="view-record" data-urn="' + urn + '" >Back</button> <button class="btn btn-primary pull-right" id="start-merge" disabled>Start Merge</button> ';
            ;
            modals.load_modal(mheader, mbody, mfooter);
            $('[name="source"]').val(urn);
            $('[name="target"]').val(target);
            modal_body.css('overflow', 'visible');
        });
    },
    start_merge: function () {
        $.ajax({
            url: helper.baseUrl + 'merge/merge_launch',
            data: $('#merge-form').serialize(),
            type: "POST",
            dataType: "JSON"
        }).done(function (response) {
            if (response.success) {
                if (typeof record !== "undefined") {
                    record.company_panel.load_panel(record.urn);
                    record.contact_panel.load_panel(record.urn);
                }
                flashalert.success('Merge Successful');
                $modal.modal('hide');

            }
            ;
        })
    },
    preview_merge: function () {
        $.ajax({
            url: helper.baseUrl + 'merge/merge_preview',
            data: $('#merge-form').serialize(),
            type: "POST",
            dataType: "JSON"
        }).done(function (response) {
            var contents = '';
            if (response.length == 0) {
                contents = 'There is nothing to merge at the moment. All fields are in sync';
            } else {

                $.each(response, function (type, actions) {

                    $.each(actions, function (action, data) {
                        var this_type = type;
                        if (type == "companies" && data.length == "1") {
                            this_type = "company";
                        } else if (type == "contacts" && data.length == "1") {
                            this_type = "contact";
                        }
                        contents += '<h4>' + data.length + ' ' + this_type + ' will be ' + action + '</h4>';

                        $.each(data, function (k, v) {
                            contents += '<ul>'
                            $.each(v, function (field, value) {
                                if (field == "telephone_number") {
                                    field = v.description;
                                    delete v.description;
                                }
                                contents += '<li>' + field + ' => ' + value + '</li>';
                            });
                            contents += '</ul>'
                        });

                    });
                });
            }
            $('#merge-preview').html(contents);
            $('#start-merge').prop('disabled', false);
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
                var appointment_id = response.appointment_id;
                flashalert.success('Appointment was saved');
                //Refresh the calendar
                if(typeof $('#calendar').fullCalendar() !== "undefined"){
                    $('#calendar').fullCalendar('refetchEvents');
                }
                //Refresh the quick planner
                if(typeof quick_planner !== "undefined"){
                    var attendee_id = response.data.attendees[0];
                    var attendee_name = $('#planner-attendee-filter').find('[data-val="'+attendee_id+'"]').find('.filter-text').text()
                    $('#slot-attendee').val(attendee_id);
                    $('#quick-planner-panel').find('[name="driver_id"]').val(attendee_id);
                    $('#planner-attendee-text').text(attendee_name);
                    $('#slot-attendee-text').text(attendee_name);
                    quick_planner.driver_id = attendee_id;
                    quick_planner.load_planner();
                }
                    if(typeof campaign_functions.save_appointment !== "undefined"){
                        campaign_functions.save_appointment(response.data);
                    }
                if (response.trackvia) {
                    $.post(response.trackvia, {urn: response.urn});
                }
                $('.close-modal').trigger('click');
                if (typeof record !== "undefined") {
                    record.appointment_panel.load_appointments();
                }
				if(response.add_to_planner){
					$.ajax({url:helper.baseUrl+'planner/add_appointment_to_the_planner',
					data:{appointment_id:response.appointment_id},
					type:"POST",
					dataType:"JSON"
					}).done(function(response){

					});
				}
                //Send the ics file if it is needed for the book appointment
                if (response.state == 'inserted') {
                    $.ajax({
                        url: helper.baseUrl + 'email/book_appointment_ics',
                        data: {
                            appointment_id: appointment_id,
                            description: 'New appointment added on the system'
                        },
                        type: "POST",
                        dataType: "JSON"
                    }).done(function(response){

                    });
                }
                //Send the ics file if it is needed for update the appointment
                else if (response.state == 'updated') {
                    $.ajax({
                        url: helper.baseUrl + 'email/update_appointment_ics',
                        data: {
                            appointment_id: appointment_id,
                            description: 'Appointment updated on the system',
                            start: response.data.start,
                            end: response.data.end,
                            postcode: response.data.postcode
                        },
                        type: "POST",
                        dataType: "JSON"
                    }).done(function(response){

                    });
                }
                //TODO send cover letter from hsl file
                if (typeof campaign_functions.appointment_saved !== "undefined") {
                    campaign_functions.appointment_saved(appointment_id, response.state);
                }
                //Notice for set the outcome before leave the page (only if we create the appointment from the record panel)
                if(typeof record !== "undefined"){
                    $(window).on('beforeunload', function () {
                        return 'You need to set the outcome after setting an appointment. Are you sure you want to leave?';
                    });
                }

                //Set appointmnt in google calendar if the attendee has a google account
                $.ajax({
                    url: helper.baseUrl + 'booking/add_google_event',
                    data: {
                        appointment_id: appointment_id,
                        data: response.data
                    },
                    type: "POST",
                    dataType: "JSON"
                });

            } else {
                flashalert.danger(response.msg);
            }
        }).fail(function(){
				flashalert.danger("There was an error saving the appointment");
        });
    },
    confirm_overlap_appointment: function (urn, appointment_id, data) {
        var mheader = "Overlap appointments";
        var mbody = "There are one or more appointments on the same date and for this attendee. Do you still want to book this appointment?";
        var mfooter = '<button class="btn btn-default pull-left cancel-overlap-appointment" data-urn="'+urn+'" data-id="'+appointment_id+'" type="button">No, cancel it</button> <button class="btn btn-danger confirm-overlap-appointment" type="button">Yes, book it</button>';

        modals.load_modal(mheader, mbody, mfooter);
        $('.confirm-overlap-appointment').click(function () {
            modals.save_appointment(data);
            $modal.modal('toggle');
        });
        /* go back to the appointment tab if they cancel the delete action */
        $(document).one('click', '.cancel-overlap-appointment', function (e) {
            e.preventDefault();
            var appointment_id = $(this).attr('data-id');
            if (appointment_id != '') {
                modals.view_appointment(appointment_id, true);
            }
            else {
                //Deserialize the form
                appointmentFormData = deserializeForm(data);
                modals.create_appointment(urn);
            }
        });
    },
    delete_appointment: function (id, cancellation_reason, urn) {
        $.ajax({
            url: helper.baseUrl + 'records/delete_appointment',
            type: "POST",
            dataType: "JSON",
            data: {
                appointment_id: id,
                cancellation_reason: cancellation_reason,
                urn: urn
            }
        }).done(function (response) {
            if (response.success) {
                //Refresh the appointment_panel
                if(typeof record !== "undefined"){
                    record.appointment_panel.load_appointments();
                }
                //Refresh the calendar
                if(typeof fullcalendar !== "undefined"){
                    fullcalendar.fullCalendar('refetchEvents');
                }
                //Refresh the quick planner
                if(typeof quick_planner !== "undefined"){
                    var attendee_id = response.appointment[0].user_id;
                    var attendee_name = $('#planner-attendee-filter').find('[data-val="'+attendee_id+'"]').find('.filter-text').text()
                    $('#slot-attendee').val(attendee_id);
                    $('#quick-planner-panel').find('[name="driver_id"]').val(attendee_id);
                    $('#planner-attendee-text').text(attendee_name);
                    $('#slot-attendee-text').text(attendee_name);
                    quick_planner.driver_id = attendee_id;
                    quick_planner.load_planner();
                }
                //Delete from the planner if it is needed
                if(response.add_to_planner){
                    $.ajax({url:helper.baseUrl+'planner/add_appointment_to_the_planner',
                        data:{appointment_id:id},
                        type:"POST",
                        dataType:"JSON"
                    }).done(function(response){

                    });
                }
                //Send ics cancellation if it is needed
                $.ajax({
                    url: helper.baseUrl + 'email/cancel_appointment_ics',
                    data: {
                        appointment_id: id,
                        description: 'Appointment cancelled on the system',
                    },
                    type: "POST",
                    dataType: "JSON"
                }).done(function(response){

                });
                //Notice for set the outcome before leave the page
                $(window).on('beforeunload', function () {
                    return 'You need to set the outcome after cancel an appointment. Are you sure you want to leave?';
                });
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
		        mbody = '<div class="row">';
		$.each(data.appointment,function(title,column){
			
		 mbody += '<div class="col-sm-6"><h4>'+title+'</h4>';	
		 if(column.display=="table"){
			 var list_icon = column.list_icon.length>0?"<i class='fa "+column.list_icon+"'></i> ":"";
		var colbody = "<table class='"+column.table_class+"'>";	 
		 $.each(column.fields,function(key,val){
			 if(val.modal_keys=="1"){
			 colbody += "<tr><td>"+list_icon+"</td><td><strong>"+key+"</strong></td><td>"+val.value+"</td></tr>";
			 } else if(val.modal_keys=="2"){
			 colbody += "<tr><td colspan='3'><strong>"+key+"</strong></td></tr><tr><td colspan='3'>"+val.value+"</td></tr>";	 
			 } else {
			 colbody += "<tr><td colspan='3'>"+val.value+"</td></tr>";	 
			 }
		 });
		 colbody += "</table>";
		 }
		 if(column.display=="list"){
		var list_icon = "";
		var list_style = "";
		if(column.list_icon.length>0){
			 list_icon = "<span class='fa "+column.list_icon+"'></span>";
			 list_style = "style='list-style:none'"
		}
		var colbody = "<ul "+list_style+">";	 
		 $.each(column.fields,function(key,val){
			 colbody += "<li>"+list_icon+" <strong>"+key+":</strong> "+val+"</li>";
		 });
		 colbody += "</ul>";
		 }
		 mbody += colbody;
		 mbody +=  "</div>";
		});

		mbody += '</div>';
        var mheader = "Appointment #" + data.id;
        /*var mbody = "<table class='table small'><tbody><tr><th>Company</th><td>" + data.coname + "</td></tr><tr><th>Date</th><td>" + data.starttext + "</td></tr><tr><th>Title</th><td>" + data.title + "</td></tr><tr><th>Notes</th><td>" + data.text + "</td></tr><tr><th>Attendees</th><td>" + attendees + "</td></tr><tr><th>Type</th><td>" + data.appointment_type + "</td></tr><tr><th>Location</th><td>" + data.address + "</td></tr>";
        if (data.distance && getCookie('current_postcode')) {
            mbody += "<tr><th>Distance</th><td>" + Number(data.distance).toFixed(2) + " Miles from " + getCookie('current_postcode') + "</td></tr>";
        }
        mbody += "</tbody></table>";
		*/
		
        mbody += "This appointment was set by <b>" + data.created_by + "</b> on <b>" + data.date_added + "</b>";
        var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>';
		if(helper.permissions['edit appointments'] > 0){
		mfooter += '<a class="btn btn-primary pull-right" data-modal="edit-appointment" data-id="' + data.id + '" >Edit Appointment</a> ';
		}
		//should change this permission to "appointment webform"
		if(helper.permissions['edit appointments'] > 0&&data.btn_text!==null){
		mfooter += '<a class="btn btn-info pull-right" href="'+helper.baseUrl+'webforms/edit/'+data.campaign_id+'/'+data.urn+'/'+data.webform_id+'/'+data.id+'" >'+data.btn_text+'</a> ';
		}
        if (data.urn != $('#urn').val()&&helper.permissions['view record'] > 0) {
            mfooter += ' <a class="btn btn-primary pull-right" href="' + helper.baseUrl + 'records/detail/' + data.urn + '">View Record</a>';
        }
        if (getCookie('current_postcode')) {
            mfooter += '<a target="_blank" class="btn btn-info pull-right" href="' + mapLink + '?q=' + data.postcode + '",+UK">View Map</a>';
        }
        if (data.distance && getCookie('current_postcode')) {
            mfooter += '<a target="_blank" class="btn btn-info pull-right" href="' + mapLink + '?zoom=2&saddr=' + getCookie('current_postcode') + '&daddr=' + data.postcode + '">Navigate</a>';
        }
        if (data.cancellation_reason) {
            mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button> <p class="text-danger">This appointment was cancelled.<br><small>' + data.cancellation_reason + '</small></p>';
        }
        modals.load_modal(mheader, mbody, mfooter)
		modal_body.css('overflow', 'auto');
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
			var cancel_btn = "";
			if(helper.permissions['delete appointments'] > 0){
			cancel_btn += '<button class="btn btn-danger" data-modal="delete-appointment" data-id="' + data.appointment_id + '" data-urn="' + data.urn + '" type="button">Cancel Appointment</button>';
			}
            var mheader = "Edit Appointment #" + data.appointment_id;
            var mbody = '<div class="row"><div class="col-lg-12">' + response + '</div></div>';
            var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button> '+cancel_btn;
			if(helper.permissions['confirm appointment'] > 0){ 
			mfooter += '<input id="appointment-confirmed" data-onstyle="success" data-toggle="toggle" data-on="Confirmed" data-off="Unconfirmed" type="checkbox"> '
			}
			mfooter += '<button class="btn btn-primary pull-right" id="save-appointment" type="button">Save</button>'
			
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


            //check if the appointment access address is already in the dropdown and if not, add it.
            var option_exists = false;
            $.each($mbody.find('#accessaddresspicker option'), function () {
                if ($(this).val() == data.access_address + '|' + data.access_postcode) {
                    option_exists = true;
                }
            });
            if (!option_exists && data.access_address) {
                $mbody.find('#accessaddresspicker').prepend('<option value="' + data.access_address + '|' + data.access_postcode + '">' + data.access_address + '</option>');
            }

            //cycle through the rest of the fields and set them in the form
            $.each(data, function (k, v) {
                $mbody.find('[name="' + k + '"]').val(v);
                if (k == "type") {
                    $mbody.find('#typepicker option[value="' + v + '"]').prop('selected', true).attr('selected','selected');
                    $mbody.find('[name="appointment_type_id"]').val(v);
                }
                if (k == "attendees") {
                    $.each(v, function (i, user_id) {
                        $mbody.find('#attendee-select option[value="' + user_id + '"]').prop('selected', true).attr('selected','selected');
                    });
                }
                if (k == "branch_id") {
                    if (v) {
                        $mbody.find('#branch-select option[value="' + v + '"]').prop('selected', true).attr('selected','selected');
                    }
                }
                $mbody.find('#addresspicker option[value="' + data.address + '|' + data.postcode + '"]').prop('selected', true).attr('selected','selected');
                $mbody.find('#accessaddresspicker option[value="' + data.access_address + '|' + data.access_postcode + '"]').prop('selected', true).attr('selected','selected');
            });
            modals.load_modal(mheader, $mbody, mfooter);
            modals.appointment_contacts(data.urn, data.contact_id);
            modal_body.css('overflow', 'visible');
            modal_body.css('max-height', '650px');

            modals.set_appointment_confirmation();
			$modal.find('#appointment-confirmed').hide();

            modals.set_appointment_access_address(data.access_address);
			
                if (typeof campaign_functions.appointment_edit_setup !== "undefined") {
                    campaign_functions.appointment_edit_setup();
                }
        });
    },
	appointment_setup: function (start,attendee,urn) {
		if(typeof start == "undefined"&&$('#slots-panel').find('input:checked').length>0){
			start = $('#slots-panel').find('input:checked').attr('data-date') +' '+ $('#slots-panel').find('input:checked').attr('data-time');	;	
		}
		if(start){
        modals.set_appointment_start(start);
		}
		if(attendee){
        modals.set_appointment_attendee(attendee);
		}
	},
	set_appointment_attendee: function (attendee) {
        $modal.find('.attendeepicker').selectpicker('val', [attendee]);
    },
    set_appointment_start: function (start) {
        var m = moment(start, "YYYY-MM-DD HH:mm");
        $modal.find('.startpicker').data("DateTimePicker").date(m);
        $modal.find('.endpicker').data("DateTimePicker").date(m.add(1,'hours').format('DD\MM\YYYY HH:mm'));
    },
    restore_appointment_form: function() {

        //Restore data if is defined

        if (typeof appointmentFormData != "undefined" && appointmentFormData['urn'].length > 0) {

            $('#appointment-form').find('input[name="urn"]').val(appointmentFormData['urn']);
            $('#appointment-form').find('input[name="appointment_id"]').val(appointmentFormData['appointment_id']);
            $('#appointment-form').find('input[name="branch_id"]').val(appointmentFormData['branch_id']);

            $('#appointment-form').find('input[name="text"]').val(appointmentFormData['text']);


            $('#appointment-form').find('input[name="title"]').val(appointmentFormData['title']);

            $('#appointment-form').find('.typepicker').selectpicker("val",appointmentFormData['appointment_type_id']);
            $('#appointment-form').find('.typepicker').selectpicker("refresh");

            $('#appointment-form').find('.attendeepicker').selectpicker("val",appointmentFormData['attendees[]']);
            $('#appointment-form').find('.attendeepicker').selectpicker("refresh");

            $('#appointment-form').find('.startpicker').data("DateTimePicker").date(appointmentFormData['start']);
            $('#appointment-form').find('.endpicker').data("DateTimePicker").date(appointmentFormData['end']);

            $('#appointment-form').find('.addresspicker').selectpicker("val", appointmentFormData['address']);
            $('#appointment-form').find('.addresspicker').selectpicker("refresh");

            if (typeof appointmentFormData['address'] == "undefined" || $('#appointment-form').find('.addresspicker').selectpicker("val") == "") {
                $('#appointment-form').find('#add-appointment-address').show();
                $('#appointment-form').find('#select-appointment-address').hide();
            } else {
                $('#appointment-form').find('#add-appointment-address').hide();
                $('#appointment-form').find('#select-appointment-address').show();
            }
            $('#appointment-form').find('input[name="add1"]').val(appointmentFormData['add1']);
            $('#appointment-form').find('input[name="add2"]').val(appointmentFormData['add2']);
            $('#appointment-form').find('input[name="add3"]').val(appointmentFormData['add3']);
            $('#appointment-form').find('input[name="county"]').val(appointmentFormData['county']);
            $('#appointment-form').find('input[name="new_postcode"]').val(appointmentFormData['new_postcode']);

            $('#appointment-form').find('input[name="access_add_check"]').val(appointmentFormData['access_add_check']);
            if (appointmentFormData['access_add_check']) {
                $('#access-add-check').bootstrapToggle('on');
            }
            else {
                $('#access-add-check').bootstrapToggle('off');
            }

            $('#appointment-form').find('.accessaddresspicker').selectpicker("val", appointmentFormData['access_address']);
            $('#appointment-form').find('.accessaddresspicker').selectpicker("refresh");

            if (typeof appointmentFormData['access_address'] == "undefined" || $('#appointment-form').find('.access_address').selectpicker("val") == "") {
                $('#appointment-form').find('#add-appointment-access-address').show();
                $('#appointment-form').find('#select-appointment-access-address').hide();
            } else {
                $('#appointment-form').find('#add-appointment-access-address').hide();
                $('#appointment-form').find('#select-appointment-access-address').show();
            }
            $('#appointment-form').find('input[name="access_add1"]').val(appointmentFormData['access_add1']);
            $('#appointment-form').find('input[name="access_add2"]').val(appointmentFormData['access_add2']);
            $('#appointment-form').find('input[name="access_add3"]').val(appointmentFormData['access_add3']);
            $('#appointment-form').find('input[name="access_county"]').val(appointmentFormData['access_county']);
            $('#appointment-form').find('input[name="access_new_postcode"]').val(appointmentFormData['access_new_postcode']);

            $('#appointment-form').find('input[name="appointment_confirmed"]').val(appointmentFormData['appointment_confirmed']);
            if (appointmentFormData['appointment_confirmed'] === "1") {
                $('#appointment-confirmed').bootstrapToggle('on');
            }
            else {
                $('#appointment-confirmed').bootstrapToggle('off');
            }

            //$('#appointment-form').find('.contactpicker').selectpicker("val",appointmentFormData['contact_id']);
            //$('#appointment-form').find('.contactpicker').selectpicker("refresh");

            appointmentFormData = [];
        }
    },
    appointment_contacts: function (urn, contact_id) {
        $.ajax({
            url: helper.baseUrl + 'appointments/get_contacts',
            data: {urn: urn},
            dataType: "JSON",
            type: "POST",
            beforeSend: function () {
                $('#contact-select').hide();
            },
            error: function () {
                $('#contact-select').parent().append('<p class="text-error"><small>Please add a contact before setting an appointment<small></p>');
            },
        }).done(function (result) {
			if(result.length>0){
            $('#contact-select').show();
            $.each(result, function (k, v) {
                var selected = "";
                if (v.id == contact_id || result.length=="1") {
                    selected = "selected";
                } else if (!contact_id&&k==0){
					//select the first contact by default
					  selected = "selected";
				}
                $('#contact-select').append('<option ' + selected + ' value="' + v.id + '">' + v.name + '</option>');
            });

            $('#contact-select').selectpicker();

			} else {
			$('.close-modal').trigger('click');
			flashalert.danger("You must add a contact before setting an appointment");	
			}
        });

    },
    create_appointment: function (urn,start,attendee) {
		console.log("Create appointment");
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
            var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>'
			 if (helper.permissions['confirm appointment'] > 0) {
			 mfooter += '<input id="appointment-confirmed" data-toggle="toggle" data-on="Confirmed" data-off="Unconfirmed" data-width="130" type="checkbox" data-onstyle="success">' 
			 }
			 mfooter += '<button class="btn btn-primary pull-right" id="save-appointment" type="button">Save</button>';
            modals.load_modal(mheader, mbody, mfooter);
			modal_body.css('overflow', 'visible');
			modal_dialog.css('width', "50%");
			$modal.find('#appointment-confirmed').bootstrapToggle();
			modals.set_appointment_confirmation(start);
            modals.appointment_contacts(urn,false);
			modals.appointment_setup(start,attendee,false);
				
				if(typeof campaign_functions.appointment_setup !== "undefined"){
				    campaign_functions.appointment_setup(start,false,urn);
				}

            modals.set_appointment_access_address();
        });
    },
	set_appointment_confirmation:function(start){
        $modal.find('#appointment-confirmed').bootstrapToggle();
        if (helper.permissions['confirm appointment'] > 0) {
            if($modal.find('input[name="appointment_confirmed"]').val()=="1"){
			    $('#appointment-confirmed').bootstrapToggle('on');
            } else {
                $('#appointment-confirmed').bootstrapToggle('off');
            }
            $('#appointment-confirmed').on('change',function(e){
                if($(this).prop("checked")){
                    $modal.find('input[name="appointment_confirmed"]').val("1");
                    modal_body.find('.typepicker').selectpicker('refresh');
                } else {
                    $modal.find('input[name="appointment_confirmed"]').val("0");
                }
            });
        } else {
            $modal.find('.toggle').hide();
        }

            if(typeof campaign_functions.set_appointment_confirmation !== "undefined"){
                campaign_functions.set_appointment_confirmation();
        }
	},

    set_appointment_access_address: function(access_address){
        $modal.find('#access-add-check').bootstrapToggle();

        if(access_address){
            $('#access-add-check').bootstrapToggle('on');
            $modal.find('.access_addess').show();
        } else {
            $('#access-add-check').bootstrapToggle('off');
            $modal.find('.access_addess').hide();
        }
        $('#access-add-check').on('change',function(e){
            if($(this).prop("checked")){
                $modal.find('input[name="access_add_check"]').val("1");
                $modal.find('.access_addess').show();
            } else {
                $modal.find('input[name="access_add_check"]').val("0");
                $modal.find('.access_addess').hide();
            }
        });

            if(typeof campaign_functions.set_access_address !== "undefined"){
                campaign_functions.set_access_address();
        }
    },
    delete_appointment_html: function (id, urn) {
        var mheader = 'Confirm Cancellation';
        var mbody = '<form class="form-horizontal appointment-cancellation-form" style="padding:0 20px"><div class="row"><div class="col-lg-12"><input type="hidden" id="appointment-id" value="' + id + '" /><input type="hidden" id="urn" value="' + urn + '" /><div class="form-group"><label>Are you sure you want to cancel this appointment?</label><textarea class="form-control" name="cancellation_reason" style="height:50px" placeholder="Please give a reason for the cancellation"/></textarea></div></div></form>';
        var mfooter = '<button data-modal="edit-appointment" data-id="' + id + '" class="btn btn-default pull-left"  type="button">Back</button> <button class="btn btn-primary pull-right delete-appointment" type="button">Confirm</button>';
        modals.load_modal(mheader, mbody, mfooter);
    },
    confirm_other_appointment_address: function () {
        var new_postcode = $modal.find('[name="new_postcode"]').val();
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
                if ($modal.find('[name="add1"]').val() != '') {
                    new_address += $modal.find('[name="add1"]').val();
                    if ($modal.find('[name="add2"]').val() != '') {
                        new_address += ', ' + $modal.find('[name="add2"]').val();
                    }
                    if ($modal.find('[name="add3"]').val() != '') {
                        new_address += ', ' + $modal.find('[name="add3"]').val();
                    }
                    if ($modal.find('[name="county"]').val() != '') {
                        new_address += ', ' + $modal.find('[name="county"]').val();
                    }
                    if ($modal.find('[name="new_postcode"]').val() != '') {
                        new_address += ', ' + response.postcode;
                    }
                    $('#addresspicker').prepend('<option value="' + new_address + '|' + response.postcode + '">' + new_address + '</option>');
                    $('.addresspicker').selectpicker('refresh').selectpicker('val', $('#addresspicker option:first').val());
					  $('#add-appointment-address').hide();
					  $('#select-appointment-address').show();
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

    confirm_other_appointment_access_address: function () {
        var new_postcode = $modal.find('[name="access_new_postcode"]').val();
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
                if ($modal.find('[name="access_add1"]').val() != '') {
                    new_address += $modal.find('[name="access_add1"]').val();
                    if ($modal.find('[name="access_add2"]').val() != '') {
                        new_address += ', ' + $modal.find('[name="access_add2"]').val();
                    }
                    if ($modal.find('[name="access_add3"]').val() != '') {
                        new_address += ', ' + $modal.find('[name="access_add3"]').val();
                    }
                    if ($modal.find('[name="access_county"]').val() != '') {
                        new_address += ', ' + $modal.find('[name="access_county"]').val();
                    }
                    if ($modal.find('[name="access_new_postcode"]').val() != '') {
                        new_address += ', ' + response.postcode;
                    }
                    $('#accessaddresspicker').prepend('<option value="' + new_address + '|' + response.postcode + '">' + new_address + '</option>');
                    $('.accessaddresspicker').selectpicker('refresh').selectpicker('val', $('#accessaddresspicker option:first').val());
					$('#add-appointment-access-address').hide();
					  $('#select-appointment-access-address').show();
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

    search_records: function (data_action,date) {
        $.ajax({
            url: helper.baseUrl + 'modals/search_records',
            type: 'POST',
            dataType: 'html'
        }).done(function (response) {
            var mheader = "Search Record";
            var mbody = '<div class="row"><div class="col-lg-12">' + response + '</div></div>';
            var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>' +
                          '<button class="btn btn-primary pull-right" id="search-records-btn" data-action="'+data_action+'" data-start="'+date+'" type="button">Search</button>';
            modals.load_modal(mheader, mbody, mfooter);
            modal_body.css('overflow', 'visible');
            modal_dialog.css('width', "90%");
            $('.record-form').on("keyup keypress", function (e) {
                var code = e.keyCode || e.which;
                if (code == 13) {
                    e.preventDefault();
                    return false;
                }
            });
        });
    },
    search_records_func: function(data_action,start){
        if(modal_body.find('#campaign').val()==""){
            flashalert.danger("Please select a campaign");
        } else {
            $.ajax({
                url: helper.baseUrl + 'search/search_records',
                type: "POST",
                dataType: "JSON",
                data: modal_body.find('.record-form').serialize(),
                beforeSend:function(){
                    modal_body.find('#dupes-found').html('<img src="' + helper.baseUrl + 'assets/img/ajax-loader-bar.gif" /> ');
                }
            }).done(function (response) {
                if (response.success) {
                    if(response.data.length>0){
                        if(modal_body.find('#campaign').val()==""){
                            var camphead = "<th>Campaign</th>";
                            var campbody = true;
                        } else {
                            var camphead = "";
                            var campbody = false;
                        }
                        var table = "<div class='table-responsive'><table class='small table-condensed table table-striped'><thead>"+camphead+"<th>Data</th><th>Name</th><th>Address</th><th>Postcode</th><th>Status</th><th>Date Added<th></thead><tbody>";
                        $.each(response.data,function(i,row){
                            table += "<tr class='pointer' data-modal='"+data_action+"' data-start='"+start+" 09:00' data-urn='"+row.urn+"'>";
                            if(campbody){ table +=  "<td>"+row.campaign_name+"</td>" }
                            table += "<td>"+row.source_name+"</td><td>"+row.name+"</td><td>"+row.add1+"</td><td>"+row.postcode+"</td><td>"+row.status_name+"</td><td>"+row.date_added+"</td></tr>";
                        });
                        table += "</tbody></table></div>";
                        modal_body.find('#dupes-found').html(table);
                    } else {
                        modal_body.find('#dupes-found').html("<p class='text-success'><span class='glyphicon glyphicon-remove'></span> No Records were found.</p>");
                    }
                } else {
                    flashalert.danger(response.msg);
                    modal_body.find('#dupes-found').html("<p class='text-danger'><span class='glyphicon glyphicon-remove'></span> "+response.msg+"</p>");
                }
            })
        }
    },
    get_addresses: function() {
        var addresses;
        var postcode = modal_body.find('.record-form').find('#postcode').val();
        var house_number = modal_body.find('.record-form').find('#house-number').val();
        modal_body.find('.record-form').find('#collapse input').val('');
        modal_body.find('.record-form').find('input[name="postcode"]').val(postcode);

        $.ajax({
            url: helper.baseUrl + 'ajax/get_addresses_by_postcode',
            type: "POST",
            dataType: "JSON",
            data: {postcode: postcode, house_number: house_number}
        }).done(function (response) {
            if (response.success) {
                modal_body.find('.record-form').find('#postcode').val(response.postcode);
                addresses = response.data;
                flashalert.info("Please select the correct address");
                var options = "<option value=''>Select one address...</option>";

                $.each(response.data, function (i, val) {
                    var option =
                        (val.add1 ? val.add1 : '') +
                        (val.add2 ? ", " + val.add2 : '') +
                        (val.add3 ? ", " + val.add3 : '') +
                        (val.add4 ? ", " + val.add4 : '') +
                        (val.locality ? ", " + val.locality : '') +
                        (val.city ? ", " + val.city : '') +
                        (val.county ? ", " + val.county : '') +
                        (typeof val.postcode_io.country != "undefined" ? ", " + val.postcode_io.country : '') +
                        (val.postcode ? ", " + val.postcode : '');

                    if(option.length>30)
                    {
                        option = option.substr(0,30)+'...';
                    }

                    options += '<option value="' + i + '">' +
                            option +
                        '</option>';
                });
                modal_body.find('.record-form').find('#addresspicker')
                    .html(options)
                    .selectpicker('refresh');

                //If the house number is found set this option by default
                if (response.address_selected !== null && response.address_selected !== undefined) {
                    var address = addresses[response.address_selected];
                    $('.record-form').find('#add1').val(address.add1);
                    $('.record-form').find('#add2').val(address.add2);
                    $('.record-form').find('#add3').val(address.add3);
                    $('.record-form').find('#add4').val(address.add4);
                    $('.record-form').find('#locality').val(address.locality);
                    $('.record-form').find('#city').val(address.city);
                    $('.record-form').find('#county').val(address.county);
                    $('.record-form').find('#country').val(address.postcode_io.country);

                    modal_body.find('.record-form').find('#addresspicker')
                        .val(response.address_selected)
                        .selectpicker('refresh');
                }
                //modal_body.css('overflow', 'auto');
                modal_body.find('#addresspicker-div').show();
            }
            else {
                //modal_body.css('overflow', 'auto');
                modal_body.find('#addresspicker-div').hide();
                flashalert.danger("No address was found. Please enter manually");
                modal_body.find('#complete-address').trigger('click');
            }
        });

        modal_body.find('.addresspicker').change(function () {

            var selectedId = $(this).val();
            var address = addresses[selectedId];
            modal_body.find('.record-form').find('#postcode').val(address.postcode);
            modal_body.find('.record-form').find('#house_number').val(address.house_number);
            modal_body.find('.record-form').find('#add1').val(address.add1);
            modal_body.find('.record-form').find('#add2').val(address.add2);
            modal_body.find('.record-form').find('#add3').val(address.add3);
            modal_body.find('.record-form').find('#add4').val(address.add4);
            modal_body.find('.record-form').find('#locality').val(address.locality);
            modal_body.find('.record-form').find('#city').val(address.city);
            modal_body.find('.record-form').find('#county').val(address.county);
            modal_body.find('.record-form').find('#country').val(address.postcode_io.country);

        });
    },

    show_modal: function () {
        $modal.modal({
            backdrop: 'static',
            keyboard: false
        })
    },
    clear_footer: function () {
        modal_footer.empty();
    },
    load_modal: function (mheader, mbody, mfooter, fixed_modal) {
        if (fixed_modal && $("#modal").hasClass('ui-draggable')) {
            $("#modal").draggable('disable')
        } else {
            $("#modal").draggable({
                handle: ".modal-header,.modal-footer"
            });
        }
		
        modal_body.css('padding', '20px');
        modal_header.html(mheader);
        modal_body.html(mbody);
		if(modal_body.find('.nav-tabs').length>0){
		 modal_body.css('padding', '0');
		} else {
		 modal_body.css('padding', '20px');	
		}
		
        modal_footer.html(mfooter);
        if (!$modal.hasClass('in')) {
            modals.show_modal();
        }

        $modal.find('.selectpicker').selectpicker();

        $modal.find('.tt').tooltip();
        $modal.find('.datetime').datetimepicker({
            format: 'DD/MM/YYYY HH:mm',
			sideBySide:true
        });
        $modal.find('.dateonly').datetimepicker({
            format: 'DD/MM/YYYY'
        });
        $modal.find('.dateyears').datetimepicker({
            viewMode: 'years',
            format: 'DD/MM/YYYY'
        });
        //this function automatically sets the end date for the appointment 1 hour ahead of the start date
		$(".startpicker").on("dp.show", function (e) {
			/*$('#attendee-select').prop('disabled',true).html('<option value="loading">Please wait</option>').selectpicker('refresh').selectpicker('val','loading');*/
		});
		
        $(".startpicker").on("dp.hide", function (e) {
            var m = moment(e.date, "DD/MM/YYYY HH:mm");
			var sql = (m.format("YYYY-MM-DD HH:mm"));
            $('.endpicker').data("DateTimePicker").date(e.date);
            $('.endpicker').data("DateTimePicker").date(m.add( 1,'hours').format('DD/MM/YYYY HH:mm'));
			//modals.get_available_attendees(sql);
            modals.set_appointment_confirmation();
        });

        $modal.find("#tabs").tab();
		modals.set_size();
    },
	get_available_attendees:function(sql){
		$.ajax({
			url:helper.baseUrl+'appointments/get_available_attendees',
			type:"POST",
			dataType:"JSON",
			data: { datetime:sql,urn:record.urn }
		}).done(function(){
			
		});
	},
    set_size: function () {
		//there is a slight delay while the modal fades in which is fixed with the 500ms timeout function
		setTimeout(function() {
		if(device_type!=="default"){
        //this will make the modals mobile responsive :)
        if ($modal.hasClass('in')) {
            var height = $(window).height() - 20;
            var mheight = $('.modal-dialog').height();
            if (mheight > height) {
                modal_body.css('max-height', 'none').css('height', 'auto');
                $('body').removeClass('modal-open');
                $modal.css('position', 'absolute');
                $('.container-fluid').css('height', mheight + 50 + 'px').css('overflow', 'hidden');
            } else {
                $modal.css('position', 'fixed').css('overflow', 'auto');
                $('body').addClass('modal-open');
                $('.container-fluid').css('height', '100%').css('overflow', 'auto');
            }
        }
		}
		}, 500);
    },
    set_location: function () {

        modals.default_buttons();
        modal_header.text('Set location');
        modal_body.html('<p>You must set a location to calculate distances and journey times</p><div class="form-group"><label>Enter Postcode</label><div class="input-group"><input type="text" class="form-control current_postcode_input" placeholder="Enter a postcode..."><div class="input-group-addon pointer btn locate-postcode"><span class="glyphicon glyphicon-map-marker"></span> Use my location</div></div>');
        $(".confirm-modal").off('click');
        $('.confirm-modal').on('click', function (e) {
            var postcode_saved = location.store_location($('.current_postcode_input').val());
            if (postcode_saved) {
                $modal.modal('toggle');
            }
        });
        if (!$modal.hasClass('in')) {
            modal.show_modal();
        }

    },
    import_appointments: function () {

        var mheader = "Import appointments from outlook (.ics)";
        var mbody = '<div id="step-2" class="step-container">' +
                        '<p>Import your appointments from a ICS file</p>' +
                        '<span class="btn btn-default fileinput-button">' +
                            '<i class="glyphicon glyphicon-plus"></i>' +
                            '<span>Select file...</span>' +
                            '<input id="fileupload" type="file" name="files[]" data-url="import/import_ical">' +
                        '</span>' +
                        '<br>' +
                        '<br>' +
                        '<div id="progress" class="progress">' +
                        '   <div class="progress-bar progress-bar-success"></div>' +
                        '</div>' +
                        '<div id="files" class="files pull-left">' +
                            '<span id="filename"></span>' +
                            '<br>' +
                            '<span id="file-status"></span>' +
                        '</div>' +
                        '<button style="display:none" class="btn btn-success pull-right marl goto-step-3">Continue</button> ' +
                    '</div>';
        //var mbody = '<p>You must select a ics file to import the appointments</p><div class="form-group"><label>Enter Postcode</label><div class="input-group"><input type="text" class="form-control current_postcode_input" placeholder="Enter a postcode..."><div class="input-group-addon pointer btn locate-postcode"><span class="glyphicon glyphicon-map-marker"></span> Use my location</div></div>';
        var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>';

        modals.load_modal(mheader, mbody, mfooter)

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
                $modal.find('#planner_status').text('This record is in your journey planner. You can remove or reschedule it below').addClass('text-success');
                $modal.find('#remove-from-planner').show();
				$modal.find('#save-planner').hide();
            } else {
                flashalert.danger(response.msg);
            }
        });
    },
	  confirm_remove_from_planner: function (urn,user_id) {
            var mheader = "Remove from planner";
            var mbody = "Are you sure you want remove this item from the planner?";
            modals.load_modal(mheader, mbody);
			modals.default_buttons();
            $('.confirm-modal').click(function () {
                modals.remove_from_planner(urn,user_id,true);
            });
        },
    remove_from_planner: function (urn,user_id,planner_page) {
        $.ajax({
            url: helper.baseUrl + 'planner/remove_record',
            data: {
                urn: urn,
				user_id:user_id
            },
            type: "POST",
            dataType: "JSON"
        }).done(function (response) {
            if (response.success) {
                flashalert.success(response.msg);
				if(planner_page){
				$modal.modal('toggle');
				planner.populate_table();
				} else {
                $modal.find('#planner_status').text('This record is not in your journey planner. You can add it below').removeClass('text-success');
                $modal.find('#remove-from-planner').hide();
				$modal.find('#save-planner').show();
				}
            }
        });
    },
    reset_table: function () {
        modals.default_buttons();
        modal_header.text('Reset table');
        modal_body.html('<p>This will clear any filters that have been set on the table</p><p>Are you sure you want to reset the table filters?</p>');

        if (!$modal.hasClass('in')) {
            modal.show_modal();
        }
    },
    view_record: function (urn,tab) {
        $.ajax({
            url: helper.baseUrl + 'modals/view_record',
            type: "POST",
            dataType: "JSON",
            data: {
                urn: urn
            }
        }).done(function (response) {
            modals.view_record_html(response.data,tab);
        });
    },
    default_buttons: function () {
        modal_footer.empty();
        modal_footer.append('<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>');
        modal_footer.append('<button class="btn btn-primary confirm-modal" type="button">Confirm</button>');
    },
    update_footer: function (content) {
        modal_footer.empty();
		console.log(content);
        modal_footer.html(content);
    },
    view_record_html: function (data,tab) {
        var mheader = "View Record <small>URN: " + data.urn +"</small>";
        var mbody = '<ul id="tabs" class="nav nav-tabs" role="tablist"><li class="active"><a role="tab" data-toggle="tab" href="#tab-records">Record</a></li>';
		if (helper.permissions['view history'] > 0) {
		  mbody +=	'<li><a role="tab" data-toggle="tab" href="#tab-history">History</a></li>';
		}
		if (helper.permissions['view appointments'] > 0) {
		  mbody +=	'<li><a role="tab" data-toggle="tab" href="#tab-apps">Appointments</a></li>';
		}
        if (data.custom_info.length > 0) {
           '<li><a role="tab" data-toggle="tab" href="#tab-custom">' + data.custom_panel_name + '</a></li>';
        }
        if (helper.permissions['planner'] > 0) {
            mbody += '<li><a role="tab" data-toggle="tab" href="#tab-planner">Planner</a></li>';
        }

        mbody += '</ul><div class="tab-content">';
        //records tab
        mbody += '<div role="tabpanel" class="tab-pane active" id="tab-records"><div class="row">';
		$.each(data.record,function(title,column){
			
		 mbody += '<div class="col-sm-6"><h4>'+title+'</h4>';	
		 if(column.display=="table"){
			 var list_icon = column.list_icon.length>0?"<i class='fa "+column.list_icon+"'></i> ":"";
		var colbody = "<table class='"+column.table_class+"'>";	 
		 $.each(column.fields,function(key,val){
			 if(val.modal_keys=="1"){
			 colbody += "<tr><td>"+list_icon+"</td><td><strong>"+key+"</strong></td><td>"+val.value+"</td></tr>";
			 } else if(val.modal_keys=="2"){
			 colbody += "<tr><td colspan='3'><strong>"+key+"</strong></td></tr><tr><td colspan='3'>"+val.value+"</td></tr>";	 
			 } else {
			 colbody += "<tr><td colspan='3'>"+val.value+"</td></tr>";	 
			 }
		 });
		 colbody += "</table>";
		 }
		 if(column.display=="list"){
		var list_icon = "";
		var list_style = "";
		if(column.list_icon.length>0){
			 list_icon = "<span class='fa "+column.list_icon+"'></span>";
			 list_style = "style='list-style:none'"
		}
		var colbody = "<ul "+list_style+">";	 
		 $.each(column.fields,function(key,val){
			 colbody += "<li>"+list_icon+" <strong>"+key+":</strong> "+val+"</li>";
		 });
		 colbody += "</ul>";
		 }
		 mbody += colbody;
		 mbody +=  "</div>";
		});

		mbody += '</div></div>';
		
		
		
		/*<div class="col-sm-6"><h4>Details</h4><table class="table small"><tr><th>Campaign</th><td>' + data.campaign_name + '</td></tr><tr><th>Reference</th><td>' + data.client_ref + '</td></tr><tr><th>Name</th><td>' + data.name + '</td></tr><tr><th>Contact</th><td>' + data.fullname + '</td></tr><tr><th>Ownership</th><td>' + data.ownership + '</td></tr><tr><th>Comments</th><td>' + data.comments + '</td></tr></table></div><div class="col-sm-6"><h4>Status</h4><table class="table small"><tr><th>Record Status</th><td>' + data.status_name + '</td></tr><tr><th>Parked Status</th><td>' + data.parked + '</td></tr><tr><th>Last Outcome</th><td>' + data.outcome + '</td></tr><tr><th>Last Action</th><td>' + data.lastcall + '</td></tr><tr><th>Next Action</th><td>' + data.nextcall + '</td></tr></table></div></div></div>'; */
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

                planner_form += '<div class="form-group relative"><label>Select Date</label><input value="' + data.planner_date + '" class="form-control dateonly" id="planner_date" placeholder="Choose date..." /></div>';
            } else {
                planner_form += '<p class="text-danger">You cannot add this record to the journey planner because it has no address</p>'
            }
				
            mbody += '<div role="tabpanel" class="tab-pane" id="tab-planner">';
            if (data.planner_id) {
                mbody += '<p id="planner_status" class="text-success">This record is in your journey planner. You can remove or reschedule it below</p>';
                mbody += planner_form;
                mbody += ' <button class="btn btn-danger pull-right" id="remove-from-planner" data-urn="' + data.urn + '" href="#">Remove from planner</button> ';
				mbody += ' <button class="marl btn btn-info pull-right" style="display:none" id="save-planner" data-urn="' + data.urn + '" href="#">Save to planner</button> ';
            } else {
                mbody += '<p id="planner_status">This record is not in your journey planner. You can add it below</p>';
                mbody += planner_form;
				mbody += ' <button class="marl btn btn-info pull-right" id="save-planner" data-urn="' + data.urn + '" href="#">Save to planner</button> ';
                mbody += ' <button style="display:none" class="btn btn-danger pull-right" style="display:none" id="remove-from-planner" data-urn="' + data.urn + '" href="#">Remove from planner</button> ';
            }
			mbody += '<div class="clearfix"></div>'

        }


        mbody += '</div>';
        merge_btn = "";
        if (typeof record !== "undefined"&&record.urn!==data.urn) {
            merge_btn = ' <button class="btn btn-info pull-right" data-modal="merge-record" data-urn="' + data.urn + '" data-merge-target="' + record.urn + '">Merge</button>';
        }
        var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>';
		if(helper.permissions['view record'] > 0){
		mfooter += '<a class="btn btn-primary pull-right" href="' + helper.baseUrl + 'records/detail/' + data.urn + '">View Record</a>' } 
		mfooter += merge_btn;
		if (data.planner_postcode&&getCookie('current_postcode')) {
            mfooter += '<a target="_blank" class="btn btn-info pull-right" href="' + mapLink + '?q=' + data.planner_postcode + '",+UK">View Map</a>';
        }
        if (data.planner_postcode&&getCookie('current_postcode')) {
            mfooter += '<a target="_blank" class="btn btn-info pull-right" href="' + mapLink + '?zoom=2&saddr=' + getCookie('current_postcode') + '&daddr=' + data.planner_postcode + '">Navigate</a>';
        }
        modals.load_modal(mheader, mbody, mfooter);
		$modal.find('.nav-tabs a[href="#'+tab+'"]').tab('show');
    },
    view_filter_options: function () {
        $.ajax({
            url: helper.baseUrl + 'modals/view_filter_options',
            type: "POST",
            dataType: 'JSON'
        }).done(function (response) {
            if (!response.success) {
                flashalert.danger(response.msg);
            }
            modals.view_filter_options_html(response.data);
        });
    },
    view_filter_options_html: function (data) {

        var filter_options = "";
        filter_options += "<div style='float:right; padding:8px 0;'>"+$('.dataTables_info').text()+"</div>";
        var clear_filters_opt = true;
        if (data) {
            $.each(data, function (i, val) {
                var remove_btn = "";
                filter_options += "<div style='width:200px; padding:8px 0; border-bottom: 1px dashed #ccc'>";
                if(val.removable){
                    remove_btn = " <span data-field='" + val.name + "' class='remove-filter-option glyphicon glyphicon-remove pointer red small'></span>";
                }
                else {
                    clear_filters_opt = false;
                }
                filter_options += "<strong>" + i + remove_btn+"</strong>";

                if (typeof val.values === "string") {
                    filter_options += "<ul><li>" + val.values + "</li></ul>";
                } else {
                    filter_options += "<ul>";
                    $.each(val.values, function(x, v) {
                        filter_options += "<li>" + v + "</li>";
                    });
                    filter_options += "</ul>";
                }
                filter_options += "</div>";
            });
        }
        else {
            filter_options += "<div>No filters have been applied. You can use search options on this page to find records matching a specific critera</div>";
        }

        var mheader = "Your search options ";
        var mbody = filter_options;

        var clear_filters_btn = (clear_filters_opt)?'<a class="btn btn-primary clear-filters-btn pull-right">Clear Filters</a> ':'';

        var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>' +
                        clear_filters_btn;

        modals.load_modal(mheader, mbody, mfooter)
    },
    remove_filter: function(field) {
        $.ajax({
            url: helper.baseUrl + 'modals/remove_filter_option',
            data: {field: field},
            type: "POST",
            dataType: "JSON"
        }).done(function(response) {
            $('.modal-header').append("Applying filters... <img src='" + helper.baseUrl + "assets/img/ajax-loader-bar.gif' />");
            location.reload(true);
        });
    },
    clear_filters: function() {
        $.ajax({
            url: helper.baseUrl + 'modals/clear_filter_option',
            type: "POST",
            dataType: "JSON"
        }).done(function(response) {
            $('.modal-header').append("Applying filters... <img src='" + helper.baseUrl + "assets/img/ajax-loader-bar.gif' />");
            location.reload(true);
        });
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
            $modal.on('click', '.nav-tabs .general-tab a,.nav-tabs .phone-tab a,.nav-tabs .address-tab a', function (e) {
                e.preventDefault();
                var tabname = $(this).attr('href');
				console.log(tabname);
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
                $tab = $modal.find('.tab-pane.active');
                $tab.find('form').hide();
                $tab.find('.table-container').show();
                //swap the buttons back
                modals.update_footer('<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>');
            });
        },
        edit_item_form: function (id, action) {
            $tab = $modal.find('.tab-pane.active');
            $tab.find('.item-id').val(id);
            if (action == "edit_address") {
                var page = "get_contact_address";
                $tab.find('form').find('input').alphanum({
                    allow: '', // Specify characters to allow
                    disallow: ''  // Specify characters to disallow
                });
                $modal.find('.address-select').hide();
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

						if(key=="primary"){
							if(val=="1"){
							$tab.find('form input[name="primary"]').val('1');
							$('#primary-toggle').bootstrapToggle('on')
                            } else {
                                $tab.find('form input[name="primary"]').val('0');
                                $('#primary-toggle').bootstrapToggle('off')
                            }
						}

                        if(key=="visible"){
                            if(val=="1"){
                                $tab.find('form input[name="visible"]').val('1');
                                $('#visible-toggle').bootstrapToggle('on')
                            } else {
                                $tab.find('form input[name="visible"]').val('0');
                                $('#visible-toggle').bootstrapToggle('off')
                            }
                        }
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
            $tab = $modal.find('.tab-pane.active');
            var type = $tab.attr('id');
            $tab.find('.table-container').hide();
            $tab.find('form')[0].reset();
            $tab.find('form').show();
            //reset the item id
            $tab.find('.item-id').val('');

            $('#visible-toggle').bootstrapToggle('on');

            $modal.find('.address-select').hide();

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
                data: $modal.find('.tab-content .tab-pane.active').find('form').serialize()
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
                if (type == "edit") {
                    modals.contacts.load_tabs(id, tab);
                }
					if(typeof campaign_functions.contact_form_setup !== "undefined"){
					campaign_functions.contact_form_setup();
				}
				
            });
        },
        load_tabs: function (id, item_form) {
            if (item_form !== "general") {
                var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>';
                $modal.find('#' + item_form + ' form').hide();
                $modal.find('#' + item_form + ' .table-container').show();
            } else {
                var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button><button type="submit" class="btn btn-primary save-contact-general">Save changes</button>';
                $modal.find('#phone form, #address form').hide();
                $modal.find('#phone .table-container,#address .table-container').show();
                $modal.find('.save-contact-general').remove();
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
                        $modal.find('#general [name="' + key + '"]').val(val);
                    });

                    if (response.data.telephone) {
                        $modal.find('#phone tbody').empty();
                        $modal.find('#phone .table-container,#phone .table-container table').show();
                        $modal.find('#phone .none-found').hide();
                        $.each(response.data.telephone, function (key, val) {
                            if (val.tel_tps == "0") {
                                var $tps = "<span style='color:green' class='glyphicon glyphicon-ok-sign tt'  data-toggle='tooltip' data-placement='right' title='This number is NOT TPS registerd'></span>";
                            } else if (val.tel_tps == "1") {
                                var $tps = "<span style='color:red' class='glyphicon glyphicon-exclamation-sign tt'  data-toggle='tooltip' data-placement='right' title='This number IS TPS registered'></span>";
                            } else {
                                var $tps = "<span class='glyphicon glyphicon-question-sign tt'  data-toggle='tooltip' data-placement='right' title='TPS Status is unknown'></span>"
                            }
                            $phone = "<tr><td>" + val.tel_name + "</td><td>" + val.tel_num + "</td><td class='tps-contact-label'>" + $tps + "</td><td style='width:140px'><span class='btn btn-default btn-xs contact-item-btn' data-action='edit_phone' data-id='" + val.tel_id + "'><span class='glyphicon glyphicon-pencil'></span> Edit</span> <span class='marl btn btn-default btn-xs' data-modal='delete-contact-phone' contact-id='" + response.data.general.contact_id + "' data-id='" + val.tel_id + "'><span class='glyphicon glyphicon-trash'></span> Delete </span> </td></tr>";
                            $modal.find('#phone tbody').append($phone);
                        });
                    } else {
                        $modal.find('#phone .table-container table').hide();
                        $modal.find('#phone .none-found').show();
                    }
                    if (response.data.address) {
                        $modal.find('#address tbody').empty();
                        $modal.find('#address .table-container, #address .table-container table').show();
                        $modal.find('#address .none-found').hide();
                        $.each(response.data.address, function (key, val) {
                            var $primary = "";
                            var $visible = "<span class='glyphicon glyphicon-eye-close red'></span>";
                            if (val.primary == 1) {
                                var $primary = "<span class='glyphicon glyphicon-ok-sign'></span>";
                            }
                            if (val.visible == 1) {
                                var $visible = "<span class='glyphicon glyphicon-eye-open highlight_green'></span>";
                            }
                            $address = "<tr><td>" + $visible + "</td><td>" + val.description + "</td><td>" + val.add1 + "</td><td>" + val.postcode + "</td><td>" + $primary + "</td><td style='width:140px'><span class='contact-item-btn btn btn-default btn-xs' data-action='edit_address' data-id='" + val.address_id + "'><span class='glyphicon glyphicon-pencil'></span> Edit</span> <span class='marl del-item-btn btn btn-default btn-xs' data-modal='delete-contact-address' contact-id='" + response.data.general.contact_id + "' data-id='" + val.address_id + "'><span class='glyphicon glyphicon-trash'></span> Delete</span></td></tr>"
                            $modal.find('#address tbody').append($address);
                        });
                    } else {
                        $modal.find('#address .table-container table').hide();
                        $modal.find('#address .none-found').show();
                    }
                }
                $('.tt').tooltip();
                $modal.find('.tab[href="#' + item_form + '"]').tab('show');

                    if(typeof campaign_functions.contact_tabs_setup !== "undefined"){
                        campaign_functions.contact_tabs_setup();
					 }
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
                    $modal.find('input[name="contact_id"]').val(response.id);
                    $('.phone-tab,.address-tab').show();
                    $('#phone,#address').find('.table-container table').hide();
                    $('.tab-alert').hide();
                }
                record.contact_panel.load_panel(record.urn, response.id);
				if($('#branch-info').length>0){
					campaign_functions.get_branch_info();
				}
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
				$modal.find('#contact-phone-form,#contact-address-form').hide();
                $modal.find('.table-container').show();
                modals.update_footer(buttons);
            }

        }


    },

    referrals: {

        init: function () {
            $(document).on('click', '.save-referral-general', function (e) {
                e.preventDefault();
                modals.referrals.save_referral();
            });
            /* loads the form for a new address to be added*/
            $(document).on('click', '.referral-add-item', function (e) {
                e.preventDefault();
                modals.referrals.new_item_form();
            });
            /*save the new address*/
            $(document).on('click', '.save-referral-address', function (e) {
                e.preventDefault();
                var action = $(this).attr('data-action');
                modals.referrals.save_item(action);
            });
            /*when a tab is changed we should reset the tab content*/
            $modal.on('click', '.nav-tabs .general-tab a,.nav-tabs .phone-tab a,.nav-tabs .address-tab a', function (e) {
                e.preventDefault();
                var tabname = $(this).attr('href');
                modals.referrals.change_tab(tabname);
            });

            $(document).on('click', '[data-modal="delete-referral"]', function (e) {
                modal.delete_referral($(this).attr('data-id'));
            });

            /*initialize the delete item buttons for address*/
            $(document).on('click', '[data-modal="delete-referral-address"]', function (e) {
                e.preventDefault();
                var id = $(this).attr('data-id');
                var rid = $(this).attr('referral-id');
                modals.referrals.confirm_delete_address(id, rid);
            });

            /* go back to the ddress for the referral if they cancel the delete action */
            $(document).on('click', '.cancel-delete-address', function (e) {
                e.preventDefault();
                var id = $(this).attr('data-id');
                modals.referrals.referral_form('edit', id, 'address');
            });
            $(document).on('click', '.referral-item-btn', function (e) {
                e.preventDefault();
                var id = $(this).attr('data-id');
                var action = $(this).attr('data-action');
                modals.referrals.edit_item_form(id, action);
            });
            /*initialize the cancel button on the add/edit referral address form*/
            $(document).on('click', '.cancel-add-item', function (e) {
                e.preventDefault();
                $tab = $modal.find('.tab-pane.active');
                $tab.find('form').hide();
                $tab.find('.table-container').show();
                //swap the buttons back
                modals.update_footer('<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>');
            });
        },
        edit_item_form: function (id, action) {
            $tab = $modal.find('.tab-pane.active');
            $tab.find('.item-id').val(id);
            if (action == "edit_referral_address") {
                var page = "get_referral_address";
                $tab.find('form').find('input').alphanum({
                    allow: '', // Specify characters to allow
                    disallow: ''  // Specify characters to disallow
                });
                $modal.find('.address-select').hide();
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
                if (action == "edit_referral_address") {
                    mfooter += '<button type="submit" class="btn btn-primary save-referral-address" data-action="edit_referral_address">Save Address</button>';
                }
                modals.update_footer(mfooter);
                if (response.success) {
                    $.each(response, function (key, val) {
                        $tab.find('form input[name="' + key + '"]').val(val);
                        $tab.find('select[name="' + key + '"]').selectpicker('val', val);

                        if(key=="primary"){
                            if(val=="1"){
                                $tab.find('form input[name="primary"]').val('1');
                                $('#primary-toggle').bootstrapToggle('on')
                            } else {
                                $tab.find('form input[name="primary"]').val('0');
                                $('#primary-toggle').bootstrapToggle('off')
                            }
                        }

                        if(key=="visible"){
                            if(val=="1"){
                                $tab.find('form input[name="visible"]').val('1');
                                $('#visible-toggle').bootstrapToggle('on')
                            } else {
                                $tab.find('form input[name="visible"]').val('0');
                                $('#visible-toggle').bootstrapToggle('off')
                            }
                        }
                    });
                    $tab.find('.table-container').hide();
                    $tab.find('form').show();

                } else {
                    flashalert.danger(response.msg);
                }
            });

        },

        confirm_delete_address: function (address_id, referral_id) {
            var mheader = "Delete referral address";
            var mbody = "Are you sure you want to delete this address?";
            var mfooter = '<button class="btn btn-default pull-left cancel-delete-address" data-id="' + referral_id + '" type="button">Cancel</button> <button class="btn btn-danger confirm-delete" type="button">Delete</button>';

            modals.load_modal(mheader, mbody, mfooter);
            $('.confirm-delete').click(function () {
                modals.referrals.delete_item(address_id, referral_id, 'delete_referral_address');
            });
        },
        delete_item: function (id, referral_id, action) {
            $.ajax({
                url: helper.baseUrl + 'ajax/' + action,
                type: "POST",
                dataType: "JSON",
                data: {
                    id: id,
                    referral: referral_id
                }
            }).done(function (response) {
                modals.referrals.referral_form('edit', referral_id, response.type);
                flashalert.success("Referral details were updated");
                if (typeof record !== "undefined") {
                    record.referral_panel.load_panel(record.urn, response.id);
                }
            });
        },
        new_item_form: function () {
            $tab = $modal.find('.tab-pane.active');
            var type = $tab.attr('id');
            $tab.find('.table-container').hide();
            $tab.find('form')[0].reset();
            $tab.find('form').show();
            //reset the item id
            $tab.find('.item-id').val('');

            $('#visible-toggle').bootstrapToggle('on');

            $modal.find('.address-select').hide();

            //this will need changing for a back button
            var mfooter = '<button class="btn btn-default cancel-add-item pull-left" type="button">Cancel</button>';
            if (type == "address") {
                mfooter += '<button type="submit" class="btn btn-primary save-referral-address" data-action="add_referral_address">Add Address</button>';
            }
            modals.update_footer(mfooter);

        },
        save_item: function (action) {
            $.ajax({
                url: helper.baseUrl + 'ajax/' + action,
                type: "POST",
                dataType: "JSON",
                data: $modal.find('.tab-content .tab-pane.active').find('form').serialize()
            }).done(function (response) {
                if (response.success) {
                    modals.referrals.load_tabs(response.id, response.type);
                    if (typeof record !== "undefined") {
                        record.referral_panel.load_panel(record.urn, response.id);
                    }
                } else {
                    flashalert.danger(response.msg);
                }
            });
        },
        referral_form: function (type, id, tab) {
            $.ajax({
                url: helper.baseUrl + 'modals/load_referral_form',
                type: "POST",
                dataType: "HTML"
            }).done(function (response) {
                if (type == "edit") {
                    var mheader = "Edit referral";
                } else {
                    var mheader = "Create referral";
                }

                $mbody = $(response);

                if (type == "edit") {
                    $mbody.find('.tab-alert').hide();
                    $mbody.find('tbody').empty();
                    $mbody.find('.address-tab').show();
                    $mbody.find('input[name="referral_id"]').val(id);
                    var mfooter = "";
                } else {
                    $mbody.find('input[name="urn"]').val(id);
                    $mbody.find('.address-tab').hide();
                    $mbody.find('.tab-alert').show();
                    $mbody.find('.table-container').hide();
                    var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button><button type="submit" class="btn btn-primary save-referral-general">Save changes</button>';
                }

                modals.load_modal(mheader, $mbody, mfooter);
                //dont want padding with tabs
                if (type == "edit") {
                    modals.referrals.load_tabs(id, tab);
                }

            });
        },
        load_tabs: function (id, item_form) {
            if (item_form !== "general") {
                var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>';
                $modal.find('#' + item_form + ' form').hide();
                $modal.find('#' + item_form + ' .table-container').show();
            } else {
                var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button><button type="submit" class="btn btn-primary save-referral-general">Save changes</button>';
                $modal.find('#address form').hide();
                $modal.find('#address .table-container').show();
                $modal.find('.save-referral-general').remove();
            }
            modals.update_footer(mfooter);
            $.ajax({
                url: helper.baseUrl + "ajax/get_referral",
                type: "POST",
                dataType: "JSON",
                data: {
                    id: id
                }
            }).done(function (response) {
                if (response.success) {

                    $.each(response.data.general, function (key, val) {
                        $modal.find('#general [name="' + key + '"]').val(val);
                    });

                    if (response.data.address) {
                        $modal.find('#address tbody').empty();
                        $modal.find('#address .table-container, #address .table-container table').show();
                        $modal.find('#address .none-found').hide();
                        $.each(response.data.address, function (key, val) {
                            var $primary = "";
                            var $visible = "<span class='glyphicon glyphicon-eye-close red'></span>";
                            if (val.primary == 1) {
                                var $primary = "<span class='glyphicon glyphicon-ok-sign'></span>";
                            }
                            if (val.visible == 1) {
                                var $visible = "<span class='glyphicon glyphicon-eye-open highlight_green'></span>";
                            }
                            $address = "<tr><td>" + $visible + "</td><td>" + val.description + "</td><td>" + val.add1 + "</td><td>" + val.postcode + "</td><td>" + $primary + "</td><td style='width:140px'><span class='referral-item-btn btn btn-default btn-xs' data-action='edit_referral_address' data-id='" + val.address_id + "'><span class='glyphicon glyphicon-pencil'></span> Edit</span> <span class='marl del-item-btn btn btn-default btn-xs' data-modal='delete-referral-address' referral-id='" + response.data.general.referral_id + "' data-id='" + val.address_id + "'><span class='glyphicon glyphicon-trash'></span> Delete</span></td></tr>"
                            $modal.find('#address tbody').append($address);
                        });
                    } else {
                        $modal.find('#address .table-container table').hide();
                        $modal.find('#address .none-found').show();
                    }
                }
                $('.tt').tooltip();
                $modal.find('.tab[href="#' + item_form + '"]').tab('show');
            });

        },
        save_referral: function () {
            var $form = $('#modal #general').find('form');
            if ($form.find('input[name="referral_id"]').val() == "") {
                var action = "add_referral";
            } else {
                var action = "save_referral";
            }
            $.ajax({
                url: helper.baseUrl + "ajax/" + action,
                type: "POST",
                dataType: "JSON",
                data: $form.serialize()
            }).done(function (response) {
                flashalert.success("Referral details saved");
                //change the add box to an edit box
                if (action == "add_referral") {
                    $modal.find('input[name="referral_id"]').val(response.id);
                    $('.address-tab').show();
                    $('#address').find('.table-container table').hide();
                    $('.tab-alert').hide();
                }
                record.referral_panel.load_panel(record.urn, response.id);
            });

        },
        change_tab: function (tab) {
            modals.clear_footer();
            var buttons = "";
            if (tab == "#general") {
                buttons = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button><button type="submit" class="btn btn-primary save-referral-general">Save changes</button>';
                modals.update_footer(buttons);
            } else {
                buttons = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>';
                $modal.find('.table-container').show();
                $modal.find('#referral-address-form').hide();
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
			   $(document).on('click', '[data-modal="delete-contact"]', function (e) {
                modal.delete_contact($(this).attr('data-id'));
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
                $tab = $modal.find('.tab-pane.active');
                $tab.find('form').hide();
                $tab.find('.table-container').show();
                //swap the buttons back
                modals.update_footer('<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>');
            });
			 /*when a tab is changed we should reset the tab content*/
          $modal.on('click', '.nav-tabs .general-tab a,.nav-tabs .phone-tab a,.nav-tabs .address-tab a', function (e) {
                e.preventDefault();
                var tabname = $(this).attr('href');
                modals.companies.change_tab(tabname);
            });
        },
        edit_item_form: function (id, action) {
            $tab = $modal.find('.tab-pane.active');
            $tab.find('.item-id').val(id);
            if (action == "edit_coaddress") {
                var page = "get_company_address";
                $tab.find('form').find('input').alphanum({
                    allow: '', // Specify characters to allow
                    disallow: ''  // Specify characters to disallow
                });
                $modal.find('.address-select').hide();
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
						if(key=="primary"){
							if(val=="1"){
                                $tab.find('form input[name="primary"]').val('1');
                                $('#primary-toggle').bootstrapToggle('on')
                            } else {
                                $tab.find('form input[name="primary"]').val('0');
                                $('#primary-toggle').bootstrapToggle('off')
                            }
						}

                        if(key=="visible"){
                            if(val=="1"){
                                $tab.find('form input[name="visible"]').val('1');
                                $('#visible-toggle').bootstrapToggle('on')
                            } else {
                                $tab.find('form input[name="visible"]').val('0');
                                $('#visible-toggle').bootstrapToggle('off')
                            }
                        }
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
            $tab = $modal.find('.tab-pane.active');
            var type = $tab.attr('id');
            $tab.find('.table-container').hide();
            $tab.find('form')[0].reset();
            $tab.find('form').show();
            //reset the item id
            $tab.find('.item-id').val('');

            $('#visible-toggle').bootstrapToggle('on');

            $modal.find('.address-select').hide();

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
                data: $modal.find('.tab-content .tab-pane.active').find('form').serialize()
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
                if (type == "edit") {
                    modals.companies.load_tabs(id, tab);
                }

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
				$modal.find('#company-phone-form,#company-address-form').hide();
                $modal.find('.table-container').show();
                modals.update_footer(buttons);
            }
		},
        load_tabs: function (id, item_form) {
            if (item_form !== "general") {
                var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>';
                $modal.find('#' + item_form + ' form').hide();
                $modal.find('#' + item_form + ' .table-container').show();
            } else {
                var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button><button type="submit" class="btn btn-primary save-company-general">Save changes</button>';
                $modal.find('#phone form, #address form').hide();
                $modal.find('#phone .table-container,#address .table-container').show();
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
                        $modal.find('#general [name="' + key + '"]').val(val);
                        if (jQuery.inArray( key, ["conumber", "employees", "turnover"] ) >= 0) {
                            $modal.find('input[name="' + key + '"]').numeric();
                        }
                    });

                    if (response.data.telephone) {
                        $modal.find('#phone tbody').empty();
                        $modal.find('#phone .table-container,#phone .table-container table').show();
                        $modal.find('#phone .none-found').hide();
                        $.each(response.data.telephone, function (key, val) {
                            if (val.tel_tps == "0") {
                                var $tps = "<span style='color:green' class='glyphicon glyphicon-ok-sign tt'  data-toggle='tooltip' data-placement='right' title='This number is NOT TPS registerd'></span>";
                            } else if (val.tel_tps == "1") {
                                var $tps = "<span style='color:red' class='glyphicon glyphicon-exclamation-sign tt'  data-toggle='tooltip' data-placement='right' title='This number IS TPS registered'></span>";
                            } else {
                                var $tps = "<span class='glyphicon glyphicon-question-sign tt'  data-toggle='tooltip' data-placement='right' title='TPS Status is unknown'></span>"
                            }
                            $phone = "<tr><td>" + val.tel_name + "</td><td>" + val.tel_num + "</td><td>" + $tps + "</td><td style='width:140px'><span class='btn btn-xs btn-default company-item-btn' data-action='edit_cophone' data-id='" + val.tel_id + "'><span class='glyphicon glyphicon-pencil'></span> Edit</span> <span class='marl btn btn-default btn-xs' data-modal='delete-company-phone' company-id='" + response.data.general.company_id + "' data-id='" + val.tel_id + "'><span class='glyphicon glyphicon-trash'></span> Delete</span></td></tr>";
                            $modal.find('#phone tbody').append($phone);
                        });
                    } else {
                        $modal.find('#phone .table-container table').hide();
                        $modal.find('#phone .none-found').show();
                    }
                    if (response.data.address) {
                        $modal.find('#address tbody').empty();
                        $modal.find('#address .table-container, #address .table-container table').show();
                        $modal.find('#address .none-found').hide();
                        $.each(response.data.address, function (key, val) {
                            var $primary = "";
                            var $visible = "<span class='glyphicon glyphicon-eye-close red'></span>";
                            if (val.primary == 1) {
                                var $primary = "<span class='glyphicon glyphicon-ok-sign'></span>";
                            }

                            if (val.visible == 1) {
                                var $visible = "<span class='glyphicon glyphicon-eye-open highlight_green'></span>";
                            }
                            $address = "<tr><td>" + $visible + "</td><td>" + val.description + "</td><td>" + val.add1 + "</td><td>" + val.postcode + "</td><td>" + $primary  + "</td><td style='width:140px'><span class='btn btn-default btn-xs company-item-btn' data-action='edit_coaddress' data-id='" + val.address_id + "'><span class='glyphicon glyphicon-pencil'></span> Edit</span> <span class='marl btn btn-default btn-xs del-item-btn' data-modal='delete-company-address' company-id='" + response.data.general.company_id + "' data-id='" + val.address_id + "'><span class='glyphicon glyphicon-trash'></span> Delete</span></td></tr>"
                            $modal.find('#address tbody').append($address);
                        });
                    } else {
                        $modal.find('#address .table-container table').hide();
                        $modal.find('#address .none-found').show();
                    }
                }
                $('.tt').tooltip();
                $modal.find('.tab[href="#' + item_form + '"]').tab('show');
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
                    $modal.find('input[name="company_id"]').val(response.id);
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
                $modal.find('.table-container').show();
                $modal.find('#company-phone-form,#company-address-form').hide();
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
	contact_us:function(){
		var menu = $("nav#menu").data("mmenu");
		menu.close();
		var mheader = "Contact Us";
		var mbody = "<p>If you you need any help or advice using this system please get in touch.</p><p><ul><li>Call us on <b>0161 9199 610</b></li><li>Email us at <b>support@121system.com</b></li></ul><p>Our offices are open 9am-5pm GMT, we endeavour to respond to all emails within 2 working hours</p>";
		 var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-right" type="button">Close</button>';
		 modals.load_modal(mheader,mbody,mfooter);	
	}
}

