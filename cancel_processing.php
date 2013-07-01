<?php

/**
 * Processing the cancel form.
 *
 * NOTE: This is a procedural page, but it has NO output.
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
    // Collect GET
    $dbid    = $mysqli->real_escape_string($_GET['sid']);

    // Pull registrant info.
    $result    = $mysqli->query("SELECT Email,EmpID,TID,Wait FROM Trainees WHERE SID='{$dbid}'");
    $row       = $result->fetch_assoc();
    $reg_empid = $row['EmpID'];
    $reg_email = $row['Email'];
    $reg_tid   = $row['TID'];
    $reg_wait  = $row['Wait'];
    $result->free();

    // Pull current course attendance counts.
    $result    = $mysqli->query("SELECT TSeats,TWait FROM Training WHERE TID='{$reg_tid}'");
    $row       = $result->fetch_assoc();
    $crs_seats = $row['TSeats'];
    $crs_waits = $row['TWait'];
    $result->free();

    // Alter the appropriate seat counts.
    $crs_seats++;
    $crs_waits--;

    // Update course attendance counts, keeping the Wait-list in mind.
    // TODO: Should we still update TSeats, if TWait exists?
    if ($reg_wait) {
        $column = "TWait='{$crs_waits}'";
    } else {
        $column = "TSeats='{$crs_seats}'";
    }
    $mysqli->query("UPDATE Training SET {$column} WHERE TID='{$reg_tid}'");

    // Remove enrollment.
    $mysqli->query("DELETE FROM Trainees WHERE SID='{$dbid}'");

    $mysqli->close();
    header("Location: cancel.php?success=1");
}
