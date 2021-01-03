[{*
parameter
name
facet
filter
*}]

<select name="filter[[{$name}]][]" form="solrFilterForm">
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
        <option value="[{$value}]" [{if $selected}]checked selected[{/if}]> [{$label}] ([{$count}])</option>
    [{/foreach}]
</select>
