<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Data</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }
        table, th, td {
            border: 2px solid #ddd;
        }
        th, td {
            padding: 4px;
            text-align: left;
        }
    </style>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 4px;
            text-align: left;
        }

        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .pagination a {
            color: #007bff;
            padding: 8px 12px;
            margin: 0 4px;
            text-decoration: none;
            border: 1px solid #ddd;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .pagination a:hover {
            background-color: #007bff;
            color: #fff;
        }
        .pagination .current {
            color: #555;
            padding: 8px 12px;
            margin: 0 4px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: #eee;
            cursor: not-allowed;
        }
        .filter-form {
            margin-bottom: 20px;
        }

        .filter-form select, .filter-form input {
            margin-right: 10px;
        }
    </style>

</head>
<body>
<h2>User Data</h2>
<form method="post" action="index.php" style="position: absolute;top: 10px;right: 10px;">
    <button type="submit" name="export">Export to CSV</button>
</form>

<form class="filter-form" method="get">
    <label for="category">Category:</label>
    <input type="text" name="category" id="category" placeholder="Search by category" value="<?php if (isset($_GET['category'])) echo $_GET['category']; else echo '' ?>">

    <label for="gender">Gender:</label>
    <select name="gender" id="gender">
        <option value="">All</option>
        <option value="male" <?php if (isset($_GET['gender']) && $_GET['gender'] == 'male') echo 'selected="selected"'?>>Male</option>
        <option value="female" <?php if (isset($_GET['gender']) && $_GET['gender'] == 'female') echo 'selected="selected"'?>>Female</option>
    </select>

    <label for="dob">Date of Birth:</label>
    <input type="date" name="dob" id="dob" value="<?php if (isset($_GET['dob']) && $_GET['dob'] != '') echo date('Y-m-d',strtotime($_GET["dob"])); else echo null ?>">

    <label for="age">Age:</label>
    <input type="number" name="age" id="age" value="<?php if (isset($_GET['age']) && $_GET['age'] != "")  echo $_GET['age']; else echo null ?>">

    <label for="age-range">Age Range:</label>
    <input type="number" name="age-range-min" id="age-range-min" placeholder="Min" value="<?php if (isset($_GET['age-range-min']) && $_GET['age-range-min'] != "")  echo $_GET['age-range-min']; else echo null ?>">
    <input type="number" name="age-range-max" id="age-range-max" placeholder="Max" value="<?php if (isset($_GET['age-range-max']) && $_GET['age-range-max'] != "")  echo $_GET['age-range-min']; else echo null ?>">

    <button type="submit">Apply Filters</button>
</form>

<?php
include 'db.php';

$db = new Database();

$db->query_builder($_GET);



die($db->insert_to_db());





$check_table = check_if_table_exists($connect, "users");



if (isset($_POST['export'])) {
    // SQL query to retrieve data from the users table
    $sql = "SELECT id, firstname, lastname, email FROM users limit 50";
    $result = $connect->query($sql);

    if ($result->num_rows > 0) {
        // Open a file for writing
        $file = fopen("users.csv", "w");

        // Write the header to the CSV file
        fputcsv($file, array('ID', 'First Name', 'Last Name', 'Email'));

        // Loop through the result set and write data to the CSV file
        while ($row = $result->fetch_assoc()) {
            fputcsv($file, $row);
        }

        // Close the file
        fclose($file);

        echo "CSV file generated successfully.";
    } else {
        echo "No records found in the users table.";
    }

// Close the database connection
    $conn->close();

}


$page = isset($_GET['page']) ? $_GET['page'] : 1;
$param = $_GET;
$param['page'] = $page;
//var_dump($_GET['dob']);
//die();
// Paginate the data

$perPage = 50;
$startIndex = ((int)$page - 1) * $perPage;
$records = get_from_db($connect, $page, $_GET, $startIndex);
$totalPages =  ceil(mysqli_num_rows(get_column_count($connect, $_GET))/$perPage);

// Display the table
echo '<table  border="1">';
echo '<thead><tr><th>Id</th><th>Category</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Gender</th><th>Birth Date</th></tr></thead>';
echo '<tbody>';
foreach ($records as $row) {
    echo '<tr>';
    foreach ($row as $value) {
        echo '<td>' . $value . '</td>';
    }
    echo '</tr>';
}
echo '</tbody>';
echo '</table>';

$range = 1; // Number of pages to show before and after the current page
$startRange = max(1, $page - $range);
$endRange = min($totalPages, $page + $range);
// Display "First" link
echo '<div class="pagination">';
if ($startRange > 1) {
    echo '<a href="?page=1">1</a>';
    if ($startRange > 2) {
        echo '...';
    }
}

// Display page links within the range
for ($i = $startRange; $i <= $endRange; $i++) {
    if ($i == $page) {
        echo '<span class="current">' . $i . '</span>';
    } else {
        $param["page"] = $i;
        $url = http_build_query($param);
        echo '<a href="?' . $url .'">' . $i . '</a>';
    }
    //echo '<a href="?page=' . $i . '">' . $i . '</a> ';
}

// Display "Last" link
if ($endRange < $totalPages) {
    if ($endRange < $totalPages - 1) {
        echo '...';
    }
    $param["page"] = $totalPages;
    $url = http_build_query($param);
    echo '<a href="?' . $url . '">' . $totalPages . '</a>';
}
echo '</div>';
?>

</body>
</html>


<?php

include 'db.php';


$table_name = "php_docker_table";


$homepage = file_get_contents('dataset.txt');
$rows = explode("\n", $homepage);
$header = str_getcsv(array_shift($rows));

//insert_to_db($connect, $rows);

//// Start HTML table
//echo '<table border="1">';
//echo '<tr>';
//foreach ($header as $field) {
//    echo '<th>' . $field . '</th>';
//}
//echo '</tr>';
//
//// Process each row
//
//
//echo '</table>';
////$result = get_from_db($connect, 10);
//if ($result->num_rows > 0) {
//    // output data of each row
//    while($row = $result->fetch_assoc()) {
//        echo "id: " . $row["id"]. " - Name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";
//    }
//} else {
//    echo "0 results";
//}
die();
