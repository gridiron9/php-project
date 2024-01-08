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
<form method="post" style="position: absolute;top: 10px;right: 10px;">
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
    <input type="number" name="age-range-max" id="age-range-max" placeholder="Max" value="<?php if (isset($_GET['age-range-max']) && $_GET['age-range-max'] != "")  echo $_GET['age-range-max']; else echo null ?>">

    <button type="submit">Apply Filters</button>
</form>

<?php
include 'db.php';
include 'view.php';

$homepage = file_get_contents('dataset.csv');
$rows = explode("\n", $homepage);
$headers = str_getcsv(array_shift($rows));

global $records;
global $page;
global $startIndex;

if (check_if_table_exists($connect, "users")){
    if (check_if_table_empty($connect) != 0){
        $time = insert_to_db($connect, $rows);
        echo "<p>Successfully inserted data to database, reload the page. Execution time:$time .</p>";
    }

    $query = query_builder($_GET);


    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $param = $_GET;
    $param['page'] = $page;

    $perPage = 50;
    $startIndex = ((int)$page - 1) * $perPage;
    $records = get_from_db($connect, $page, $_GET, $startIndex, $query);
    $totalPages =  ceil(mysqli_num_rows(get_column_count($connect, $_GET, $query))/$perPage);

    display_table($records);

    add_pagination($page, $totalPages, $param);

}
else{
    if (create_table($connect, "users")){
        echo "<p>Table created with name of users. Please refresh the page. </p>";
    }

}

if (isset($_POST['export'])) {
    $query = query_builder($_GET);
    extract_to_csv($connect, $page, $_GET, $startIndex, $query);
}


?>

</body>
</html>

