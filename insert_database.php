<?php

// Connect to your MySQL database
$servername = "db";
$username = "php_docker";
$password = "password";
$dbname = "php_docker";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sample data
$batchSize = 1000;
$totalRecords = 100001;

// Disable autocommit and start a transaction
$conn->autocommit(false);

// Disable foreign key checks and indexes
$conn->query("SET foreign_key_checks = 0");
$conn->query("ALTER TABLE your_table_name DISABLE KEYS");

$homepage = file_get_contents('dataset.txt');

//echo $homepage;
$rows = explode("\n", $homepage);
$header = str_getcsv(array_shift($rows));
$i = 0;

foreach ($rows as $row) {
        $rowData = str_getcsv($row);

        // Output each field
        $category = $rowData[0];
        $firstname = $rowData[1];
        $lastname = $rowData[2];
        $email = $rowData[3];
        $gender = $rowData[4];
        $birthDate = $rowData[5];

        $sql = 'INSERT INTO users ( category, firstname, lastname, email, gender, birthDate) VALUES ("'
            .$category.'" ,"'. $firstname.'", "' . $lastname . '", "' .$email .'", "' .$gender .'", "' . $birthDate .'")';

        $response = mysqli_query($conn, $sql);

        $i++;
}

// Insert data in batches
for ($i = 1; $i <= $totalRecords; $i += $batchSize) {
    $sql = "INSERT INTO users ( category, firstname, lastname, email, gender, birthDate) VALUES ";

    for ($j = $i; $j < $i + $batchSize; $j++) {
        // Generate your data here, replace column1, column2, ... with your actual column names
        $sql .= "('$value1', '$value2', ...),";
    }

    // Trim the trailing comma
    $sql = rtrim($sql, ',');

    $conn->query($sql);
}

// Enable foreign key checks and rebuild indexes
$conn->query("ALTER TABLE your_table_name ENABLE KEYS");
$conn->query("SET foreign_key_checks = 1");

// Commit the transaction
$conn->commit();

// Enable autocommit
$conn->autocommit(true);

// Close the connection
$conn->close();
?>
