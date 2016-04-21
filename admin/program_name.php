<?php

require_once '../_config.php';

    $courses = '<form name="program_name" method="post" action="program_name_create.php"><fieldset><legend>Create Program</legend><label for="pr_name">Program Name:</label><input type="text" name="pr_name" id="pr_name" required> ';

    $courses .= "<input type='submit' value='Create Program Name' name='submit'></fieldset></form\n";



  // ########## Prepare content
  $html = $back_link."<h1>".NAME_GROUP." Program Creation</h1>";
  $html .= "<p>STEP 1: Create Program Name:</p>{$courses}";



  // ########## Write content
  $page['content'] = $html;
  $page['js'] = "<script src='".URL_COMMON."/js/jquery.min.js' type='text/javascript'></script>\n";

  include_once (TEMPLATE);

