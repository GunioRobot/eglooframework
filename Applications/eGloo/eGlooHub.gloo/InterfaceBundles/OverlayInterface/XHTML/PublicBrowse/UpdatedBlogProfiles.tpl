<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	    <title>eGloo | Welcome</title>
	    <!--{* Import statements for the CSS Styling *}-->
	    <link href="/css/intMainRoot.css" rel="stylesheet" type="text/css" media="screen" />
	    <link href="/css/fridgeStyles.css" rel="stylesheet" type="text/css" media="screen" />	      
		<style type="text/css">
		#userProfile { clear:both; margin:10px 0 0 15px; padding-top:20px; }
		#userProfile img { float:left; margin:0; padding:0 10px; }
		#userProfile h2 { color:#0099e5; font:bold 16px verdana; padding-bottom:10px; }
		</style>
		
    	<script type="text/javascript" src="/javascript/eGlooUtil.js"></script>
    	<script type="text/javascript" src="/javascript/internalMainInit.js"></script>
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
	     		<div id="UserProfileControlStripUserName" class="">Recent Blog Updates</div>
			</div>
<!--{*
			<!--{foreach from=$recentBlogProfilesArray item=blogProfile }-->
				<div id="userProfile">
					<div style="height:100px;width:50px;">
					<!--{assign var=blogWriterID value=$blogProfile->get_blogwriter()}-->
						<img id="UserProfileImage_IMG" src="/image/viewProfileImage/&profileID=<!--{$blogProfile->get_blogwriter()}-->&profileImageHash=<!--{$profileImageThumbnailHashArray.$blogWriterID}-->" border="0"  alt="profile picture" style="margin:0 auto 0 auto;" />
					</div>
					<h2><!--{$blogProfile->get_profilename()}--></h2>
					<a href="/blog/viewBlog/&blogID=<!--{$blogProfile->get_blog_id()}-->" >View blog</a> <br />
				</div>
			<!--{/foreach}-->
*}-->
			<!--{foreach from=$recentBlogProfilesArray item=blogProfile }-->
				<div id="userBlog_<!--{$blogProfile->get_blog_id()}-->" style="clear:both;height:64px;width:250px;clear:both;position:relative:float:left;overflow:hidden;">
					<div style="height:64px;width:64px;float:left;">
						<!--{assign var=blogWriterID value=$blogProfile->get_blogwriter()}-->
						<img id="UserProfileImage_IMG" src="/image/viewProfileImageThumbnail/&profileID=<!--{$blogWriterID}-->&profileImageHash=<!--{$profileImageThumbnailHashArray.$blogWriterID}-->" border="0" alt="profile picture" style="margin:0 auto 0 auto;" />
					</div>
					<div style="height:64px;width:175px;float:left;">
						<!--{*<a href="/blog/viewBlog/&blogID=<!--{$blogProfile->get_blog_id()}-->" ><!--{$blogProfile->get_profilename()}--></a>*}-->
						<!--{assign var=blogProfileID value=$blogProfile->get_blog_id()}-->
						<a href="/blog/viewBlog/&blogID=<!--{$blogProfile->get_blog_id()}-->" ><!--{ $blogInfoArray.$blogProfileID->get_output_blogtitle() }--></a>
						<br />
						<span style="font-size:11px;"><!--{ $blogInfoArray.$blogProfileID->get_output_blogcontent()|strip_tags:false|truncate:200:"..."|strip }--></span>
						<a href="/blog/viewBlog/&blogID=<!--{$blogProfile->get_blog_id()}-->" style="font-size:11px;">Continue Reading...</a>
					</div>
				</div>
			<!--{/foreach}-->

		</div>
    </body>
</html>