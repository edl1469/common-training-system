<?php

/**
 * The configuration variables and strings for this web application.
 *
 * PHP version 5
 *
 * @category  ITSWebApplication
 * @package   ESTrainingApp
 * @author    Steven Orr <Steven.Orr@csulb.edu>
 * @copyright 2013 CSULB Information Technology Services
 * @license   http://opensource.org/licenses/gpl-3.0.html GNU Public License v3
 * @link      http://daf.csulb.edu/
 */

 //
// These configuration settings SHOULD be edited.
//

/**
 * This controls whether the ASM is emailed when a user signs up for a workshop.
 */
$config_alertASM = false;

/**
 * This populates the administrative form's location select field, when creating a workshop.
 */
$config_locations = array(
    'BLDG-RM' => 'Training Room Name, BLDG-RM',
);

//
// The configuration settings below SHOULD NOT require editing.
//

/**
 * Environment variable, used in settings configurations and in application. NO NEED TO EDIT!
 *
 * This must be set to one of the following:
 *     development
 *     testing
 *     production
 *
 * IMPORTANT: This static variable is now set using a host-wide Apache
 *   Environment Variable, found in a special server configuration file:
 *   /etc/httpd/conf.d/environment.conf
 */
define('ENVIRONMENT', apache_getenv("HTTPD_ENV"));

/**
 * Constants, which CAN change per application environment. NO NEED TO EDIT!
 *
 * The order in which these are created IS important.
 */
$config_app = parse_ini_file("_app.ini.php", true);
define('DIR',            '/'.$config_app['common']['appdir']);
define('PATH_PARENT',    $config_app['common']['localdocroot']."/".$config_app['common']['parentdir']);
define('PATH_APP',       PATH_PARENT.DIR);
define('PATH_COMMON',    PATH_PARENT.'/'.$config_app['common']['assetsdir']);
define('PATH_GROUP',     $config_app['common']['externalwebroot']);
define('NAME_GROUP',     $config_app['common']['appname']);
define('APP_HOST',       'http://'.$config_app[ENVIRONMENT]['apphost']);
define('URL_PARENT',     APP_HOST."/".$config_app['common']['parentdir']);
define('URL_APP',        URL_PARENT.DIR);
define('URL_COMMON',     URL_PARENT."/".$config_app['common']['assetsdir']);
define('URL_GROUP',      'http://'.$config_app[ENVIRONMENT]['apphost'].PATH_GROUP);
define('BACKLINK_ADMIN', "<div id='back'><a href='".URL_APP."/admin/'>Return to Control Panel</a></div>");
define('BACKLINK_APP',   "<div id='back'><a href='".URL_APP."'>Return to ".NAME_GROUP."</a></div>");
define('MAIL_GROUP',     $config_app['common']['mailaddress']);
define('CHUNK1',         PATH_COMMON.'/'.$config_app['common']['guifile'].'1.php');
define('CHUNK2',         PATH_COMMON.'/'.$config_app['common']['guifile'].'2.php');
define('CHUNK3',         PATH_COMMON.'/'.$config_app['common']['guifile'].'3.php');
define('CHUNK4',         PATH_COMMON.'/'.$config_app['common']['guifile'].'4.php');

/**
 * Sets the default timezone used by all date/time functions
 */
date_default_timezone_set('America/Los_Angeles');
