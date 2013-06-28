<?php

/**
 * Administrative tool to send workshop email reminder.
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

    // Pull and parse course information.
    $tid       = $mysqli->real_escape_string($_POST['course']);
    $result    = $mysqli->query("SELECT * FROM Training WHERE TID='{$tid}'");
    $row       = $result->fetch_assoc();
    $crs_date  = $row['TDate'];
    $crs_datef = date("l, F j, Y", strtotime($row['TDate']));
    $crs_detl  = $row['Details'];
    $crs_end   = date("g:i a", strtotime($row['TEndTime']));
    $crs_instr = $row['Trainer'];
    $crs_loc   = $row['Location'];
    $crs_name  = $row['Description'];
    $crs_priv  = $row['IsPrivate'];
    $crs_seats = $row['TSeats'];
    $crs_start = date("g:i", strtotime($row['TStartTime']));
    $crs_waits = $row['TWait'];
    $result->free();

    $message = "Please remember that you are enrolled in a training course, titled '{$crs_name}', "
            ."to be held at {$crs_start} on {$crs_datef} in {$crs_loc}.";
    $privacy = ($crs_priv) ? 'Private' : 'Public';

    // Start page code
    $html  = file_get_contents(CHUNK1);
    $html .= "<link href='".URL_COMMON."/css/remind.css' rel='stylesheet' type='text/css' /></head><body>";
    $html .= file_get_contents(CHUNK2);
    $html .= BACKLINK_ADMIN."<h1>Course Reminder</h1>";

    $html .= "<p>The course details are as follows:</p>";
    $html .= "<ul><li>Name: {$crs_name}</li><li>Date: {$crs_datef}</li><li>Time: {$crs_start} - {$crs_end}</li>";
    $html .= "<li>Location: {$crs_loc}</li><li>Instructor: {$crs_instr}</li><li>Category: {$privacy}</li>";
    $html .= "<li>Seats Available: {$crs_seats}</li><li>Wait-listed Registrants: {$crs_waits}</li></ul>";
    $html .= "<p><strong><em>NOTE:</em> Email text formatting is not supported. The email will be sent as text-only.</strong>";
    $html .= "<form method='post' name='form' action='remind_processing.php'><fieldset>
        <p><label for='message'>Email Text</label>
            <textarea name='message' id='message' rows='10' cols='60'>{$message}</textarea></p>
        <p><input type='submit' name='submit' value='Send Reminder'></p>
        <input type='hidden' name='tid' value='{$tid}'></fieldset></form>";
    $html .= file_get_contents(CHUNK3);
    $html .= file_get_contents(CHUNK4);

    $mysqli->close();
    print $html;
}
