<?php

/**
 * Dashboard for training administration panel.
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
require_once '../_connect-mysql.php';

// Stop and redirect, if any database resources are unavailable.
if ($mysqli->connect_error) {
    header("Location: ".URL_APP."/resource-unavailable.php?err=MySQLi%20Connect&admin=true");
} else {

    // Start page code
    $html  = file_get_contents(CHUNK1);
    $html .= "<link href='https://daf.csulb.edu/css/main_page.css' rel='stylesheet' type='text/css' />"
            ."<style> .colA, .colB { width: 45%; } .colB { margin-left: 2em; } </style>";
    $html .= file_get_contents(CHUNK2);
    $html .= BACKLINK_ADMIN."<h1>".NAME_GROUP." Control Panel</h1>";

    $html .= "<div class='colA'>
      	<h2>Enrollment Management</h2>
        <ul>
            <li><a href='select.php?type=view'>View Roster</a></li>
            <li><a href='select.php?type=enroll'>Enroll Attendees</a></li>
        </ul>
        <h2>Communication Management</h2>
        <ul>
            <li><a href='select.php?type=remind'>Send Reminder</a></li>
        </ul>
      </div>
      <div class='colB'>
        <h2>Course Management</h2>
        <ul>
            <li><a href='create.php'>Create Course</a></li>
            <li><a href='select.php?type=edit'>Edit Course</a></li>
            <li><a href='../list.php?admin=true'>List View</a></li>
            <li><a href='../calendar.php?admin=true'>Calendar View</a></li>
        </ul>
        <!--h2>Report Management</h2>
    	<ul>
    	   <li><a href='report/training_report.php'>Training Report</a></li>
    	</ul-->
      </div>";
    $html .= file_get_contents(CHUNK3);
    $html .= file_get_contents(CHUNK4);

    print $html;
}
