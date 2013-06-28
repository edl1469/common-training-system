<?php

/**
 * Form to view and cancel registrations.
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

// Set-up files.
session_start();
require_once '_config.php';
require_once '_connect-mysqli.php';

// Stop and redirect, if any database resources are unavailable.
if ($mysqli->connect_error) {
    header("Location: resource-unavailable.php?err=MySQLi%20Connect");
} else {
    $back_link  = "<div id='back'><a href='".URL_APP."'>Return to ".NAME_GROUP."</a></div>";

    // Populate form values from Session or Get traffic.
    $reg_email = $_SESSION['user'];
    $reg_empid = $_SESSION['emp'];

    // Was there a previous cancellation action?
    $message = (isset($_GET['success']))
            ? 'You have successfully canceled that course.'
            : 'Click on a link to cancel your registration.';

    // Pull and parse course information.
    $list      = '';
    $today     = date("Y-m-d");
    $reg_email = $mysqli->real_escape_string($reg_email);
    $reg_empid = $mysqli->real_escape_string($reg_empid);
    $query     = "SELECT * FROM Trainees WHERE EmpID='{$reg_empid}' AND Email='{$reg_email}' "
                ."AND TDate>='{$today}' ORDER BY TDate ASC";
    $result    = $mysqli->query($query);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $list .= "<tr><td>{$row['Description']} ";
            $list .= ($row['Wait']) ? "<span class='waiting'>( Wait-listed )</span></td>": '</td>';
            $list .= "<td>".date("l, F j, Y", strtotime($row['TDate']))." @ "
                    .date("g:i", strtotime($row['TStartTime']))." - "
                    .date("g:i A", strtotime($row['TEndTime']))."</td>";
            $list .= "<td><a href='cancel_processing.php?sid={$row['SID']}'>Cancel</a></td></tr>";
        }
    }
    $result->free();

    // Start page code
    $html  = file_get_contents(CHUNK1);
    $html .= "<style type='text/css'> .waiting { float: right; font-weight: bold; margin-right: 2em; } </style>";
    $html .= file_get_contents(CHUNK2);
    $html .= $back_link."<h1>Cancel A Registration</h1>";

    // Display result of course query.
    if (empty($list)) {
        $html .=  "<p><strong>No registration records found.</strong></p>";
    } else {
        $html .= "<div id='can'>$message</div><table width='100%'><thead><caption>Course List</caption><tr>";
        $html .= "<th scope='col'>Name</th><th scope='col'>Date @ Time</th><th scope='col'>Action</th></tr></thead>";
        $html .= "{$list}</table>";
    }
    $html .= file_get_contents(CHUNK3);
    $html .= file_get_contents(CHUNK4);

    $mysqli->close();
    print $html;
}
