	
	<!--{foreach from=$blogCommentListArray item=rootBlogCommentEntry key=rootBlogCommentEntryKey}-->
	      
	      <div id="<!--{$rootBlogCommentEntry->get_blogcomment_id()}-->" class="blogComments childComment">
	      		
	      		<b> <!--{$rootBlogCommentEntry->get_profilename()}--> </b>
        		
        		<br>
        		<br>
				<!--{$rootBlogCommentEntry->get_blogcommentcontent()}--><br>

				<br>
				
		    	<form name="aform" action="/blog/editBlogComment/" method="post">
				<input type="hidden" name="blogCommentID" value="<!--{$rootBlogCommentEntry->get_blogcomment_id()}-->">
				<a href="javascript:reply( '<!--{$rootBlogCommentEntry->get_blogcomment_id()}-->');">[reply]</a>
				<!--{if $rootBlogCommentEntry->get_childcommentcount() > 0}-->
					
					<div id="ccHRefMAX_<!--{$rootBlogCommentEntry->get_blogcomment_id()}-->" class="commentLine">
						<a  href="javascript:showComment('<!--{$rootBlogCommentEntry->get_blogcomment_id()}-->');">[+]</a> <!--{ $rootBlogCommentEntry->get_childcommentcount() }--> comment(s)
					</div>

					<a class="minusText" style="display: none;" id="ccHRefMIN_<!--{$rootBlogCommentEntry->get_blogcomment_id()}-->" href="javascript:hideIt('<!--{$rootBlogCommentEntry->get_blogcomment_id()}-->');">[-]</a>
					
					<div class="commentsOfComments" style="display:none;" id="childComments_<!--{$rootBlogCommentEntry->get_blogcomment_id()}-->"></div>
				<!--{/if}-->			
				
		  </div>	
		  		
  <!--{/foreach}-->