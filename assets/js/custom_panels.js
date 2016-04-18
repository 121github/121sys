var custom_panels = {
    init: function() {
        custom_panels.load_all_panels();
        $(document).on('click', '.add-custom-btn', function() {
            var id = $(this).attr('custom-panel-id');
            custom_panels.load_form(id, false);
        });
        $(document).on('click', '.edit-custom-btn', function() {
            var data_id = $(this).attr('custom-data-id');
            var id = $(this).closest('.custom-panel').attr('custom-panel-id');
            custom_panels.load_form(id, data_id);
        });
		$(document).on('click','[data-tab]',function(){
			var data_id = $(this).attr('data-tab');
			$(this).parent().find('[data-tab]').removeClass('text-primary');
			$(this).addClass('text-primary');
			$(this).closest('.panel-body').find('.table-wrapper').hide();
			$(this).closest('.panel-body').find('.table-wrapper[data-list-id="'+data_id+'"]').show();
			$(this).closest('.panel').find('.edit-custom-btn').attr('custom-data-id',data_id);
		});
        $modal.on('click', '#save-custom-panel', function() {
            var data_id = $(this).attr('custom-data-id');
            var panel_id = $(this).attr('custom-panel-id');
            custom_panels.save_form(panel_id);
        });


        $modal.on('click', '.toggle-buttons button', function(e) {
            e.preventDefault();
            $(this).closest('.toggle-buttons').children().removeClass('active').removeClass('btn-primary').addClass('btn-default');
            $(this).addClass('active').addClass('btn-primary').removeClass('btn-default');
            $(this).closest('.toggle-buttons').next('input').val($(this).text());
        });
		
		 $modal.on('click', '#delete-custom-panel', function() {
			var urn = false;
            var data_id = $(this).attr('custom-data-id');
			if(typeof record!=="undefined"){
			urn = record.urn;
			}
            custom_panels.confirm_delete_data(data_id,urn);
        });
    },
	confirm_delete_data: function (data_id,urn) {
            var mheader = "Delete data";
            var mbody = "Are you sure you want to delete this?";
            modals.load_modal(mheader, mbody);
			modals.default_buttons();
            $('.confirm-modal').click(function () {
				$modal.modal('hide');
                custom_panels.delete_data(data_id,urn);
            });
        },
	 delete_data: function(data_id,urn) {
        $.ajax({
            url: helper.baseUrl + 'ajax/delete_data_panel',
            data: {
               data_id:data_id,
			   urn:urn
            },
            dataType: "JSON",
            type: "POST"
		}).done(function(){
			custom_panels.load_all_panels();
		}).fail(function(){
			flashalert.danger("There was an error deleting the data");
		});
		},
    load_all_panels: function() {
        $('.custom-panel').each(function() {
            var id = $(this).attr('custom-panel-id');
            var display = $(this).attr('custom-panel-display');
            custom_panels.load_panel(id, display, false);
        });
    },
    load_panel: function(panel_id, display, data_id) {
        $.ajax({
            url: helper.baseUrl + 'ajax/load_custom_panel',
            data: {
                urn: record.urn,
                id: panel_id
            },
            dataType: "JSON",
            type: "POST",
            beforeSend: function() {
                $('.custom-panel[custom-panel-id="' + panel_id + '"]').find('.panel-body').html('<img src="' + helper.baseUrl + 'assets/img/ajax-loader-bar.gif" /> ');
            }
        }).done(function(response) {
			if(response.data.length==0){
				$('.custom-panel[custom-panel-id="' + panel_id + '"]').find('.edit-custom-btn').hide();
			} else {
				$('.custom-panel[custom-panel-id="' + panel_id + '"]').find('.edit-custom-btn').show();
			}
            if (display == "table") {
                var html = custom_panels.load_table(response);
            } else {
                var html = custom_panels.load_list(response,data_id);
				  if(!$.isEmptyObject(response.data)) {
				//set the visible one as the latest one added
				 $('.custom-panel[custom-panel-id="' + panel_id + '"]').find('.edit-custom-btn').show().attr('custom-data-id',Object.keys(response.data)[Object.keys(response.data).length-1]);
				  }
            }
            $('.custom-panel[custom-panel-id="' + panel_id + '"]').find('.panel-body').html(html);
			if(typeof campaign_functions.custom_items_loaded !== "undefined"){
			campaign_functions.custom_items_loaded();
			}
        });
    },
    load_table: function(response) {
        var html = "";
        var blanks = "";
        html = "<table class='table "+response.panel.table_class+"'><thead><tr>";
        $.each(response.fields, function(id, field) {
            html += "<th "+(field.hidden=="1"?"style='display:none'":"")+">" + field.name + "</th>";
            blanks += "<td "+(field.hidden=="1"?"style='display:none'":"")+">-</td>";
        });
		if (helper.permissions['edit custom data'] > 0) {
        html += "<th></th>";
		}
        html += "</tr></thead><tbody>";
        if ($.isEmptyObject(response.data)) {
            html += "<tr>" + blanks + "</tr>";
        } else {
            $.each(response.data, function(id, row) {
                html += "<tr>";
                $.each(response.fields, function(field_id, field) {
                    if (typeof row[field_id] !== "undefined") {
                        var value = row[field_id].value;
                    } else {
                        var value = "-";
                    }
                    if (field.type == "checkbox" && value == "on"||field.type == "buttons" && value == "Yes") {
                        value = "<span class='fa fa-check'></span>"
                    }
					
                    html += "<td "+(field.hidden=="1"?"style='display:none'":"")+">" + value + "</td>";
                });
				if (helper.permissions['edit custom data'] > 0) {
                html += "<td><span class='btn btn-default btn-xs pull-right edit-custom-btn marl' custom-data-id='" + id + "'><span class='glyphicon glyphicon-pencil'></span> Edit</span></td>";
				}
                html += "</tr>";
            });
        }

        html += "</tbody></table>";
		html += "<input type='hidden' id='appointment-contact-email' value='"+response.email+"' />";
        return html;
    },
    load_list: function(response,visible_data_set) {
        var html = "";
		 var wrapper = "";
		var table = "";
        var header = "";
		var meta = "";
		var list_counts = "<div class='pull-right'>";
        if ($.isEmptyObject(response.data)) {
			console.log("empty");
            table = "<table class='table "+response.panel.table_class+"'>";
            $.each(response.fields, function(id, field) {
                table += "<tr  "+(field.hidden=="1"?"style='display:none'":"")+"><th>" + field.name + "</th><td>-</td></tr>";
            });
            table += "</table>";
			html=table;
        } else {
			var i=1; var last_set = Object.keys(response.data).length;
            $.each(response.data, function(data_id, row) {
				var hidden = "style='display:none'"; var color=""; var table = "";
				if(data_id==visible_data_set){
				hidden = ""; color="text-primary";
				} else if(i==last_set&&!visible_data_set){ 
				hidden = ""; color="text-primary";
				}
				wrapper = "<div class='table-wrapper' "+hidden+" data-list-id='"+data_id+"'>";
				list_counts += "<span class='fa fa-circle pointer "+color+"' data-tab='"+data_id+"'></span> ";
                table += "<table data-list-id='"+data_id+"' class='table "+response.panel.table_class+"'>";		
				   table += "<tr><th style='white-space:nowrap;padding-right:5px' class='id-title'>ID#</th><td style='width:100%'>" + data_id + "</td></tr>";
                $.each(response.fields, function(id, field) {
                   if(typeof response.data[data_id][id] !== "undefined"){ var val=response.data[data_id][id].value;
				   if(typeof response.data[data_id][id].created_on!=="undefined"){
				   meta = "<span style='padding-bottom:5px' class='pull-left small'>Added on "+response.data[data_id][id].created_on+"</span>";
				   }
				    } else { var val= "-"; }
                    if (field.type == "checkbox" && val == "on"||field.type == "buttons" && val == "Yes") {
                        val = "<span class='fa fa-check'></span>"
                    }
                    table += "<tr "+(field.hidden=="1"?"style='display:none'":"")+"><th style='white-space:nowrap;padding-right:5px'>" + field.name + "</th><td style='width:100%'>" + val + "</td></tr>";
                });
                table += "</table>";
				html += wrapper+meta+table+"</div>";
				html += "<input type='hidden' name='appointment_contact_email' value='"+response.data[data_id]['email']+"' />";
				i++;
            });
			list_counts += "</div>";
			if(i>1){
			html = list_counts+html;
			}
        }
        return html;
    },
    load_form: function(panel_id, data_id) {
        $.ajax({
            url: helper.baseUrl + 'ajax/load_custom_form',
            data: {
                urn: record.urn,
                id: panel_id
            },
            dataType: "JSON",
            type: "POST"
        }).done(function(response) {
            html = "<form><div class='row'>";
            html += "<input type='hidden' name='urn' value='" + record.urn + "'/>";
            if (data_id) {
                html += "<input type='hidden' name='data_id' value='" + data_id + "'/>";
            } else {
                html += "<input type='hidden' name='data_id'/>";
            }
            $.each(response.fields, function(i, column) {
                html += "<div class='col-sm-6'>";
                $.each(column, function(key, field) {
					var field_id = field.field_id;
                    html += "<div class='form-group' "+(field.hidden==1?"style='display:none'":"")+">";
                    html += "<label>" + field.name + "</label>"
                    if (field.tooltip.length > 0) {
                        html += "<span class='pointer glyphicon glyphicon-info-sign marl' data-toggle='tooltip' title='" + field.tooltip + "'></span>";
                    }
                    html += "<br>";
					var value = "";
                    if (data_id && typeof response.data[data_id][i] !== "undefined") {
						if(typeof response.data[data_id][i][field_id] !== "undefined"){
                        var value = response.data[data_id][i][field_id]['value']
                    }
					} 
					if(field.read_only==1){
						html += "<input type='hidden' name='" + field.field_id + "' value='" + value + "' />";
					}
                    if (field.type == "string") {
                        html += "<input type='text' "+(field.read_only==1?"disabled":"")+" name='" + field.field_id + "' class='form-control input-sm' value='" + value + "'/>";
                    }
                    if (field.type == "number" || field.type == "decimal") {
                        html += "<input type='text' "+(field.read_only==1?"disabled":"")+" name='" + field.field_id + "' class='number form-control input-sm' value='" + value + "'/>";
                    }
                    if (field.type == "date") {
                        html += "<input type='text' "+(field.read_only==1?"disabled":"")+" name='" + field.field_id + "' class='form-control date input-sm' value='" + value + "'/>";
                    }
                    if (field.type == "datetime") {
                        html += "<input type='text' "+(field.read_only==1?"disabled":"")+" name='" + field.field_id + "' class='form-control datetime input-sm' value='" + value + "'/>";
                    }
                    if (field.type == "select") {
                        html += "<select "+(field.read_only==1?"disabled":"")+" name='" + field.field_id + "' class='selectpicker'><option value=''>--Please select--</option>";
                        $.each(field.options, function(o, option) {
                            var selected = "";
                            if (value == option.option_name) {
                                selected = "selected";
                            }
                            html += "<option " + selected + " value='" + option.option_name + "'>" + option.option_name + "</option>";
                        });
                        html += "</select>";
                    }
                    if (field.type == "multiple") {
                        html += "<select "+(field.read_only==1?"disabled":"")+" name='" + field.field_id + "[]' multiple class='selectpicker'>";
                        $.each(field.options, function(o, option) {
                            var selected = "";
                            //dirty hack to check if the option value is in the comma seperated list
                            if (value.split(',').indexOf(option.option_id) > -1) {
                                selected = "selected";
                            }
                            html += "<option " + selected + " value='" + option.option_id + "'>" + option.option_name + "</option>";
                        });
                        html += "</select>";
                    }
                    if (field.type == "buttons") {
                        html += "<div class='btn-group toggle-buttons' role='group' >";
                        $.each(field.options, function(o, option) {
							var selected = '';
                            if (value == option.option_name) {
                                 selected = "active";
                            }
                            html += '<button '+(field.read_only==1?"disabled":"")+' class="btn btn-default '+selected+'">' + option.option_name + '</button>';
                        });
                        html += "</div><input name='"+field.field_id+"' type='hidden' value='"+value+"' />";
                    }
                    if (field.type == "checkbox") {
                        var checked = "";
                        if (value == "on") {
                            checked = "checked";
                        }
                        html += "<input "+(field.read_only==1?"disabled":"")+" " + checked + " type='checkbox' name='" + field.field_id + "' class='input-sm'/>";
                    }
                    html += "</div>";
                });
                html += "</div>";
            });
            html += "</div></form>";
            var mheader = $('.custom-panel[custom-panel-id="' + panel_id + '"] .panel-heading').find('.title').text();
            var mfooter = '<button type="submit" class="btn btn-primary pull-right marl" id="save-custom-panel" custom-panel-id="' + panel_id + '">Save</button> '
			if(data_id){
			mfooter += '<button type="submit" class="btn btn-danger pull-right" id="delete-custom-panel" custom-data-id="' + data_id + '">Delete</button>'	
			}
			
			mfooter +=  ' <button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Cancel</button>';
			//load the modal
            modals.load_modal(mheader, html, mfooter);
			
            modal_body.css('overflow', 'visible');
			//initialise the datepickers
            $modal.find('.date').datetimepicker({
                format: 'DD/MM/YYYY',
                showClear: true
            });
            $modal.find('.datetime').datetimepicker({
                format: 'DD/MM/YYYY HH:mm',
                showClear: true,
                sideBySide: true
            });
			//initialize any tooltips
            $modal.find('[data-toggle="tooltip"]').tooltip();
			
			if(!data_id&&typeof campaign_functions.new_custom_item_setup !== "undefined"){
				campaign_functions.new_custom_item_setup();
			} else if(data_id&&typeof campaign_functions.edit_custom_item_setup !== "undefined"){
				campaign_functions.edit_custom_item_setup();
			}		
        });
    },
    save_form: function(panel_id) {
        $.ajax({
            url: helper.baseUrl + 'ajax/save_custom_panel',
            data: $modal.find('form').serialize(),
            dataType: "JSON",
            type: "POST"
        }).done(function(response) {
            var display = $('.custom-panel[custom-panel-id="' + panel_id + '"]').attr('custom-panel-display');
			  if(typeof campaign_functions.save_custom_panel !== "undefined"){
                        campaign_functions.save_custom_panel($modal.find('form'));
                }
            //reload the panel with the updated values
            custom_panels.load_panel(panel_id, display, response.data_id);
            //close the modal
            $modal.modal('toggle');
        });
    },

}