<?php

/**
 * Common code used to administratively select a course.
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
require_once '../_config.php';
require_once '../_connect-mysqli.php';

// Stop and redirect, if any database resources are unavailable.
if ($mysqli->connect_error) {
    header("Location: ".URL_APP."/resource-unavailable.php?err=MySQLi%20Connect&admin=true");
} else {

    // Gather GET variables.
    $action = ucfirst($_GET['type']);
    $msg    = (isset($_GET['success']))? 'The action was successfully accomplished.': '';

    // Which action to peform?
    switch ($action) {
        case 'Enroll':
        case 'View':
            $future_only = 0;
            break;
        case 'Remind':
        case 'Edit':
        default:
            $future_only = 1;
    }

    $options = "<option value=''>Make a Selection</option>";
    $period  = ($future_only)? date("Y-m-d") : date("Y-m-d", strtotime("-90 days"));
    $range   = ($future_only)? 'only': 'and from the last ninety days';

    // Gather course information.
    $query   = "SELECT TID,TDate,Short_Description,TStartTime FROM Training WHERE TDate>='{$period}' ORDER BY TDate ASC";
    $result  = $mysqli->query($query);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $day      = strftime("%b %e %y", strtotime($row['TDate']));
            $time     = " @ ".strftime("%l %p", strtotime($row['TStartTime']));
            $options .="<option value='{$row['TID']}'>{$row['Short_Description']} &nbsp; &nbsp; [ {$day}{$time} ]</option>";
        }
    } else {
        $options = "<option value=''>No courses available.</option>";
    }
    $result->free();

    // Start page code
    $html  = file_get_contents(CHUNK1);
    $html .= file_get_contents(CHUNK2);
    $html .= BACKLINK_ADMIN."<h1>Select Course to {$action}</h1>";

    $html .= "<div id='success'>{$msg}</div><h2>Course List</h2><p>The courses listed are in the future {$range}.</p>";
    $html .= "<form method='post' action='".strtolower($action).".php'><select name='course'>{$options}</select> &nbsp; ";
    $html .= "<input type='submit' value='{$action}' name='submit' id='submit' style='margin-top: .5em;' /></form>";
    $html .= file_get_contents(CHUNK3);
    $html .= file_get_contents(CHUNK4);

    $mysqli->close();
    print $html;
}
