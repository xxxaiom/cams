$.ajaxSetup({
  headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

$(document).ready(function () {
  loadChartData(0);

  $('#reportSelect').change(function () {
    var selectedValue = $(this).val();
    loadChartData(selectedValue);
  });

  function loadChartData(reportType) {
    $.ajax({
      url: '/fetch-data',
      method: 'GET',
      success: function (data) {
        var xAxisData, seriesData;

        if (reportType == 0) {
          xAxisData = data.accidentData.days;
          seriesData = data.accidentData.counts;
        } else if (reportType == 1) {
          xAxisData = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
          seriesData = data.monthlyReports;
        } else if (reportType == 2) {
          xAxisData = Object.keys(data.yearlyReports);
          seriesData = Object.values(data.yearlyReports);
        }

        // Chart for accident data
        var myChart = echarts.init(document.getElementById('first_chart'));
        var option = {
          tooltip: {
            trigger: 'axis',
            axisPointer: {
              type: 'shadow'
            }
          },
          grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
          },
          xAxis: [
            {
              type: 'category',
              data: xAxisData,
              axisTick: {
                alignWithLabel: true
              }
            }
          ],
          yAxis: [
            {
              type: 'value'
            }
          ],
          series: [
            {
              name: 'Accident Reports',
              type: 'bar',
              barWidth: '60%',
              data: seriesData
            }
          ]
        };
        myChart.setOption(option);

        // Pie Chart
        var mysecondChart = echarts.init(document.getElementById('second_chart'));
        var secondoption = {
          tooltip: {
            trigger: 'item'
          },

          legend: {
            show: false
          },
          series: [
            {
              name: 'Offense Types',
              type: 'pie',
              radius: ['40%', '70%'],
              avoidLabelOverlap: false,
              itemStyle: {
                borderRadius: 10,
                borderColor: '#fff',
                borderWidth: 2
              },
              label: {
                show: false
              },
              emphasis: {
                label: {
                  show: false
                }
              },
              labelLine: {
                show: false
              },
              data: data.offenseData
            }
          ]
        };

        mysecondChart.setOption(secondoption);
      },
      error: function (error) {
        console.error('Error fetching data', error);
      }
    });
  }
});



let selectedYear = null;
$(document).ready(function () {
  var mapColor = L.map('mapColor').setView([10.449444, 125.008231], 12);
  var google = L.tileLayer('http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
    minZoom: 12,
    maxZoom: 20,
    subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
  });
  google.addTo(mapColor);
  function updateMap(selectedYear) {
    $.ajax({
      type: 'get',
      url: '/add-color',
      data: { selectedYear },
      dataType: 'json',
      success: function (response) {
        console.log(response.mapColors);
        mapColor.eachLayer(function (layer) {
          if (layer instanceof L.Polygon) {
            mapColor.removeLayer(layer);  
          }
        });
        const accidentData = response.mapColors;
        response.boundaries.forEach(function (boundary) {
          const barangay = boundary.barangay_name;
          const latLngArray = boundary.coordinates;

          const accidentDataForBarangay = accidentData.find(item => item.barangay_name === barangay);
          const accidentCount = accidentDataForBarangay ? accidentDataForBarangay.accident_count : 0;

          const maxCount = Math.max(...accidentData.map(item => item.accident_count));
          const minCount = Math.min(...accidentData.map(item => item.accident_count));

          const polygonColor = getRedShadeByAccidentCount(accidentCount, minCount, maxCount);

          const polygon = L.polygon(latLngArray, {
            color: polygonColor,
            fillColor: polygonColor,
            fillOpacity: 0.9,
            weight: 2
          }).addTo(mapColor);

          polygon.bindPopup(`<strong>${barangay}</strong><br>Accident Count: ${accidentCount}`);
        });
      },
      error: function (xhr, status, error) {
        console.error("Error fetching data:", error);
      }
    });
  }

  updateMap(selectedYear);

  $('#selectColorBarangay').change(function (e) {
    e.preventDefault();
    const selectedYear = $(this).val();  
    updateMap(selectedYear);  
  });
});


function getRedShadeByAccidentCount(count, minCount, maxCount) {
  if (count === 0) {
    return 'rgb(255, 120, 120)';  // Light red for zero accidents
  }

  // Normalize the count between 0 and 1
  const normalized = (count - minCount) / (maxCount - minCount);

  // Increase the contrast by making the red intensity decrease more noticeably
  const redIntensity = Math.floor(255 - (normalized * 200));  // From light red (255) to darker red (55)

  // Ensure the red intensity never goes below 55 (for a dark red)
  const finalRedIntensity = Math.max(redIntensity, 55); // Prevents too dark (blackish)

  // Return the RGB value for pure red, with no green or blue
  return `rgb(${finalRedIntensity}, 0, 0)`; // Pure red, no green, no blue
}

let currentHeatMap = null; // 
let year = null; // 
$(document).ready(function () {
  var map = L.map('map').setView([10.449444, 125.008231], 12);
  var googleSat = L.tileLayer('http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
  minZoom: 12,
  maxZoom: 20,
  subdomains: ['mt0', 'mt1', 'mt2', 'mt3']
});
googleSat.addTo(map);
var myIcon = L.icon({
  iconUrl: 'assets/img/marker-icons/circle.png',
  iconSize: [20, 20]
});

addBoundary(map)
addHeatMap(map, year);
$('#selectHeatMap').change(function (e) { 
  e.preventDefault();
  year = $(this).val();
  console.log(year);

  if (currentHeatMap) {
    map.removeLayer(currentHeatMap);
  }

  addHeatMap(map, year)
});
});

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

function addHeatMap(map) {
  $.ajax({
    type: 'get',
    url: '/fetch-heat-map',
    data: { year },
    dataType: 'json',
    success: function (response) {
      console.log(response.brgyHeatMap);

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


$('#clickRScript').click(function (e) { 
  e.preventDefault();
  $.ajax({
    type: "get",
    url: "/runRScript",
    success: function (response) {
      console.log(response.output);
      console.log(response.error);
      console.log(response.message);
    }
  });
});

function changeReports(){
  
}