$.ajaxSetup({
  headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

let barangaysRow1;
let map;
let googleSat;
$(document).ready(function () {
   map = L.map('map').setView([10.449444, 125.008231], 12);
   googleSat = L.tileLayer('http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
    minZoom: 12,
    maxZoom: 20,
    subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
  });
  googleSat.addTo(map);
  addBoundaries(map, barangaysRow1);
});

var sound;
function playNotificationSound() {
  sound = document.getElementById('notificationSound');
  
  sound.muted = false; 
  sound.loop = true;

  sound.play().catch(function(error) {
    console.log('Audio play failed:', error);
  });

  console.log('sound is playing');
}


const existingReportIds = new Set();

let callCount = 0;

function createPulsingMarkerAndCircle(lat, long, options, accuracy) {
  console.log('Creating marker at:', lat, long, options, accuracy);
  callCount++; // Increment the counter each time the function is called
  console.log(`Function called ${callCount} times.`); // Log the count for debugging

  if (existingReportIds.has(options.id)) return;

  existingReportIds.add(options.id);

  const baseIconUrl = 'assets/img/marker-icons/red-marker.png';

  playNotificationSound();

  let scale = 1;
  let increasing = true;
  const marker = L.marker([lat, long], {
    icon: L.icon({ iconUrl: baseIconUrl, iconSize: [35, 35] })
  }).addTo(map);

  const circle = L.circle([lat, long], { radius: accuracy, color: 'red', fillOpacity: 0.2 }).addTo(map);

  const featureGroup = L.featureGroup([marker, circle]).addTo(map);
  map.fitBounds(featureGroup.getBounds());


  const pulseDuration = 500;
  const frameRate = 60;
  const steps = Math.round(pulseDuration / (1000 / frameRate));
  const scaleIncrement = 0.1 / steps;

  function pulse() {
    scale += increasing ? scaleIncrement : -scaleIncrement;

    if (scale >= 1.1) {
      increasing = false;
    } else if (scale <= 1) {
      increasing = true;
    }

    marker.setIcon(L.icon({ iconUrl: baseIconUrl, iconSize: [35 * scale, 35 * scale] }));

    circle.setRadius(accuracy * scale);

    requestAnimationFrame(pulse);
  }

  requestAnimationFrame(pulse);

  marker.bindPopup(`
    <div class="popup-content">
      <div class="mb-3">
        <h5 class="text-center" style="font-weight: bold; font-size: 20px;">New Report</h5>
      </div> 
      <div class="mb-3">
        <span>Crime Committed:</span> <span style="font-weight: bold;">${options.crime_name}</span><br>
        <span>Crime Location:</span> <span style="font-weight: bold;">${options.crimeLocation}</span><br>
        <span>Crime Description:</span> <span style="font-weight: bold;">${options.crimeDescription}</span><br>
        <span>Reported on:</span> <span style="font-weight: bold;">${options.reportedDate}</span><br>
        <span>Reported on:</span> <span style="font-weight: bold;">${options.reportedTime}</span><br>
        <span>Reported By:</span> <span style="font-weight: bold;">${options.reportedBy}</span><br>
      </div>
      <div class="text-center">
        <button class="btn btn-primary" id="btnTakeAction" onclick="reportReceived(${options.id}, ${marker._leaflet_id}, ${circle._leaflet_id})"> Take Action</button>
      </div>
      
    </div>
  `);
}
let fetchReportsInterval = null;
function fetchNewReports() {
  $.ajax({
    type: 'get',
    url: '/get_latest_reports',
    success: function (reports) {
      if (reports) {
        console.log('Fetched Reports:', reports);

        if (!Array.isArray(reports) || reports.length === 0) {
          console.log('No new reports available.');
          return;
        }

        reports.forEach(report => {
          console.log('Processing Report:', report);

          const id = report.id;
          if (existingReportIds.has(id)) {
            console.log(`Report ${id} already exists.`);
            return;
          }

          const options = {
            reportedDate: report.reported_date,
            reportedTime: report.reported_on,
            id,
            crime_name: report.crime_name,
            crimeDescription: report.crime_description,
            crimeLocation: report.crime_location,
            reportedBy: report.reportedBy
          };

          const lat = parseFloat(report.latitude);
          const long = parseFloat(report.longitude);
          const accuracy = parseFloat(report.accuracy);

          createPulsingMarkerAndCircle(lat, long, options, accuracy);

          map.invalidateSize();
        });
      }
    },
    error: function (xhr) {
      console.error('Error fetching new reports:', xhr);
    }
  });
}
fetchReportsInterval = setInterval(function() {
  fetchNewReports(); 
}, 5000);

let isSuccessAlertShown = false;
let isSpinnerRemoved = false;
function reportReceived(id, markerId, circleID) {
  addSpinnerToButton();  
    startTracking(id, function(error, lat, long) {
      if (error && !isSpinnerRemoved) {
        errorAlert(customMessage);
        removeSpinnerFromButton();
        console.log(error);
        isSpinnerRemoved = true;
        return; 
      }
      console.log("Tracking successful:", lat, long);
        const marker = map._layers[markerId];
        const circle = map._layers[circleID];
        
        $.ajax({
          type: 'post',
          url: '/report_received',
          data: { lat: lat, long: long, id: id },
          success: function(response) {
            console.log(response.message);
            if (response.code === 0 && !isSuccessAlertShown) {
              
              return showPoliceLocation();
              successAlert(response.message);
              map.setView([10.449444, 125.008231], 12);
  
              if (callCount > 0) {
                callCount--;
  
                map.removeLayer(marker); 
                map.removeLayer(circle); 
  
                if (callCount === 0) {
                  const sound = document.getElementById('notificationSound');
                  sound.pause();
                  sound.currentTime = 0;
                }
              }
  
              isSuccessAlertShown = true; 
            }
            else{
              console.log(response.message);
            }
          },
          error: function(xhr) {
            console.error('Error updating status:', xhr);
            stopTracking();
          }
        });

    });
  
}


function addBoundaries(map, marker) {
  $.ajax({
    type: 'get',
    url: '/fetch-boundaries',
    success: function (response) {
      response.boundaries.forEach(function (boundary) {
        var barangay = boundary.barangay_name;
        var latlng = L.latLng(boundary.latitude, boundary.longitude);
        var latLngArray = boundary.coordinates;

        var textIcon = L.divIcon({
          className: 'custom-label',
          html: barangay,
          iconSize: [20, 20],
          iconAnchor: [50, 10]
        });

        marker = L.marker(latlng, { icon: textIcon }).addTo(map);

        marker.bindPopup(barangay);

        var polygon = L.polygon(latLngArray, {
          color: 'darkgray',
          fillOpacity: 0
        }).addTo(map);
      });
    }
  });
}


var watchId;
var marker;
var customMessage;
var isTrackingSuccessful = false;
var retryInterval;

function startTracking(id, callback) {
  checkExistingReport(id, function(canProceed) {
    if (!canProceed) {
      console.log("Cannot proceed with geolocation tracking.");
      return; 
    }

    isTrackingSuccessful = false;

    function attemptGeolocation() {
      watchId = navigator.geolocation.watchPosition(
        function(position) {
          isTrackingSuccessful = true;
          var lat = position.coords.latitude;
          var long = position.coords.longitude;
          console.log(lat, long);
          callback(null, lat, long); 
        },
        function(error) {
          let customMessage;

          switch(error.code) {
            case error.PERMISSION_DENIED:
              customMessage = "You denied the request for Geolocation. Please enable location access.";
              break;
            case error.POSITION_UNAVAILABLE:
              customMessage = "Location information is unavailable. Please try again later.";
              break;
            case error.TIMEOUT:
              customMessage = "The request to get user location timed out. Please try again.";
              break;
            case error.UNKNOWN_ERROR:
              customMessage = "An unknown error occurred while retrieving your location.";
              break;
          }

          console.log(customMessage);
          callback(error, null, null); 
          
          retryGeolocation(); 
        },
        {
          enableHighAccuracy: true,
          timeout: 20000,
          maximumAge: 0
        }
      );
    }

    attemptGeolocation(); 

    function retryGeolocation() {
      clearInterval(retryInterval);
      retryInterval = setInterval(function() {
        console.log("Retrying geolocation...");
        attemptGeolocation(); 
      }, 30000); 
    }

    window.addEventListener('online', () => {
      console.log('Internet connection restored! Trying to start geolocation...');
      if (!isTrackingSuccessful) {
        attemptGeolocation();  
      }
    });

    window.addEventListener('load', () => {
      attemptGeolocation(); 
      alert('Page reloaded');
    });

    window.addEventListener('offline', () => {
      console.log('Internet connection lost! Stopping geolocation attempts.');
      clearInterval(retryInterval); 
    });

  });  
}


function stopTracking() {
  if (watchId) {
    navigator.geolocation.clearWatch(watchId);
    console.log('Geolocation Stopped');
  }
}


function successAlert(message) {
  Swal.fire({
    icon: "success",
    html: `<div class="alert alert-success d-flex align-items-center" role="alert">
  <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
  <div>
    ${message}
  </div>
</div>`,
    width: '400px', 
    padding: '10px', 
    showConfirmButton: false ,
    timer: 3000,
  });
}

function checkLocation(){
  let timerInterval;
  Swal.fire({
  title: "Auto close alert!",
  html: "I will close in <b></b> milliseconds.",
  timer: 2000,
  timerProgressBar: true,
  didOpen: () => {
    Swal.showLoading();
    const timer = Swal.getPopup().querySelector("b");
    timerInterval = setInterval(() => {
      timer.textContent = `${Swal.getTimerLeft()}`;
    }, 100);
  },
  willClose: () => {
    clearInterval(timerInterval);
  }
}).then((result) => {
  /* Read more about handling dismissals below */
  if (result.dismiss === Swal.DismissReason.timer) {
    console.log("I was closed by the timer");
  }
});
}


function errorAlert(message) {

  Swal.fire({
    icon: "error",
    html: `<div class="alert alert-danger d-flex align-items-center" role="alert">
      <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:">
        <use xlink:href="#exclamation-triangle-fill"/>
      </svg>
      <div>
        ${message}
      </div>
    </div>`,
    width: '400px', 
    padding: '10px', 
    showConfirmButton: false ,
    timer: 3000,
  });
}

function addSpinnerToButton() {
  const button = document.getElementById('btnTakeAction');
  
  const spinnerIcon = document.createElement('i');
  spinnerIcon.classList.add('fa', 'fa-spinner', 'fa-spin');

  button.insertBefore(spinnerIcon, button.firstChild);
  button.innerHTML = ' ' + button.innerHTML;
}

function removeSpinnerFromButton() {
  const button = document.getElementById('btnTakeAction');
  
  const spinnerIcon = button.querySelector('i');
  
  if (spinnerIcon) {
    button.removeChild(spinnerIcon);
  }
}

function checkExistingReport(id, callback){
  console.log(id);
  $.ajax({
    type: "get",
    url: "/checkExistingReport",
    data: {id},
    dataType: 'json',
    success: function (response) {
      console.log(response);
      if(response.code === 'reportAlreadyHandled'){
        errorAlert(response.message);
        removeSpinnerFromButton();
        callback(false);  
      }
      else if(response.code === 'userHasReport'){
        errorAlert(response.message);
        removeSpinnerFromButton();
        callback(false);  
      }
      else if(response.code === 'noReport'){
        errorAlert(response.message);
        removeSpinnerFromButton();
        callback(false);  
      }
      else if(response.code === 'reportActivated'){
        console.log(response.message);
        callback(true);  
      }
      
    },
    error: function() {
      console.error("Error checking report status");
      callback(false); 
    }
  });
}

let realTimeLocation;
let barangaysRow2;
function showPoliceLocation(){
  if(realTimeLocation){
    realTimeLocation.remove();
  }

  if(fetchReportsInterval){
    clearInterval(fetchReportsInterval);
    console.log('Fetching new reports stopped');
  }

  stopTracking();
  
  $('#alertMessage').remove();
  $('#row1').remove();
  console.log('Real-time map Removed');

  if(!realTimeLocation){
    realTimeLocation = L.map('policeMapLocation').setView([10.449285, 124.993744], 12);
    L.tileLayer('http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
      minZoom: 12,
      maxZoom: 20,
      subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
    }).addTo(realTimeLocation);
  
    addBoundaries(realTimeLocation, barangaysRow2);
  }
 
}
