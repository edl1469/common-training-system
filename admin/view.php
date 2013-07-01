<?php

/**
 * Administrative view of course enrollment; allows attendance recording.
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

    // Pull POST / GET variables.
    $tid = (isset($_POST['course'])) ? $_POST['course'] : $_GET['tid'];

    // Pull and parse course information.
    $tid       = $mysqli->real_escape_string($tid);
    $result    = $mysqli->query("SELECT TDate,TStartTime,TSeats,Location,Description FROM Training WHERE TID='{$tid}'");
    $row       = $result->fetch_assoc();
    $crs_date  = $row['TDate'];
    $crs_datef = date("l, F j, Y", strtotime($row['TDate']));
    $crs_loc   = $row['Location'];
    $crs_name  = $row['Description'];
    $crs_seats = $row['TSeats'];
    $crs_start = date("g:i", strtotime($row['TStartTime']));
    $result->free();


    // Set date to control attendance registration.
    $date = getdate();
    $m      = ($date["mon"] < 10) ? "0" . $date["mon"]  : $date["mon"];
    $d      = ($date["mday"] < 10) ? "0" . $date["mday"] : $date["mday"];
    $today  = $date["year"] . "-{$m}-{$d}";

    // Set variables.
    $pastcourse  = ($today >= $crs_date) ? 1: 0;
    $registered  = '';
    $registrants = "<p>No one has enrolled in this course yet.</p>";
    $waitlisted  = '';

    // Pull and parse registrant information.
    $result = $mysqli->query("SELECT SID,FirstName,LastName,Reg_Date,Attend,Wait FROM Trainees WHERE TID='{$tid}'");
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            // Separate enrolled from wait-listed registrants.
            if ($row['Wait']) {
                $waitlisted .= "<li>{$row['LastName']}, {$row['FirstName']}</li>";
            } else {
                $attended = $row['Attend'];
                $recorded = '';
                $url      = "view_processing.php?what={$tid}&amp;who={$row['SID']}&amp;went=";
                if ($pastcourse) {

                    // If marked, give opposite URL to allow changes.
                    if ($attended) {
                        $recorded = "<a href='{$url}0'><img src='".URL_COMMON."/img/marked-present.png' "
                                ."title='Present' alt='Present' /></a>";
                    } else {
                        $recorded = "<a href='{$url}1'><img src='".URL_COMMON."/img/marked-absent.png' "
                                ."title='Absent' alt='Absent' /></a>";
                    }
                }
                $registered .= "<li>{$row['LastName']}, {$row['FirstName']} <span>{$recorded}</span></li>";
            }
        }

        // Prepare formatted registrant enrollment lists.
        $registrants  = "<div class='colA'><h2>Enrolled Registrants ";
        $registrants .= "<span class='h2link'><a href='report/printable.php?tid={$tid}'>Printable</a> Attend?</span></h2>";
        $registrants .= "<ol id='reg'>{$registered}</ol></div>";
        $registrants .= "<div class='colB'><h2>Wait-listed Registrants ";
        $registrants .= "<span class='h2link'><a href='enroll.php?TID={$tid}'>Enroll</a></span></h2>";
        $registrants .= "<ol>{$waitlisted}</ol></div>";
    }
    $result->free();

    // Start page code
    $html  = file_get_contents(CHUNK1);
    $html .= "<link href='https://daf.csulb.edu/css/main_page.css' rel='stylesheet' type='text/css' />"
            ."<link href='".URL_COMMON."/css/view.css' rel='stylesheet' type='text/css' />";
    $html .= file_get_contents(CHUNK2);
    $html .= BACKLINK_ADMIN."<h1>View Course</h1>";

    $html .= "<ul><li>Name: {$crs_name}</li><li>Date: {$crs_datef}</li><li>Time: {$crs_start}</li>"
            ."<li>Available Seats: {$crs_seats}</li></ul>\n{$registrants}";
    $html .= file_get_contents(CHUNK3);
    $html .= file_get_contents(CHUNK4);

    $mysqli->close();
    print $html;
}
