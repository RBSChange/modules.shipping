jQuery(document).ready(function() {
	if(typeof ChangeContext == "undefined") {ChangeContext = {};}
	var mapNode = document.getElementById('relays-map');

	var center = new google.maps.LatLng(mapNode.getAttribute('data-latitude'), mapNode.getAttribute('data-longitude'));
	var myOptions = { zoom: parseInt(mapNode.getAttribute('data-zoom')), center: center, mapTypeId: google.maps.MapTypeId.ROADMAP };
	var map = new google.maps.Map(mapNode, myOptions);
	
	if (navigator.geolocation)
	{
		jQuery('.locate-me').click(function () {
			jQuery('.locate-me-indicator').css('visibility', 'visible');
			navigator.geolocation.getCurrentPosition(function (position) {
				var latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
				var geocoder = new google.maps.Geocoder();
				geocoder.geocode({'latLng': latlng}, function(results, status) {
					if (status == google.maps.GeocoderStatus.OK)
					{
						if (results[1])
						{
							var addressComponents = results[1].address_components;
							
							var streetNumber = '';
							var route = '';
							for (var i = 0; i < addressComponents.length; i++)
							{
								if (addressComponents[i].types == 'locality,political')
								{
									jQuery('#relay-city').val(addressComponents[i].long_name);
								}
								if (addressComponents[i].types == 'country,political')
								{
									jQuery('#relay-country').val('');
									jQuery('#relay-country-code').val(addressComponents[i].short_name);
								}
								if (addressComponents[i].types == 'postal_code')
								{
									jQuery('#relay-zipcode').val(addressComponents[i].long_name);
								}
								if (addressComponents[i].types == 'street_number')
								{
									streetNumber = addressComponents[i].long_name;
								}
								if (addressComponents[i].types == 'route')
								{
									route = addressComponents[i].long_name;
								}
							}
							
							jQuery('#relay-address').val(streetNumber + ' ' + route);
							
						}
						else { alert("${trans:m.shipping.fo.no-result,ucf,js}"); }
					}
					else { alert("${trans:m.shipping.fo.geocoder-failed,ucf,lab,js} " + status); }
				});
				jQuery('.locate-me-indicator').css('visibility', 'hidden');
			});
		});
	};
	
	ChangeContext.map = map;
	ChangeContext.locationcoord = center;
	
    var homeMarker = new google.maps.Marker({
        position: center,
        map: map,
        icon: '/media/frontoffice/marker-home.png'
    });
    
    var c = map.getCenter();
	jQuery('#relay-over-infos').html('');
	jQuery('#relay-map-center').val(c.toUrlValue()); 
	jQuery('#relay-map-zoom').val(map.getZoom()); 
	
	for (var i = 0; i < markers.length; i++)
	{
		var markerdata = markers[i];
		var marker = gmaps_addMarkerToMap(map, markerdata.latitude, markerdata.longitude, markerdata.name, { ref: markerdata.ref, index: i})
		
		relay_attachMarkerEvent(marker);
	}

});

function relay_attachMarkerEvent(marker)
{
	google.maps.event.addListener(marker, 'click', function () 
		{
			var m = markers[marker.index];	
			jQuery('#select-new-relay-form-relayRef').val(m.ref);
			jQuery('#select-new-relay-form-relayCountryCode').val(m.countryCode);
			jQuery('#select-new-relay-form-relayName').val(m.name);
			jQuery('#select-new-relay-form-relayAddressLine1').val(m.addressLine1);
			jQuery('#select-new-relay-form-relayAddressLine2').val(m.addressLine2);
			jQuery('#select-new-relay-form-relayAddressLine3').val(m.addressLine3);
			jQuery('#select-new-relay-form-relayZipCode').val(m.zipCode);
			jQuery('#select-new-relay-form-relayCity').val(m.city);
			jQuery('#select-new-relay-form-submit').click(); 
		});
		
	google.maps.event.addListener(marker, 'mouseover', function () 
		{ 
			var m = markers[marker.index];	
			jQuery('#select-new-relay-form-relayRef').val(m.ref);
			jQuery('#select-new-relay-form-relayCountryCode').val(m.countryCode);
			jQuery('#select-new-relay-form-relayName').val(m.name);
			jQuery('#select-new-relay-form-relayAddressLine1').val(m.addressLine1);
			jQuery('#select-new-relay-form-relayAddressLine2').val(m.addressLine2);
			jQuery('#select-new-relay-form-relayAddressLine3').val(m.addressLine3);
			jQuery('#select-new-relay-form-relayZipCode').val(m.zipCode);
			jQuery('#select-new-relay-form-relayCity').val(m.city);
			var html = jQuery('#' + m.ref).html();
			jQuery('#relay-over-infos').html(html);
		});
}

function relay_SelectRelayByRef(ref, countryCode, name, addressLine1, addressLine2, addressLine3, zipCode, city)
{
	jQuery('#select-new-relay-form-relayRef').val(ref);
	jQuery('#select-new-relay-form-relayCountryCode').val(countryCode);
	jQuery('#select-new-relay-form-relayName').val(name);
	jQuery('#select-new-relay-form-relayAddressLine1').val(addressLine1);
	jQuery('#select-new-relay-form-relayAddressLine2').val(addressLine2);
	jQuery('#select-new-relay-form-relayAddressLine3').val(addressLine3);
	jQuery('#select-new-relay-form-relayZipCode').val(zipCode);
	jQuery('#select-new-relay-form-relayCity').val(city);
	jQuery('#select-new-relay-form-submit').click();
}

function relay_CenterToHome()
{
	ChangeContext.map.setCenter(ChangeContext.locationcoord);
}