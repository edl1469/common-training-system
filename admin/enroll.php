<?php

/**
 * Administrative manual course enrollment tool.
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

  // Pull POST / GET variables.
  $tid = (isset($_GET['TID']))? $_GET['TID']: $_POST['course'];
  $errormsg = (isset($_GET['dup']))? 'This person has been previously registered.': '';
  $successmsg = (isset($_GET['success']))? 'The person was successfully registered.': '';

  // Pull and parse course information.
  $tid = $mysqli->real_escape_string($tid);
  $result = $mysqli->query("SELECT * FROM Training WHERE TID='{$tid}'");
  $row = $result->fetch_assoc();
  $crs_date = $row['TDate'];
  $crs_datef = date("l, F j, Y", strtotime($row['TDate']));
  $crs_end = date("g:i A", strtotime($row['TEndTime']));
  $crs_instr = $row['Trainer'];
  $crs_loc = $row['Location'];
  $crs_name = $row['Description'];
  $crs_seats = $row['TSeats'];
  $crs_start = date("g:i A", strtotime($row['TStartTime']));
  $result->free();

  // Pull and parse registrant information.
  $registrants = "<p>No one is wait-listed in this course yet.</p>";
  $tid = $mysqli->real_escape_string($tid);
  $result = $mysqli->query("SELECT SID,FirstName,LastName FROM Trainees WHERE TID='{$tid}' AND Wait=1");
  if ($result->num_rows > 0) {
    $registrants = "<p>Click on the registrant name to enroll:</p><ol>";
    while ($row = $result->fetch_assoc()) {
      if ($crs_seats > 0) {
        $registrants .= "<li><a href='enroll_processing.php?TID={$tid}&SID={$row['SID']}'>"."{$row['LastName']}, {$row['FirstName']}</a></li>";
      } else {
        $registrants .= "<li>{$row['LastName']}, {$row['FirstName']}</li>";
      }
    }
    $registrants .= '</ol>';
  }
  $result->free();
  $mysqli->close();

  // ########## Prepare content
  $html = BACKLINK_ADMIN."<h1>Course Enrollment</h1>";
  $html .= "<form method='post' name='signup_form' action='enroll_processing.php' onsubmit='return validateForm();'>\n";
  $html .= "<div  class='colA'><p>The course details are as follows:</p>";
  $html .= "<ul><li>Name: {$crs_name}</li><li>Date: {$crs_datef}</li><li>Time: {$crs_start} - {$crs_end}</li>";
  $html .= "<li>Location: {$crs_loc}</li><li>Instructor: {$crs_instr}</li><li>Available Seats: {$crs_seats}</li></ul></div>\n";
  $html .= "<div class='colB'><div id='errorList'></div>\n<div id='message'>{$successmsg}{$errormsg}</div></div>\n";
  $html .= "<div style='clear: both;'></div>";
  if ($crs_seats > 0) {
    $html .= "<div class='colA'><h2>Manual Enrollment</h2>\n";
    $html .= "<p>Please consider all fields as <strong>required</strong>, unless otherwise marked.</p>
        <fieldset><legend>Enrollment Form</legend>
        <div>
            <p><label for='fname'>First Name</label> <input type='text' name='fname' /></p>
            <p><label for='lname'>Last Name</label> <input type='text' name='lname' /></p>
            <p><label for='e_mail'>Email</label> <input type='text' name='e_mail' /></p>
            <p><label for='emp_id'>Employee ID</label> <input type='text' name='emp_id' /></p>
            <p>
                <label for='emp_status'>Employment Status</label>
                <select name='emp_status'>
                    <option value=''>Make a Selection</option>
                    <option value='Staff'>Staff</option>
                    <option value='Faculty'>Faculty</option>
                    <option value='MPP'>MPP</option>
                    <option value='Other'>Other</option>
                </select>
            </p>
            <p>
                <label for='division'>Division</label>
                <select name='division'>
                    <option value=''>Make a Selection</option>
                    <option value='Academic Affairs'>Academic Affairs</option>
                    <option value='Administration and Finance'>Administration and Finance</option>
                    <option value='Auxiliary'>Auxiliary</option>
                    <option value='Office of the President'>Office of the President</option>
                    <option value='Student Services'>Student Services</option>
                    <option value='University Relations &amp; Development'>University Relations &amp; Development</option>
                </select>
            </p>
            <p><label for='dept'>Department</label> <input type='text' name='dept' /></p>
            <p><label for='extension'>Phone / Extension</label> <input type='text' name='extension' /></p>
            <p>
                <input type='hidden' name='cdate' value='{$crs_date}'>
                <input type='hidden' name='cetime' value='{$crs_end}'>
                <input type='hidden' name='course' value='{$crs_name}'>
                <input type='hidden' name='cstime' value='{$crs_start}'>
                <input type='hidden' name='tid' value='{$tid}'>
                <input type='submit' name='submit' id='submit' value='Enroll'>
            </p>
            </fieldset></div>\n";
    $html .= "<div class='colB'><h2>Wait-listed Registrants</h2>{$registrants}</div>\n";
  } else {
    $html .= "<p>We cannot enroll someone until a seat is available.</p>\n";
  }
  $html .= "</form>\n";

  // ########## Write content
  $page['content'] = $html;
  $page['css'] = "<link href='".URL_COMMON."/css/form.css' rel='stylesheet' type='text/css' />\n"."<link href='".URL_COMMON."/css/details.css' rel='stylesheet' type='text/css' />\n"."<link href='".URL_COMMON."/css/signup.css' rel='stylesheet' type='text/css' />\n";
  $page['js'] = "<script src='".URL_COMMON."/js/signup.js' type='text/javascript'></script>\n";
  include_once (TEMPLATE);
}
