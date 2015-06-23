// JavaScript Document
var filter = {
    init: function() {
		filter.custom_fields_panel()
        filter.count_records();
        $(document).on('change','select:not(#campaign-select,.actions_parked_code_select, .actions_parked_code_campaign,.actions_ownership_select,.actions_campaign_select,.actions_template_select)',function() {
            filter.count_records();
        });

        $(document).on('click', '.locate-postcode', function(e) {
            e.preventDefault();
            getLocation();
            setTimeout(function() {
                if (getCookie('current_postcode')) {
                    $('.current_postcode_input').val(getCookie('current_postcode')).trigger('change');
                }
            }, 2000);
        });

        $(document).on('click', '.remove-filter-selection', function() {
            var field = $(this).attr('data-field');
            if ($('select#' + field).prop('multiple')) {
                $('select#' + field).selectpicker('val', []).selectpicker('refresh');
            } else {
                $('select#' + field).val('').selectpicker('refresh');
            }
			if($('[name="' + field + '"]').is(':checkbox')){
			$('input[name="' + field + '"]').prop('checked',false);
			} else {
            $('input[name="' + field + '"]').val('');
			$('input[name="' + field + '[0]"]').val('');
			$('input[name="' + field + '[1]"]').val('');
			}

			$(this).closest('div').remove();
			filter.count_records();
        });

        $(document).on('click', '.no-number', function() {
            filter.set_no_number($(this));
            filter.count_records();
        });
        $(document).on('change', '.sector-select', function() {
            filter.load_subsectors($(this).val());
        });

        $(document).on('blur', 'input[type="text"]', function() {
            var postcode = $('.filter-form').find('input[name="postcode"]').val();
            //The postcode is evaluated in the current_postcode_input click control
            if (!postcode.length) {
                filter.count_records();
            }
        });

        $(".current_postcode_input").change(function() {
            var postcode = $('.filter-form').find('input[name="postcode"]').val();
            filter.check_postcode(postcode);
        });

        $(document).on('click', 'input[type="checkbox"]:not(.all_campaigns_checkbox)', function() {
            filter.count_records();
        });

        $(document).on('click', 'input[name="all_campaigns"]', function(e) {
            if ($('.edit-parkedcode-form').find('input[name="all_campaigns"]').is(":checked")) {
                $('.actions_parked_code_campaign').attr('disabled', true).trigger("chosen:updated");
                $('.actions_parked_code_campaign').val('');
                $('.actions_parked_code_campaign').selectpicker('deselectAll');
            } else {
                $('.actions_parked_code_campaign').attr('disabled', false).trigger("chosen:updated")
            }
        });

        $(document).on('click', '.submit-filter', function(e) {
            e.preventDefault();
            filter.apply_filter();
        });

        $(document).on('click', '.actions-filter', function(e) {
            e.preventDefault();
            filter.actions();
        });

        $(document).on('click', '.close-actions', function(e) {
            e.preventDefault();
            filter.close_actions($(this));
        });

        $('.actions-container').hide();

        //clear the date box buttons
        $(document).on('click', '.clear-input', function(e) {
            $(this).closest('.input-group').find('input').val('');
            filter.count_records();
        });

        $(document).on('click', '.clear-filter', function(e) {
            e.preventDefault()
            $('.no-number').removeClass('btn-danger').closest('.form-group').find('input').prop('disabled', false);
            $('form')[0].reset();
            $('.record-status').selectpicker('render');
            $('input[type="text"],input[type="hidden"], input[type="select"]:not(.record-status,#campaign-select)').val('');
            filter.count_records();
            $('.copy-records').prop('disabled', true);
            $('.copy_records_error').show();
        });

        $(document).on('click', '.change-parkedcode', function(e) {
            $('.actions-content').fadeOut(1000, function() {
                $('.edit-parkedcode-form').fadeIn(1000)
            });
        });
        $(document).on('click', '.actions-parkedcode-btn', function(e) {
            e.preventDefault();
            var urn_list = filter.get_urn_list();
            var parked_code_id = $('.actions_parked_code_select option:selected').val();
            var all_campaigns = ($('.edit-parkedcode-form').find('input[name="all_campaigns"]').is(":checked") ? 1 : 0);
            var suppression_campaigns = $('.actions_parked_code_campaign').val();
            var reason = $('.edit-parkedcode-form').find('textarea[name="reason"]').val();
            var suppress = $('.edit-parkedcode-form').find('input[name="suppress"]').val();
            filter.save_parked_code(urn_list, parked_code_id, all_campaigns, suppression_campaigns, reason, suppress);
        });

        $(document).on('click', '.change-ownership', function(e) {
            $('.actions-content').fadeOut(1000, function() {
                $('.edit-ownership-form').fadeIn(1000)
            });
        });
        $(document).on('click', '.actions-ownership-add-btn', function(e) {
            e.preventDefault();
            var urn_list = filter.get_urn_list();
            var ownership_ar = $('.actions_ownership_select').val();
            filter.add_ownership(urn_list, ownership_ar);
        });

        $(document).on('click', '.actions-ownership-replace-btn', function(e) {
            e.preventDefault();
            var urn_list = filter.get_urn_list();
            var ownership_ar = $('.actions_ownership_select').val();
            filter.replace_ownership(urn_list, ownership_ar);
        });

        $(document).on('click', '.send-email', function(e) {
            $('.actions-content').fadeOut(1000, function() {
                //Get the templates for the campaigns selected
                $.ajax({
                    url: helper.baseUrl + 'search/get_templates_by_campaign_ids',
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        campaign_ids: $('.campaigns_select').val()
                    }
                }).done(function(response) {
                    var $options = '<option value="" >Nothing selected</option>';
                    $.each(response.data, function(k, v) {
                        $options += "<option value='" + v.id + "'>" + v.name + "</options>";
                    });
                    $('#actions_template_select').html($options);
                    $('.actions_template_select').selectpicker('refresh');
                });
                $('.send-email-form').fadeIn(1000);
            });
        });
        $(document).on('click', '.actions-send-email-btn', function(e) {
            e.preventDefault();
            var urn_list = filter.get_urn_list();
            var template_id = $('.actions_template_select').val();
            filter.send_email(urn_list, template_id);
        });

        $(document).on('click', '.copy-records', function(e) {
            $('.actions-content').fadeOut(1000, function() {
                $('.copy-records-form').fadeIn(1000)
            });
        });
        $(document).on('click', '.actions-copy-btn', function(e) {
            e.preventDefault();
            var urn_list = filter.get_urn_list();
            var campaign_id = $('.actions_campaign_select option:selected').val();
            filter.copy_records(urn_list, campaign_id);
        });


        $(document).on('click', '.close-edit-actions-btn', function(e) {
            e.preventDefault();
            filter.close_edit_actions();
        });

        $('.actions_parked_code_select').on('change', function() {
            var selected = $('.actions_parked_code_select option:selected').val();
            var selected_name = $('.actions_parked_code_select option:selected').text();
            if (selected) {
                $('.actions-parkedcode-btn').prop('disabled', false);
                if (selected_name == "Suppressed") {
                    $('.suppress-form').show();
                    $('.edit-parkedcode-form').find('input[name="suppress"]').val('1');
                } else {
                    $('.suppress-form').hide();
                    $('.edit-parkedcode-form').find('input[name="all_campaigns"]').removeAttr('checked')
                    $('.edit-parkedcode-form').find('textarea[name="reason"]').val('');
                    $('.actions_parked_code_campaign').attr('disabled', false).trigger("chosen:updated");
                    $('.actions_parked_code_campaign').val('');
                    $('.actions_parked_code_campaign').selectpicker('deselectAll');
                    $('.edit-parkedcode-form').find('input[name="suppress"]').val('0');
                }
            } else {
                $('.actions-parkedcode-btn').prop('disabled', true);
                $('.suppress-form').hide();
            }
        });
        $('.actions_ownership_select').on('change', function() {
            var selected = $('.actions_ownership_select option:selected').val();
            if (selected) {
                $('.actions-ownership-add-btn').prop('disabled', false);
                $('.actions-ownership-select-msg').hide();
                //$('.actions-ownership-replace-btn').prop('disabled', false);
            } else {
                $('.actions-ownership-add-btn').prop('disabled', true);
                $('.actions-ownership-select-msg').show();
                //$('.actions-ownership-replace-btn').prop('disabled', true);
            }
        });
        $('.actions_template_select').on('change', function() {
            var selected = $('.actions_template_select option:selected').val();
            if (selected) {
                $('.actions-send-email-btn').prop('disabled', false);
            } else {
                $('.actions-send-email-btn').prop('disabled', true);
            }
        });
        $('.actions_campaign_select').on('change', function() {
            var selected = $('.actions_campaign_select option:selected').val();
            if (selected) {
                if ($('.campaigns_select').val()[0] != selected) {
                    $('.actions-copy-btn').prop('disabled', false);
                    $('.actions_copy_records_error').hide();
                } else {
                    $('.actions-copy-btn').prop('disabled', true);
                    $('.actions_copy_records_error').show();
                }
            } else {
                $('.actions-copy-btn').prop('disabled', true);
                $('.actions_copy_records_error').hide();
            }
        });

        if ($('.campaigns_select').val() && $('.campaigns_select').val().length != 1) {
            $('.copy-records').prop('disabled', false);
            $('.copy_records_error').hide();
        }
        $('.campaigns_select').on('change', function() {
            var num_selected = $('.campaigns_select').val();
            if (num_selected && num_selected.length == 1) {
				$('#custom_fields').closest('.panel').find('.panel-heading').removeAttr('style');
                $('.copy-records').prop('disabled', false);
                $('.copy_records_error').hide();
				filter.filter_display();
				filter.custom_fields_panel();
            } else {
				$('#custom_fields').html("<p class='text-danger'><span class='glyphicon glyphicon-glyphicon glyphicon-exclamation-sign'></span> You can only search the custom fields on an individual campaign. Please select a single campaign from the campaign filter options</p>");	
				
				$('#custom_fields').closest('.panel').find('.panel-heading').css('background','#ccc');
                $('.copy-records').prop('disabled', true);
                $('.copy_records_error').show();
            }
        });
    },
    set_no_number: function($btn) {
        var type = $btn.attr('data-type');
        if ($btn.hasClass('btn-danger')) {
            $btn.removeClass('btn-danger').closest('.form-group').find('input').val('').prop('disabled', false);
            $btn.closest('.form-group').find('.no-number-input').remove();
            if (type == "company") {
                $btn.closest('.form-group').find('input').attr('name', 'company_phone');
            } else {
                $btn.closest('.form-group').find('input').attr('name', 'phone');
            }

        } else {
            $btn.addClass('btn-danger').closest('.form-group').find('input').val('Records without a ' + type + ' telephone number').prop('disabled', true);
            $btn.closest('.form-group').find('input').removeAttr('name');
            if (type == "company") {
                $btn.closest('.form-group').append('<input type="hidden" class="no-number-input" value="on" name="no_company_tel" />');
            } else {
                $btn.closest('.form-group').append('<input type="hidden" class="no-number-input" value="on" name="no_phone_tel" />');
            }
        }
    },
    load_subsectors: function(sectors) {
        $.ajax({
            url: helper.baseUrl + 'search/get_subsectors',
            type: "POST",
            dataType: "JSON",
            data: {
                sectors: sectors
            }
        }).done(function(response) {
            var option_data = "";
            $.each(response, function(i, val) {
                option_data += '<option value="' + val.id + '">' + val.name + '</option>';
            });
            $('#subsector_id').html(option_data);
            $('.subsector-select').selectpicker('refresh');
        });

    },
    check_postcode: function(postcode) {
        var data = {
            postcode: postcode
        };
        $.ajax({
            url: helper.baseUrl + 'search/get_coords',
            type: "POST",
            dataType: "JSON",
            data: data
        }).done(function(response) {
            if (!response.success) {
                $('.filter-form').find('input[name="postcode"]').val('')
                flashalert.danger("The postcode does not exist or the connection with Google Maps failed: " + response.error);
            } else {
                $('.filter-form').find('input[name="lat"]').val(response.coords.lat);
                $('.filter-form').find('input[name="lng"]').val(response.coords.lng);
                filter.count_records();
            }
        });
    },
    check_distance: function() {
        var lat = $('.filter-form').find('input[name="lat"]').val();
        var lng = $('.filter-form').find('input[name="lng"]').val();
        if (lat.length && lng.length) {
            filter.count_records();
        }
    },
    count_records: function() {

        var formData = $('#filter-form').serialize();

        $.ajax({
            url: helper.baseUrl + 'search/count_records',
            type: "POST",
            dataType: "JSON",
            data: formData,
            beforeSend: function() {
                $('.record-count').html("<img src='" + helper.baseUrl + "assets/img/ajax-load-black.gif' />");
            }
        }).done(function(response) {
            filter.filter_display();
            if (response.data < 1) {
                $('button[type="submit"]').prop('disabled', true);
                $('.record-count').text(response.data).css('color', 'red');
                $('.records-found').html(response.data).css('color', 'red');
                $('.actions-filter').prop('disabled', true);
                $('.change-parkedcode').prop('disabled', true);
                $('.change-ownership').prop('disabled', true);
                $('.send-email').prop('disabled', true);
                if ($('.campaigns_select').val() && $('.campaigns_select').val().length == 1) {
                    $('.copy-records').prop('disabled', true);
                }
            } else {
                $('button[type="submit"]').prop('disabled', false);
                $('.record-count').html(response.data).css('color', 'green');
                $('.records-found').html(response.data).css('color', 'green');
                $('.actions-filter').prop('disabled', false);
                $('.change-parkedcode').prop('disabled', false);
                $('.change-ownership').prop('disabled', false);
                $('.send-email').prop('disabled', false);
                if ($('.campaigns_select').val() && $('.campaigns_select').val().length == 1) {
                    $('.copy-records').prop('disabled', false);
                }
                $('.actions-qry').html(response.query);
            }
        });

        filter.reload_actions();

    },
    apply_filter: function() {
        $.ajax({
            url: helper.baseUrl + 'search/apply_filter',
            type: "POST",
            dataType: "JSON",
            data: $('#filter-form').serialize()
        }).done(function(response) {
            window.location.href = helper.baseUrl + 'records/view';
            localStorage.removeItem('DataTables_' + settings.sInstance + '_' + '/records/view');
        });
    },
    actions: function() {
        var pagewidth = $(window).width() / 2;
        var moveto = pagewidth - 250;
        $('<div class="modal-backdrop actions in"></div>').appendTo(document.body).hide().fadeIn();
        $('.actions-container').find('.actions-panel').show();
        $('.actions-content').show();
        $('.actions-container').fadeIn()
        $('.actions-container').animate({
            width: '500px',
            left: moveto,
            top: '10%'
        }, 1000);

        $('.records-found').html($('.record-count').html());

        $('.actions-parkedcode-btn').prop('disabled', true);
        $('.actions-ownership-add-btn').prop('disabled', true);
        $('.actions-ownership-replace-btn').prop('disabled', false);
        $('.actions-send-email-btn').prop('disabled', true);
        $('.actions-copy-btn').prop('disabled', true);
    },
    close_actions: function() {
        $('.modal-backdrop.actions').fadeOut();
        $('.actions-container').fadeOut(500, function() {
            $('.actions-content').show();
            $('.alert').addClass('hidden');
        });
        $('.edit-parkedcode-form').hide();
        $('.edit-ownership-form').hide();
        $('.send-email-form').hide();
        $('.copy-records-form').hide();
    },
    close_edit_actions: function() {
        $('.actions-ownership-replace-btn').prop('disabled', false);
        $('.edit-parkedcode-form').hide();
        $('.edit-ownership-form').hide();
        $('.send-email-form').hide();
        $('.copy-records-form').hide();
        $('.actions-content').fadeIn(1000);
    },
    reload_actions: function() {
        $('.actions-parkedcode-btn').prop('disabled', true);
        $('.actions-ownership-add-btn').prop('disabled', true);
        $('.actions-ownership-replace-btn').prop('disabled', false);
        $('.actions-copy-btn').prop('disabled', true);

        $('.change-parkedcode-result').html("");
        $('.change-ownership-result').html("");
        $('.copy-records-result').html("");
        $('.send-email-result').html("");

        $(".edit-parkedcode-form").trigger("reset");
        $(".edit-ownership-form").trigger("reset");
        $(".send-email-form").trigger("reset");
        $(".copy-records-form").trigger("reset");
        $('.actions_parked_code_select').selectpicker('render');
        $('.actions_ownership_select').selectpicker('render');
        $('.actions_campaign_select').selectpicker('render');
        $('.actions_template_select').selectpicker('render');

    },
    get_urn_list: function() {

        var urn_list;
        var query = "";
        $.ajax({
            url: helper.baseUrl + 'search/get_urn_list',
            type: "POST",
            dataType: "JSON",
            async: false
        }).done(function(response) {
            urn_list = response.data;
        });

        return urn_list;
    },

    save_parked_code: function(urn_list, parked_code_id, all_campaigns, suppression_campaigns, reason, suppress) {

        if (!all_campaigns && !suppression_campaigns && suppress == 1) {
            $('.change-parked-code-campaign-error').html("Please select a campaign before or click on \"Check for all campaigns\"");
            $('.change-parked-code-campaign-error').show();
        } else {
            $('.change-parked-code-campaign-error').hide();
            $.ajax({
                url: helper.baseUrl + 'search/save_parked_code',
                type: "POST",
                dataType: "JSON",
                data: {
                    'urn_list': urn_list,
                    'parked_code_id': parked_code_id,
                    'all_campaigns': all_campaigns,
                    'suppression_campaigns': suppression_campaigns,
                    'reason': reason,
                    'suppress': suppress
                },
                beforeSend: function() {
                    $('.saving').html("<img src='" + helper.baseUrl + "assets/img/ajax-loader-bar.gif' />");
                    $('.actions-parkedcode-btn').prop('disabled', true);
                }
            }).done(function(response) {
                if (response.success) {
                    flashalert.success(response.msg);
                    $('.change-parkedcode-result').html("Success").css('color', 'green');
                    filter.count_records();
                } else {
                    flashalert.danger(response.msg);
                    $('.change-parkedcode-result').html("Error").css('color', 'red');
                }
                $('.saving').html("");
                filter.close_edit_actions();
                setTimeout(function() {
                    $('.records-found').html($('.record-count').html());
                }, 2000);
            });
        }
    },
    add_ownership: function(urn_list, ownership_ar) {
        $.ajax({
            url: helper.baseUrl + 'search/add_ownership',
            type: "POST",
            dataType: "JSON",
            data: {
                'urn_list': urn_list,
                'ownership_ar': ownership_ar
            },
            beforeSend: function() {
                $('.saving').html("<img src='" + helper.baseUrl + "assets/img/ajax-loader-bar.gif' />");
                $('.actions-ownership-add-btn').prop('disabled', true);
                $('.actions-ownership-replace-btn').prop('disabled', true);
            }
        }).done(function(response) {
            if (response.success) {
                flashalert.success(response.msg);
                $('.change-ownership-result').html("Success").css('color', 'green');
            } else {
                flashalert.danger(response.msg);
                $('.change-ownership-result').html("Error").css('color', 'red');
            }
            $('.saving').html("");
            filter.close_edit_actions();
        });
    },
    replace_ownership: function(urn_list, ownership_ar) {
        $.ajax({
            url: helper.baseUrl + 'search/replace_ownership',
            type: "POST",
            dataType: "JSON",
            data: {
                'urn_list': urn_list,
                'ownership_ar': ownership_ar
            },
            beforeSend: function() {
                $('.saving').html("<img src='" + helper.baseUrl + "assets/img/ajax-loader-bar.gif' />");
                $('.actions-ownership-add-btn').prop('disabled', true);
                $('.actions-ownership-replace-btn').prop('disabled', true);
            }
        }).done(function(response) {
            if (response.success) {
                flashalert.success(response.msg);
                $('.change-ownership-result').html("Success").css('color', 'green');
            } else {
                flashalert.danger(response.msg);
                $('.change-ownership-result').html("Error").css('color', 'red');
            }
            $('.saving').html("");
            filter.close_edit_actions();
        });
    },
    send_email: function(urn_list, template_id) {
        $.ajax({
            url: helper.baseUrl + 'search/send_email',
            type: "POST",
            dataType: "JSON",
            data: {
                'urn_list': urn_list,
                'template_id': template_id
            },
            beforeSend: function() {
                $('.saving').html("<img src='" + helper.baseUrl + "assets/img/ajax-loader-bar.gif' />");
                $('.actions-send-email-btn').prop('disabled', true);
            }
        }).done(function(response) {
            if (response.success) {
                flashalert.success(response.msg);
                $('.send-email-result').html("Success").css('color', 'green');
            } else {
                flashalert.danger(response.msg);
                $('.send-email-result').html("Error").css('color', 'red');
            }
            $('.saving').html("");
            filter.close_edit_actions();
        });
    },
    copy_records: function(urn_list, campaign_id) {
        $.ajax({
            url: helper.baseUrl + 'search/copy_records',
            type: "POST",
            dataType: "JSON",
            data: {
                'urn_list': urn_list,
                'campaign_id': campaign_id
            },
            beforeSend: function() {
                $('.saving').html("<img src='" + helper.baseUrl + "assets/img/ajax-loader-bar.gif' />");
                $('.actions-copy-btn').prop('disabled', true);
            }
        }).done(function(response) {
            if (response.success) {
                flashalert.success(response.msg);
                $('.copy-records-result').html("Success").css('color', 'green');
            } else {
                flashalert.danger(response.msg);
                $('.copy-records-result').html("Error").css('color', 'red');
            }
            $('.saving').html("");
            filter.close_edit_actions();
        });
    },
    filter_display: function() {
        var filter_options = "";
        $.ajax({
            url: helper.baseUrl + 'search/filter_display',
            data: $('#filter-form').serializeArray(),
            dataType: "JSON",
            type: "POST",
            beforeSend: function() {
                //$('#filter-panel .panel-body').html('<img src="'+helper.baseUrl+'assets/img/ajax-loader-bar.gif" /> ');
            }
        }).done(function(response) {
			var remove_btn = "";
            $.each(response, function(k, v) {
                filter_options += "<div style='float:left; width:200px; padding:4px 0; border-bottom: 1px dashed #ccc'>";
                if (v.value.length > 0) {
                    var title = v.name;
                    if (v.name == "Distance") {
                        v.value = "Within " + v.value + " mile(s)";
                    } else if (v.name == "Nextcall date" || v.name == "Lastcall date" || v.name == "Creation date" || v.name == "Contact DOB") {
                        v.value[0] = v.value[0]!==""?"From " + v.value[0]:"";
                        v.value[1] = v.value[1]!==""?"To " + v.value[1]:"";
                    } else if (v.name == "Turnover" || v.name == "Employees") {
                        v.value[0] = v.value[1]!==""?"More than " + v.value[0]:"";
                        v.value[1] = v.value[1]!==""?"Less than " + v.value[1]:"";
                    }
					if(v.removable){
					remove_btn = " <span data-field='" + v.field + "' class='remove-filter-selection glyphicon glyphicon-remove pointer red small'></span>";	
					}

                    filter_options += "<strong>" + title + remove_btn+"</strong>";
                    if (typeof v.value === "string") {
                        filter_options += "<ul><li>" + v.value + "</li></ul>";
                    } else {
                        filter_options += "<ul>";
                        $.each(v.value, function(x, newval) {
							if(newval!==""){
                            if ($('#' + v.field).is("select")) {
                                filter_options += "<li>" + $('#' + v.field + ' option[value="' + newval + '"]').text() + "</li>";
                            } else {
                                filter_options += "<li>" + newval + "</li>";
                            }
							}
                        });
                        filter_options += "</ul>";
                    }
                }
                filter_options += "</div>";
            });
            $('#filter-panel .panel-body').html(filter_options).append('<div id="filter-display-count" style="float:left; width:200px; padding:10px 0 5px">Records found: </div>');
            $('.record-count:first').clone().appendTo('#filter-display-count');
        });
    },
	custom_fields_panel:function(){
		$('#custom_fields').empty().hide();
	$.ajax({ 
		url:helper.baseUrl+'search/get_custom_fields',
		dataType:"JSON",
		data:{ campaign:$('#campaign_id').val() },
		type:"POST"
	}).done(function(response){
		var custom_form = "";
		$.each(response,function(k,v){
			
			if(v.options){
				custom_form += '<div class="form-group"><label style="display:block">'+v.field_name+'</label>';
			custom_form += '<select id="'+v.field_name+'" name="'+v.field+'">';
			custom_form += '<option value="">Any</option>';
			 $.each(v.options,function(id,data){
				 custom_form += '<option value="'+data.option+'">'+data.option+'</option>';
			 });
			custom_form += '</select>';
			} else if(v.type=="date"||v.type=="datetime"||v.type=="number"){
				custom_form += '<div class="form-group">'+
                '<label>'+v.field_name+'</label>'+
                '<div class="row">'+
                  '<div class="col-md-6 col-sm-6">'+
                    '<div class="input-group">'+
                      '<input id="'+v.field_name+'-0" name="'+v.field+'[0]" type="text" class="form-control '+v.type+'" placeholder="from">'+
                      '<span class="input-group-btn">'+
                      '<button class="btn btn-default clear-input" type="button">Clear</button>'+
                      '</span> </div>'+
                  '</div>'+
                  '<div class="col-md-6 col-sm-6">'+
                    '<div class="input-group">'+
                      '<input id="'+v.field_name+'-1" name="'+v.field+'[1]" type="text" class="form-control '+v.type+'" placeholder="to">'+
                      '<span class="input-group-btn">'+
                      '<button class="btn btn-default clear-input" type="button">Clear</button>'+
                      '</span> </div>'+
                  '</div>'+
               '</div>'+
              '</div>'
				}
			else {
			custom_form += '<div class="form-group"><label style="display:block">'+v.field_name+'</label>';
			custom_form += '<input class="form-control" type="text" name="'+v.field+'" />';
			}
			custom_form += '</div>';
		});
		$('#custom_fields').prepend(custom_form).show();
		$('#custom_fields select').selectpicker();
		renew_js();
	});
	}
	
}