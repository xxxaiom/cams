$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
  });


$('#clickRScript').click(function (e) { 
    e.preventDefault();

    var type = $('#selectType').val();

    type === "" || type === null ? console.log('null') : console.log(type);
    
    $.ajax({
      type: "get",
      url: "/runRScript",
      data: { type },
      dataType: 'json',
      success: function (response) {
        if(response.message){
          console.log(response.message);
        }else{
          if (response.output && response.ts_plot && response.auto_arima_plot) {
            $('#tsPlotContainer').empty();
            $('#autoArimaPlotContainer').empty();

            $('#ts').hide();
            $('#ar').hide();

              let forecast = response.output;
              console.log("Forecast data: ", forecast);
  
              let tsPlotImg = new Image();
              tsPlotImg.src = 'data:image/png;base64,' + response.ts_plot;
              $('#tsPlotContainer').html(tsPlotImg); 
  
              let autoArimaPlotImg = new Image();
              autoArimaPlotImg.src = 'data:image/png;base64,' + response.auto_arima_plot;
              $('#autoArimaPlotContainer').html(autoArimaPlotImg); 
          } else {
              console.error("Error: Response data is incomplete or malformed.");
          }
        }
      },
      error: function (xhr, status, error) {
          console.error("AJAX Error: " + error);
      }
  });
  
});
