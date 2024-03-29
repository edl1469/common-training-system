<?php

/**
 * Current course choices in list format.
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
require_once '_config.php';
require_once '_connect-mysqli.php';

// Stop and redirect, if any database resources are unavailable.
if ($mysqli->connect_error) {
  header("Location: resource-unavailable.php?err=MySQLi%20Connect");
} else {
  $append = (isset($_GET['admin']))? "?admin=true": '';
  $appendtoo = (isset($_GET['admin']))? "&admin=true": '';
  $back_link = "<div id='back'>";
  $back_link .= (isset($_GET['admin']))? "<a href='admin/index.php'>Return to Control Panel</a></div>": "<a href='".URL_APP."'>Return to ".NAME_GROUP."</a></div>";

  $courses = "<p>No future courses have been found. Please check back again soon.</p>";
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
      );
    }

    // Second, plan one or two column page structure. Modulo.
    /* $n = count( $list );  Right now we will keepit at one column, until they decide otherwise. */

    // Last, overwrite $courses string.
    while ($class = current($list)) {
      $courses .= '<h3>'.key($list).'</h3><ul>';
      foreach ($class as $event) {
        $courses .= "<li><a href='details.php?id={$event['id']}{$appendtoo}'>{$event['date']}</a></li>";
      }
      $courses .= "</ul>\n";
      next($list);
    }
  }
  $result->free();
  $mysqli->close();

  // ########## Prepare content
  $html = $back_link."<h1>".NAME_GROUP." Course Catalog</h1>";
  $html .= "<p>These are all of the courses currently scheduled for future dates."."You can also see these in a <a href='calendar.php{$append}'>calendar view</a>.</p>{$courses}";

  // ########## Write content
  $page['content'] = $html;
  include_once (TEMPLATE);
}
