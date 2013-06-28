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

// Set-up files.
session_start();
require_once '../_config.php';
require_once '../_connect-mysqli.php';

// Stop and redirect, if any database resources are unavailable.
if ($mysqli->connect_error) {
    header("Location: ".URL_APP."/resource-unavailable.php?err=MySQLi%20Connect&admin=true");
} else {

    // Gather POST variables.
    $tid = $_POST['tid'];
    $m   = $_POST['message'];

    // Gather registrant list and send the emails.
    $tid    = $mysqli->real_escape_string($tid);
    $result = $mysqli->query("SELECT Email FROM Trainees WHERE TID='{$tid}'");
    if ($result->num_rows > 0) {
        $h = "MIME-Version: 1.0\r\nContent-type:text/html;charset=iso-8859-1\r\nFrom: training@csulb.edu";
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
