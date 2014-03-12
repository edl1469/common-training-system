<?php

/**
 * Administrative course edit form.
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

  // Pull and parse course information.
  $tid = $mysqli->real_escape_string($_POST['course']);
  $result = $mysqli->query("SELECT * FROM Training WHERE TID='{$tid}'");
  $row = $result->fetch_assoc();
  $crs_date = $row['TDate'];
  $crs_detl = $row['Details'];
  $crs_email = $row['Email_Confirm'];
  $crs_end = date("g:i A", strtotime($row['TEndTime']));
  $crs_instr = $row['Trainer'];
  $crs_loc = $row['Location'];
  $crs_name = $row['Description'];
  $crs_priv = $row['IsPrivate'];
  $crs_seats = $row['TSeats'];
  $crs_short = $row['Short_Description'];
  $crs_start = date("g:i A", strtotime($row['TStartTime']));
  $crs_visbl = $row['IsVisible'];
  $result->free();

  // Prepare form variables.
  $pretty_end = explode(' ', $crs_end);
  $pretty_start = explode(' ', $crs_start);

  // Set one-use form variables.
  $end_merid_am = ($pretty_end[1] == 'AM')? " checked='checked'": '';
  $end_merid_pm = ($pretty_end[1] == 'PM')? " checked='checked'": '';
  $start_merid_am = ($pretty_start[1] == 'AM')? " checked='checked'": '';
  $start_merid_pm = ($pretty_start[1] == 'PM')? " checked='checked'": '';
  $priv = ($crs_priv)? " checked='checked'": '';
  $visible = ($crs_visbl)? " checked='checked'": '';

  // Prepare location list for form.
  $locationlist = "<option value=''>Select a Location</option>";
  $result = $mysqli->query("SELECT name FROM location ORDER BY name");
  while ($row = $result->fetch_assoc()) {
    $mark = '';
    if ($crs_loc == $row['name']) {
      $mark = " selected='selected'";
    }
    $locationlist .= "<option value='{$row['name']}'{$mark}>{$row['name']}</option>";
  }
  $result->free();

  // Prepare instructor list for form.
  $trainerlist = "<option value=''>Select a Trainer</option>";
  $result = $mysqli->query("SELECT * FROM Trainers ORDER BY Name");
  while ($row = $result->fetch_assoc()) {
    $mark = '';
    if ($crs_instr == $row['Name']) {
      $mark = " selected='selected'";
    }
    $trainerlist .= "<option value='{$row['Name']}'{$mark}>{$row['Name']}</option>";
  }
  $result->free();
  $mysqli->close();

  // ########## Prepare content
  $html = BACKLINK_ADMIN."<h1>Course Edit</h1>";
  $html .= "<form method='post' name='edit_form' action='edit_processing.php' onsubmit='return validateForm();'>\n";
  $html .= "<p>Unless noted, all fields below are required. <strong>IMPORTANT:</strong> If changing course <em>Location</em>, <em>Date</em>, or <em>Times</em>, you must manually update the email content.</p>\n
            <div class='colA'><fieldset>
            <p><label class='next_line' for='short_desc'>Short Description</label><br />
                <input name='short_desc' size='25' maxlength='50' value='{$crs_short}' /></p>
            <p><label class='next_line' for='description'>Description</label><br />
                <input name='description' size='60' maxlength='120' value='{$crs_name}' /></p>
            <p><label class='next_line' for='details'>Course Details</label><br />
                <textarea name='details' cols='60' rows='18'>".html_entity_decode($crs_detl)."</textarea></p>
            <p><label for='cdate'>Course Date</label>
                <input name='cdate' size='10' maxlength='10' value='{$crs_date}' /> <span class='note'>(mm/dd/yyyy)</span></p>
            <p>
                <label for='cstime'>Course Start Time</label>
                <input name='cstime' size='5' maxlength='5' value='{$pretty_start[0]}' />
                <input type='radio' name='csmeridian' value='AM'{$start_merid_am} /> AM
                <input type='radio' name='csmeridian' value='PM'{$start_merid_pm} /> PM
                <span class='note'>(h:mm)</span>
            </p>
            <p>
                <label for='cetime'>Course End Time</label>
                <input name='cetime' size='5' maxlength='5' value='{$pretty_end[0]}' />
                <input type='radio' name='cemeridian' value='AM'{$end_merid_am} /> AM
                <input type='radio' name='cemeridian' value='PM'{$end_merid_pm} /> PM
                <span class='note'>(h:mm)</span>
            </p>
            <p><label for='private'>Is Private Event</label>
                <input type='checkbox' class='close' name='private' value='1'{$priv} /> Yes</p>
            <p><label for='visible'>Is Visible</label>
                <input type='checkbox' class='close' name='visible' value='1'{$visible} /> Yes</p>
            <p><label for='seats'>Available Seats</label>
                <input name='seats' size='5' maxlength='3' value='{$crs_seats}' /></p>
            <p><label for='location'>Location</label>
                <select name='location'>{$locationlist}</select></p>
            <p><label for='trainer'>Trainer</label>
                <select name='trainer'>{$trainerlist}</select></p>
            <p><label class='next_line' for='confirm'>Confirmation Email Text</label>
                <textarea name='confirm' cols='60' rows='18'>{$crs_email}</textarea></p>
            <p><input type='submit' name='submit' id='submit' value='Update Course'>
                <input type='hidden' name='tid' value='{$tid}'></p>
            </fieldset></div>";
  $html .= "<div class='colB'><div id='errorList'></div>\n</div></form>\n";

  // ########## Write content
  $page['content'] = $html;
  $page['css'] = "<link href='".URL_COMMON."/css/form.css' rel='stylesheet' type='text/css' />\n"."<link href='".URL_COMMON."/css/create.css' rel='stylesheet' type='text/css' />\n";
  $page['js'] = "<script src='".URL_COMMON."/js/edit.js' type='text/javascript'></script>\n";
  include_once (TEMPLATE);
}
