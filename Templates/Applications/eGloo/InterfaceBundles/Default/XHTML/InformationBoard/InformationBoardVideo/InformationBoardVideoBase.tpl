<div id="Center_1_Block">
    <!--{* Displays search navigation for the global/network/favorites/recommended filters *}-->
    <div id="Menu_1_Header12">
        <div id="Menu_Header_Break4">Suggest</div>
        <div id="Menu_Header_Break3">Favorite</div>
        <div id="Menu_Header_Break2">Network</div>
        <div id="Menu_Header_Break1">Global</div>            
    </div>
    <!--{* The overall containers for the top 30 *}-->
    <div id="Center_Sub_Column1"></div>

	<!--{counter name=columnCounter start=20 skip=-10 assign=resultSectionLowerDelim}-->

    <!--{section name=resultColumnLoopIndex loop=$resultColumns show=true}-->
        <div id="<!--{$resultColumns[resultColumnLoopIndex]->id}-->" class="<!--{$resultColumns[resultColumnLoopIndex]->style}-->">
		<!--{section name=resultColumnItemIndex loop=$resultColumnItemList start=$resultSectionLowerDelim step=1 max=10 show=true}-->
            		<div id="<!--{$resultColumnItemList[resultColumnItemIndex]->getID()}-->" class="top30">
		            	<div class="top30Number"><!--{$smarty.section.resultColumnItemIndex.index_next}-->:</div>
		            	<div class="top30Value"><!--{$resultColumnItemList[resultColumnItemIndex]->getName()}--></div>
            		</div>
		<!--{/section}-->
		</div>
		<!--{counter name=columnCounter}-->
    <!--{/section}-->

    <div id="DropDownContentSummary">
        Video News
    </div>
</div>
    <!--{* Divs containing navigation to search deeper into the 30 search results *}-->
<div id="DropDownBottomNavigation">
    <div id="DropDownBottomNavJoinRankedButton">Join eGloo's </div>
    <div id="DropDownBottomNavHelpButton">Help</div>
    <div id="DropDownBottomNavPrivacyButton">Privacy</div>
    <div id="DropDownBottomNavLegalButton">Legal</div>
    <div id="DropDownBottomNavArrowLeft">◀</div>
    <div id="DropDownBottomNavResultBounds">1 - 30</div>
    <div id="DropDownBottomNavArrowRight">▶</div>
</div>
