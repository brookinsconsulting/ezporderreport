{* Order Report View *}

{* ezcss_require( array( 'demo_table.css' ) ) *}

{ezcss_require( 'demo_table.css', 'demo_table_jui.css' )}

{* ezcss_require( array( 'demo_table.css','demo_table_jui.css' ) )}

{ezcss_require( 'demo_table_jui.css' ) *}

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
            <col width="4%" />
            <col width="10%" />
            <col width="21%" />
            <col width="8%" />
            <col width="8%" />
            <col width="9%" />
            <col width="15%" />
        </colgroup>
        <thead>
            <tr>
                <th class="dataTablesSortable">{'ID'|i18n('design/admin/orderreport/orderreport')}</th>
                <th>Customer</th>
                <th>Agency</th>
                <th>{'Total (ex. VAT)'|i18n('design/admin/orderreport/orderreport')}</th>
                <th>{'Total (inc. VAT)'|i18n('design/admin/orderreport/orderreport')}</th>
                <th class="dataTablesSortable">{'Time'|i18n('design/admin/orderreport/orderreport')}</th>
                <th>{'Status'|i18n('design/admin/orderreport/orderreport')}</th>
                <th class="tight">&nbsp;</th>
            </tr>
        </thead>
        </table>
    </div>
</div>
<script type="text/javascript" src={'javascript/jquery.dataTables.min.js'|ezdesign()}></script>
{literal}
<script type="text/javascript">
    jQuery(function(){
        jQuery('.datatable').dataTable({
            aoColumns: [
                        null,
                        null,
                        null,
                        null,
                        null,
                        { "sType": "date" },
                        null
                    ],
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
            aLengthMenu: [15, 25, 50, 75, 100, 250, 350, 500, 750]
        });
    });
</script>
{/literal}