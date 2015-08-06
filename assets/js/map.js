// JavaScript Document
/*******************************************************************/
/********************** MAP ****************************************/
/*******************************************************************/


var maps = {
    initialize: function (map_type) {
        this.map_type = map_type;
        this.directionsDisplay = new google.maps.DirectionsRenderer();
        this.directionsService = new google.maps.DirectionsService();
        this.map;
        this.current_postcode = false;
        this.markers = [];
        this.markerLocation;
        this.bounds = null;
        this.temp_bounds = null;
        this.items = [];
        this.table;
        this.infowindow = new google.maps.InfoWindow();
        this.geocoder = new google.maps.Geocoder();
        this.uk_lat = 54.9830568815027;
        this.uk_lng = -4.331033059374933;
        this.default_zoom = 6;
        this.panelList = $('#draggablePanelList');
        this.colour_by = null;

/*
        if (getCookie('lat') && getCookie('lng')) {
            this.lat = getCookie('lat');
            this.lng = getCookie('lng');
            var default_zoom = 12;
        } else {
            this.lat = maps.uk_lat;
            this.lng = maps.uk_lng;
            var default_zoom = maps.default_zoom;
        }
		*/
		 this.lat = maps.uk_lat;
            this.lng = maps.uk_lng;
            var default_zoom = maps.default_zoom;
        this.myLatlng = new google.maps.LatLng(maps.lat, maps.lng);

        this.mapOptions = {
            zoom: default_zoom,
            center: maps.myLatlng,
            mapTypeControl: true,
            mapTypeControlOptions: {
                style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                position: google.maps.ControlPosition.LEFT_BOTTOM
            },
            zoomControl: true,
            zoomControlOptions: {
                style: google.maps.ZoomControlStyle.SMALL,
                position: google.maps.ControlPosition.LEFT_TOP
            },
            panControl: false,
            scaleControl: true,
            streetViewControl: true,
            streetViewControlOptions: {
                position: google.maps.ControlPosition.LEFT_TOP
            }
        };

        map = new google.maps.Map(document.getElementById('map-canvas'), maps.mapOptions);

        maps.markerLocation = new google.maps.Marker({
            map: map,
            position: maps.myLatlng
        });

        $('.map-form').on("keyup keypress", function (e) {
            var code = e.keyCode || e.which;
            if (code == 13) {
                e.preventDefault();
                return false;
            }
        });

        $('#optimized').bootstrapToggle({
            onstyle: 'success',
        });

        $('#map-view-toggle').bootstrapToggle({
            onstyle: 'success',
            size: 'mini',
        }).show().bootstrapToggle('off');

        $('#map-view-toggle').change(function () {
            maps.showMap($(this));
            map_table_reload();					
        });	
        $(document).on('click', '.get-location-btn', function () {
            maps.removeDirections();
            maps.codeAddress(12);
            //Wait until the map is loaded
            setTimeout(function () {
                map_table_reload();
            }, 2000);
        });
		$(document).on('click','#show-uk',function(){
			$('.map-form').find('input[name="postcode"]').val('');
			$('.get-location-btn').trigger('click');
		});
        $('[data-toggle="tooltip"]').tooltip();
        $(document).on('click', '.appointment-btn', function () {
            $('.map-form').find('input[name="destination"]').val($(this).attr('item-postcode'));
            var destination = $('.map-form').find('input[name="destination"]').val();
            var origin = maps.markerLocation.getPosition();
            maps.getDirections(origin, destination);
        });

        $(document).on('click', '.record-planner-btn', function () {
            $('.map-form').find('input[name="destination"]').val($(this).attr('item-postcode'));
            var destination = $('.map-form').find('input[name="destination"]').val();
            var origin = maps.markerLocation.getPosition();
            maps.getDirections(origin, destination);
        });

        $(document).on('click', '.change-directions-btn', function () {
            $('.map-form').find('input[name="travel-mode"]').val($(this).attr('item-mode'));
            var destination = $('.map-form').find('input[name="destination"]').val();
            var origin = maps.markerLocation.getPosition();
            maps.getDirections(origin, destination);
        });

        $(document).on('click', '.close-directions-btn', function () {
            maps.removeDirections();
        });

        $(document).on('click', '.get-current-location-btn', function () {
            maps.removeDirections();
            var current_postcode = getCookie("current_postcode");
            if (current_postcode.length == 0) {
                getLocation();
                current_postcode = getCookie("current_postcode");
            }
            $('.map-form').find('input[name="postcode"]').val(current_postcode);
            maps.codeAddress(12);
        });

        $(document).on('click', '.get-origin-location-btn', function () {
            var current_postcode = getCookie("current_postcode");
            if (current_postcode.length == 0) {
                getLocation();
                current_postcode = getCookie("current_postcode");
            }
            $('.directions-form').find('input[name="origin"]').val(current_postcode);
        });

        $(document).on('click', '.get-destination-location-btn', function () {
            var current_postcode = getCookie("current_postcode");
            if (current_postcode.length == 0) {
                getLocation();
                current_postcode = getCookie("current_postcode");
            }
            $('.directions-form').find('input[name="destination"]').val(current_postcode);
        });

        $(document).on('click', '.show-directionsPanel-btn', function () {
            maps.showDirections();
        });

        //Planner form
        $(document).on('click', '.planner-btn', function () {
            var urn = $(this).attr('item-urn');
            var planner_date = $(this).attr('item-planner-date');
            var today_date = new Date();
            today_date = today_date.getUTCDate() + "/" + (today_date.getUTCMonth() + 1) + "/" + today_date.getFullYear();
            $('#bodyContent_' + urn).hide();
            $('#formContent_' + urn).show();

            $('.date').datetimepicker({
                format: 'DD/MM/YYYY',
                pickTime: false,
                defaultDate: (planner_date.length > 0 ? planner_date : today_date)
            });
        });

        //Cancel planner form
        $(document).on('click', '.cancel-planner-btn', function () {
            var urn = $(this).attr('item-urn');

            $('#bodyContent_' + urn).show();
            $('#formContent_' + urn).hide();
        });

        //Save planner
        $(document).on('click', '.save-planner-btn', function () {
            maps.savePlanner($(this));
        });


        $('.map-form').find('input[name="travel-mode"]').val("DRIVING");

        var bounds_changer = debounce(function () {
            map_table_reload();
        }, 500);

        google.maps.event.addListener(map, 'zoom_changed', function () {
            google.maps.event.addListenerOnce(map, 'bounds_changed', bounds_changer);
        });
        google.maps.event.addListener(map, 'dragend', function () {
            map_table_reload();
        });

        google.maps.event.addListener(map, "click", function () {
            maps.infowindow.close();
        });


        if (maps.map_type == 'planner') {
            //Date range
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
                    map_table_reload();
                });
            $(document).on("click", '.daterange', function (e) {
                e.preventDefault();
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
				if($('.directions-form').find('input[name="origin"]').val()==""||$('.directions-form').find('input[name="destination"]').val()==""){
					alert("You must set an origin and destination postcode");
				} else {
                maps.calcRoute();
				}
            });
        }
    },

    showMap: function (btn) {
        if (btn.prop('checked')) {
            if (getCookie('location_error')) {
                var location_error = '<div class="alert alert-danger" role="alert">' +
                    '<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> ' +
                    '<span class="sr-only">Error: </span>' +
                    getCookie('location_error') + ' Please contact support for assistance</div>'
                $('.container-fluid').prepend(location_error);
            }

            //Start animation in the map for the marker selected in the table
            $(document).on('mouseenter', '.data-table tbody tr', function () {
                maps.animateMarker($(this).attr('data-id'));
                $(this).css('color', 'green');
            });

            $(document).on('mouseenter', 'li .record-planner-heading', function () {
                maps.animateMarker($(this).attr('data-urn'));
                $(this).css('color', 'red');
            });

            //End animation in the map for the marker deselected in the table
            $(document).on('mouseleave', '.data-table tbody tr', function () {
                maps.removeMarkerAnimation($(this).attr('data-id'));
                $(this).css('color', 'black');
            });

            $(document).on('mouseleave', 'li .record-planner-heading', function () {
                maps.removeMarkerAnimation($(this).attr('data-urn'));
                $(this).css('color', 'white');
            });

            if (device_type == ('default')) {
                $("#table-wrapper").removeClass("col-lg-12").addClass("col-lg-6");
                $(".map-view").show();
            } else {
                $("#table-wrapper").find('table').find('tbody').hide();
                $("#table-wrapper").find('.planner-data').hide();
                $(".map-view").show();
            }
            //Reload the map
            var currCenter = map.getCenter();
            google.maps.event.trigger(map, 'resize');
            map.setCenter(currCenter);
        } else {
            $(document).off('mouseenter', '.data-table tbody tr');
            $(document).off('mouseleave', '.data-table tbody tr');
            //Start animation in the map for the marker deselected in the table
            if (device_type == ('default')) {
                $("#table-wrapper").removeClass("col-lg-6").addClass("col-lg-12");
                $(".map-view").hide();
                $('table').removeAttr('style')
            } else {
                $("#table-wrapper").find('table').find('tbody').show();
                $("#table-wrapper").find('.planner-data').show();
                $(".map-view").hide();
                maps.removeDirections();
            }
        }
    },

    calcRoute: function () {
        maps.panelList = $('#draggablePanelList');
        var record_list_route = [];
        var waypts = [];
        //Open map view
       	if (device_type=="default"&&!$('#map-view-toggle').prop('cheched')) {
            $('#map-view-toggle').bootstrapToggle('on')
            maps.showMap($('#map-view-toggle'));
        } 
        maps.removeDirections();
        $('.record-planner-item', maps.panelList).not('.exclude-waypoint').each(function (index, elem) {
            var postcode = $(elem).attr('data-postcode');
            var record_planner_id = $(elem).attr('data-planner-id');
            if (index < 8) {
                waypts.push({
                    //location: new google.maps.LatLng(lat, lng),
                    location: postcode + ', UK',
                    stopover: true
                });

                record_list_route[index] = {
                    record_planner_id: record_planner_id,
                    postcode: postcode
                };

            } else {
				 record_list_route[index] = {};
			}
        });
        //Get the origin and the destination
        var origin = maps.markerLocation.getPosition();
        var destination = maps.markerLocation.getPosition();

        if ($('.directions-form').find('input[name="origin"]').val().length > 0) {
            origin = $('.directions-form').find('input[name="origin"]').val();
        }

        if ($('.directions-form').find('input[name="destination"]').val().length > 0) {
            destination = $('.directions-form').find('input[name="destination"]').val();
        }

        maps.getDirections(origin+" UK", destination+" UK", waypts, record_list_route);
    },

    updateRecordPlannerList: function () {
        panelList = $('#draggablePanelList');
        $('li', panelList).each(function (index, elem) {
			$(elem).find('btn').each(function(i,e){
            if (index < 8) {
                $(e).removeClass("btn-default").addClass("btn-success");
            }
            else {
                $(e).removeClass("btn-success").addClass("btn-default");
            }
			});
        });
        $('.route-header').hide();
        maps.removeDirections();
    },

    codeAddress: function (zoom) {
        var address = $('.map-form').find('input[name="postcode"]').val();
        if (address == "") {
            map.setCenter(maps.myLatlng);
            map.setZoom(maps.default_zoom);
            helper.current_postcode = false;
            var has_postcode = false
        } else {
			address += " UK"
            if (typeof maps.markerLocation != 'undefined') {
                maps.markerLocation.setMap(null);
            }
            maps.geocoder.geocode({
                'address': address
            }, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    map.setCenter(results[0].geometry.location);
                    map.setZoom(zoom);
                    maps.markerLocation = new google.maps.Marker({
                        map: map,
                        position: results[0].geometry.location
                    });
                } else {
                    alert('Geocode was not successful for the following reason: ' + status);
                }
            });
        }
    },

    getDirections: function (origin, destination, waypoints, record_list_ord) {
        var start = origin;
        var dest = destination;
        var travelMode = $('.map-form').find('input[name="travel-mode"]').val();

        $('.change-directions-btn,.planner-travel-mode').fadeTo("fast", 0.4);
        $('.' + travelMode).fadeTo("fast", 1);

        var request = {
            origin: start,
            destination: dest,
            waypoints: waypoints,
            optimizeWaypoints: ($('.directions-form').find('input[name="optimized"]').prop('checked')),
            travelMode: google.maps.DirectionsTravelMode[travelMode],
            transitOptions: {
                routingPreference: google.maps.TransitRoutePreference.FEWER_TRANSFERS
            },
            unitSystem: google.maps.UnitSystem.IMPERIAL
        };
        maps.directionsService.route(request, function (result, status) {
            if (status == google.maps.DirectionsStatus.OK) {
                var total_duration = 0;
                var total_distance = 0;
                var record_list_route = [];
                var waypoint_order = result.routes[0].waypoint_order;
                //Iterate the routes
                $.each(result.routes[0].legs, function (index, route) {
                    if (maps.map_type == 'planner') {
                        var data_route = {
                            'order_num': index,
                            'record_planner': record_list_ord[index],
                            'start_add': route.start_address,
                            'start_lat': route.start_location.G,
                            'start_lng': route.start_location.K,
                            'end_add': route.end_address,
                            'end_lat': route.end_location.G,
                            'end_lng': route.end_location.K,
                            'distance': route.distance.value,
                            'duration': route.duration.value,
                            'travel_mode': travelMode
                        };
                        record_list_route.push(data_route);
                    }
                    total_duration = total_duration + parseInt(route.duration.value);
                    total_distance = total_distance + parseInt(route.distance.value);
                });
                $('.route-info').html(
                    (Math.ceil((total_distance / 1000) / 1.2)) + ' miles - ' +
                    (toHHMMSS(total_duration)) +
                    '<span style="font-size: 25px; margin-right: 12px; margin-left: 11px;" class="show-directionsPanel-btn pointer glyphicon glyphicon-eye-open"></span>');
                maps.directionsDisplay.setDirections(result);
                if (maps.map_type == 'planner') {
                    maps.saveRecordRoute(record_list_route);
                    map.setZoom(maps.default_zoom);	
                }
            }
        });
        maps.directionsDisplay.setMap(map);
        $('.directions-menu').show();
        maps.directionsDisplay.setPanel(document.getElementById("directionsPanel"));
    },

    removeDirections: function () {
        maps.directionsDisplay.setMap(null);
        $('.directions-menu').hide();
    },

    showDirections: function () {
        var mheader = $('.directionsPanel-container').find('.panel-heading').html();
        var mbody = $('.directionsPanel-container').find('.panel-body').html();
        var mfooter = '';
        modals.load_modal(mheader, mbody, mfooter);
    },

    //Get current bounds
    getBounds: function () {
        var bounds_obj = map.getBounds();

        neLat = (bounds_obj) ? bounds_obj.getNorthEast().lat() : null;
        neLng = (bounds_obj) ? bounds_obj.getNorthEast().lng() : null;
        swLat = (bounds_obj) ? bounds_obj.getSouthWest().lat() : null;
        swLng = (bounds_obj) ? bounds_obj.getSouthWest().lng() : null;

        maps.bounds = {
            'neLat': neLat,
            'neLng': neLng,
            'swLat': swLat,
            'swLng': swLng
        };

        return maps.bounds;
    },

    //Show the items in the map
    showItems: function () {
        if ($('#map-view-toggle').prop('checked')) {
            maps.deleteMarkers();
            var legend_ar = [];
            $.each(maps.items, function (i, item) {
                if (maps.map_type == "appointments") {
                    maps.addAppointmentMarker(item);
                }
                else if (maps.map_type == "records") {
                    maps.addRecordMarker(item);
                }
                else if (maps.map_type == "planner") {
                    maps.addPlannerMarker(item);
                }

                //Get colour legend if it is filtered by colour
                if (maps.colour_by) {
                    var colour_ar = item.record_color.split('/');
                    var colour = colour_ar[0];
                    $.each(item, function (key, value) {
                        if (key.indexOf(maps.colour_by) === 0) {
                            legend_ar[colour] = (value?value:'-');
                        }
                    });
                }
            });
            //Show colour legend if it is filtered by colour
            map.controls[google.maps.ControlPosition.LEFT_TOP].clear();
            if (maps.colour_by) {
                var showLegendDiv = document.createElement('div');
                var legendControl = new maps.showLegend(showLegendDiv, map, legend_ar);
                map.controls[google.maps.ControlPosition.LEFT_TOP].push(showLegendDiv);
            }
        }
    },

    //Show colour legend if it is filtered by colour
    showLegend: function(showLegendDiv, map,legend_data) {
        showLegendDiv.style.padding = '5px';
        var controlUI = document.createElement('div');
        controlUI.style.backgroundColor = 'white';
        controlUI.style.opacity = '0.8';
        controlUI.style.filter = 'alpha(opacity=80)';
        controlUI.style.border='1px solid';
        //controlUI.style.cursor = 'pointer';
        controlUI.style.textAlign = 'center';
            showLegendDiv.appendChild(controlUI);
        var controlText = document.createElement('div');
        controlText.style.fontFamily='Arial,sans-serif';
        controlText.style.fontSize='12px';
        controlText.style.paddingLeft = '4px';
        controlText.style.paddingRight = '4px';
        var content = '<table><thead><tr><th></th><th>'+maps.colour_by.toUpperCase()+'</th></tr></thead><tbody>';
        for (var key in legend_data) {
            content += '<tr>' +
                            '<td><span class="fa fa-circle" style="font-size:20px; color: '+key+'"></span></td>' +
                            '<td title="'+legend_data[key]+'" style="padding-left: 5px; text-align: left;">'+legend_data[key].substr(0,30)+(legend_data[key].length > 30?'...':'')+'</td>' +
                        '</tr>';
        }
        content += '</tbody></table>';
        controlText.innerHTML = content;
        controlUI.appendChild(controlText);

        // Setup click-event listener: simply set the map to London
        //google.maps.event.addDomListener(controlUI, 'click', function() {
        //
        //});
    },

    //Animate a marker icon
    animateMarker: function(id) {
        $.each(maps.markers, function (index, marker) {
            if (marker.id == id) {
                if (marker.getAnimation() != null) {
                    marker.setAnimation(null);
                } else {
                    marker.setAnimation(google.maps.Animation.BOUNCE);
                }
            }
        });
    },

    //Remove marker animation icon
    removeMarkerAnimation: function (id) {
        $.each(maps.markers, function (index, marker) {
            if (marker.id == id) {
                if (marker.getAnimation() != null) {
                    marker.setAnimation(null);
                }
            }
        });
    },

    //Open infowindow for a marker
    //openInfoWindow: function(postcode) {
    //    maps.temp_bounds = maps.getBounds();
    //    var contentString = "";
    //    $.each(maps.markers, function(index, marker) {
    //        if (marker.postcode == postcode) {
    //            maps.infowindow.close();
    //            maps.infowindow.setContent(marker.content);
    //            maps.infowindow.open(map, marker);
    //        }
    //    });
    //},
    addRecordMarker: function (value) {
        var marker_color = "#" + (value.record_color_map?(value.record_color_map).substr(1):maps.intToARGB(maps.hashCode(value.attendee)));
        var marker_icon = fontawesome.markers.MAP_MARKER;
        var marker_scale = 0.4;

        if (((planner_permission == true)) && (value.record_planner_id)) {
            marker_icon = fontawesome.markers.FLAG;
            marker_scale = 0.3;
        }
        else if (value.map_icon) {
            marker_icon = eval("fontawesome.markers."+value.map_icon);
            marker_scale = 0.3;
        }
        else if (value.campaign_map_icon) {
            marker_icon = eval("fontawesome.markers."+value.campaign_map_icon);
            marker_scale = 0.3;
        }

        var navbtn = false;
        var planner_info = false;
        var planner_postcode = false;
        if (planner_permission == true) {
            planner_info =
                '<b>Planner: </b>' +
                '<span style="margin-right: 5px;">' + (value.record_planner_id ? (value.planner_user + ' on ' + value.planner_date) : '') + '</span>' +
                '<a href="#" class="btn btn-info btn-sm glyphicon glyphicon-time planner-btn" item-urn="' + value.urn + '" item-planner-date="' + (value.planner_date ? value.planner_date : '') + '"></a>';
            if(value.planner_postcode) {
                planner_postcode =
                    '<b>Planner Postcode: </b>' +
                    '<span style="margin-right: 5px;">' + value.planner_postcode + '</span>';
            }
        }
        if ($('.map-form').find('input[name="postcode"]').val().length > 0) {
            navbtn = '<p>' +
                '<span><a class="btn btn-xs btn-success appointment-btn" item-postcode="' + value.postcode + '" href="#">Navigate </a></span>';
        }

        var postcode_options = '';
        $.each(value.planner_addresses, function (k, address) {
            if (value.planner_postcode == address) {
                var selected = "selected";
            } else {
                var selected = "";
            }
            postcode_options += '<option ' + selected + ' value="' + k + '">' + address + '</option>';
        });

        var planner_form =
            '<form class="planner-form-' + value.urn + '">' +
                '<div class="form-group input-group-sm">' +
                    '<p>Planning Date: </p><input type="text" class="form-control date" name="date" placeholder="Enter the planning date" required/>' +
                '</div>' +
                '<div class="form-group input-group-sm">' +
                    '<p>Planning Postcode: </p><select class="selectpicker" data-width="100%" id="planner_address">' + postcode_options + '</select>' +
                '</div>' +
                '<p>' +
                    '<span><a class="btn btn-default btn-sm cancel-planner-btn" item-urn="' + value.urn + '" href="#">Cancel</a></span>' +
                    '<span class="pull-right"><a class="btn btn-primary btn-sm save-planner-btn" item-urn="' + value.urn + '" item-record-planner-id="' + (value.record_planner_id ? value.record_planner_id : '') + '" href="#">Save</a></span>' +
                '</p>' +
            '</form>';

        var contentString =
            '<div id="content">' +
            '<div id="siteNotice">' +
            '</div>' +
            '<h4 id="firstHeading" class="firstHeading">' + (value.name ?value.name:'') + '</h4>' +
            '<div id="bodyContent_' + value.urn + '">' +
            (value.company_name ? '<p><b>Company: </b>' + value.company_name + '</p>' : '') +
            (value.contact_name ? '<p><b>Contact: </b>' + value.contact_name + '</p>' : '') +
            (value.outcome ? '<p><b>Outcome: </b>' + value.outcome + '</p>' : '') +
            (value.nextcall ? '<p><b>Next Call: </b>' + value.nextcall + '</p>' : '') +
            (value.date_updated ? '<p><b>Last Updated: </b>' + value.date_updated + '</p>' : '') +
            (value.postcode ? '<p><b>Postcode: </b>' + value.postcode + '</p>' : '') +
            (value.website ? '<p><b>Website: </b>' + value.website + '</p>' : '') +
            (planner_info ? '<p>' + planner_info + '</p>' : '') + '<p>' +
            (planner_postcode ? '<p>' + planner_postcode + '</p>' : '') + '<p>' +
            (navbtn ? navbtn : '') +
            '<span class="pull-right"><a class="btn btn-primary btn-xs marl" href="' + helper.baseUrl + 'records/detail/' + value.urn + '">View Record</a></span> ' +
            '</p>' +
            '</div>' +
            '<div id="formContent_' + value.urn + '" style="display:none;">' +
                planner_form +
            '</div>' +
        '</div>';

        var marker = new google.maps.Marker({
            position: new google.maps.LatLng(value.lat, value.lng),
            map: map,
            title: value.name,
            postcode: value.postcode,
            id: value.marker_id,
            content: contentString,
            icon: {
                path: marker_icon,
                scale: marker_scale,
                strokeWeight: 0.2,
                strokeColor: 'black',
                strokeOpacity: 1,
                fillColor: marker_color,
                fillOpacity: 0.9,
            },
        });
        maps.setMarker(marker);
    },
    // Add a marker to the map and push to the array.
    addAppointmentMarker: function (value) {
        //var marker_color = "#" + maps.intToARGB(maps.hashCode(value.name));
        //var marker_icon = fontawesome.markers.MAP_MARKER;
        var marker_color = "#" + (value.record_color_map?(value.record_color_map).substr(1):maps.intToARGB(maps.hashCode(value.name)));
        var marker_icon = fontawesome.markers.MAP_MARKER;
        var marker_scale = 0.4;

        if (value.map_icon) {
            marker_icon = eval("fontawesome.markers."+value.map_icon);
            marker_scale = 0.3;
        }
        else if (value.campaign_map_icon) {
            marker_icon = eval("fontawesome.markers."+value.campaign_map_icon);
            marker_scale = 0.3;
        }

        var navbtn = false;
        var planner_info = false;
        var planner_postcode = false;
        if (planner_permission == true) {
            planner_info =
                '<b>Planner: </b>' +
                '<span style="margin-right: 5px;">' + (value.record_planner_id ? (value.planner_user + ' on ' + value.planner_date) : '') + '</span>' +
                '<a href="#" class="btn btn-info btn-xs glyphicon glyphicon-time planner-btn" item-urn="' + value.urn + '" item-planner-date="' + (value.planner_date ? value.planner_date : '') + '"></a>';
            if(value.planner_postcode) {
                planner_postcode =
                    '<b>Planner Postcode: </b>' +
                    '<span style="margin-right: 5px;">' + value.planner_postcode + '</span>';
            }
        }
        if ($('.map-form').find('input[name="postcode"]').val().length > 0) {
            navbtn = '<p>' +
                '<span><a class="btn btn-success appointment-btn" item-postcode="' + value.postcode + '" href="#">Navigate </a></span>';
        }

        var postcode_options = '';
        $.each(value.planner_addresses, function (k, address) {
            if (value.planner_postcode == address) {
                var selected = "selected";
            } else {
                var selected = "";
            }
            postcode_options += '<option ' + selected + ' value="' + k + '">' + address + '</option>';
        });

        var planner_form =
            '<form class="planner-form-' + value.urn + '">' +
            '<div class="form-group input-group-sm">' +
            '<p>Planning Date: </p><input type="text" class="form-control date" name="date" placeholder="Enter the planning date" required/>' +
            '</div>' +
            '<div class="form-group input-group-sm">' +
            '<p>Planning Postcode: </p><select class="selectpicker" data-width="100%" id="planner_address">' + postcode_options + '</select>' +
            '</div>' +
            '<p>' +
            '<span><a class="btn btn-default btn-sm cancel-planner-btn" item-urn="' + value.urn + '" href="#">Cancel</a></span>' +
            '<span class="pull-right"><a class="btn btn-primary btn-sm save-planner-btn" item-urn="' + value.urn + '" item-record-planner-id="' + (value.record_planner_id ? value.record_planner_id : '') + '" href="#">Save</a></span>' +
            '</p>' +
            '</form>';

        var contentString =
            '<div id="content">' +
            '<div id="siteNotice">' +
            '</div>' +
            '<h4 id="firstHeading" class="firstHeading">' + (value.name?value.name:'') + '</h4>' +
            '<div id="bodyContent_' + value.urn + '">' +
            (value.name ? '<p><b>Company: </b>' + value.name + '</p>' : '') +
            (value.start ? '<p><b>Date: </b>' + value.start + '</p>' : '') +
            (value.title ? '<p><b>Title: </b>' + value.title + '</p>' : '') +
            (value.attendee ? '<p><b>Attendees: </b>' + value.attendee + '</p>' : '') +
            (value.date_added ? '<p><b>Created on: </b>' + value.date_added + '</p>' : '') +
            (value.postcode ? '<p><b>Postcode: </b>' + value.postcode + '</p>' : '') + '<p>' +
            (planner_info ? '<p>' + planner_info + '</p>' : '') + '<p>' +
            (planner_postcode ? '<p>' + planner_postcode + '</p>' : '') + '<p>' +
            (navbtn ? navbtn : '') +
            '<span class="pull-right"><a class="btn btn-primary" href="' + helper.baseUrl + 'records/detail/' + value.urn + '">View Record</a></span>' +
            '</p>' +
            '</div>' +
            '<div id="formContent_' + value.urn + '" style="display:none;">' +
                planner_form +
            '</div>' +
            '</div>';

        var marker = new google.maps.Marker({
            position: new google.maps.LatLng(value.lat, value.lng),
            map: map,
            title: value.name,
            postcode: value.postcode,
            content: contentString,
            id: value.marker_id,
            icon: {
                path: marker_icon,
                scale: marker_scale,
                strokeWeight: 0.2,
                strokeColor: 'black',
                strokeOpacity: 1,
                fillColor: marker_color,
                fillOpacity: 0.9,
            },
        });
        maps.setMarker(marker);
    },

    // Add a marker to the map and push to the array.
    addPlannerMarker: function (value) {
        var marker_color = "#" + maps.intToARGB(maps.hashCode(value.name));
        var marker_icon = fontawesome.markers.MAP_MARKER;

        var planner_info =
            '<b>Planner: </b>' +
            '<span style="margin-right: 5px;">' + (value.record_planner_id ? (value.user + ' on ' + value.start) : '') + '</span>' +
            '<a href="#" class="btn btn-info btn-sm glyphicon glyphicon-time planner-btn" item-urn="' + value.urn + '" item-planner-date="' + (value.start ? value.start : '') + '"></a>';


        var contentString =
            '<div id="content">' +
            '<div id="siteNotice">' +
            '</div>' +
            '<h2 id="firstHeading" class="firstHeading">' + value.name + '</h2>' +
            '<div id="bodyContent_' + value.urn + '">' +
            '<p><b>Company: </b>' + (value.name ? value.name : '') + '</p>' +
            '<p><b>Contact: </b>' + (value.fullname ? value.fullname : '') + '</p>' +
            '<p><b>Outcome: </b>' + (value.outcome ? value.outcome : '') + '</p>' +
            '<p><b>Next Call: </b>' + (value.nextcall ? value.nextcall : '') + '</p>' +
            '<p><b>Last Updated: </b>' + (value.date_updated ? value.date_updated : '') + '</p>' +
            '<p><b>Postcode: </b>' + (value.postcode ? (value.postcode + '(' + (value.lat ? value.lat : '-') + ',' + (value.lng ? value.lng : '-') + ')') : '') + '</p>' +
            '<p style="display: none;">' + value.location_id + '</p>' +
            '<p><b>Website: </b><a target="_blank" href="' + value.website + '">' + value.website + '</a></p>' +
            '<p>' + planner_info + '</p>' +
            '<p>' +
            '<span><a class="btn btn-success btn-sm record-btn" item-postcode="' + value.postcode + '" href="#"><span class="glyphicon glyphicon-road"></span> Navigate </a></span>' +
            '<span class="pull-right"><a class="btn btn-primary btn-sm" href="' + helper.baseUrl + 'records/detail/' + value.urn + '"><span class="glyphicon glyphicon-eye-open"></span> View Record</a></span>' +
            '</p>' +
            '</div>' +
            '<div id="formContent_' + value.urn + '" style="display:none;">' +
            '<form class="planner-form-' + value.urn + '">' +
            '<div class="form-group input-group-sm">' +
            '<p>Planning Date: </p><input type="text" class="form-control date" name="date" placeholder="Enter the planning date" required/>' +
            '</div>' +
            '<p>' +
            '<span><a class="btn btn-default btn-sm cancel-planner-btn" item-urn="' + value.urn + '" href="#">Cancel</a></span>' +
            '<span class="pull-right"><a class="btn btn-primary btn-sm save-planner-btn" item-urn="' + value.urn + '" item-postcode="' + value.postcode + '" item-location-id="' + value.location_id + '" item-record-planner-id="' + (value.record_planner_id ? value.record_planner_id : '') + '" href="#">Save</a></span>' +
            '</p>' +
            '</form>' +
            '</div>'
        '</div>';

        var marker = new google.maps.Marker({
            record_planner_id: value.record_planner_id,
            position: new google.maps.LatLng(value.lat, value.lng),
            map: map,
            title: value.name,
            postcode: value.postcode,
            content: contentString,
            id: value.urn,
            icon: {
                path: marker_icon,
                scale: 0.4,
                strokeWeight: 0.2,
                strokeColor: 'black',
                strokeOpacity: 1,
                fillColor: marker_color,
                fillOpacity: 0.9,
            },
        });

        maps.setMarker(marker);
    },

    setMarker: function (marker) {
        google.maps.event.addListener(marker, 'click', function () {
            maps.infowindow.close();
            maps.infowindow.setContent(marker.content);
            maps.infowindow.open(map, marker);
        });

        //Show in the table the appointment selected in the map

        google.maps.event.addListener(marker, 'mouseover', function () {
            $('.data-table tbody').find("[postcode='" + marker.postcode + "']").css('color', 'green');
            $('li').find(".planner-title[data-urn='" + marker.id + "']").css('color', '#FF3333');
        });

        //Hide in the table the appointment deselected in the map
        google.maps.event.addListener(marker, 'mouseout', function () {
            $('.data-table tbody').find("[postcode='" + marker.postcode + "']").css('color', 'black');
            $('li').find(".planner-title[data-urn='" + marker.id + "']").css('color', '#fff');
        });

        maps.markers.push(marker);
    },

    // Hash any string into an integer value
    hashCode: function (str) {
        var hash = 0;
        for (var i = 0; i < str.length; i++) {
            hash = str.charCodeAt(i) + ((hash << 5) - hash);
        }
        return hash;
    },

    // Convert an int to hexadecimal with a max length
    // of six characters.
    intToARGB: function (i) {
        var h = ((i >> 24) & 0xFF).toString(16) +
            ((i >> 16) & 0xFF).toString(16) +
            ((i >> 8) & 0xFF).toString(16) +
            (i & 0xFF).toString(16);
        return h.substring(0, 6);
    },


    // Sets the map on all markers in the array.
    setAllMap: function (map) {
        for (var i = 0; i < maps.markers.length; i++) {
            maps.markers[i].setMap(map);
        }
    },

    // Removes the markers from the map, but keeps them in the array.
    clearMarkers: function () {
        maps.setAllMap(null);
    },

    // Shows any markers currently in the array.
    showMarkers: function () {
        maps.setAllMap(map);
    },

    // Deletes all markers in the array by removing references to them.
    deleteMarkers: function () {
        maps.clearMarkers();
        maps.markers = [];

    },

    //Save record planner
    savePlanner: function (btn) {
        var urn = btn.attr('item-urn');
        var postcode = $('.planner-form-'+urn+' .selectpicker option:selected').html();
        var location_id = $('.planner-form-'+urn+' .selectpicker option:selected').val();
        var record_planner_id = btn.attr('item-record-planner-id');
        var planner_date = $('.planner-form-' + urn).find('input[name="date"]').val();

        $.ajax({
            url: helper.baseUrl + 'records/save_record_planner',
            type: "POST",
            dataType: "JSON",
            data: {
                'urn': urn,
                'postcode': postcode,
                'location_id': location_id,
                'start_date': planner_date,
                'record_planner_id': record_planner_id
            }
        }).done(function (response) {
            if (response.success) {
                flashalert.success(response.msg);
                if (maps.map_type == 'planner') {
                    planner_reload();
                }
                else {
                    if (typeof view_records !== 'undefined') {
                        maps.temp_bounds = maps.bounds
                        var currentPage = view_records.table.page();
                        view_records.table.page(currentPage).draw(false);
                    }
                    if (typeof appointment !== 'undefined') {
                        var currentPage = appointment.table.page();
                        appointment.table.page(currentPage).draw(false);
                    }
                }
            } else {
                flashalert.danger(response.msg);
            }
        });
    },

    saveRecordRoute: function (record_list_route) {
        $.ajax({
            url: helper.baseUrl + "planner/save_record_route",
            type: "POST",
            dataType: "JSON",
            data: {
                record_list: $.parseJSON(JSON.stringify(record_list_route)),
                date: $('.filter-form').find('input[name="date"]').val(),
				origin: $('.directions-form').find('input[name="origin"]').val(),
				destination: $('.directions-form').find('input[name="destination"]').val(),
				user_id: false //we can add a user here to edit some elses planner
            }
        }).done(function (response) {
            if (response.success) {
				planner_reload();
            } else {
				flashalert.danger(response.error);
            }
        }).error(function(){
			flashalert.danger("There was a problem planning the route");
		});
    }

}