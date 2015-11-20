/* ==========================================================================
   LOCATION
 ========================================================================== */
//getLocation();
function getLocation() {
	if (navigator.geolocation) {
        return navigator.geolocation.getCurrentPosition(getLocationSuccess, getLocationError,{timeout:10000});
    }
    alert('Geolocation is not enabled on this device. Some features will not work');
	flashalert.danger('Geolocation is not enabled on this device. Some features will not work');
    return false;
}


function getLocationSuccess(position) {
    //store in localstorage
	document.cookie = "lat="+position.coords.latitude;
	document.cookie = "lng="+position.coords.longitude;
	
    var postcode = null,
        locality = null,
        exit     = 0,
        geocoder;
    
    geocoder = new google.maps.Geocoder();
    geocoder.geocode({
        location: new google.maps.LatLng(position.coords.latitude, position.coords.longitude),
        region: 'GB'
    },
    function(result, status) {

    	if (status === 'OK') {
		
            for(var i = 0, length = result.length; i < length; i++) {
                //each result has an address with multiple parts (it's all in the reference)
                for(var j = 0; j < result[i].address_components.length; j++) {
                    var component = result[i].address_components[j];
                    //if the address component has postal code then write it out
                    if(component.types[0] === 'postal_code') {
                        postcode = component.long_name;
                        exit++;
                    }
                    if(component.types[0] === 'locality') {
                        locality = component.long_name;
                        exit++;
                    }
                    if(component.types[0] === 'country') {
                        country = component.long_name;
                        exit++;
                    }
                    if(component.types[0] === 'street_number') {
                    	street_number = component.long_name;
                        exit++;
                    }
                    if(component.types[0] === 'route') {
                    	route = component.long_name;
                        exit++;
                    }
                    if (exit === 2) {
                        break;
                    }
                }
            }
            if(postcode !== null){
				document.cookie = "current_postcode="+postcode;
				document.cookie = "location_error==;expires=Thu, 01 Jan 1970 00:00:00 UTC";
                //$('.current_postcode').text(postcode);
//                $.ajax({
//                    url: helper.baseUrl + 'planner/store_postcode',
//                    type: 'post',
//                    data: {
//                    	add1	 : route + address_number,
//                        lat      : position.coords.latitude,
//                        lng      : position.coords.longitude,
//                        postcode : postcode,
//                        locality : locality,
//                        country  : country
//                    }
//                });  
            } else {
                alert('Cannot find your location');
            }
        } else {
            alert('Location error: ' + status);
        }
    });
}

function getLocationError(error){
	console.log(error);
    var errMsg = 'Unknown Error';
    switch (error.code) {
        case 0:
            errMsg = 'Unknown Error';
            break;
        case 1:
            errMsg = 'Location permission denied by user.';
            break;
        case 2:
            errMsg = 'Position is not available';
            break;
        case 3:
            errMsg = 'Request timeout';
            break;
    }
    document.cookie = "current_postcode=;expires=Thu, 01 Jan 1970 00:00:00 UTC";
	document.cookie = "lat=;expires=Thu, 01 Jan 1970 00:00:00 UTC";
	document.cookie = "lng=;expires=Thu, 01 Jan 1970 00:00:00 UTC";
 	document.cookie = "location_error="+errMsg;

	if(localStorage.location_error){
	flashalert.warning('Geolocation failure: '+localStorage.location_error);	
	}
}

function getAddressByPostcode(postcode){
  if(postcode.length >= 5 && typeof google != 'undefined'){
    var addr = {};
    var geocoder = new google.maps.Geocoder();
    geocoder.geocode({ 'address': postcode, region: 'GB' }, function(results, status){
    	
      if (status == google.maps.GeocoderStatus.OK){
        
    	  if (results.length >= 1) {
    		  for (var ii = 0; ii < results[0].address_components.length; ii++){
    			  var street_number = route = street = city = state = postcodecode = country = formatted_address = '';
    			  var types = results[0].address_components[ii].types.join(",");
    			  if (types == "street_number"){
    				  addr.street_number = results[0].address_components[ii].long_name;
    			  }
    			  if (types == "route" || types == "point_of_interest,establishment"){
    				  addr.route = results[0].address_components[ii].long_name;
    			  }
    			  if (types == "sublocality,political" || types == "locality,political" || types == "neighborhood,political" || types == "administrative_area_level_3,political"){
    				  addr.city = (city == '' || types == "locality,political") ? results[0].address_components[ii].long_name : city;
    			  }
    			  if (types == "administrative_area_level_1,political"){
    				  addr.state = results[0].address_components[ii].short_name;
    			  }
    			  if (types == "postal_code" || types == "postal_code_prefix,postal_code"){
    				  addr.postcodecode = results[0].address_components[ii].long_name;
    			  }
    			  if (types == "country,political"){
    				  addr.country = results[0].address_components[ii].long_name;
    			  }
    		  }
    		  addr.success = true;
    		  $('.current_add1_input').val(addr.route).trigger('change');
          	  $('.current_locality_input').val(addr.city).trigger('change');
          	  $('.current_country_input').val(addr.country).trigger('change');
    	  }
      	} 
    });
  }
}


$(document).on('blur', '.current_postcode_input', function(e) {
    e.preventDefault();
    var postcode = $('.current_postcode_input')[0].value; 
	if (postcode) {
		//getAddressByPostcode(postcode);
	}
});

