<?php

/**
 * Processing the administrative course creation form.
 *
 * NOTE: This is a procedural page, but it has NO output.
 *
 * This processing script performs the following:
 *  1. Processes the original course creation form, fowards to email creation form.
 *  2. Processes the email creation form.
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

// Stop and redirect, if any database resources are unavailable.
if ($mysqli->connect_error) {
  header("Location: ".URL_APP."/resource-unavailable.php?err=MySQLi%20Connect");
} else {

  // Process the course creation
  if (!isset($_POST['submit'])) {

    // Direct access, so send away
    $destination = 'Location: index.php';
  } else {

    // Collect POST
    $pretty_end = $mysqli->real_escape_string($_POST['cetime'].' '.$_POST['cemeridian']);
    $pretty_start = $mysqli->real_escape_string($_POST['cstime'].' '.$_POST['csmeridian']);

    $crs_date = date("Y-m-d", strtotime($_POST['cdate']));
    $crs_detl = $mysqli->real_escape_string($_POST['details']);
    $crs_end = date("G:i", strtotime($pretty_end));
    $crs_loc = $mysqli->real_escape_string($_POST['location']);
    $crs_long = $mysqli->real_escape_string($_POST['description']);
    $crs_priv = (isset($_POST['private']))? 1: 0;
    $crs_seat = (int)$_POST['seats'];
    $crs_short = $mysqli->real_escape_string($_POST['short_desc']);
    $crs_start = date("G:i", strtotime($pretty_start));
    $crs_trnr = $mysqli->real_escape_string($_POST['trainer']);
    $crs_visbl = 1;

    // Construct suggested confirmation email text.
    $text = "<html><body><h3>Thank you for your enrollment.</h3><p>Your training session, ";
    $text .= "<strong>{$crs_long}</strong>, will be held on ".date("l, F j, Y", strtotime($crs_date));
    $text .= " at {$pretty_start} to {$pretty_end} in {$crs_loc}.</p><p>To cancel a reservation, please visit ";
    $text .= "<a href='".URL_FULL."'>".NAME_GROUP."</a> and click '<strong>Manage Your Registrations</strong>'.</p>";
    $text .= "<p>Thank you, ".NAME_GROUP." Group</p></body></html>";
    $text = $mysqli->real_escape_string($text);

    // Prepare and insert new course.
    $cols = "TDate, IsPrivate, IsVisible, Description, TStartTime, TEndTime, TSeats, Email_Confirm,";
    $cols .= " Location, Trainer, Short_Description, Details, TWait";
    $vals = "'{$crs_date}', {$crs_priv}, {$crs_visbl}, '{$crs_long}', '{$crs_start}', '{$crs_end}', {$crs_seat}, '{$text}', ";
    $vals .= "'{$crs_loc}', '{$crs_trnr}', '{$crs_short}', '{$crs_detl}', 0";
    $mysqli->query("INSERT INTO Training ( {$cols} ) VALUES ( {$vals} )");

    // Test if insert failed.
    if (!$mysqli->insert_id) {
      $destination = 'Location: create.php?err=1';
    } else {
      $destination = 'Location: create.php?success=1';
    }

  }
  $mysqli->close();

  header($destination);
}
