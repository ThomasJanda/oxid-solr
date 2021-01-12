[{include file="headitem.tpl" title="" box=" "}]
[{oxscript include="js/libs/jquery.min.js"}]

<h1>Solr import</h1>

<div>
    Create columns in Solr. Require one time before the first import.
</div>
<form action="[{ $oViewConf->getSelfLink() }]" enctype="multipart/form-data" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="cl" value="[{$oViewConf->getActiveClassName()}]">
    <button type="submit" name="fnc" value="setup">Setup</button>
</form>
<br>
<br>

<div>
    Delete all products in solr. Be carefull with this command.
</div>
<form action="[{ $oViewConf->getSelfLink() }]" enctype="multipart/form-data" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="cl" value="[{$oViewConf->getActiveClassName()}]">
    <button type="submit" name="fnc" value="deleteAll">Delete all</button>
</form>
<br>
<br>

<div>
    Import all products to solr. Only one time required.
</div>
<form action="[{ $oViewConf->getSelfLink() }]" enctype="multipart/form-data" method="post" id="form_import">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="cl" value="[{$oViewConf->getActiveClassName()}]">
    <input type="hidden" name="fnc" value="import">
    <input type="hidden" name="importoxid" value="[{$importOxid}]">
    [{if $continueInsert}]
        [{if $startDatetime}]Start: [{$startDatetime}]<br>[{/if}]
        [{if $endDatetime}]End: [{$endDatetime}]<br>[{/if}]
        Offset: [{$offset}]<br>
        <input type="hidden" name="startdatetime" value="[{$startDatetime}]">
        <input type="hidden" name="type" value="[{$type}]">
        <input type="hidden" name="offset" value="[{$offset}]">
     [{else}]
        <button type="submit">Full import</button>
    [{/if}]
</form>
[{if $continueInsert}]
    <script>
        document.getElementById('form_import').submit();
    </script>
[{/if}]
<br>
<br>

<div>
    Import/update/delete only changed products since the last update.
</div>
<form action="[{ $oViewConf->getSelfLink() }]" enctype="multipart/form-data" method="post" id="form_update">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="cl" value="[{$oViewConf->getActiveClassName()}]">
    <input type="hidden" name="fnc" value="update">
    <input type="hidden" name="updateoxid" value="[{$importOxid}]">
    [{if $continueUpdate}]
        [{if $startDatetime}]Start: [{$startDatetime}]<br>[{/if}]
        [{if $endDatetime}]End: [{$endDatetime}]<br>[{/if}]
        <input type="hidden" name="startdatetime" value="[{$startDatetime}]">
    [{else}]
        <button type="submit">Delta update</button>
    [{/if}]
</form>
[{if $continueUpdate}]
    <script>
        document.getElementById('form_update').submit();
    </script>
[{/if}]
<br>
<br>

[{include file="bottomitem.tpl"}]