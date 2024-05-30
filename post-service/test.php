<?php

// Print some information
echo "Hello, World! This is a test PHP script.";
echo "<br>"; // Line break for better readability

// Get the current date and time
$date = date("Y-m-d H:i:s");
echo "Current date and time: " . $date;

// Check if a GET parameter is set
if (isset($_GET["name"])) {
    $name = $_GET["name"];
    echo "<br><br>Hello, " . $name . "!";
} else {
    echo "<br><br>Please provide a name in the URL parameter: ?name=your_name";
}

?>
