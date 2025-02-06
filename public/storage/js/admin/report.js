$.ajaxSetup({
  headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

$(document).ready(function () {
  flatpickr('#dateTime_committed', {
    enableTime: true, // Enable time selection
    dateFormat: 'Y-m-d H:i', // Date and time format (YYYY-MM-DD HH:MM)
    altInput: true, // Use custom styled input
    altFormat: 'F j, Y h:i K', // Alternate format for displayed date and time
    theme: 'material_blue', // Optional: Material Blue theme
    maxDate: 'today', // Limit the date picker to today or before
    minuteIncrement: 1,
    disableMobile: true
  });
});

$(document).ready(function () {
  // setInterval(loadNewReportsTable, 1000);
  var selectedValue = 0;
  loadTimeFrame(selectedValue);

  $('#selectTimeFrame').on('change', function () {
    selectedValue = $(this).val();
    loadTimeFrame(selectedValue);
  });

  function loadTimeFrame(selectedValue) {
    $.ajax({
      type: 'get',
      url: '/fetch-report-data',
      success: function (response) {
        var rows = '';

        if (selectedValue == 0) {
          if (response.reportsToday.length > 0) {
            response.reportsToday.forEach(function (report) {
              rows += `
                        <tr>
                          <td>${report.incident_name}</td>
                          <td>${report.location_description}</td>
                          <td>${report.reported_on}</td>
                          <td>${report.reportedBy}</td>
                          <td>
                            <button type="button" class="btn btn-icon btn-primary" onclick="viewFullReport(${report.report_id})">
                              <span class="tf-icons bx bx-show-alt"></span>
                            </button>
                          </td>
                        </tr>
                    `;
            });
          } else {
            rows = `
                    <tr>
                      <td colspan="5" style="text-align: center;">There are no data</td>
                    </tr>
                `;
          }
        } else if (selectedValue == 1) {
          if (response.reportsWeek.length > 0) {
            response.reportsWeek.forEach(function (report) {
              rows += `
                         <tr>
                          <td>${report.incident_name}</td>
                          <td>${report.location_description}</td>
                          <td>${report.reported_on}</td>
                          <td>${report.reportedBy}</td>
                          <td>
                            <button type="button" class="btn btn-icon btn-primary" onclick="viewFullReport(${report.report_id})">
                              <span class="tf-icons bx bx-show-alt"></span>
                            </button>
                          </td>
                        </tr>
                    `;
            });
          } else {
            rows = `
                    <tr>
                      <td colspan="5" style="text-align: center;">There are no data</td>
                    </tr>
                `;
          }
        } else if (selectedValue == 2) {
          if (response.reportsMonth.length > 0) {
            response.reportsMonth.forEach(function (report) {
              rows += `
                        <tr>
                          <td>${report.incident_name}</td>
                          <td>${report.location_description}</td>
                          <td>${report.reported_on}</td>
                          <td>${report.reportedBy}</td>
                          <td>
                            <button type="button" class="btn btn-icon btn-primary" onclick="viewFullReport(${report.report_id})">
                              <span class="tf-icons bx bx-show-alt"></span>
                            </button>
                          </td>
                        </tr>
                    `;
            });
          } else {
            rows = `
                    <tr>
                      <td colspan="5" style="text-align: center;">There are no data</td>
                    </tr>
                `;
          }
        } else {
          rows = `
                <tr>
                  <td colspan="5" style="text-align: center;">There are no data</td>
                </tr>
            `;
        }

        $('#tbody').html(rows);
      }
    });
  }

  let timeout;
  $('#search').keyup(function () {
    clearTimeout(timeout);
    var input = $(this).val();

    if (input !== '') {
      timeout = setTimeout(function () {
        $.ajax({
          type: 'post',
          url: '/search-report',
          data: { input },
          success: function (response) {
            var rows = '';
            if (response.reports.length > 0) {
              response.reports.forEach(function (report) {
                rows += `
                          <tr>
                          <td>${report.incident_name}</td>
                          <td>${report.incident_location}</td>
                          <td>${report.reported_on}</td>
                          <td>${report.reportedBy}</td>
                          <td>
                            <button type="button" class="btn btn-icon btn-primary">
                              <span class="tf-icons bx bx-show-alt"></span>
                            </button>
                          </td>
                        </tr>
                `;
              });
            } else {
              rows = `
                    <tr>
                      <td colspan="6" style="text-align: center;">There are no data</td>
                    </tr>
                `;
            }
            $('#tbody').html(rows);
          },
          error: function (xhr, status, error) {
            console.error('AJAX Error:', error);
          }
        });
      }, 300); // Adjust delay as needed
    } else {
      loadTimeFrame(selectedValue);
    }
  });
});

function viewInModal(ecryptedID) {
  $('#reportsDetails').modal('show');
  $.ajax({
    type: 'get',
    url: '/fetch-modal-data',
    data: { id: ecryptedID },
    success: function (response) {
      $('#report_id').val(ecryptedID);
      $('#reported_by').val(response.reports.reportedBy);
      $('#crime_description').val(response.reports.crime_description);
      $('#crime_location').val(response.reports.crime_location);
      $('#date_reported').val(response.date_reported);

      const latitude = response.reports.latitude;
      const longitude = response.reports.longitude;
      const accuracy = response.reports.accuracy;
      const barangay = response.reports.crime_location;

      $('#reportLatitude').val(latitude);
      $('#reportLongitude').val(longitude);
      $('#reportAccuracy').val(accuracy);
      $('#reportBarangay').val(barangay);
    }
  });
}

var myModal = document.getElementById('reportsDetails');
var focus = document.getElementById('incident_name');

myModal.addEventListener('shown.bs.modal', function () {
  focus.focus();
});

$('#reportsDetails').on('hidden.bs.modal', function () {
  clearInput();
});

function clearInput() {
  document.getElementById('incident_name').value = '';
  document.getElementById('incident_description').value = '';
  document.getElementById('location_description').value = '';
  document.getElementById('dateTime_committed').value = '';
  firstPage.hidden = false;
  secondPage.hidden = true;
  thirdPage.hidden = true;
  $('#title').html('Details of Report');
  if (adminMarker) {
    myMap.removeLayer(adminMarker);
    adminMarker = null;
    console.log('marker removed');
  }
  myMap.setView([10.449444, 125.008231], 12);
}

$('#incident_report').on('submit', function (e) {
  e.preventDefault();
  $.ajax({
    type: 'post',
    url: '/createIncidentReport',
    data: $(this).serialize(),
    dataType: 'json',
    success: function (response) {
      if (response.code === 0) {
        $('#reportsDetails').modal('toggle');
        clearInput();
        const Toast = Swal.mixin({
          toast: true,
          position: 'center',
          showConfirmButton: false,
          timer: 1500
        });
        Toast.fire({
          icon: 'success',
          title: 'Report Submitted'
        });
        loadReportsTable();
      }
      if (response.code === 1) {
        $('#errorMessage').html(
          `
            <div class="alert alert-danger" role="alert">
              ${response.message}
            </div>
          `
        );
        setTimeout(() => {
          $('#errorMessage').html('');
        }, 5000);
      }
      if (response.code === 2) {
        $('#errorMessage').html(
          `
            <div class="alert alert-danger" role="alert">
              ${response.message}
            </div>
          `
        );
        setTimeout(() => {
          $('#errorMessage').html('');
        }, 5000);
      }
    }
  });
});

function loadNewReportsTable() {
  $.ajax({
    type: 'get',
    url: '/fetch-new-reports',
    success: function (response) {
      console.log(response.newReports);
      var newRows = '';
      if (response.newReports.length > 0) {
        response.newReports.forEach(function (report) {
          newRows += `
                      <tr>
                          <td>${report.reports_id}</td>
                          <td>${report.reports_id}</td>
                          <td>${report.crime_name}</td>
                          <td>${report.reportedBy}</td>
                          <td>${report.status}</td>
                          <td>
                            <button type="button" class="btn btn-icon btn-primary" onclick="viewInModal(${report.reports_id})">
                              <span class="tf-icons bx bx-show-alt"></span>
                            </button>
                          </td>
                      </tr>
          `;
        });
      } else {
        newRows = `
                <tr>
                  <td colspan="6" style="text-align: center;">No new reports</td>
                </tr>
            `;
      }
      $('#tableBody').html(newRows);
    }
  });
}

function loadReportsTable() {
  $.ajax({
    type: 'get',
    url: '/fetch-reports-data',
    success: function (response) {
      console.log(response.fullReports);
      var reportRows = '';
      if (response.fullReports.length > 0) {
        response.fullReports.forEach(function (report) {
          reportRows += `
                          <tr>
                              <td>${report.incident_name}</td>
                              <td>${report.location_description}</td>
                              <td>${report.reported_on}</td>
                              <td>${report.reportedBy}</td>
                              <td>
                               <button type="button" class="btn btn-icon btn-primary"
                                      onclick="viewFullReport(${report.id})">
                                    <span class="tf-icons bx bx-show-alt"></span>
                                </button>
                              </td>
                          </tr>
                        `;
        });
      } else {
        reportRows = `
                <tr>
                  <td colspan="5" style="text-align: center;">There are no data</td>
                </tr>
            `;
      }
      $('#tbody').html(reportRows);
    }
  });
}

function viewFullReport() {
  $('#incidentReportDetails').modal('show');
}

let myMap = null;
let adminMarker = null;
const firstPage = document.getElementById('firstPage');
const secondPage = document.getElementById('secondPage');

$('#map-show').click(function (e) {
  e.preventDefault();
  showMap();
});

function showMap() {
  if (firstPage && secondPage) {
    firstPage.hidden = true;
    secondPage.hidden = false;
  }
  $('#title').html('Location of the Incident');
  if (!myMap) {
    myMap = L.map('addLocation').setView([10.449444, 125.008231], 12);
    L.tileLayer('http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
      minZoom: 12,
      maxZoom: 20,
      subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
    }).addTo(myMap);
    fetchMapBoundaries(myMap);

    if (adminMarker) {
      myMap.removeLayer(adminMarker);
      adminMarker = null;
      console.log('map removed');
    }
  }
  createMarker();
}

function createMarker() {
  const reportLatitude = $('#reportLatitude').val();
  const reportLongitude = $('#reportLongitude').val();
  const reportBarangay = $('#reportBarangay').val();
  console.log(reportBarangay);
  if (!adminMarker) {
    console.log('marker created');
    adminMarker = L.marker([reportLatitude, reportLongitude], { draggable: true });
    var popUp = adminMarker
      .bindPopup(
        `
          <div class="mb-2">
            <p class="fs-6">Latitude: ${reportLatitude}</p>
            <p class="fs-6">Longitude: ${reportLongitude}</p>
            <p class="fs-6">Barangay: ${reportBarangay}</p>
          </div>
        `
      )
      .openPopup();
    popUp.addTo(myMap);

    adminMarker.on('dragend', function () {
      var newLatLng = adminMarker.getLatLng();
      getBrgyNames(newLatLng.lat, newLatLng.lng);
    });
  }
}

function fetchMapBoundaries(map) {
  $.ajax({
    type: 'get',
    url: '/fetch-map-boundaries',
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

        var marker = L.marker(latlng, { icon: textIcon }).addTo(map);

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
function pointLocation(map) {
  map.on('click', function (e) {
    const latlng = e.latlng;

    if (currentMarker) {
      map.removeLayer(currentMarker);
    }

    currentMarker = L.marker(latlng).addTo(map).bindPopup(`Latitude: ${latlng.lat} <br> Longitude: ${latlng.lng}`);

    console.log(latlng);
    $('#lat').val(latlng.lat);
    $('#long').val(latlng.lng);
    brgyNames(latlng.lat, latlng.lng);
  });
}

function brgyNames(lat, lng) {
  $('#confirmLocation').modal('show');
  $.ajax({
    type: 'get',
    url: '/get-brgy-names-admin',
    data: { lat, lng },
    dataType: 'json',
    success: function (response) {
      if (response.barangay === null) {
        myMap.removeLayer(currentMarker);
      }
      $('#location').val(response.barangay);
      const modalBody =
        response.barangay === null
          ? `<div class="alert alert-danger" role="alert">
                Marker location is outside of Sogod Boundary!
             </div>`
          : `<p>The location has been marked on the map.</p>
            <p>Latitude: ${lat}</p>
            <p>Longitude: ${lng}</p>
            <p>Barangay: ${response.barangay}</p>`;

      const modalFooter =
        response.barangay === null
          ? `<button type="button" class="btn btn-secondary close-btn" data-bs-dismiss="modal">Close</button>
             <button type="button" class="btn btn-primary retry-btn" data-bs-dismiss="modal">Retry</button>`
          : `<button type="button" class="btn btn-secondary close-btn" data-bs-dismiss="modal">Close</button>
             <button type="submit" class="btn btn-primary" onclick="confirmBrgy()">Submit</button>`;

      const title =
        response.barangay === null
          ? `<h4 class="modal-title" id="title-modal">Invalid Location!</h4>`
          : `<h4 class="modal-title" id="title-modal">Confirm Location?</h4>`;

      $('#confirmLocation .modal-body').html(modalBody);
      $('#confirmLocation .modal-footer').html(modalFooter);
      $('#title-modal').html(title);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.error('AJAX Error: ', textStatus, errorThrown);
    }
  });
}

function confirmBrgy() {
  document.getElementById('firstPage').removeAttribute('hidden');
  document.getElementById('secondPage').setAttribute('hidden', true);
  $('#confirmLocation').modal('toggle');
  $('#confirmLocation .modal-body').html('');
}

let reportMap;
let marker, circle;
let featureGroup = null;
const thirdPage = document.getElementById('thirdPage');
function showReportLocation(latitude, longitude, accuracy, barangay) {
  if (firstPage && secondPage && thirdPage) {
    firstPage.hidden = true;
    secondPage.hidden = true;
    thirdPage.hidden = false;
  }
  if (!reportMap) {
    reportMap = L.map('viewReportLocation').setView([10.449444, 125.008231], 12);
    L.tileLayer('http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
      minZoom: 12,
      maxZoom: 20,
      subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
    }).addTo(reportMap);
  }

  if (featureGroup) {
    reportMap.removeLayer(featureGroup);
  }

  marker = L.marker([latitude, longitude]).bindPopup(barangay).openPopup();
  circle = L.circle([latitude, longitude], { radius: accuracy, color: 'red', fillOpacity: 0.2 });
  featureGroup = L.featureGroup([marker, circle]).addTo(reportMap);

  reportMap.fitBounds(featureGroup.getBounds());

  fetchMapBoundaries(reportMap);
}

let currentLatitude, currentLongitude, currentAccuracy, currentBarangay;
$('.show-report-location').click(function (e) {
  e.preventDefault();
  $('#title').html('User-reported location');

  currentLatitude = $('#reportLatitude').val();
  currentLongitude = $('#reportLongitude').val();
  currentAccuracy = $('#reportAccuracy').val();
  currentBarangay = $('#reportBarangay').val();
  console.log(currentBarangay);

  showReportLocation(currentLatitude, currentLongitude, currentAccuracy, currentBarangay);
});

function getBrgyNames(lat, lng) {
  $.ajax({
    type: 'get',
    url: 'get-brgy-names',
    data: { lat, lng },
    dataType: 'json',
    success: function (response) {
      adminMarker
        .setPopupContent(
          `
            <div class="mb-2">
              <p class="fs-6">Latitude: ${lat}</p>
              <p class="fs-6">Longitude: ${lng}</p>
              <p class="fs-6">Barangay: ${response.barangay}</p>
            </div>
            <div class="d-flex justify-content-center">
              <button class="btn btn-primary btn-sm" onclick="confirmTheBrgy()">Confirm</button>
            </div>
          `
        )
        .openPopup();

      $('#lat').val(lat);
      $('#long').val(lng);
      $('#location_description').val(response.barangay);
    }
  });
}

function confirmTheBrgy() {
  if (firstPage && secondPage && thirdPage) {
    firstPage.hidden = false;
    secondPage.hidden = true;
    thirdPage.hidden = true;
  }
  $('#title').html('Details of Report');
}

$('#openLargeModal').click(function (e) { 
  e.preventDefault();
  $('#largeModal').modal('show');
});

var myModal = document.getElementById('largeModal')
var myInput = document.getElementById('myFirstInput')

myModal.addEventListener('shown.bs.modal', function () {
  myInput.focus()
})
