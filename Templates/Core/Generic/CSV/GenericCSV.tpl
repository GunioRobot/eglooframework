{strip}
{foreach from=$data_rows item=data_row key=row_key}
{strip}{foreach from=$data_row item=data_cell key=cell_key}
{$data_cell},
{/foreach} {/strip}
{/foreach}
{/strip}