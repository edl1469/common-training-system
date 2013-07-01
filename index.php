<?php

/**
 * Fowarding agent to application owner pages.
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
include_once '_config.php';

$html = '';
if (ENVIRONMENT !== 'development') {
    header("Location: ".URL_GROUP);
} else {
    $html  = "<html><head><title>temp page</title></head><body><p>Temporary ".NAME_GROUP." page.</p><ul>";
    $html .= "<li><a href='list.php'>List</a></li><li><a href='calendar.php'>Calendar</a></li>";
    $html .= "<li><a href='review.php'>Reservation</a></li></ul></body></html>";
}

print $html;
