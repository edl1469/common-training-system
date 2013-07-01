<?php

/**
 * The MySQL Extension database connection code for this web application.
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
 * Set constants according to environment.
 */
define('MYSQL_SVR', $config_app[ENVIRONMENT]['mysqlhst']);
define('MYSQL_USR', $config_app[ENVIRONMENT]['mysqlusr']);
define('MYSQL_PWD', $config_app[ENVIRONMENT]['mysqlpwd']);
define('MYSQL_DBS', $config_app[ENVIRONMENT]['mysqldbs']);

/**
 * Host connection handle.
 */
$mysql_connect = mysql_connect(MYSQL_SVR, MYSQL_USR, MYSQL_PWD);

/**
 * Connection error handling.
 */
// This activity must be performed on the main code body.

/**
 * Database selection.
 */
// This activity must be performed on the main code body.
