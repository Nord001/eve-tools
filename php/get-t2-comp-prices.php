<?php
// Get variables from URL
$date=$_GET['date'];

// Connect to DB
$db_t2_comps = new mysqli('localhost', 'eve_tools', 'eve_tools_pw', 'eve_tools_t2_comps');
if ($db_t2_comps->connect_errno) {
    die('<p><b>Failed to connect to MySQL: ' . $db_t2_comps->connect_error . '</b></p>');
}

// Get prices from DB
$query = 'SELECT * FROM ' . $date;
$result = $db_t2_comps->query($query);
$num = $result->num_rows;

if ($num == 0) {
	die('<p><b>Data missing...</b></p>');
} else if ($num != 47) {
	die('<p><b>Data corrupted...</b></p>');
}

// Export material prices
echo "<table border='1'>
<tr>
<th>Moon Materials</th>
<th>Jita Price</th>
</tr>";

for ($i = 0; $i < 11; $i++) {
	$row = $result->fetch_assoc();
        echo '<tr>';
        echo '<td>'.preg_replace('/_/', ' ', $row['item']).'</td>';
        echo '<td>'.number_format($row['jita_price'], 2).'</td>';
        echo '</tr>';
}
echo '</table>';
echo '<br />';
echo '<p>All prices rounded to nearest whole number</p>';
echo '<br />';

// Export component prices
for ($i = 11; $i < 47; $i++) {
	$row = $result->fetch_assoc();

	if (($i-11) % 9 == 0 ) {
		echo "<table border='1'>";
		echo '<tr>';

		if ($i == 11)
			echo '<th>Amarr Components</th>';
		else if ($i == 20)
			echo '<th>Caldari Components</th>';
		else if ($i == 29)
			echo '<th>Gallente Components</th>';
		else if ($i == 38)
			echo '<th>Minmatar Components</th>';

		echo '<th>Jita Price</th>';
		echo '<th>Cost Price</th>';
		echo '</tr>';
	}

	echo '<tr>';
	echo '<td>' . preg_replace('/_/',' ',$row['item']) . '</td>';
	echo '<td>' . number_format($row['jita_price']) . '</td>';
	echo '<td>' . number_format($row['cost_price']) . '</td>';
	echo '</tr>';

	if (($i-19) % 9 == 0 ) {
		echo '</table>';
		echo '<br />';
	}
}

// Free results and close DB connection
$result->close();
$db_t2_comps->close();
?>
