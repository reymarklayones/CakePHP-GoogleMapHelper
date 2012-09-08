<?php
/*
  CakePHP Google Map V3 - Helper to CakePHP framework that integrates a Google Map in your view
  using Google Maps API V3.
  
	Copyright (c) 2012 Marc Fernandez Girones: marc.fernandezg@gmail.com

	MIT LICENSE:
	Permission is hereby granted, free of charge, to any person obtaining a copy
	of this software and associated documentation files (the "Software"), to deal
	in the Software without restriction, including without limitation the rights
	to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
	copies of the Software, and to permit persons to whom the Software is
	furnished to do so, subject to the following conditions:
	
	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.
	
	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
	THE SOFTWARE.
   
	@author      Marc Fernandez Girones <marc.fernandezg@gmail.com>
	@version     3.0
	@license     OPPL
	 
	Date	     May 13, 2010

  This helper uses the latest Google API V3 so you don't need to provide or get any Google API Key

*/

class GoogleMapHelper extends AppHelper {

	
	//DEFAULT MAP OPTIONS (function map())
	var $defaultId = "map_canvas";																											//Map canvas ID
	var $defaultWidth = "800px";																												//Width of the map
	var $defaultHeight = "800px";																												//Height of the map
	var $defaultStyle = "style";																															//CSS style for the map canvas
	var $defaultZoom = 6;																																//Default zoom
	var $defaultType = 'HYBRID';																												//Type of map (ROADMAP, SATELLITE, HYBRID or TERRAIN)
	var $defaultCustom = "";																														//Any other map option not mentioned before and available for the map. 
																																											//For example 'mapTypeControl: true' (http://code.google.com/apis/maps/documentation/javascript/controls.html)
	var $defaultLatitude = 40.69847032728747;																						//Default latitude if the browser doesn't support localization or you don't want localization
	var $defaultLongitude = -73.9514422416687;																					//Default longitude if the browser doesn't support localization or you don't want localization
	var $defaultLocalize = true;																												//Boolean to localize your position or not
	var $defaultMarker = true;	//Boolean to put a marker in the position or not
	var $defaultMarkerTitle = 'My Position';																													
	var $defaultMarkerIcon = 'http://google-maps-icons.googlecode.com/files/home.png'; 	//Default icon of the marker
	var $defaultMarkerShadow = '';																											//Default shadow for the marker icon
	var $defaultInfoWindow = true;																											//Boolean to show an information window when you click the marker or not
	var $defaultWindowText = 'My Position';																							//Default text inside the information window
		
	//DEFAULT MARKER OPTIONS (function addMarker())
	var $defaultInfoWindowM = true;																											//Boolean to show an information window when you click the marker or not
	var $defaultWindowTextM = 'Marker info window';																											//Default text inside the information window
	var $defaultmarkerTitleM = "Title";																											//
	var $defaultmarkerIconM = "http://labs.google.com/ridefinder/images/mm_20_purple.png";																												//
	var $defaultmarkerShadowM = "http://labs.google.com/ridefinder/images/mm_20_purple.png";																											//
	
	/* 
	* Function map 
	* 
	* This method generates a tag called map_canvas and insert
	* a google maps.
	* 
	* Pass an array with the options listed above in order to customize it
	* 
	* @author Marc Fernandez <marc.fernandezg (at) gmail (dot) com> 
	* @param array $options - options array 
	* @return string - will return all the javascript script to generate the map
	* 
	*/	
	public function map($options = null)
	{
		if( $options != null )
		{
			extract($options);
		}
		if( !isset($id) )			$id = $this->defaultId;
		if( !isset($width) )		$width = $this->defaultWidth;
		if( !isset($height) )		$height = $this->defaultHeight;	
		if( !isset($style) )		$style = $this->defaultStyle;
		if( !isset($zoom) )			$zoom = $this->defaultZoom;			
		if( !isset($type) )			$type = $this->defaultType;
		if( !isset($custom) )		$custom = $this->defaultCustom;		
		if( !isset($localize) )		$localize = $this->defaultLocalize;		
		if( !isset($marker) )		$marker = $this->defaultMarker;
		if( !isset($markerIcon) ) 	$markerIcon = $this->defaultMarkerIcon;
		if( !isset($markerShadow) ) $markerShadow = $this->defaultMarkerShadow;
		if( !isset($markerTitle) ) 	$markerTitle = $this->defaultMarkerTitle;	
		if( !isset($infoWindow) ) 	$infoWindow = $this->defaultInfoWindow;	
		if( !isset($windowText) ) 	$windowText = $this->defaultWindowText;	

		$map = "<div id='$id' style='width:$width; height:$height; $style'></div>";
		$map .="
			<script>
				var markers = new Array();
				var markersIds = new Array();
				var geocoder = new google.maps.Geocoder();
				function geocodeAddress(address, action, map,markerId, markerTitle, markerIcon, markerShadow, windowText) {
				    geocoder.geocode( { 'address': address}, function(results, status) {
				      if (status == google.maps.GeocoderStatus.OK) {
				      	if(action =='setCenter'){
				      		setCenterMap(results[0].geometry.location);
				      	}
				      	if(action =='setMarker'){
				      		setMarker(map,markerId,results[0].geometry.location,markerTitle, markerIcon, markerShadow,windowText, ".($infoWindow? 'true' : 'false').");
				      	}
				      } else {
				        alert('Geocode was not successful for the following reason: ' + status);
				        return null;
				      }
				    });
				}";
		
		$map .= "
			var initialLocation;
		    var browserSupportFlag =  new Boolean();
		    var {$id};
		    var myOptions = {
		      zoom: {$zoom},
		      mapTypeId: google.maps.MapTypeId.{$type}
		      ".(($custom != "")? ",$custom" : "")."
		      
		    };
		    {$id} = new google.maps.Map(document.getElementById('$id'), myOptions);
		";
		$map.="
			function setCenterMap(position){
		";
		if($localize) $map .= "localize();"; 
		else {
			$map .= "{$id}.setCenter(position);";
			if($marker) $map .= "setMarker({$id},'center',position,'{$markerTitle}','{$markerIcon}','{$markerShadow}','{$windowText}', ".($infoWindow? 'true' : 'false').");";
		}
		$map .="
			}
		";
		if(isset($latitude) && isset($longitude)) $map .="setCenterMap(new google.maps.LatLng({$latitude}, {$longitude}));";
		else if(isset($address)) $map .="geocodeAddress('{$address}','setCenter') ; ";
		else $map .="setCenterMap(new google.maps.LatLng({$this->defaultLatitude}, {$this->defaultLongitude}));";
		$map .= "
			function localize(){
		        if(navigator.geolocation) { // Try W3C Geolocation method (Preferred)
		            browserSupportFlag = true;
		            navigator.geolocation.getCurrentPosition(function(position) {
		              initialLocation = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
		              {$id}.setCenter(initialLocation);";
					  if($marker) $map .= "setMarker({$id},'center',initialLocation,'{$markerTitle}','{$markerIcon}','{$markerShadow}','{$windowText}', ".($infoWindow? 'true' : 'false').");";
		                       
		            $map .= "}, function() {
		              handleNoGeolocation(browserSupportFlag);
		            });
		            
		        } else if (google.gears) { // Try Google Gears Geolocation
					browserSupportFlag = true;
					var geo = google.gears.factory.create('beta.geolocation');
					geo.getCurrentPosition(function(position) {
						initialLocation = new google.maps.LatLng(position.latitude,position.longitude);
						{$id}.setCenter(initialLocation);";
					  	if($marker) $map .= "setMarker({$id},'center',initialLocation,'{$markerTitle}','{$markerIcon}','{$markerShadow}','{$windowText}', ".($infoWindow? 'true' : 'false').");";         
		        
		            $map .= "}, function() {
		              handleNoGeolocation(browserSupportFlag);
		            });
		        } else {
		            // Browser doesn't support Geolocation
		            browserSupportFlag = false;
		            handleNoGeolocation(browserSupportFlag);
		        }
		    }
		    
		    function handleNoGeolocation(errorFlag) {
		        if (errorFlag == true) {
		          initialLocation = noLocation;
		          contentString = \"Error: The Geolocation service failed.\";
		        } else {
		          initialLocation = noLocation;
		          contentString = \"Error: Your browser doesn't support geolocation.\";
		        }
		        {$id}.setCenter(initialLocation);
		        {$id}.setZoom(3);
		    }";

		    $map .= "
			function setMarker(map, id, position, title, icon, shadow, content, showInfoWindow){
				var index = markers.length;
				markersIds[markersIds.length] = id;
				markers[index] = new google.maps.Marker({
		            position: position,
		            map: map,
		            icon: icon,
		            shadow: shadow,
		            title:title
		        });
		     	if(content != '' && showInfoWindow){
			     	var infowindow = new google.maps.InfoWindow({
			            content: content
			        });
			     	google.maps.event.addListener(markers[index], 'click', function() {
						infowindow.open(map,markers[index]);
        			});
		        }
		     }";

		$map .= "</script>";
		return $map;
	}
	
	
	/* 
	* Function addMarker 
	* 
	* This method puts a marker in the google map generated with the function map
	* 
	* Pass an array with the options listed above in order to customize it
	* 
	* @author Marc Fernandez <marc.fernandezg (at) gmail (dot) com> 
	* @param array $options - options array 
	* @return string - will return all the javascript script to add the marker to the map
	* 
	*/ 
	function addMarker($map_id, $id, $position, $options = array()){
		if($id == null || $map_id == null || $position == null) return null;
		$geolocation = false;
		// Check if position is array and has the two necessary elements
		// or if is not array that the string is not empty
		if( is_array($position) ){
			if( !isset($position["latitude"]) || !isset($position["longitude"]) )  
				return null;
			$latitude = $position["latitude"];
			$longitude = $position["longitude"];
		}else{
			$geolocation = true;
		}

		extract($options);
		if( !isset($infoWindow) ) 		$infoWindow = $this->defaultInfoWindowM;
		if( !isset($windowText) ) 		$windowText = $this->defaultWindowTextM;
		if( !isset($markerTitle) ) 		$markerTitle = $this->defaultmarkerTitleM;
		if( !isset($markerIcon) ) 		$markerIcon = $this->defaultmarkerIconM;
		if( !isset($markerShadow) ) 	$markerShadow = $this->defaultmarkerShadowM;
		$marker = "<script>";
		
		if(!$geolocation){
			if (!preg_match("/[-+]?\b[0-9]*\.?[0-9]+\b/", $latitude) || !preg_match("/[-+]?\b[0-9]*\.?[0-9]+\b/", $longitude)) return null;

			$marker .= "setMarker({$map_id},'{$id}',new google.maps.LatLng($latitude, $longitude),'{$markerTitle}','{$markerIcon}','{$markerShadow}','{$windowText}')";
		}else{
			if( empty($position) ) return null;
			$marker .= "geocodeAddress('{$position}', 'setMarker', {$map_id},'{$id}','{$markerTitle}','{$markerIcon}','{$markerShadow}','{$windowText}')";
		}
		
		$marker .= "</script>";
		return $marker;
	}
	

}
?>