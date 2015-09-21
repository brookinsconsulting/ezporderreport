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
 * Default module parameters
 */
$module = $Params["Module"];

/**
* Default class instances
*/

/** Parse HTTP POST variables **/
$http = eZHTTPTool::instance();

/** Init template behaviors **/
$tpl = eZTemplate::factory();

/** Access ini variables **/
// $ini = eZINI::instance();
// $iniOrderreport = eZINI::instance( 'ezporderreport.ini' );

/** Report file variables **/
$ordersCount = eZOrder::activeCount();

$requestlimit = 15;
$requestoffset = 0;
$requestssearch = '';

if( $http->getVariable( 'limit' ) )
{
    $requestlimit = $http->getVariable( 'limit' );
}

$tpl->setVariable( 'requestlimit', $requestlimit );

if( $http->getVariable( 'offset' ) )
{
    $requestoffset = $http->getVariable( 'offset' );
}

$tpl->setVariable( 'requestoffset', $requestoffset );

if( $http->getVariable( 'sSearch' ) )
{
    $requestssearch = $http->getVariable( 'sSearch' );
}

$tpl->setVariable( 'requestssearch', $requestssearch );

$tpl->setVariable( 'order_count', $ordersCount );

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