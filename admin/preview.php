<?php

/**
 * A preview of the upcoming courses and their enrollment.
 *
 * NOTE: This is a procedural page, preparing content as page processes.
 *
 * PHP version 5
 *
 * @category   ITSWebApplication
 * @package    ESTrainingApp
 * @author     Steven Orr <Steven.Orr@csulb.edu>
 */
session_start();
require_once '../_config.php';
require_once '../_connect-mysqli.php';

// Stop and redirect, if any database resources are unavailable.
if ($mysqli->connect_error) {
    header("Location: ".URL_APP."/resource-unavailable.php?err=MySQLi%20Connect&admin=true");
} else {
  $courses = "<p>No future courses have been found.</p>";
  $date = getdate();
  $list = array();

  // Prep Month and Day by inserting zero, if necessary.
  $m = ($date["mon"] < 10)? "0".$date["mon"]: $date["mon"];
  $d = ($date["mday"] < 10)? "0".$date["mday"]: $date["mday"];
  $td = $date["year"]."-{$m}-{$d}";

  // Execute SQL and parse results.
  $result = $mysqli->query("SELECT * FROM Training WHERE TDate>='{$td}' AND IsVisible=1 ORDER BY Short_Description, TDate");
  if ($result->num_rows > 0) {
    $courses = '';

    // First, collect all courses into an array by Short_Description.
    while ($row = $result->fetch_assoc()) {
      $bit = explode('-', $row['TDate']);
      $list[$row['Short_Description']][] = array(
        'id' => $row['TID'],
        'date' => date("F j, Y - l", mktime(0, 0, 0, $bit[1], $bit[2], $bit[0])),
        'seats' => $row['TSeats'],
        'waiters' => $row['TWait'],
      );
    }

    // Last, overwrite $courses string.
    while ($class = current($list)) {
      $courses .= '<h3>'.key($list).'</h3><ul>';
      foreach ($class as $event) {
        $seat_text = 'No seats open';
        if ($event['seats'] > 1) {
            $seat_text = "{$event['seats']} seats open";
        } elseif ($event['seats'] == 1) {
            $seat_text = "1 seat open";
        }
        $wait_text = '';
        if ($event['waiters'] > 1) {
            $wait_text .= " and {$event['waiters']} people waiting";
        } elseif ($event['waiters'] == 1) {
            $wait_text .= " and 1 person waiting";
        }
        $courses .= "<li><a href='view.php?tid={$event['id']}'>{$event['date']}</a> : {$seat_text} {$wait_text}</li>";
      }
      $courses .= "</ul>\n";
      next($list);
    }
  }
  $result->free();
  $mysqli->close();

  // ########## Prepare content
  $html = BACKLINK_ADMIN."<h1>".NAME_GROUP." Course Catalog</h1>";
  $html .= "<h2>Preview the Enrollment</h2>";
  $html .= "<p>These are the enrollment numbers for the courses currently scheduled for future dates.</p>";
  $html .= "{$courses}";

  // ########## Write content
  $page['content'] = $html;
  include_once (TEMPLATE);
}
