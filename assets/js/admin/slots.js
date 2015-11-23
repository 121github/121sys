// JavaScript Document
var admin = {
    //initialize all the generic javascript datapickers etc for this page
    init: function() {
		
		this.panel = $('#slots-panel');
		
        $(document).on('click', '.close-btn', function(e) {
            e.preventDefault();
            admin.hide_edit_form();
        });
		
		$('.timepicker').datetimepicker({format:'HH:mm:ss'});

    },
	    hide_edit_form: function() {
   			$('#form-container').fadeOut(1000, function() {
                $('#table-container').fadeIn();
            });
    },
	 slots: {
        //initalize the group specific buttons 
        init: function() {
			
            admin.panel.on('click', '.add-btn', function() {
                admin.slots.create();
            });
            admin.panel.on('click', '.save-btn', function(e) {
                e.preventDefault();
                admin.slots.save();
            });
            admin.panel.on('click', '.edit-btn', function() {
                admin.slots.edit($(this).attr('data-id'));
            });
            admin.panel.on('click', '.new-btn', function() {
                admin.slots.create();
            });
            admin.panel.on('click', '.del-btn', function() {
                modal.delete_slot($(this).attr('data-id'));
            });
            //start the function to load the groups into the table
            admin.slots.load_slots();
        },
        //this function reloads the groups into the table body
        load_slots: function() {
            $.ajax({
                url: helper.baseUrl + 'admin/get_all_slots',
                type: "POST",
                dataType: "JSON",
				beforeSend:function(){
				$('#form-container').hide();
				$('#table-container').html('<img src="'+helper.baseUrl+'assets/img/ajax-loader-bar.gif" />').show();	
				}
            }).done(function(response) {
				var table = '<table class="table"><thead><tr><th>ID</th><th>Name</th><th>Start</th><th>End</th><th>Description</th><th>Options</th></tr></thead><tbody>';
				if(response.length>0){
                $.each(response, function(i, val) {
                        table += "<tr><td>" + val.appointment_slot_id + "</td><td>" + val.slot_name + "</td><td>" + val.slot_start + "</td><td>" + val.slot_end + "</td><td>" + val.slot_description + "</td><td><button data-id='"+val.appointment_slot_id+"' class='btn btn-default btn-xs edit-btn'>Edit</button> <button class='btn btn-default btn-xs del-btn' data-id='" + val.appointment_slot_id + "'>Delete</button></td></tr>";
                });
				 table += '</body></table>';
				 $('#table-container').html(table);
				} else {
				$('#form-container').hide();
				$('#table-container').html('No slots were found').show();	
				}
            });
        },
        //edit a group
        edit: function(id) {
			$.ajax({
                url: helper.baseUrl + 'admin/get_slot',
                type: "POST",
                dataType: "JSON",
				data: { id:id }
            }).done(function(response) {
			$('#form-container').find('[name="appointment_slot_id"]').val(response.data.appointment_slot_id);
			$('#form-container').find('[name="slot_name"]').val(response.data.slot_name);	
			$('#form-container').find('[name="slot_description"]').val(response.data.slot_description);	
			$('#form-container').find('[name="slot_start"]').val(response.data.slot_start);	
			$('#form-container').find('[name="slot_end"]').val(response.data.slot_end);	
		
			 $('#table-container').fadeOut(1000, function() {
                $('#form-container').fadeIn();
            });
			})
        },
        //add a new group
        create: function() {
			$('#form-container').find('[name="appointment_slot_id"]').val('');	
			$('#form-container').find('[name="slot_name"]').val('');	
			$('#form-container').find('[name="slot_description"]').val('');	
			$('#form-container').find('[name="slot_start"]').val('');	
			$('#form-container').find('[name="slot_end"]').val('');	
          $('#table-container').fadeOut(1000, function() {
                $('#form-container').fadeIn();
            });
        },
        //save a group
        save: function() {
            $.ajax({
                url: helper.baseUrl + 'admin/save_slot',
                type: "POST",
                dataType: "JSON",
                data: $('#form-container').find('form').serialize()
            }).done(function(response) {
                admin.slots.load_slots();
                admin.hide_edit_form();
                flashalert.success("Slot saved");
            });
        },
        remove: function(id) {
            $.ajax({
                url: helper.baseUrl + 'admin/delete_slot',
                type: "POST",
                dataType: "JSON",
                data: {
                    id: id
                }
            }).done(function(response) {
                admin.slots.load_slots();
                if (response.success) {
                    flashalert.success("Slot deleted");
                } else {
                    flashalert.danger("Unable to delete the slot. Contact administrator");
                }
            }).fail(function(response) {
                flashalert.danger("Unable to delete slot. Contact administrator");
            });

        }

    }
}
/* ==========================================================================
MODALS ON THIS PAGE
 ========================================================================== */
var modal = {
    delete_slot: function(id) {
        $('.modal-title').text('Confirm Delete');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').text('Are you sure you want to delete this slot?');
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
            admin.slots.remove(id);
            $('#modal').modal('toggle');
        });
    }
}