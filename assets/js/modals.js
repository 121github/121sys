// JavaScript Document
$(document).ready(function(){
	$(document).on('click','.modal-set-location',function(){
		modals.set_location();
	});
	$(document).on('click','.modal-set-columns',function(){
		modals.set_columns();
	});
	$(document).on('click','.modal-reset-table',function(){
		console.log("click");
		modals.reset_table();
	});
	
});


var modals = {
	    show_modal: function () {
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        });
    },
	default_buttons: function () {
        $('#modal').find('.modal-footer .btn').remove();
        $('#modal').find('.modal-footer').append('<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>');
        $('#modal').find('.modal-footer').append('<button class="btn btn-primary confirm-modal" type="button">Confirm</button>');
    },
    clear_buttons: function () {
        $('#modal').find('.modal-footer .btn').remove();
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
}
}