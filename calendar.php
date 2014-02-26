<?php

/**
 * Course listing in calendar format.
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
include_once '_config.php';
include_once '_connect-mysql.php';

// Stop and redirect, if any database resources are unavailable.
if (!$mysql_connect) {
  header("Location: resource-unavailable.php?err=MySQL%20Connect");
} else {
  mysql_select_db(MYSQL_DBS, $mysql_connect);

  $append = (isset($_GET['admin']))? "?admin=true": '';
  $appendtoo = (isset($_GET['admin']))? "&admin=true": '';
  $back_link = "<div id='back'>";
  $back_link .= (isset($_GET['admin']))? "<a href='admin/index.php'>Return to Control Panel</a></div>": "<a href='".URL_APP."'>Return to ".NAME_GROUP."</a></div>";

  $calcount = 1;
  // Keep track of the number of calendars printed.
  $date = getdate();
  // Returns an associative array containing the date information of the current local time.
  $showmonths = 4;
  // Select how many months should be shown.

  // ########## Prepare content
  $html .= $back_link."<h1>".NAME_GROUP." Calendar</h1>";
  do {
    $display = ($calcount > 1)? " style='display: none;' ": '';

    // Prepare the Previous and Next strings.
    $next_link = '';
    $prev_link = '';
    if ($calcount < $showmonths) {
      $next = $calcount + 1;
      $next_link = "<a class='n' href='#div{$next}' onclick=\"toggle('div{$calcount}', 'div{$next}');\"><span>Next</span></a>";
    }
    if ($calcount > 1) {
      $prev = $calcount - 1;
      $prev_link = "<a class='p' href='#div{$prev}' onclick=\"toggle('div{$calcount}', 'div{$prev}');\"><span>Previous</span></a>";
    }

    // Get date variables populated.
    $day_name = $date['weekday'];
    $day_num = ($date['mday'] < 10)? "0{$date['mday']}": "{$date['mday']}";
    $month_name = $date['month'];
    $month_num = ($date['mon'] < 10)? "0{$date['mon']}": "{$date['mon']}";
    $year = $date['year'];

    $today_date = "{$year}-{$month_num}-{$day_num}";

    // Set variables for first day of month.
    $new_day = getdate(mktime(0, 0, 0, $month_num, 1, $year));
    $first_week_day = $new_day["wday"];
    $more_days = true;

    // Find last day of month. Set var to near last day and cycle until new month.
    $near_last_day = 27;
    while (($near_last_day <= 32) && ($more_days)) {
      $new_day = getdate(mktime(0, 0, 0, $month_num, $near_last_day, $year));

      // Are we in the next month yet?
      if ($new_day["mon"] != $month_num) {

        // New month, the last day was the day before.
        $lastday = $near_last_day - 1;
        $more_days = false;
      }
      $near_last_day++;
    }

    // Print top part of calendar
    $html .= "<div class='grid' id='div{$calcount}'{$display}>";
    $html .= "<div class='prev_next'><h2>{$month_name} {$year}</h2><p>{$next_link} {$prev_link}</p></div>";

    // Print remainder of calendar
    $html .= "<p>These are all of the courses currently scheduled this month. You can also see these in a <a href='list.php{$append}'>list view</a>.</p>";
    $html .= "<form id='calForm{$calcount}' name='calForm{$calcount}' method='post' action='#'>";
    $html .= "<table class='clearit' summary='Reporting Calendar for {$month_name}' id='cal{$calcount}'>";
    $html .= '<thead><tr><th scope="col" class="small_col">Sunday</th><th scope="col">Monday</th><th scope="col">Tuesday</th><th scope="col">Wednesday</th><th scope="col">Thursday</th><th scope="col">Friday</th><th scope="col" class="small_col">Saturday</th></tr></thead><tbody>';

    // Begin placement of days in calendar.
    $day = 1;
    $isfirstweek = true;
    $week_day = $first_week_day;

    // Create days and weeks of calendar
    while ($day <= $lastday) {

      // Note this days's timestamp for comparison.
      $this_day = ($day < 10)? "0{$day}": "{$day}";
      $this_date = "{$year}-{$month_num}-{$this_day}";
      $day_name = date("l", mktime(0, 0, 0, $month_num, $this_day, $year));

      // If this date is found in the 'training' table, show the corresponding information.
      // @TODO: This query is run once for each day of the month, for every month! RUN ONCE!!!!!
      $sql = "SELECT * FROM Training Where TDate='{$this_date}' AND IsVisible=1 ";
      $result = mysql_query($sql);

      // If first week, fill with blank day spaces until first day.
      if ($isfirstweek) {
        for ($i = 1; $i <= $first_week_day; $i++) {
          if ($i == 1) {
            $html .= "\t<tr><th class='badday'> &nbsp; </th>";
          } else {
            $html .= "<td class='badday'> &nbsp; </td>";
          }
        }
        $isfirstweek = false;
      }

      // If the day being created is Sunday, start off new row; else construct correctly.
      if ($week_day == 0) {
        $html .= "\t<tr><td class='weekend'>";
      } elseif ($week_day == 6) {
        $html .= "\t\t<td class='weekend'>";
      } elseif ($this_date === $today_date) {
        $html .= "\t\t<td class='today'>";
      } else {
        $html .= "\t\t<td>";
      }

      $html .= "<label for='Day_{$day}'><span class='dayNumber' "."title='{$day_name}, {$month_name} {$day}'>{$day}</span> </label>\n";

      // Work through each course's information.
      while ($row = mysql_fetch_array($result)) {
        $desc = $row['Description'];
        $location = $row['Location'];
        $tid = $row['TID'];
        $title = $row['Short_Description'];
        $trainer = $row['Trainer'];

        $state = '';
        $time = '';

        // Filter out unfinished courses.
        if ($row['TStartTime'] == "00:00:00") {
          $time = '';
          $state = '';
        } else {
          $time = date("g:i A", strtotime($row['TStartTime']))." - ".date("g:i A", strtotime($row['TEndTime']));

          // Do not print if in the past.
          if ($this_date >= $today_date) {

            // Do not allow signup if private.
            if ($row['IsPrivate'] == 0) {

              // Do not allow signup if full.
              $state = "<a href='signup.php?TID={$row['TID']}";
              if (($row['TSeats'] - $row['TWait']) > 0) {
                $state .= "'>Sign Up</a>";
              } else {
                $state .= "&wait=1'>Join Waitlist</a>";
              }
            } else {
              $state = "Private.";
            }
          }
        }
        $html .= "<br /><br /><a href='details.php?id={$tid}{$appendtoo}' class='tt'>{$title}"."<span class='tooltip'><span class='top'></span><span class='middle'>{$desc}<br>{$time}<br>"."Location: {$location}<br>Trainer: {$trainer}</span><span class='bottom'></span></span></a>"."<a href='#' class='tt' id='info'><span class='tooltip'><span class='top'></span>"."<span class='middle'>{$desc}<br>{$time}<br>Location: {$location}<br>Trainer: {$trainer}</span>"."<span class='bottom'></span></span></a><br /><strong>{$state}</strong><br />";
      }

      // Close row on Saturday.
      if ($week_day == 6) {
        $html .= "</td></tr>\n";
      } else {
        $html .= "</td>";
      }

      // Adjust all the week counter values for another loop
      $week_day++;
      $week_day = $week_day % 7;
      $day++;
    }

    // If we didn't finish out the last week of the month, create empty cells.
    if (($week_day <= 6) && ($week_day != 0)) {
      for ($i = (7 - $week_day); $i > 0; $i--) {
        // Perform for Saturday or Weekdays
        $html .= "<td class='badday'> &nbsp; </td>";
      }
      $html .= "</tr>\n";
    }

    // End of month, close the table
    $html .= "</tbody></table></form><div class='prev_next' id='lower'><p>{$next_link} {$prev_link}</p></div></div>";

    // Adjust all the date and calendar values for the next month
    $month_next = $month_num + 1;
    $calcount++;
    $date = getdate(mktime(0, 0, 0, $month_next, 1, $year));
  } while ($calcount <= $showmonths);
  mysql_close($mysql_connect);

  // ########## Write content
  $page['content'] = $html;
  $page['css'] = "<link href='https://daf.csulb.edu/css/main_page.css' rel='stylesheet' type='text/css' />\n"
      ."<link type='text/css' href='".URL_COMMON."/css/calendar.css' rel='stylesheet' media='screen' />";
  $page['js'] = "<script language='javascript'> function toggle(hide, show) { "
      ."document.getElementById(hide).style.display = 'none'; "
      ."document.getElementById(show).style.display = 'block'; } </script>";
  include_once (TEMPLATE);
}
