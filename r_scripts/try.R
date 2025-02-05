args <- commandArgs(trailingOnly = TRUE)
incident_type <- args[1] 

library(ggplot2)
library(RMariaDB)
library(jsonlite)
library(forecast)
library(base64enc)

connection <- dbConnect(RMariaDB::MariaDB(),
                        user = "root",
                        password = "Gibeako5",
                        host = "localhost",
                        port = 3306,
                        dbname = "pnp")

if (incident_type == "crime") {
    condition <- "NOT LIKE"
    plotTitle <- "Crime"
} else {
    condition <- "LIKE"
    plotTitle <- "Accidents"
}

query <- paste0("SELECT 
                                d.year, 
                                d.month, 
                                COUNT(ds.id) AS numOfReports
                            FROM 
                                date_dimension d
                            LEFT JOIN 
                                pnp.datasets ds 
                            ON YEAR(ds.dateTimeReported) = d.year AND MONTH(ds.dateTimeReported) = d.month
                            AND ds.offense ", condition, " '%RECKLESS%'
                            WHERE 
                                d.year BETWEEN '2016' AND '2021'
                            GROUP BY 
                                d.year, d.month
                            ORDER BY 
                                d.year, d.month;")

query_result <- dbGetQuery(connection, query)

dbDisconnect(connection)

query_result$numOfReports <- as.numeric(query_result$numOfReports)

query_result$date <- as.Date(paste(query_result$year, query_result$month, "01", sep = "-"))

ts_data <- ts(query_result$numOfReports, frequency = 12, start = c(2016, 1), end = c(2021, 12))

ts_png_file <- tempfile(fileext = ".png")
png(ts_png_file)
plot(ts_data, main = paste("Number of", plotTitle, "Reports"), xlab = "Years", ylab = "Number of Reports")
dev.off() 
ts_encoded_image <- base64enc::base64encode(ts_png_file)

auto_forecast <- auto.arima(ts_data)
auto_arima <- forecast(auto_forecast, h = 12)

auto_arima_png_file <- tempfile(fileext = ".png")
png(auto_arima_png_file) 
plot(auto_arima, main = "Forecasted Reports in 2025", xlab = "Years", ylab = "Number of Reports")
dev.off() 
auto_arima_encoded_image <- base64enc::base64encode(auto_arima_png_file)

forecast_data <- data.frame(
  Month = as.character(time(auto_arima$mean)),  
  Point_Forecast = as.numeric(auto_arima$mean)
)

forecast_data$Month <- format(as.Date(paste(floor(as.numeric(forecast_data$Month)), 
                                            round((as.numeric(forecast_data$Month) %% 1) * 12) + 1, "01", sep = "-")),
                              "%Y-%m")

json_data <- toJSON(list(
  forecast = forecast_data,
  ts_plot = ts_encoded_image,
  auto_arima_plot = auto_arima_encoded_image
), pretty = TRUE, auto_unbox = TRUE)

cat(json_data)
