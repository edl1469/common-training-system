<?php
if (isset($_GET['cid'])){

$response = '';
$esc_campusid = $_GET['emp_id'];
$crs_id = $_GET['cid'];

require_once '../_config.php';
require_once '../_connect-mysqli.php';

    // Check for duplicate enrollment.
    $result = $mysqli->query("SELECT EmpID FROM Trainees WHERE EmpID = '{$esc_campusid}' AND TID='{$crs_id}'");
    $row = $result->fetch_assoc();
    $reg_exists = $result->num_rows;
    $result->free();
    if ($reg_exists) {
        $response = '<span style="color:red;" class="data">Duplicate record. Please Remove.</span>';
    echo $response;
    }

    else{
        if (!empty($esc_campusid)) {


        $hostname = "ldaps://idm.csulb.edu/";
        $ldapport = 636;

        // Accounts with which to Bind
        $binddn01 = 'CN=alertmgmt_service,OU=Users,OU=Infrastructure Support,DC=idm,DC=csulb,DC=edu';
        $bindpwd01 = 'cbjM8ugF';
        $search_basedn = "OU=Active,OU=People,DC=idm,DC=csulb,DC=edu";

        $search_filter = "CN={$esc_campusid}";

        $ldapconn = @ldap_connect($hostname, $ldapport);

        if ($ldapconn) {

            $ldapbind = ldap_bind($ldapconn, $binddn01, $bindpwd01);

            // verify binding
            if ($ldapbind) {

                $entry = @ldap_search($ldapconn, $search_basedn, $search_filter);

                // verify search
                if ($entry) {

                    $info = @ldap_get_entries($ldapconn, $entry);

                    $response = '<span style="color:blue;" class="data">'.$info[0]['givenname'][0].'&nbsp;'.$info[0]['sn'][0].'</span>';

                    @ldap_close($ldapconn);
                }

            }

        }

    }


echo $response;
    }

}



