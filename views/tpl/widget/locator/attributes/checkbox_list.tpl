[{*
parameter
name = oxcategories__oxtitle...
facet = array with all values, labels and counts
filter = array with selected filters
*}]

[{foreach from=$facet key=value item=data name=list}]
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
    <div class="form-check">
        <input type="checkbox" class="form-check-input" form="solrFilterForm" id="checkbox_list_[{$name}]_[{$smarty.foreach.list.index}]"  name="filter[[{$name}]][]" value="[{$value}]" [{if $selected}]checked selected[{/if}]> 
        <label class="form-check-label" for="checkbox_list_[{$name}]_[{$smarty.foreach.list.index}]">[{$label}] ([{$count}])</label>
    </div>
[{/foreach}]
