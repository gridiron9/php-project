<?php

class Database {

    public $connect;
    public $query;
    public $headers;
    public $rows;


    function __construct(){
        $this->connect = mysqli_connect(
            'db', # service name
            'php_docker', # username
            'password', # password
            'php_docker' # db table
        );

        if ($this->connect->connect_error) {
            die("Connection failed: " . $this->connect->connect_error);
        }

        $homepage = file_get_contents('dataset.txt');
        $this->rows = explode("\n", $homepage);
        $this->headers = str_getcsv(array_shift($this->rows));

    }
    public function query_builder($filters){
        $sql = "";
        if (isset($filters['category']) && $filters['category'] != '') {
            $category = $filters['category'];
            $sql .= " and category  like '%$category%'";
        }
        if (isset($filters['gender']) && $filters['gender'] != '') {
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

        $this->query = $sql;

    }
    public function insert_to_db()
    {
        $batchSize = 1000;
        $totalRecords = 100000;

        $j = 1;
        $sql = 'INSERT INTO users ( category, firstname, lastname, email, gender, birthDate) VALUES ';
        for ($i = 0; $i < $totalRecords; $i++) {

            $rowData = str_getcsv($this->rows[$i]);
            $category = $rowData[0];
            $firstname = $rowData[1];
            $lastname = $rowData[2];
            $email = $rowData[3];
            $gender = $rowData[4];
            $birthDate = $rowData[5];

            if ($j == $batchSize) {
                $sql = rtrim($sql, ',');
                $response = mysqli_query($this->connect, $sql);
                $j = 0;
                $sql = 'INSERT INTO users ( category, firstname, lastname, email, gender, birthDate) VALUES ';
            }

            $sql .= '("' . $category . '" ,"' . $firstname . '", "' . $lastname . '", "' . $email . '", "' . $gender . '", "' . $birthDate . '"),';
            $j++;
        }

        $sql = rtrim($sql, ',');
        $response = mysqli_query($this->connect, $sql);
        return true;
    }

    public function get_from_db($connect, $page, $filters, $startIndex)
    {
        $sql = "SELECT * FROM users WHERE id >0";
        if (isset($filters['category']) && $filters['category'] != '') {
            $category = $filters['category'];
            $sql .= " and category  like '%$category%'";
        }
        if (isset($filters['gender']) && $filters['gender'] != '') {
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

        $sql .= " LIMIT 50 OFFSET " . $startIndex;

        $response = mysqli_query($connect, $sql);

        return $response;
    }

    public function get_column_count($connect, $filters)
    {
        $sql = "SELECT * FROM users WHERE id >0";
        if (isset($filters['category'])) {
            $category = $filters['category'];
            $sql .= " and category  like '%$category%'";
        }
        if (isset($filters['gender']) && $filters['gender'] != '') {
            $gender = $filters['gender'];
            $sql .= " and gender  = '$gender'";
        }
        if (isset($filters['dob']) && $filters['dob'] != '') {
            $date_birth = $filters['dob'];
            $sql .= " AND DATE(birthdate) = " . $date_birth;
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

    public function check_if_table_exists($table_name)
    {
        $sql = "SHOW TABLES IN php_docker";

// perform the query and store the result
        $result = mysqli_query($this->connect, $sql);

        foreach ($result as $re){
            if ($re["Tables_in_php_docker"] == $table_name)
                return true;
        }

        return false;
    }

    public function create_table($table_name){
        $sql = "CREATE TABLE php_docker.users (id INT NOT NULL AUTO_INCREMENT , category VARCHAR(255) NULL , firstname VARCHAR(255) NULL , lastname VARCHAR(255) NULL , email VARCHAR(255) NULL , gender VARCHAR(255) NULL , birthDate DATE NULL , PRIMARY KEY (id)) ENGINE = InnoDB";

// perform the query and store the result
        $result = mysqli_query($this->connect, $sql);

        if ($result){
            return true;
        }

        return false;
    }

    public function check_if_table_empty(){
        $sql = "SELECT COUNT(*) FROM users";

// perform the query and store the result
        $result = mysqli_query($this->connect, $sql);

        if ($result) {
            $rowCount = $result->fetch_row()[0];

            if ($rowCount == 0) {
                return true;
            } else {
                return false;
            }
        } else {
            die("Error checking if table is empty: " . $this->connect->error);
        }
    }

}

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

function insert_to_db($connect, $rows)
{
    $start = microtime(true);
    $batchSize = 10000;
    $totalRecords = 100000;

    $j = 1;
    $sql = 'INSERT INTO users ( category, firstname, lastname, email, gender, birthDate) VALUES ';
    for ($i = 0; $i < $totalRecords; $i++) {

        $rowData = str_getcsv($rows[$i]);
        $category = $rowData[0];
        $firstname = $rowData[1];
        $lastname = $rowData[2];
        $email = $rowData[3];
        $gender = $rowData[4];
        $birthDate = $rowData[5];

        if ($j == $batchSize) {
            $sql = rtrim($sql, ',');
            $response = mysqli_query($connect, $sql);
            $j = 0;
            $sql = 'INSERT INTO users ( category, firstname, lastname, email, gender, birthDate) VALUES ';
        }

        $sql .= '("' . $category . '" ,"' . $firstname . '", "' . $lastname . '", "' . $email . '", "' . $gender . '", "' . $birthDate . '"),';
        $j++;
    }

    $sql = rtrim($sql, ',');
    $response = mysqli_query($connect, $sql);
    $time_elapsed_secs = microtime(true) - $start;
    return $time_elapsed_secs;
}

function get_from_db($connect, $page, $filters, $startIndex, $query)
{
    $sql = "SELECT * FROM users WHERE id > 0" . $query ;

    $sql .= " LIMIT 50 OFFSET " . $startIndex;

    $response = mysqli_query($connect, $sql);

    return $response;
}

function get_column_count($connect, $filters, $query)
{
    $sql = "SELECT * FROM users WHERE id >0" . $query;


    $response = mysqli_query($connect, $sql);

    return $response;
}

function check_if_table_exists($connect, $table_name)
{
    $sql = "SHOW TABLES IN php_docker";

// perform the query and store the result
    $result = mysqli_query($connect, $sql);

    foreach ($result as $re){
        if ($re["Tables_in_php_docker"] == $table_name)
            return true;
    }

    return false;
}

function create_table($connect, $table_name){
    $sql = "CREATE TABLE php_docker.users (id INT NOT NULL AUTO_INCREMENT , category VARCHAR(255) NULL , firstname VARCHAR(255) NULL , lastname VARCHAR(255) NULL , email VARCHAR(255) NULL , gender VARCHAR(255) NULL , birthDate DATE NULL , PRIMARY KEY (id)) ENGINE = InnoDB";

// perform the query and store the result
    $result = mysqli_query($connect, $sql);

    if ($result){
        return true;
    }

    return false;
}
function check_if_table_empty($connect){
    $sql = "SELECT COUNT(*) FROM users";

// perform the query and store the result
    $result = mysqli_query($connect, $sql);

    if ($result) {
        $rowCount = $result->fetch_row()[0];

        if ($rowCount == 0) {
            return true;
        } else {
            return false;
        }
    } else {
        die("Error checking if table is empty: " . $connect->error);
    }
}
function query_builder($filters){
    $sql = "";
    if (isset($filters['category']) && $filters['category'] != '') {
        $category = $filters['category'];
        $sql .= " and category  like '%$category%'";
    }
    if (isset($filters['gender']) && $filters['gender'] != '') {
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

    return $sql;
}

