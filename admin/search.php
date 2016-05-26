<?php
require_once '../_config.php';
/**

 * Form to facilitate the reporting of existing user registrations.
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
  

  $html = BACKLINK_ADMIN.."<h1>Quick Search</h1>\n";
  $html .="<p id='introtext'> This search feature can be used to perform a quick search by Employee ID, Course Description, First Name, or Last Name. The data will display after 3 characters have been typed.</p>";
  $html .= "<div id='search_pad'><label for='search' id='srchlabel'>Search: </label> <input type='text' autocomplete='off' name='search' id='srch'  /></div>";
  $html .="<table id='search' class='display' cellspacing='0' width='100%'>
            <thead>
            <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Course</th>
            <th>Date</th>
            </tr>";
  $html .= "<tbody id='data'>";
  $html .= "</tbody>";

  $html .= "</table></div>";
  $html .= "<script type='text/javascript'>
            $(document).on('keyup', 'input', function(){

       var srchdata = $('#srch').val();
        if ($(this).val().length > 2){
        $.ajax({
            type: 'GET',
            url: 'search_process.php',
            data: {keywords: srchdata},
            success : function(response){

                $('#data').html(response);


            }

        });
        }else{
            srchdata = '';
            $('#data').html('');
        }
        });
        </script>";

  // ########## Write content
  $page['content'] = $html;
  $page['css'] = "<link href='".URL_COMMON."/css/form.css' rel='stylesheet' type='text/css' />\n"."<link href='".URL_COMMON."/css/details.css' rel='stylesheet' type='text/css' />\n"."<link href='".URL_COMMON."/css/signup.css' rel='stylesheet' type='text/css' />\n"."<link href='".URL_COMMON."/css/dataTable.min.css' rel='stylesheet' type='text/css' />\n";
  $page['js'] = "<script src='".URL_COMMON."/js/jquery.min.js' type='text/javascript'></script>\n";

  include_once (TEMPLATE);

