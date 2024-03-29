<?php
/**
 * Processing the administrative course email reminder form.
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

    // Gather POST variables.
    $m = $_POST['message'];
    $tid = $mysqli->real_escape_string($_POST['tid']);

    // Prepare course reply-to email, appropriately.
    $result = $mysqli->query("SELECT course_email FROM Training Where TID='{$tid}'");
    $row = $result->fetch_assoc();
    $email_from = (!is_null($row['course_email']))? $row['course_email']: MAIL_GROUP;

    // Gather registrant list and send the emails.
    $result = $mysqli->query("SELECT Email FROM Trainees WHERE TID='{$tid}' AND Wait = 0");
    if ($result->num_rows > 0) {
        $h = "MIME-Version: 1.0\r\nContent-type:text/html;charset=iso-8859-1\r\nFrom: {$email_from}\r\n";
        $s = NAME_GROUP." Course Reminder";

        // @TODO: Create a queue to send email later.
        while ($row = $result->fetch_assoc()) {
            mail($row['Email'], $s, $m, $h);
        }
    }
    $result->free();
    $mysqli->close();

    header("Location: select.php?type=remind&success=1");
}
