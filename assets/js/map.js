// JavaScript Document
/*******************************************************************/
/********************** MAP ****************************************/
/*******************************************************************/
var maps = {
    initialize: function(map_type) {
        getLocation();
        this.map_type = map_type;
        this.directionsDisplay = new google.maps.DirectionsRenderer();
        this.directionsService = new google.maps.DirectionsService();
        this.map;
        this.current_postcode = false;
        this.markers = [];
        this.markerLocation;
        this.bounds = null;
        this.items = [];
        this.table;
        this.infowindow = new google.maps.InfoWindow();
        this.geocoder = new google.maps.Geocoder();
        this.uk_lat = 54.9830568815027;
        this.uk_lng = -4.331033059374933;
        this.default_zoom = 6
        if (typeof localStorage.lat != "undefined" && typeof localStorage.lng != "undefined") {
            this.lat = localStorage.lat;
            this.lng = localStorage.lng;
            var default_zoom = 12;
        } else {
            this.lat = maps.uk_lat;
            this.lng = maps.uk_lng;
            var default_zoom = maps.default_zoom;
        }
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


        $('.map-form').on("keyup keypress", function(e) {
            var code = e.keyCode || e.which;
            if (code == 13) {
                e.preventDefault();
                return false;
            }
        });

        $('#map-view-toggle').bootstrapToggle({
            onstyle: 'success',
            size: 'mini',
        }).show().bootstrapToggle('off');

        $('#map-view-toggle').change(function() {
            if ($(this).prop('checked')) {
                $(document).on('mouseenter', '.data-table tbody tr', function() {
                    maps.animateMarker($(this).attr('postcode'));
                    $(this).css('color', 'green');
                });

                //Start animation in the map for the marker deselected in the table
                $(document).on('mouseleave', '.data-table tbody tr', function() {
                    maps.removeMarkerAnimation($(this).attr('postcode'));
                    $(this).css('color', 'black');
                });
                if (device_type == ('default')) {
                    $("#table-wrapper").removeClass("col-lg-12").addClass("col-lg-6");
                    $(".map-view").show();
                } else {

                    $("#table-wrapper").find('table').find('tbody').hide();
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
                    $(".map-view").hide();
                    removeDirections();
                }
            }
            map_table_reload();
        });

        $(document).on('click', '.get-location-btn', function() {
            maps.removeDirections();
            maps.codeAddress(12);
            //Wait until the map is loaded
            setTimeout(function() {
                map_table_reload();
            }, 2000);
        });

        $('[data-toggle="tooltip"]').tooltip();
        $(document).on('click', '.appointment-btn', function() {
            $('.map-form').find('input[name="destination"]').val($(this).attr('item-postcode'));
            var destination = $('.map-form').find('input[name="destination"]').val();
            maps.getDirections(destination);
        });

        $(document).on('click', '.change-directions-btn', function() {
            $('.map-form').find('input[name="travel-mode"]').val($(this).attr('item-mode'));
            var destination = $('.map-form').find('input[name="destination"]').val();
            maps.getDirections(destination);
        });

        $(document).on('click', '.close-directions-btn', function() {
            maps.removeDirections();
        });

        $(document).on('click', '.get-current-location-btn', function() {
            maps.removeDirections();
            maps.codeCurrentAddress();
        });

        $(document).on('click', '.show-directionsPanel-btn', function() {
            maps.showDirections();
        });

        $(document).on('click', '.close-directionsPanel', function() {
            maps.hideDirections();
        });

        if (typeof localStorage.current_postcode != "undefined") {
            $('.map-form').find('input[name="postcode"]').val(localStorage.current_postcode);
        }
        $('.map-form').find('input[name="travel-mode"]').val("DRIVING");

        var bounds_changer = debounce(function() {
            map_table_reload();
        }, 500);

        google.maps.event.addListener(map, 'zoom_changed', function() {
            google.maps.event.addListenerOnce(map, 'bounds_changed', bounds_changer);
        });
        google.maps.event.addListener(map, 'dragend', function() {
            map_table_reload();
        });

        google.maps.event.addListener(map, "click", function() {
            maps.infowindow.close();
        });

    },
    codeAddress: function(zoom) {
        var address = $('.map-form').find('input[name="postcode"]').val();
        if (address == "") {
            map.setCenter(maps.myLatlng);
            map.setZoom(maps.default_zoom);
            var has_postcode = false
        } else {
            if (typeof maps.markerLocation != 'undefined') {
                maps.markerLocation.setMap(null);
            }
            maps.geocoder.geocode({
                'address': address
            }, function(results, status) {
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

    codeCurrentAddress: function() {
        getLocation();
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                if (typeof maps.markerLocation != 'undefined') {
                    maps.markerLocation.setMap(null);
                }
                var address = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                map.setCenter(address);
                map.setZoom(12);
                maps.markerLocation = new google.maps.Marker({
                    map: map,
                    position: address
                });
                $('.map-form').find('input[name="postcode"]').val(localStorage.current_postcode);
                //Wait until the map is loaded
                setTimeout(function() {
                    map_table_reload();
                }, 2000);
            });
        }
    },

    getDirections: function(destination) {
        var start = maps.markerLocation.getPosition();
        var dest = destination;
        var travelMode = $('.map-form').find('input[name="travel-mode"]').val();

        $('.change-directions-btn').fadeTo("fast", 0.4);
        $('.' + travelMode).fadeTo("fast", 1);

        var request = {
            origin: start,
            destination: dest,
            travelMode: google.maps.DirectionsTravelMode[travelMode],
            transitOptions: {
                routingPreference: google.maps.TransitRoutePreference.FEWER_TRANSFERS
            },
            unitSystem: google.maps.UnitSystem.IMPERIAL
        };
        maps.directionsService.route(request, function(result, status) {
            if (status == google.maps.DirectionsStatus.OK) {
                $('.route-info').html(
                    result.routes[0].legs[0].distance.text + ': ' +
                    result.routes[0].legs[0].duration.text + ' ' +
                    '<span style="font-size: 15px;" class="show-directionsPanel-btn pointer glyphicon glyphicon-eye-open"></span>');
                maps.directionsDisplay.setDirections(result);
            }
        });
        maps.directionsDisplay.setMap(map);
        $('.directions-menu').show();
        maps.directionsDisplay.setPanel(document.getElementById("directionsPanel"));
    },

    removeDirections: function() {
        maps.directionsDisplay.setMap(null);
        $('.directions-menu').hide();
    },

    showDirections: function() {
        var pagewidth = $(window).width() / 2;
        var moveto = pagewidth - 250;
        $('<div class="modal-backdrop directionsPanel in"></div>').appendTo(document.body).hide().fadeIn();
        $('.directionsPanel-container').find('.directionsPanel-panel').show();
        $('.directionsPanel-content').show();
        $('.directionsPanel-container').fadeIn()
        $('.directionsPanel-container').animate({
            width: '600px',
            left: '1%',
            top: '10%'
        }, 1000);
    },

    hideDirections: function() {
        $('.modal-backdrop.directionsPanel').fadeOut();
        $('.directionsPanel-container').fadeOut(500, function() {
            $('.directionsPanel-content').show();
            $('.alert').addClass('hidden');
        });
        $('.directionsPanel-container').fadeOut(500, function() {
            $('.directionsPanel-content').show();
        });
    },

    //Get current bounds
    getBounds: function() {
        var bounds_obj = map.getBounds();

        neLat = (bounds_obj) ? bounds_obj.getNorthEast().lat() : null;
        neLng = (bounds_obj) ? bounds_obj.getNorthEast().lng() : null;
        swLat = (bounds_obj) ? bounds_obj.getSouthWest().lat() : null;
        swLng = (bounds_obj) ? bounds_obj.getSouthWest().lng() : null;

        bounds = {
            'neLat': neLat,
            'neLng': neLng,
            'swLat': swLat,
            'swLng': swLng
        };
        return bounds;
    },

    //Show the items in the map
    showItems: function() {
        if ($('#map-view-toggle').prop('checked')) {
            maps.deleteMarkers();
            $.each(maps.items, function(index, value) {
                if (maps.map_type == "appointments") {
                    maps.addAppointmentMarker(value);
                }
                if (maps.map_type == "records") {
                    maps.addRecordMarker(value);
                }
            });
        }
    },

    //Animate a marker icon
    animateMarker: function(postcode) {
        $.each(maps.markers, function(index, marker) {
            if (marker.postcode == postcode) {
                if (marker.getAnimation() != null) {
                    marker.setAnimation(null);
                } else {
                    marker.setAnimation(google.maps.Animation.BOUNCE);
                }
            }
        });
    },

    //Remove marker animation icon
    removeMarkerAnimation: function(postcode) {
        $.each(maps.markers, function(index, marker) {
            if (marker.postcode == postcode) {
                if (marker.getAnimation() != null) {
                    marker.setAnimation(null);
                }
            }
        });
    },

    //Open infowindow for a marker
    openInfoWindow: function(postcode) {
        var contentString = "";
        $.each(maps.markers, function(index, marker) {
            if (marker.postcode == postcode) {
                maps.infowindow.close();
                maps.infowindow.setContent(marker.content);
                maps.infowindow.open(map, marker);
            }
        });
    },
    addRecordMarker: function(value) {
        var marker_color = "|" + maps.intToARGB(maps.hashCode(value.attendee));
        var marker_text_color = "|FFFFFF";
        var character = "|" + (value.attendee).substr(0, 1);
        var pin_style = (((planner_permission == true)) && (value.record_planner_id) ? "pin_star" : "pin");
        var star_color = (((planner_permission == true)) && (value.record_planner_id) ? "|FCEF04" : "");
        var map_pin_style = "d_map_xpin_letter";


        var planner_info = '';
        if (planner_permission == true) {
            planner_info =
                '<b>Planner: </b>' +
                '<span style="margin-right: 5px;">' + (value.record_planner_id ? (value.planner_user + ' on ' + value.planner_date) : '') + '</span>' +
                '<a href="#" class="btn btn-info btn-sm glyphicon glyphicon-time planner-btn" item-urn="' + value.urn + '" item-planner-date="' + (value.planner_date ? value.planner_date : '') + '"></a>';
        }

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
            position: new google.maps.LatLng(value.lat, value.lng),
            map: map,
            title: value.name,
            postcode: value.postcode,
            content: contentString,
            icon: "http://chart.apis.google.com/chart?chst=" + map_pin_style + "&chld=" + pin_style + character + marker_color + marker_text_color + star_color
        });
		 maps.setMarker(marker);
    },
    // Add a marker to the map and push to the array.
    addAppointmentMarker: function(value) {
        var marker_color = maps.intToARGB(maps.hashCode(value.name));
        var marker_text_color = "FFFFFF";
        var character = (value.name).substr(0, 1);
        var contentString =
            '<div id="content">' +
            '<div id="siteNotice">' +
            '</div>' +
            '<h2 id="firstHeading" class="firstHeading">' + value.name + '</h2>' +
            '<div id="bodyContent">' +
            '<p><b>Company: </b>' + (value.name ? value.name : '') + '</p>' +
            '<p><b>Date: </b>' + (value.start ? value.start : '') + '</p>' +
            '<p><b>Title: </b>' + (value.title ? value.title : '') + '</p>' +
            '<p><b>Attendees: </b>' + (value.attendee ? value.attendee : '') + '</p>' +
            '<p><b>Set on: </b>' + (value.date_added ? value.date_added : '') + '</p>' +
            '<p><b>Postcode: </b>' + (value.postcode ? (value.postcode + '(' + (value.lat ? value.lat : '-') + ',' + (value.lng ? value.lng : '-') + ')') : '') + '</p>' +
            '<p>' +
            '<span><a class="btn btn-success appointment-btn" item-postcode="' + value.postcode + '" href="#">Navigate </a></span>' +
            '<span class="pull-right"><a class="btn btn-primary" href="' + helper.baseUrl + 'records/detail/' + value.urn + '">View Record</a></span>' +
            '</p>' +
            '</div>' +
            '</div>';

        var marker = new google.maps.Marker({
            position: new google.maps.LatLng(value.lat, value.lng),
            map: map,
            title: value.name,
            postcode: value.postcode,
            content: contentString,
            icon: "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=" + character + "|" + marker_color + "|" + marker_text_color
        });
        maps.setMarker(marker);
    },
    setMarker: function(marker) {
        google.maps.event.addListener(marker, 'click', function() {
            maps.infowindow.close();
            maps.infowindow.setContent(marker.content);
            maps.infowindow.open(map, marker);
        });

        //Show in the table the appointment selected in the map

        google.maps.event.addListener(marker, 'mouseover', function() {
            $('.data-table tbody').find("[postcode='" + marker.postcode + "']").css('color', 'green');
        });

        //Hide in the table the appointment deselected in the map
        google.maps.event.addListener(marker, 'mouseout', function() {
            $('.data-table tbody').find("[postcode='" + marker.postcode + "']").css('color', 'black');
        });

        maps.markers.push(marker);
    },

    // Hash any string into an integer value
    hashCode: function(str) {
        var hash = 0;
        for (var i = 0; i < str.length; i++) {
            hash = str.charCodeAt(i) + ((hash << 5) - hash);
        }
        return hash;
    },

    // Convert an int to hexadecimal with a max length
    // of six characters.
    intToARGB: function(i) {
        var h = ((i >> 24) & 0xFF).toString(16) +
            ((i >> 16) & 0xFF).toString(16) +
            ((i >> 8) & 0xFF).toString(16) +
            (i & 0xFF).toString(16);
        return h.substring(0, 6);
    },


    // Sets the map on all markers in the array.
    setAllMap: function(map) {
        for (var i = 0; i < maps.markers.length; i++) {
            maps.markers[i].setMap(map);
        }
    },

    // Removes the markers from the map, but keeps them in the array.
    clearMarkers: function() {
        maps.setAllMap(null);
    },

    // Shows any markers currently in the array.
    showMarkers: function() {
        maps.setAllMap(map);
    },

    // Deletes all markers in the array by removing references to them.
    deleteMarkers: function() {
        maps.clearMarkers();
        maps.markers = [];

    }

}