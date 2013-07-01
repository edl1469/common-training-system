<?php

/**
 * The MySQL Improved Extension database connection code for this web application.
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
define('MYSQLI_SVR', $cfg_app[ENVIRONMENT]['mysqlihst']);
define('MYSQLI_PRT', $cfg_app[ENVIRONMENT]['mysqliprt']);
define('MYSQLI_USR', $cfg_app[ENVIRONMENT]['mysqliusr']);
define('MYSQLI_PWD', $cfg_app[ENVIRONMENT]['mysqlipwd']);
define('MYSQLI_DBS', $cfg_app[ENVIRONMENT]['mysqlidbs']);

/**
 * Host connection handle.
 */
$mysqli = new mysqli(MYSQLI_SVR, MYSQLI_USR, MYSQLI_PWD, MYSQLI_DBS, MYSQLI_PRT);

/**
 * Connection error handling.
 */
// This activity must be performed on the main code body.

/**
 * Database selection.
 */
// This activity must be performed at time of object instantiation, above.
