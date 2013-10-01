<?php

/**
 * Administrative control of course attendance record-keeping.
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
  header("Location: ".URL_APP."/resource-unavailable.php?err=MySQLi%20Connect&admin=true");
} else {
  $complete = true;

  // Pull GET variables.
  // @TODO: Sanitize and verify these variables.
  $went = (isset($_GET['went']))? $_GET['went']: $complete = false;
  $what = (isset($_GET['what']))? $_GET['what']: $complete = false;
  $who = (isset($_GET['who']))? $_GET['who']: $complete = false;

  // Test for all required variables.
  if ($complete) {
    $went = (int)$mysqli->real_escape_string($went);
    $who = (int)$mysqli->real_escape_string($who);
    $mysqli->query("UPDATE Trainees SET Attend={$went} WHERE SID={$who}");
  }

  $mysqli->close();
  header("Location: view.php?tid={$what}");
}
