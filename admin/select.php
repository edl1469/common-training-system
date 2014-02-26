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
session_start();
require_once '../_config.php';
require_once '../_connect-mysqli.php';

// Stop and redirect, if any database resources are unavailable.
if ($mysqli->connect_error) {
  header("Location: ".URL_APP."/resource-unavailable.php?err=MySQLi%20Connect&admin=true");
} else {

  // Gather GET variables.
  $action = ucfirst($_GET['type']);
  $msg = (isset($_GET['success']))? 'The action was successfully accomplished.': '';

  // Which action to peform?
  switch ($action) {
    case 'Enroll' :
    case 'View' :
      $future_only = 0;
      break;
    case 'Remind' :
    case 'Edit' :
    default :
      $future_only = 1;
  }

  $options = "<option value=''>Make a Selection</option>";
  $period = ($future_only)? date("Y-m-d"): date("Y-m-d", strtotime("-90 days"));
  $range = ($future_only)? 'only': 'and from the last ninety days';

  // Gather course information.
  $query = "SELECT TID,TDate,Short_Description,TStartTime,IsVisible FROM Training WHERE TDate>='{$period}' ORDER BY TDate ASC";
  $result = $mysqli->query($query);
  if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
      $day = strftime("%b %e %y", strtotime($row['TDate']));
      $time = " @ ".strftime("%l %p", strtotime($row['TStartTime']));
      $mark = (!$row['IsVisible'])? ' &nbsp; &empty;': '';
      $options .= "<option value='{$row['TID']}'>{$row['Short_Description']} &nbsp; &nbsp; [ {$day}{$time} ]{$mark}</option>";
    }
  } else {
    $options = "<option value=''>No courses available.</option>";
  }
  $result->free();
  $mysqli->close();

  // ########## Prepare content
  $html = BACKLINK_ADMIN."<h1>Select Course to {$action}</h1>";
  $html .= "<div id='success'>{$msg}</div><h2>Course List</h2>";
  $html .= "<p>The courses listed are in the future {$range}.</p>";
  $html .= "<form name='select_form' method='post' action='".strtolower($action).".php' onsubmit='return validateForm();'>";
  $html .= "<select name='course'>{$options}</select> &nbsp; ";
  $html .= "<input type='submit' value='{$action}' name='submit' id='submit' /><div id='errorList'></div></form>";

  // ########## Write content
  $page['content'] = $html;
  $page['css'] = "<link href='".URL_COMMON."/css/form.css' rel='stylesheet' type='text/css' />\n"
      ."<link href='".URL_COMMON."/css/select.css' rel='stylesheet' type='text/css' />\n";
  $page['js'] = "<script src='".URL_COMMON."/js/select.js' type='text/javascript'></script>\n";
  include_once (TEMPLATE);
}
