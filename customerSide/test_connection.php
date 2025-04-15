<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php'; // Include the database configuration file

// CREATE: Insert data into the database
$insertQuery = "INSERT INTO some_table (name, details) VALUES ('John Doe', 'Sample details')";
if ($link->query($insertQuery) === TRUE) {
    echo "Record inserted successfully!<br>";
} else {
    echo "Error inserting record: " . $link->error . "<br>";
}

// READ: Retrieve data from the database
$selectQuery = "SELECT * FROM some_table";
$result = $link->query($selectQuery);
if ($result->num_rows > 0) {
    echo "Data retrieved successfully:<br>";
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . " - Name: " . $row['name'] . " - Details: " . $row['details'] . "<br>";
    }
} else {
    echo "No data found.<br>";
}

// UPDATE: Update data in the database
$updateQuery = "UPDATE some_table SET details = 'Updated details' WHERE name = 'John Doe'";
if ($link->query($updateQuery) === TRUE) {
    echo "Record updated successfully!<br>";
} else {
    echo "Error updating record: " . $link->error . "<br>";
}

// DELETE: Delete data from the database
$deleteQuery = "DELETE FROM some_table WHERE name = 'John Doe'";
if ($link->query($deleteQuery) === TRUE) {
    echo "Record deleted successfully!<br>";
} else {
    echo "Error deleting record: " . $link->error . "<br>";
}

// Close the database connection
$link->close();
?>