$.ajaxSetup({
  headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

// Initialize the map
var map = L.map('map').setView([10.431573, 124.99499], 12);
var googleHybrid = L.tileLayer('http://{s}.google.com/vt?lyrs=s,h&x={x}&y={y}&z={z}', {
  minZoom: 12,
  maxZoom: 20,
  subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
});
googleHybrid.addTo(map);



// $('#click').click(function (e) { 
//   e.preventDefault();
//   startTracking(); 
// });

// var watchId;
// var marker;
// function startTracking() {
//   watchId = navigator.geolocation.watchPosition(
//     function(position) {
//       var lat = position.coords.latitude;
//       var long = position.coords.longitude;
//       console.log("Latitude: " + lat + ", Longitude: " + long);
//       if(marker){
//         map.removeLayer(marker);
//       }
//       marker = L.marker([lat, long]).addTo(map);
//     },
//     function(error) {
//       switch(error.code) {
//         case error.PERMISSION_DENIED:
//           console.log("User denied the request for Geolocation.");
//           break;
//         case error.POSITION_UNAVAILABLE:
//           console.log("Location information is unavailable.");
//           break;
//         case error.TIMEOUT:
//           console.log("The request to get user location timed out.");
//           break;
//         case error.UNKNOWN_ERROR:
//           console.log("An unknown error occurred.");
//           break;
//       }
//     },
//     {
//       enableHighAccuracy: true,
//       timeout: 5000, // The maximum time (in ms) that the device should wait before calling the error callback if the location cannot be determined
//       maximumAge: 0 // No cached data (always fresh location)
//     }
//   );
// }

// // To stop the tracking when needed
// function stopTracking() {
//   if (watchId) {
//     navigator.geolocation.clearWatch(watchId);
//   }
// }
