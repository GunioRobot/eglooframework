<div id="ControlCenterCube_Relationships_FriendList">

<!--{foreach name=Friends from=$relationshipDTOArray item=currentEntry}-->
	<div class="friendItemContainer <!--{if $smarty.foreach.Friends.index % 2 == 0}-->gray<!--{/if}-->">
		<div id="ControlCenterCube_Relationships_Friend_ID_<!--{$currentEntry->get_profile_id()}-->" class="friendItemName">
		 	<a href="/internalMainPage/getInternalMainPage/&profileID=<!--{$currentEntry->get_profile_id()}-->" class="friendItemName clickable"><!--{$currentEntry->get_profilename()}--></a>
		</div>
	</div>
<!--{/foreach}-->

</div>