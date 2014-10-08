<?php

/**
 * Administrative course creation form.
 *
 * NOTE: This is a procedural page, preparing content as page processes.
 *
 * This form is the first of a two-step course creation process:
 *  1. Set course details.
 *  2. Set email details, using customized details from above step.
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

  // Pull GET variables.
  $errormsg = (isset($_GET['err']))? 'Error encountered during creation of course.': '';
  $successmsg = (isset($_GET['success']))? 'The course was successfully created.': '';

  // Prepare location list for form.
  $locationlist = "<option value=''>Select a Location</option>";
  $result = $mysqli->query("SELECT name FROM location ORDER BY name");
  while ($row = $result->fetch_assoc()) {
    $locationlist .= "<option value='{$row['name']}'>{$row['name']}</option>";
  }
  $result->free();

  // Prepare instructor list for form.
  $trainerlist = "<option value=''>Select a Trainer</option>";
  $result = $mysqli->query("SELECT * FROM Trainers ORDER BY Name");
  while ($row = $result->fetch_assoc()) {
    $trainerlist .= "<option value='{$row['Name']}'>{$row['Name']}</option>";
  }
  $result->free();
  $mysqli->close();

  // ########## Prepare content
  $html = BACKLINK_ADMIN."<h1>Course Creation</h1>";
  $html .= "<form method='post' name='create_form' action='create_processing.php'
                onsubmit='return validateForm();'>\n";
  $html .= "<p>Unless noted, all fields below are required.</p>\n
            <div class='colA'><fieldset>
            <p><label class='next_line' for='short_desc'>Short Description</label><br />
                <input name='short_desc' size='25' maxlength='50' /></p>
            <p><label class='next_line' for='description'>Description</label><br />
                <input name='description' size='60' maxlength='120' /></p>
            <p><label class='next_line' for='details'>Course Details</label><br />
                <textarea name='details' cols='60' rows='18'></textarea></p>
            <p><label for='cdate'>Course Date</label>
                <input name='cdate' size='10' maxlength='10' /> <span class='note'>(mm/dd/yyyy)</span></p>
            <p>
                <label for='cstime'>Course Start Time</label>
                <input name='cstime' size='5' maxlength='5' />
                <input type='radio' name='csmeridian' value='AM' checked='checked' /> AM
                <input type='radio' name='csmeridian' value='PM' /> PM
                <span class='note'>(h:mm)</span>
            </p>
            <p>
                <label for='cetime'>Course End Time</label>
                <input name='cetime' size='5' maxlength='5' />
                <input type='radio' name='cemeridian' value='AM' checked='checked' /> AM
                <input type='radio' name='cemeridian' value='PM' /> PM
                <span class='note'>(h:mm)</span>
            </p>
            <p><label for='private'>Is Private Event</label>
                <input type='checkbox' class='close' name='private' value='1' /> Yes</p>
            <p><label for='seats'>Available Seats</label>
                <input name='seats' size='5' maxlength='3' value='16' /></p>
            <p><label for='location'>Location</label>
                <select name='location'>{$locationlist}</select></p>
            <p><label for='trainer'>Trainer</label>
                <select name='trainer'>{$trainerlist}</select></p>
            <p><label for='course_email'>Contact Email</label>
                <input name='course_email' size='25' maxlength='50' /> ( <span style='border-bottom: green 2px dotted;' title='If shared group email is not desired.'>Optional</span> )</p>
            <p><input type='submit' name='submit' id='submit' value='Create Course'></p>
            </fieldset></div>";
  $html .= "<div class='colB'><div id='errorList'></div>\n";
  $html .= "<div id='message'>{$successmsg}{$errormsg}</div></div></form>\n";

  // ########## Write content
  $page['content'] = $html;
  $page['css'] = "<link href='".URL_COMMON."/css/form.css' rel='stylesheet' type='text/css' />\n"
      ."<link href='".URL_COMMON."/css/create.css' rel='stylesheet' type='text/css' />\n";
  $page['js'] = "<script src='".URL_COMMON."/js/create.js' type='text/javascript'></script>\n";
  include_once (TEMPLATE);
}
