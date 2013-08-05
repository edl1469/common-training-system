<?php

/**
 * Processing the administrative course enrollment form.
 *
 * Currently this sends out an email ONLY IF manually enrolled;
 * if selecting waitlisted person, the process is to speak with
 * them first, offline, then activate them.
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
session_start();
require_once '../_config.php';
require_once '../_connect-mysqli.php';
require_once '../_connect-oracle.php';

// Stop and redirect, if any database resources are unavailable.
if ($mysqli->connect_error) {
    header("Location: ".URL_APP."/resource-unavailable.php?err=MySQLi%20Connect&admin=true");
} elseif (!$oracle_connect) {
    header("Location: ".URL_APP."/resource-unavailable.php?err=Oracle%20Connect&admin=true");
} else {

    // @TODO: Check if TSeats is not zero; throw err if it is.
    // Handle Wait-listed registrants.
    if (isset($_GET['SID'])) {

        // Gather GET variables.
        $crs_id = $mysqli->real_escape_string($_GET['TID']);
        $dbid   = $mysqli->real_escape_string($_GET['SID']);

        // Get and parse attendance information.
        $tid       = $mysqli->real_escape_string($tid);
        $result    = $mysqli->query("SELECT TSeats,TWait FROM Training WHERE TID='{$crs_id}'");
        $row       = $result->fetch_assoc();
        $crs_seats = (int) $row['TSeats'];
        $crs_waits = (int) $row['TWait'];
        $result->free();

        // Modify course seating counts.
        $crs_seats--;
        $crs_waits--;

        // Now update registrant and course tables with attendance.
        $mysqli->query("UPDATE Trainees SET Wait=0,Attend=1 WHERE SID='{$dbid}'");
        $mysqli->query("UPDATE Training SET TSeats={$crs_seats},TWait={$crs_waits} WHERE TID='{$crs_id}'");
    } else {

        // Gather POST variables.
        $crs_date   = $_POST['cdate'];
        $crs_end    = $_POST['cetime'];
        $crs_id     = $mysqli->real_escape_string($_POST['tid']);
        $crs_name   = $_POST['course'];
        $crs_start  = $_POST['cstime'];
        $reg_email  = $mysqli->real_escape_string($_POST['e_mail']);
        $reg_empdiv = $_POST['division'];
        $reg_empdpt = $_POST['dept'];
        $reg_empid  = $_POST['emp_id'];
        $reg_ext    = $_POST['extension'];
        $reg_first  = ucfirst($_POST['fname']);
        $reg_last   = ucfirst($_POST['lname']);
        $reg_status = $_POST['emp_status'];

        // Check for duplicate enrollment.
        $result     = $mysqli->query("SELECT EmpID FROM Trainees WHERE Email='{$reg_email}' AND TID='{$crs_id}'");
        $row        = $result->fetch_assoc();
        $reg_exists = $result->num_rows ;
        $result->free();

        if ($reg_exists) {
            header("Location: enroll.php?TID={$crs_id}&dup=1");
        } else {

            // Pull ASM information.
            $asm_email = 'wdc@csulb.edu';
            $asm_name  = 'Unknown';
            $query     = "SELECT EMAIL_ADDR,NAME FROM sysadm.PS_LB_HR_WO_ASM_VW WHERE emplid='{$reg_empid}'";
            $parsed    = oci_parse($oracle, $query);
            $product   = oci_execute($parsed);
            if (!$product) {
                $e = oci_error($parsed);
                $h = "MIME-Version: 1.0\r\nFrom: training@csulb.edu";
                $m = "Environment: ".ENVIRONMENT."\nHost: ".ORACLE_SVR."\nDB: ".ORACLE_DBS."\nOCI Error: ".htmlentities($e['message']);
                $s = "Oracle View Error: ".DIR;
                mail('wdc@csulb.edu', $s, $m, $h);
            } else {
                $row       = oci_fetch_array($parsed, OCI_ASSOC);
                $asm_email = $row['EMAIL_ADDR'];
                $asm_name  = $row['NAME'];
            }
            oci_free_statement($parsed);

            // Pull course information.
            $result    = $mysqli->query("SELECT TSeats,Email_Confirm FROM Training Where TID='{$crs_id}'");
            $row       = $result->fetch_assoc();
            $email_msg = $row['Email_Confirm'];
            $newtotal  = (int) $row['TSeats'] - 1;
            $result->free();

            // Update course seating.
            $mysqli->query("UPDATE Training SET TSeats={$newtotal} WHERE TID='$crs_id'");

            // Prepare and insert new registrant.
            $cols = "FirstName, LastName, Email, Division, Dept, ASM, EmpID, EmpStatus, Ext, "
                        ."TDate, TStartTime, TEndTime, Description, TID, Attend, Wait";
            $vals = "'{$reg_first}', '{$reg_last}', '{$reg_email}', '{$reg_empdiv}', '{$reg_empdpt}', '{$asm_name}', "
                    ."'{$reg_empid}', '{$reg_status}', '{$reg_ext}', "
                    ."'{$crs_date}', '{$crs_start}', '{$crs_end}', '{$crs_name}', '{$crs_id}', 1, 0";
            $mysqli->query("INSERT INTO Trainees ({$cols}) VALUES ({$vals})");

            // Send registration confirmation email.
            // @TODO: Quick fix to stop ASM emails for Enrollment Services. Must be comprehensively done later.
            $headers = "MIME-Version: 1.0\r\nContent-type:text/html;charset=iso-8859-1\r\nFrom: training@csulb.edu";
            $msg     = (empty($email_msg))? "You have been enrolled in a ".NAME_GROUP." course.": $email_msg;
            $to      = "{$reg_email}";
            if ($config_alertASM) {
                $to .= ", {$asm_email}";
            }
            $headers  = "MIME-Version: 1.0\r\nContent-type:text/html;charset=iso-8859-1\r\nFrom: training@csulb.edu";
            mail($to, 'Training Registration Confirmation', $msg, $headers);
        }
    }

    // Close all DB connections
    oci_close($oracle_connect);
    $mysqli->close();
    header("Location: enroll.php?TID={$crs_id}&success=1");
}
