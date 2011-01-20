<div id="MessageList">

<!--{foreach name=Messages from=$blogDTOArray item=currentEntry}-->
	<div class="messageItemContainer <!--{if $smarty.foreach.Messages.index % 2 == 0}-->gray<!--{/if}-->">
		<div class="messageListTitle clickable"><!--{$currentEntry->getTitle()}--></div>
		<div class="messageListEdit clickable">
			<a href="/blog/editBlogEntry/&height=400&width=475&blogID=<!--{$currentEntry->getBlogID()}-->" class="thickbox" title="Edit Blog Entry">edit</a>
		</div>
		<div class="messageListSummary"><!--{$currentEntry->getContent()}--></div>
	</div>
<!--{/foreach}-->

</div>