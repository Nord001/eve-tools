<?php
// Get variables from URL
$corp = $_GET['corp'];

// Connect to evetools DB
$db_evetools = new mysqli('localhost', 'evetools', 'qweasd', 'eve_tools_API');
if ($db_evetools->connect_errno) {
    die('<h1>' . $db_evetools->connect_error . '</h1>');
}

// Connect to EVE static dump DB
$db_staticdump = new mysqli('localhost', 'evetools', 'qweasd', 'eveStaticDump');
if ($db_staticdump->connect_errno) {
    die('<h1>' . $db_staticdump->connect_error . '</h1>');
}

// Get POS ownerID
$ownerID;
if ($corp == 'kazo') {
	$ownerID = 1063967110;
} else if ($corp == 'fict') {
	$ownerID = 374470429;
} else {
	echo '<tr><td>INCORRECT CORP ARG</td></tr>';
}

// Retrieve list of current POSes
$query = "SELECT * FROM corpStarbaseList WHERE `ownerID`='".$ownerID."'";
$res_POS = $db_evetools->query($query);


// Get info for each POS
while ($row_pos = $res_POS->fetch_assoc()) {
	// New table row
	echo '<tr>';

	// POS type
	$query = "SELECT typeName FROM invTypes WHERE `typeID`='".$row_pos['typeID']."'";
	$res_tmp = $db_staticdump->query($query);
	$posType = $res_tmp->fetch_row();
	echo '<td>'.$posType[0].'</td>';
	
	// Location
	$query = "SELECT itemName FROM invNames WHERE `itemID`='".$row_pos['moonID']."'";
	$res_tmp = $db_staticdump->query($query);
	$posLoc = $res_tmp->fetch_row();
	echo '<td>'.$posLoc[0].'</td>';
	
	// Current status
	$state = $row_pos['state'];
	if ($state == 0) {
		$state = "Unachored!";	
	} else if ($state == 1) {
		$state = "Offline";
	} else if ($state == 2) {
		$state = "Onlining at ".$row_pos['onlineTimestamp'];
	} else if ($state == 3) {
		$state = "Reinforced until ".$row_pos['stateTimestamp'];
	} else if ($state == 4) {
		$state = "Online since ".$row_pos['onlineTimestamp'];
	}
	echo '<td>'.$state.'</td>';
	
	
	// Retrieve fuel list
	$query = "SELECT * FROM corpFuel WHERE `ownerID`='".$ownerID."' AND `posID`='".$row_pos['itemID']."'";
	$res_fuel = $db_evetools->query($query);
	
	// Array to hold fuel amounts
	$fuels = array();
	
	while ($row_fuel = $res_fuel->fetch_assoc()) {
		// Fuel type
		$query = "SELECT typeName FROM invTypes WHERE `typeID`='".$row_fuel['typeID']."'";
		$res_tmp = $db_staticdump->query($query);
		$fuelType = $res_tmp->fetch_row();
		$fuelType = $fuelType[0];
		
		if (preg_match("/block/i",$fuelType)) {
			$fuels['blocks'] = $row_fuel['quantity'];
		} else if (preg_match("/Charter/i",$fuelType)) {
			$fuels['charters'] = $row_fuel['quantity'];
		} else if (preg_match("/Stront/i",$fuelType)) {
			$fuels['stront'] = $row_fuel['quantity'];
		}
	}	echo '<td>'.$fuelType.'</td>';
	
	// Fuel blocks
#	if ($fuels['blocks']) {
#		echo '<td>'.$fuels['blocks'].'</td>';
#	} else {
#		echo '<td>Offline!</td>';
#	}
#	
#	// Charters
#	if ($fuels['charters']) {
#		echo '<td>'.$fuels['charters'].'</td>';
#	} else {
#		echo '<td>N/A</td>';
#	}
#	
#	// Fuel remaining
#	echo '<td>'.$fuels['remaining'].'</td>';
#	
#	// Stront
#	if ($fuels['stront']) {
#		echo '<td>'.$fuels['stront'].'</td>';
#	} else {
#		echo '<td>No stront!</td>';
#	}
#	
#	// Reinforcement timer
#	echo '<td>'.$fuels['reinfTime'].'</td>';

	// End of table row
	echo '</tr>';	
}

// Free results and close DB connection
$res_POS->close();
#$res_fuel->close();
#$res_tmp->close();
$db_evetools->close();
$db_staticdump->close();
?>
