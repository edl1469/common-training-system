<?php

/**
 * The destination page, if any required resources (databases, etc) are unavailable.
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

// Collect GET
$type = $_GET['err'];

$back_link = "<div id='back'>";
$back_link .= (isset($_GET['admin']))? "<a href='admin/index.php'>Return to Control Panel</a></div>": "<a href='".URL_APP."'>Return to ".NAME_GROUP."</a></div>";

// ########## Prepare content
$html .= $back_link."<h1>Application Difficulties</h1>";
$html .= "<p>Unfortunately, our web application has experienced difficulties that "."it cannot overcome. Please return later to conduct your business.</p>";
$html .= "<p>Please report the problem, if you are willing, to: "."<a href='mailto:wdc@csulb.edu?subject=".htmlentities(NAME_GROUP)."&body=Application%20Error:%20{$type}'>wdc@csulb.edu</a> ";

// ########## Write content
$page['content'] = $html;
include_once (TEMPLATE);
