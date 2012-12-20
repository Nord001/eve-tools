<?php
// Get variables from URL
$year = $_GET['year'];
$month = $_GET['month'];

// Connect to DB
$mysqli = new mysqli('localhost', 'wwwEveTools', 'qweasd', 'wwwEveToolsT2Comps');
if ($mysqli->connect_errno) {
    die('<option>' . $mysqli->connect_error . '</option>');
}

// Get dates from DB
if ($year == '' && $month == '') {
	$query = 'SHOW TABLES';
	echo '<option>Year:</option>';
} else if ($month == '') {
	$query = "SHOW TABLES LIKE '".$year."_%'";
	echo '<option>Month:</option>';
} else {
	$query = "SHOW TABLES LIKE '".$year."_".$month."_%'";
	echo '<option>Day:</option>';
}
$result = $mysqli->query($query);

// List all dates in range
$date_old = 0;
while ($row = $result->fetch_row()) {
        if ($year == '' && $month == '') {
        	$date = substr($row[0], 0, 4);
        } else if ($month == '') {
		$date = substr($row[0], 5, 2);
	} else {
		$date = substr($row[0], 8, 2);
	}

        if ($date != $date_old) {
                echo '<option>'.$date.'</option>';
                $date_old = $date;
        }
}

// Free results and close DB connection
$result->close();
$mysqli->close();
?>
