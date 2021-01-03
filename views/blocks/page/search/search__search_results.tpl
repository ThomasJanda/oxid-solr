[{if $oView->getArticleCount()}]
    [{$smarty.block.parent}]
[{else}]
    <div class="listRefine clear bottomRound">
        [{include file="widget/locator/listlocator.tpl" locator=$oView->getPageNavigationLimitedTop() listDisplayType=true itemsPerPage=true sort=true}]
    </div>
    [{$smarty.block.parent}]
    
    [{capture name="rsscript"}]
        $('#solrFilterAreaButtons button').click();
    [{/capture}]
    [{oxscript add=$smarty.capture.rsscript}]
[{/if}]
[{include file="rs/solr/views/tpl/page/search/search__search_results.tpl"}]