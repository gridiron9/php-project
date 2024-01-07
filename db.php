<?php

global $connect;
$connect = mysqli_connect(
    'db', # service name
    'php_docker', # username
    'password', # password
    'php_docker' # db table
);

if ($connect->connect_error) {
    die("Connection failed: " . $connect->connect_error);
}

function insert_to_db($connect, $rows){
    $batchSize = 1000;
    $totalRecords = 100000;

    $j = 1;
    $sql = 'INSERT INTO users ( category, firstname, lastname, email, gender, birthDate) VALUES ';
    for ($i = 0; $i < $totalRecords; $i ++) {

        $rowData = str_getcsv($rows[$i]);
        $category = $rowData[0];
        $firstname = $rowData[1];
        $lastname = $rowData[2];
        $email = $rowData[3];
        $gender = $rowData[4];
        $birthDate = $rowData[5];

        if ($j == $batchSize){
            $sql = rtrim($sql, ',');
            $response = mysqli_query($connect, $sql);
            $j = 0;
            $sql = 'INSERT INTO users ( category, firstname, lastname, email, gender, birthDate) VALUES ';
        }

        $sql .= '("'.$category.'" ,"'. $firstname.'", "' . $lastname . '", "' .$email .'", "' .$gender .'", "' . $birthDate .'"),';
        $j++;
    }

    $sql = rtrim($sql, ',');
    $response = mysqli_query($connect, $sql);
}

function get_from_db($connect, $page, $filters, $startIndex){
    $sql = "SELECT * FROM users WHERE id >0";
    if (isset($filters['category']) && $filters['category'] != '') {
        $category = $filters['category'];
        $sql .= " and category  like '%$category%'";
    }
    if (isset($filters['gender']) && $filters['gender'] != '' ) {
        $gender = $filters['gender'];
        $sql .= " and gender  = '$gender'";
    }
    if (isset($filters['dob']) && $filters['dob'] != '') {
        $date_birth = $filters['dob'];
        $sql .= " AND DATE(birthDate) = '" . $date_birth . "'";

    }
    if (isset($filters['age']) && $filters['age'] != '') {
        $age = $filters['age'];
        $sql .= " and DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(), birthDate)), '%Y') + 0 = $age";
    }
    if (isset($filters['age-range-min']) && $filters['age-range-min'] != '') {
        $min_age = $filters['age-range-min'];
        $sql .= " and DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(), birthDate)), '%Y') + 0 >= $min_age";
    }
    if (isset($filters['age-range-max']) && $filters['age-range-max'] != '') {
        $max_age = $filters['age-range-max'];
        $sql .= " and DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(), birthDate)), '%Y') + 0 <= $max_age";
    }

    $sql .= " LIMIT 50 OFFSET " .$startIndex;

    $response = mysqli_query($connect, $sql);

    return $response;
}

function get_column_count($connect, $filters){
    $sql = "SELECT * FROM users WHERE id >0";
    if (isset($filters['category'])) {
        $category = $filters['category'];
        $sql .= " and category  like '%$category%'";
    }
    if (isset($filters['gender']) && $filters['gender'] != '' ) {
        $gender = $filters['gender'];
        $sql .= " and gender  = '$gender'";
    }
    if (isset($filters['dob']) && $filters['dob'] != '') {
        $date_birth = $filters['dob'];
        $sql .= " AND DATE(birthdate) = " .$date_birth;
    }
    if (isset($filters['age']) && $filters['age'] != '') {
        $age = $filters['age'];
        $sql .= " and DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(), birthDate)), '%Y') + 0 = $age";
    }

    if (isset($filters['age-range-min']) && $filters['age-range-min'] != '') {
        $min_age = $filters['age-range-min'];
        $sql .= " and DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(), birthDate)), '%Y') + 0 >= $min_age";
    }
    if (isset($filters['age-range-max']) && $filters['age-range-max'] != '') {
        $max_age = $filters['age-range-max'];
        $sql .= " and DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(), birthDate)), '%Y') + 0 <= $max_age";
    }

    $response = mysqli_query($connect, $sql);

    return $response;
}

