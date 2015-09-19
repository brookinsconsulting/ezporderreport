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


$offset = (isset($_REQUEST['iDisplayStart'])) ? $_REQUEST['iDisplayStart'] : 0;
$limit = (isset($_REQUEST['iDisplayLength'])) ? $_REQUEST['iDisplayLength'] : 15;
$sSearch = (isset($_REQUEST['sSearch'])) ? $_REQUEST['sSearch'] : false;

$sortCol = (isset($_REQUEST['iSortCol_0'])) ? $_REQUEST['iSortCol_0'] : false;
$sortDir = (isset($_REQUEST['sSortDir_0'])) ? $_REQUEST['sSortDir_0'] : false;

if( $sortCol == 0 )
{
    if( eZPreferences::value( 'admin_orderlist_sortfield' ) )
    {
        $sortField = eZPreferences::value( 'admin_orderlist_sortfield' );
    }

    if ( !isset( $sortField ) || ( ( $sortField != 'created' ) && ( $sortField!= 'user_name' ) ) )
    {
        $sortField = 'created';
    }
}
else
{
    switch( $sortCol )
    {
        case 0:
            $sortField = 'id';
            break;
        case 1:
            $sortField = 'user_name';
            break;
        case 2:
            $sortField = 'adgency';
            break;
        case 3:
            $sortField = 'totalexvat';
            break;
        case 4:
            $sortField = 'totalinvvat';
            break;
        case 5:
            $sortField = 'created';
            break;
        case 5:
            $sortField = 'status';
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



/**
* Default class instances
*/

/** Parse HTTP POST variables **/
$http = eZHTTPTool::instance();

/** Access system variables **/
// $sys = eZSys::instance();

/** Init template behaviors **/
// $tpl = eZTemplate::factory();

/** Access ini variables **/
$ini = eZINI::instance();
$iniOrderreport = eZINI::instance( 'ezporderreport.ini' );

/** Report file variables **/
$storageDirectory = eZSys::cacheDirectory();
$contentTreeContentCsvReportName = 'ezporderreport';
$contentTreeContentCsvReportFileName = $contentTreeContentCsvReportName;
$contentTreeContentCsvReportFileNameWithExtension = $contentTreeContentCsvReportName . '.csv';
$contentTreeContentCsvReportFileNameWithExtensionFullPath = $storageDirectory . '/' . $contentTreeContentCsvReportFileNameWithExtension;

/** Default variables **/
$siteNodeUrlHostname = $ini->variable( 'SiteSettings', 'SiteURL' );
$adminSiteAccessName = $iniOrderreport->variable( 'eZPOrderReportSettings', 'AdminSiteAccessName' );
$currentSiteAccessName = $GLOBALS['eZCurrentAccess']['name'];


/**
 * Prepare order report data
 */

$ordersCount = eZOrder::activeCount();
$ordersArray =  eZOrder::active( true, $offset, $limit, $sortField, $sortOrder );
$orders = array();

foreach( $ordersArray as $orderItem )
{
    $orderID = $orderItem->attribute( 'id' );
    $orderAccountName = $orderItem->attribute( 'account_name' );

    $orderUser = $orderItem->user();
    $orderUserContentObjectID = $orderUser->attribute( 'contentobject_id' );

    $agencyContentObject = eZContentObject::fetch( $orderUserContentObjectID );
    $agencyContentObjectDataMap = $agencyContentObject->dataMap();
    $agencyContentObjectAttribute = $agencyContentObjectDataMap['agency'];

    if( $orderAccountName === null )
    {
        $orderAccountName = '(removed)';
    }

    if( $agencyContentObjectAttribute->hasContent() )
    {
        $agency = $agencyContentObjectAttribute->content();
    }
    else
    {
        $agency = 'n/a';
    }

    $locale = eZLocale::instance();

    $orderTotalExVat = $orderItem->attribute( 'total_ex_vat' );
    $orderTotalIncVat = $orderItem->attribute( 'total_inc_vat' );

    $orderTotalExVatFormatted = $locale->formatCurrency( $orderTotalExVat, false );
    $orderTotalIncVatFormatted = $locale->formatCurrency( $orderTotalIncVat, false );

/*
    <td class="number" align="right">{$Orders.item.total_ex_vat|l10n( 'currency', $locale, $symbol )}</td>
    <td class="number" align="right">{$Orders.item.total_inc_vat|l10n( 'currency', $locale, $symbol )}</td>
*/

    $orderCreatedDateTime = $orderItem->attribute( 'created' );
    $orderCreatedDateTimeFormatted = $locale->formatShortTime( $orderCreatedDateTime );

    $orderStatusModificationList = $orderItem->attribute( 'status_modification_list' );
    $orderStatusID = $orderItem->attribute( 'status_id' );

    foreach( $orderStatusModificationList as $orderStatusModificationListItem )
    {
        if( $orderStatusModificationListItem->attribute( 'status_id' ) == $orderStatusID )
        {
            $orderStatus = $orderStatusModificationListItem->attribute( 'name' );
        }
    }

/*
    <td>{$Orders.item.created|l10n( shortdatetime )}</td>
    <td>
    {let order_status_list=$Orders.status_modification_list}

    {section show=$order_status_list|count|gt( 0 )}
        {set can_apply=true()}
        <select name="StatusList[{$Orders.item.id}]">
        {section var=Status loop=$order_status_list}
            <option value="{$Status.item.status_id}"
                {if eq( $Status.item.status_id, $Orders.item.status_id )}selected="selected"{/if}>
                {$Status.item.name|wash}</option>
        {/section}
        </select>
    {section-else}
        {* Lets just show the name if we don't have access to change the status *}
        {$Orders.status_name|wash}
    {/section}
*/

    $orders[] = array( $orderID, $orderAccountName, $agency, $orderTotalExVatFormatted, $orderTotalIncVatFormatted, $orderCreatedDateTimeFormatted, $orderStatus );
}

/**
 * Send order report data to client
 */

header( 'Content-Type: application/json' );

echo json_encode( array( 'sEcho' => (int) $_REQUEST['sEcho'],
                         'iTotalRecords' => $ordersCount,
                         'iTotalDisplayRecords' => $ordersCount,
                         'aaDataCount' => count( $orders ),
                         'aaData' => $orders ) );

eZExecution::cleanExit();

?>