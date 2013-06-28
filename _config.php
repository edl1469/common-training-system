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

/**
 * Environment variable, used in settings configurations and in application.
 *
 * This must be set to one of the following:
 *
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
 * Constants, which CAN change per application environment.
 */
$cfg_app = parse_ini_file("_app.ini.php", true);
define('DIR', '/'.$cfg_app['common']['host']);
define('PATH_PARENT', $cfg_app['common']['localdocroot']."/".$cfg_app['common']['parentdir']);
define('PATH_APP', PATH_PARENT.DIR);
define('PATH_COMMON', PATH_PARENT.'/common');
define('PATH_GROUP', $cfg_app['common']['externalwebroot']);
define('NAME_GROUP', $cfg_app['common']['appname']);

define('APP_HOST', 'http://'.$cfg_app[ENVIRONMENT]['apphost']);
define('URL_PARENT', APP_HOST."/".$cfg_app['common']['parentdir']);
define('URL_APP', URL_PARENT.DIR);
define('URL_COMMON', URL_PARENT."/".$cfg_app['common']['assetsdir']);
define('URL_GROUP', 'http://'.$cfg_app[ENVIRONMENT]['apphost'].PATH_GROUP);
define('BACKLINK_ADMIN', "<div id='back'><a href='".URL_APP."/admin/'>Return to Control Panel</a></div>");
define('BACKLINK_APP', "<div id='back'><a href='".URL_APP."'>Return to ".NAME_GROUP."</a></div>");
define('MAIL_GROUP', $cfg_app['common']['mailaddress']);

define('CHUNK1', PATH_COMMON.'/'.$cfg_app['common']['guifile'].'1.php');
define('CHUNK2', PATH_COMMON.'/'.$cfg_app['common']['guifile'].'2.php');
define('CHUNK3', PATH_COMMON.'/'.$cfg_app['common']['guifile'].'3.php');
define('CHUNK4', PATH_COMMON.'/'.$cfg_app['common']['guifile'].'4.php');

/**
 * Variables.
 */
$config_alertASM = false;
$config_locations = array(
    'ITS Training, BH-180D' => 'ITS Training Room, BH-180D',
);
