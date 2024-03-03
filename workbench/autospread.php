<?php
require_once 'session.php';
require_once 'shared.php';
require_once 'header.php';
?>

<h2>pages</h2>

<ul>
    <li><a href="">relationship docman</a></li>
    <li><a href="">universal docman</a></li>
    <li><a href="">spreads</a></li>
    <li><a href="">dashboard</a></li>
</ul>

<h2>types</h2>

<table class="table">
    <tr>
        <td colspan="3"><h3>Automated Spreading</h3></td>
    </tr>
    <tr>
        <td>Document Version</td>
        <td><a href="/describe.php?default_object=ncinoocr__DR_Document_Version__c">SObject</a></td>
        <td><a href="/query.php?QB_object_sel=ncinoocr__DR_Document_Version__c">SOQL</a></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
    </tr>
</table>

<table class="table">
    <tr>
        <td colspan="3"><h3>Spreads</h3></td>
    </tr>
    <tr>
        <td>Bundle/Template</td>
        <td><a href="/describe.php?default_object=LLC_BI__Underwriting_Bundle__c">SObject</a></td>
        <td><a href="/query.php?QB_object_sel=LLC_BI__Underwriting_Bundle__c">SOQL</a></td>
    </tr>
    <tr>
        <td>Period</td>
        <td><a href="/describe.php?default_object=LLC_BI__Spreads_Period__c">SObject</a></td>
        <td><a href="/query.php?QB_object_sel=LLC_BI__Spreads_Period__c">SOQL</a></td>
    </tr>
    <tr>
        <td>Statement Template</td>
        <td><a href="/describe.php?default_object=LLC_BI__Spread_Statement_Type__c">SObject</a></td>
        <td><a href="/query.php?QB_object_sel=LLC_BI__Spread_Statement_Type__c">SOQL</a></td>
    </tr>
    <tr>
        <td>Statement Record</td>
        <td><a href="/describe.php?default_object=LLC_BI__Spread_Statement_Record__c">SObject</a></td>
        <td><a href="/query.php?QB_object_sel=LLC_BI__Spread_Statement_Record__c">SOQL</a></td>
    </tr>
    <tr>
        <td></td>
    </tr>
    <tr>
        <td>Schedule</td>
        <td><a href="/describe.php?default_object=LLC_BI__Schedule__c">SObject</a></td>
        <td><a href="/query.php?QB_object_sel=LLC_BI__Schedule__c">SOQL</a></td>
    </tr>
    <tr>
        <td>Schedule Section</td>
        <td><a href="/describe.php?default_object=LLC_BI__Schedule_Section__c">SObject</a></td>
        <td><a href="/query.php?QB_object_sel=LLC_BI__Schedule_Section__c">SOQL</a></td>
    </tr>
</table>

<table class="table">
    <tr>
        <td colspan="3"><h3>Document Manager</h3></td>
    </tr>
    <tr>
        <td>Placeholder (Relationship)</td>
        <td><a href="/describe.php?default_object=LLC_BI__AccountDocument__c">SObject</a></td>
        <td><a href="/query.php?QB_object_sel=LLC_BI__AccountDocument__c">SOQL</a></td>
    </tr>
    <tr>
        <td>Placeholder (Universal/Collateral)</td>
        <td><a href="/describe.php?default_object=LLC_BI__Document_Placeholder__c">SObject</a></td>
        <td><a href="/query.php?QB_object_sel=LLC_BI__Document_Placeholder__c">SOQL</a></td>
    </tr>
</table>

<?php
require_once 'footer.php';
?>
