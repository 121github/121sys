$(document).ready(function () {
    planner.init();

    $('.map-form').on("keyup keypress", function (e) {
        var code = e.keyCode || e.which;
        if (code == 13) {
            e.preventDefault();
            return false;
        }
    });

    $('#map-view-toggle').bootstrapToggle({
        onstyle: 'success',
        size: 'mini'
    });
});

var planner = {
    init: function () {
        $('[data-toggle="tooltip"]').tooltip();
        planner.reload_table('<th>Postcode</th>-table');
    },
    reload_table: function (table_name) {
        planner.populate_table();
    },
    populate_table: function (table_name) {

        var directionsDisplay = new google.maps.DirectionsRenderer();
        var directionsService = new google.maps.DirectionsService();
        var map;
        var markers = [];
        var markerLocation;
        var bounds = null;
        var records = [];
        var infowindow = new google.maps.InfoWindow();
        var geocoder = new google.maps.Geocoder();
        var panelList = null;

        google.maps.event.addDomListener(window, 'load', initialize);


        /*******************************************************************/
        /********************** GET and PRINT THE RECORDS *************/
        /*******************************************************************/
        function getRecords() {

            records = [];
            $('.dt_info').hide();

            $.ajax({
                url: helper.baseUrl + "planner/planner_data",
                type: "POST",
                dataType: "JSON",
                data: {
                    bounds: getBounds(),
                    map: $('#map-view-toggle').prop('checked'),
                    date: $('.filter-form').find('input[name="date"]').val()
                }
            }).done(function (response) {

                var body = '';
                $.each(response.data, function (k, val) {
                    records.push(val);
                    body +=
                        '<div class="row">' +
                            '<div class="col-lg-1">' +
                                '<a class="" data-toggle="collapse" data-parent="#accordion" href="#collapse_'+val.record_planner_id+'" aria-expanded="false" aria-controls="#collapse_"'+val.record_planner_id+'">' +
                                    '<span class="glyphicon glyphicon-plus pointer"></span>' +
                                '</a>' +
                            '</div>' +
                            '<div class="col-lg-11">' +
                                '<li class="panel panel-default" postcode="'+val.postcode+'" record-planner-id="'+val.record_planner_id+'">' +
                                        '<div class="panel-heading record-planner-heading pointer" data-modal="view-record" data-urn="'+val.urn+'" record-planner-id="'+val.record_planner_id+'">' +
                                            '<span>' + val.name + '</span>' +
                                            '<span class="pull-right">' + val.postcode + '</span>' +
                                        '</div>' +
                                    '<div class="panel-body collapse" id="collapse_'+val.record_planner_id+'">' +
                                        '<div class="panel-body">Content ...</div>' +
                                    '</div>' +
                                '</li>' +
                            '</div>' +
                        '</div>';
                });
                $('#draggablePanelList').html(body);
                showRecords();


            });
        }


        /*******************************************************************/
        /********************** MAP ****************************************/
        /*******************************************************************/
        function initialize() {
            var lat = 53.46755;
            var lng = -2.276584;
            var myLatlng = new google.maps.LatLng(lat, lng);

            var mapOptions = {
                zoom: 12,
                center: myLatlng,
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

            map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

            $('.map-form').find('input[name="postcode"]').val("M5 3EZ");
            $('.map-form').find('input[name="travel-mode"]').val("DRIVING");
            codeAddress(5);

            //Wait until the map is loaded
            setTimeout(function () {
                getRecords();
            }, 3000);

            google.maps.event.addListener(map, 'zoom_changed', function () {
                google.maps.event.addListenerOnce(map, 'bounds_changed', function (e) {
                    getRecords();
                });
            });

            google.maps.event.addListener(map, 'dragend', function () {
                getRecords();
            });

            google.maps.event.addListener(map, "click", function () {
                infowindow.close();
            });


            $(document).on('click', '.record-planner-btn', function () {
                $('.map-form').find('input[name="destination"]').val($(this).attr('item-postcode'));
                var destination = $('.map-form').find('input[name="destination"]').val();
                getDirections(destination);
            });

            $(document).on('click', '.change-directions-btn', function () {
                $('.map-form').find('input[name="travel-mode"]').val($(this).attr('item-mode'));
                var destination = $('.map-form').find('input[name="destination"]').val();
                getDirections(destination);
            });

            $(document).on('click', '.close-directions-btn', function () {
                removeDirections();
            });

            $(document).on('click', '.get-location-btn', function () {
                removeDirections();
                codeAddress(12);
                //Wait until the map is loaded
                setTimeout(function () {
                    getRecords();
                }, 2000);
            });

            $(document).on('click', '.get-current-location-btn', function () {
                removeDirections();
                codeCurrentAddress();
            });

            $(document).on('click', '.get-origin-location-btn', function () {
                $('.directions-form').find('input[name="origin"]').val('M5 3EZ');
            });

            $(document).on('click', '.get-destination-location-btn', function () {
                $('.directions-form').find('input[name="destination"]').val('M5 3EZ');
            });

            $(document).on('click', '.show-directionsPanel-btn', function () {
                showDirections();
            });

            $(document).on('click', '.close-directionsPanel', function () {
                hideDirections();
            });


            //Planner form
            $(document).on('click', '.planner-btn', function () {
                var urn = $(this).attr('item-urn');
                var planner_date = $(this).attr('item-planner-date');
                var today_date = new Date();
                today_date = today_date.getUTCDate()+"/"+(today_date.getUTCMonth()+1)+"/"+today_date.getFullYear();
                $('#bodyContent_'+urn).hide();
                $('#formContent_'+urn).show();

                $('.date').datetimepicker({
                    format: 'DD/MM/YYYY',
                    pickTime: false,
                    defaultDate: (planner_date.length > 0?planner_date:today_date)
                });
            });

            //Cancel planner form
            $(document).on('click', '.cancel-planner-btn', function () {
                var urn = $(this).attr('item-urn');

                $('#bodyContent_'+urn).show();
                $('#formContent_'+urn).hide();
            });

            //Save planner
            $(document).on('click', '.save-planner-btn', function () {
                savePlanner($(this));
            });

            //Date range
            $('.daterange').daterangepicker({
                    opens: "left",
                    singleDatePicker: true,
                    showDropdowns: true,
                    format: 'DD/MM/YYYY',
                    startDate: moment()
                },
                function(start, end, element) {
                    var $btn = this.element;
                    $btn.find('.date-text').html(start.format('MMMM D'));
                    $btn.closest('.filter-form').find('input[name="date"]').val(start.format('YYYY-MM-DD'));
                    getRecords();
                });
            $(document).on("click", '.daterange', function(e) {
                e.preventDefault();
            });

            //Drag and drop
            jQuery(function($) {
                panelList = $('#draggablePanelList');

                panelList.sortable({
                    // Only make the .panel-heading child elements support dragging.
                    // Omit this to make then entire <li>...</li> draggable.
                    handle: '.record-planner-heading',
                    update: function() {
                        $('.panel', panelList).each(function(index, elem) {
                            var $listItem = $(elem),
                                newIndex = $listItem.index();
                        });
                    }
                });
            });

            //Generate route
            $(document).on("click", '.calc-route-btn', function(e) {
                e.preventDefault();
                var record_list_route = [];
                var waypts = [];
                //Open map view
                if (!$('#map-view-toggle').prop('cheched')) {
                    $('#map-view-toggle').bootstrapToggle('on')
                    showMap($('#map-view-toggle'));
                }
                removeDirections();
                $('.panel', panelList).each(function(index, elem) {
                    var postcode = $(elem).attr('postcode');
                    var record_planner_id = $(elem).attr('record-planner-id');
                    if (index < 8) {
                        waypts.push({
                            location:postcode,
                            stopover:true
                        });
                        record_list_route[index] = record_planner_id;
                    }
                });
                saveRecordOrder(record_list_route);
                getDirections(markerLocation.getPosition(), waypts);
            });

            //Show map button actions
            $('#map-view-toggle').change(function () {
                showMap($('#map-view-toggle'));
                getRecords();
            });



            //var myTitle = document.createElement('span');
            //myTitle.style.color = 'black';
            //myTitle.className = 'btn btn-sm glyphicon glyphicon-chevron-left';
            //myTitle.innerHTML = 'Hello';
            //var myTextDiv = document.createElement('div');
            //myTextDiv.appendChild(myTitle);
            //
            //map.controls[google.maps.ControlPosition.RIGHT_CENTER].push(myTextDiv);

        }

        function saveRecordOrder(record_list_route) {
            $.ajax({
                url: helper.baseUrl + "planner/save_record_order",
                type: "POST",
                dataType: "JSON",
                data: {
                    record_list: record_list_route,
                    date: $('.filter-form').find('input[name="date"]').val()
                }
            }).done(function (response) {

            });
        }
        function showMap(btn) {
            if (btn.prop('checked')) {
                $(document).on('mouseenter', '.record-planner-heading', function () {
                    animateMarker(btn.attr('record-planner-id'));
                    btn.css('color', 'green');
                });

                //Start animation in the map for the marker deselected in the table
                $(document).on('mouseleave', '.record-planner-heading', function () {
                    removeMarkerAnimation(btn.attr('record-planner-id'));
                    btn.css('color', 'black');
                });
                if (device_type == ('default')) {
                    $(".planner-view").removeClass("col-lg-12").addClass("col-lg-6");
                    $(".map-view").show();
                }
                else {

                    $(".planner-data").hide();
                    $(".map-view").show();
                }
                //Reload the map
                google.maps.event.trigger(map, 'resize');
                map.setCenter(markerLocation.getPosition());
            }
            else {
                $(document).off('mouseenter', '.record-planner-heading');
                $(document).off('mouseleave', '.record-planner-heading');
                //Start animation in the map for the marker deselected in the table
                if (device_type == ('default')) {
                    $(".planner-view").removeClass("col-lg-6").addClass("col-lg-12");
                    $(".map-view").hide();
                }
                else {
                    $(".planner-data").show();
                    $(".map-view").hide();
                    removeDirections();
                }
            }
        }

        function codeAddress(zoom) {
            var address = $('.map-form').find('input[name="postcode"]').val();
            if (typeof markerLocation != 'undefined') {
                markerLocation.setMap(null);
            }
            geocoder.geocode({'address': address}, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    map.setCenter(results[0].geometry.location);
                    map.setZoom(zoom);
                    markerLocation = new google.maps.Marker({
                        map: map,
                        position: results[0].geometry.location
                    });
                } else {
                    alert('Geocode was not successful for the following reason: ' + status);
                }
            });
        }

        function codeCurrentAddress() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    if (typeof markerLocation != 'undefined') {
                        markerLocation.setMap(null);
                    }
                    var address = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                    map.setCenter(address);
                    map.setZoom(12);
                    markerLocation = new google.maps.Marker({
                        map: map,
                        position: address
                    });
                    $('.map-form').find('input[name="postcode"]').val('');
                    //Wait until the map is loaded
                    setTimeout(function () {
                        getRecords();
                    }, 2000);
                });
            }
        }

        function getDirections(destination, waypoints) {
            var start = markerLocation.getPosition();
            var dest = destination;
            var travelMode = $('.map-form').find('input[name="travel-mode"]').val();

            $('.change-directions-btn').fadeTo("fast", 0.4);
            $('.' + travelMode).fadeTo("fast", 1);

            var request = {
                origin: start,
                destination: dest,
                waypoints: waypoints,
                optimizeWaypoints: true,
                travelMode: google.maps.DirectionsTravelMode[travelMode],
                transitOptions: {
                    routingPreference: google.maps.TransitRoutePreference.FEWER_TRANSFERS
                },
                unitSystem: google.maps.UnitSystem.IMPERIAL
            };
            directionsService.route(request, function (result, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                    $('.route-info').html(
                        result.routes[0].legs[0].distance.text + ': ' +
                        result.routes[0].legs[0].duration.text + ' ' +
                        '<span style="font-size: 15px;" class="show-directionsPanel-btn pointer glyphicon glyphicon-eye-open"></span>');
                    directionsDisplay.setDirections(result);
                }
            });
            directionsDisplay.setMap(map);
            $('.directions-menu').show();
            directionsDisplay.setPanel(document.getElementById("directionsPanel"));
        }

        function removeDirections() {
            directionsDisplay.setMap(null);
            $('.directions-menu').hide();
        }

        function showDirections() {
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
        }

        function hideDirections() {
            $('.modal-backdrop.directionsPanel').fadeOut();
            $('.directionsPanel-container').fadeOut(500, function () {
                $('.directionsPanel-content').show();
                $('.alert').addClass('hidden');
            });
            $('.directionsPanel-container').fadeOut(500, function () {
                $('.directionsPanel-content').show();
            });
        }

        //Get current bounds
        function getBounds() {
            var bounds_obj = map.getBounds();

            neLat = (bounds_obj) ? bounds_obj.getNorthEast().lat() : null;
            neLng = (bounds_obj) ? bounds_obj.getNorthEast().lng() : null;
            swLat = (bounds_obj) ? bounds_obj.getSouthWest().lat() : null;
            swLng = (bounds_obj) ? bounds_obj.getSouthWest().lng() : null;

            bounds = {'neLat': neLat, 'neLng': neLng, 'swLat': swLat, 'swLng': swLng};

            return bounds;
        }

        //Show the records in the map
        function showRecords() {
            if ($('#map-view-toggle').prop('checked')) {
                deleteMarkers();
                if (records.length > 0) {
                    $.each(records, function (index, value) {
                        addMarker(value);
                    });
                }
            }
        }

        //Animate a marker icon
        function animateMarker(record_planner_id) {
            $.each(markers, function (index, marker) {
                if (marker.record_planner_id == record_planner_id) {
                    if (marker.getAnimation() != null) {
                        marker.setAnimation(null);
                    } else {
                        marker.setAnimation(google.maps.Animation.BOUNCE);
                    }
                }
            });
        }

        //Remove marker animation icon
        function removeMarkerAnimation(record_planner_id) {
            $.each(markers, function (index, marker) {
                if (marker.record_planner_id == record_planner_id) {
                    if (marker.getAnimation() != null) {
                        marker.setAnimation(null);
                    }
                }
            });
        }

        // Add a marker to the map and push to the array.
        function addMarker(value) {
            var marker_color = intToARGB(hashCode(value.name));
            var marker_text_color = "FFFFFF";
            var character = (value.name).substr(0, 1);
            var contentString =
                '<div id="content">' +
                '<div id="siteNotice">' +
                '</div>' +
                '<h2 id="firstHeading" class="firstHeading">' + value.name + '</h2>' +
                '<div id="bodyContent">' +
                '<p><b>Comapny: </b>' + (value.name ? value.name : '') + '</p>' +
                '<p><b>Date: </b>' + (value.start ? value.start : '') + '</p>' +
                '<p><b>User: </b>' + (value.user ? value.user : '') + '</p>' +
                '<p><b>Distance: </b></p>' +
                '<p><b>Time: </b></p>' +
                '<p><b>Postcode: </b>' + (value.postcode ? (value.postcode + '(' + (value.lat ? value.lat : '-') + ',' + (value.lng ? value.lng : '-') + ')') : '') + '</p>' +
                '<p>' +
                '<span><a class="btn btn-success record-planner-btn" item-postcode="' + value.postcode + '" href="#">Navigate </a></span>' +
                '<span class="pull-right"><a class="btn btn-primary" href="' + helper.baseUrl + 'records/detail/' + value.urn + '">View Record</a></span>' +
                '</p>' +
                '</div>' +
                '</div>';


            var planner_info =
                '<b>Planner: </b>' +
                '<span style="margin-right: 5px;">' + (value.record_planner_id?(value.user + ' on ' + value.start):'') + '</span>' +
                '<a href="#" class="btn btn-info btn-sm glyphicon glyphicon-time planner-btn" item-urn="'+value.urn+'" item-planner-date="'+(value.start?value.start:'')+'"></a>';


            var contentString =
                '<div id="content">' +
                '<div id="siteNotice">' +
                '</div>' +
                '<h2 id="firstHeading" class="firstHeading">' + value.name + '</h2>' +
                '<div id="bodyContent_'+value.urn+'">' +
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
                '<div id="formContent_'+value.urn+'" style="display:none;">' +
                '<form class="planner-form-'+value.urn+'">' +
                '<div class="form-group input-group-sm">' +
                '<p>Planning Date: </p><input type="text" class="form-control date" name="date" placeholder="Enter the planning date" required/>' +
                '</div>' +
                '<p>' +
                '<span><a class="btn btn-default btn-sm cancel-planner-btn" item-urn="'+value.urn+'" href="#">Cancel</a></span>' +
                '<span class="pull-right"><a class="btn btn-primary btn-sm save-planner-btn" item-urn="'+value.urn+'" item-postcode="'+value.postcode+'" item-location-id="'+value.location_id+'" item-record-planner-id="'+(value.record_planner_id?value.record_planner_id:'')+'" href="#">Save</a></span>' +
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
                icon: "http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=" + character + "|" + marker_color + "|" + marker_text_color
            });

            google.maps.event.addListener(marker, 'click', function () {
                infowindow.close();
                infowindow.setContent(contentString);
                infowindow.open(map, marker);
            });

            //Show in the table the record selected in the map

            google.maps.event.addListener(marker, 'mouseover', function () {
                console.log(marker.record_planner_id);
                $('.record-planner-heading').find("[record-planner-id='" + marker.record_planner_id + "']").css('color', 'green');
            });

            //Hide in the table the record deselected in the map
            google.maps.event.addListener(marker, 'mouseout', function () {
                $('.record-planner-heading').find("[record-planner-id='" + marker.record_planner_id + "']").css('color', 'black');
            });

            markers.push(marker);
        }

        // Hash any string into an integer value
        function hashCode(str) {
            var hash = 0;
            for (var i = 0; i < str.length; i++) {
                hash = str.charCodeAt(i) + ((hash << 5) - hash);
            }
            return hash;
        }

        // Convert an int to hexadecimal with a max length
        // of six characters.
        function intToARGB(i) {
            var h = ((i >> 24) & 0xFF).toString(16) +
                ((i >> 16) & 0xFF).toString(16) +
                ((i >> 8) & 0xFF).toString(16) +
                (i & 0xFF).toString(16);
            return h.substring(0, 6);
        }


        // Sets the map on all markers in the array.
        function setAllMap(map) {
            for (var i = 0; i < markers.length; i++) {
                markers[i].setMap(map);
            }
        }

        // Removes the markers from the map, but keeps them in the array.
        function clearMarkers() {
            setAllMap(null);
        }

        // Shows any markers currently in the array.
        function showMarkers() {
            setAllMap(map);
        }

        // Deletes all markers in the array by removing references to them.
        function deleteMarkers() {
            clearMarkers();
            markers = [];
        }

        //Save record planner
        function savePlanner(btn) {
            var urn = btn.attr('item-urn');
            var postcode = btn.attr('item-postcode');
            var location_id = btn.attr('item-location-id');
            var record_planner_id = btn.attr('item-record-planner-id');
            var planner_date = $('.planner-form-'+urn).find('input[name="date"]').val();

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
                    getRecords();
                } else {
                    flashalert.danger(response.msg);
                }
            });

        }
    }
}