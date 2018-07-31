<?php

if (!empty($_POST['emp_id'])&& !empty($_POST['divs'])){
require_once '../_config.php';
require_once '../_connect-mysqli.php';
require_once '../_connect-oracle.php';



$hostname = "ldaps://idm.csulb.edu/";
$ldapport = 636;

// Accounts with which to Bind
$binddn01  = 'CN=alertmgmt_service,OU=Users,OU=Infrastructure Support,DC=idm,DC=csulb,DC=edu';
$bindpwd01 = 'cbjM8ugF';
$search_basedn = "OU=Active,OU=People,DC=idm,DC=csulb,DC=edu";

//create arrays for emp_ids and division information
$emp_array = $_POST['emp_id'];
$div_array = $_POST['divs'];

for($i=0; $i< count($emp_array);$i++)
{
 $search_filter = "CN={$emp_array[$i]}";
 $ldapconn = @ldap_connect($hostname,$ldapport);
 $ldapbind = ldap_bind($ldapconn, $binddn01, $bindpwd01);
 $entry = @ldap_search($ldapconn, $search_basedn, $search_filter);
 $info = @ldap_get_entries($ldapconn, $entry);

    $crs_date = $_POST['cdate'];
    $crs_end = date("G:i", strtotime($_POST['cetime']));
    $crs_id = $mysqli->real_escape_string($_POST['tid']);
    $crs_name = $_POST['course'];
    $crs_start = date("G:i", strtotime($_POST['cstime']));
    $reg_email = $info[0]['csulbemployeemail'][0];
    $reg_empdiv = $mysqli->real_escape_string($div_array[$i]);
    $reg_empdpt = addslashes($info[0]['department'][0]);
    $reg_empid = $mysqli->real_escape_string($emp_array[$i]);
    $reg_ext = $info[0]['telephonenumber'][0];
    $reg_first = $info[0]['givenname'][0];
    $reg_last = $info[0]['sn'][0];
    $reg_status = $info[0]['calstateedupersonprimaryaffiliation'][0];






      // Pull ASM information.
      $asm_email = 'wdc@csulb.edu';
      $asm_name = 'Unknown';
      $query = "SELECT EMAIL_ADDR,NAME FROM sysadm.PS_LB_HR_WO_ASM_VW WHERE emplid='{$reg_empid}'";
      $parsed = oci_parse($oracle_connect, $query);
      $product = oci_execute($parsed);
      if (!$product) {

        // Cannot combine following function in Write context; must separate.
        $e = oci_error($parsed);
        if (empty($e)) {
          $e = oci_error();
        }
        $h = "MIME-Version: 1.0\r\nFrom: ".MAIL_GROUP;
        $m = "Environment: ".ENVIRONMENT."\nHost: ".ORACLE_SVR."\nDB: ".ORACLE_DBS."\nOCI Error: "
            .htmlentities($e['message'])."\nSQL: ".htmlentities($e['sqltext']);
        $s = "Oracle View Error: ".$_SERVER['REQUEST_URI'];
        mail('wdc@csulb.edu', $s, $m, $h);
      } else {
        $row = oci_fetch_array($parsed, OCI_ASSOC);
        $asm_email = $row['EMAIL_ADDR'];
        $asm_name = $row['NAME'];
      }
      oci_free_statement($parsed);

      // Pull course information.
      $result = $mysqli->query("SELECT TSeats,Email_Confirm,course_email FROM Training Where TID='{$crs_id}'");
      $row = $result->fetch_assoc();
      $email_from = (!is_null($row['course_email']))? $row['course_email']: MAIL_GROUP;
      $email_msg = $row['Email_Confirm'];
      $newtotal = (int)$row['TSeats'] - 1;
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
      $headers = "MIME-Version: 1.0\r\nContent-type:text/html;charset=iso-8859-1\r\nFrom: {$email_from}\r\n";
      $msg = (empty($email_msg))? "You have been enrolled in a ".NAME_GROUP." course.": $email_msg;
      $to = "{$reg_email}";
      if ($config_alertASM) {
        $to .= ", {$asm_email}";
      }
      mail($to, 'Training Registration Confirmation', $msg, $headers);
    }

  oci_close($oracle_connect);
  $mysqli->close();

  header("Location: batch.php?TID={$crs_id}&success=1");

}





