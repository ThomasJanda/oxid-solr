[{if $oView->showSearch()}]

    <script type="text/javascript">
        var source = '[{$oViewConf->getModuleUrl('rs-solr','Application/Controller/AutoSuggestController.php')}]';
        var text_category = '[{oxmultilang ident="rs_solr_suggest_categorie"}]';
        var text_category_main = '[{oxmultilang ident="rs_solr_suggest_categorie_main"}]';
        var text_manufacturer = '[{oxmultilang ident="rs_solr_suggest_manufacturer"}]';
    </script>
    
    [{oxscript include=$oViewConf->getModuleUrl('rs-solr','out/src/js/autosuggest.js')}]
    [{oxstyle include=$oViewConf->getModuleUrl('rs-solr','out/src/css/autosuggest.css')}]
    
[{/if}]