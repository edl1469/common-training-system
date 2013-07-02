<?php

/**
 * The Oracle Extension database connection code for this web application.
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
define('ORACLE_SVR', $config_app[ENVIRONMENT]['oraclehst']);
define('ORACLE_PRT', $config_app[ENVIRONMENT]['oracleprt']);
define('ORACLE_USR', $config_app[ENVIRONMENT]['oracleusr']);
define('ORACLE_PWD', $config_app[ENVIRONMENT]['oraclepwd']);
define('ORACLE_DBS', $config_app[ENVIRONMENT]['oracledbs']);

/**
 * Host connection handle.
 */
$oracle_connect = @oci_connect(ORACLE_USR, ORACLE_PWD, ORACLE_SVR.":".ORACLE_PRT."/".ORACLE_DBS);

/**
 * Connection error handling.
 */
// This activity must be performed on the main code body.

/**
 * Database selection.
 */
// This activity must be performed at time of query.
