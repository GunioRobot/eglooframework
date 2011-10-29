<div id="UserRelationshipFriendIncomingRequestsContainer">

	<div id="UserRelationshipFriendIncomingRequestsList">
		<!--{foreach name=FriendshipRequests from=$relationshipRequestsDTOArray item=currentEntry}-->
			<div id="friendshipRequestItemContainer_<!--{$currentEntry->get_profile_id()}-->_<!--{$currentEntry->get_relationshiptype()}-->"
				 class="friendshipRequestItemContainer <!--{if $smarty.foreach.FriendshipRequests.index % 2 == 0}-->gray<!--{/if}-->">
				<div class="friendshipRequestListTitle">
					 <a href="/internalMainPage/getInternalMainPage/&profileID=<!--{$currentEntry->get_profile_id()}-->" class="friendItemName clickable"><!--{$currentEntry->get_profilename()}--></a>
				</div>
				<div class="friendshipRequestReject clickable"
					 onclick="rejectFriendship('<!--{$currentEntry->get_profile_id()}-->', '<!--{$currentEntry->get_relationshiptype()}-->');
					          $('#friendshipRequestItemContainer_<!--{$currentEntry->get_profile_id()}-->_<!--{$currentEntry->get_relationshiptype()}-->').fadeOut(1000).remove();
					          appState['numberAlerts'] -= 1;
					          $('#alertButton').html('Alerts ('+appState['numberAlerts']+')');">
					reject
				</div>
				<div class="friendshipRequestAccept clickable"
					 onclick="acceptFriendship('<!--{$currentEntry->get_profile_id()}-->', '<!--{$currentEntry->get_relationshiptype()}-->');
					          $('#friendshipRequestItemContainer_<!--{$currentEntry->get_profile_id()}-->_<!--{$currentEntry->get_relationshiptype()}-->').fadeOut(1000).remove();
					          appState['numberAlerts'] -= 1;
					          $('#alertButton').html('Alerts ('+appState['numberAlerts']+')');">
					accept
				</div>
			</div>
		<!--{/foreach}-->
	</div>

</div>