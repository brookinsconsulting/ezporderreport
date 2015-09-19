<?php
/**
 * File containing the orderreport/report module view.
 *
 * @copyright Copyright (C) 1999 - 2016 Brookins Consulting. All rights reserved.
 * @copyright Copyright (C) 2013 - 2016 Think Creative. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2 (or later)
 * @version 0.1.5
 * @package ezporderreport
 */

/**
 * Disable memory and time limit
 */
// set_time_limit( 0 );
// ini_set( "memory_limit", -1 );

/**
 * Default module parameters
 */
$module = $Params["Module"];

/**
* Default class instances
*/

/** Parse HTTP POST variables **/
// $http = eZHTTPTool::instance();

/** Access system variables **/
// $sys = eZSys::instance();

/** Init template behaviors **/
$tpl = eZTemplate::factory();

/** Access ini variables **/
// $ini = eZINI::instance();
// $iniOrderreport = eZINI::instance( 'ezporderreport.ini' );

/** Report file variables **/
$ordersCount = eZOrder::activeCount();
$tpl->setVariable( 'order_count', $ordersCount );

$requestlimit = 15;
$requestoffset = 0;
$requestssearch = '';

if( isset( $_GET['limit'] ) )
{
    $requestlimit = $_GET['limit'];
}

$tpl->setVariable( 'requestlimit', $requestlimit );

if( isset( $_GET['offset'] ) )
{
    $requestoffset = $_GET['offset'];
}

$tpl->setVariable( 'requestoffset', $requestoffset );

if( isset( $_GET['sSearch'] ) )
{
    $requestssearch = $_GET['sSearch'];
}

$tpl->setVariable( 'requestsSearch', $requestssearch );

/**
 * Default template include
 */
$Result = array();
$Result['content'] = $tpl->fetch( "design:orderreport/orderreport_datatables.tpl" );
$Result['path'] = array( array( 'url' => false,
                                'text' => ezpI18n::tr('design/standard/orderreport', 'Order Report') ),
                         array( 'url' => false,
                                'text' => ezpI18n::tr('design/standard/orderreport', 'Report') )
                        );

$Result['left_menu'] = 'design:orderreport/menu.tpl';

?>