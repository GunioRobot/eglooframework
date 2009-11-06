<!--{strip}-->
<ul id="FridgeSortedList" class="sortable">
	<!--{foreach name=FridgeDTOLoop from=$fridgeCubes item=fridgeDTO}-->
		<li id="fridgeItemID_<!--{$fridgeDTO->getElementTypeID()}-->" class="fridgeLI dragdropsort moveable">

			<div><!--{$fridgeDTO->getCubeName()}--></div>

		</li> 

	<!--{/foreach}-->
</ul>
<!--{/strip}-->