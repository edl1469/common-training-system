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

// Set-up files.
session_start();
require_once '../_config.php';
require_once '../_connect-mysqli.php';

// Stop and redirect, if any database resources are unavailable.
if ($mysqli->connect_error) {
    header("Location: ".URL_APP."/resource-unavailable.php?err=MySQLi%20Connect&admin=true");
} else {

    // Pull and parse course information.
    $tid       = $mysqli->real_escape_string($_POST['course']);
    $result    = $mysqli->query("SELECT * FROM Training WHERE TID='{$tid}'");
    $row       = $result->fetch_assoc();
    $crs_date  = $row['TDate'];
    $crs_detl  = $row['Details'];
    $crs_email = $row['Email_Confirm'];
    $crs_end   = $row['TEndTime'];
    $crs_instr = $row['Trainer'];
    $crs_loc   = $row['Location'];
    $crs_name  = $row['Description'];
    $crs_priv  = $row['IsPrivate'];
    $crs_seats = $row['TSeats'];
    $crs_short = $row['Short_Description'];
    $crs_start = $row['TStartTime'];
    $result->free();

    // Prepare form variables.
    $priv   = ($crs_priv) ? " checked='checked'" : '';

    // Prepare location list for form.
    $locationlist = "<option value=''>Select a Location</option>";
    $result       = $mysqli->query("SELECT name FROM location ORDER BY name");
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
    $result      = $mysqli->query("SELECT * FROM Trainers ORDER BY Name");
    while ($row = $result->fetch_assoc()) {
        $mark = '';
        if ($crs_instr == $row['Name']) {
            $mark = " selected='selected'";
        }
        $trainerlist .= "<option value='{$row['Name']}'{$mark}>{$row['Name']}</option>";
    }
    $result->free();

    // Start page code
    $html  = file_get_contents(CHUNK1);
    $html .= "<link href='".URL_COMMON."/css/form.css' rel='stylesheet' type='text/css' />\n";
    $html .= "<link href='".URL_COMMON."/css/create.css' rel='stylesheet' type='text/css' />\n";
    $html .= "<script src='".URL_COMMON."/js/edit.js' type='text/javascript'></script>\n";
    $html .= file_get_contents(CHUNK2);
    $html .= BACKLINK_ADMIN."<h1>Course Edit</h1>";

    $html .= "<form method='post' name='edit_form' action='edit_processing.php' onsubmit='return validateForm();'>\n";
    $html .= "<p>Unless noted, all fields below are required. <strong>IMPORTANT:</strong> If changing course <em>Location</em>, you must manually update the email content.</p>\n
            <div class='colA'><fieldset>
            <p><label class='next_line' for='short_desc'>Short Description</label><br />
                <input name='short_desc' size='25' maxlength='50' value='{$crs_short}' /></p>
            <p><label class='next_line' for='description'>Description</label><br />
                <input name='description' size='60' maxlength='120' value='{$crs_name}' /></p>
            <p><label class='next_line' for='details'>Course Details</label><br />
                <textarea name='details' cols='60' rows='18'>".html_entity_decode($crs_detl)."</textarea></p>
            <p><label for='cdate'>Course Date</label>
                <input name='cdate' size='10' maxlength='10' value='{$crs_date}' /> <span class='note'>(mm/dd/yyyy)</span></p>
            <p><label for='cstime'>Course Start Time</label>
                <input type='text' name='cstime' size='10' maxlength='8' value='{$crs_start}' /> <span class='note'>(HH:mm:ss)</span></p>
            <p><label for='cetime'>Course End Time</label>
                <input type='text' name='cetime' size='10' maxlength='8' value='{$crs_end}' /> <span class='note'>(HH:mm:ss)</span></p>
            <p><label for='private'>Is Private Event</label>
                <input type='checkbox' class='close' name='private' value='1'{$priv} /> Yes</p>
            <p><label for='seats'>Available Seats</label>
                <input name='seats' size='5' maxlength='2' value='{$crs_seats}' /></p>
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
    $html .= file_get_contents(CHUNK3);
    $html .= file_get_contents(CHUNK4);

    $mysqli->close();
    print $html;
}
