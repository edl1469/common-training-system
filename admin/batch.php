<?php

/**
 * Administrative manual course enrollment tool.
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
$successmsg = (isset($_GET['success']))? 'Your batch enrollment process completed successfully.': '';
// Stop and redirect, if any database resources are unavailable.
if ($mysqli->connect_error) {
    header("Location: ".URL_APP."/resource-unavailable.php?err=MySQLi%20Connect&admin=true");
} else {

    // Pull POST / GET variables.
    $tid = (isset($_GET['TID']))? $_GET['TID']: $_POST['course'];
    $errormsg = (isset($_GET['dup']))? 'This person has been previously registered.': '';
    $successmsg = (isset($_GET['success']))? 'The person was successfully registered.': '';

    // Pull and parse course information.
    $tid = $mysqli->real_escape_string($tid);
    $result = $mysqli->query("SELECT * FROM Training WHERE TID='{$tid}'");
    $row = $result->fetch_assoc();
    $crs_date = $row['TDate'];
    $crs_datef = date("l, F j, Y", strtotime($row['TDate']));
    $crs_end = date("g:i A", strtotime($row['TEndTime']));
    $crs_instr = $row['Trainer'];
    $crs_loc = $row['Location'];
    $crs_name = $row['Description'];
    $crs_seats = $row['TSeats'];
    $crs_start = date("g:i A", strtotime($row['TStartTime']));
    $result->free();

    // Pull and parse registrant information.
    $registrants = "<p>No one is wait-listed in this course yet.</p>";
    $tid = $mysqli->real_escape_string($tid);
    $result = $mysqli->query("SELECT SID,FirstName,LastName FROM Trainees WHERE TID='{$tid}' AND Wait=1");
    //if ($result->num_rows > 0) {
    $registrants = "<p>Click on the registrant name to enroll:</p><ol>";
    while ($row = $result->fetch_assoc()) {
        if ($crs_seats > 0) {
            $registrants .= "<li><a href='enroll_processing.php?TID={$tid}&SID={$row['SID']}'>"."{$row['LastName']}, {$row['FirstName']}</a></li>";
        } else {
            $registrants .= "<li>{$row['LastName']}, {$row['FirstName']}</li>";
        }
    }
    $registrants .= '</ol>';
    //}
    $result->free();
    $mysqli->close();

    // ########## Prepare content
    $html = BACKLINK_ADMIN."<h1>Course Enrollment</h1>";

    $html .= "<p>The course details are as follows:</p>";
    $html .= "<ul><li>Name: {$crs_name}</li><li>Date: {$crs_datef}</li><li>Time: {$crs_start} - {$crs_end}</li>";
    $html .= "<li>Location: {$crs_loc}</li><li>Instructor: {$crs_instr}</li><li>Available Seats: {$crs_seats}</li></ul>\n";
    $html .= "<div id='errorList'></div>\n<div id='message'>{$successmsg}{$errormsg}</div>\n";
    $html .= "<div style='clear: both;'></div>";
    $html .= "<div id='results'></div>";
    if ($crs_seats > 0) {

        $html .= "<h2>Batch Enrollment</h2>\n";
        $html .= "<p>Please provide the Employee ID and Division for each enrollee. Do not leave empty rows. Use the delete button to remove empty rows.</p>";
        $html .= "<div class='dataTables_wrapper'><p><button id='addRow'>Add New Row</button><button id='delRow'>Delete Row</button><button type='reset' value='Reset' id='reset'>Reset</button></p>.<form method='post' role='form' action='batch_enroll_processing.php' id='batchform'>";
        $html .= "
        <table id='batchTable' class='display' cellspacing='0' width='100%'>
        <thead>
            <tr>
                <th>Employee ID</th>
                <th>Division</th>
                <th>Employee Name</th>

            </tr>
        </thead>
        <tbody>
            <tr class='enrowl'>
                <th><input type='text' name='emp_id[]' class='eid' required /></th>
                <th><select name='divs[]' id='drop0' required >
                    <option value=''>Make a Selection</option>
                    <option value='Academic Affairs'>Academic Affairs</option>
                    <option value='Administration and Finance'>Administration and Finance</option>
                    <option value='Athletics'>Athletics</option>
                    <option value='Auxiliary'>Auxiliary</option>
                    <option value='Division of Information Technology'>Division of Information Technology</option>
                    <option value='Office of the President'>Office of the President</option>
                    <option value='Student Services'>Student Services</option>
                    <option value='University Relations &amp; Development'>University Relations &amp; Development</option>
                </select></th>
            <th><span class='data' id='dname0'></span></th>
            </tr>
     </tbody>
    </table>

";
        $html .= "<p>
                <input type='hidden' name='cdate' value='{$crs_date}'>
                <input type='hidden' name='cetime' value='{$crs_end}'>
                <input type='hidden' name='course' value='{$crs_name}'>
                <input type='hidden' name='cstime' value='{$crs_start}'>
                <input type='hidden' name='tid'  id=\"courseid\" value='{$tid}'>

            </p>\n";
    $html .="<input type='submit' value='Enroll All' name='submit' id='submit'>";
    } else {
        $html .= "<p>We cannot enroll someone until a seat is available.</p>\n";
    }

    $html .= "</form></div></div>\n";
    $html .= "<script type='text/javascript'>
    var counter = 1;
    var sel = 0;

    if (sel == counter){
        sel = 0;
    }

    var max =".json_encode($crs_seats).";



    $('#addRow').click(function(){
        sel ++;

    if (counter >= max){
        alert('No more seats available.');

    }
    else{
                 $('#batchTable').append(
            '<tr class=\"enrowl\"><th>' +
              '<input type=\"text\" name = \"emp_id[]\" class=\"eid\"/ required></th>' +
              '<th><select name=\"divs[]\" id=\"drop' + counter + '\" required><option value=\"\">Make a Selection</option>' +
                    '<option value=\"Academic Affairs\">Academic Affairs</option>' +
                    '<option value=\"Administration and Finance\">Administration and Finance</option>' +
                    '<option value=\"Athletics\">Athletics</option>' +
                    '<option value=\"Auxiliary\">Auxiliary</option>' +
                    '<option value=\"Office of the President\">Office of the President</option>' +
                    '<option value=\"Student Services\">Student Services</option>' +
                    '<option value=\"University Relations &amp; Development\">University Relations &amp; Development</option></select>' + '<th><span class=\"data\" id=\"dname' + counter + '\"></span></th> ' + '</th></tr>'
                    );
                    counter ++;

    }
    });
    $('#delRow').click(function(){


    if (sel == 0 && counter == 1){

        alert('The first row cannot be deleted.');

    }
    else{
        if (sel == counter){
            sel--;
        }
        else{

        $(\"#batchTable tr:last\").remove();

        sel --;
        counter --;

        }
    }
});


    $(document).on('change', 'select', function(){
       var rowIndx = $(this).closest('tr').find('span').attr('id');
       var empdata = $(this).closest('tr').find('.eid').val();
        var tid = $('#courseid').val();
       var nrow = sel - 1;

        $.ajax({
            type: 'GET',
            url: 'auth.php',
            data: {emp_id: empdata, cid: tid},
            success : function(response){

                $('#' + rowIndx).html(response);


            }

        });


});

$('#reset').click(function(){
  $('#batchform')[0].reset();
  $('.data').html('');
});




$('#batchform').submit(function(e) {

    var values = $('input[name=\"emp_id[]\"]').map(function() {
      return this.value;
    }).toArray();

    var hasDups = !values.every(function(v,i) {
      return values.indexOf(v) == i;
    });
    if(hasDups){
       // having duplicate values
       alert(\"Duplicate Employee ID Found.\");
       e.preventDefault();
    }

});


    </script>";
    // ########## Write content
    $page['content'] = $html;
    $page['css'] = "<link href='".URL_COMMON."/css/form.css' rel='stylesheet' type='text/css' />\n"."<link href='".URL_COMMON."/css/details.css' rel='stylesheet' type='text/css' />\n"."<link href='".URL_COMMON."/css/signup.css' rel='stylesheet' type='text/css' />\n"."<link href='".URL_COMMON."/css/dataTable.min.css' rel='stylesheet' type='text/css' />\n";
    $page['js'] = "<script src='".URL_COMMON."/js/signup.js' type='text/javascript'></script>\n<script src='".URL_COMMON."/js/dataTables.js' type='text/javascript'></script>\n";
    include_once (TEMPLATE);
}
