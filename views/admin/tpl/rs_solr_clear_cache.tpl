[{include file="headitem.tpl" title="GENERAL_ADMIN_TITLE"|oxmultilangassign box=" "}]

<h1>Cache files</h1>
<div style="margin-bottom:20px; ">
    Contain all files which created during using Solr search engine.
</div>

<div style="margin-bottom:20px; ">
    [{assign var=iCount value=$oView->getFileCount()}]
    [{$iCount}] requests cached
</div>

[{if $oView->hasFileCount()}]
    <div style="margin-bottom:20px; ">
        <form action="[{$oViewConf->getSelfLink()}]" method="post">
            [{$oViewConf->getHiddenSid()}]
            <input type="hidden" name="cl" value="rs_solr_clear_cache">
            <input type="hidden" name="fnc" value="deleteCacheFiles">

            <button class="button" type="submit">Clear cache</button>
        </form>
    </div>
[{/if}]

</body>
</html>