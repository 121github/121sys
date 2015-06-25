// JavaScript Document
var template = {
    //initalize the group specific buttons 
    init: function() {
    	$(document).on('click', '.add-btn', function() {
        	template.create();
        });
        $(document).on('click', '.save-btn', function(e) {
            e.preventDefault();
            template.save($(this));
        });
        $(document).on('click', '.edit-btn', function() {
        	template.edit($(this));
        });
        $(document).on('click', '.new-btn', function() {
        	template.create();
        });
        $(document).on('click', '.del-btn', function() {
            modal.delete_template($(this).attr('item-id'));
        });
        $(document).on('click', '.close-btn', function(e) {
        	e.preventDefault();
        	template.cancel();
        });


        template.load_templates();
    },
    //this function reloads the groups into the table body
    load_templates: function() {
    	$.ajax({
            url: helper.baseUrl + 'smstemplates/all_template_data',
            type: "POST",
            dataType: "JSON"
        }).done(function(response) {
            $tbody = $('.template-data .ajax-table').find('tbody');
            $tbody.empty();
            $.each(response.data, function(i, val) {
                if (response.data.length) {
						$tbody
							.append("<tr><td class='template_id'>"
										+ val.template_id
									+ "</td><td class='template_name'>"
										+ val.template_name
									+ "</td><td><button class='btn btn-default btn-xs edit-btn' data-id='"+val.template_id+"' >Edit</button> <button class='btn btn-default btn-xs del-btn' item-id='"
										+ val.template_id
									+ "'>Delete</button></td></tr>");
                }
            });
        });
    },
    //cancel the edit view
    cancel: function() {
    	
            	template.hide_edit_form();
            	template.load_templates();

    },
	//edit a template
    edit: function($btn) {
		var id = $btn.attr('data-id');
		$.ajax({  url: helper.baseUrl + 'templates/template_data',
            data: { id:id },
			type:"POST",
			dataType:"JSON",
		}).done(function(result){
			    	$("button[type=submit]").attr('disabled',false);

        var row = $btn.closest('tr');
        $('form').find('input[name="template_id"]').val(result.data.template_id);
        $('form').find('input[name="template_name"]').val(result.data.template_name);
        $('form').find('input[name="template_from"]').val(result.data.template_from);
        $('form').find('input[name="template_to"]').val(result.data.template_to);
        $('form').find('input[name="template_cc"]').val(result.data.template_cc);
        $('form').find('input[name="template_bcc"]').val(result.data.template_bcc);
        $('form').find('input[name="template_subject"]').val(result.data.template_subject);
		if(result.data.template_unsubscribe=="1"){
			$('form').find('#unsubscribe-yes').prop('checked',true).parent().addClass('active');
			$('form').find('#unsubscribe-no').prop('checked',false).parent().removeClass('active');
		} else {
			$('form').find('#unsubscribe-no').prop('checked',true).parent().addClass('active');
			$('form').find('#unsubscribe-yes').prop('checked',false).parent().removeClass('active');
		}
        $('#summernote').code(result.data.template_body);
        
        var data = {id : $('form').find('input[name="template_id"]').val()};
        
        $.ajax({
            url: helper.baseUrl + "templates/get_campaings_by_template_id",
            type: 'POST',
            dataType: "JSON",
            data: data,
            success: function(data){
    
                    $('#campaigns_select').selectpicker('val',data["data"]).selectpicker('render');
       
            },
            error: function(jqXHR, textStatus, errorThrown) {
             console.log(textStatus+" "+errorThrown);
           }
        });
        

		});
       
    },
	//add a new template
    create: function() {

            	$("button[type=submit]").attr('disabled',false);
                $('form').trigger('reset');
                $('#summernote').code('');
                $('#campaigns_select').selectpicker('val',[]).selectpicker('render');
                $('form').find('input[type="hidden"]').val('');
    	
        $('.ajax-table').fadeOut(1000, function() {
            $('form').fadeIn(1000)
        });
    },
    //save a template
    save: function($btn) {
    	$('textarea[name="template_body"]').html(btoa($('#summernote').code()));
    	$("button[type=submit]").attr('disabled','disabled');
    	$.ajax({
            url: helper.baseUrl + 'smstemplates/save_template',
            type: "POST",
            dataType: "JSON",
            data: $('form').serialize()
        }).done(function(response) {
        	//Reload template table
            template.load_templates();
            //Hide edit form
            template.hide_edit_form();

        	
            flashalert.success("Template saved");
        });
    },
    remove: function(id) {
        $.ajax({
            url: helper.baseUrl + 'smstemplates/delete_template',
            type: "POST",
            dataType: "JSON",
            data: {
                id: id
            }
        }).done(function(response) {
            template.load_templates();
			if(response.success){
            flashalert.success("Template was deleted");
			} else {
			flashalert.danger("Unable to delete template. Contact administrator");
			}
        }).fail(function(response){
			flashalert.danger("Unable to delete template. Contact administrator");
		});

    },
    //this fades out the template edit form and brings back the table
    hide_edit_form: function() {
        $('form,.editor-form').fadeOut(1000, function() {
            $('.ajax-table').fadeIn();
        });
    },

    stripos: function(f_haystack, f_needle, f_offset) {
        //  discuss at: http://phpjs.org/functions/stripos/
        // original by: Martijn Wieringa
        //  revised by: Onno Marsman
        //   example 1: stripos('ABC', 'a');
        //   returns 1: 0

        var haystack = (f_haystack + '')
            .toLowerCase();
        var needle = (f_needle + '')
            .toLowerCase();
        var index = 0;

        if ((index = haystack.indexOf(needle, f_offset)) !== -1) {
            return index;
        }
        return false;
    }

}

/* ==========================================================================
MODALS ON THIS PAGE
 ========================================================================== */
var modal = {

    delete_template: function(id) {
       modal_header.text('Confirm Delete');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        })
		modal_body.text('Are you sure you want to delete this template?');
        $(".confirm-modal").off('click').show();
        $('.confirm-modal').on('click', function(e) {
            template.remove(id);
            $('#modal').modal('toggle');
        });
    }
}