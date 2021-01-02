[{include file="headitem.tpl" title="" box=" "}]
[{oxscript include="js/libs/jquery.min.js"}]

<h1>Solr import</h1>

<form action="[{ $oViewConf->getSelfLink() }]" enctype="multipart/form-data" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="cl" value="[{$oViewConf->getActiveClassName()}]">
    <button type="submit" name="fnc" value="setup">Setup</button>
</form>
<br>
<br>


<form action="[{ $oViewConf->getSelfLink() }]" enctype="multipart/form-data" method="post">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="cl" value="[{$oViewConf->getActiveClassName()}]">
    <button type="submit" name="fnc" value="deleteAll">Delete all</button>
</form>
<br>
<br>


<form action="[{ $oViewConf->getSelfLink() }]" enctype="multipart/form-data" method="post" id="form_import">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="cl" value="[{$oViewConf->getActiveClassName()}]">
    <input type="hidden" name="fnc" value="import">
    [{if $continue}]
        Offset: [{$offset}]<br>
        <input type="hidden" name="type" value="[{$type}]">
        <input type="hidden" name="offset" value="[{$offset}]">
     [{else}]
        <button type="submit">Import</button>
    [{/if}]
</form>
[{if $continue}]
    <script>
        document.getElementById('form_import').submit();
    </script>
[{/if}]
<br>
<br>

[{include file="bottomitem.tpl"}]