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
session_start();
require_once '_config.php';
require_once '_connect-mysqli.php';

// Stop and redirect, if any database resources are unavailable.
if ($mysqli->connect_error) {
  header("Location: resource-unavailable.php?err=MySQLi%20Connect");
} else {
  // Collect GET
  $dbid = $mysqli->real_escape_string($_GET['sid']);

  // Pull registrant info.
  $result = $mysqli->query("SELECT FirstName,LastName,Email,EmpID,TID,Wait FROM Trainees WHERE SID='{$dbid}'");
  $row = $result->fetch_assoc();
  $reg_first = $row['FirstName'];
  $reg_last = $row['LastName'];
  $reg_empid = $row['EmpID'];
  $reg_email = $row['Email'];
  $reg_tid = $row['TID'];
  $reg_wait = $row['Wait'];
  $result->free();

  // Pull current course attendance counts.
  $result = $mysqli->query("SELECT TSeats,TWait FROM Training WHERE TID='{$reg_tid}'");
  $row = $result->fetch_assoc();
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

  // Pull course information.
      $result = $mysqli->query("SELECT course_email,TDate,Description,TStartTime,TEndTime,Location FROM Training Where TID='{$reg_tid}'");
      $row = $result->fetch_assoc();
      $email_from = (!is_null($row['course_email']))? $row['course_email']: MAIL_GROUP;
      $can_date = $row['TDate'];
      $can_desc = "<p>Your registration has been cancelled for the following : </p><p><strong>".$row['Description']."</strong></p>";
      $can_start = $row['TStartTime'];
      $can_end = $row['TEndTime'];
      $can_loc = $row['Location'];

      $result->free();
      $mysqli->close();

  // Send registration confirmation email.
      $headers = "MIME-Version: 1.0\r\nContent-type:text/html;charset=iso-8859-1\r\nFrom: {$email_from}\r\n";
      $msg = (empty($can_desc))? "<p>Your registration has been cancelled for the following : <strong>".NAME_GROUP." course.</strong></p>": $can_desc;
      $msg .= "<p>The cancellation is for the following person: <strong>" .$reg_first. "&nbsp;" .$reg_last. "</strong></p>";
      $msg .= "<p>Course Details: <ul><li>Date: " . $can_date. "</li><li> StartTime: ". $can_start . "</li><li> EndTime:" . $can_end." </li><li>Location: ".$can_loc. "</li></ul></p>";

      $to = MAIL_GROUP;
      mail($to, 'Training Cancellation Notification', $msg, $headers);


  header("Location: cancel.php?success=1");

}
