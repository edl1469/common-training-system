<?php

/**
 * Form to enroll in course.
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
require_once '_config.php';
require_once '_connect-mysqli.php';

// Stop and redirect, if any database resources are unavailable.
if ($mysqli->connect_error) {
  header("Location: resource-unavailable.php?err=MySQLi%20Connect");
} else {
  $back_link = "<div id='back'><a href='".URL_APP."'>Return to ".NAME_GROUP."</a></div>";
  $errormsg = '';
  $is_problem = false;

  // Collect GET
  $id = $_GET['TID'];
  $waitlisted = (isset($_GET['wait']))? 1: 0;
  if (isset($_GET['dup'])) {
    $errormsg = "<p>You are ALREADY registered for this course. Please check your past email for confirmation.</p>";
    $is_problem = true;
  } elseif (isset($_GET['full'])) {
    $errormsg = "<p>You are NOT registered for the course as it is <strong>full</strong>. "
          ."Please <a href='calendar.php'>check the training calendar</a> again for another course "
          ."with availability or join a Wait-list.</p>";
    $is_problem = true;
  }
  elseif (isset($_GET['tip'])) {
    $errormsg = "<p style='color:red';>Invalid Credentials entered.</p>";
    $is_problem = true;
  }

  // Pull and parse course information.
  $id = $mysqli->real_escape_string($id);
  $result = $mysqli->query("SELECT * FROM Training Where TID='{$id}' ");
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
  $mysqli->close();

  // ########## Prepare content
  $html = $back_link."<h1>Course Registration</h1>\n";
  $html .= "<form method='post' name='signup_form' id='signup' action='signup_processing.php' onsubmit='return validateForm();'>\n";
  $html .= "<div  class='colA'><p>The course details are as follows:</p>";
  $html .= "<ul><li>Name: {$crs_name}</li><li>Date: {$crs_datef}</li><li>Time: {$crs_start} - {$crs_end}</li>";
  $html .= "<li>Location: {$crs_loc}</li><li>Instructor: {$crs_instr}</li><li>Available Seats: {$crs_seats}</li></ul></div>\n";
  $html .= "<div class='colB'><div id='errorList'></div>\n<div id='message'>{$errormsg}</div></div>\n";
  $html .= "<div style='clear: both;'></div>";

  // Prepare form
  if (!$is_problem) {
    $html .= "<p>Please consider all fields as <strong>required</strong>, unless otherwise marked.</p>
        <div class='colA'><fieldset><legend>Enrollment Form</legend>
            <p><label for='fname'>First Name</label> <input type='text' name='fname' /></p>
            <p><label for='lname'>Last Name</label> <input type='text' name='lname' /></p>
            <p><label for='e_mail'>Email</label> <input type='text' name='e_mail' id='e_mail' /></p>
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
                    <option value='Athletics'>Athletics</option>
                    <option value='Auxiliary'>Auxiliary</option>
                    <option value='Office of the President'>Office of the President</option>
                    <option value='Student Affairs'>Student Affairs</option>
                    <option value='University Relations &amp; Development'>University Relations &amp; Development</option>
                </select>
            </p>
            <p><label for='dept'>Department</label> <input type='text' name='dept' /></p>
            <p><label for='extension'>Phone / Extension</label> <input type='text' name='extension' /></p>
            <p><input type='checkbox' id='notify_super' name='notify_super><label for='notify_super'>Check box if you wish to have a notifcation sent to your supervisor.</label></p>
            <p id='spremail'><label for ='super_email'>Supervisor Email:</label><input type='text' name='super_email'></p>
            <p>
                <input type='hidden' name='cdate' value='{$crs_date}'>
                <input type='hidden' name='cetime' value='{$crs_end}'>
                <input type='hidden' name='course' value='{$crs_name}'>
                <input type='hidden' name='cstime' value='{$crs_start}'>
                <input type='hidden' name='tid' value='{$id}'>
                <input type='hidden' name='wait' value='{$waitlisted}'>
                <input type='submit' name='submit' id='submit' value='Sign Up'>
            </p>
        </fieldset></div>\n";

    if ($waitlisted == "1") {
      $html .= "<div class='graybox colB'>";
      $html .= "<h3>ATTENTION:</h3><p>You are joining the Wait-list for this course. You are not actually registering now.</p>"
            ."<p>You will be contacted before the course start, if you can be enrolled.</p>";
      $html .= "</div>\n";
    }
  }
  $html .= "</form>\n";
  $html .= "<script type='text/javascript'>
  $('#notify_super').click(function(){
     $('#spremail').toggle();
  });
  </script>

  ";
  // ########## Write content
  $page['content'] = $html;
  $page['css'] = "<link href='".URL_COMMON."/css/form.css' rel='stylesheet' type='text/css' />\n"
        ."<link href='".URL_COMMON."/css/signup.css' rel='stylesheet' type='text/css' />\n";
  $page['js'] = "<script src='".URL_COMMON."/js/signup.js' type='text/javascript'></script>\n<script src='".URL_COMMON."/js/jquery.min.js' type='text/javascript'></script>\n";
  include_once (TEMPLATE);
}
