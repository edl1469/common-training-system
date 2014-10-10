<?php

/**
 * Processing the administrative course edit form.
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
require_once '../_config.php';
require_once '../_connect-mysqli.php';

// Stop and redirect, if any database resources are unavailable.
if ($mysqli->connect_error) {
  header("Location: ".URL_APP."/resource-unavailable.php?err=MySQLi%20Connect");
} else {

  // Collect POST
  $pretty_end = $mysqli->real_escape_string($_POST['cetime'].' '.$_POST['cemeridian']);
  $pretty_start = $mysqli->real_escape_string($_POST['cstime'].' '.$_POST['csmeridian']);
  $crs_date = date("Y-m-d", strtotime($_POST['cdate']));
  $crs_detl = $mysqli->real_escape_string($_POST['details']);
  $crs_email = $mysqli->real_escape_string($_POST['confirm']);
  $crs_end = date("G:i", strtotime($pretty_end));
  $crs_id = $mysqli->real_escape_string($_POST['tid']);
  $crs_loc = $mysqli->real_escape_string($_POST['location']);
  $crs_long = $mysqli->real_escape_string($_POST['description']);
  $crs_priv = (isset($_POST['private']))? 1: 0;
  $crs_seat = (int)$_POST['seats'];
  $crs_short = $mysqli->real_escape_string($_POST['short_desc']);
  $crs_start = date("G:i", strtotime($pretty_start));
  $crs_trnr = $mysqli->real_escape_string($_POST['trainer']);
  $crs_reply = $mysqli->real_escape_string($_POST['course_email']);
  $crs_visbl = (isset($_POST['visible']))? 1: 0;

  // Prepare and update new course.
  $cols = "TDate='{$crs_date}', IsPrivate={$crs_priv}, IsVisible={$crs_visbl}, Description='{$crs_long}', TStartTime='{$crs_start}', "
      ."TEndTime='{$crs_end}', TSeats='{$crs_seat}', Email_Confirm='{$crs_email}', Location='{$crs_loc}', "
      ."Trainer='{$crs_trnr}', Short_Description='{$crs_short}', Details='{$crs_detl}', course_email='{$crs_reply}'";
  $mysqli->query("UPDATE Training SET {$cols} WHERE TID='{$crs_id}'");
  $mysqli->close();

  header("Location: index.php");
}
