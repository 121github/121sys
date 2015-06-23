// JavaScript Document
/* ==========================================================================
 RECORD DETAILS PAGE
 ========================================================================== */


//when all the ajax requests have finished we run the stretch function to align the panels
$(document).ajaxStop(function () {
	if(device_type=="default"){
    stretch();
	}
});

var record = {
    init: function (urn, role, campaign) {
		 $(document).on('click', '#update-record', function (e) {
                e.preventDefault();
                if ($('.outcomepicker').val().length > 0) {
                    if ($('.outcomepicker').val() == "4" && $('.history-panel').find('tbody tr').length > 0) {
                        modal.dead_line($(this));
                    } else {
                        record.update_panel.save($(this));
                    }
                } else {
                    flashalert.danger("You must select a call outcome first");
                }
            });
            $(document).on('click', '#reset-record', function (e) {
                e.preventDefault();
                record.update_panel.reset_record($(this));
            });
            $(document).on('click', '#unpark-record', function (e) {
                e.preventDefault();
                record.update_panel.unpark_record($(this));
            });
            $(document).on('click', '#favorite-btn', function (e) {
                record.update_panel.set_favorite($(this));
            });
            $(document).on('click', '#urgent-btn', function (e) {
                record.update_panel.set_urgent($(this));
            });
            $(document).on('click', '.close-xfer', function (e) {
                e.preventDefault();
                record.update_panel.close_cross_transfer();
            });
            $(document).on('click', '.set-xfer', function (e) {
                e.preventDefault();
                var xfer = $('select[name="campaign"]').find('option:selected').text()
                $('#record-update-form').append($('<input name="xfer_campaign" type="hidden"/>').val($('select[name="campaign"]').val()));
                $('div.outcomepicker').find('.filter-option').text('Cross Transer: ' + xfer);
                record.update_panel.close_cross_transfer();
            });
            var old_outcome = $('.outcomepicker option:selected').val();
            var current_outcome = old_outcome;
            $(document).on('change', '.outcomepicker', function (e) {
				record.update_panel.enable_outcome_reasons($(this).val());
                e.preventDefault();
                $val = $(this).val();
                if ($val == 71) {
                    record.update_panel.cross_transfer();
                } else {
                    $('input[name="xfer_campaign"]').remove();
                }
                $delay = $('#outcomes').find("option[value='" + $val + "']").attr('delay');
                //if the selected option has a delay attribute we disable the nextcall and set it as now+the amount of delay. This is for outcomes such as answer machine to give us more control over when agents should try again
                if ($delay > 0) {
                    var today = new Date();
                    var nextcall = new Date().addHours($delay);
                    var hour = nextcall.getHours();
                    if (hour > 16) {
                        var nextcall = moment(today).add(1, 'days').toDate();
                    }

                    $('#nextcall').val(timestamp_to_uk(nextcall, true));
                    $('#nextcall').datetimepicker({
                        format: 'DD/MM/YYYY HH:mm'
                    });
                    $('#nextcall').animate({backgroundColor: "#99FF99"}, 500).delay(300).animate({backgroundColor: "#FFFFFF"}, 500);
                    //$('#nextcall').data("DateTimePicker").setDate(timestamp_to_uk(nextcall,true));
                }
                //If the previous outcome had delay, set the date to the old_nextcall
                else if ($('#outcomes').find("option[value='" + current_outcome + "']").attr('delay')) {
                    nextcall = old_nextcall;
                    $('#nextcall').val(nextcall);
                    $('#nextcall').datetimepicker({
                        format: 'DD/MM/YYYY HH:mm'
                    });
                    $('#nextcall').animate({backgroundColor: "#99FF99"}, 500).delay(300).animate({backgroundColor: "#FFFFFF"}, 500);
                }

                //Set the new outcome
                current_outcome = $('#outcomes option:selected').val();

                var outcome = $('#outcomes option:selected').val();
                var new_nextcall = $('input[name="nextcall"]').val();
                var comments = $('textarea[name="comments"]').val();
                record.update_panel.disabled_btn(old_outcome, outcome, old_nextcall, new_nextcall, old_comments, comments);
            });

            var old_nextcall = $('input[name="nextcall"]').val();
            var datetimepicker = $('.datetime');
            datetimepicker.off("dp.hide");
            datetimepicker.on("dp.hide", function (e) {
                var new_nextcall = $('input[name="nextcall"]').val();
                var outcome = $('#outcomes option:selected').val();
                var comments = $('textarea[name="comments"]').val();
                record.update_panel.disabled_btn(old_outcome, outcome, old_nextcall, new_nextcall, old_comments, comments);
            });

            var old_comments = $('textarea[name="comments"]').val();
            $('textarea[name="comments"]').bind('input propertychange', function () {
                var new_nextcall = $('input[name="nextcall"]').val();
                var outcome = $('#outcomes option:selected').val();
                var comments = $('textarea[name="comments"]').val();
                record.update_panel.disabled_btn(old_outcome, outcome, old_nextcall, new_nextcall, old_comments, comments);
            });

            $(document).on('click', 'td a span.view-workbooks-data', function (e) {
                e.preventDefault();
                workbooks.view_workbooks_data($(this).attr('item-id'));
            });
        /* Initialize all the jquery widgets */
        $("span.close-alert").click(function () {
            $(this).closest('.alert').addClass('hidden');
            $(this).closest('.alert-text').text('');
        });
        /*initialize the call when the agent calls */
        $(document).on('click', 'dd a.startcall', function (e) {
            e.preventDefault();
            window.location.href = $(this).attr('item-url');
        });
        /*initialize the timer when the agent calls */
        $(document).on('click', 'dd a.starttimer', function (e) {
            e.preventDefault();
            record.start_call();
        });
        modals.contacts.init();
        modals.companies.init();
        /* Initialize all the panel functions for the record details page */
        this.urn = urn;
        this.role = role;
        this.campaign = campaign;
        this.limit = 6;
        var data = [];
        window.history.pushState(data, "Record Details-" + record.urn, helper.baseUrl + 'records/detail/' + record.urn);

        // Map iconpicker
        $('#map-icon').on('change', function (e) {
            record.setIcon(e.icon);
        });
    },
    start_call: function () {
        $('#defaultCountdown').countdown('destroy');
        $('#timeropened').css({"color": "#3C0"});
        $('#timerclosed').css({"color": "#3C0"});
        $('#timeropened').fadeIn('slow');
        $('#timerclosed').fadeOut('slow');
        $('#defaultCountdown').countdown({
            since: 0, compact: true,
            format: 'MS', description: ''
        });
        var counter = 0;
        setInterval(function () {
            ++counter;
            if ($('#defaultCountdown').text() >= '00:20') {
                $('#timeropened').css({"color": "#F00"});
                $('#timerclosed').css({"color": "#F00"});
            }
        }, 1000);

        $('#closetimer').click(function () {
            $('#timeropened').fadeOut('slow');
            $('#timerclosed').fadeIn('slow');
        });
        $('#opentimer').click(function () {
            $('#timeropened').fadeIn('slow');
            $('#timerclosed').fadeOut('slow');
        });
        $('#stoptimer').click(function () {
            $('#timeropened').hide();
            $('#timerclosed').hide();
            $('#defaultCountdown').countdown('destroy');
        });
    },
    setIcon: function (icon) {
        $.ajax({
            url: helper.baseUrl + 'records/set_icon',
            type: "POST",
            dataType: "JSON",
            data: {
                'map_icon': ((icon.length>0 && icon != 'empty')?icon:null),
                'urn': record.urn
            }
        }).done(function (response) {
            if (response.success) {
                flashalert.success(response.msg);
            } else {
                flashalert.danger(response.msg);
            }
        });
    },
    sticky_note: {
        init: function () {
            /*initialize the save notes button*/
            $(document).on('click', '#save-sticky', function (e) {
                e.preventDefault();
                record.sticky_note.save($(this).prev('span'));
            });
        },
        save: function ($alert) {
            $.ajax({
                url: helper.baseUrl + 'records/save_notes',
                type: "POST",
                dataType: "JSON",
                data: {
                    'notes': $('#sticky-notes').val(),
                    'urn': record.urn
                }
            }).done(function (response) {
                if (response.success) {
                    flashalert.success('Sticky note was updated');
                } else {
                    flashalert.danger('Notes could not be saved. Please contact support@121customerinsight.co.uk');
                }
            });

        }

    },
    //history panel functions
    history_panel: {
        init: function () {
            record.history_panel.load_panel();

            $(document).on('click', '#show-all-history-btn', function (e) {
                e.preventDefault();
                record.history_panel.load_all_history_panel();
            });
            $(document).on('click', '#close-history-all', function (e) {
                e.preventDefault();
                record.history_panel.close_all_history($(this));
            });
            $(document).on('click', '#edit-history-btn', function (e) {
                e.preventDefault();
                record.history_panel.edit_history($(this).attr('item-id'), $(this).attr('item-modal'));
            });
            $(document).on("click", "#close-history-btn", function (e) {
                e.preventDefault();
                var modal = $(this).attr('item-modal');
                var panel = (modal == "1" ? '.history-all-panel' : '.history-panel');
                var content = (modal == "1" ? '.history-all-content' : '.panel-content');
                $(panel).find('.glyphicon-remove').removeClass('glyphicon-remove');
                $(panel).find('form').fadeOut(function () {
                    $(panel).find(content).fadeIn()
                });
            });
            $(document).on("click", "#save-history-btn", function (e) {
                e.preventDefault();
                record.history_panel.update_history($(this).attr('data-modal'));
            });
			$(document).on("click", "#edit-history-back", function (e) {
                e.preventDefault();
				$('#edit-history-container').fadeOut(function(){ $('#all-history-container').fadeIn(); modal_body.css('overflow','auto') });
            });
			
            $(document).on('click', '#del-history-btn', function (e) {
                e.preventDefault();
                modal.delete_history($(this).attr('item-id'), $(this).attr('item-modal'));
            });
        },
        load_panel: function () {
            $.ajax({
                url: helper.baseUrl + 'ajax/get_history',
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: record.urn,
                    limit: record.limit
                }
            }).done(function (response) {
                if (response.success) {
                    $('.history-panel').find('.panel-content').empty();
                    var $body = "";
                    var $edit_btn = "";
                    var $delete_btn = "";
                    if (response.data.length) {
                        //if any outcomes in the history have the "keep" flag the user will keep ownership. This stops answermachines from being taken out of the users pot when they have had a call back Dm previously
                        if (response.keep) {
                            $('#record-update-form').append('<input type="hidden" name="keep" value="1" />');
                        }
                        //Use the k var only to know if there are more than x records
                        var k = 0;
                        $.each(response.data, function (i, val) {
                            if (helper.permissions['edit history'] > 0) {
                                $edit_btn = '<span class="glyphicon glyphicon-pencil pointer pull-right" id="edit-history-btn" item-modal="0" item-id="' + val.history_id + '"></span>';
                            }
                            if (helper.permissions['delete history'] > 0) {
                                $delete_btn = '<span class="glyphicon glyphicon-trash pointer pull-right marl" data-target="#modal" id="del-history-btn" item-modal="0" item-id="' + val.history_id + '" title="Delete history"></span>';
                            }
                            if (k <= record.limit - 1) {
                                $body += '<tr><td>' + val.contact + '</td><td>' + val.outcome + '</td><td>' + val.client_name + '</td><td>' + val.comments + '</td><td>' + $edit_btn + '</td><td>' + $delete_btn + '</td></tr>';
                            }
                            k++;
                        });
                        if (k > record.limit - 1) {
                            $body += '<tr><td colspan="6"><a href="#"><span class="btn btn-info btn-sm pull-right"  id="show-all-history-btn">Show All</span></a></td></tr>';
                        }
                        $('.history-panel').find('.panel-content').append('<div class="table-responsive"><table class="table table-striped table-condensed table-responsive"><thead><tr><th>Date</th><th>Outcome</th><th>User</th><th>Notes</th><th colspan="2"></th></tr></thead><tbody>' + $body + '</tbody></table></div>');

                    } else {
                        $('.history-panel').find('.panel-content').append('<p>This record has no history information yet</p>');
                    }
                }
                ;
            });

        },
        load_all_history_panel: function () {
			modal_body.css('overflow','auto');
            //Get attachment data
            $.ajax({
                url: helper.baseUrl + "ajax/get_history",
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: record.urn
                }
            }).done(function (response) {
               var edit_btn = "",delete_btn = "",mheader = "Showing all history",mfooter='<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>';

                if (response.data.length > 0) {
					var mbody = '<div id="edit-history-container" style="display:none"></div><div id="all-history-container">';
					 mbody += '<table class="table table-striped"><thead><tr><th>Name</th><th>Date</th><th>Outcome</th><th>User</th><th>Notes</th><th colspan="2"></th></tr></thead><tbody>';
                    $.each(response.data, function (i, val) {
                        if (helper.permissions['edit history'] > 0) {
                            edit_btn = '<span class="glyphicon glyphicon-pencil pointer pull-right" id="edit-history-btn" item-modal="1" item-id="' + val.history_id + '"></span>';
                        }
                        if (helper.permissions['delete history'] > 0) {
                            delete_btn = '<span class="glyphicon glyphicon-trash pointer pull-right marl" data-target="#modal" id="del-history-btn" item-modal="1" item-id="' + val.history_id + '" title="Delete history"></span>';
                        }

                        mbody += '<tr><td>' + val.contact + '</td><td>' + val.outcome + '</td><td>' + val.client_name + '</td><td>' + val.comments + '</td><td>' + edit_btn + '</td><td>' + delete_btn + '</td></tr>';
                    });
                   
                    mbody += '</tbody></table></div>';
					modals.load_modal(mheader,mbody,mfooter);
                } else {
                    flashalert.danger('This record has no history information yet');
                }
            });
        },
        edit_history: function (id, modal) {
                $.ajax({
                    url: helper.baseUrl + "ajax/get_history_by_id",
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        id: id
                    }
                }).done(function (response) {
                    record.history_panel.load_history_form(response.data, response.outcomes, response.progress_list, id, modal);
                });
        },
        load_history_form: function (data, outcomes, progress_list, id, modal) {
            var form = '<form>';
            form += "<input type='hidden' name='history_id' value='" + id + "'/>";

            var select_outcome = "<option value=''>No action required</option>", select_progress = "<option value=''>No action required</option>";

            var disabled = (record.role == 1 ? "" : "disabled");

            $.each(outcomes, function (key, outcome) {
                var selected = "";
                if (data.outcome == outcome['name']) {
                    selected = "selected";
                }
                select_outcome += "<option " + selected + " value='" + outcome['id'] + "'>" + outcome['name'] + "</option>";
            });

            $.each(progress_list, function (key, progress) {
                var selected = "";
                if (data.progress == progress['name']) {
                    selected = "selected";
                }
                select_progress += "<option " + selected + " value='" + progress['id'] + "'>" + progress['name'] + "</option>";
            });
            date_input = "<div class='form-group input-group-sm'><p>" + data.contact + "</p></div>";
            user_input = "<div class='form-group input-group-sm'><p>" + data.name + "</p></div>";
            outcome_input = (data.outcome_id) ? "<div class='form-group input-group-sm'><p>Outcome</p><select name='outcome_id' id='selectpicker_outcome' class='selectpicker_outcome'  " + disabled + ">" + select_outcome + "</select></div>" : "";
            progress_input = (data.progress_id) ? "<div class='form-group input-group-sm'><p>Progress</p><select name='progress_id' id='selectpicker_progress' class='selectpicker_progress'  " + disabled + ">" + select_progress + "</select></div>" : "";
            comments_input = "<div class='form-group input-group-sm'><p>Comments</p><textarea class='form-control ' placeholder='Enter the comments here' rows='3' name='comments'>" + data.comments + "</textarea></div>";

            form += date_input +
                user_input +
                outcome_input +
                progress_input +
                comments_input;

			form += '</form>';
			$form = $(form);
			$form.find('select').selectpicker();
			
			if(modal=="0"){
				var mheader="Edit History",mbody="<div id='edit-history-container'></div><div id='all-history-container'></div>",mfooter='<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button> <button class="btn btn-primary pull-right marl" id="save-history-btn">Save</button>';
			modals.load_modal(mheader,mbody,mfooter);	
			} else {
			 var mfooter = "<button class='btn btn-primary pull-right marl' id='save-history-btn'>Save</button> <button class='btn btn-default pull-left' id='edit-history-back'>Back</button>";
			modals.update_footer(mfooter);	
			}
			$('#all-history-container').fadeOut(function(){ 
				$('#edit-history-container').html($form).fadeIn();
				modal_body.css('overflow','visible');
			});
        },
        update_history: function (modal) {
            $.ajax({
                url: helper.baseUrl + "ajax/update_history",
                type: "POST",
                dataType: "JSON",
                data: $('#modal').find('form').serialize()
            }).done(function (response) {
				$('#modal').modal('hide');
                record.history_panel.load_panel();
                flashalert.success(response.msg);

            });
        },
        remove_history: function (history_id, modal) {
            $.ajax({
                url: helper.baseUrl + 'ajax/delete_history',
                type: "POST",
                dataType: "JSON",
                data: {history_id: history_id}
            }).done(function (response) {
                if (response.success) {
                    record.history_panel.load_panel();
                    if (modal == 1) {
                        record.history_panel.load_all_history_panel();
                    }
                    flashalert.success("History was deleted from the history");
                }
                ;
            });
        }
    },
    //update panel functions
    update_panel: {
        init: function () {
           
        },
        disabled_btn: function (old_outcome, outcome, old_nextcall, nextcall, old_comments, comments) {
            if (((outcome.length != 0) && (outcome != old_outcome)) || ((nextcall.length != 0) && (nextcall != old_nextcall)) || ((comments.length != 0) && (comments != old_comments))) {
                $('#update-record').prop('disabled', false);
            }
            else {
                $('#update-record').prop('disabled', true);
            }
        },
		enable_outcome_reasons:function(outcome){
			//unset the reason if the call outcome is changed
			$('#outcome-reasons').val('');
			if($('#outcome-reasons option[outcome-id="'+outcome+'"]').length>0){
				//show if any reasons are linked to the selected outcome and hide any other
			$('#outcome-reasons option').each(function(){
				if($(this).attr('outcome-id')==outcome&&$(this).attr('value')!==""||$(this).attr('value')=="na"){
					$(this).addClass('option-hidden');
				} else {
					$(this).removeClass('option-hidden');
				}
			});
			//enable the reason dropdown if required
			$('#outcome-reasons').prop('disabled',false);
			} else {
			//if there are no reasons added we just show "na" and leave it disabled
			$('#outcome-reasons[value="na"]').removeClass('option-hidden');
			$('#outcome-reasons').val('na');
			$('#outcome-reasons').prop('disabled',true);	
			}
			//finally refresh the reasons dropdown ui
			$('#outcome-reasons').selectpicker('refresh');
			$('.outcomereasonpicker').find('.option-hidden').closest('li').hide();
		},
        cross_transfer: function () {
            var pagewidth = $(window).width() / 2;
            var moveto = pagewidth - 250;
            $('<div class="modal-backdrop in"></div>').appendTo(document.body).hide().fadeIn();
            $('.xfer-container').find('.edit-panel').show()
            $('.xfer-container').fadeIn()
            $('.xfer-container').animate({
                width: '500px',
                left: moveto,
                top: '10%'
            }, 1000);
        },
        close_cross_transfer: function () {
            $('.modal-backdrop').fadeOut();
            $('.xfer-container').fadeOut(500, function () {
            });
        },
        save: function ($btn) {
            $.ajax({
                url: helper.baseUrl + 'records/update',
                type: "POST",
                dataType: "JSON",
                data: $btn.closest('form').serialize(),
                beforeSend: function () {
                    //$btn.hide()
                    //$btn.closest('div').append("<img class='update-loader pull-right' src='" + helper.baseUrl + "assets/img/ajax-load-black.gif' />");
                }
            }).done(function (response) {
                if (response.success) {
                    $('#last-updated').text('Last Updated: Just Now');
                    record.history_panel.load_panel();
                    record.ownership_panel.load_panel();
                    check_session();
                    $(document).off('click', '.nav-btn');
                    flashalert.success(response.msg);
                    if (response.email_trigger) {
                        $.ajax({
                            url: helper.baseUrl + 'email/trigger_email',
                            type: "POST",
                            data: {urn: record.urn}
                        });
                    }
                    if (response.function_triggers) {
                        $.each(response.function_triggers, function (i, path) {
                            $.ajax({
                                url: helper.baseUrl + path + '/' + record.urn,
                                type: "POST",
                                dataType: "JSON",
                                data: {urn: record.urn}
                            }).done(function (function_trigger_response) {
                                if (function_trigger_response.function_name == "workbooks") {
                                    record.additional_info.load_panel();
                                }
                            });
                        });
                    }
                    record.update_panel.init();
                    $('textarea[name="comments"]').val('');
                    $('#update-record').prop('disabled', true);
                } else {
                    flashalert.warning(response.msg);
                }
                $btn.show();
                $('#update-loader').remove();
            });
        },
        set_favorite: function ($btn) {
            $.ajax({
                url: helper.baseUrl + 'records/set_favorites',
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: record.urn,
                    action: $btn.attr('action')
                }
            }).done(function (response) {
                if (response.added) {
                    $btn.html('<span class="glyphicon glyphicon-star"></span> Remove from favourites').attr("action", "remove").children('span').css('color', 'yellow');
                } else {
                    $btn.html('<span class="glyphicon glyphicon-star-empty"></span> Add to favourites').attr("action", "add").children('span').css('color', 'black');
                }
                flashalert.success(response.msg);
            });
        },
        set_urgent: function ($btn) {
            $.ajax({
                url: helper.baseUrl + 'records/set_urgent',
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: record.urn,
                    action: $btn.attr('action')
                }
            }).done(function (response) {
                if (response.added) {
                    $btn.html('<span class="glyphicon glyphicon-flag red"></span> Unflag as urgent').attr("action", "remove");
                    //$('#progress').selectpicker('val','1').selectpicker('render');
                } else {
                    $btn.html('<span class="glyphicon glyphicon-flag"></span> Flag as urgent').attr("action", "add");
                }
                flashalert.success(response.msg);
            });
        },
        unpark_record: function ($btn) {
            $.ajax({
                url: helper.baseUrl + 'records/unpark_record',
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: record.urn
                }
            }).done(function (response) {
                if (response.success) {
                    flashalert.success(response.msg);
                    location.reload();
                } else {
                    flashalert.danger(response.msg);
                }
            });
        },
        reset_record: function ($btn) {
            $.ajax({
                url: helper.baseUrl + 'records/reset_record',
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: record.urn
                }
            }).done(function (response) {
                if (response.success) {
                    flashalert.success(response.msg);
                    location.reload();
                } else {
                    flashalert.danger(response.msg);
                }
            });
        }
    },
    //contact_panel_functions
    contact_panel: {
        init: function () {
            this.config = {
                panel: '.contact-panel'
            };
            /* initialize the delete contact buttons */
            $(document).on('click', 'span.del-contact-btn', function (e) {
                e.preventDefault();
                modal.delete_contact($(this).attr('item-id'));
            });
            /*check tps */
            $(document).on('click', 'span.tps-btn', function (e) {
                e.preventDefault();
                record.contact_panel.check_tps($(this).attr('item-number'), $(this).attr('item-contact-id'), $(this).attr('item-number-id'));
            });
        },
        load_panel: function (urn, id) {
            var $panel = $(record.contact_panel.config.panel);
            $.ajax({
                url: helper.baseUrl + 'ajax/get_contacts',
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: urn
                }
            }).done(function (response) {
                $panel.find('.contacts-list').empty();
                $.each(response.data, function (key, val) {
                    var show = "";
                    var collapse = "collapsed"
                    if (key == id) {
                        show = "in";
                        collapse = ""
                    }
                    var $contact_detail_list_items = "",
                        $contact_detail_telephone_items = "";
                    $address = "";
                    $postcode = "";
                    $.each(val.visible, function (dt, dd) {
                        if (dd && dd != '' && dd.length > 0 && dt != 'Address') {
                            $contact_detail_list_items += "<dt>" + dt + "</dt><dd>" + dd + "</dd>";
                        } else if (dd && dd != '' && dt == 'Address') {
                            $.each(dd, function (key, val) {
                                if (val) {
                                    $address += val + "</br>";
                                    $postcode = dd.postcode;
                                }
                            });
                            $contact_detail_list_items += "<dt>" + dt + "</dt><dd><a class='pull-right pointer' target='_blank' href='https://maps.google.com/maps?q=" + $postcode + ",+UK'><span class='glyphicon glyphicon-map-marker'></span> Map</a>" + $address + "</dd>";
                        }

                    });
                    $.each(val.telephone, function (dt, tel) {
                        if (tel.tel_name) {
                            var tps = "";
                            if (tel.tel_tps == null) {
                                tps = "<span class='glyphicon glyphicon-question-sign black tps-btn tt pointer' item-contact-id='" + id + "' item-number-id='" + dt + "' item-number='" + tel.tel_num + "' data-toggle='tooltip' data-placement='right' title='TPS Status is unknown. Click to check it'></span>";
                            }
                            else if (tel.tel_tps == 1) {
                                tps = "<span class='glyphicon glyphicon-exclamation-sign red tt' data-toggle='tooltip' data-placement='right' title='This number IS TPS registered'></span>";
                            }
                            else {
                                tps = "<span class='glyphicon glyphicon-ok-sign green tt' data-toggle='tooltip' data-placement='right' title='This number is NOT TPS registerd'></span>";
                            }
                            $contact_detail_telephone_items += "<dt>" + tel.tel_name + "</dt><dd><a href='#' class='startcall' item-url='callto:" + tel.tel_num + "'>" + tel.tel_num + "</a> " + tps + "</dd>";
                        }
                    });
                    $panel.find('.contacts-list').append($('<li/>').addClass('list-group-item').attr('item-id', key)
                            .append($('<a/>').attr('href', '#collapse-' + key).attr('data-parent', '#accordian').attr('data-toggle', 'collapse').text(val.name.fullname).addClass(collapse))
                            .append($('<span/>').addClass('glyphicon glyphicon-trash pull-right pointer marl').attr('data-id', key).attr('data-modal', 'delete-contact'))
                            .append($('<span/>').addClass('glyphicon glyphicon-pencil pointer pull-right').attr('data-id', key).attr('data-modal', 'edit-contact'))
                            .append($('<div/>').attr('id', 'collapse-' + key).addClass('panel-collapse collapse ' + show)
                                .append($('<dl/>').addClass('dl-horizontal contact-detail-list').append($contact_detail_list_items).append($contact_detail_telephone_items))
                        )
                    );
                });

            });
        },
        check_tps: function (telephone_number, contact_id, telephone_id) {
            $.ajax({
                url: helper.baseUrl + 'cron/check_tps',
                type: "POST",
                dataType: "JSON",
                data: {
                    telephone_number: telephone_number,
                    type: "tps",
                    telephone_id: telephone_id,
                    contact_id: contact_id
                }
            }).done(function (response) {
                flashalert.warning(response.msg);
                record.contact_panel.load_panel(record.urn, contact_id)
            });
        },
        remove: function (id) {
            $.ajax({
                url: helper.baseUrl + 'ajax/delete_contact',
                type: "POST",
                dataType: "JSON",
                data: {
                    contact: id
                }
            }).done(function (response) {
                if (response.success) {
                    $('.contacts-list').find('li[item-id="' + id + '"]').remove();
                    flashalert.success("Contact was deleted");
                }
                ;
            });
        },
    },
    //contact_panel_functions
    company_panel: {
        init: function () {
            this.config = {
                panel: '.company-panel'
            };

            $(document).on('click', '[data-modal="search-company"]', function (e) {
                e.preventDefault();
                var id = $(this).attr('data-id');
                var urn = $(this).attr('data-urn');
                record.company_panel.search_form(id, urn);
            });
            $(document).on('click', '#search-company-action', function (e) {
                e.preventDefault();
                record.company_panel.search_company();
            });
            $(document).on('click', 'li.search-next-company-action', function (e) {
                e.preventDefault();
                record.company_panel.search_company($(this).attr('item-start-index'));
            });
            $(document).on('click', '#search-table tr', function (e) {
                e.preventDefault();
                record.company_panel.get_company($(this).attr('item-number'));
            });
            $(document).on('click', '#back-company-btn', function (e) {
                e.preventDefault();
                record.company_panel.close_get_company();
            });
            $(document).on('click', '#update-company-action', function (e) {
                e.preventDefault();
                record.company_panel.update_company();
            });

            /*check ctps */
            $(document).on('click', 'span.ctps-btn', function (e) {
                e.preventDefault();
                record.company_panel.check_ctps($(this).attr('item-number'), $(this).attr('item-company-id'), $(this).attr('item-number-id'));
            });
        },
        load_panel: function (urn, id) {
            var $panel = $(record.company_panel.config.panel);
            $.ajax({
                url: helper.baseUrl + 'ajax/get_companies',
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: urn
                }
            }).done(function (response) {
                $panel.find('.company-list').empty();
                $.each(response.data, function (key, val) {
                    var show = "";
                    var collapse = "collapsed";
                    if (key == id||typeof id == "undefined") {
                        show = "in";
                        collapse = "";
                    }
                    var $company_detail_list_items = "",
                        $company_detail_telephone_items = "";
                    $address = "";
                    $postcode = "";
                    $.each(val.visible, function (dt, dd) {
                        if (dd && dd != '' && dt != 'Address' && dt != 'Company') {
                            if (dt == 'Company #') {
                                dd = "<a href='http://companycheck.co.uk/company/" + dd + "' target='blank'>" + dd + "</a>";
                            }
                            $company_detail_telephone_items += "<dt>" + dt + "</dt><dd>" + dd + "</dd>";
                        } else if (dd && dd != '' && dt == 'Address') {
                            $.each(dd, function (key, val) {
                                if (val && val != '') {
                                    $address += val + "</br>";
                                    $postcode = dd.postcode;
                                }
                            });

                            $company_detail_list_items += "<dt>" + dt + "</dt><dd><a class='pull-right pointer' target='_blank' href='https://maps.google.com/maps?q=" + $postcode + ",+UK'><span class='glyphicon glyphicon-map-marker'></span> Map</a>" + $address + "</dd>";
                        }
                    });
                    $.each(val.telephone, function (dt, tel) {
                        var ctps = "";
                        if (tel.tel_num) {
                            if (tel.tel_tps == null) {
                                ctps = "<span class='glyphicon glyphicon-question-sign black ctps-btn tt pointer' item-company-id='" + id + "' item-number-id='" + dt + "' item-number='" + tel.tel_num + "' data-toggle='tooltip' data-placement='right' title='CTPS Status is unknown. Click to check it'></span>";
                            }
                            else if (tel.tel_tps == 1) {
                                ctps = "<span class='glyphicon glyphicon-exclamation-sign red tt' data-toggle='tooltip' data-placement='right' title='This number IS CTPS registered'></span>";
                            }
                            else {
                                ctps = "<span class='glyphicon glyphicon-ok-sign green tt' data-toggle='tooltip' data-placement='right' title='This number is NOT CTPS registerd'></span>";
                            }
                        }
                        $company_detail_telephone_items += "<dt>" + tel.tel_name + "</dt><dd><a href='#' class='startcall' item-url='callto:" + tel.tel_num + "'>" + tel.tel_num + "</a> " + ctps + "</dd>";
                    });
                    $panel.find('.company-list').append($('<li/>').addClass('list-group-item').attr('item-id', key)
                            .append($('<a/>').attr('href', '#com-collapse-' + key).attr('data-parent', '#accordian').attr('data-toggle', 'collapse').text(val.visible['Company']).addClass(collapse))
                            //.append($('<span/>').addClass('glyphicon glyphicon-trash pull-right del-company-btn').attr('item-id', key).attr('data-target', '#model'))
                            .append($('<span/>').addClass('glyphicon glyphicon-search pointer marl pull-right').attr('data-id', key).attr('data-modal', 'search-company'))
                            .append($('<span/>').addClass('glyphicon glyphicon-pencil pointer pull-right').attr('data-id', key).attr('data-modal', 'edit-company'))
                            .append($('<div/>').attr('id', 'collapse-' + key).addClass('panel-collapse collapse ' + show)
                                .append($('<dl/>').addClass('dl-horizontal company-detail-list').append($company_detail_list_items).append($company_detail_telephone_items))
                        )
                    );
                });

            });
        },
        check_ctps: function (telephone_number, company_id, telephone_id) {
            $.ajax({
                url: helper.baseUrl + 'cron/check_tps',
                type: "POST",
                dataType: "JSON",
                data: {
                    telephone_number: telephone_number,
                    type: "ctps",
                    telephone_id: telephone_id,
                    company_id: company_id
                }
            }).done(function (response) {
                flashalert.warning(response.msg);
                record.company_panel.load_panel(record.urn, company_id)
            });
        },
        remove: function (id) {
            $.ajax({
                url: helper.baseUrl + 'ajax/delete_company',
                type: "POST",
                dataType: "JSON",
                data: {
                    company: id
                }
            }).done(function (response) {
                if (response.success) {
                    $('.company-list').find('li[item-id="' + id + '"]').remove();
                    flashalert.success("company was deleted");
                }
                ;
            });
        },    
        search_form: function (id, urn) {
            $.ajax({
                url: helper.baseUrl + 'modals/load_company_search',
                dataType: "HTML"
            }).done(function (response) {
                var mheader = '<a href="https://www.gov.uk/government/organisations/companies-house" target="_blank" ><img src="' + helper.baseUrl + 'assets/img/companieshouse.png"></a>';
                var $panel = $(response);
                var mfooter = '<button class="btn btn-primary pull-right" id="search-company-action">Search</button> <button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>';

                $('.result-pagination').empty();
                $('.searchresult-tab').find('.num-results').html("");
                $('.nav-tabs a[href="#cosearch"]').tab('show');
                $('.tab[href="#search"]').tab('show');
                $panel.find('.tab-alert').hide();
                $panel.find('tbody').empty();
                $panel.find('#cosearchresult .table-container table').hide();
                $panel.find('.searchresult-tab').show();
                $panel.find('input[name="company_id"]').val(id);
                $panel.find('input[name="urn"]').val(urn);
                record.company_panel.load_search_tabs(id);
                modals.load_modal(mheader, $panel, mfooter);
                modal_body.css('padding', '0px');
            });

        },
        search_company: function (start_index) {
            var $panel = $('#modal');
            var $form = $panel.find('.search-company-form');
            var name = $form.find('input[name="name"]').val();
            var conumber = $form.find('input[name="conumber"]').val();
            var search = name + (name.length == 0 ? "" : " ") + conumber;
            if (conumber.length > 0) {
                var search = conumber;
            }
            var num_results = 5;
            start_index = (start_index ? start_index : 0);
            $('.result-pagination').empty();

            $.ajax({
                url: helper.baseUrl + "companyhouse/search_companies",
                type: "POST",
                dataType: "JSON",
                data: {'search': search, 'num_per_page': num_results, 'start_index': start_index}
            }).done(function (response) {
                $('.searchresult-tab').find('.num-results').html((response.total_results < 200 ? response.total_results : '> 200'));
                $('.nav-tabs a[href="#cosearchresult"]').tab('show');
                var tbody = $('#modal').find('#cosearchresult .table-container table tbody');
                tbody.empty();
                if (response.total_results > 0) {
                    response.total_results = (response.total_results < 200 ? response.total_results : 199);
                    $('#modal').find('#cosearchresult .table-container table').show();
                    $.each(response.items, function (key, val) {
                        tbody.append("<tr class='pointer' item-number='" + val.description_values.company_number.replace('<strong>', '').replace('</strong>', '') + "'>" +
                            "<td>" + val.title + "</td>" +
                            "<td>" + val.description_values.company_number + "</td>" +
                            "<td>" + val.description_values.company_status + "</td>" +
                            "<td>" + val.date_of_creation + "</td>" +
                            "</tr>");
                    });
                    if (response.total_results > num_results) {
                        var num_pages = Math.ceil(response.total_results / num_results);
                        var prev = (response.page_number == 1 ? 'disabled' : 'search-next-company-action');
                        var next = (response.page_number == num_pages ? 'disabled' : 'search-next-company-action');

                        var pagination = '';
                        pagination += '<ul class="pagination">';
                        pagination += '<li class="' + prev + '" item-start-index="0"><a href="#">' + "<<" + '</a></li>';
                        pagination += '<li class="' + prev + '" item-start-index="' + Math.ceil((response.page_number - 2) * num_results) + '"><a href="#">' + "<" + '</a></li>';

                        for (var i = 1; i <= num_pages; i++) {
                            var active = ((response.page_number) == i ? 'active' : '');
                            var num_pages_view = ((1 == response.page_number || response.page_number == num_pages) ? 6 : 4);
                            if (i > (response.page_number - num_pages_view) && i < (response.page_number + num_pages_view)) {
                                pagination += '<li class="search-next-company-action ' + active + '" item-start-index="' + Math.ceil((i - 1) * num_results) + '"><a href="#">' + i + '</a></li>';
                            }
                        }
                        pagination += '<li class="' + next + '" item-start-index="' + Math.ceil((response.page_number) * num_results) + '"><a href="#">' + ">" + '</a></li>';
                        pagination += '<li class="' + next + '" item-start-index="' + Math.ceil((num_pages - 1) * num_results) + '"><a href="#">' + ">>" + '</a></li>';
                        pagination += '</ul>';
                        $('.result-pagination').append(pagination);
                    }
                }
                else {
                    $('#modal').find('#cosearchresult .table-container table').hide();
                }
            });
        },
        load_search_tabs: function (company) {
            var $panel = $('#modal');
            $.ajax({
                url: helper.baseUrl + "ajax/get_company",
                type: "POST",
                dataType: "JSON",
                data: {id: company}
            }).done(function (response) {
                if (response.success) {
                    $.each(response.data.general, function (key, val) {
                        $panel.find('input[name="' + key + '"]').val(val);
                    });
                    $panel.find('#cosearchresult .table-container table').hide();
                    $panel.find('#cosearchresult .none-found').show();
                }
                $('.tt').tooltip();
            });
        },
        get_company: function (company_no) {
            var $panel = $('#modal');
            var form = $('.update-company-form');

            $panel.find('.search-container').fadeOut(1000, function () {
                $panel.find('.get-company-container').fadeIn(1000);
            });
            $.ajax({
                url: helper.baseUrl + "companyhouse/get_company",
                type: "POST",
                dataType: "JSON",
                data: {'company_no': company_no}
            }).done(function (response) {
                $.ajax({
                    url: helper.baseUrl + 'companyhouse/sic_to_subsectors',
                    dataType: "JSON",
                    type: "POST",
                    data: {sic_codes: response.sic_codes}
                }).done(function (sics) {
                    var sic_options = "";
                    $.each(sics, function (i, row) {
                        sic_options += '<optgroup label="' + i + '">';
                        $.each(row, function (i, v) {
                            sic_options += '<option selected value="' + v.subsector_id + '">' + v.subsector_name + '</option>';
                        });

                    });
                    $('#sic_codes').append(sic_options);
                    $('#sic_codes').selectpicker();
                });
                $('.sic_codes').change(function () {
                    $('.sic_codes').selectpicker('selectAll');
                });
                var mfooter = '<button class="btn btn-default pull-left" id="back-company-btn">Back</button> <button class="btn btn-primary pull-right" id="update-company-action">Update</button>';
                modals.update_footer(mfooter);
                form.find('input[name="company_id"]').val($('.search-company-form').find('input[name="company_id"]').val());
                form.find('input[name="company_name"]').val(response.company_name);
                form.find('input[name="company_number"]').val(response.company_number);
                form.find('input[name="date_of_creation"]').val(response.date_of_creation);
                form.find('input[name="company_status"]').val(response.company_status);

                if (response.registered_office_address) {
                    form.find('input[name="postal_code"]').val(response.registered_office_address.postal_code);
                    form.find('input[name="address_line_1"]').val(response.registered_office_address.address_line_1);
                    form.find('input[name="address_line_2"]').val(response.registered_office_address.address_line_2);
                    form.find('input[name="locality"]').val(response.registered_office_address.locality);
                }
                $('.company-officers').empty();
                var i = 1;
                var officers_table = '<table class="small table-condensed table">' +
                    '<thead>' +
                    '<th></th>' +
                    '<th>Name</th>' +
                    '<th>Position</th>' +
                    '<th>DOB</th>' +
                    '</thead>' +
                    '<tbody>';
                var officer_val = '';
                $.each(response.officer_summary.officers, function (key, val) {
                    officer_val = val.name + '_' + val.officer_role + '_' + (val.date_of_birth ? val.date_of_birth : '');
                    var checkbox = ((val.name.length > 0) && (val.officer_role.length > 0) && (val.date_of_birth) ? '<input type="checkbox" name="officer[' + i + ']" value="' + officer_val + '">' : '');
                    officers_table += '<tr class="' + (checkbox ? 'success' : 'danger') + '">' +
                        '<td>' + checkbox + '</td>' +
                        '<td>' + val.name + '</td>' +
                        '<td>' + val.officer_role + '</td>' +
                        '<td>' + (val.date_of_birth ? val.date_of_birth : '') + '</td>' +
                        '</tr>';
                    i++;
                });
                officers_table += '</tbody></table>';

                $('.company-officers').append(officers_table);
            });
        },
        update_company: function (start_index) {
            $.ajax({
                url: helper.baseUrl + "companyhouse/update_company",
                type: "POST",
                dataType: "JSON",
                global: false,
                data: $('.update-company-form').serialize()
            }).done(function (response) {
                if (response.success) {
                    record.company_panel.load_panel(record.urn, $('.search-company-form').find('input[name="company_id"]').val());
                    record.contact_panel.load_panel(record.urn, $('.search-company-form').find('input[name="company_id"]').val());
                    record.company_panel.close_get_company();
                    flashalert.success(response.msg);
                }
                else {
                    flashalert.danger(response.msg);
                }

            });
        }
    },    //emails panel functions
    sms_panel: {
        init: function () {
            this.config = {
                panel: '#sms-panel'
            };
            record.sms_panel.load_panel();
            $(document).on('click', '#new-sms-btn', function (e) {
                e.preventDefault();
                record.sms_panel.create();
            });
            $(document).on('click', '#continue-sms', function (e) {
                e.preventDefault();
                var template = $('#smstemplatespicker').val();
                window.location.href = helper.baseUrl + 'sms/create/' + template + '/' + record.urn;
            });
            $(document).on('click', 'span.del-sms-btn', function (e) {
                e.preventDefault();
                modal.delete_sms($(this).attr('item-id'), $(this).attr('item-modal'));
            });
            $(document).on('click', '#show-all-sms-btn', function (e) {
                e.preventDefault();
                record.sms_panel.show_all_sms();
            });
            $(document).on('click', 'span.view-sms-btn', function (e) {
                e.preventDefault();
                record.sms_panel.view_sms($(this).attr('item-id'));
            });
        },
        create: function () {
			$.ajax({url:helper.baseUrl+'modals/new_sms_form',
			type:"POST",
			dataType:"HTML",
			data:{urn:record.urn},
			}).done(function(data){
				var $mbody = $(data),mheader = "Send sms",mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button> <button type="submit" class="marl btn btn-primary" disabled id="continue-sms">Continue</button>';
            $mbody.find('#smstemplatespicker').selectpicker().on('change', function () {
                var selected = $('#smstemplatespicker option:selected').val();
                if (selected) {
                    $('#continue-sms').prop('disabled', false);
                }
                else {
                    $('#continue-sms').prop('disabled', true);
                }
            });
			modals.load_modal(mheader,$mbody,mfooter);
			modal_body.css('overflow','visible');
			 });
        },
        remove_sms: function (sms_id, modal) {
            $.ajax({
                url: helper.baseUrl + 'sms/delete_sms',
                type: "POST",
                dataType: "JSON",
                data: {sms_id: sms_id}
            }).done(function (response) {
                if (response.success) {
                    record.sms_panel.load_panel();
                    if (modal == 1) {
                        record.sms_panel.close_all_sms();
                        record.sms_panel.show_all_sms();
                    }
                    flashalert.success("sms was deleted from the history");
                }
                ;
            });
        },
        view_sms: function (sms_id) {
			//Get template data
            $.ajax({
                url: helper.baseUrl + 'modals/view_sms',
                dataType: "HTML",
            }).done(function (data){
				var mheader = "View sms",$mbody =$(data),mfooter='<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>';
				modals.load_modal(mheader,$mbody,mfooter);
				record.sms_panel.load_sms_view(sms_id);
			});
		},
			load_sms_view: function (sms_id) {
            $.ajax({
                url: helper.baseUrl + 'sms/get_sms',
                type: "POST",
                dataType: "JSON",
                data: {sms_id: sms_id}
            }).done(function (response) {
                var message = (response.data.status == true) ? "<th colspan='2' style='color:green'>" + ((response.data.pending == 1) ? "Pending to (re)send automatically..." : "This sms was sent successfuly") + "</th>" : "<th colspan='2' style='color:red'>" + ((response.data.pending == 1) ? "Pending to send automatically..." : "This sms was not sent") + "</th>"
                var status = (response.data.status == true) ? "Yes" : "No";
                var read_confirmed = (response.data.read_confirmed == 1) ? "Yes " + " (" + response.data.read_confirmed_date + ")" : "No";

                var tbody = "<tr>" +
                    message +
                    "</tr>" +
                    "<th>Sent Date</th>" +
                    "<td class='sent_date'>" + response.data.sent_date + "</td>" +
                    "</tr>" +
                    "<tr>" +
                    "<th>From</th>" +
                    "<td class='from'>" + response.data.send_from + "</td>" +
                    "</tr>" +
                    "<tr>" +
                    "<th>To</th>" +
                    "<td class='to'>" + response.data.send_to + "</td>" +
                    "</tr>" +
                    "<tr>" +
                    "<th>CC</th>" +
                    "<td class='cc'>" + response.data.cc + "</td>" +
                    "</tr>" +
                    "<tr>" +
                    "<th>BCC</th>" +
                    "<td class='bcc'>" + response.data.bcc + "</td>" +
                    "</tr>" +
                    "<tr>" +
                    "<th>Subject</th>" +
                    "<td class='subject'>" + response.data.subject + "</td>" +
                    "</tr>" +
                    "<tr>" +
                    "<th colspan=2>Body</th>" +
                    "</tr>" +
                    "<td colspan=2 class='body'>" + response.data.body + "</td>" +
                    "</tr>" +
                    "<th>Sent</th>" +
                    "<td class='status'>" + status + ((response.data.pending == 1) ? " (Pending to (re)send automatically...)" : "") + "</td>" +
                    "</tr>" +
                    "<th>Read Confirmed</th>" +
                    "<td class='read_confirmed'>" + read_confirmed + "</td>" +
                    "</tr>"
                if (response.attachments.length > 0) {
                    tbody += "<tr>" +
                        "<th colspan=2>Attachments</th>" +
                        "</tr>";
                    $.each(response.attachments, function (key, val) {
                        tbody += "<tr>" +
                            "<td colspan='2' class='attachments'><a target='_blank' href='" + val.path + "'>" + val.name + "</td></tr>";
                    });
                }
              $('#sms-view-table').html(tbody);
            });
        },
        show_all_sms: function () {
			
			  $.ajax({
                url: helper.baseUrl + "modals/show_all_sms",
                type: "POST",
                dataType: "HTML"
            }).done(function(data){
				var mheader = "Showing all sms", $mbody = $(data), mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>';
				modals.load_modal(mheader,$mbody,mfooter);
				record.sms_panel.load_sms();
			});
		},
		load_sms:function(){
            //Get emails data
            $.ajax({
                url: helper.baseUrl + "email/get_sms_by_urn",
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: record.urn
                }
            }).done(function (response) {
                if (response.data.length > 0) {
					var tbody = '';
                    $.each(response.data, function (key, val) {
                        var status = (val.pending == 1) ? "glyphicon-time red" : ((val.status != true) ? "glyphicon-eye-open red" : ((val.read_confirmed == 1) ? "glyphicon-eye-open green" : "glyphicon-eye-open"));
                        var message = (val.pending == 1) ? "sms pending to send" : (val.status != true) ? "sms no sent" : ((val.read_confirmed == 1) ? "sms read confirmed " + " (" + val.read_confirmed_date + ")" : "Waiting sms read confirmation");
                        var send_to = (val.send_to.length > 15) ? val.send_to.substring(0, 15) + '...' : val.send_to;
                        var subject = (val.subject.length > 20) ? val.subject.substring(0, 20) + '...' : val.subject;
                        var $delete_option = "";
                        if (helper.permissions['delete sms'] > 0) {
                            $delete_option = '<span class="glyphicon glyphicon-trash pull-right del-sms-btn marl" data-target="#modal" item-modal="1" item-id="' + val.sms_id + '" title="Delete sms" ></span>';
                        }
                        $view_option = '<span class="glyphicon ' + status + ' pull-right view-sms-btn pointer"  item-id="' + val.sms_id + '" title="' + message + '"></span>';
                        tbody += '<tr><td>' + val.sent_date + '</td><td>' + val.name + '</td><td title="' + val.send_to + '" >' + send_to + '</td><td title="' + val.subject + '" >' + subject + '</td><td>' + $view_option + '</td><td>' + $delete_option + '</td></tr>';
                    });
                    var table = '<thead><tr><th>Date</th><th>User</th><th>To</th><th>Subject</th><th></th><th></th></tr></thead><tbody>'+tbody+'</tbody>';
					$('#sms-all-table').html(table);
                } else {
                    modal_body.html('<p>No sms have been sent for this record</p>');
                }
            });
        },
        load_panel: function () {
            $.ajax({
                url: helper.baseUrl + "sms/get_sms_by_urn",
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: record.urn,
                    limit: record.limit
                }
            }).done(function (response) {
                $('#sms-panel').empty();
                var $body = "";
                if (response.data.length > 0) {
                    //Use the k var only to know if there are more than x records
                    var k = 0;
                    $.each(response.data, function (key, val) {
                        if (k <= record.limit - 1) {
                            var status = (val.pending == 1) ? "glyphicon-time red" : ((val.status != true) ? "glyphicon-eye-open red" : ((val.read_confirmed == 1) ? "glyphicon-eye-open green" : "glyphicon-eye-open"));
                            var message = (val.pending == 1) ? "sms pending to send" : (val.status != true) ? "sms no sent" : ((val.read_confirmed == 1) ? "sms read confirmed " + " (" + val.read_confirmed_date + ")" : "Waiting sms read confirmation");
                            var send_to = (val.send_to.length > 15) ? val.send_to.substring(0, 15) + '...' : val.send_to;
                            var subject = (val.subject.length > 20) ? val.subject.substring(0, 20) + '...' : val.subject;
                            var $delete_option = "";
                            if (helper.permissions['delete sms'] > 0) {
                                $delete_option = '<span class="glyphicon glyphicon-trash pull-right pointer del-sms-btn marl" data-target="#modal" item-modal="0" item-id="' + val.sms_id + '" title="Delete sms" ></span>';
                            }
                            $view_option = '<span class="glyphicon ' + status + ' pull-right view-sms-btn pointer"  item-id="' + val.sms_id + '" title="' + message + '"></span>';
                            $body += '<tr><td>' + val.sent_date + '</td><td>' + val.name + '</td><td title="' + val.send_to + '" >' + send_to + '</td><td title="' + val.subject + '" >' + subject + '</td><td>' + $view_option + '</td><td>' + $delete_option + '</td></tr>';
                        }
                        k++;
                    });
                    if (k > record.limit - 1) {
                        $body += '<tr><td colspan="6"><a href="#"><span class="btn btn-info btn-sm pull-right" id="show-all-sms-btn">Show All</span></a></td></tr>';
                    }
                    $('#sms-panel').append('<div class="table-responsive"><table class="table table-striped table-condensed table-responsive"><thead><tr><th>Date</th><th>User</th><th>To</th><th>Subject</th><th></th><th></th></tr></thead><tbody>' + $body + '</tbody></table></div>');
                } else {
                     $('#sms-panel').append('<p>No sms have been sent for this record</p>');
                }
                ;
            });
        }
    },
	appointment_slots_panel: {
		  init: function () {
			 record.appointment_slots_panel.load_panel();
		  },
		  load_panel:function(){
			   $.ajax({
                url: helper.baseUrl + "appointments/slot_availability",
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: record.urn,
                }
            }).done(function (response) {
				$('#slots-panel').empty();
				if(response.success){
				var table ='<div class="table-responsive" style="overflow:auto; max-height:250px"><table class="table table-condensed table-striped"><thead><th>Date</th><th>AM</th><th>PM</th><th>EVE</th></thead><tbody>';
				$.each(response.data,function(k,v){
					var slot_color="";
					if(v.am>=v.am_max||v.pm>=v.pm_max||v.eve>=v.eve_max){ 
					slot_color = 'text-danger';
					}
					table += '<tr><td>'+k+'</td><td class="'+slot_color+'" >'+v.am+'</td><td  class="'+slot_color+'">'+v.pm+'</td><td  class="'+slot_color+'">'+v.eve+'</td></tr>'
				});
				table += '</tbody></table></div>';
				$('#slots-panel').html(table);	
				} else {
					$('#slots-panel').html('<p>No slots found</p>');		
				}
			});
		  }
	},
    //emails panel functions
    email_panel: {
        init: function () {
            this.config = {
                panel: '.email-panel'
            };
            record.email_panel.load_panel();
            $(document).on('click', '#new-email-btn', function (e) {
                e.preventDefault();
                record.email_panel.create();
            });
            $(document).on('click', '#continue-email', function (e) {
                e.preventDefault();
                var template = $('#emailtemplatespicker').val();
                window.location.href = helper.baseUrl + 'email/create/' + template + '/' + record.urn;
            });
            $(document).on('click', 'span.del-email-btn', function (e) {
                e.preventDefault();
                modal.delete_email($(this).attr('item-id'), $(this).attr('item-modal'));
            });
            $(document).on('click', '#show-all-email-btn', function (e) {
                e.preventDefault();
                record.email_panel.show_all_email();
            });
            $(document).on('click', 'span.view-email-btn', function (e) {
                e.preventDefault();
                record.email_panel.view_email($(this).attr('item-id'));
            });
        },
        create: function () {
			$.ajax({url:helper.baseUrl+'modals/new_email_form',
			type:"POST",
			dataType:"HTML",
			data:{urn:record.urn},
			}).done(function(data){
				var $mbody = $(data),mheader = "Send Email",mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button> <button type="submit" class="marl btn btn-primary" disabled id="continue-email">Continue</button>';
            $mbody.find('#emailtemplatespicker').selectpicker().on('change', function () {
                var selected = $('#emailtemplatespicker option:selected').val();
                if (selected) {
                    $('#continue-email').prop('disabled', false);
                }
                else {
                    $('#continue-email').prop('disabled', true);
                }
            });
			modals.load_modal(mheader,$mbody,mfooter);
			modal_body.css('overflow','visible');
			 });
        },
        remove_email: function (email_id, modal) {
            $.ajax({
                url: helper.baseUrl + 'email/delete_email',
                type: "POST",
                dataType: "JSON",
                data: {email_id: email_id}
            }).done(function (response) {
                if (response.success) {
                    record.email_panel.load_panel();
                    if (modal == 1) {
                        record.email_panel.close_all_email();
                        record.email_panel.show_all_email();
                    }
                    flashalert.success("Email was deleted from the history");
                }
                ;
            });
        },
        view_email: function (email_id) {
			//Get template data
            $.ajax({
                url: helper.baseUrl + 'modals/view_email',
                dataType: "HTML",
            }).done(function (data){
				var mheader = "View Email",$mbody =$(data),mfooter='<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>';
				modals.load_modal(mheader,$mbody,mfooter);
				record.email_panel.load_email_view(email_id);
			});
		},
			load_email_view: function (email_id) {
            $.ajax({
                url: helper.baseUrl + 'email/get_email',
                type: "POST",
                dataType: "JSON",
                data: {email_id: email_id}
            }).done(function (response) {
                var message = (response.data.status == true) ? "<th colspan='2' style='color:green'>" + ((response.data.pending == 1) ? "Pending to (re)send automatically..." : "This email was sent successfuly") + "</th>" : "<th colspan='2' style='color:red'>" + ((response.data.pending == 1) ? "Pending to send automatically..." : "This email was not sent") + "</th>"
                var status = (response.data.status == true) ? "Yes" : "No";
                var read_confirmed = (response.data.read_confirmed == 1) ? "Yes " + " (" + response.data.read_confirmed_date + ")" : "No";

                var tbody = "<tr>" +
                    message +
                    "</tr>" +
                    "<th>Sent Date</th>" +
                    "<td class='sent_date'>" + response.data.sent_date + "</td>" +
                    "</tr>" +
                    "<tr>" +
                    "<th>From</th>" +
                    "<td class='from'>" + response.data.send_from + "</td>" +
                    "</tr>" +
                    "<tr>" +
                    "<th>To</th>" +
                    "<td class='to'>" + response.data.send_to + "</td>" +
                    "</tr>" +
                    "<tr>" +
                    "<th>CC</th>" +
                    "<td class='cc'>" + response.data.cc + "</td>" +
                    "</tr>" +
                    "<tr>" +
                    "<th>BCC</th>" +
                    "<td class='bcc'>" + response.data.bcc + "</td>" +
                    "</tr>" +
                    "<tr>" +
                    "<th>Subject</th>" +
                    "<td class='subject'>" + response.data.subject + "</td>" +
                    "</tr>" +
                    "<tr>" +
                    "<th colspan=2>Body</th>" +
                    "</tr>" +
                    "<td colspan=2 class='body'>" + response.data.body + "</td>" +
                    "</tr>" +
                    "<th>Sent</th>" +
                    "<td class='status'>" + status + ((response.data.pending == 1) ? " (Pending to (re)send automatically...)" : "") + "</td>" +
                    "</tr>" +
                    "<th>Read Confirmed</th>" +
                    "<td class='read_confirmed'>" + read_confirmed + "</td>" +
                    "</tr>"
                if (response.attachments.length > 0) {
                    tbody += "<tr>" +
                        "<th colspan=2>Attachments</th>" +
                        "</tr>";
                    $.each(response.attachments, function (key, val) {
                        tbody += "<tr>" +
                            "<td colspan='2' class='attachments'><a target='_blank' href='" + val.path + "'>" + val.name + "</td></tr>";
                    });
                }
              $('#email-view-table').html(tbody);
            });
        },
        show_all_email: function () {
			
			  $.ajax({
                url: helper.baseUrl + "modals/show_all_email",
                type: "POST",
                dataType: "HTML"
            }).done(function(data){
				var mheader = "Showing all emails", $mbody = $(data), mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>';
				modals.load_modal(mheader,$mbody,mfooter);
				record.email_panel.load_emails();
			});
		},
		load_emails:function(){
            //Get emails data
            $.ajax({
                url: helper.baseUrl + "email/get_emails",
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: record.urn
                }
            }).done(function (response) {
                if (response.data.length > 0) {
					var tbody = '';
                    $.each(response.data, function (key, val) {
                        var status = (val.pending == 1) ? "glyphicon-time red" : ((val.status != true) ? "glyphicon-eye-open red" : ((val.read_confirmed == 1) ? "glyphicon-eye-open green" : "glyphicon-eye-open"));
                        var message = (val.pending == 1) ? "Email pending to send" : (val.status != true) ? "Email no sent" : ((val.read_confirmed == 1) ? "Email read confirmed " + " (" + val.read_confirmed_date + ")" : "Waiting email read confirmation");
                        var send_to = (val.send_to.length > 15) ? val.send_to.substring(0, 15) + '...' : val.send_to;
                        var subject = (val.subject.length > 20) ? val.subject.substring(0, 20) + '...' : val.subject;
                        var $delete_option = "";
                        if (helper.permissions['delete emails'] > 0) {
                            $delete_option = '<span class="glyphicon glyphicon-trash pull-right del-email-btn marl" data-target="#modal" item-modal="1" item-id="' + val.email_id + '" title="Delete email" ></span>';
                        }
                        $view_option = '<span class="glyphicon ' + status + ' pull-right view-email-btn pointer"  item-id="' + val.email_id + '" title="' + message + '"></span>';
                        tbody += '<tr><td>' + val.sent_date + '</td><td>' + val.name + '</td><td title="' + val.send_to + '" >' + send_to + '</td><td title="' + val.subject + '" >' + subject + '</td><td>' + $view_option + '</td><td>' + $delete_option + '</td></tr>';
                    });
                    var table = '<thead><tr><th>Date</th><th>User</th><th>To</th><th>Subject</th><th></th><th></th></tr></thead><tbody>'+tbody+'</tbody>';
					$('#email-all-table').html(table);
                } else {
                    modal_body.html('<p>No emails have been sent for this record</p>');
                }
            });
        },
        load_panel: function () {
            $.ajax({
                url: helper.baseUrl + "email/get_emails",
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: record.urn,
                    limit: record.limit
                }
            }).done(function (response) {
                $('.email-panel').empty();
                var $body = "";
                if (response.data.length > 0) {
                    //Use the k var only to know if there are more than x records
                    var k = 0;
                    $.each(response.data, function (key, val) {
                        if (k <= record.limit - 1) {
                            var status = (val.pending == 1) ? "glyphicon-time red" : ((val.status != true) ? "glyphicon-eye-open red" : ((val.read_confirmed == 1) ? "glyphicon-eye-open green" : "glyphicon-eye-open"));
                            var message = (val.pending == 1) ? "Email pending to send" : (val.status != true) ? "Email no sent" : ((val.read_confirmed == 1) ? "Email read confirmed " + " (" + val.read_confirmed_date + ")" : "Waiting email read confirmation");
                            var send_to = (val.send_to.length > 15) ? val.send_to.substring(0, 15) + '...' : val.send_to;
                            var subject = (val.subject.length > 20) ? val.subject.substring(0, 20) + '...' : val.subject;
                            var $delete_option = "";
                            if (helper.permissions['delete email'] > 0) {
                                $delete_option = '<span class="glyphicon glyphicon-trash pull-right pointer del-email-btn marl" data-target="#modal" item-modal="0" item-id="' + val.email_id + '" title="Delete email" ></span>';
                            }
                            $view_option = '<span class="glyphicon ' + status + ' pull-right view-email-btn pointer"  item-id="' + val.email_id + '" title="' + message + '"></span>';
                            $body += '<tr><td>' + val.sent_date + '</td><td>' + val.name + '</td><td title="' + val.send_to + '" >' + send_to + '</td><td title="' + val.subject + '" >' + subject + '</td><td>' + $view_option + '</td><td>' + $delete_option + '</td></tr>';
                        }
                        k++;
                    });
                    if (k > record.limit - 1) {
                        $body += '<tr><td colspan="6"><a href="#"><span class="btn btn-info btn-sm pull-right" id="show-all-email-btn">Show All</span></a></td></tr>';
                    }
                    $('.email-panel').append('<div class="table-responsive"><table class="table table-striped table-condensed table-responsive"><thead><tr><th>Date</th><th>User</th><th>To</th><th>Subject</th><th></th><th></th></tr></thead><tbody>' + $body + '</tbody></table></div>');
                } else {
                    $('.email-panel').append('<p>No emails have been sent for this record</p>');
                }
                ;
            });
        }
    },
    //surveys panel functions
    surveys_panel: {
        init: function () {
            this.config = {
                panel: '.surveys-panel'
            };
            record.surveys_panel.load_panel();
            $(document).on('click', '#new-survey', function (e) {
                e.preventDefault();
                record.surveys_panel.create();
            });
            $(document).on('click', '#continue-survey', function (e) {
                e.preventDefault();
                var survey = modal_body.find('#surveypicker').val();
                var contact = modal_body.find('#contactpicker').val();
                window.location.href = helper.baseUrl + 'survey/create/' + survey + '/' + record.urn + '/' + contact;
                //record.surveys_panel.new_survey(); we dont use the popup any more
            });
            $(document).on('change', '#contactpicker', function (e) {
                record.surveys_panel.check_contact($(this));
            });
            $(document).on('click', 'span.edit-survey-btn', function (e) {
                e.preventDefault();
                window.location.href = helper.baseUrl + "survey/edit/" + $(this).attr('item-id');
            });
            $(document).on('click', 'span.eye-survey-btn', function (e) {
                e.preventDefault();
                window.location.href = helper.baseUrl + "survey/edit/" + $(this).attr('item-id');
            });
            $(document).on('click', 'span.del-survey-btn', function (e) {
                e.preventDefault();
                modal.delete_survey($(this).attr('item-id'));
            });

        },
        create: function () {
           $.ajax({
                url: helper.baseUrl + 'modals/start_survey',
                type: "POST",
				data: { urn:record.urn },
                dataType: "HTML"
		   }).done(function(data){
			  var $mbody = $(data);
			  var mheader = "Create Survey"; 
			  var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button> <button type="submit" class="btn btn-primary pull-right" id="continue-survey">Continue</button>';
		  
		  $mbody.find('#surveypicker,#contactpicker').selectpicker();
		  modals.load_modal(mheader,$mbody,mfooter);
		   });
        },
        check_contact: function ($btn) {
            if ($('#contactpicker').val() == "") {
                $('#continue-survey').prop('disabled', true);
                $('.page-danger .alert-text').text('You must add the contact to the record before you can start a survey');
                $('.page-danger').removeClass('hidden').fadeIn(1000);
            } else {
                $('#continue-survey').prop('disabled', false);
                $('.page-danger').fadeOut(1000).addClass('hidden');
            }
        },
        remove: function (id) {
            $.ajax({
                url: helper.baseUrl + 'ajax/delete_survey',
                type: "POST",
                dataType: "JSON",
                data: {
                    survey: id
                }
            }).done(function (response) {
                if (response.success) {
                    record.surveys_panel.load_panel();
                    flashalert.success("Survey was deleted");
                }
                ;
            });
        },
        load_panel: function () {
            $.ajax({
                url: helper.baseUrl + "ajax/get_surveys",
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: record.urn
                }
            }).done(function (response) {
                $('#surveys-panel').empty();
                var $body = "";
                if (response.data) {
                    $.each(response.data, function (key, val) {
                        var $delete = "";
                        var $options = "";

                        if (helper.permissions['delete surveys'] > 0) {
                            $options = '<span class="glyphicon glyphicon-trash pointer pull-right del-survey-btn" data-target="#modal" item-id="' + key + '" ></span><span class="glyphicon glyphicon-pencil pointer pull-right edit-survey-btn"  item-id="' + key + '"></span>';
                        }
                        if (helper.permissions['edit surveys'] > 0 || !val.locked) {
                            $options = '<span class="glyphicon glyphicon-edit pull-right edit-survey-btn pointer"  item-id="' + key + '"></span>';
                        }
                        if ($options == "") {
                            $options = '<span class="glyphicon glyphicon-eye-open pull-right eye-survey-btn pointer"  item-id="' + key + '"></span>';
                        }

                        $body += '<tr><td>' + val.date_created + '</td><td>' + val.contact_name + '</td><td>' + val.client_name + '</td><td>' + val.answer + '</td><td>' + val.is_completed + '</td><td>' + $options + '</td></tr>';
                    });
                    $('#surveys-panel').append('<div class="table-responsive"><table class="table table-striped table-condensed"><thead><tr><th>Date</th><th>Contact</th><th>User</th><th>NPS</th><th>Status</th><th>Options</th></tr></thead><tbody>' + $body + '</tbody></table></div>');

                    //alert("show surveys");
                } else {
                    $('#surveys-panel').append('<p>No surveys have been created for this record</p>');
                    //alert("no surveys");
                }

            });
        }
    },
    //get additional info
    additional_info: {
        init: function () {
            $(document).on("click", "span.add-detail-btn", function () {
                $(this).removeClass('glyphicon-pencil pointer add-detail-btn').addClass('glyphicon-remove close-custom');
                $('#custom-panel').find('.panel-content').fadeOut(function () {
                    $('#custom-panel').find('form')[0].reset();
                    $('#custom-panel').find('form input').not('input[name="urn"]').val('');
                    $('#custom-panel').find('form').fadeIn();
                });
            });
            $(document).on("click", ".del-detail-btn", function () {
                modal.delete_additional_item($(this).attr('item-id'));
            });
            $(document).on("click", ".edit-detail-btn", function () {
                record.additional_info.edit($(this).attr('item-id'));
            });
            $(document).on("click", ".close-custom", function (e) {
                e.preventDefault();
                $('#custom-panel').find('.glyphicon-remove').removeClass('glyphicon-remove close-custom').addClass('glyphicon-plus add-detail-btn');
                $('#custom-panel').find('form').fadeOut(function () {
                    $('#custom-panel').find('.panel-content').fadeIn()
                });
            });
            $(document).on("click", ".save-info", function (e) {
                e.preventDefault();
                record.additional_info.save();
            })
            record.additional_info.load_panel();
        },
        remove: function (id) {
            $.ajax({
                url: helper.baseUrl + "ajax/remove_custom_item",
                type: "POST",
                dataType: "JSON",
                data: {
                    id: id
                }
            }).done(function (response) {
                record.additional_info.load_panel();
                flashalert.success("Selected information was deleted");
            });
        },
        edit: function (id) {
            $('#custom-panel').find('.panel-content').fadeOut(function () {
                $.ajax({
                    url: helper.baseUrl + "ajax/get_details_from_id",
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        urn: record.urn,
                        campaign: record.campaign,
                        id: id
                    }
                }).done(function (response) {
                    record.additional_info.load_form(response.data, id);
                    $('#custom-panel').find('form').fadeIn();
                });
            });
        },
        save: function () {
            $.ajax({
                url: helper.baseUrl + "ajax/save_additional_info",
                type: "POST",
                dataType: "JSON",
                data: $('#custom-panel').find('form').serialize()
            }).done(function (response) {
                record.additional_info.load_panel();
                $('#custom-panel').find('form').fadeOut(function () {
                    $('#custom-panel').find('.panel-content').fadeIn()
                });
                flashalert.success(response.msg);
            });
        },
        load_panel: function () {
            $.ajax({
                url: helper.baseUrl + "ajax/get_additional_info",
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: record.urn
                }
            }).done(function (response) {
                if (response.data.length > 0) {
                    record.additional_info.load_table(response.data);
                    record.additional_info.load_form(response.data);
                } else {
                    $('#custom-panel').find('.panel-content').text("Nothing was found");
                }
            });
        },
        load_table: function (data) {
            var $panel = $('#custom-panel').find('.panel-content');
            $panel.empty();
            var table = "<div class='table-responsive'><table class='table table-striped table-condensed'>";
            var thead, detail_id;
            var tbody = "<tbody>";
            var contents = "";
            $.each(data, function (k, detail) {
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
                tbody += '<td><span class="glyphicon glyphicon-trash pointer pull-right del-detail-btn marl" data-target="#modal" item-id="' + detail_id + '" ></span> <span class="glyphicon glyphicon-pencil pointer pull-right edit-detail-btn"  item-id="' + detail_id + '"></span></td><tr>';
            });
            table += thead + '</thead>' + tbody + '<tbody></table></div>';
            $panel.append(table);
        },
        load_form: function (data, id) {
            var $form = $('#custom-panel').find('form');
            $form.empty();
            $form.append("<input type='hidden' name='urn' value='" + record.urn + "'/>");
            $form.append("<input type='hidden' name='detail_id' value='" + id + "'/>");
            var form = "";
            $.each(data, function (k, detail) {

                $.each(detail, function (i, row) {
                    var inputclass = "";
                    if (row.options) {
                        $select = "<div class='form-group input-group-sm'>" + row.name;
                        $select += '<br><select name="' + row.code + '" class="selectpicker"><option value="">Please select</option>';
                        $.each(row.options, function (option_id, option_val) {
                            if (row.value == option_val) {
                                var selected = "selected";
                            }
                            $select += "<option " + selected + " value='" + option_val + "'>" + option_val + "</option>";
                        });
                        $select += "</select></div>";
                        form += $select;
                    } else {
                        if (row.type != "varchar" && row.type != "number") {
                            inputclass = row.type;
                            if (row.value = '-' && row.type == "datetime") {
                                row.value = moment(new Date()).format("DD/MM/YYYY HH:mm:ss");
                            }
                            if (row.value = '-' && row.type == "date") {
                                row.value = moment(new Date()).format("DD/MM/YYYY");
                            }
                        }
                        form += " <div class='form-group input-group-sm " + inputclass + "'>" + row.name + "<input class='form-control' name='" + row.code + "' type='text' value='" + row.value + "'/></div>";
                    }
                });
            });
            $form.append(form + "<button class='btn btn-primary pull-right marl save-info'>Save</button> <button class='btn btn-default pull-right close-custom'>Cancel</button>");
            $('.selectpicker').selectpicker();
            $('.date').datetimepicker({
                pickTime: false,
                format: 'DD/MM/YYYY'
            });
            $('.datetime').datetimepicker({
                format: 'DD/MM/YYYY HH:mm'
            });
        }
    },
    //ownership panel functions
    ownership_panel: {
        init: function () {
            record.ownership_panel.load_panel(record.urn);
            $(document).on('click', '.edit-owner', function (e) {
                e.preventDefault();
                record.ownership_panel.edit($(this));
            });
            $(document).on('click', '.close-owner', function (e) {
                e.preventDefault();
                record.ownership_panel.close_panel();
            });
            $(document).on('click', '.save-ownership', function (e) {
                e.preventDefault();
                record.ownership_panel.save();
            });
        },

        close_panel: function () {
            $panel = $('.ownership-panel');
            record.ownership_panel.load_panel();
            $panel.find('.edit-panel').fadeOut(1000, function () {
                $panel.find('.panel-content').fadeIn(1000, function () {

                    $panel.find('.glyphicon-remove').removeClass('glyphicon-remove close-owner').addClass('glyphicon-pencil pointer edit-owner');

                });
            });
        },
        save: function () {
            $panel = $('.ownership-panel');
            var owners = $panel.find('.owners').val();
            $.ajax({
                url: helper.baseUrl + "ajax/save_ownership",
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: record.urn,
                    owners: owners
                }
            }).done(function (response) {
                record.ownership_panel.close_panel();
                flashalert.success("Ownership was updated");
            });
        },
        load_panel: function () {
            $panel = $('.ownership-panel');
            $.ajax({
                url: helper.baseUrl + "ajax/get_users",
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: record.urn
                },
                beforeSend: function () {
                    $panel.find('.panel-content').html("<img src='" + helper.baseUrl + "assets/img/ajax-loader-bar.gif' />");
                }
            }).done(function (response) {
                $panel.find('.panel-content').empty();
                if (response.data.length) {
                    $panel.find('.panel-content').append($('<ul/>'));
                    $.each(response.data, function (i, val) {
                        $panel.find('.panel-content ul').append($('<li/>').text(val.name));
                    });
                } else {
                    $panel.find('.panel-content').append($('<p/>').text('There are no users allocated to this record. To take ownership you can update the record or use the edit button to assign it to another user.'));
                }

            });
        },
        edit: function ($btn) {
            $panel = $('.ownership-panel');

            $btn.removeClass('glyphicon-pencil edit-owner').addClass('glyphicon-remove close-owner');
            $panel.find('.panel-content').fadeOut(1000, function () {
                $panel.find('.edit-panel').fadeIn(1000);
            });
            $.ajax({
                url: helper.baseUrl + "ajax/get_ownership",
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: record.urn
                }
            }).done(function (response) {
                $('.owners').selectpicker('val', response.data).selectpicker('render');
            });
        }
    },
    //script panel functions
    script_panel: function () {
        $(document).on("click", ".view-script", function (e) {
            e.preventDefault();
            $.ajax({
                url: helper.baseUrl + "ajax/get_script",
                type: "POST",
                dataType: "JSON",
                data: {
                    id: $(this).attr('script-id')
                }
            }).done(function (response) {
                var mheader = response.data.script_name;
                var mbody = response.data.script;
                var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>';
                modals.load_modal(mheader, mbody, mfooter);
            });
        });
    },
    appointment_panel: {
        //initalize the group specific buttons 
        init: function () {
            $(document).on('click', '.close-appointment', function (e) {
                e.preventDefault();
                record.appointment_panel.hide_edit_form();
            });
            $(document).on('click', '.view-calendar', function (e) {
                e.preventDefault();
                modal.show_calendar(record.urn);
            });
            //start the function to load the groups into the table
            record.appointment_panel.load_appointments();
        },
        //this function reloads the groups into the table body
        load_appointments: function () {
            $.ajax({
                url: helper.baseUrl + 'records/load_appointments',
                type: "POST",
                dataType: "JSON",
                data: {urn: record.urn}
            }).done(function (response) {
                var $panel = $('.appointment-panel').find('.panel-content');
                $panel.empty();
                if (response.success) {
                    if (response.data.length > 0) {
                        record.appointment_panel.load_table(response.data);
                        $('.record-panel').find('.outcomepicker').find('li.disabled').each(function () {
                            $(this).removeClass('disabled');
                        });
                        $('.record-panel').find('.outcomepicker').find('option:disabled').each(function () {
                            $(this).prop('disabled', false);
                        });
                    } else {
                        $panel.append('<p>No appointments have been created</p>');
                    }
                } else {
                    $panel.append('<p>' + response.msg + '</p>');
                }
            });
        },
        load_table: function (data) {
            var $panel = $('.appointment-panel').find('.panel-content');
            $panel.empty();

            var table = "<div class='table-responsive'><table class='table table-striped table-condensed table-hover pointer'><thead><tr><th>Title</th><th>Info</th><th>Date</th><th>Time</th></tr></thead><tbody>";
            $.each(data, function (i, val) {
                if (data.length) {
					var cancel_class = "";
				if(val.cancellation_reason){ cancel_class='danger' }
                    table += '<tr class="'+cancel_class+'" data-modal="view-appointment" data-id="' + val.appointment_id + '"><td>' + val.title + '</td><td>' + val.text + '</td><td>' + val.date + '</td><td>' + val.time + '</td></tr>';
                }
            });
            $panel.append(table + "</tbody></table></div>");

        }
    },
    related_panel: {
        init: function () {
            record.related_panel.load_panel();
            $(document).on('change', '.related-campaigns', function (e) {
                e.preventDefault();
                record.recordings_panel.load_panel($(this).val())
            })
        },
        load_panel: function (campaign) {
            var $panel = $('.related-panel');
            $.ajax({
                url: helper.baseUrl + "records/related_records",
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: record.urn,
                    campaign: campaign
                },
                beforeSend: function () {
                    $panel.html("<img src='" + helper.baseUrl + "assets/img/ajax-loader-bar.gif' />");
                }
            }).fail(function () {
                $panel.html($('<p/>').text("No similar records could be found"));
            }).done(function (response) {
                $panel.empty();
                $body = "";
                if (response.data.length > 0) {
                    $.each(response.data, function (i, val) {

                        $body += '<tr class="pointer" data-modal="view-record" data-urn=' + val.urn + '><td>' + val.campaign_name + '</td><td>' + val.name + '</td><td>' + val.status_name + '</td><td>' + val.matched_on + '</td></tr>';
                    });
                    $panel.html('<div class="table-responsive"><table class="table table-hover table-striped table-condensed"><thead><tr><th>Campaign</th><th>Company</th><th>Status</th><th>Matched on</th></tr></thead><tbody>' + $body + '</tbody></table></div>');
                } else {
                    $panel.html($('<p/>').text(response.msg));
                }

            });
        }
    },
    recordings_panel: {
        init: function () {
            record.recordings_panel.load_panel();
            $(document).on('click', '.listen', function (e) {
                e.preventDefault();
                record.recordings_panel.convert_recording($(this), $(this).attr('data-id'), $(this).attr('data-path'))
            })
        },
        load_panel: function () {
            var $panel = $('.recordings-panel');
            $.ajax({
                url: helper.baseUrl + "recordings/find_calls",
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: record.urn
                },
                beforeSend: function () {
                    $panel.html("<img src='" + helper.baseUrl + "assets/img/ajax-loader-bar.gif' />");
                }
            }).fail(function () {
                $panel.html($('<p/>').text("Call recordings could not be found"));
            }).done(function (response) {
                $panel.empty();
                $body = "";
                if (response.data.length > 0) {
                    $.each(response.data, function (i, val) {
                        var icon = "";
                        if (val.transfer) {
                            icon = '<img style="vertical-align:top"  src="' + helper.baseUrl + 'assets/img/icons/icon-transfer.png"/>';
                        }
                        $body += '<tr><td>' + val.calldate + '</td><td>' + val.duration + '</td><td>' + val.servicename + ' ' + icon + '</td><td width="180"><a href="#" class="listen" data-id="' + val.id + '" data-path="' + val.filepath + '"><span class="speaker glyphicon glyphicon-play"></span> Listen</a> <span class="player-loading hidden">Please wait  <img src="' + helper.baseUrl + 'assets/img/ajax-load-black.gif"/></span></td></tr>';
                    });
                    $panel.html('<div class="table-responsive"><table class="table table-striped table-condensed"><thead><tr><th>Call Date</th><th>Duration</th><th>Number</th><th>Options</th></tr></thead><tbody>' + $body + '</tbody></table></div>');
                } else {
                    $panel.html($('<p/>').text(response.msg));
                }

            });
        },
        convert_recording: function ($btn, id, path) {
            $.ajax({
                url: helper.baseUrl + 'recordings/listen/' + id + '/' + path,
                type: "POST",
                dataType: "JSON",
                beforeSend: function () {
                    $btn.next('.player-loading').removeClass("hidden");
                }
            }).done(function (response) {
                $btn.next('.player-loading').addClass("hidden");
                if (response.success) {
                    modal.call_player(response.filename, response.filetype)
                } else {
                    flashalert.danger("There was a problem loading the recording");
                }
            });
        }
    },
    //attachment_panel_functions
    attachment_panel: {
        init: function () {
            this.config = {
                panel: '.attachment-panel'
            };
            record.attachment_panel.load_panel();

            /* initialize the delete attachment buttons */
            $(document).on('click', '.del-attachment-btn', function (e) {
                e.preventDefault();
                modal.delete_attachment($(this).attr('item-id'));
            });

            $(document).on('click', '.show-all-attachments-btn', function (e) {
                e.preventDefault();
                record.attachment_panel.show_all_attachments($(this));
            });
            $(document).on('click', '.close-attachment-all', function (e) {
                e.preventDefault();
                record.attachment_panel.close_all_attachments($(this));
            });
        },
        load_panel: function (attachment_id) {
            var $panel = $(record.attachment_panel.config.panel);
            $.ajax({
                url: helper.baseUrl + 'records/get_attachments',
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: record.urn,
                    limit: record.limit
                }
            }).done(function (response) {
                $panel.find('.attachment-list').empty();
                var body = '';
                if (response.data.length > 0) {
                    //Use the k var only to know if there are more than x records
                    var k = 0;
                    $.each(response.data, function (key, val) {
                        if (k <= record.limit - 1) {
                            var remove_btn = '<span class="glyphicon glyphicon-trash del-attachment-btn marl" data-target="#modal" item-id="' + val.attachment_id + '" title="Delete attachment"></span>';
                            var download_btn = '<a style="color:black;" href="' + val.path + '"><span class="glyphicon glyphicon-download-alt"></span></a>';
                            body += '<tr class="' + val.attachment_id + '">' +
                                '<td>' + val.name +
                                '</td><td>' + val.date +
                                '</td><td>' + val.user +
                                '</td><td>' + download_btn +
                                '</td><td>' + remove_btn +
                                '</td></tr>';
                        }
                        k++;
                    });
                    if (k > record.limit - 1) {
                        body += '<tr><td colspan="6"><a href="#"><span class="btn pull-right marl" id="show-all-attachments-btn" >Show All</span></a></td></tr>';
                    }
                    $('.attachment-list').append('<div class="table-responsive"><table class="table table-striped table-condensed table-responsive"><thead><tr><th>Name</th><th>Date</th><th>Added by</th><th colspan="2">Options</th></tr></thead><tbody>' + body + '</tbody></table></div>');

                    if (attachment_id) {
                        $panel.find('.attachment-list').find('.' + attachment_id).fadeIn(500).delay(250).fadeOut(500).fadeIn(500).delay(250).fadeOut(500).fadeIn(500).delay(250).fadeOut(500).fadeIn(500);
                    }
                }
                else {
                    $('.attachment-list').append('<p>This record has no attachments</p>');
                }

            });
        },
        show_all_attachments: function (btn) {
            var pagewidth = $(window).width() / 2;
            var moveto = pagewidth - 250;
            $('<div class="modal-backdrop all-attachments in"></div>').appendTo(document.body).hide().fadeIn();
            $('.attachment-all-container').find('.attachment-all-panel').show();
            $('.attachment-all-content').show();
            $('.attachment-all-container').fadeIn()
            $('.attachment-all-container').animate({
                width: '600px',
                left: moveto,
                top: '10%'
            }, 1000);
            //Get attachment data
            $.ajax({
                url: helper.baseUrl + "records/get_attachments",
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: record.urn
                }
            }).done(function (response) {
                var $thead = $('.attachment-all-table').find('thead');
                $thead.empty();

                var $tbody = $('.attachment-all-table').find('tbody');
                $tbody.empty();
                var body = "";

                if (response.data.length > 0) {
                    $.each(response.data, function (key, val) {
                        var remove_btn = '<span class="glyphicon glyphicon-trash del-attachment-btn marl" data-target="#modal" item-id="' + val.attachment_id + '" title="Delete attachment"></span>';
                        var download_btn = '<a style="color:black;" href="' + val.path + '"><span class="glyphicon glyphicon-download-alt"></span></a>';
                        body += '<tr class="' + val.attachment_id + '">' +
                            '<td>' + val.name +
                            '</td><td>' + val.date +
                            '</td><td>' + val.user +
                            '</td><td>' + download_btn +
                            '</td><td>' + remove_btn +
                            '</td></tr>';
                    });
                    $thead.append('<tr><th>Name</th><th>Date</th><th>Added by</th><th colspan="2">Options</th></tr>');
                    $tbody.append(body);
                } else {
                    $tbody.append('<p>This record has no attachments</p>');
                }
            });
        },
        close_all_attachments: function () {
            $('.modal-backdrop.all-attachments').fadeOut();
            $('.attachment-container').fadeOut(500, function () {
                $('.attachment-content').show();
                $('.attachment-select-form')[0].reset();
                $('.alert').addClass('hidden');
            });
            $('.attachment-all-container').fadeOut(500, function () {
                $('.attachment-all-content').show();
            });
        },
        delete_attachment: function (id) {
            $.ajax({
                url: helper.baseUrl + 'records/delete_attachment',
                type: "POST",
                dataType: "JSON",
                data: {
                    attachment_id: id
                }
            }).done(function (response) {
                if (response.success) {
                    record.attachment_panel.close_all_attachments();
                    record.attachment_panel.show_all_attachments();
                    record.attachment_panel.load_panel();
                    flashalert.success("Attachment was deleted");
                }
                ;
            });
        },
        save_attachment: function (name, type, path) {
            $.ajax({
                url: helper.baseUrl + 'records/save_attachment',
                type: "POST",
                dataType: "JSON",
                data: {
                    name: name,
                    type: type,
                    path: path,
                    urn: record.urn
                }
            }).done(function (response) {
                if (response.success) {
                    record.attachment_panel.load_panel(response.attachment_id);
                    flashalert.success("Attachment was saved");
                }
                else {
                    flashalert.danger("ERROR: The attachment was NOT saved");
                }
            });
        }
    }
}

$(document).on('click', '.nav-btn', function (e) {
    e.preventDefault();
    //modal.confirm_move($(this).attr('href'));
    flashalert.danger("You must update the record first");
})
/* ==========================================================================
 MODALS ON THIS PAGE
 ========================================================================== */
var modal = {
    confirm_move: function (moveUrl) {
        var mheader = 'Are you sure?';
        var mbody = 'You have not updated the record. Do you really want to continue?';
        var mfooter = '';
        modals.load_modal(mheader, mbody, mfooter);
        modals.default_buttons();
        $('.confirm-modal').on('click', function (e) {
            window.location.href = moveUrl
            $('#modal').modal('toggle');
        });

    },
    delete_contact: function (id) {
        var mheader = 'Confirm Delete';
        var mbody = 'Are you sure you want to delete this contact?';
        var mfooter = '';
        modals.load_modal(mheader, mbody, mfooter);
        modals.default_buttons();
        $('.confirm-modal').on('click', function (e) {
            record.contact_panel.remove(id);
            $('#modal').modal('toggle');
        });
    },
    delete_additional_item: function (id) {
        var mheader = 'Confirm Delete';
        var mbody = 'Are you sure you want to delete this?';
        var mfooter = '';
        modals.load_modal(mheader, mbody, mfooter);
        modals.default_buttons();
        $('.confirm-modal').on('click', function (e) {
            record.additional_info.remove(id);
            $('#modal').modal('toggle');
        });
    },
    delete_company: function (id) {
        var mheader = 'Confirm Delete';
        var mbody = 'Are you sure you want to delete this company?';
        var mfooter = '';
        modals.load_modal(mheader, mbody, mfooter);
        modals.default_buttons();
        $('.confirm-modal').on('click', function (e) {
            record.company_panel.remove(id);
            $('#modal').modal('toggle');
        });
    },
    delete_email: function (email_id, modal) {
        var mheader = 'Confirm Delete';
        var mbody = 'Are you sure you want to delete this email?';
        var mfooter = '';
        modals.load_modal(mheader, mbody, mfooter);
        modals.default_buttons();
        $('.confirm-modal').on('click', function (e) {
            record.email_panel.remove_email(email_id, modal);
            $('#modal').modal('toggle');
        });
    },
    delete_attachment: function (attachment_id) {
        var mheader = 'Confirm Delete';
        var mbody = 'Are you sure you want to delete this attachment?';
        var mfooter = '';
        modals.load_modal(mheader, mbody, mfooter);
        modals.default_buttons();
        $('.confirm-modal').on('click', function (e) {
            record.attachment_panel.delete_attachment(attachment_id);
            $('#modal').modal('toggle');
        });
    },
    delete_survey: function (id) {
        var mheader = 'Confirm Delete';
        var mbody = 'Are you sure you want to delete this survey and all the answers?';
        var mfooter = '';
        modals.load_modal(mheader, mbody, mfooter);
        modals.default_buttons();
        $('.confirm-modal').on('click', function (e) {
            record.surveys_panel.remove(id);
            $('#modal').modal('toggle');
        });
    },
    call_player: function (url, filetype) {
        var mheader = 'Call Playback';
        var mbody = '<div id="waveform"></div><div class="controls"><button class="btn btn-primary" id="playpause"><i class="glyphicon glyphicon-pause"></i>Pause</button> <button class="btn btn-primary" id="slowplay"><i class="glyphicon glyphicon-left"></i>Slower</button> <button class="btn btn-primary" id="speedplay"><i class="glyphicon glyphicon-right"></i>Faster</button> <a target="blank" class="btn btn-info" href="' + url.replace("ogg", "mp3") + '">Download</a> <span class="pull-right" id="duration"></span> <span id="audiorate" class="hidden">1</span></div>';
        var mfooter = '<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>';
        modals.load_modal(mheader, mbody, mfooter);
        $(document).one("click", ".close-modal,.close", function () {
            wavesurfer.destroy();
            modal_body.empty();
        });

        $(document).one("click", ".close-modal,.close", function () {
            wavesurfer.destroy();
            modal_body.empty();
        });
        modal.wavesurfer(url.replace('ogg', 'mp3'));
    },
    wavesurfer: function (fileurl) {
        // Create an instance
        wavesurfer = Object.create(WaveSurfer);
        // Init & load audio file
        var options = {
            container: document.querySelector('#waveform'),
            waveColor: 'violet',
            progressColor: 'purple',
            loaderColor: 'purple',
            cursorColor: 'navy',
            audioRate: 1
        };
        if (location.search.match('scroll')) {
            options.minPxPerSec = 100;
            options.scrollParent = true;
        }
        if (location.search.match('normalize')) {
            options.normalize = true;
        }
        // Init
        wavesurfer.init(options);
        // Load audio from URL
        wavesurfer.load(fileurl);
        // Regions
        if (wavesurfer.enableDragSelection) {
            wavesurfer.enableDragSelection({
                color: 'rgba(0, 255, 0, 0.1)'
            });
        }
        ;
        // Play at once when ready
        // Won't work on iOS until you touch the page
        wavesurfer.on('ready', function () {
            wavesurfer.play();
            $('#duration').text(wavesurfer.getDuration() + 's');
        });
        // Report errors
        wavesurfer.on('error', function (err) {
            console.error(err);
        });
        // Do something when the clip is over
        wavesurfer.on('finish', function () {
            console.log('Finished playing');
        });

        $('#speedplay').click(function () {
            wavesurfer.setPlaybackRate(Number($('#audiorate').text()) + 0.2);
            $('#audiorate').text(Number($('#audiorate').text()) + 0.2);
        });
        $('#slowplay').click(function () {
            wavesurfer.setPlaybackRate(Number($('#audiorate').text()) - 0.2);
            $('#audiorate').text(Number($('#audiorate').text()) - 0.2);
        });
        $('#playpause').click(function () {
            if ($('#playpause i').hasClass('glyphicon-pause')) {
                $('#playpause').html('<i class="glyphicon glyphicon-play"></i> Play');
                wavesurfer.pause();
                console.log("Paused");
            } else {
                $('#playpause').html('<i class="glyphicon glyphicon-pause"></i> Pause');
                wavesurfer.play();
                console.log("Playing");
            }
        });
    },
    delete_history: function (history_id, modal) {
        var mheader = 'Confirm Delete';
        var mbody = 'Are you sure you want to delete this history record?';
        var mfooter = '';
        modals.load_modal(mheader, mbody, mfooter);
        modals.default_buttons();
        $('.confirm-modal').on('click', function (e) {
            record.history_panel.remove_history(history_id, modal);
            $('#modal').modal('toggle');
        });
    },
    dead_line: function ($btn) {
        var mheader = 'Confirm Dead Line';
        var mbody = '<p>You have set this record as a dead line but the history shows it has been dialed previously. There may be a telephony issue or we could be at full dialing capacity. Please try to dial again and confirm it\'s an actual dead line. If this is a B2B record please search for the company telephone number online and update the record with the correct number<p><p>Click confirm if you are sure this is a dead line otherwise click cancel</p>';
        var mfooter = '';
        modals.load_modal(mheader, mbody, mfooter);
        modals.default_buttons();
        $('.confirm-modal').on('click', function (e) {
            record.update_panel.save($btn);
            $('#modal').modal('toggle');
        });
    },
    show_calendar: function (urn) {
        var mheader = 'Confirm Dead Line';
        var mbody = '<img id="modal-loading" src="' + helper.baseUrl + 'assets/img/ajax-loader-bar.gif"/><div class="responsive-calendar" style="display:none"><div class="controls"><a data-go="prev" class="pull-left"><div class="btn btn-primary">Prev</div></a><h4><span data-head-year=""></span> <span data-head-month=""></span></h4><a data-go="next" class="pull-right"><div class="btn btn-primary">Next</div></a></div><hr/><div class="day-headers"><div class="day header">Mon</div><div class="day header">Tue</div><div class="day header">Wed</div><div class="day header">Thu</div><div class="day header">Fri</div><div class="day header">Sat</div><div class="day header">Sun</div></div><div class="days" data-group="days"></div></div';
        var mfooter = '<button class="btn btn-default close-modal pull-left" data-dismiss="modal" type="button">Close</button> <button class="btn btn-primary submit-cal pull-right" type="button">Update</button> <input class="form-control pull-right marl" style="width:130px" value="" name="postcode" id="cal-postcode" placeholder="Postcode"/> <select class="cal-range selectpicker" data-width="130px"><option value="5">5 Miles</option><option value="10" selected>10 Miles</option><option value="15">15 Miles</option><option value="20">20 Miles</option><option value="30">30 Miles</option><option value="40">40 Miles</option><option value="50">50 Miles</option><option value="100">100 Miles</option><option value="150">150 Miles</option><option value="">Any Distance</option></select>';
        modals.load_modal(mheader, mbody, mfooter);
        var d = new Date();
        var time = d.getTime();

        $('#modal').find('.cal-range').selectpicker();
        $('#modal').find('.submit-cal').on('click', function () {
            modal.configure_calendar(urn, $('#modal').find('.cal-range').val(), $('#cal-postcode').val(), true);
        });
        modal.configure_calendar(urn, 10);
    },
    configure_calendar: function (urn, distance, postcode, renew) {
        if (distance) {
            modal_header.text('Scheduled appointments within ' + distance + ' miles of ' + postcode)
        } else {
            modal_header.text('Scheduled appointments')
        }
        $.ajax({
            url: helper.baseUrl + 'calendar/get_events',
            data: {
                modal: true, urn: urn, distance: distance, postcode: postcode,
                campaigns: [record.campaign]
            },
            dataType: "JSON",
            type: "POST"
        }).done(function (response) {
            if (response.error) {
                flashalert.danger(response.msg);
            }
            if (response.postcode && distance) {
               modal_header.text('Scheduled appointments within ' + distance + ' miles of ' + response.postcode)
                $('#cal-postcode').val(response.postcode);
            }
            if (renew) {
                $('.responsive-calendar').responsiveCalendar('clearAll').responsiveCalendar('edit', response.result);
            } else {
                $('.responsive-calendar').responsiveCalendar({
                    time: response.date,
                    events: response.result
                })
            }
            $('#modal-loading').hide();
            $('.responsive-calendar').slideDown();
        });
    }

}

/* ==========================================================================
 WORKBOOKS INTEGRATION WITH THE RECORD
 ========================================================================== */
var workbooks = {

    view_workbooks_data: function (lead_id) {
        //Get workbooks data
        $.ajax({
            url: helper.baseUrl + 'workbooks/get_lead',
            dataType: "JSON",
            type: "POST",
            data: {'lead_id': lead_id}
        }).done(function (response) {
			var mheader="Workbooks Data",mbody =   '<table class="table table-striped"><tbody>'
            if (response.success) {
                var val = response.data;
                mbody +=
                    '<tr><th>Id</th><td>' + val.id + '</td></tr>' +
                    '<tr><th>Lock Version</th><td>' + val.lock_version + '</td></tr>' +
                    '<tr><th>Created At</th><td>' + val.created_at + '</td></tr>' +
                    '<tr><th>Name</th><td>' + val.name + '</td></tr>' +
                    '<tr><th>Title</th><td>' + val.title + '</td></tr>' +
                    '<tr><th>Job Title</th><td>' + val.job_title + '</td></tr>' +
                    '<tr><th>First Name</th><td>' + val.first_name + '</td></tr>' +
                    '<tr><th>Last Name</th><td>' + val.last_name + '</td></tr>' +
                    '<tr><th>Salutation</th><td>' + val.salutation + '</td></tr>' +
                    '<tr><th>Telephone</th><td>' + val.telephone + '</td></tr>' +
                    '<tr><th>Mobile</th><td>' + val.mobile + '</td></tr>' +
                    '<tr><th>Email</th><td>' + val.email + '</td></tr>' +
                    '<tr><th>Assigned To</th><td>' + val.assigned_to + '</td></tr>' +
                    '<tr><th>Organisation</th><td>' + val.organisation + '</td></tr>' +
                    '<tr><th>Industry</th><td>' + val.industry + '</td></tr>' +
                    '<tr><th>Website</th><td><a href="http://' + val.website + '" target="_blank">' + val.website + '</a></td></tr>' +
                    '<tr><th>Street Address</th><td>' + val.street_address + '</td></tr>' +
                    '<tr><th>Town/City</th><td>' + val.town_city + '</td></tr>' +
                    '<tr><th>County/State</th><td>' + val.county_state + '</td></tr>' +
                    '<tr><th>Postcode/Zipcode</th><td>' + val.postcode_zipcode + '</td></tr>' +
                    '<tr><th>Country</th><td>' + val.country + '</td></tr>' +
                    '<tr><th>No Sales Calls</th><td>' + val.no_sales_calls + '</td></tr>' +
                    '<tr><th>No Email</th><td>' + val.no_email + '</td></tr>' +
                    '<tr><th>No Post Calls</th><td>' + val.no_post_calls + '</td></tr>' +
                    '<tr><th>Source</th><td>' + val.source + '</td></tr>' +
                    '<tr><th>Rating</th><td>' + val.rating + '</td></tr>' +
                    '<tr><th>Status</th><td>' + val.status + '</td></tr>' +
                    '<tr><th>Last Contacted</th><td>' + val.last_contacted + '</td></tr>' +
                    '<tr><th>Permanent Only</th><td>' + val.permanent_only + '</td></tr>' +
                    '<tr><th>No Of Employees</th><td>' + val.no_of_employees + '</td></tr>' +
                    '<tr><th>No Of Contractors</th><td>' + val.no_of_contractors + '</td></tr>' +
                    '<tr><th>Ave. Contract Rate</th><td>' + val.ave_contract_rate + '</td></tr>' +
                    '<tr><th>How Contractors Work</th><td>' + val.how_contractors_work + '</td></tr>' +
                        //'<tr><th>Main Competitor</th><td>' + val.main_competitor + '</td></tr>' +
                    '<tr><th>Uses a PSL</th><td>' + val.uses_a_psl + '</td></tr>' +
                    '<tr><th>PSL Review Date</th><td>' + val.psl_review_date + '</td></tr>' +
                    '<tr><th>PSL Review Person</th><td>' + val.psl_review_person + '</td></tr>' +
                    '<tr><th>Year Established</th><td>' + val.year_established + '</td></tr>' +
                    '<tr><th>Annual Revenue</th><td>' + val.annual_revenue + '</td></tr>' +
                    '<tr><th>Turnover Band</th><td>' + val.turnover_band + '</td></tr>' +
                    '<tr><th>Industry Description</th><td>' + val.industry_description + '</td></tr>';
					
					mbody += '</tbody></table>'; 
            } else {
               mbody = '<p>The lead does not exist in workbooks</p>';
            }
			modals.load_modal(mheader,mbody,mfooter);
        });
    }
}


function changeContactNumberFunction() {
    var contact_id = $('.contact-phone-form').find('input[name="contact_id"]').val();
    var telephone_number = $('.contact-phone-form').find('input[name="telephone_number"]').val();
    var telephone_id = $('.contact-phone-form').find('input[name="telephone_id"]').val();
    var tps = "";
    if (telephone_number.length > 0) {
        tps = "<span class='glyphicon glyphicon-question-sign black edit-tps-btn tt pointer' item-contact-id='" + contact_id + "' item-number-id='" + telephone_id + "' item-number='" + telephone_number + "' data-toggle='tooltip' data-placement='right' title='TPS Status is unknown. Click to check it'></span>";
        $('select[name="tps"]').selectpicker('val', "");
    }
    $('.edit-tps').html(tps);
}

function changeTpsFunction() {
    var contact_id = $('.contact-phone-form').find('input[name="contact_id"]').val();
    var telephone_number = $('.contact-phone-form').find('input[name="telephone_number"]').val();
    var telephone_id = $('.contact-phone-form').find('input[name="telephone_id"]').val();
    var tps_option = $('.contact-phone-form').find('select[name="tps"]').val();
    var tps = "";
    if (telephone_number.length > 0) {
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
}

$(document).on('click', '.edit-tps-btn', function (e) {
    e.preventDefault();
    check_edit_tps();
});

function check_edit_tps() {
    var contact_id = $('.contact-phone-form').find('input[name="contact_id"]').val();
    var telephone_number = $('.contact-phone-form').find('input[name="telephone_number"]').val();
    var telephone_id = $('.contact-phone-form').find('input[name="telephone_id"]').val();
    var tps = '';

    $.ajax({
        url: helper.baseUrl + 'cron/check_tps',
        type: "POST",
        dataType: "JSON",
        data: {
            telephone_number: telephone_number,
            type: "tps",
            contact_id: contact_id
        }
    }).done(function (response) {
        flashalert.warning(response.msg);
        if (response.tps == 1) {
            tps = "<span class='glyphicon glyphicon-question-sign black edit-tps-btn tt pointer' item-contact-id='" + contact_id + "' item-number-id='" + telephone_id + "' item-number='" + telephone_number + "' data-toggle='tooltip' data-placement='right' title='TPS Status is unknown. Click to check it'></span>";
            $('.contact-phone-form').find('select[name="tps"]').selectpicker('val', 1);
            $tab.find('.edit-tps').html(tps);
        }
        else if (response.tps == 0) {
            tps = "<span class='glyphicon glyphicon-ok-sign green tt' data-toggle='tooltip' data-placement='right' title='This number is NOT TPS registerd'></span>";
            $('.contact-phone-form').find('select[name="tps"]').selectpicker('val', 0);
            $tab.find('.edit-tps').html(tps);
        }
    });
}


