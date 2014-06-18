<?php

/**
 * Administrative date-oriented data collection processing.
 *
 * NOTE: This is a procedural page, preparing content as page processes.
 *
 * PHP version 5
 *
 * @category   ITSWebApplication
 * @package    ESTrainingApp
 * @author     Ed Lara <Ed.Lara@csulb.edu>
 * @author     Steven Orr <Steven.Orr@csulb.edu>
 */
session_start();
require_once '../_config.php';
require_once '../_connect-mysqli.php';

$begin_bits = explode('/', $_POST['startdate']);
$begin = $begin_bits[2].'-'.$begin_bits[0].'-'.$begin_bits[1];
$destinaton = "Location: report.php?error=1";
$end_bits = explode('/', $_POST['enddate']);
$end = $end_bits[2].'-'.$end_bits[0].'-'.$end_bits[1];
$group_name = strtolower(str_replace(" ", "-", NAME_GROUP));
$headers = array();
$q = "SELECT Trainees.*, Training.Short_Description, Training.TDate ";
$q .= "FROM Trainees INNER JOIN Training ON Training.TID=Trainees.TID ";
$q .= "WHERE Training.TDate BETWEEN '{$begin}' AND '{$end}' ORDER BY Training.TDate DESC";

if ($result = $mysqli->query($q)) {
    if ( $result->num_rows > 0) {
        // Prepare file headers.
        $fields = $result->fetch_fields();
        foreach ($fields as $val) {
            $headers[] = $val->name;
        }

        // Prepare for CSV output.
        $file = fopen('php://output', 'w');
        if ($file && $result) {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename='.$group_name.'_training_export.csv');
            header('Pragma: no-cache');
            header('Expires: 0');
            fputcsv($file, $headers);
            while ($row = $result->fetch_row()) {
                fputcsv($file, array_values($row), ",");
            }
        }

        // Nicely close up shop.
        $result->close();
        $mysqli->close();
        die;
    } else {
        $destinaton = "Location: report.php?error=2";
    }
}
$mysqli->close();
header($destinaton);
