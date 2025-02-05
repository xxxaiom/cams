library(RMariaDB)

# Database connection details
conn <- dbConnect(RMariaDB::MariaDB(), 
                  user = "root",          # User should be a string
                  password = "root",      # Password should be a string
                  dbname = "pnp",         # Database name should be a string
                  host = "127.0.0.1",     # Host should be a string
                  port = 3306)            # Port number (no quotes)

# Query to fetch data
query <- "SELECT * FROM users LIMIT 100"

# Execute the query
data <- dbGetQuery(conn, query)

# Check if the query returned any results
if (nrow(data) > 0) {
  # If data has rows, print the result
  print("Query returned results:")
  print(head(data))  # Print the first few rows of the result
} else {
  # If no data returned
  print("Query returned no results.")
}

# Close the connection after fetching data
dbDisconnect(conn)

