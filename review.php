<?php

/**
 * Form to facilitate the reporting of existing user registrations.
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
require_once '_config.php';
require_once '_connect-mysqli.php';

// Stop and redirect, if any database resources are unavailable.
if ($mysqli->connect_error) {
    header("Location: resource-unavailable.php?err=MySQLi%20Connect");
} else {
    unset( $_SESSION['emp'] );
    unset( $_SESSION['user'] );
    $back_link  = "<div id='back'><a href='".URL_APP."'>Return to ".NAME_GROUP."</a></div>";
    $message    = '<em>If you are unsure, submit the form and we will tell you if any exist.</em>';

    // If form submission, find their records.
    if (isset($_POST['submit'])) {
        $today     = date("Y-m-d");
        $reg_email = $mysqli->real_escape_string($_POST['email']);
        $reg_empid = $mysqli->real_escape_string($_POST['empl']);
        $query     = "SELECT TDate FROM Trainees WHERE EmpID='{$reg_empid}' AND Email='{$reg_email}' "
                    ."AND TDate>='{$today}' ORDER BY TDate ASC";
        $result    = $mysqli->query($query);
        if ($result->num_rows > 0) {

            $_SESSION['emp']  = $reg_empid;
            $_SESSION['user'] = $reg_email;
            header("Location: cancel.php");
        }
        $result->free();
        $message = "<strong>No Records found. Please verify your information and try again.</strong>";
    }

    // Start page code
    $html  = file_get_contents(CHUNK1);
    $html .= "<link href='".URL_COMMON."/css/form.css' rel='stylesheet' type='text/css' />";
    $html .= file_get_contents(CHUNK2);
    $html .= $back_link."<h1>Review Your Registrations</h1>";

    $html .= "<form action='review.php' method='post'>"
            ."<p>Enter your information to view your upcoming ".NAME_GROUP." registrations.</p><fieldset>"
            ."<p><label for='email'>Email </label> <input type='text' name='email' id='email' /></p>"
            ."<p><label for='empl'>Employee ID</label> <input type='text' name='empl' id='empl' /></p>"
            ."<p><input type='submit' value='Enter' id='sub' name='submit' /></p></fieldset></form> <p>{$message}</p>";
    $html .= file_get_contents(CHUNK3);
    $html .= file_get_contents(CHUNK4);

    $mysqli->close();
    print $html;
}
