<?php

/**
 * Administrative date-oriented data collection tool.
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
require_once '../_config.php';
require_once '../_connect-mysqli.php';

// Stop and redirect, if any database resources are unavailable.
if ($mysqli->connect_error) {
    header("Location: ".URL_APP."/resource-unavailable.php?err=MySQLi%20Connect&admin=true");
} else {
    $msg = (isset($_GET['error']))? "<p style='color: red;'>The report has no results.</p>": '';

    // ########## Prepare content
    $html = "<h1>".NAME_GROUP." Training Report</h1>";
    $html .= "{$msg}<form name='report' method='post' action='report_processing.php' id='treport'>
          <h3>Please select a date range for your report</h3>
          <fieldset>
            <legend>Date Range</legend>
            <label for='startdate'>Start Date</label><input type='text' id='startdate' name='startdate' />
            <label for='enddate'>End Date</label><input type='text' name='enddate' id='enddate' />
            <input type='submit' id='submit' name='submit' value='Submit' />
          </fieldset>
        </form>
        <script type='text/javascript'>
            $(function() { $( '#startdate' ).datepicker(); });
            $(function() { $( '#enddate' ).datepicker(); });
        </script>";

    // ########## Write content
    $page['content'] = $html;
    $page['css'] = "
    <link rel='stylesheet' href='".URL_APP."/common/jquery/css/base/jquery.ui.all.css' />
        <link rel='stylesheet' href='".URL_APP."/common/jquery/css/ui-darkness/jquery.ui.datepicker.css' />
        <style type='text/css'>
            #startdate, #enddate { margin-right: 20px; } label { margin-right: 5px; } #treport { width: 700px; }
        </style>";
    $page['js'] = "<script src='https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js' type='text/javascript'></script>
        <script src='https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js'  type='text/javascript'></script>
        <script type='text/javascript'>
            $(function() { $( '#startdate' ).datepicker(); });
            $(function() { $( '#enddate' ).datepicker(); });
        </script>";
    include_once (TEMPLATE);
}
