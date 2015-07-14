$(document).ready(function () {
    maps.initialize("planner");
    planner.init();
});

//allow the map.js file to call a generic function to redraw the table specified here (appointment)
function map_table_reload() {
    //planner.getRecords();
    planner.populate_table();
}

var planner = {
    init: function () {
        planner.reload_table();
		$(document).on('click','.remove-from-planner,.save-planner',function(){
			 planner.reload_table();
		});
		
    },
    reload_table: function () {
        planner.populate_table();
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

            var body = '';
            if (response.data.length > 0) {
                $.each(response.data, function (k, val) {
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
                            '<div class="route-header col-lg-12" style="text-align: right;">' +
                                '<span style="opacity: 0.4; filter: alpha(opacity=40); margin-right: 5px;" class="change-directions-btn DRIVING pointer" item-mode="DRIVING">' +
                                    '<img width="15px;" src="assets/img/icons/'+travelIcon+'.png"/>' +
                                '</span>' +
                                '<span>' +
                                    (Math.ceil((val.distance/1000)/1.2)) + ' miles - ' +
                                    (toHHMMSS(val.duration)) +
                                '</span>' +
                            '</div>';
                    }
                    if (k < 8) {
                        color = 'success';
                    }

                    body +=
                        '<div class="row record-planner-item">' +
                            '<div class="col-lg-1 col-sm-1">' +
                                '<span class="glyphicon glyphicon-plus pointer" data-toggle="collapse" data-parent="#accordion" data-target="#collapse_'+val.record_planner_id+'" ></span>' +
                            '</div>' +
                            '<div class="col-lg-10 col-sm-10">' +
                                '<li class="panel panel-'+color+'" postcode="'+val.postcode+'" record-planner-id="'+val.record_planner_id+'">' +
                                    '<div class="panel-heading record-planner-heading pointer" data-modal="view-record" data-urn="'+val.urn+'" record-planner-id="'+val.record_planner_id+'">' +
                                        '<div class="row" style="color:#333">' +
                                            '<div class="col-lg-9">' +
                                                '<span>' + val.name + '</span>' +
                                            '</div>' +
                                            '<div class="col-lg-3" style="text-align: right">' +
                                                '<span>' + val.postcode + '</span>' +
                                            '</div>' +
                                            route +
                                        '</div>' +
                                    '</div>' +
                                    '<div class="panel-body collapse" id="collapse_'+val.record_planner_id+'">' +
                                        '<div class="panel-body">' +
                                            '<div class="row">' +
                                                '<div class="col-lg-12 col-sm-12">' +
                                                    '<p><b>Company: </b>' + (val.name ? val.name : '') + '</p>' +
                                                    '<p><b>Contact: </b>' + (val.fullname ? val.fullname : '') + '</p>' +
                                                    '<p><b>Outcome: </b>' + (val.outcome ? val.outcome : '') + '</p>' +
                                                    '<p><b>Next Call: </b>' + (val.nextcall ? val.nextcall : '') + '</p>' +
                                                    '<p><b>Last Updated: </b>' + (val.date_updated ? val.date_updated : '') + '</p>' +
                                                    '<p><b>Postcode: </b>' + (val.postcode ? (val.postcode + '(' + (val.lat ? val.lat : '-') + ',' + (val.lng ? val.lng : '-') + ')') : '') + '</p>' +
                                                    '<p><b>Website: </b><a target="_blank" href="' + val.website + '">' + val.website + '</a></p>' +
                                                    '<p><b>Planner: </b>' + (val.record_planner_id?(val.user + ' on ' + val.start):'') + '</p>' +
                                                '</div>' +
                                            '</div>' +

                                        '</div>' +
                                    '</div>' +
                                '</li>' +
                            '</div>' +
                            '<div class="col-lg-1 col-sm-1">' +
                                '<span class="glyphicon glyphicon-arrow-up green pointer goup-btn"></span>' +
                                '<span class="glyphicon glyphicon-arrow-down red pointer godown-btn"></span>' +
                            '</div>' +
                        '</div>';
                });
            }
            else {
                body = '<div>No waypoints have been added on this day!</div>' +
                       '<div>' +
                            '<span class="glyphicon glyphicon-question-sign"></span> ' +
                                '<a href="'+helper.baseUrl+'records/view"> You can add records to the planner to create waypoints </a>' +
                            '' +
                        '</div>';
            }
            $('#draggablePanelList').html(body);
            maps.showItems();


        });
    }
}