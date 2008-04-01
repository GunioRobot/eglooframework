<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	    <title>eGloo | Welcome</title>
	     <!-- Skin CSS file -->
		<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.3.1/build/assets/skins/sam/skin.css"> 
		
	    <!--{* Import statements for the CSS Styling *}-->
	    <link href="/css/blogMainRoot.css" rel="stylesheet" type="text/css" media="screen" />
	    <link href="/css/colorPicker.css" rel="stylesheet" type="text/css" media="screen" />
	    <link href="/css/fridgeStyles.css" rel="stylesheet" type="text/css" media="screen" />	      
	    
    	<script type="text/javascript" src="/javascript/eGlooUtil.js"></script>
    	
<!--    <script type="text/javascript" src="/javascript/eGlooUtil_Extras.js"></script>	 -->
    	
		<!-- TODO: update this to be specific to blog -->
    	<script type="text/javascript" src="/javascript/CreateEditBlog.js"></script>

		<!-- Utility Dependencies -->
		<script type="text/javascript" src="http://yui.yahooapis.com/2.3.1/build/yahoo-dom-event/yahoo-dom-event.js"></script> 
		<script type="text/javascript" src="http://yui.yahooapis.com/2.3.1/build/element/element-beta-min.js"></script> 

		<!-- Needed for Menus, Buttons and Overlays used in the Toolbar -->
		<script src="http://yui.yahooapis.com/2.3.1/build/container/container_core-min.js"></script>
		<script src="http://yui.yahooapis.com/2.3.1/build/menu/menu-min.js"></script>
		<script src="http://yui.yahooapis.com/2.3.1/build/button/button-beta-min.js"></script>

		<!-- Source file for Rich Text Editor-->
		<script src="http://yui.yahooapis.com/2.3.1/build/editor/editor-beta-min.js"></script>
    	
	<script>	

	
	var myEditor = new YAHOO.widget.Editor('msgpost', {
    height: '300px',
    width: '515px',
    dompath: true, //Turns on the bar at the bottom
    animate: true, //Animates the opening, closing and moving of Editor windows
    handleSubmit: true
	});
	myEditor.render();


	</script>
		
    	
    </head>

    <body class="yui-skin-sam">
    	<div id="eGlooApplicationState">
    		<div id="eas_MainProfileID"><!--{$eas_MainProfileID}--></div>
    		<div id="eas_ActiveViewedProfileID"><!--{$eas_ViewingProfileID}--></div>
    	</div>

	   <!--{include file='../Templates/Applications/eGloo/InterfaceBundles/OverlayInterface/XHTML/GlobalMenuBar/GlobalMenuBarContainer.tpl'}-->
	   <!--{include file='../Templates/Applications/eGloo/InterfaceBundles/OverlayInterface/XHTML/InformationBoard/InformationBoardContainer.tpl'}-->
    <div id="UserProfile">
     <div id="UserProfileControlStrip">
     	<div id="UserProfileControlStripUserName" class=""><!--{$username}--></div>
     	<div id="UserProfileControlStripBlogs" class="clickable" onclick="window.location = '/blog/viewBlog/&profileID=<!--{$eas_ViewingProfileID}-->'">Blogs</div>     	
<!--{*  <div id="UserProfileControlStripPictures" class="clickable">Pictures</div>
     	<div id="UserProfileControlStripVideos" class="clickable">Videos</div>
     	<div id="UserProfileControlStripMusic" class="clickable">Music</div>*}-->
     	<div id="UserProfileControlStripProfile" class="clickable" onclick="window.location = '/internalMainPage/getInternalMainPage/&profileID=<!--{$eas_ViewingProfileID}-->'">Profile</div>
     </div>
     
	     <div id="left">
			<!-- Adsense or whatever -->
		</div>
		<div id="center">
			<p class="date"><!--{$smarty.now|date_format:"%A, %B %e, %Y"}--></p>
			
			<!-- put if statement for whether we want create or edit text -->
			
			<!--{if $editFlag }-->
				<h1 id="blogTitle">Edit Blog</h1>
				<form name="aform" action="/blog/editBlog/" method="post">
				<input type="hidden" name="blogID" value="<!--{$blogID}-->">
			<!--{else}-->
				<h1 id="blogTitle">Create Blog</h1>
				<form name="aform" action="/blog/createBlog/" method="post">
			<!--{/if}-->
			
				<h5>Title</h5>
			<!--{if $editFlag }-->
				<input type="text" name="newBlogTitle" id="newBlogTitle" value="<!--{$editBlogTitle}-->">
			<!--{else}-->
				<input type="text" name="newBlogTitle" id="newBlogTitle">
			<!--{/if}-->
				<h5>Content</h5>
				
				<!--{if $editFlag }-->				
				<textarea name="newBlogContent" id="msgpost" cols="50" rows="10">
 			   <!--{$editBlogContent}-->
				</textarea>

				<!--{else}-->
					<textarea id="msgpost" name="newBlogContent" cols="70" rows="20"></textarea>
				<!--{/if}-->
				<input type="submit" value="Submit Blog">
				<input type="button" value="Clear Form" onClick="clearForm()">
			</form>
			<!-- <p class="date">Posted by Artem at 7:04 PM&nbsp;&nbsp;0 comments</p> -->
			<div id="test"></div>
		</div>
		<div id="right">

		<!--{blogNavList profileID=$eas_ViewingProfileID fullBlogDateCreated=$fullBlogDateCreated }-->

		</div>
		</div>

	   
		<div id="hiddenCollapseZone" style="position:absolute;left:0;right:0;top:0;bottom:0;display:none;background-color:transparent;width:expression(document.documentElement.clientWidth + 'px');height:expression(document.documentElement.clientHeight + 'px');">&nbsp;</div>
    </body>
</html>