<?php


require_once '../_config.php';
require_once '../_connect-mysqli.php';

if (isset($_POST['pr_name'])){

    $prgname = $mysqli->real_escape_string($_POST['pr_name']);
    $is_active = 1;
    $success = 'We are good to start';
    $cols = "program_name, is_active";
    $vals = "'$prgname','$is_active'";

    //insert into program table
    $mysqli->query("INSERT INTO program ({$cols}) VALUES ({$vals})");
    $mysqli->close();
    header("Location:program.php?name=$prgname");

}
