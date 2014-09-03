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
session_start();
require_once '../../_config.php';
require_once '../../_connect-mysqli.php';

// Stop and redirect, if any database resources are unavailable.
if ($mysqli->connect_error) {
  header("Location: ".URL_APP."/resource-unavailable.php?err=MySQLi%20Connect&admin=true");
} else {

  // Pull GET variables.
  $tid = (isset($_GET['tid']))? $_GET['tid']: 0;

  if (!$tid) {
    header("Location: ".URL_APP."/admin/select.php");
  } else {

    // Pull and parse course information.
    $tid = $mysqli->real_escape_string($tid);
    $result = $mysqli->query("SELECT TDate,Description,TStartTime FROM Training WHERE TID='{$tid}'");
    $row = $result->fetch_assoc();
    $crs_date = $row['TDate'];
    $crs_datef = date("l, F j, Y", strtotime($row['TDate']));
    $crs_name = $row['Description'];
    $crs_start = date("g:i A", strtotime($row['TStartTime']));
    $result->free();

    // Gather registrant information.
    $registrant = '';
    $result = $mysqli->query("SELECT * FROM Trainees WHERE TID={$tid} AND Wait=0 ORDER BY LastName ASC");
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $registrant .= "<tr class='rowhead'><td class='dta'>{$row['FirstName']}&nbsp;{$row['LastName']}</td><td class='dta'>{$row['Dept']}</td>";
        $registrant .= "<td class='dta'>{$row['EmpID']}</td><td><p class='sigline'><span >&nbsp;</span></p></td></tr>";
      }
    }
    $result->free();
    $mysqli->close();

    // ########## Prepare content
    $html .= "<div class='no-print'>".BACKLINK_ADMIN."(This will not show on paper.)</div><h1>{$crs_name}</h1><p><strong>Date:</strong> {$crs_datef}, ";
    $html .= "<strong>Start Time:</strong> {$crs_start}</p><table width='100%' id='rostable'>";
    $html .= "<tr><th class='col1'>Name</th><th class='col3'>Dept</th><th class='col4'>Id</th><th class='sig'>Signature</th></tr>";
    $html .= "{$registrant}</table><p id='backlink'><a href='".URL_APP."/admin/view.php?tid={$tid}'>Back</a></p>";
  }

  // ########## Write content
  $page['content'] = $html;
  $page['css'] = "<link href='".URL_COMMON."/css/printable.css' rel='stylesheet' type='text/css' />\n
        <style type='text/css'>@media print { .no-print, .no-print * { display: none !important; } }</style>\n";
  include_once (TEMPLATE_BLANK);
}