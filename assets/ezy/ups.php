<?php
// Create connection
$con = mysqli_connect("localhost","crmvoxsi_crm","woodkey98","crmvoxsi_crmvox");

// Check connection
if (mysqli_connect_errno()) {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

$result = mysqli_query($con,"SELECT * FROM events WHERE status != 2 AND status != 3");
$results = array();
echo '<div style="height: 320px; overflow: auto;">';
while($row = mysqli_fetch_array($result))
{
	echo $row['eid']. ' - ';
	echo $row['status'];
	echo "<br>";
	$results[] = $row['eid'];
}
echo '</div>';

foreach( $results as $res )
{
	mysqli_query($con,"UPDATE events SET status=1 WHERE eid = ".$res);
}

$result = mysqli_query($con,"SELECT * FROM events WHERE status != 2 AND status != 3");
echo '<div style="height: 320px; overflow: auto;">';
while($row = mysqli_fetch_array($result))
{
	echo $row['eid']. ' - ';
	echo $row['status'];
	echo "<br>";
}
echo '</div>';


mysqli_close($con);
?>