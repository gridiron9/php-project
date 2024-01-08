<?php

function add_pagination($page, $totalPages, $param){
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
}

function display_table($records){
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

}

function extract_to_csv($connect, $page, $filter, $startIndex, $query){
    $records = get_from_db($connect, $page, $filter, $startIndex, $query);

    if ($records->num_rows > 0) {
        // Open a file for writing
        $currentDate = date("Y-m-d H:i:s");
        $file = fopen("exports/users". $currentDate .".csv", "w");

        // Write the header to the CSV file
        fputcsv($file, array('ID', 'Category','First_Name','Last_Name', 'Email', 'Gender',	'Birth_Date'));

        // Loop through the result set and write data to the CSV file
        while ($row = $records->fetch_assoc()) {
            fputcsv($file, $row);
        }

        // Close the file
        fclose($file);

        echo '<script language="javascript">';
        echo 'alert("CSV file generated successfully.")';
        echo '</script>';
    } else {
        echo "No records found in the users table.";
    }

    $connect->close();
}

?>


