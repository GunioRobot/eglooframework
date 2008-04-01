<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	    <title>eGloo | Welcome</title>
	    <!--{* Import statements for the CSS Styling *}-->
	    <link href="/css/blogMainRoot.css" rel="stylesheet" type="text/css" media="screen" />
	    <link href="/css/fridgeStyles.css" rel="stylesheet" type="text/css" media="screen" />
	    
    	<script type="text/javascript" src="/javascript/eGlooUtil.js"></script>
    	 
		<!-- TODO: update this to be specific to blog -->
    	<script type="text/javascript" src="/javascript/BlogMainInit.js"></script>
    	
    	
    	<script type="text/javascript">
    	
    		var commentsViewed = new Array();
    	
    		function showComment( blogComment_id ){
    			
	    		if( ! commentsViewed[ blogComment_id ] ) {
	    		
	    			//do ajax call here to display the comments
	    			$( "#childComments_" + blogComment_id).load("/blog/viewChildBlogComment/&blogCommentID="+blogComment_id,
	    
	    			 function() { 
	    			 	$("#childComments_" + blogComment_id).show("slow");
	    			 	
	    			 	$("#ccHRefMIN_" + blogComment_id).show();
	    			 	$("#ccHRefMAX_" + blogComment_id).hide();
	    			 	
	    			 	commentsViewed[ blogComment_id ] = true;
	    			 }
	   				);
	   				
	   			} else {
   				
					$("#childComments_" + blogComment_id).show("slow");
    			 	$("#ccHRefMIN_" + blogComment_id).show();
    			 	$("#ccHRefMAX_" + blogComment_id).hide();				
				}
    		}
    	
    	    function hideIt( blogComment_id ){
				$("#childComments_" + blogComment_id).hide("slow");
				$("#ccHRefMIN_" + blogComment_id).hide();
				$("#ccHRefMAX_" + blogComment_id).show();
    		}
    		
    		function reply( blogComment_id ) {
    			window.location = '/blog/cmntBlogCmntForm/&blogCommentID=' + blogComment_id;
    		}
    	
    	</script>
    	
    </head>
    <body>
    	<div id="eGlooApplicationState">
    		<div id="eas_MainProfileID"><!--{$eas_MainProfileID}--></div>
    		<div id="eas_ActiveViewedProfileID"><!--{$eas_ViewingProfileID}--></div>
    	</div>

	   <!--{include file='../Templates/Applications/eGloo/InterfaceBundles/OverlayInterface/XHTML/GlobalMenuBar/GlobalMenuBarContainer.tpl'}-->
	   <!--{include file='../Templates/Applications/eGloo/InterfaceBundles/OverlayInterface/XHTML/InformationBoard/InformationBoardContainer.tpl'}-->
    <div id="UserProfile">
     <div id="UserProfileControlStrip">
     	<div id="UserProfileControlStripUserName" class=""><!--{$username}--></div>
     	<div id="UserProfileControlStripBlogs">Blogs</div>
<!--{*  <div id="UserProfileControlStripPictures" class="clickable">Pictures</div>
     	<div id="UserProfileControlStripVideos" class="clickable">Videos</div>
     	<div id="UserProfileControlStripMusic" class="clickable">Music</div>*}-->
     	<div id="UserProfileControlStripProfile" class="clickable" onclick="window.location = '/profileID=<!--{$eas_ViewingProfileID}-->'">Profile</div>
     </div>

		<div id="left">
			<!-- Adsense or whatever -->
		</div>
		<div id="center">
		<!--{* FIX This shouldn't display a comma if no date is shown *}-->
			<p class="date"><!--{$fullBlogMonth}--> <!--{$fullBlogDay}-->, <!--{$fullBlogYear}--></p>
			<h1 id="blogTitle"><!--{$fullBlogTitle}--></h1>
			
			<!-- This div is the background, needed a way to have both the div and p be the same size -->
			<div id="blogContentBackground"></div>
			<p id="blogContent"><!--{$fullBlogContent}--></p>
			
			<div id="blogManagement">
			<!--{if $fullBlogID }-->
				<p class="date" id="commentBlog"><a href="/blog/commentBlogForm/blogID=<!--{$fullBlogID}-->">Comment on this blog</a></p>
				<!--{if $eas_MainProfileID === $fullBlogWriterID}-->
				<p class="date"><a href="/blog/editBlogForm/blogID=<!--{$fullBlogID}-->">Edit Blog</a></p>
				<!--{/if}-->
			<!--{/if}-->
				<p class="end"></p>
			</div>

	   <!--{include file='../Templates/Applications/eGloo/InterfaceBundles/OverlayInterface/XHTML/InternalBlogPage/ViewChildBlogComment.tpl'}-->
			
		</div>
		<div id="right">
			<!--{blogNavList profileID=$eas_ViewingProfileID fullBlogDateCreated=$fullBlogDateCreated }-->
		</div>
		</div>

	   
	    <div id="hiddenCollapseZone" style="position:absolute;left:0;right:0;top:0;bottom:0;display:none;background-color:transparent;">&nbsp;</div>
    </body>
</html>