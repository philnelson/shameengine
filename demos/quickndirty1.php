<!DOCTYPE html>
<html>
<head>
	<title>Change Camp Prototype</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<!-- Framework CSS -->
	<link rel="stylesheet" href="../css/blueprint/blueprint/screen.css" type="text/css" media="screen, projection">
	<link rel="stylesheet" href="../css/blueprint/blueprint/print.css" type="text/css" media="print">
	<!--[if IE]><link rel="stylesheet" href="../css/blueprint/blueprint/ie.css" type="text/css" media="screen, projection"><![endif]-->
	<link rel="stylesheet" href="style.css" type="text/css" media="screen" />
	<script src="../js/jquery-1.3.1.min.js" type="text/javascript"></script>
	<script type="text/javascript">
		$(document).ready(function(){
 			$('#geoError').hide();
		});
	</script>
	<?php
		if($_SERVER['SERVER_ADDR'] == "127.0.0.1")
		{
	?>
	<script src="http://maps.google.com/maps?file=api&amp;v=2.x&amp;key=ABQIAAAAhKmW6TN0CiPqXCUjd9Y9sxTgtaT0YcsA7oWKq5sA2DUkHZo1FBTw33sYJQHUT6mFpnjr7QYaty53_w" type="text/javascript"></script>
	<?php
		}
		else
		{
		?>
	<script src="http://maps.google.com/maps?file=api&amp;v=2.x&amp;key=ABQIAAAAhKmW6TN0CiPqXCUjd9Y9sxRzVltsHy9HH76x-oMdQWJaEp9p3hRB-tPSE4WzlqE13pC-VIYUivGHtQ" type="text/javascript"></script>
		<?php
		}
		?>
	<script type="text/javascript">
		var map;
	    var geocoder;
	    var address;
	
	    function initialize() {
	    var center = new GLatLng(43.670233, -79.386755);
	      map = new GMap2(document.getElementById("map"));
	      map.enableContinuousZoom();
	      map.setCenter(center, 15);
	      map.addControl(new GLargeMapControl);
	      GEvent.addListener(map, "click", getAddress);
	      geocoder = new GClientGeocoder();
	      var marker = new GMarker(center,{draggable: true});
	    
	    <?php
	    	if($_GET['address'])
	    	{
	    	?>
	    	frontGeocode("<?=$_GET['address']?>");
	    	<?php
	    	}
	    ?>
	    
	    }
	    
	    
	    function scopeZoom(level)
	    {
	    	map.zoomIn();
	    	//alert(level:value);
	    }
	    
	    function frontGeocode(address) 
	    {
			if (geocoder) {
				geocoder.getLocations(address, reverseGeocode);
			}
		}
	    
	    function getAddress(overlay, latlng) 
	    {
	    	overlayType = typeof(overlay);
			if (latlng != null) {
				// if we're getting this from the click
				address = latlng;
				geocoder.getLocations(latlng, reverseGeocode);
			}
			// Make sure we haven't just closed a box or something else. This is SUPER sloppy, right here. Please fix.
			if(overlayType != 'object')
			{
				geocoder.getLocations(overlay, reverseGeocode);
			}
			$('#geoError').hide('fast');
			//alert(overlayType);
			//alert(latlng + " " + overlay);
	    }
	
		function reverseGeocode(response) 
		{
			map.clearOverlays();
			if (!response || response.Status.code != 200) 
			{
				$('#geoError').fadeIn('fast');
			} 
			else 
			{
				place = response.Placemark[0];
				$('#address').val(place.address);
				point = new GLatLng(place.Point.coordinates[1],
				place.Point.coordinates[0]);
				marker = new GMarker(point,{draggable: true});
				map.addOverlay(marker);
				marker.openInfoWindowHtml(
				'<b>orig latlng:</b>' + response.name + '<br/>' + 
				'<b>latlng:</b>' + place.Point.coordinates[1] + "," + place.Point.coordinates[0] + '<br>' +
				'<b>Status Code:</b>' + response.Status.code + '<br>' +
				'<b>Status Request:</b>' + response.Status.request + '<br>' +
				'<b>Address:</b>' + place.address + '<br>' +
				'<b>Accuracy:</b>' + place.AddressDetails.Accuracy + '<br>' +
				'<b>Country code:</b> ' + place.AddressDetails.Country.CountryNameCode);
				GEvent.addListener(marker, "dragstart", function() {
					marker.closeInfoWindow();
				});
			GEvent.addListener(marker, "dragend", getAddress);
			map.panTo(point, 15);
			}
	  
    	}
	
    </script>
</head>
<body onload="initialize()" onunload="GUnload()">
<div class="container" id="airlock">
	<div class="span-24 last">
		<div class="span-24">
			<div id="column-a" class="span-10">
				<form id="findLocation" name="findLocation" action="#">
					<label for="address">I live at or near</label>
					<input type="text" class="span-10" id="address" name="address" onblur="frontGeocode(this.value); return false" value="The Mod Club, Toronto, ON" />
					<p class="error" id="geoError">Sorry! We couldn't find that location.</p>
				</form>
				<p class="and">- and -</p> 
				<form id="helloForm">
					<label for="message">I want to say</label>
					<textarea name="message" class="span-10" style="height: 75px;"></textarea>
					<div class="span-5">
						<p id="charactersLeft">140 characters left</p>
					</div>
					<div class="subtext span-5 last">
						<label for="scope">about</label>
						<select name="scope" onchange="scopeZoom(this.value) return false;"><option value="16">my street</option><option value="13">my city</option><option value="9">my province</option><option vaue="2">my country</option></select>
					</div>
				</form>
			</div>
			<div class="span-14 last">
				<div id="map" style="width:500px;height:309px"></div>
				<p>Click anywhere to put your pin in the map.</p>
			</div>
		</div>
		<div class="span-24">
			<h1>Try:</h1>
			<ul>
				<li>Typing something into the "I live at or near" field, then click on the "I want to say" box. The address should change and the map marker should pop in.</li>
				<li>Drag the marker! It will change the "I live near field"</li>
				<li>Change the "I live near" field again. Once you unfocus the text area (meaning the typing cursor leaves it, not just the mouse) it will move the map again.</li>
			</ul>
			<h1>Notes:</h1>
			<ul>
				<li>Almost entirely cribbed from <a href="http://code.google.com/apis/maps/documentation/examples/geocoding-reverse.html">this example in google's documentation</a></li>
				<li>The actual "problem" form doesn't do anything still. I know.</li>
				<li>All work and no play makes jack a dull boy.</li>
				<li>This took about 6 hours.</li>
			</ul>
			<h1>Todo:</h1>
			<ul>
				<li>There's an annoying bug where it gives you a "can't geocode this" error (602) when you click the info window.</li>
				<li>The character counter is broken.</li>
			</ul>
		</div>
	</div>
</div>
</body>
</html>
