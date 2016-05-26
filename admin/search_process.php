<?php


if (!empty($_GET['keywords'])) {
// core files
require_once '../_config.php';
require_once '../_connect-mysqli.php';

// ajax search
$response = '';
$keywords = $_GET['keywords'];
$result = $mysqli->query("SELECT EmpID,FirstName,LastName,Description,TDate FROM Trainees WHERE EmpID LIKE '%".$keywords."%' OR FirstName LIKE '%".$keywords."%' OR LastName LIKE '%".$keywords."%' OR Description LIKE '%".$keywords."%' ORDER BY TDate DESC ");
//$result = $mysqli->query("SELECT EmpID,FirstName,LastName,Description,TDate FROM Trainees WHERE EmpID LIKE '%".$keywords."%'");
if ($result->num_rows > 0) {

while ($row = $result->fetch_assoc()) {
 $fdate = $row['TDate'];
 $mdate = date('F j, Y',strtotime($fdate));
$response .=  '<tr><td>'.$row['EmpID'].'</td><td>'.$row['FirstName']. ' '. $row['LastName'].'</td><td>'.$row['Description']. '</td><td>'.$mdate. '</td></tr>';
}
}



echo $response;
}
?>