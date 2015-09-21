<?php
/**
 * File containing the orderreport module configuration file, module.php
 *
 * @copyright Copyright (C) 1999 - 2016 Brookins Consulting. All rights reserved.
 * @copyright Copyright (C) 2013 - 2016 Think Creative. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2 (or later)
 * @version 0.1.5
 * @package ezporderreport
 */

// Define module name
$Module = array( 'name' => 'Order Report',
                 'default_view' => 'report',
          );

// Define module view and parameters
$ViewList = array();

// Define 'report' module view parameters
$ViewList['report'] = array( 'name' => 'Order Report',
                             'script' => 'report.php',
                             'functions' => array( 'report' ),
                             'unordered_params' => array( 'offset' => 'Offset', 'limit' => 'Limit' ),
                             'default_navigation_part' => 'ezporderreportnavigationpart',
                             'post_actions' => array(),
                             'params' => array() );

// Define 'reportlist' module view parameters
$ViewList['reportlist'] = array( 'script' => 'reportlist.php',
                                 'functions' => array( 'reportlist' ),
                                 'unordered_params' => array( 'offset' => 'Offset', 'limit' => 'Limit' ),
                                 'default_navigation_part' => 'ezporderreportnavigationpart',
                                 'post_actions' => array(),
                                 'params' => array() );

// Define function parameters
$FunctionList = array();

// Define function 'report' parameters
$FunctionList['report'] = array();

// Define function 'reportlist' parameters
$FunctionList['reportlist'] = array();

?>