{* Order Report View *}

{ezcss_require( array( 'datatables.css', 'ezp_datatables_override.css' ) )}

{* older datatables styles that look different than admin2 styles *}

{* ezcss_require( array( 'datatables.css', 'demo_table.css', 'demo_table_jui.css' ) ) *}

{ezscript_require( array( 'jquery.dataTables.min.js' ) )}

<form id="order-list-form" name="orderlist" method="post" action={concat( '/orderreport/reportlist', $view_parameters.offset|gt(0)|choose( '', concat( '/(offset)/', $view_parameters.offset ) ) )|ezurl}>
<div class="context-block">
    <div class="box-header">
        <div class="box-ml">
            <h1 class="context-title">Orders ({$order_count|l10n('number')|explode('.00')|implode('')})</h1>
            <div class="header-mainline"></div>
        </div>
    </div>
    <div class="box-content">
        <table class="datatable list" cellspacing="0">
        <colgroup>
            <col width="3%" />
            <col width="4%" />
            <col width="10%" />
            <col width="21%" />
            <col width="8%" />
            <col width="8%" />
            <col width="9%" />
            <col width="10%" />
        </colgroup>
        <thead>
            <tr>
                <th><img src={'toggle-button-16x16.gif'|ezimage} width="16" height="16" alt="{'Invert selection.'|i18n( 'design/admin/shop/orderlist' )}" title="{'Invert selection.'|i18n( 'design/admin/shop/orderlist' )}" onclick="ezjs_toggleCheckboxes( document.orderlist, 'OrderIDArray[]' ); return false;"/></th>
                <th class="dataTablesSortable" style="width: 10px;">{'ID'|i18n('design/admin/orderreport/orderreport')}</th>
                <th class="dataTablesSortable" style="width: 26px;">Customer</th>
                <th>Agency</th>
                <th>{'Total (ex. VAT)'|i18n('design/admin/orderreport/orderreport')}</th>
                <th>{'Total (inc. VAT)'|i18n('design/admin/orderreport/orderreport')}</th>
                <th class="dataTablesSortable" style="width: 16px;">{'Time'|i18n('design/admin/orderreport/orderreport')}</th>
                <th>{'Status'|i18n('design/admin/orderreport/orderreport')}</th>
            </tr>
        </thead>
        </table>
    </div>
</div>

   <div class="controlbar">
    {* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">

    <div class="block">
        <div class="button-left">
            <input class="button" id="archive-btn" type="submit" name="ArchiveButton" value="{'Archive selected'|i18n( 'design/admin/shop/orderlist' )}" title="{'Archive selected orders.'|i18n( 'design/admin/shop/orderlist' )}" />
        {*if $order_list}
            <input class="button" type="submit" name="ArchiveButton" value="{'Archive selected'|i18n( 'design/admin/shop/orderlist' )}" title="{'Archive selected orders.'|i18n( 'design/admin/shop/orderlist' )}" />
        {else}
            <input class="button-disabled" type="submit" name="ArchiveButton" value="{'Archive selected helllo'|i18n( 'design/admin/shop/orderlist' )}" disabled="disabled" />
        {/if*}
        </div>
        <div class="button-right">
            <input class="button" type="submit" name="SaveOrderStatusButton" value="{'Apply changes'|i18n( 'design/admin/shop/orderlist' )}" title="{'Click this button to store changes if you have modified any of the fields above.'|i18n( 'design/admin/shop/orderlist' )}" />
            {*if and( $order_list|count|gt( 0 ), $can_apply )}
            <input class="button" type="submit" name="SaveOrderStatusButton" value="{'Apply changes'|i18n( 'design/admin/shop/orderlist' )}" title="{'Click this button to store changes if you have modified any of the fields above.'|i18n( 'design/admin/shop/orderlist' )}" />
            {else}
            <input class="button-disabled" type="submit" name="SaveOrderStatusButton" value="{'Apply changes'|i18n( 'design/admin/shop/orderlist' )}" disabled="disabled" />
            {/if*}
        </div>
    </div>
    <div class="break"></div>
    {* DESIGN: Control bar END *}</div></div></div>

</form>

{literal}
<script type="text/javascript">
    jQuery(function(){
        jQuery('.datatable').dataTable({
            aoColumnDefs: [{ "bSortable": false, "aTargets": [ 0,3,4,5,7 ] }],
            aoColumns: [
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        { "sType": "date" },
                        null
                    ],
            aaSorting : [[ 0, 'desc' ]],
            bProcessing: true,
            bServerSide: true,
            sAjaxSource: '/orderreport/reportlist',
            sPaginationType: 'full_numbers',
{/literal}
{if $requestsSearch}
                        "oSearch": {literal}{"sSearch": "{/literal}{$requestssearch}{literal}"}{/literal},
{/if}
{if $requestoffset}
                        iDisplayStart: {$requestoffset},
{/if}
{if $requestlimit}
                        iDisplayLength: {$requestlimit},
{else}
            iDisplayLength: 25,
{/if}
{literal}
            aLengthMenu: [15, 25, 50, 75, 100, 150, 250, 350, 500, 750]
        });
    });
</script>
{/literal}