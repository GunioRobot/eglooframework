<!--{foreach from=$recentBlogProfilesArray item=blogProfile }-->
	<div id="userBlog_<!--{$blogProfile->get_blog_id()}-->" style="clear:both;height:64px;width:250px;clear:both;position:relative:float:left;">
		<div style="height:64px;width:64px;float:left;">
			<img id="UserProfileImage_IMG" src="/image/viewProfileImage/&profileID=<!--{$blogProfile->get_blogwriter()}-->" border="0" height="100%"  alt="profile picture" style="margin:0 auto 0 auto;" />
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
