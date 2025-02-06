$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

$('#today, #month, #all').click(function (e) { 
    e.preventDefault();
    var value = $(this).attr('id');

    $.ajax({
        type: "get",
        url: "/changeReportValue",
        data: {value},
        dataType: "json",
        success: function (response) {
            $('#countValue').text(response.value);
            $('#sortDate').text(response.sort);

            $('.crime-bar').css('width', response.nonRecklessPercentage + '%');
            $('.accident-bar').css('width', response.recklessPercentage + '%');
            $('.crime-bar').attr('aria-valuenow',response.nonRecklessPercentage);
            $('.accident-bar').attr('aria-valuenow',response.recklessPercentage);
            $('.crime-bar').text(response.nonRecklessPercentage + '%'); 
            $('.accident-bar').text(response.recklessPercentage + '%');
        }
    });
});

$(document).ready(function () {
    var numOfReports = [];
    var year = [];

    function loadChartData(chartData){

      $('#chartTitle').text(chartData);

      numOfReports = [];
      year = [];

      $.ajax({
        type: "get",
        url: "/getAllData",
        data: { chartData: chartData }, 
        dataType: 'json',
        success: function (response) {
            response.allData.forEach(data => {
                numOfReports.push(data.numOfReports);
                year.push(data.year);
            });

            if (chart) {
              chart.destroy();
              
          }
          $('#chart').empty();
            var options = {
                chart: {
                  type: 'line',
                  toolbar: {
                    show: false
                  },
                  width: '100%',
                  height: '240px'
                },
                series: [{
                  name: 'reports',
                  data: numOfReports
                }],
                xaxis: {
                  categories: year
                },
                markers: {
                  size: 5,
              },
              stroke: {
                curve: 'smooth',
              },
              }
              
              var chart = new ApexCharts(document.querySelector("#chart"), options);
              
              chart.render();
        }
      });
    }

    loadChartData('All');

    $('#All ,#Crime, #Accident').click(function (e) { 
        e.preventDefault();
        var chartData = $(this).attr('id');

        loadChartData(chartData);
        
    });
    
});

$(document).ready(function () {

  function loadSecondChart(donutData){
    $.ajax({
      type: "get",
      url: "/getDonutData",
      data: {data: donutData},
      dataType: "json",
      success: function (response) {

        var seriesData = [
          response.data[0]["1AM_to_4AM_count"] || 0,  // Default to 0 if value is missing
          response.data[0]["4AM_to_8AM_count"] || 0,
          response.data[0]["8AM_to_12PM_count"] || 0,
          response.data[0]["12PM_to_4PM_count"] || 0,
          response.data[0]["4PM_to_8PM_count"] || 0,
          response.data[0]["8PM_to_1AM_count"] || 0
        ];

        if(chart){
          chart.destroy()
        }

        $('#secondChart').empty();
        if(response.select === 'donutCrime'){
          $('#secondChartTitle').text('Crime ');
        }
        else if(response.select === 'donutAccident'){
          $('#secondChartTitle').text('Accident ');
        }
        else{
          $('#secondChartTitle').text('Reports ');
        }

        var options = {
          series: seriesData,
          chart: {
          type: 'donut',
          width: '100%',   // Set the width to 100% of the parent container (responsive)
          height: '255px'
        },
        labels: ['1:00 am - 4:00 am', '4:00 am - 8:00 am', '8:00 am - 12:00 pm', '12:00 pm - 4:00 pm', '4:00 pm - 8:00 pm', '8:00 pm - 12:00 am'],  // Add the labels here
        responsive: [{
          breakpoint: 480,
          options: {
            chart: {
              width: 200
            },
            legend: {
              position: 'bottom'
            }
          }
        }]
        };
      
        var chart = new ApexCharts(document.querySelector("#secondChart"), options);
        chart.render();
      }
    });
  }

  loadSecondChart('donutAll');

  $('#selectDonutData').on('change', function () {
    var data = $(this).val()

    loadSecondChart(data);
  });

  
});

$(document).ready(function () {

  let currentHeatMap = null; // 
  let year = null; // 

  var map = L.map('map').setView([10.449285, 124.993744

  ], 12);
  var googleSat = L.tileLayer('http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
  minZoom: 12,
  maxZoom: 20,
  subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
});
googleSat.addTo(map);

addBoundary(map)
addHeatMap(map)
function addBoundary(map){
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

function addHeatMap(map, year) {
  $.ajax({
    type: 'get',
    url: '/fetch-heat-map',
    data: { year: year },
    dataType: 'json',
    success: function (response) {

      var heatMapData = [];

      // Prepare the heat map data
      response.brgyHeatMap.forEach(function (data) {
        var lat = data.lat;
        var long = data.lng;
        heatMapData.push([lat, long, 1.0]); // Add each point with a weight of 1
      });

      // Create a new heat map layer
      currentHeatMap = L.heatLayer(heatMapData, {
        radius: 25,
        blur: 15,
        maxZoom: 17
      }).addTo(map);
    },
    error: function (error) {
      console.log('Error fetching heat map data:', error);
    }
  });
}

$('#heatMap').change(function (e) { 
  e.preventDefault();
  var heatYear = $(this).val();

  if (currentHeatMap) {
    map.removeLayer(currentHeatMap);
  }

  addHeatMap(map, heatYear)
});

})

$(document).ready(function () {
  $('#barangayReports').change(function (e) { 
    e.preventDefault();
    var year = $(this).val();

    $.ajax({
      type: "get",
      url: "/fetch-most-reports",
      data: {year},
      dataType: "json",
      success: function (response) {
        $('#brgyTable tbody').empty();

        $.each(response.allData, function (index, item) { 
           var row = '<tr class="text-nowrap">';
           var color = (index + 1 <= 3) ? 'red' : ''; 

           row += '<td style="color: ' + color + ';">' + (index + 1) + '</td>'; 
           row += '<td style="color: ' + color + ';">' + item.barangay_name + '</td>';
           row += '<td style="color: ' + color + ';">' + item.crimeReports + '</td>';
           row += '<td style="color: ' + color + ';">' + item.accidentReports + '</td>';
           row += '<td style="color: ' + color + ';">' + item.totalReports + '</td>';
           row += '</tr>';

           $('#brgyTable tbody').append(row);
        });
      }
    });
  });
});

$(document).ready(function () {

  function barData(barData){
    $.ajax({
      type: "get",
      url: "/barData",
      data: {data: barData},
      dataType: "json",
      success: function (response) {
      
        var reportData = response.data.map(function (item) {
          return item.totalReports;
        });
      
        var categories = response.data.map(function (item) {
          return item.barangay_name;
        });

        if(chart){
          chart.destroy();
        }
        $('#bar').empty();
      
        var options = {
          series: [{
            name: 'Reports',
            data: reportData,
          }],
          chart: {
            type: 'bar',
            height: 350,
            toolbar: {
              show: false
            }
          },
          plotOptions: {
            bar: {
              borderRadius: 4,
              borderRadiusApplication: 'end',
              horizontal: true,
            }
          },
          dataLabels: {
            enabled: false
          },
          xaxis: {
            categories: categories,  
          }
        };
      
        var chart = new ApexCharts(document.querySelector("#bar"), options);
        chart.render();
      }
    });
  }

  barData('allReports');

  $('#selectBar').on('change', function () {
    var selectData = $(this).val(); 
  
    if(selectData === 'fullList'){
      $('#fullListModal').modal('show');
    } else {
      barData(selectData);
    }
  });
});
