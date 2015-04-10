var planner = {
    init: function () {
        planner.reload_table('planner-table');
        //planner.map();
    },
    reload_table: function (table_name) {
        var table = "<table class='table table-striped table-bordered table-hover data-table'>" +
                "<thead>" +
                    "<tr>" +
                        "<th>Date</th>" +
                        "<th>Company</th>" +
                        "<th>Attendee</th>" +
                        "<th>Date Added</th>" +
                        "<th>Postcode</th>" +
                    "</tr>" +
                "</thead>" +
                "<tfoot>" +
                    "<tr>" +
                        "<th>Time</th>" +
                        "<th>Company</th>" +
                        "<th>Attendee</th>" +
                        "<th>Date Added</th>" +
                        "<th>Postcode</th>" +
                    "</tr>" +
                "</tfoot>" +
            "</table>";


        $('#planner-table').html(table);
        planner.populate_table(table_name);

        $(document).on('click', '.data-table tbody tr', function () {
            modal.show_planner($(this).attr('data-id'));
        });
    },
    populate_table: function (table_name) {

        var directionsDisplay;
        var directionsService = new google.maps.DirectionsService();
        var map;
        var markers = [];
        var bounds = null;
        var appointments = [];
        var table;

        google.maps.event.addDomListener(window, 'load', initialize);


        /*******************************************************************/
        /********************** GET and PRINT THE APPOINTMENTS *************/
        /*******************************************************************/
        function getAppointments() {
            table = $('.data-table').DataTable({
                "dom": '<"top">p<"dt_info"i>rt<"bottom"lp><"clear">',
                "oLanguage": {
                    "sProcessing": "<img src='" + helper.baseUrl + "assets/img/ajax-loader-bar.gif'>"
                },
                "autoWidth": false,
                "processing": true,
                "serverSide": true,
                //ordering:  false,
                "iDisplayLength": 10,
                stateSave: true,
                responsive: true,
                "ajax": {
                    url: helper.baseUrl + "planner/planner_data",
                    type: 'POST',
                    beforeSend: function () {
                        $('.dt_info').hide();
                        appointments = [];
                    },
                    data: function (d) {
                        d.extra_field = false;
                        d.bounds = getBounds();
                    },
                    complete: function () {
                        $('.dt_info').show();
                        $('.tt').tooltip();
                        //Show the appointments in the map
                        showAppointments();
                    }
                },
                "columns": [{
                    "data": "start"
                }, {
                    "data": "name"
                }, {
                    "data": "attendee"
                }, {
                    "data": "date_added"
                }, {
                    "data": "postcode"
                }],
                "columnDefs": [{
                    "targets": [0, 1, 2, 3, 4],
                    "data": null,
                    "defaultContent": "na"
                }
                ],
                "createdRow": function (row, data, dataIndex) {
                    $(row).attr('data-id', data['planner_id']);
                    $(row).addClass('pointer');
                    if (data['change_type'] == "delete") {
                        $(row).addClass('danger');
                    }
                    appointments.push(data);
                }
            });

            $(document).on('click', '.reload-table', function () {
                table.draw();
            });

            //filterable columns
            // Setup - adds search input boxes to the footer row
            $('.data-table tfoot th').each(function () {
                var title = $('.data-table thead th').eq($(this).index()).text();
                if (title == "Options") {
                    $(this).html('');
                } else {
                    var search_val = table.column($(this).index()).search();
                    //console.log(table.column($(this).index()).search());
                    $(this).html('<input class="dt-filter form-control" style="width:100%" placeholder="Filter..." value="' + search_val[0] + '" />');
                }
            });

            // Apply the search
            table.columns().eq(0).each(function (colIdx) {
                $('input', table.column(colIdx).footer()).on('keyup change', function () {
                    table
                        .column(colIdx)
                        .search(this.value)
                        .draw();
                });
            });
            //this moves the search input boxes to the top of the table

            var r = $('.data-table tfoot tr');
            r.find('th').each(function () {
                $(this).css('padding', 8);
            });
            $('.data-table thead').append(r);
            $('#search_0').css('text-align', 'center');
        }


        /*******************************************************************/
        /********************** MAP ****************************************/
        /*******************************************************************/
        function initialize() {
            var lat = 53.499501;
            var lng = -2.208951;
            var myLatlng = new google.maps.LatLng(lat,lng);
            var mapOptions = {
                zoom: 12,
                center: myLatlng
                //mapTypeId: google.maps.MapTypeId.TERRAIN
            }
            map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

            google.maps.event.addListener(map, 'zoom_changed', function () {
                google.maps.event.addListenerOnce(map, 'bounds_changed', function (e) {
                    //bounds = getBounds();

                    table.draw();
                });
            });

            google.maps.event.addListener(map, 'dragend', function () {
                //bounds = getBounds();

                table.draw();
            });

            //Wait until the map is loaded
            setTimeout(function(){
                getAppointments();
            }, 3000);


        }

        //Get current bounds
        function getBounds() {
            var bounds_obj = map.getBounds();

            neLat = (bounds_obj)?bounds_obj.getNorthEast().lat():null;
            neLng = (bounds_obj)?bounds_obj.getNorthEast().lng():null;
            swLat = (bounds_obj)?bounds_obj.getSouthWest().lat():null;
            swLng = (bounds_obj)?bounds_obj.getSouthWest().lng():null;

            bounds = {'neLat': neLat,'neLng': neLng, 'swLat': swLat, 'swLng': swLng};

            return bounds;
        }

        //Show the appointments in the map
        function showAppointments() {
            deleteMarkers();
            $.each(appointments, function(index, value) {
                addMarker(value);
            });
        }

        // Add a marker to the map and push to the array.
        function addMarker(value) {
            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(value.lat,value.lng),
                map: map,
                title: value.name
            });

            var contentString =
                '<div id="content">'+
                    '<div id="siteNotice">'+
                    '</div>'+
                    '<h2 id="firstHeading" class="firstHeading">'+value.name+'</h2>'+
                    '<div id="bodyContent">'+
                        '<p><b>Start: </b>' + value.start + '</p>'+
                        '<p><b>Attendee: </b>' + value.attendee + '</p>' +
                        '<p><b>Date added: </b>' + value.date_added + '</p>' +
                        '<p><b>Postcode: </b>' + value.postcode + '(' + value.lat + ',' + value.lng + ')' + '</p>' +
                        '<p><b>Website: </b><a taget="_blank" href="' + value.website + '"/>' + value.website + '</p>' +
                    '</div>'+
                '</div>';


            var infowindow = new google.maps.InfoWindow({
                content: contentString
            });

            google.maps.event.addListener(marker, 'click', function() {
                infowindow.open(map,marker);
            });
            markers.push(marker);
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

        function calcRoute(start, end) {
            var request = {
                origin:start,
                destination:end,
                travelMode: google.maps.TravelMode.DRIVING
            };

            directionsService.route(request, function(response, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                    directionsDisplay.setDirections(response);
                }
            });
        }
    },


    map: function() {
//        var directionsDisplay;
//        var directionsService = new google.maps.DirectionsService();
//        var map;
//
//        function initialize() {
//            var lat = 53.499501;
//            var lng = -2.208951;
//            var myLatlng = new google.maps.LatLng(lat,lng);
//            var mapOptions = {
//                zoom: 12,
//                center: myLatlng
//            }
//            map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
//
////        if ({{ postcodeDest|json_encode|raw }}) {
////            var destination = {{ postcodeDest|json_encode|raw }};
////            var origin = {{ (appointment.appointmentDetail.address.postcode ~ "," ~ appointment.appointmentDetail.address.town ~ "," ~ appointment.appointmentDetail.address.country)|json_encode|raw }};
////            directionsDisplay = new google.maps.DirectionsRenderer();
////            calcRoute(origin, destination);
////            directionsDisplay.setMap(map);
////        }
////        else {
////
////            var marker = new google.maps.Marker({
////                position: myLatlng,
////                map: map,
////                title: '{{ appointment.appointmentDetail.title }}'
////            });
////
////            var infowindow = new google.maps.InfoWindow({
////                content: '<span><b>Appointment</b><br></span>Recruiter'
////            });
////
////            google.maps.event.addListener(marker, 'click', function() {
////                infowindow.open(map,marker);
////            });
////        }
//
//            var marker = new google.maps.Marker({
//                position: map.getCenter(),
//                map: map,
//                title: 'Click to zoom'
//            });
//
//            //google.maps.event.addListener(map, 'zoom_changed', function() {
//            //    var zoomLevel = map.getZoom();
//            //    //map.setCenter(myLatLng);
//            //    //infowindow.setContent('Zoom: ' + zoomLevel);
//            //    console.log(marker.getPosition());
//            //});
//            //google.maps.event.addListener(map, "rightclick", function(event) {
//            //    var lat = event.latLng.lat();
//            //    var lng = event.latLng.lng();
//            //    // populate yor box/field with lat, lng
//            //    alert("Lat=" + lat + "; Lng=" + lng);
//            //});
//
//            google.maps.event.addListener(map, 'zoom_changed', function () {
//                google.maps.event.addListenerOnce(map, 'bounds_changed', function (e) {
//                    //my_zoom_handler(); // do your job here
//                    var ne = map.getBounds().getNorthEast();
//                    var sw = map.getBounds().getSouthWest();
//                    var bounds = map.getBounds();
//                    if(bounds.contains(marker.position)) {
//                        console.log("Marker"+ marker.position +" - matched");
//                    }
//                    //planner.reload_table('planner-table', bounds);
//                    table.draw();
//                });
//            });
//
//            google.maps.event.addListener(map, 'dragend', function () {
//                //google.maps.event.addListenerOnce(map, 'bounds_changed', function (e) {
//                //my_zoom_handler(); // do your job here
//                var ne = map.getBounds().getNorthEast();
//                var sw = map.getBounds().getSouthWest();
//                var bounds = map.getBounds();
//                if(bounds.contains(marker.position)) {
//                    console.log("Marker"+ marker.position +" - matched");
//                }
//                //});
//            });
//        }
//
//        function calcRoute(start, end) {
//            var request = {
//                origin:start,
//                destination:end,
//                travelMode: google.maps.TravelMode.DRIVING
//            };
//
//            directionsService.route(request, function(response, status) {
//                if (status == google.maps.DirectionsStatus.OK) {
//                    directionsDisplay.setDirections(response);
//                }
//            });
//        }
//
//        google.maps.event.addDomListener(window, 'load', initialize);
    }

}

var modal = {
    default_buttons: function () {
        $('#modal').find('.modal-footer .btn').remove();
        $('#modal').find('.modal-footer').append('<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>');
        $('#modal').find('.modal-footer').append('<button class="btn btn-primary confirm-modal" type="button">Confirm</button>');
    },
    clear_buttons: function () {
        $('#modal').find('.modal-footer .btn').remove();
    },
    show_planner: function (id) {
        $('.modal-title').text('planner #' + id);
        $.ajax({
            url: helper.baseUrl + 'planner/planner_modal',
            type: "POST",
            dataType: "JSON",
            data: {id: id}
        }).done(function (response) {
            if (response.success) {
                var modal_html = "";
                modal_html += "<p>planner was set for <b>" + response.data.planner.date_formatted + "</b></p>";
                modal_html += "<p><ul>";
                modal_html += "<li><b>Title:</b> " + response.data.planner.title + "</li>"
                modal_html += "<li><b>Notes:</b> " + response.data.planner.text + "</li>"
                modal_html += "</ul></p>";
                $('#modal').find('.modal-body').html(modal_html);
                modal.clear_buttons();
                $('#modal').find('.modal-footer').append('<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>');
                $('#modal').find('.modal-footer').append('<a class="btn btn-primary" href="' + helper.baseUrl + 'records/detail/' + response.data.planner.urn + '">View Record</a>');
                modal.show_modal();
            }
        });
    },
    show_modal: function () {
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        });
    }
}