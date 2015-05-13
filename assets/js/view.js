// JavaScript Document

$(document).ready(function () {
    view.init();

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

function table_columns() {
    var form = "";
    $('.save-modal').show();
    $('.confirm-modal').hide();
    $('.modal-title').text('Choose display data');
    $.ajax({
        url: helper.baseUrl + 'ajax/get_table_columns',
        type: "POST",
        dataType: "JSON"
    }).done(function (response) {
        form = "<form>";
        $.each(response, function (key, fields) {
            form += "<p>" + key + "</p>";
            form += "<select class='fieldpicker' multiple title='Select the " + key + "'>";
            $.each(fields, function (key, field) {
                form += "<option value='" + key + "'>" + field + "</p>";
            });
            form += "</select>";
        });
        form += "</form>";


        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        }).find('.modal-body').html(form);
        $('.fieldpicker').selectpicker();
        $(".save-modal").off('click').show();
        $('.save-modal').on('click', function (e) {
            console.log('load');
        });


    });


}

var view = {
    init: function () {
        $('[data-toggle="tooltip"]').tooltip();
        view.populate_table();
    },
    populate_table: function (table_name) {

        var directionsDisplay = new google.maps.DirectionsRenderer();
        var directionsService = new google.maps.DirectionsService();
        var map;
        var markers = [];
        var markerLocation;
        var bounds = null;
        var records = [];
        var table;
        var infowindow = new google.maps.InfoWindow();
        var geocoder = new google.maps.Geocoder();
        var planner_permission = false;

        google.maps.event.addDomListener(window, 'load', initialize);


        /*******************************************************************/
        /********************** GET and PRINT THE RECORDS *************/
        /*******************************************************************/
       function getRecords() {
            table = $('.data-table').DataTable({
                "oLanguage": {
                    "sProcessing": "<img src='" + helper.baseUrl + "assets/img/ajax-loader-bar.gif'>"
                },
                "dom": '<"row"<"col-xs-12 col-sm-5"<"dt_info"i>r><"col-xs-12 col-sm-7"p>><"row"<"col-lg-12"t>><"clear">',
                "autoWidth": true,
                "scrollX": true,
                "processing": true,
                "serverSide": true,
				"pagingType": "full",
                
				"responsive":true,
                "iDisplayLength": 10,
                "ajax": {
                    url: helper.baseUrl + "records/process_view",
                    type: 'POST',
                    beforeSend: function () {
                        $('.dt_info').hide();
                        records = [];
                    },
                    data: function (d) {
                        d.extra_field = false;
                        d.bounds = getBounds();
                        d.map = $('#map-view-toggle').prop('checked');
                    },
                    complete: function (d) {
                        $('.dt_info').show();
                        $('.tt').tooltip();
                        //Show the records in the map
                        showRecords();
                        planner_permission = d.responseJSON.planner_permission;
                    }
                },
                "deferRender": true,
                "columns": [
                    {"data": "campaign_name"},
                    {"data": "name"},
                    {"data": "fullname"},
                    {"data": "outcome"},
                    {"data": "nextcall"}
                ],
                "columnDefs": [{
                    "targets": [0, 1, 2, 3, 4],
                    "data": null,
                    "defaultContent": "-"
                }],
                "createdRow": function (row, data, dataIndex) {
                    $(row).attr('data-urn', data['urn']);
					$(row).attr('data-modal', 'view-record');
                    $(row).attr('postcode', data['postcode']);
                    $(row).addClass('pointer');
                    if (data['change_type'] == "delete") {
                        $(row).addClass('danger');
                    }
                    records.push(data);
                    $(row).attr('data-id', records.length - 1);
                }
            });

            //filterable columns
            // Setup - adds search input boxes to the footer row
            $('.data-table tfoot th').each(function () {
                var title = $('.data-table thead th').eq($(this).index()).text();
                if (title == "Options") {
                    $(this).html('');
                } else {
                    var search_val = table.column($(this).index()).search();
                    $(this).html('<input class="dt-filter form-control" placeholder="Filter..." value="' + search_val[0] + '" />');
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

            $(document).on('click', '.reset-dt-filter', function (table) {
                localStorage.removeItem('DataTables_' + settings.sInstance + '_' + '/records/view');
                table.search().draw();
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
                    if (typeof table != 'undefined') {
                        table.draw();
                    }
                });
            });

            google.maps.event.addListener(map, 'dragend', function () {
                table.draw();
            });

            google.maps.event.addListener(map, "click", function () {
                infowindow.close();
            });


            $(document).on('click', '.record-btn', function () {
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
                    table.draw();
                }, 2000);
            });

            $(document).on('click', '.get-current-location-btn', function () {
                removeDirections();
                codeCurrentAddress();
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


            //Start animation in the map for the marker deselected in the table
            //$(document).on('click', '.data-table tbody tr', function () {
            //    //openInfoWindow($(this).attr('postcode'));
            //    var record = records[$(this).attr('data-id')];
            //    modal.show_record(record);
            //    $(this).css('color', 'green');
            //});


            //Show map button actions
            $('#map-view-toggle').change(function () {
                if ($(this).prop('checked')) {
                    $(document).on('mouseenter', '.data-table tbody tr', function () {
                        animateMarker($(this).attr('postcode'));
                        $(this).css('color', 'green');
                    });

                    //Start animation in the map for the marker deselected in the table
                    $(document).on('mouseleave', '.data-table tbody tr', function () {
                        removeMarkerAnimation($(this).attr('postcode'));
                        $(this).css('color', 'black');
                    });
                    if (device_type == ('default')) {
                        $(".record-view").removeClass("col-lg-12").addClass("col-lg-6");
                        $(".map-view").show();
						 $(".record-view").find('table').find('tbody').show();
                    }
                    else {

                        $(".record-view").find('table').find('tbody').hide();
                        $(".map-view").show();
                    }
                    //Reload the map
                    google.maps.event.trigger(map, 'resize');
                    map.setCenter(markerLocation.getPosition());
                }
                else {
                    $(document).off('mouseenter', '.data-table tbody tr');
                    $(document).off('mouseleave', '.data-table tbody tr');
                    //Start animation in the map for the marker deselected in the table
                    if (device_type == ('default')) {
                        $(".record-view").removeClass("col-lg-6").addClass("col-lg-12");
                        $(".map-view").hide();
                    }
                    else {
                        $(".record-view").find('table').find('tbody').show();
                        $(".map-view").hide();
                        removeDirections();
                    }
                }
                table.draw();
            });

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
                        table.draw();
                    }, 2000);
                });
            }
        }

        function getDirections(destination) {
            var start = markerLocation.getPosition();
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
                $.each(records, function (index, value) {
                    addMarker(value);
                });
            }
        }

        //Animate a marker icon
        function animateMarker(postcode) {
            $.each(markers, function (index, marker) {
                if (marker.postcode == postcode) {
                    if (marker.getAnimation() != null) {
                        marker.setAnimation(null);
                    } else {
                        marker.setAnimation(google.maps.Animation.BOUNCE);
                    }
                }
            });
        }

        //Remove marker animation icon
        function removeMarkerAnimation(postcode) {
            $.each(markers, function (index, marker) {
                if (marker.postcode == postcode) {
                    if (marker.getAnimation() != null) {
                        marker.setAnimation(null);
                    }
                }
            });
        }

        //Open infowindow for a marker
        function openInfoWindow(postcode) {
            var contentString = "";
            $.each(markers, function (index, marker) {
                if (marker.postcode == postcode) {
                    infowindow.close();
                    infowindow.setContent(marker.content);
                    infowindow.open(map, marker);
                }
            });
        }

        // Add a marker to the map and push to the array.
        function addMarker(value) {
            var marker_color = "|"+intToARGB(hashCode(value.attendee));
            var marker_text_color = "|FFFFFF";
            var character = "|"+(value.attendee).substr(0, 1);
            var pin_style = (((planner_permission == true))&&(value.record_planner_id)?"pin_star":"pin");
            var star_color = (((planner_permission == true))&&(value.record_planner_id)?"|FCEF04":"");
            var map_pin_style = "d_map_xpin_letter";


            var planner_info = '';
            if (planner_permission == true) {
                planner_info =
                    '<b>Planner: </b>' +
                    '<span style="margin-right: 5px;">' + (value.record_planner_id?(value.planner_user + ' on ' + value.planner_date):'') + '</span>' +
                    '<a href="#" class="btn btn-info btn-sm glyphicon glyphicon-time planner-btn" item-urn="'+value.urn+'" item-planner-date="'+(value.planner_date?value.planner_date:'')+'"></a>';
            }

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
                position: new google.maps.LatLng(value.lat, value.lng),
                map: map,
                title: value.name,
                postcode: value.postcode,
                content: contentString,
                icon: "http://chart.apis.google.com/chart?chst="+map_pin_style+"&chld=" + pin_style + character + marker_color + marker_text_color + star_color
            });

            google.maps.event.addListener(marker, 'click', function () {
                infowindow.close();
                infowindow.setContent(contentString);
                infowindow.open(map, marker);
            });

            //Show in the table the record selected in the map

            google.maps.event.addListener(marker, 'mouseover', function () {
                $('.data-table tbody').find("[postcode='" + marker.postcode + "']").css('color', 'green');
            });

            //Hide in the table the record deselected in the map
            google.maps.event.addListener(marker, 'mouseout', function () {
                $('.data-table tbody').find("[postcode='" + marker.postcode + "']").css('color', 'black');
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
                    var currentPage = table.page();
                    table.page(currentPage).draw(false);
                } else {
                    flashalert.danger(response.msg);
                }
            });

        }
    }
}

/*
var modal = {
	  
    default_buttons: function () {
        $('#modal').find('.modal-footer .btn').remove();
        $('#modal').find('.modal-footer').append('<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>');
        $('#modal').find('.modal-footer').append('<button class="btn btn-primary confirm-modal" type="button">Confirm</button>');
    },
    clear_buttons: function () {
        $('#modal').find('.modal-footer .btn').remove();
    },
  show_record: function (value) {
        $('.modal-title').html('<h3 style="color: grey" id="firstHeading" class="firstHeading">URN: ' + value.urn + " [" + value.campaign_name + ']</h3>');
        var modal_html = "";
        var contentString =
            '<div id="content">' +
            '<div id="siteNotice">' +
            '</div>' +
            '<div id="bodyContent">' +
            '<p class="pull-right">' +
            '<span><a class="btn btn-success record-btn" item-postcode="' + value.postcode + '" href="#">Navigate </a></span>' +
            '</p>' +
            '<p><b>Comapny: </b>' + (value.name ? value.name : '') + '</p>' +
            '<p><b>Contact: </b>' + (value.fullname ? value.fullname : '') + '</p>' +
            '<p><b>Outcome: </b>' + (value.outcome ? value.outcome : '') + '</p>' +
            '<p><b>Next Call: </b>' + (value.nextcall ? value.nextcall : '') + '</p>' +
            '<p><b>Last Updated: </b>' + (value.date_updated ? value.date_updated : '') + '</p>' +
            '<p><b>Postcode: </b>' + (value.postcode ? (value.postcode + '(' + (value.lat ? value.lat : '-') + ',' + (value.lng ? value.lng : '-') + ')') : '') + '</p>' +
            '<p><b>Website: </b><a target="_blank" href="' + value.website + '">' + value.website + '</a></p>' +
            '</div>' +
            '</div>';
        modal_html += contentString;
        $('#modal').find('.modal-body').html(modal_html);
        modal.clear_buttons();
        $('#modal').find('.modal-footer').append('<button data-dismiss="modal" class="btn btn-default close-modal pull-left" type="button">Close</button>');
        $('#modal').find('.modal-footer').append('<a class="btn btn-primary" href="' + helper.baseUrl + 'records/detail/' + value.urn + '">View Record</a>');
        modal.show_modal();
    },
    show_modal: function () {
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        });
    }
	
}*/