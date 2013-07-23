<?php

/**
 * Shows course details.
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
require_once '_config.php';
require_once '_connect-mysqli.php';

// Stop and redirect, if any database resources are unavailable.
if ($mysqli->connect_error) {
    header("Location: resource-unavailable.php?err=MySQLi%20Connect");
} else {
    $back_link  = "<div id='back'>";
    $back_link .= (isset($_GET['admin']))
            ? "<a href='admin/index.php'>Return to Control Panel</a></div>"
            : "<a href='".URL_APP."'>Return to ".NAME_GROUP."</a></div>";

    // Collect GET
    $tid = $_GET['id'];

    // Pull and parse course information.
    $tid       = $mysqli->real_escape_string($tid);
    $result    = $mysqli->query("SELECT * FROM Training WHERE TID='{$tid}'");
    $row       = $result->fetch_assoc();
    $crs_date  = $row['TDate'];
    $crs_datef = date("l, F j, Y", strtotime($row['TDate']));
    $crs_detl  = $row['Details'];
    $crs_end   = date("g:i A", strtotime($row['TEndTime']));
    $crs_instr = $row['Trainer'];
    $crs_loc   = $row['Location'];
    $crs_name  = $row['Description'];
    $crs_priv  = $row['IsPrivate'];
    $crs_seats = $row['TSeats'];
    $crs_start = date("g:i A", strtotime($row['TStartTime']));
    $crs_waits = $row['TWait'];
    $result->free();

    // Get today ISO date.
    $date  = getdate();
    $m     = ($date["mon"] < 10) ? "0".$date["mon"]  : $date["mon"];
    $d     = ($date["mday"] < 10)? "0".$date["mday"] : $date["mday"];
    $today = $date["year"]."-{$m}-{$d}";

    // Make display decisions.
    $available_seats = (($crs_seats - $crs_waits) > 0)? 1: 0;
    $display         = ($today <= $crs_date)? 1: 0;
    $lnk_class = "signup";
    $lnk_text  = "Sign Up";
    $lnk_url   = "signup.php?TID={$tid}";

    // Start page code
    $html  = file_get_contents(CHUNK1);
    $html .= "<link href='".URL_COMMON."/css/details.css' rel='stylesheet' type='text/css' />";
    $html .= file_get_contents(CHUNK2);
    $html .= $back_link."<h1>Course Information</h1>";

    $html .= "<p>The course details are as follows:</p>";
    $html .= "<ul><li>Name: {$crs_name}</li><li>Date: {$crs_datef}</li><li>Time: {$crs_start} - {$crs_end}</li>";
    $html .= "<li>Location: {$crs_loc}</li><li>Instructor: {$crs_instr}</li></ul>";
    $html .= "<h3>Details</h3><div id='desc'>{$crs_detl}</div>";
    if ($display) {
        if ($crs_priv == 1) {
            $html .= "<p id='action'>Registration closed, as is private training.</p>";
        } else {
             if (!$available_seats) {
                $lnk_class = "joinlist";
                $lnk_url  .= '&wait=1';
                $lnk_text  = 'Join Wait-list';
            }
            $html .= "<p id='action'><a class='{$lnk_class}' href='{$lnk_url}'>{$lnk_text}</a></p>";
        }
    }  else {
        $html .= "<p id='action'>Registration closed, as date is past.</p>";
    }
    $html .= file_get_contents(CHUNK3);
    $html .= file_get_contents(CHUNK4);

    $mysqli->close();
    print $html;
}
