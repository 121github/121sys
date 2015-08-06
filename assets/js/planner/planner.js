$(document).ready(function () {
    maps.initialize("planner");
    planner.init();
});

//allow the map.js file to call a generic function to redraw the table specified here (appointment)
function planner_reload() {
    //reload the planner
    planner.populate_table();
}
function map_table_reload() {
		//do nothing - the planner does not need to change when the map does.
}
var planner = {
    init: function () {
        planner.reload_table();
		$(document).on('click','.remove-from-planner,.save-planner',function(){
			 planner.reload_table();
		});		
	$(document).on('click','.expand-planner', function(e) {
    e.preventDefault();
		$this =$(this);
    $collapse = $this.closest('.record-planner-item').find('.collapse').collapse('show');
	$this.closest('.record-planner-item').find('.expand-planner span').removeClass('fa-plus').addClass('fa-minus');
	$this.removeClass('expand-planner').addClass('collapse-planner');
});
$(document).on('click','.collapse-planner', function(e) {
    e.preventDefault();
	$this =$(this);
    $collapse = $this.closest('.record-planner-item').find('.collapse').collapse('hide');
	$this.closest('.record-planner-item').find('.collapse-planner span').removeClass('fa-minus').addClass('fa-plus');
	$this.removeClass('collapse-planner').addClass('expand-planner');
});
$(document).on('click','.planner-travel-mode',function(e){
	$('.map-form').find('input[name="travel-mode"]').val($(this).attr('item-mode'));
	 maps.calcRoute();
});

    },
    reload_table: function () {
        planner.populate_table();
    },
	fix_order_buttons: function(){
		$planner_items = $('ul .record-planner-item');
		$planner_items.find('.godown-btn,.goup-btn').prop('disabled',false);
		$planner_items.each(function(i,e){
			if(i==0){
				$(this).find('.godown-btn').prop('disabled',false);
				$(this).find('.goup-btn').prop('disabled',true);
			}
			if(i==$planner_items.length-1){
				$(this).find('.godown-btn').prop('disabled',true);
				$(this).find('.goup-btn').prop('disabled',false);
			}
		});
	},
    populate_table: function () {

        /*******************************************************************/
        /********************** GET and PRINT THE RECORDS *************/
        /*******************************************************************/
        maps.items = [];
        $('.dt_info').hide();

        $.ajax({
            url: helper.baseUrl + "planner/planner_data",
            type: "POST",
            dataType: "JSON",
            data: {
                bounds: maps.getBounds(),
                map: $('#map-view-toggle').prop('checked'),
                date: $('.filter-form').find('input[name="date"]').val()
            }
        }).done(function (response) {
					var button_size = "btn-lg";
					if(device_type=="mobile"){
					 	button_size = "btn-sm";
					} else if(device_type=="tablet"||device_type=="tablet2"||device_type=="mobile2"){
						button_size = "";
					}
            var body = '';
            if (response.data.length > 0) {
				var pbody = "";
                $.each(response.data, function (k, val) {
					if(val.planner_type=="1"){
						$('.directions-form').find('input[name="origin"]').val(val.postcode);
					} else if(val.planner_type=="3"){
					$('.directions-form').find('input[name="destination"]').val(val.postcode);
					}
					
                    maps.items.push(val);
                    var color = 'default';
                    var route = '';
                    var travelIcon = "";
                    if (val.duration) {

                        if (val.travel_mode == "DRIVING") {
                            travelIcon = "car";
                        }

                        switch(val.travel_mode) {
                            case "DRIVING":
                                travelIcon = "car";
                                break;
                            case "BICYCLING":
                                travelIcon = "cycle";
                                break;
                            case "WALKING":
                                travelIcon = "walking";
                                break;
                            default:
                                travelIcon = "";
                        }
                        route =
                            '<span class="route" style=" vertical-align:top"><span style="opacity: 0.4; filter: alpha(opacity=40); padding:5px 5px 0">' +
                                    '<img width="15px;" src="assets/img/icons/'+travelIcon+'.png"/>' +
                                '</span>' +
                                '<span class="small">' +
                                    (Math.ceil((val.distance/1000)/1.2)) + ' miles - ' +
                                    (toHHMMSS(val.duration)) +
                            '</span></span>';

                    }
                    if (k < 8) {
                        color = 'success';
                    }
					var title = val.title;
					if(device_type !== 'mobile'){
					title += ': '+val.postcode;
					}
					if(val.planner_type==1){
					//$('.directions-form').find('input[name="origin"]').val(val.postcode);	
					title = "Start: "+val.postcode;	
					}
					if(val.planner_type==3){
					//$('.directions-form').find('input[name="destination"]').val(val.postcode);
					title = "End: "+val.postcode;	
					}
					var planner_item="";
					var button_style = "btn-success";	
					var planner_details = '<div class="collapse" id="collapse_'+val.record_planner_id+'">' + '<div class="col-lg-12 col-sm-12 small" style="padding:10px 20px 0px">' +
													(val.client_ref ?	'<p><b>Reference: </b>' + val.client_ref + '</p>' : '') +	
										(val.name!=="na" ?	'<p><b>Company: </b>' + val.name + '</p>' : '') +	
											(val.fullname ?	'<p><b>Contact: </b>' + val.fullname + '</p>' : '') +	
											(val.outcome ?	'<p><b>Outcome: </b>' + val.outcome + '</p>' : '') +	
	(val.nextcall ?	'<p><b>Next Action: </b>' + val.nextcall + '</p>' : '') +	
      	(val.date_updated ?	'<p><b>Last Updated: </b>' + val.date_updated + '</p>' : '') +	
			(val.postcode ?	'<p><b>Postcode: </b>' + val.postcode + '</p>' : '') +	
				(val.record_planner_id ?	'<p><b>Planner: </b>' + val.user + ' on ' + val.start + '</p>' : '') +	             	(val.comments ?	'<p><b>Last Comments: </b>' + val.comments + '</p>' : '') +	                  
                                                '</div>' +
												  '</div>' +
                                            '</div></li>';
											
					if(val.planner_type==3||val.planner_type==1){
					button_style = "btn-info";	
					planner_item = '<div class="row record-planner-item exclude-waypoint" style="margin:10px 0" data-postcode="'+val.postcode+'" data-planner-id="'+val.record_planner_id+'" >' +
   (k>0?'<div style="text-align:center"><span style="font-size:30px; padding-bottom:5px" class="fa fa-arrow-down"></span>'+ route+'</div>':'')+
   '<div class="col-lg-12 col-sm-12" style="padding:0px;margin:0px">' +
'<div class="btn-group" style="width:100%;display:table;">'+
  '<button type="button" style="display:table-cell;width:10%;" class="btn '+button_size+' btn-info expand-planner"><span class="fa fa-plus"></span></button>'+
  '<button type="button" style="display:table-cell; width:90%" class="btn '+button_size+' btn-info"><span class="pull-left">'+title+'</span>'+
  '</button>'+
    '</div></div>'
					} else {
					planner_item = '<li class="list-unstyled" ><div class="row record-planner-item" style="margin:10px 0" data-postcode="'+val.postcode+'" data-planner-id="'+val.record_planner_id+'" >' +
  (k>0?'<div style="text-align:center"><span style="font-size:30px; padding-bottom:5px; cursor:grab" class="fa fa-arrow-down drag"></span>'+ route+'</div>':'')+
   '<div class="col-lg-12 col-sm-12" style="padding:0px;margin:0px">' +
'<div class="btn-group" style="width:100%;display:table;">'+
  '<button type="button" style="display:table-cell;width:10%;" class="btn '+button_size+' btn-success expand-planner"><span class="fa fa-plus"></span></button>'+
  '<button type="button" style="display:table-cell; width:60%" class="btn '+button_size+' btn-success planner-title" data-modal="view-record" data-urn="'+val.urn+'" ><span class="pull-left">'+title+'</span>'+
  '</button>'+
    '<button type="button" data-pos="'+k+'" style="display:table-cell;width:10%" '+
	' class="btn '+button_style+' '+button_size+' godown-btn">'+
    '<span class="fa fa-arrow-down"></span>'+
  '</button>'+
    '<button type="button" '+
	' style="display:table-cell;width:10%"  class="btn btn-success '+button_size+' goup-btn">'+
    '<span class="fa fa-arrow-up"></span>'+
  '</button>'+
      '<button type="button" '+
	' style="display:table-cell;width:10%" class="btn btn-danger '+button_size+' remove-from-planner-confirm" data-urn="'+val.urn+'" >'+
    '<span class="fa fa-remove"></span>'+
  '</button>'+
    '</div></div>'	
					}
					if(val.planner_type==1){
						 pbody += planner_item + planner_details + '<ul id="draggable-items" class="list-unstyled ui-sortable">';
					} else if(val.planner_type==3){
						  pbody += '</ul>' + planner_item + planner_details;
					} else {
						pbody += planner_item + planner_details;
					}			
                });
            }
            else {
                var pbody = '<div>No waypoints have been added on this day!</div>' +
                       '<div>' +
                            '<span class="glyphicon glyphicon-question-sign"></span> ' +
                                '<a href="'+helper.baseUrl+'records/view"> You can add records to the planner to create waypoints </a>' +
                            '' +
                        '</div>';
            }
            $('#draggablePanelList').html(pbody);
			planner.fix_order_buttons();
            maps.showItems();
 			$('#draggable-items').sortable({
                    // Only make the .panel-heading child elements support dragging.
                    // Omit this to make then entire <li>...</li> draggable.
                    handle: '.drag',
                    update: function () {
						$('#draggablePanelList').find('.route').empty();
                        maps.updateRecordPlannerList();
                    }
                });

        });
    }
}