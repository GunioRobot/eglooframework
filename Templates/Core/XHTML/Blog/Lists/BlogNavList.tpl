		
			<!--{if $eas_MainProfileID === $eas_ViewingProfileID}-->
				<h4 class="date">BLOG ARCHIVE</h4>
				<div id="blogNavBackground"></div>
				<div id="blogNav">	
					<a id="blog" href="/blog/createBlogForm/">Create New Blog</a>
			<!--{else}-->
				<h4 class="date">BLOG ARCHIVE</h4>
				<div id="blogNavBackground"></div>
				<div id="blogNav">	
			<!--{/if}-->
			
			<ul id="mainUL">
			
			<!--{foreach from=$blogListArray item=yearEntries key=yearEntriesKey}-->
				
				<li class="archive"><!--{$yearEntriesKey}--></li>
				
				<!--{if $yearEntriesKey eq $fullBlogYear}-->
				    <ul id="<!--{$yearEntriesKey}-->" style="overflow: visible; opacity: 0.9999; display: block;">
				<!--{else}-->
				    <ul id="<!--{$yearEntriesKey}-->">
				<!--{/if}-->
				
				<!--{foreach from=$yearEntries item=monthEntries key=monthEntriesKey}-->
					
					<li class="archive"><!--{$monthEntriesKey}--></li>
					
				<!--{if $monthEntriesKey eq $fullBlogMonth}-->
					<ul id="<!--{$monthEntriesKey}-->" style="overflow: visible; opacity: 0.9999; display: block;">
				<!--{else}-->
					<ul id="<!--{$monthEntriesKey}-->" class="blogs">
				<!--{/if}-->
				
					<!--{foreach from=$monthEntries item=blogEntry key=blogEntryKey}-->
						<li><a href="/blog/viewBlog/&blogID=<!--{$blogEntryKey}-->"><!--{$blogEntry}--></a></li>
					<!--{/foreach}-->	
					</ul>
					
				<!--{/foreach}-->
				</ul>
			<!--{/foreach}-->
			</ul>
		</div>