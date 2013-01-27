<?php
// Get variables from URL
$year = $_GET['year'];
$month = $_GET['month'];

// Connect to DB
$db_t2_comps = new mysqli('localhost', 'eve_tools', 'eve_tools_pw', 'eve_tools_t2_comps');
if ($db_t2_comps->connect_errno) {
    die('<option>' . $db_t2_comps->connect_error . '</option>');
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
$result = $db_t2_comps->query($query);

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
$db_t2_comps->close();
?>
