<?php
//// YOU WERE WORKING ON MAKING A TABLE ///////
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
require_once '../_config.php';
require_once '../_connect-mysqli.php';

// Stop and redirect, if any database resources are unavailable.
if ($mysqli->connect_error) {
  header("Location: resource-unavailable.php?err=MySQLi%20Connect");
} else {
  // pull program id from get variable to include in hidden input field
  $prg = (isset($_GET['name']))? strtoupper($_GET['name']): 'Error creating Program. Try Again.';
  $result = $mysqli->query("SELECT pid from program WHERE program_name = '{$prg}'");
    $row = $result->fetch_assoc();
    $pid = $row['pid'];
    $result->free();

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
  //counter for rows
  $i = 1;
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

    $courses = '<form name="create_program" method="post" action="create_program.php"><table><tr><th>Course Title</th><th>Course Date</th><th>Add to Program</th><th>Sort Order</th></tr>';
    while ($class = current($list)) {
      foreach ($class as $event) {
        $courses .= "<tr><td><h3>".key($list)."</h3></td><td><a href='../details.php?id={$event['id']}{$appendtoo}' class='modo'>{$event['date']}</a></td><td style='text-align:center;'><input type='checkbox' class='chkbx' name='course_group[]' value='{$event['id']}'></td><td style='text-align:center;'><input type='text' name='sortorder[]' class='sort' maxlength='3' size='3'></td></tr>";
      }

      next($list);
      $i++;
    }
$courses .= "</table><input type='submit' value='Create Program' name='submit'><input type='hidden' name='pid' value='{$pid}'></form\n";
  }

  $result->free();

    $mysqli->close();

  // ########## Prepare content

  $html = $back_link."<h1>".NAME_GROUP." Program Creation</h1>";
  $html .="<h2>Program Name: {$prg}</h2>";
  $html .= "<p>STEP 2. These are all of the courses currently scheduled for future dates.</p><p>Create your program from the following available courses.</p>{$courses}";
  $html .="<script type='text/javascript'>
    $('.chkbx').click(function(){
        $(this).closest('td').find('.sort').attr('required', 'true');
    });
  </script>";

  // ########## Write content
  $page['content'] = $html;
  $page['js'] = "<script src='".URL_COMMON."/js/jquery.min.js' type='text/javascript'></script>\n";

  include_once (TEMPLATE);

}
