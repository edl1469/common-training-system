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
define('DIR', '/'.$config_app['common']['appdir']);
define('PATH_PARENT', '/var/www/html/'.$config_app['common']['parentdir']);
define('PATH_APP', PATH_PARENT.DIR);
define('PATH_GROUP', $config_app['common']['externalwebroot']);
define('NAME_GROUP', $config_app['common']['appname']);
define('URL_APP', '/'.$config_app['common']['parentdir'].DIR);
define('URL_COMMON', "/common/cts");
define('URL_FULL', 'https://'.$config_app[ENVIRONMENT]['apphost'].URL_APP);
define('URL_GROUP', 'http://'.$config_app[ENVIRONMENT]['externalhost'].PATH_GROUP);
define('BACKLINK_ADMIN', "<div id='back'><a href='".URL_APP."/admin/'>Return to Control Panel</a></div>");
define('BACKLINK_APP', "<div id='back'><a href='".URL_APP."'>Return to ".NAME_GROUP."</a></div>");
define('MAIL_GROUP', $config_app['common']['mailaddress']);
define('TEMPLATE', '/var/www/html/common/gui/gui-combined.php');
define('TEMPLATE_BLANK', '/var/www/html/common/gui/gui-blank.php');

/**
 * Default templating variables. NO NEED TO EDIT!
 *
 * These are here only in case they are not set in the file, specifically. Used in gui-combined.php file.
 */
$page['css'] = '<!-- No CSS Inserted. -->';
$page['content'] = '<!-- No Content Inserted. -->';
$page['crumbs'] = '<!-- No Breadcrumbs Inserted. -->';
$page['fauxbody'] = 'offices staff_training';
$page['js'] = "";
$page['menu'] = null;
$page['title'] = NAME_GROUP.' Workshops';

/**
 * Sets the default timezone used by all date/time functions
 */
date_default_timezone_set('America/Los_Angeles');

/**
 * Does ASM get emailed when a user signs up for a workshop? Set in _app.ini.php file.
 */
$config_alertASM = (boolean)$config_app['common']['alertasm'];
