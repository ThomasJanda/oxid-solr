[{if $facets}]
    <div>
        <div id="solrFilterAreaButtons">
            <button type="button" data-toggle="collapse" data-target="#solrFilterArea" aria-expanded="false" aria-controls="solrFilterArea" class="btn btn-secondary">
                [{oxmultilang ident="rs_solr_button_filter_show"}] [{if $hasfilterset!==false}] ([{$hasfilterset}]) [{/if}]
            </button>
        </div>
        <div class="collapse" id="solrFilterArea">
            <form id="solrFilterForm" role="form" action="[{$oViewConf->getSelfActionLink()}]" method="get">
                [{$oViewConf->getHiddenSid()}]
                [{$oViewConf->getNavFormParams()}]
                [{$oViewConf->getHiddenSid()}]
                <input type="hidden" name="cl" value="[{$oViewConf->getTopActiveClassName()}]">
                <input type="hidden" name="pgNr" value="0">

                <div class="row filter-attributes">
                    [{foreach from=$facets key=name item=facet}]
                        [{*assign var=facet value=$oView->getSolrEnrichFacetData($name, $facet)*}]
                        <div class="col-12 col-md-6 col-lg-4 text-left">
                            <div class="solrFilterFacet [{$filtersettingstype.$name}] card">
                                <div class="card-body">
                                    <h5 class="solrFilterFacetTitle card-title">[{$oView->getSolrFilterSettingName($name)}]</h5>
                                    [{if $filtersettingstype.$name == "checkbox_list"}]
                                        [{include file='rs/solr/views/tpl/widget/locator/attributes/checkbox_list.tpl' name=$name facet=$facet filter=$filter}]
                                    [{/if}]
                                    [{if $filtersettingstype.$name == "selectbox"}]
                                        [{include file='rs/solr/views/tpl/widget/locator/attributes/selectbox.tpl' name=$name facet=$facet filter=$filter}]
                                    [{/if}]
                                    [{if $filtersettingstype.$name == "range_slider"}]
                                        [{include file='rs/solr/views/tpl/widget/locator/attributes/range_slider.tpl' name=$name facet=$facet filter=$filter}]
                                    [{/if}]
                                    [{if $filtersettingstype.$name == "range_slider_numeric"}]
                                        [{include file='rs/solr/views/tpl/widget/locator/attributes/range_slider_numeric.tpl' name=$name facet=$facet filter=$filter}]
                                    [{/if}]
                                    [{if $filtersettingstype.$name == "range_slider_currency"}]
                                        [{include file='rs/solr/views/tpl/widget/locator/attributes/range_slider_currency.tpl' name=$name facet=$facet filter=$filter}]
                                    [{/if}]
                                    [{if $filtersettingstype.$name == "custom_template"}]
                                        [{assign var=o value=$oView->getSolrFilterSetting($name)}]
                                        [{assign var=template value=$o->rssolr_facets_categories__rscustom->value}]
                                        [{include file='widget/locator/attributes/'|cat:$template name=$name facet=$facet filter=$filter}]
                                    [{/if}]
                                </div>
                            </div>
                        </div>
                    [{/foreach}]
                </div>
                <div class="row"> 
                    <div class="col">
                        <button type="submit" form="solrFilterForm" class="btn btn-primary">
                            [{oxmultilang ident="rs_solr_button_filter_execute"}]
                        </button>
                    </div>
                    <div class="col">
                        <button type="submit" name="filterclear" value="1" form="solrFilterForm" class="btn btn-secondary">
                            [{oxmultilang ident="rs_solr_button_filter_delete_all"}]
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
[{/if}]