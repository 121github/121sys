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

        $('.daterange').daterangepicker({
                opens: "left",
                singleDatePicker: true,
                showDropdowns: true,
                format: 'DD/MM/YYYY',
                startDate: moment()
            },
            function (start, end, element) {
                var $btn = this.element;
                $btn.find('.date-text').html(start.format('MMMM D'));
                $btn.closest('.filter-form').find('input[name="date"]').val(start.format('YYYY-MM-DD'));
                planner_reload();
            });
        $(document).on("click", '.daterange', function (e) {
            e.preventDefault();
        });


        var user_id = $('form').find('input[name="user"]').val();
        $('.user-filter[id="' + user_id + '"]').css("color", "green");
        $('.user-filter-name').text($('.user-filter[id="' + user_id + '"]').text());

        $(document).on("click", ".user-filter", function (e) {
            e.preventDefault();
            $icon = $(this).closest('ul').prev('button').find('span');
            //$(this).closest('ul').prev('button').text($(this).text()).prepend($icon);
            $(this).closest('form').find('input[name="user"]').val($(this).attr('id'));
            $(this).closest('ul').find('a').css("color", "black");
            $('.user-filter').css("color", "black");
            $('.user-filter-name').text($('.user-filter[id="' + $(this).attr('id') + '"]').text());
            $(this).css("color", "green");
            maps.removeDirections();
            planner_reload();
        });

        $(document).on("click", '.goup-btn', function (e) {
            e.preventDefault();
            $(this).closest('li').insertBefore($(this).closest('li').prev());
            $(this).closest('.record-planner-item').find('.route').empty();
            $('.route').empty();
            planner.fix_order_buttons();
            maps.updateRecordPlannerList();
        });

        $(document).on("click", '.godown-btn', function (e) {
            e.preventDefault();
            $(this).parents('li').insertAfter($(this).parents('li').next());
            $(this).closest('.record-planner-item').find('.route').empty();
            $('.route').empty();
            planner.fix_order_buttons();
            maps.updateRecordPlannerList();
        });


        //Generate route
        $(document).on("click", '.calc-route-btn', function (e) {
            e.preventDefault();
            var origin = $('.directions-form').find('input[name="origin"]').val();
            var destination = $('.directions-form').find('input[name="destination"]').val();
            //check the postcodes are valid first
            $.ajax({
                url: helper.baseUrl + 'ajax/validate_postcode',
                data: {
                    origin: origin,
                    destination: destination
                },
                dataType: 'JSON',
                type: 'POST'
            }).done(function (response) {
                //if postcode is valid
                if (response.success) {
                    maps.calcRoute();
                } else {
                    flashalert.danger(response.msg);
                }
            });
        });


        $(document).on('click', '.remove-from-planner,.save-planner', function () {
            planner.reload_table();
        });
        $('.filter-form').find('input[name="date"]').change(function () {
            console.log("pooP");
        });
        $(document).on('click', '.expand-planner', function (e) {
            e.preventDefault();
            $this = $(this);
            $collapse = $this.closest('.record-planner-item').find('.collapse').collapse('show');
            $this.closest('.record-planner-item').find('.expand-planner span').removeClass('fa-plus').addClass('fa-minus');
            $this.removeClass('expand-planner').addClass('collapse-planner');
        });
        $(document).on('click', '.collapse-planner', function (e) {
            e.preventDefault();
            $this = $(this);
            $collapse = $this.closest('.record-planner-item').find('.collapse').collapse('hide');
            $this.closest('.record-planner-item').find('.collapse-planner span').removeClass('fa-minus').addClass('fa-plus');
            $this.removeClass('collapse-planner').addClass('expand-planner');
        });
        $(document).on('click', '.planner-travel-mode', function (e) {
            $('.map-form').find('input[name="travel-mode"]').val($(this).attr('item-mode'));
            maps.calcRoute();
        });

    },
    reload_table: function () {
        planner.populate_table();
    },
    fix_order_buttons: function () {
        $planner_items = $('ul .record-planner-item');
        $planner_items.find('.godown-btn,.goup-btn').prop('disabled', false);
        $planner_items.each(function (i, e) {
            if (i == 0) {
                $(this).find('.godown-btn').prop('disabled', false);
                $(this).find('.goup-btn').prop('disabled', true);
            }
            if (i == $planner_items.length - 1) {
                $(this).find('.godown-btn').prop('disabled', true);
                $(this).find('.goup-btn').prop('disabled', false);
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
                date: $('.filter-form').find('input[name="date"]').val(),
                user_id: $('.filter-form').find('input[name="user"]').val()
            }
        }).done(function (response) {
            var button_size = "btn-lg";
            if (device_type == "mobile") {
                button_size = "btn-sm";
            } else if (device_type == "tablet" || device_type == "tablet2" || device_type == "mobile2") {
                button_size = "";
            }
            var start_point = '', end_point = '', waypoints = '';
            if (response.data.length > 0) {
                var pbody = "";
                $.each(response.data, function (k, val) {
                    if (val.planner_type == "1") {
                        $('.directions-form').find('input[name="origin"]').val(val.postcode);
                    } else if (val.planner_type == "3") {
                        $('.directions-form').find('input[name="destination"]').val(val.postcode);
                    }

                    maps.items.push(val);
                    var color = 'default', route = '', travelIcon = '';
                    if (val.duration) {
                        if (val.travel_mode == "DRIVING") {
                            travelIcon = "car";
                        }

                        switch (val.travel_mode) {
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
                            '<img width="15px;" src="assets/img/icons/' + travelIcon + '.png"/>' +
                            '</span>' +
                            '<span class="small">' +
                            (Math.ceil((val.distance / 1000) / 1.2)) + ' miles - ' +
                            (toHHMMSS(val.duration)) +
                            '</span></span>';

                    }
                    if (k < 8) {
                        color = 'success';
                    }
                    var title = val.title;
                    if (device_type !== 'mobile') {
                        title += ': ' + val.postcode;
                    }
                    if (val.planner_type == 1) {
                        //$('.directions-form').find('input[name="origin"]').val(val.postcode);
                        title = "Start: " + val.postcode;
                    }
                    if (val.planner_type == 3) {
                        //$('.directions-form').find('input[name="destination"]').val(val.postcode);
                        title = "End: " + val.postcode;
                    }
                    var planner_item = "";
                    var button_style = "btn-success";
                    var planner_details = '<div class="collapse" id="collapse_' + val.record_planner_id + '">' + '<div class="col-lg-12 col-sm-12 small" style="padding:10px 20px 0px">' +
                        (val.client_ref ? '<p><b>Reference: </b>' + val.client_ref + '</p>' : '') +
                        (val.name !== "Start" && val.name !== "Destination" ? '<p><b>Company: </b>' + val.name + '</p>' : '') +
                        (val.fullname ? '<p><b>Contact: </b>' + val.fullname + '</p>' : '') +
                        (val.outcome ? '<p><b>Outcome: </b>' + val.outcome + '</p>' : '') +
                        (val.nextcall ? '<p><b>Next Action: </b>' + val.nextcall + '</p>' : '') +
                        (val.date_updated ? '<p><b>Last Updated: </b>' + val.date_updated + '</p>' : '') +
                        (val.postcode ? '<p><b>Postcode: </b>' + val.postcode + '</p>' : '') +
                        (val.record_planner_id ? '<p><b>Planner: </b>' + val.user + ' on ' + val.start + '</p>' : '') + (val.comments ? '<p><b>Last Comments: </b>' + val.comments + '</p>' : '') +
                        '</div>' +
                        '</div>' +
                        '</div></li>';

                    if (val.planner_type == 3 || val.planner_type == 1) {
                        button_style = "btn-info";
                        planner_item = '<div class="row record-planner-item exclude-waypoint" style="margin:10px 0" data-postcode="' + val.postcode + '" data-planner-id="' + val.record_planner_id + '" >' +
                            (k > 0 ? '<div style="text-align:center"><span style="font-size:30px; padding-bottom:5px" class="fa fa-arrow-down"></span>' + route + '</div>' : '') +
                            '<div class="col-lg-12 col-sm-12" style="padding:0px;margin:0px">' +
                            '<div class="btn-group" style="width:100%;display:table;">' +
                            '<button type="button" style="display:table-cell;width:10%;" class="btn ' + button_size + ' btn-info expand-planner"><span class="fa fa-plus"></span></button>' +
                            '<button type="button" data-marker=' + val.planner_id + ' style="display:table-cell; width:90%" class="btn ' + button_size + ' btn-info"><span class="pull-left">' + title + '</span>' +
                            '</button>' +
                            '</div></div>'
                    } else {
                        planner_item = '<li class="list-unstyled" style="margin:0; padding:0" ><div class="row record-planner-item" style="margin:10px 0" data-postcode="' + val.postcode + '" data-planner-id="' + val.record_planner_id + '" >' +
                            (k > 0 ? '<div style="text-align:center"><span style="font-size:30px; padding-bottom:5px; cursor:grab" class="fa fa-arrow-down drag"></span>' + route + '</div>' : '') +
                            '<div class="col-lg-12 col-sm-12" style="padding:0px;margin:0px">' +
                            '<div class="btn-group" style="width:100%;display:table;">' +
                            '<button type="button" style="display:table-cell;width:10%;" class="btn ' + button_size + ' btn-success expand-planner"><span class="fa fa-plus"></span></button>' +
                            '<button type="button" style="display:table-cell; width:60%" class="btn ' + button_size + ' btn-success planner-title" data-modal="view-record" data-marker=' + val.planner_id + ' data-urn="' + val.urn + '" ><span class="pull-left">' + title + '</span>' +
                            '</button>' +
                            '<button type="button" data-pos="' + k + '" style="display:table-cell;width:10%" ' +
                            ' class="btn ' + button_style + ' ' + button_size + ' godown-btn">' +
                            '<span class="fa fa-arrow-down"></span>' +
                            '</button>' +
                            '<button type="button" ' +
                            ' style="display:table-cell;width:10%"  class="btn btn-success ' + button_size + ' goup-btn">' +
                            '<span class="fa fa-arrow-up"></span>' +
                            '</button>' +
                            '<button type="button" ' +
                            ' style="display:table-cell;width:10%" class="btn btn-danger ' + button_size + ' remove-from-planner-confirm" data-urn="' + val.urn + '" >' +
                            '<span class="fa fa-remove"></span>' +
                            '</button>' +
                            '</div></div>'
                    }
                    if (val.planner_type == 1) {
                        start_point += planner_item + planner_details;
                    } else if (val.planner_type == 3) {
                        end_point += planner_item + planner_details;
                    } else {
                        waypoints += planner_item + planner_details;
                    }
                });

                pbody = start_point + '<ul style="margin:0; padding:0">' + waypoints + '</ul>' + end_point;

            }
            else {
                var pbody = '<div>No waypoints have been added on this day!</div>' +
                    '<div>' +
                    '<span class="glyphicon glyphicon-question-sign"></span> ' +
                    '<a href="' + helper.baseUrl + 'records/view"> You can add records to the planner to create waypoints </a>' +
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
        //Show the branches if those exist
        planner.showBranches();
    },

    showBranches: function () {
        maps.branches = [];
        var user_id = $('form').find('input[name="user"]').val();
        $.ajax({
            url: helper.baseUrl + "planner/showBranches",
            type: "POST",
            dataType: "JSON",
            data: {'user_id': $('form').find('input[name="user"]').val()}
        }).done(function (response) {
            $.each(response.data, function (key, val) {
                maps.branches.push(val);
            });
            //console.log(maps.branches);
            maps.showBranches();
        });
    }
}