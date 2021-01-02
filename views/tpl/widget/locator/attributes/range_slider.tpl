[{*
parameter
name = oxcategories__oxtitle...
facet = array with all values, labels and counts
filter = array with selected filters
filtertype = default range_slider
*}]

[{* range_slider range_slider_numeric range_slider_currency *}]
[{assign var=filtertype value=$filtertype|default:"range_slider"}]

[{oxstyle include="https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.1/css/ion.rangeSlider.min.css"}]
[{*oxscript include="js/libs/jquery.min.js"*}]
[{oxscript include="https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.1/js/ion.rangeSlider.min.js"}]

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
        [{if $filtertype == "range_slider_currency"}]
            [{assign var="currency" value=$oViewConf->getActCurrency()}]
            n = '[{$currency.sign}] ' + (Math.round(n * 100) / 100).toFixed(2);
        [{elseif $filtertype == "range_slider_numeric"}]
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
                    input.setAttribute("form", "solrFilterForm");
                    input.setAttribute("name", "filter[[{$name}]][]");
                    input.setAttribute("value", custom_values_[{$id}][i]);
                    document.getElementById("values_[{$id}]").appendChild(input);
                }
            }
        }
    }
    [{capture}]</script>[{/capture}]
[{/capture}]
[{oxscript add=$smarty.capture.rsscript}]

