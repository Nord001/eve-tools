<?php
// Connect to DB
$db_yapeal = new mysqli('localhost', 'eve_tools', 'eve_tools_pw', 'yapeal');
if ($db_yapeal->connect_errno) {
    die('<h1>' . $db_yapeal->connect_error . '</h1>');
}

// Get wallet journal from DB
//$query = "SELECT accountKey, balance, date FROM corpWalletJournal WHERE `ownerID`='1063967110'";	// Get all data - very slow to plot!
//$query = "SELECT accountKey, AVG(balance) AS balance, date FROM corpWalletJournal WHERE `ownerID`='1063967110' GROUP BY accountKey, unix_timestamp(date) DIV (60*60)";	// Downsample data - use less bandwidth and plot faster
$query = "SELECT accountKey, balance, date FROM corpWalletJournal WHERE `ownerID`='1063967110' GROUP BY accountKey, unix_timestamp(date) DIV (60*60)";	// Downsample data - use less bandwidth and plot faster
$res_journal = $db_yapeal->query($query);

// Get wallet divisions from DB and store in array
//$query = "SELECT accountKey, description FROM corpWalletDivisions WHERE `ownerID`='374470429'";
$query = "SELECT accountKey, description FROM corpWalletDivisions WHERE `ownerID`='1063967110'";
$res_divisions = $db_yapeal->query($query);
$div = array();
while($row = $res_divisions->fetch_assoc()) {
	$div[] = $row;
}

// Array to hold all divisions' data
$dataSets = array();

// Array to hold individual wallet division data
$dataDiv = array();

// Array to hold data points
$data = array();

// Get initial division name
$divNum = $div[0]['accountKey'] - 1000;
$divName = $div[$divNum]['description'];
$dataDiv['label'] = "$divName";


// Retrieve data series from DB result
$accountKeyOld = 1000;
while ($row = $res_journal->fetch_assoc()) {
		
		$accountKey = $row['accountKey'];
		
		if ($accountKey != $accountKeyOld) {
			// Store data points from previous division
			$dataDiv['data'] = $data;
			
			// Store previous division data in overall array
			$dataSets["$divName"] = $dataDiv;
			
			// Reset data point array and account key
			$data = array();
			$accountKeyOld = $accountKey;
			
			// Get new division name
			$divNum = $accountKey - 1000;
			$divName = $div[$divNum]['description'];
			$dataDiv['label'] = "$divName";
		}
		$data[] = array(strtotime($row['date'])*1000, $row['balance']);
}

// Store data points for last division
$dataDiv['data'] = $data;

// Store last division data in overall array
$dataSets["$divName"] = $dataDiv;

echo json_encode($dataSets);

// Free results and close DB connection
$res_journal->close();
$res_divisions->close();
$db_yapeal->close();
?>
