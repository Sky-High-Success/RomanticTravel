function GooglemapListing(markerArr) {

	var mapDiv, infobox;

	mapDiv = jQuery("#classiads-main-map");

	mapDiv.height(650).gmap3({
		map : {
			options : {
				"draggable" : true,
				"mapTypeControl" : true,
				"mapTypeId" : google.maps.MapTypeId.ROADMAP,
				"scrollwheel" : true,
				"panControl" : true,
				"rotateControl" : false,
				"scaleControl" : true,
				"streetViewControl" : true,
				"zoomControl" : true,
				"maxZoom" : 16,
				"minZoom" : 3
			}
		}

	}, "autofit");

	var map = mapDiv.gmap3("get");

	infobox = new InfoBox({
		
		closeBoxURL : '',
		pixelOffset : new google.maps.Size(-125, -88),
		alignBottom : true,
		enableEventPropagation : true
	});

	mapDiv.delegate('.infoBox .close', 'click', function() {
		infobox.close();
	});

	mapDiv.gmap3({
		marker : {
			values : markerArr,
			options : {
				draggable : false
			},
			cluster : {
				radius : 20,
				// This style will be used for clusters with more than 0 markers
				0 : {
					content : "<div class='cluster cluster-1'>CLUSTER_COUNT</div>",
					width : 62,
					height : 62
				},
				// This style will be used for clusters with more than 20
				// markers
				20 : {
					content : "<div class='cluster cluster-2'>CLUSTER_COUNT</div>",
					width : 82,
					height : 82
				},
				// This style will be used for clusters with more than 50
				// markers
				50 : {
					content : "<div class='cluster cluster-3'>CLUSTER_COUNT</div>",
					width : 102,
					height : 102
				},
				events : {
					click : function(cluster) {

						map.panTo(cluster.main.getPosition());
						map.setZoom(map.getZoom() + 2);
					}
				}
			},

			events : {
				click : function(marker, event, context) {
					map.panTo(marker.getPosition());
					
					var lat1 = marker.getPosition().lat();
					var lon1 = marker.getPosition().lng();
					
					
					infobox.setContent(context.data);
					infobox.open(map, marker);
						
					if(navigator.geolocation)
				        navigator.geolocation.getCurrentPosition(handleGetCurrentPosition, onError);
					
					function handleGetCurrentPosition(location){
						
						var lat2 = location.coords.latitude;
						var lon2 = location.coords.longitude;

						var result = latlongDistance(lat1, lon1, lat2, lon2, "K");
						var km_span = jQuery("<span></span>").addClass("km-style");
						km_span.text(result.toFixed(2));
						jQuery("#marker-info-distance-id").html(km_span);
						jQuery("#marker-info-distance-id").append(" km away from your current location.");

					}
					
					function onError(error){
						switch(error.code) {
					        case error.PERMISSION_DENIED:
					        	 jQuery("#marker-info-distance-id").html("User denied the request for Geolocation.");
					            break;
					        case error.POSITION_UNAVAILABLE:
					        	 jQuery("#marker-info-distance-id").html("Location information is unavailable.");
					            break;
					        case error.TIMEOUT:
					        	 jQuery("#marker-info-distance-id").html("The request to get user location timed out.");
					            break;
					        case error.UNKNOWN_ERROR:
					        	 jQuery("#marker-info-distance-id").html("An unknown geolocation error occurred.");
					            break;
					    }
					}

					// if map is small
					var iWidth = 370;
					var iHeight = 370;
					if ((mapDiv.width() / 2) < iWidth) {
						var offsetX = iWidth - (mapDiv.width() / 2);
						map.panBy(offsetX, 0);
					}
					if ((mapDiv.height() / 2) < iHeight) {
						var offsetY = -(iHeight - (mapDiv.height() / 2));
						map.panBy(0, offsetY);
					}

				}
			}
		}
	}, "autofit");

	// for touch screen only
	if (Modernizr.touch) {
		map.setOptions({
			draggable : false
		});
		var draggableClass = 'inactive';
		var draggableTitle = "Activate map";
		var draggableButton = jQuery('<div class="draggable-toggle-button ' + draggableClass + '">' + draggableTitle + '</div>').appendTo(mapDiv);
		draggableButton.click(function() {
			if (jQuery(this).hasClass('active')) {
				jQuery(this).removeClass('active').addClass('inactive').text("Activate map");
				map.setOptions({
					draggable : false
				});
			} else {
				jQuery(this).removeClass('inactive').addClass('active').text("Deactivate map");
				map.setOptions({
					draggable : true
				});
			}
		});
	}

	jQuery("#advance-search-slider").slider({
		range : "min",
		value : 500,
		min : 1,
		max : 100,
		slide : function(event, ui) {
			jQuery("#geo-radius").val(ui.value);
			jQuery("#geo-radius-search").val(ui.value);

			jQuery(".geo-location-switch").removeClass("off");
			jQuery(".geo-location-switch").addClass("on");
			jQuery("#geo-location").val("on");

			mapDiv.gmap3({
				getgeoloc : {
					callback : function(latLng) {
						if (latLng) {
							jQuery('#geo-search-lat').val(latLng.lat());
							jQuery('#geo-search-lng').val(latLng.lng());
						}
					}
				}
			});

		}
	});

	jQuery("#geo-radius").val(jQuery("#advance-search-slider").slider("value"));
	jQuery("#geo-radius-search").val(jQuery("#advance-search-slider").slider("value"));

	jQuery('.geo-location-button .fa').click(function() {

		if (jQuery('.geo-location-switch').hasClass('off')) {
			jQuery(".geo-location-switch").removeClass("off");
			jQuery(".geo-location-switch").addClass("on");
			jQuery("#geo-location").val("on");

			mapDiv.gmap3({
				getgeoloc : {
					callback : function(latLng) {
						if (latLng) {
							jQuery('#geo-search-lat').val(latLng.lat());
							jQuery('#geo-search-lng').val(latLng.lng());
						}
					}
				}
			});

		} else {
			jQuery(".geo-location-switch").removeClass("on");
			jQuery(".geo-location-switch").addClass("off");
			jQuery("#geo-location").val("off");
		}

	});
	
	function latlongDistance(lat1, lon1, lat2, lon2, unit) {
		var radlat1 = Math.PI * lat1/180;
		var radlat2 = Math.PI * lat2/180;
		var radlon1 = Math.PI * lon1/180;
		var radlon2 = Math.PI * lon2/180;
		var theta = lon1-lon2;
		var radtheta = Math.PI * theta/180;
		var dist = Math.sin(radlat1) * Math.sin(radlat2) + Math.cos(radlat1) * Math.cos(radlat2) * Math.cos(radtheta);
		dist = Math.acos(dist);
		dist = dist * 180/Math.PI;
		dist = dist * 60 * 1.1515;
		if (unit=="K") { dist = dist * 1.609344; }
		if (unit=="N") { dist = dist * 0.8684; }
		return dist;
	}

}
