<?php

/**
 * Administrative view of printable course roster.
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
require_once '../../_config.php';
require_once '../../_connect-mysqli.php';

// Stop and redirect, if any database resources are unavailable.
if ($mysqli->connect_error) {
    header("Location: ".URL_APP."/resource-unavailable.php?err=MySQLi%20Connect&admin=true");
} else {

    // Pull GET variables.
    $tid = (isset($_GET['tid'])) ? $_GET['tid'] : 0;

    if (!$tid) {
        header("Location: ".URL_APP."/admin/select.php");
    } else {

        // Pull and parse course information.
        $tid       = $mysqli->real_escape_string($tid);
        $result    = $mysqli->query("SELECT TDate,Description,TStartTime FROM Training WHERE TID='{$tid}'");
        $row       = $result->fetch_assoc();
        $crs_date  = $row['TDate'];
        $crs_datef = date("l, F j, Y", strtotime($row['TDate']));
        $crs_name = $row['Description'];
        $crs_start = date("g:i A", strtotime($row['TStartTime']));
        $result->free();


        // Gather registrant information.
        $registrant = '';
        $result     = $mysqli->query("SELECT * FROM Trainees WHERE TID={$tid} AND Wait=0 ORDER BY LastName ASC");
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $registrant .= "<div class='columns'>{$row['FirstName']}</div><div class='columns'>{$row['LastName']}</div><div class='columns'>{$row['Dept']}</div>";
                $registrant .= "<div class='columns'>{$row['EmpID']}</div><div class='sigline'></div><div class='clearit'></div>";
            }
        }
        $result->free();

        // Start page code
        //$html  = file_get_contents(CHUNK1);
        $html  = "<html><head>";
        $html .= "<link href='".URL_COMMON."/css/printable.css' rel='stylesheet' type='text/css' />";
        $html .="</head>";
        $html .= file_get_contents(CHUNK2);
        $html .= "<h2>{$crs_name}</h2><p><strong>Date:</strong> {$crs_datef}, ";
        $html .= "<strong>Start Time:</strong> {$crs_start}</p>";
        $html .= "<div class='columns_container'><div class='headings_row'><div class='columns'>First</div><div class='columns'>Last</div><div class='columns'>Dept</div><div class='columns'>Id</div><div class='sig'>Signature</div></div><div class='clearit'></div>";
        $html .= "{$registrant}</div><p id='backlink'><a href='".URL_APP."/admin/view.php?tid={$tid}'>Back</a></p>";
        $html .= file_get_contents(CHUNK3);
        $html .= file_get_contents(CHUNK4);
    }

    $mysqli->close();
    print $html;
}
