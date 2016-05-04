// JavaScript Document
var record_update = {
    init: function (urn) {
		var $record_panel = $(document);
		this.urn = urn;
		console.log("init record update");
		    $record_panel.on('click', '#update-record', function (e) {
			e.preventDefault();
			if(record_update.check_record_update()){
			record_update.save($(this));	
			}
			});

        $record_panel.on('click', '#reset-record', function (e) {
            e.preventDefault();
			record_update.reset_record();
        });
        $record_panel.on('click', '#unpark-record,#record-unpark', function (e) {
            e.preventDefault();
			record_update.unpark_record();
        });
        $record_panel.on('click', '#favorite-btn', function (e) {record_update.set_favorite($(this));
        });
        $record_panel.on('click', '#urgent-btn', function (e) {record_update.set_urgent($(this));
        });
        $record_panel.on('click', '.close-xfer', function (e) {
            e.preventDefault();record_update.close_cross_transfer();
        });
        $record_panel.on('click', '.set-xfer', function (e) {
            e.preventDefault();
            var xfer = $('select[name="campaign"]').find('option:selected').text()
            $('#record-update-form').append($('<input name="xfer_campaign" type="hidden"/>').val($('select[name="campaign"]').val()));
            $('div.outcomepicker').find('.filter-option').text('Cross Transer: ' + xfer);record_update.close_cross_transfer();
        });
        var old_outcome = $('#outcomes option:selected').val();
        var current_outcome = old_outcome;
        $record_panel.on('change', '#outcomes', function (e) {
			record_update.enable_outcome_reasons($(this).val());
            e.preventDefault();
            $val = $(this).val();
            if ($val == 71) {
				record_update.cross_transfer();
            } else {
                $('input[name="xfer_campaign"]').remove();
            }
            $delay = $record_panel.find("#outcomes option[value='" + $val + "']").attr('delay');
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
                    format: 'DD/MM/YYYY HH:mm',
					sideBySide:true
                });
                $('#nextcall').animate({backgroundColor: "#99FF99"}, 500).delay(300).animate({backgroundColor: "#FFFFFF"}, 500);
                //$('#nextcall').data("DateTimePicker").setDate(timestamp_to_uk(nextcall,true));
            }
            //If the previous outcome had delay, set the date to the old_nextcall
            else if ($('#outcomes').find("option[value='" + current_outcome + "']").attr('delay')) {
                nextcall = old_nextcall;
                $('#nextcall').val(nextcall);
                $('#nextcall').datetimepicker({
                    format: 'DD/MM/YYYY HH:mm',
					sideBySide:true
                });
                $('#nextcall').animate({backgroundColor: "#99FF99"}, 500).delay(300).animate({backgroundColor: "#FFFFFF"}, 500);
            }

            //Set the new outcome
            current_outcome = $('#outcomes option:selected').val();

            var outcome = $('#outcomes option:selected').val();
            var new_nextcall = $('input[name="nextcall"]').val();
            var comments = $('textarea[name="comments"]').val();record_update.disabled_btn(old_outcome, outcome, old_nextcall, new_nextcall, old_comments, comments);
        });

        var old_nextcall = $('input[name="nextcall"]').val();
        var datetimepicker = $('.datetime');
        datetimepicker.off("dp.hide");
        datetimepicker.on("dp.hide", function (e) {
			if($('#outcomes').length>0){
            var new_nextcall = $('input[name="nextcall"]').val();
            var outcome = $('#outcomes option:selected').val();
            var comments = $('textarea[name="comments"]').val();record_update.disabled_btn(old_outcome, outcome, old_nextcall, new_nextcall, old_comments, comments);
			}
        });

        var old_comments = $('textarea[name="comments"]').val();
        $('textarea[name="comments"]').bind('input propertychange', function () {
            var new_nextcall = $('input[name="nextcall"]').val();
            var outcome = $('#outcomes option:selected').val();
            var comments = $('textarea[name="comments"]').val();record_update.disabled_btn(old_outcome, outcome, old_nextcall, new_nextcall, old_comments, comments);
        });
	},
	get_urn:function(){
		if(typeof record !== "undefined"){
		return record.urn;	
		} else {
		return modals.urn;		
		}
	},
	 disabled_btn: function (old_outcome, outcome, old_nextcall, nextcall, old_comments, comments) {
            if (((outcome.length != 0) && (outcome != old_outcome)) || ((nextcall.length != 0) && (nextcall != old_nextcall)) || ((comments.length != 0) && (comments != old_comments))) {
                $('#update-record').prop('disabled', false);
            }
            else {
                $('#update-record').prop('disabled', true);
            }
        },
		check_record_update:function(){
            if ($('[name="call_direction"]').length > 0 && !$('[name="call_direction"]').is(':checked')) {
                flashalert.danger("You must set a call direction");
				return false;
            } else if ($('#outcomes').val().length > 0||$('[name="progress_id"]').val()>0) {
                if ($('#outcomes').val() == "4" && $('#history-panel').find('tbody tr').length > 0) {
                record_update.dead_line($(this));
                } else { 
				return true;
                }
            } else {
                flashalert.danger("You must select a call outcome first");
				return false;
            }
		},
		dead_line: function ($btn) {
        var mheader = 'Confirm Dead Line';
        var mbody = '<p>You have set this record as a dead line but the history shows it has been dialed previously. There may be a telephony issue or we could be at full dialing capacity. Please try to dial again and confirm it\'s an actual dead line. If this is a B2B record please search for the company telephone number online and update the record with the correct number<p><p>Click confirm if you are sure this is a dead line otherwise click cancel</p>';
        var mfooter = '';
        modals.load_modal(mheader, mbody, mfooter);
        modals.default_buttons();$modal.find('.confirm-modal').on('click', function (e) {
            update_panel.save($btn);
            $modal.modal('toggle');
        });
    },
        enable_outcome_reasons: function (outcome) {
			console.log("Modify outcome reasons");
            //unset the reason if the call outcome is changed
            $('#outcome-reasons').val('');
            if ($('#outcome-reasons option[outcome-id="' + outcome + '"]').length > 0) {
                //show if any reasons are linked to the selected outcome and hide any other
                $('#outcome-reasons option').each(function () {
                    if ($(this).attr('outcome-id') == outcome && $(this).attr('value') !== "na" || $(this).attr('value') == "0") {
                        $(this).removeClass('option-hidden');
                    } else {

                        $(this).addClass('option-hidden');
                    }
                });
                //enable the reason dropdown if required
                $('#outcome-reasons').prop('disabled', false);
            } else {
                //if there are no reasons added we just show "na" and leave it disabled
                $('#outcome-reasons[value="na"]').removeClass('option-hidden');
                $('#outcome-reasons').val('na');
                $('#outcome-reasons').prop('disabled', true);
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
			var urn = record_update.get_urn();
            $.ajax({
                url: helper.baseUrl + 'records/update',
                type: "POST",
                dataType: "JSON",
                data: $btn.closest('form').serialize(),
                beforeSend: function () {
                    $('#update-record').prop('disabled',true);
                }
            }).fail(function(){
				 $('#update-record').prop('disabled',false);
				flashalert.danger("There was an error updating the record");
			}).done(function (response) {
                if (response.success) {
					$('.refresh-overview-data').trigger('click');
                    $('#last-updated').text('Last Updated: Just Now');
					if(typeof record !== "undefined"){
                    record.history_panel.load_panel();
					record.ownership_panel.load_panel();
					}
                    check_session();
                    $('#container-fluid').off('click', '.nav-btn');
                    flashalert.success(response.msg);
                    if (response.email_trigger) {
                        $.ajax({
                            url: helper.baseUrl + 'email/trigger_email',
                            type: "POST",
                            data: {urn:urn}
                        });
                    }
					 if (response.sms_trigger) {
                        $.ajax({
                            url: helper.baseUrl + 'sms/trigger_sms',
                            type: "POST",
                            data: {urn: urn}
                        });
                    }
                    if (response.function_triggers) {
                        $.each(response.function_triggers, function (i, path) {
                            $.ajax({
                                url: helper.baseUrl + path + '/' + urn,
                                type: "POST",
                                dataType: "JSON",
                                data: {urn: urn}
                            }).done(function (function_trigger_response) {
                                if(function_trigger_response.length>0){
									$.each(function_trigger_response.js_functions,function(i,f){
									eval(f+'()');
									});
								}
                            });
                        });
                    }
                    //Disable leave page notice
                    $(window).off('beforeunload');
                    $('textarea[name="comments"]').val('');
                    $('#update-record').prop('disabled', true);
					if($('#update-record-tab').length){
					modals.update_record(urn);	
					}
                } else {
                    flashalert.warning(response.msg);
                }
                $btn.show();
                $('#update-loader').remove();
            });
        },
        set_favorite: function ($btn) {
			var urn = record_update.get_urn();
            $.ajax({
                url: helper.baseUrl + 'records/set_favorites',
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: urn,
                    action: $btn.attr('action')
                }
            }).done(function (response) {
                if (response.added) {
                    $btn.html('<span class="glyphicon glyphicon-star"></span> Remove from favourites').attr("action", "remove").children('span').css('color', 'yellow');
                } else {
                    $btn.html('<span class="glyphicon glyphicon-star-empty"></span> Add to favourites').attr("action", "add").children('span').css('color', 'black');
                }
               // flashalert.success(response.msg);
            });
        },
        set_urgent: function ($btn) {
			var urn = record_update.get_urn();
            $.ajax({
                url: helper.baseUrl + 'records/set_urgent',
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: urn,
                    action: $btn.attr('action')
                }
            }).done(function (response) {
                if (response.added) {
                    $btn.html('<span class="glyphicon glyphicon-flag red"></span> Unflag as urgent').attr("action", "remove");
                    //$('#progress').selectpicker('val','1').selectpicker('render');
                } else {
                    $btn.html('<span class="glyphicon glyphicon-flag"></span> Flag as urgent').attr("action", "add");
                }
                //flashalert.success(response.msg);
            });
        },
        unpark_record: function () {
			var urn = record_update.get_urn();
            $.ajax({
                url: helper.baseUrl + 'records/unpark_record',
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: urn
                }
            }).done(function (response) {
                if (response.success) {
                    //flashalert.success(response.msg);
                    if($('#update-record-tab').length){
					modals.update_record(urn);
					} else {
                    location.reload();
					}
                } else {
                    flashalert.danger(response.msg);
                }
            });
        },
        reset_record: function () {
			var urn = record_update.get_urn();
            $.ajax({
                url: helper.baseUrl + 'records/reset_record',
                type: "POST",
                dataType: "JSON",
                data: {
                    urn: urn
                }
            }).done(function (response) {
                if (response.success) {
                    //flashalert.success(response.msg);
                  	if($('#update-record-tab').length){
					modals.update_record(urn);
					} else {
                    location.reload();
					}
                } else {
                    flashalert.danger(response.msg);

                }
            });
        }
}
record_update.init();