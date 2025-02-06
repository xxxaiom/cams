$.ajaxSetup({
  headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

const Toast = Swal.mixin({
  toast: true,
  position: 'center',
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true
});

function showToast() {
  return Toast.fire({
    icon: 'info',
    title: 'Checking location.... ',
    didOpen: toast => {
      const timer = document.createElement('b');
      toast.querySelector('.swal2-title').appendChild(timer);

      Swal.showLoading();

      const timerInterval = setInterval(() => {
        const remainingTime = Swal.getTimerLeft();
        timer.textContent = ` ${remainingTime} ms`;
      }, 100);

      toast.timerInterval = timerInterval;
    },
    willClose: toast => {
      clearInterval(toast.timerInterval);
    }
  });
}

function showToastSuccess() {
  return Toast.fire({
    icon: 'success',
    title: 'Reported submitted',
    timer: 1500,
    timerProgressBar: false
  });
}

function clear() {
  const form = document.getElementById('create_reports');
  form.reset();
  $('#message').html('');
  document.getElementById('secondPage').setAttribute('hidden', true);
  document.getElementById('firstPage').removeAttribute('hidden');
  myMap.removeLayer(currentMarker);
}

function check(input_id) {
  var id = document.getElementById(input_id).value;
  if (id != '') {
    $('#message').html('');
  }
}

var myModal = document.getElementById('createReport');
var focus = document.getElementById('crime_location');

myModal.addEventListener('shown.bs.modal', function () {
  focus.focus();
});

$('.close-btn').click(function (e) {
  e.preventDefault();
  clear();
});

$('#share_location').change(function (e) {
  if (this.checked) {
    e.preventDefault();
    $('#createReport').modal('toggle');

    if (!navigator.geolocation) {
      throw new Error('Geolocation unavailable');
    }

    showToast().then(() => {
      requestLocation();
    });

    function requestLocation() {
      navigator.geolocation.getCurrentPosition(success, error);
    }

    function success(position) {
      const lat = position.coords.latitude;
      const long = position.coords.longitude;
      const accuracy = position.coords.accuracy;

      $('#lat').val(lat);
      $('#long').val(long);
      $('#accuracy').val(accuracy);
      $('#share_location').prop('checked', true);
      $('#createReport').modal('toggle');
    }

    function error(err) {
      switch (err.code) {
        case err.PERMISSION_DENIED:
          alert('Please turn on location');
          $('#share_location').prop('checked', false);
          $('#createReport').modal('toggle');
          break;
        case err.POSITION_UNAVAILABLE:
          alert('Location information is unavailable.');
          $('#createReport').modal('toggle');
          $('#share_location').prop('checked', false);
          break;
        case err.TIMEOUT:
          alert('The request to get your location timed out.');
          $('#createReport').modal('toggle');
          $('#share_location').prop('checked', false);
          break;
        case err.UNKNOWN_ERROR:
          alert('An unknown error occurred.');
          $('#createReport').modal('toggle');
          $('#share_location').prop('checked', false);
          break;
      }
    }
  }
});

$('#create_reports').on('submit', function (e) {
  e.preventDefault();

  $.ajax({
    type: 'post',
    url: '/submit-report',
    data: $(this).serialize(),
    dataType: 'json',
    success: function (response) {
      if (response.code === 0) {
        $('#createReport').modal('toggle');
        clear();
        showToastSuccess().then(() => {
          const badgeClass =
            response.newReport.status === 'responding'
              ? 'bg-info'
              : response.newReport.status === 'pending'
              ? 'bg-danger'
              : response.newReport.status === 'responded'
              ? 'bg-success'
              : 'bg-secondary';

          $('#tbody').append(`
          <tr class="row-${response.newReport.id}">
            <td>${response.newReport.id}</td>
            <td>${response.newReport.crime_location}</td>
            <td>${response.newReport.crime_description}</td>
            <td>${response.created_at}</td>
            <td class="status">
              <span class="badge rounded-pill ${badgeClass}">
                ${response.newReport.status}
              </span>
            </td>
            <td>
              <button type="button"
                  class="btn rounded-pill btn-primary"data-bs-toggle="modal"
                  data-bs-target="#policeLocationModal"
                  onclick="showPoliceLocation(${response.newReport.id})">
                  <i class='bx bx-map me-1'></i>View
              </button>
            </td>
          </tr>
        `);
        });
      }
      if (response.code === 1) {
        $('#message').html(
          `<div class="alert alert-danger" role="alert">
                  ${response.message}
              </div>`
        );
        setTimeout(() => {
          $('#message').html('');
        }, 5000);
      }
      if (response.code === 2) {
        $('#message').html(
          `<div class="alert alert-danger" role="alert">
                  ${response.message}
              </div>`
        );
        setTimeout(() => {
          $('#message').html('');
        }, 5000);
      }
    }
  });
});

function updateStatus() {
//   const encryptedIds = [];

//   // Loop through each row to get encrypted IDs
//   $('tr').each(function() {
//     const encryptedId = $(this).find('td:first').text();  // Assuming 'id' is the first <td>
//     const status = $(this).find('.status span').text().trim();  // Get the status text from the span inside the .status class

//     if (encryptedId && status) {
//         encryptedIds.push({ encryptedId: encryptedId, status: status });
//     }
// });


    $.ajax({
      type: 'get',
      url: '/get-latest-status',
      // data: { reports: encryptedIds }, 
      // dataType: 'json',
      success: function(response) {
        console.log(response);
        if (response.data && response.data.length > 0) {
          response.data.forEach(report => {
            
            const row = $(`tr.row-${report.id}`);
          console.log(row);

            if (row.length > 0) {
              const reportCell = row.find('.status');
              const status = report.status;

              const badgeClass = getBadgeClass(status);
              reportCell.html(`<span class="badge rounded-pill ${badgeClass}">${status}</span>`);
            } else {
              console.warn('No row found for encrypted_id:', report.id);
            }
          });
        } else {
          console.warn('No data received from server.');
        }
      },
      error: function(xhr, status, error) {
        console.error('AJAX Error: ', status, error);
        alert('Failed to update status. Please try again later.');
      }
    });
}



$(document).ready(function() {
  setInterval(updateStatus, 5000);
});



function getBadgeClass(status) {
  switch (status) {
    case 'responding':
      return 'bg-info';
    case 'pending':
      return 'bg-danger';
    case 'responded':
      return 'bg-success';
    default:
      return 'bg-secondary';
  }
}

let myMap;
var userMarker;
function showMap() {
  $('#title').html('Location');
  document.getElementById('firstPage').setAttribute('hidden', true);
  document.getElementById('secondPage').removeAttribute('hidden');

  if(myMap){
    myMap.remove();
  }

    myMap = L.map('userMap').setView([10.449285, 124.993744], 12);
    L.tileLayer('http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
      minZoom: 12,
      maxZoom: 20,
      subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
    }).addTo(myMap);

  fetchMapBoundaries(myMap, userMarker);
  pointLocation();
}

function fetchMapBoundaries(map, marker) {
  $.ajax({
    type: 'get',
    url: '/fetch-user-map-boundaries',
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

var currentMarker = null;
function pointLocation() {
  myMap.on('click', function (e) {
    const latlng = e.latlng;

    if (currentMarker) {
      myMap.removeLayer(currentMarker);
    }

    currentMarker = L.marker(latlng).addTo(myMap).bindPopup(`Latitude: ${latlng.lat} <br> Longitude: ${latlng.lng}`);
    $('#reportLat').val(latlng.lat);
    $('#reportLong').val(latlng.lng);
    brgyNames(latlng.lat, latlng.lng);
    
  });
}

function brgyNames(lat, lng) {
  $('#reportMapLocation').modal('show');
  $.ajax({
    type: 'get',
    url: '/get-brgy-names-user',
    data: { lat, lng },
    dataType: 'json',
    success: function (response) {
      
      console.log(response.barangay);
      if (response.barangay === null) {
        myMap.removeLayer(currentMarker);
        const modalBody = `<div class="alert alert-danger" role="alert">
                Marker location is outside of Sogod Boundary!
             </div>`;
        const modalFooter = `<button type="button" class="btn btn-secondary close-btn" data-bs-dismiss="modal">Close</button>
             <button type="button" class="btn btn-primary retry-btn" data-bs-dismiss="modal">Retry</button>`;
        const title = `<h4 class="modal-title" id="title-modal">Invalid Location!</h4>`

        $('#reportMapLocation .modal-body').html(modalBody);
        $('#reportMapLocation .modal-footer').html(modalFooter);
        $('#title-modal').html(title);
      }
      else{
        $('#crime_location').val(response.barangay);
        const modalBody = `<p>The location has been marked on the map.</p>
                           <p>Latitude: ${lat}</p>
                           <p>Longitude: ${lng}</p>
                           <p>Barangay: ${response.barangay}</p>`;
        const modalFooter = `<button type="button" class="btn btn-secondary close-btn" data-bs-dismiss="modal">Close</button>
             <button type="submit" class="btn btn-primary" onclick="confirmBrgy()">Submit</button>`;
        const title = `<h4 class="modal-title" id="title-modal">Confirm Location?</h4>`;

        $('#reportMapLocation .modal-body').html(modalBody);
        $('#reportMapLocation .modal-footer').html(modalFooter);
        $('#title-modal').html(title);
      }
      
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.error('AJAX Error: ', textStatus, errorThrown);
    }
  });
}

function confirmBrgy() {
  document.getElementById('firstPage').removeAttribute('hidden');
  document.getElementById('secondPage').setAttribute('hidden', true);
  $('#reportMapLocation').modal('toggle');
  $('#reportMapLocation .modal-body').html('');
}

let policeLocation;
var policeMarker;
let intervalId = null; 

function showPoliceLocation(id) {
  let report_id = id;
  
  $('#policeLocationModal').off('shown.bs.modal').on('shown.bs.modal', function () {

    runOnce = false;

    if (intervalId) {
      clearInterval(intervalId);
    }

    if (policeLocation) {
      policeLocation.remove(); 
      policeLocation = null; 
    }

    if (policeMarker) {
      policeLocation.removeLayer(policeMarker); 
      policeMarker = null;
    }
    
  
    console.log('Cleared Existing Maps and Interval.')

    if(!policeLocation){
      policeLocation = L.map('policeMapLocation').setView([10.439190, 125.000524], 13);
      L.tileLayer('http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
        minZoom: 12,
        maxZoom: 20,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
      }).addTo(policeLocation);
      
      fetchMapBoundaries(policeLocation, policeMarker);
  
      intervalId = setInterval(function() {
        trackPoliceLocation(report_id); 
      }, 15000);
    }
    
  });
}

let runOnce = false;
let policeCoords = [];
let reportCoords;
let policePath;
let policeStartMarker, currentPoliceCoords, reportCoordsMarker, policeCoordsMarker;

const reportLocationIcon = L.icon({
  iconUrl: 'assets/img/marker-icons/incident-icon.png',  
  iconSize: [50, 50], 
  iconAnchor: [16, 32], 
  popupAnchor: [0, -32]
});
const policeLocationIcon = L.icon({
  iconUrl: 'assets/img/marker-icons/police-officer.png',  
  iconSize: [50, 50],
  iconAnchor: [16, 32],  
  popupAnchor: [0, -32] 
});

function trackPoliceLocation(id) {
  $.ajax({
    type: "get",
    url: "/getPoliceLocation",
    data: {id},
    dataType: 'json',
    success: function (response) {
      console.log(response);
      if (response.code === 0) {
        if (!runOnce) {
          if (response.reportCoords) {
            console.log('Report Location:', response.reportCoords.report_lat, response.reportCoords.report_long);
            reportCoordsMarker = L.marker([response.reportCoords.report_lat, response.reportCoords.report_long], { icon: reportLocationIcon })
              .addTo(policeLocation)
              .bindPopup("Report Location")
              .openPopup();
          }

          if (response.policeCoords && response.policeCoords.length > 0) {
 
            let oldestPolice = response.policeCoords[response.policeCoords.length - 1];

            console.log('Starting Location:', oldestPolice.police_lat, oldestPolice.police_long);
            policeStartMarker = L.marker([oldestPolice.police_lat, oldestPolice.police_long], { icon: policeLocationIcon })
              .addTo(policeLocation)
              .bindPopup("Police Starting Location")
              .openPopup();

            policePath = L.polyline([], { color: 'blue' }).addTo(policeLocation);
  
            response.policeCoords.forEach(function(policeLocation) {
              policePath.addLatLng([policeLocation.police_lat, policeLocation.police_long]);
            });
  
            runOnce = true;
          } else {
            console.error('Invalid or empty police coordinates:', response.policeCoords);
            return;
          }
        }

        let latestPolice = response.policeCoords[0];  
        let currentPoliceCoords = [latestPolice.police_lat, latestPolice.police_long];
        console.log('Current Location:', currentPoliceCoords);

        if (typeof currentPoliceCoords[0] === 'undefined' || typeof currentPoliceCoords[1] === 'undefined') {
          console.error('Invalid LatLng object:', currentPoliceCoords);
        return;
        }

        console.log('Updating Current Marker:', currentPoliceCoords);
        if (currentPoliceCoords) {
          console.log('Updating Current Marker:', currentPoliceCoords);
          if (policeCoordsMarker) {
            policeCoordsMarker.setLatLng(currentPoliceCoords); 
          } else {
            policeCoordsMarker = L.marker(currentPoliceCoords, { icon: policeLocationIcon })
              .addTo(policeLocation)
              .bindPopup("Police Current Location")
              .openPopup();
          }
        }        
      } 
      else if (response.code === 1) {
          alert(response.message);
      } else {
          alert('Error in database');
      }
    },
    error: function (xhr, status, error) {
      console.error('Error fetching police location:', status, error);
    }
  });
}

$('.close-modal').on('click', function () {
  if (intervalId) {
    clearInterval(intervalId);
  }
});
