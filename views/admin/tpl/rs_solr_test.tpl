[{include file="headitem.tpl" title="" box=" "}]

[{* slider *}]
[{oxstyle include="https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.1/css/ion.rangeSlider.min.css"}]
[{oxscript include="js/libs/jquery.min.js"}]
[{oxscript include="https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.1/js/ion.rangeSlider.min.js"}]

[{oxstyle}]

<h2>Search</h2>
<form action="[{ $oViewConf->getSelfLink() }]" enctype="multipart/form-data" method="post" id="form_search">
    [{$oViewConf->getHiddenSid()}]
    <input type="hidden" name="cl" value="[{$oViewConf->getActiveClassName()}]">
    <input type="hidden" name="fnc" value="search">
    
    <table>
        <tr>
            <td valign="top">
                <table>
                    <tr>
                        <td>Suchbegriff</td>
                        <td><input type="search" class="editinput" name="phrase" value="[{$phrase}]"></td>
                    </tr>
                    <tr>
                        <td>Kategorie/Hersteller</td>
                        <td>
                            <select class="editinput" name="art_category">
                                <option value="">Startseite</option>
                                <optgroup label="[{oxmultilang ident="GENERAL_CATEGORY"}]">
                                    [{foreach from=$cattree->aList item=pcat}]
                                        <option value="cat@@[{$pcat->oxcategories__oxid->value}]" [{if $pcat->selected}]SELECTED[{/if}]>[{$pcat->oxcategories__oxtitle->getRawValue()}]</option>
                                    [{/foreach}]
                                </optgroup>
                                <optgroup label="[{oxmultilang ident="GENERAL_MANUFACTURER"}]">
                                    [{foreach from=$mnftree item=pmnf}]
                                        <option value="mnf@@[{$pmnf->oxmanufacturers__oxid->value}]" [{if $pmnf->selected}]SELECTED[{/if}]>[{$pmnf->oxmanufacturers__oxtitle->value}]</option>
                                    [{/foreach}]
                                </optgroup>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Anzahl Produkte</td>
                        <td><input type="text" class="editinput" name="rows" value="[{$rows}]"></td>
                    </tr>
                    <tr>
                        <td>Sortierung</td>
                        <td>
                            <select class="editinput" name="sort">
                                <option value="">Score ASC</option>
                                <option value="">Score DESC</option>
                                [{*
                                [{assign var=value value="relevance@@asc"}]<option value="[{$value}]" [{if $sort==$value}] selected [{/if}]>Relevanz ASC</option>
                                [{assign var=value value="relevance@@desc"}]<option value="[{$value}]" [{if $sort==$value}] selected [{/if}]>Relevanz DESC</option>
                                *}]
                                [{assign var=value value="title@@asc"}]<option value="[{$value}]" [{if $sort==$value}] selected [{/if}]>Titel ASC</option>
                                [{assign var=value value="title@@desc"}]<option value="[{$value}]" [{if $sort==$value}] selected [{/if}]>Titel DESC</option>
                                [{assign var=value value="price@@asc"}]<option value="[{$value}]" [{if $sort==$value}] selected [{/if}]>Preis ASC</option>
                                [{assign var=value value="price@@desc"}]<option value="[{$value}]" [{if $sort==$value}] selected [{/if}]>Preis DESC</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <button class="editinput" type="submit">Search</button><br>
                        </td>
                    </tr>
                </table>
            </td>
            <td valign="top">
                <table>
                    [{assign var=fs value=$oView->getFilterSettings()}]
                    [{foreach from=$fs key=key item=value name=list}]
                        <tr>
                            <td>
                                [{assign var=selected value=$key|in_array:$filtersettings}]
                                <input type="checkbox" name="filtersettings[[{$smarty.foreach.list.index}]]" value="[{$key}]" [{if $selected}]checked selected[{/if}]> [{$value}]<br>
                            </td>
                            <td>
                                <select name="filtersettingstype[[{$key}]]">
                                    <option value="checkbox_list" [{if $filtersettingstype.$key == "checkbox_list"}] checked selected [{/if}]>Checkbox list</option>
                                    <option value="selectbox" [{if $filtersettingstype.$key == "selectbox"}] checked selected [{/if}]>Selectbox</option>
                                    <option value="rangeslider" [{if $filtersettingstype.$key == "rangeslider"}] checked selected [{/if}]>Range slider</option>
                                    <option value="rangeslider_num" [{if $filtersettingstype.$key == "rangeslider_num"}] checked selected [{/if}]>Range slider numbers</option>
                                    <option value="rangeslider_currency" [{if $filtersettingstype.$key == "rangeslider_currency"}] checked selected [{/if}]>Range slider currency</option>
                                </select> 
                            </td>
                        </tr>
                    [{/foreach}]
                </table>

                    
                
            </td>
        </tr>
    </table> 
    

                
    [{if $resultQuery}]
        <div style="margin:10px; ">
            Search Query: [{$resultQuery}]
        </div>
    [{/if}]
    [{if $error}]
        <div style="margin:10px; ">
            <pre>[{$error}]</pre>
        </div> 
    [{/if}]
    [{if $result}]
        <table width="100%">
            <tr>
                <b>Facets</b>
                [{if $facets}]
                    <td valign="top" width="200">

                        <table border="1" cellspacing="0" width="100%">
                            [{foreach from=$facets key=name item=facet}]
                                <tr>
                                    <td valign="top">
                                        <b>[{$oView->getFilterSettingName($name)}]</b><br>
                                        
                                        [{if $filtersettingstype.$name == "checkbox_list"}]
                                        
                                            [{foreach from=$facet key=value item=data}]
                                                [{assign var=count value=$data.count}]
                                                [{assign var=label value=$data.label}]
                                                [{assign var=selected value=false}]
                                                [{if $filter}]
                                                    [{foreach from=$filter key=filter_name item=filter_values}]
                                                        [{foreach from=$filter_values item=filter_value}]
                                                            [{if $filter_name==$name && $filter_value==$value}]
                                                                [{assign var=selected value=true}]
                                                            [{/if}]
                                                        [{/foreach}]
                                                    [{/foreach}]
                                                [{/if}]
                                                <input type="checkbox" name="filter[[{$name}]][]" value="[{$value}]" [{if $selected}]checked selected[{/if}]> 
                                                [{$label}] ([{$count}])
                                                <br>
                                            [{/foreach}]
                                        
                                        [{/if}]
                                        
                                        [{if $filtersettingstype.$name == "selectbox"}]
                                            <select name="filter[[{$name}]][]">
                                                <option></option>
                                                [{foreach from=$facet key=value item=data}]
                                                    [{assign var=count value=$data.count}]
                                                    [{assign var=label value=$data.label}]
                                                    [{assign var=selected value=false}]
                                                    [{if $filter}]
                                                        [{foreach from=$filter key=filter_name item=filter_values}]
                                                            [{foreach from=$filter_values item=filter_value}]
                                                                [{if $filter_name==$name && $filter_value==$value}]
                                                                    [{assign var=selected value=true}]
                                                                [{/if}]
                                                            [{/foreach}]
                                                        [{/foreach}]
                                                    [{/if}]
                                                    [{assign var=o value=$oView->getObject($name, $value)}]
                                                    <option value="[{$value}]" [{if $selected}]checked selected[{/if}]> [{$label}] ([{$count}])</option>
                                                [{/foreach}]
                                            </select>

                                        [{/if}]
                                        
                                                                                
                                        [{if $filtersettingstype.$name == "rangeslider" || $filtersettingstype.$name == "rangeslider_num" || $filtersettingstype.$name == "rangeslider_currency"}]
                                                [{assign var=id value="rs_solr_range_slider_"|cat:$name|md5}]

                                                [{assign var=jsvalues value=""}]
                                                [{assign var=jsvaluemin value=""}]
                                                [{assign var=jsvaluemax value=""}]
                                                [{assign var=jsvalueminselected value=null}]
                                                [{assign var=jsvaluemaxselected value=null}]
                                                [{foreach from=$facet key=value item=data name=list}]
                                                    [{assign var=count value=$data.count}]
                                                    [{assign var=label value=$data.label}]
                                                    [{if $filter}]
                                                        [{foreach from=$filter key=filter_name item=filter_values}]
                                                            [{foreach from=$filter_values item=filter_value}]
                                                                [{if $filter_name==$name && $filter_value==$value}]
                                                                    [{if $jsvalueminselected==null}] [{assign var=jsvalueminselected value=$value}] [{/if}]
                                                                    [{assign var=jsvaluemaxselected value=$value}]
                                                                [{/if}]
                                                            [{/foreach}]
                                                        [{/foreach}]
                                                    [{/if}]
                                                    
                                                    [{if !$smarty.foreach.list.first}]
                                                        [{assign var=jsvalues value=$jsvalues|cat:","}]
                                                    [{/if}]
                                                    [{assign var=jsvalues value=$jsvalues|cat:"'"|cat:$value|cat:"'"}]

                                                    [{if $smarty.foreach.list.first}][{assign var=jsvaluemin value=$value}][{/if}]
                                                    [{if $smarty.foreach.list.last}][{assign var=jsvaluemax value=$value}][{/if}]

                                                [{/foreach}]
                                                
                                                [{if $jsvalueminselected!=null}] [{assign var=jsvaluemin value=$jsvalueminselected}] [{/if}]
                                                [{if $jsvaluemaxselected!=null}] [{assign var=jsvaluemax value=$jsvaluemaxselected}] [{/if}]
                                                
                                                <div style='padding:10px; '>
                                                    <div id="[{$id}]"></div>
                                                </div>
                                                <div id='values_[{$id}]'>
                                                    <input type='hidden' id='min_[{$id}]' name="" value='[{$jsvaluemin}]'>
                                                    <input type='hidden' id="max_[{$id}]" name="filter[[{$name}]][max]" value='[{$jsvaluemax}]'>
                                                </div>
                                                
                                                [{capture name="rsscript"}]
                                                    [{capture}]<script>[{/capture}]
                                                    var custom_values_[{$id}] = [[{$jsvalues}]];
                                                    var value_from_[{$id}] = custom_values_[{$id}].indexOf('[{$jsvaluemin}]');
                                                    var value_to_[{$id}] = custom_values_[{$id}].indexOf('[{$jsvaluemax}]');

                                                    $("#[{$id}]").ionRangeSlider({
                                                        type: "double",
                                                        grid: true,
                                                        from: value_from_[{$id}],
                                                        to: value_to_[{$id}],
                                                        values: custom_values_[{$id}],
                                                        prettify: range_slider_pretty_[{$id}],
                                                        onStart: function(data) {
                                                            range_slider_hidden_[{$id}](data);
                                                        },
                                                        onChange: function (data) {
                                                            range_slider_hidden_[{$id}](data);
                                                        }
                                                    });
                                                    function range_slider_pretty_[{$id}](n)
                                                    {
                                                        [{if $filtersettingstype.$name == "rangeslider_currency"}]
                                                            [{assign var="currency" value=$oViewConf->getActCurrency()}]
                                                            n = '[{$currency.sign}] ' + (Math.round(n * 100) / 100).toFixed(2);
                                                        [{elseif $filtersettingstype.$name == "rangeslider_num"}]
                                                            n = (Math.round(n * 100) / 100).toFixed(2);
                                                        [{/if}]
                                                        return n;
                                                    }
                                                    function range_slider_hidden_[{$id}](data)
                                                    {
                                                        /* remove all hidden */
                                                        var div = document.getElementById("values_[{$id}]"); 
                                                        while (div.firstChild) {
                                                          div.removeChild(div.lastChild);
                                                        }
                                                        
                                                        if(data.min!==data.from || data.max!==data.to)
                                                        {
                                                            /* create new fields */
                                                            for (var i = 0; i < custom_values_[{$id}].length; i++) {
                                                                if(i >= data.from && i <= data.to)
                                                                {
                                                                    var input = document.createElement("input");
                                                                    input.setAttribute("type", "hidden");
                                                                    input.setAttribute("name", "filter[[{$name}]][]");
                                                                    input.setAttribute("value", custom_values_[{$id}][i]);
                                                                    document.getElementById("values_[{$id}]").appendChild(input);
                                                                }
                                                            }

                                                        }

                                                        /*document.getElementById('min_[{$id}]').value = from;
                                                        document.getElementById('max_[{$id}]').value = to;
                                                        */
                                                    }
                                                    [{capture}]</script>[{/capture}]
                                                [{/capture}]
                                                [{oxscript add=$smarty.capture.rsscript}]
                                        [{/if}]
                                    </td>
                                </tr>
                            [{/foreach}]
                        </table>
                        
                    </td>
                [{/if}]
                <td valign="top">
                    
                    <b>Count search result [{$found}] / Pages [{$pages}]</b>
                    <table border="1" cellspacing="0" width="100%">
                        [{assign var=name value="oxarticles__oxid"}]
                        [{foreach from=$result item=value}]
                            <tr>
                                [{assign var=product value=$oView->getObject($name, $value)}]
                                [{if $product}]
                                    <td>
                                        [{$product->oxarticles__oxartnum->value}]
                                    </td>
                                    <td>
                                        [{$product->oxarticles__oxtitle->value}] [{$product->oxarticles__oxvarselect->value}]
                                    </td>
                                    <td align="right">
                                        [{if $product->getFPrice()}]
                                            <span class="lead text-nowrap">
                                            [{if $product->isRangePrice()}]
                                                ab
                                                [{if !$product->isParentNotBuyable()}]
                                                    [{$product->getFMinPrice()}]
                                                [{else}]
                                                    [{$product->getFVarMinPrice()}]
                                                [{/if}]
                                            [{else}]
                                                [{if !$product->isParentNotBuyable()}]
                                                    [{$product->getFPrice()}]
                                                [{else}]
                                                    [{$product->getFVarMinPrice()}]
                                                [{/if}]
                                            [{/if}]
                                            [{assign var="currency" value=$oViewConf->getActCurrency()}]
                                            [{$currency->sign}]
                                        </span>
                                        [{/if}]
                                    </td>
                                [{else}]
                                    <td colspan="2">[{$value}]</td>
                                [{/if}]
                            </tr>
                        [{/foreach}]                        
                    </table>
                    
                </td>
            </tr>
        </table>
      
    [{/if}]
</form>
<br><br>

[{include file="bottomitem.tpl"}]