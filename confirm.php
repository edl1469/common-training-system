<?php

/**
 * Sign-up confirmation.
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
include_once '_config.php';

$back_link  = "<div id='back'><a href='".URL_APP."'>Return to ".NAME_GROUP."</a></div>";

// Start page code
$html  = file_get_contents(CHUNK1);
$html .= "<style type='text/css'> #back { float: right; } </style>";
$html .= file_get_contents(CHUNK2);
$html .= $back_link."<h1>Registration Success</h1>";
$html .= "<p>Your registration information has been submitted. "
        ."Please check your email for confirmation or further instructions.</p>";
$html .= file_get_contents(CHUNK3);
$html .= file_get_contents(CHUNK4);

print $html;
