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

<form class="filter-form" method="get">
    <label for="category">Category:</label>
    <input type="text" name="category" id="category" placeholder="Search by category" value="">

    <label for="gender">Gender:</label>
    <select name="gender" id="gender">
        <option value="">All</option>
        <option value="male">Male</option>
        <option value="female">Female</option>
    </select>

    <label for="dob">Date of Birth:</label>
    <input type="date" name="dob" id="dob">

    <label for="age">Age:</label>
    <input type="number" name="age" id="age">

    <label for="age-range">Age Range:</label>
    <input type="number" name="age-range-min" id="age-range-min" placeholder="Min">
    <input type="number" name="age-range-max" id="age-range-max" placeholder="Max">

    <button type="submit">Apply Filters</button>
</form>

<?php
include 'db.php';

// Paginate the data
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$perPage = 50;
$startIndex = ($page - 1) * $perPage;
//$column_count = get_column_count($connect);
$records = get_from_db($connect, $page, $_GET, $startIndex);
$totalPages =  ceil(mysqli_num_rows(get_column_count($connect, $_GET))/$perPage);

var_dump($records);

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
        echo '<a href="?page=' . $i . '">' . $i . '</a>';
    }
    //echo '<a href="?page=' . $i . '">' . $i . '</a> ';
}

// Display "Last" link
if ($endRange < $totalPages) {
    if ($endRange < $totalPages - 1) {
        echo '...';
    }
    echo '<a href="?page=' . $totalPages . '">' . $totalPages . '</a>';
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
