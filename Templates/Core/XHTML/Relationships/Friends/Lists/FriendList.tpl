<!--{foreach name=Friends from=$friendDTOArray item=currentEntry}-->
	<div class="friendItemContainer <!--{if $smarty.foreach.Friends.index % 2 == 0}-->gray<!--{/if}-->">
		<div id="ControlCenterCube_Relationships_Friend_ID_<!--{$currentEntry->getOtherProfileID()}-->" 
			class="friendItemName clickable" onclick="buildProfilePage('<!--{$currentEntry->getOtherProfileID()}-->')">
			<!--{$currentEntry->getOtherProfileName()}-->
		</div>
	</div>
<!--{/foreach}-->
