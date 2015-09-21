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
// $tpl = eZTemplate::factory();

/** Access ini variables **/
// $ini = eZINI::instance();
// $iniOrderreport = eZINI::instance( 'ezporderreport.ini' );

/**
 * Default module view request parameters
 */

$http->getVariable('iDisplayStart');
$offset = $http->getVariable('iDisplayStart');
$limit = ( $http->getVariable('iDisplayLength') ) ? $http->getVariable('iDisplayLength') : 15;
$sSearch = $http->getVariable('sSearch');

$sortCol = $http->getVariable('iSortCol_0');
$sortDir = $http->getVariable('sSortDir_0');

if( $sortCol == 0 )
{
    if( eZPreferences::value( 'admin_orderlist_sortfield' ) )
    {
        $sortField = eZPreferences::value( 'admin_orderlist_sortfield' );
    }

    if ( !isset( $sortField ) || ( ( $sortField != 'created' ) && ( $sortField != 'user_name' ) ) )
    {
        $sortField = 'created';
        //$sortField = 'order_nr';
    }
}
else
{
    switch( $sortCol )
    {
        case 1:
            $sortField = 'order_nr';
            break;
        case 2:
            $sortField = 'user_name';
            break;
        case 4:
            $sortField = 'totalexvat';
            break;
        case 5:
            $sortField = 'totalinvvat';
            break;
        case 6:
            $sortField = 'created';
            break;
        case 7:
            $sortField = 'status';
            break;
        default:
            $sortField = 'order_nr';
            break;
    }
}

if( $sortDir == 'asc' )
{
    $sortOrder = 'asc';
}
elseif( $sortDir == 'desc' )
{
    $sortOrder = 'desc';
}
else
{
    if( eZPreferences::value( 'admin_orderlist_sortorder' ) )
    {
        $sortOrder = eZPreferences::value( 'admin_orderlist_sortorder' );
    }

    if ( !isset( $sortOrder ) || ( ( $sortOrder != 'asc' ) && ( $sortOrder!= 'desc' ) ) )
    {
        $sortOrder = 'asc';
    }
}

// Archive options.
if ( $http->hasPostVariable( 'ArchiveButton' ) )
{
    if ( $http->hasPostVariable( 'OrderIDArray' ) )
    {
        $orderIDArray = $http->postVariable( 'OrderIDArray' );
        if ( $orderIDArray !== null )
        {
            $db = eZDB::instance();
            $db->begin();
            foreach ( $orderIDArray as $archiveID )
            {
                eZOrder::archiveOrder( $archiveID );
            }
            $db->commit();
         }
    }
}

if ( $http->hasPostVariable( 'SaveOrderStatusButton' ) )
{
    if ( $http->hasPostVariable( 'StatusList' ) )
    {
        foreach ( $http->postVariable( 'StatusList' ) as $orderID => $statusID )
        {
            $order = eZOrder::fetch( $orderID );
            $access = $order->canModifyStatus( $statusID );
            if ( $access and $order->attribute( 'status_id' ) != $statusID )
            {
                $order->modifyStatus( $statusID );
            }
        }
    }
}


/**
 * Prepare order report data
 */

$ordersCount = eZOrder::activeCount();

if( $http->getVariable( 'sSearch' ) )
{
    $ordersArray =  eZOrder::active( true, $offset, false, $sortField, $sortOrder );
}
else
{
    $ordersArray =  eZOrder::active( true, $offset, $limit, $sortField, $sortOrder );
}

$ordersArrayCount = count( $ordersArray );

$orders = array();

$locale = eZLocale::instance();

foreach( $ordersArray as $orderItem )
{
    $orderID = $orderItem->attribute( 'id' );
    $orderNR = $orderItem->attribute( 'order_nr' );
    $orderUser = $orderItem->user();
    $orderAccountName = $orderItem->attribute( 'account_name' );

    $orderUserContentObject = $orderUser->attribute( 'contentobject' );
    $orderUserContentObjectID = $orderUserContentObject->attribute( 'id' );
    $orderUserContentObjectDataMap = $orderUserContentObject->dataMap();
    $orderUserContentObjectAttributeAgency = $orderUserContentObjectDataMap['agency'];
    $orderUserContentObjectAttributeFirstNameContent = $orderUserContentObjectDataMap['first_name']->content();
    $orderUserContentObjectAttributeLastNameContent = $orderUserContentObjectDataMap['last_name']->content();
    $orderUserContentObjectAttributeEmailContent = $orderUserContentObjectDataMap['user_account']->content()->attribute( 'email' );

    if( $orderAccountName === null )
    {
        $orderAccountName = '(removed)';
    }

    if( $orderUserContentObjectAttributeAgency->hasContent() )
    {
        $agency = $orderUserContentObjectAttributeAgency->content();
    }
    else
    {
        $agency = 'n/a';
    }

    $orderTotalExVat = $orderItem->attribute( 'total_ex_vat' );
    $orderTotalIncVat = $orderItem->attribute( 'total_inc_vat' );

    $orderTotalExVatFormatted = $locale->formatCurrency( $orderTotalExVat, false );
    $orderTotalIncVatFormatted = $locale->formatCurrency( $orderTotalIncVat, false );

    $orderCreatedDateTime = $orderItem->attribute( 'created' );
    $orderCreatedDateTimeFormatted = $locale->formatShortTime( $orderCreatedDateTime );
    //$orderCreatedDateTimeFormatted = $locale->formatDateTimeType( '%m/%d/%Y %h:%i %a', $orderCreatedDateTime );

    $orderStatusModificationList = $orderItem->attribute( 'status_modification_list' );
    $orderStatusID = $orderItem->attribute( 'status_id' );

    foreach( $orderStatusModificationList as $orderStatusModificationListItem )
    {
        if( $orderStatusModificationListItem->attribute( 'status_id' ) == $orderStatusID )
        {
            $orderStatus = $orderStatusModificationListItem->attribute( 'name' );
        }
    }

    $orderFormCustomerLink = '<a href="/shop/customerorderview/' . $orderUserContentObjectID . '/' . $orderUserContentObjectAttributeEmailContent . '">' . $orderAccountName . '</a>';

    $orderFormOrderViewLink = '<a href="/shop/orderview/' . $orderNR . '">' . $orderNR .'</a>';

    $orderFormInputCheckbox = '<input class="no-sort" type="checkbox" name="OrderIDArray[]" value="' . $orderID . '" title="Select order for removal">';

    $orderFormInputSelect = '<select name="'. "StatusList[" . $orderID . ']">';
    foreach( $orderStatusModificationList as $statusItem )
    {
        $statusID = $statusItem->attribute( 'status_id' );
        $statusSelected = $statusID == $orderStatusID ? "selected='selected'" : "";
        $orderFormInputSelect .= '<option ' . $statusSelected . 'value="' . $statusID .'">' . $statusItem->attribute( 'name' ) . '</option>';
    }
    $orderFormInputSelect .= "</select>";

    if( $http->getVariable( 'sSearch' ) )
    {
        $needle = strtolower( $http->getVariable( 'sSearch' ) );
        $haystack = strtolower( $orderUserContentObjectAttributeFirstNameContent . ' ' . $orderUserContentObjectAttributeLastNameContent . ' ' . $agency . ' ' . $orderNR );

        if( strpos( $haystack, $needle ) === false )
        {
            continue;
        }
    }

    $orders[] = array( $orderFormInputCheckbox, $orderFormOrderViewLink, $orderFormCustomerLink, $agency, $orderTotalExVatFormatted, $orderTotalIncVatFormatted, $orderCreatedDateTimeFormatted, $orderStatus );
}

/**
 * Prepare order report results
 */

$orderResults = array( 'sEcho' => (int) $_REQUEST['sEcho'],
                       'iTotalRecords' => $ordersCount,
                       'iTotalDisplayRecords' => $ordersCount,
                       'aaDataCount' => count( $orders ),
                       'aaData' => $orders );

/**
 * Send order report data to client
 */

header( 'Content-Type: application/json' );

echo json_encode( $orderResults );

eZExecution::cleanExit();

?>