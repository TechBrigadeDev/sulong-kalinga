<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Test Google Map</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        #googleMap { width: 100%; height: 400px; border: 1px solid #ccc; }
        .container { max-width: 600px; margin: 30px auto; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Test Google Map & Geocoding</h2>
        <div id="googleMap"></div>
        <input type="hidden" id="latitude" value="{{ $latitude ?? '' }}">
        <input type="hidden" id="longitude" value="{{ $longitude ?? '' }}">
        <div style="margin-top: 15px;">
            <input type="text" id="searchAddress" class="form-control" placeholder="Enter address" style="width: 70%; display: inline-block;">
            <button type="button" id="searchAddressBtn" class="btn btn-primary">Find on Map</button>
        </div>
        <div style="margin-top: 10px;">
            <strong>Latitude:</strong> <span id="latDisplay"></span>
            <strong>Longitude:</strong> <span id="lngDisplay"></span>
        </div>
    </div>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&callback=initMap" async defer></script>
    <script>
        let map, marker, geocoder;

        function initMap() {
            var lat = parseFloat(document.getElementById('latitude').value) || 13.41;
            var lng = parseFloat(document.getElementById('longitude').value) || 122.56;
            var initialPosition = {lat: lat, lng: lng};

            map = new google.maps.Map(document.getElementById('googleMap'), {
                center: initialPosition,
                zoom: 12
            });

            marker = new google.maps.Marker({
                position: initialPosition,
                map: map,
                draggable: true
            });

            geocoder = new google.maps.Geocoder();

            updateLatLngDisplay(lat, lng);

            marker.addListener('dragend', function(e) {
                document.getElementById('latitude').value = e.latLng.lat();
                document.getElementById('longitude').value = e.latLng.lng();
                updateLatLngDisplay(e.latLng.lat(), e.latLng.lng());
            });

            document.getElementById('searchAddressBtn').addEventListener('click', function() {
                geocodeAddress();
            });

            document.getElementById('searchAddress').addEventListener('keyup', function(event) {
                if (event.key === 'Enter') {
                    geocodeAddress();
                }
            });
        }

        function geocodeAddress() {
            var address = document.getElementById('searchAddress').value;
            if (!address) return;
            geocoder.geocode({ 'address': address }, function(results, status) {
                if (status === 'OK') {
                    var location = results[0].geometry.location;
                    map.setCenter(location);
                    marker.setPosition(location);
                    document.getElementById('latitude').value = location.lat();
                    document.getElementById('longitude').value = location.lng();
                    updateLatLngDisplay(location.lat(), location.lng());
                } else {
                    alert('Geocode was not successful for the following reason: ' + status);
                }
            });
        }

        function updateLatLngDisplay(lat, lng) {
            document.getElementById('latDisplay').textContent = lat;
            document.getElementById('lngDisplay').textContent = lng;
        }
    </script>