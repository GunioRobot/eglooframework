<div id="Center_1_Block">
    <!--{* Displays search navigation for the global/network/favorites/recommended filters *}-->
    <div id="Menu_1_Header12">
        <div id="Menu_Header_Break4" class="clickable">Suggest</div>
        <div id="Menu_Header_Break3" class="clickable">Favorite</div>
        <div id="Menu_Header_Break2" class="clickable">Network</div>
        <div id="Menu_Header_Break1" class="clickable">Global</div>            
    </div>
    <!--{* The overall containers for the top 30 *}-->
<!--{*
    <div id="Center_Sub_Column1">
    	<div id="pod-wrap">
			<div id="top-but-wrap">
				<a id="top-but" onclick="podButtonClicked(event);" href="#">&nbsp;</a>
			</div>
			<div id="pod-list-wrap">
				<div style="left: 0px; top: 0px;" class="pod-list"></div>
			</div>
			<div id="bot-but-wrap">
				<a id="bot-but" onclick="podButtonClicked(event);" href="#">&nbsp;</a>
			</div>
		</div>
    </div>
*}-->

	<!--{counter name=columnCounter start=10 skip=5 direction="down" assign=resultSectionLowerDelim}-->

    <!--{section name=resultColumnLoopIndex loop=$resultColumns show=true}-->
        <div id="<!--{$resultColumns[resultColumnLoopIndex]->id}-->" class="<!--{$resultColumns[resultColumnLoopIndex]->style}-->">
		<!--{section name=resultColumnItemIndex loop=$recentBlogProfilesArray start=$resultSectionLowerDelim step=1 max=5 show=true}-->
					<div id="userBlog_<!--{$recentBlogProfilesArray[resultColumnItemIndex]->get_blog_id()}-->" style="clear:both;height:75px;width:240px;clear:both;position:relative:float:left;margin: 10px 0 10px 0;overflow:hidden;">
						<div style="height:64px;width:64px;float:left;">
							<!--{assign var=blogWriterID value=$recentBlogProfilesArray[resultColumnItemIndex]->get_blogwriter()}-->
							<img id="UserProfileImage_IMG" src="/image/viewProfileImageThumbnail/&profileID=<!--{$blogWriterID}-->&profileImageHash=<!--{$profileImageThumbnailHashArray.$blogWriterID}-->" border="0" alt="profile picture" style="margin:0 auto 0 auto;" />
						</div>
						<div style="height:75px;width:165px;float:left;margin-left:5px;">
							<!--{assign var=blogProfileID value=$recentBlogProfilesArray[resultColumnItemIndex]->get_blog_id()}-->
							<a href="/blog/viewBlog/&blogID=<!--{$recentBlogProfilesArray[resultColumnItemIndex]->get_blog_id()}-->" style="font-size:11px;"><!--{ $blogInfoArray.$blogProfileID->get_output_blogtitle()|truncate:30:"..."|strip }--></a>
							<br />
							<span style="font-size:11px;"><!--{ $blogInfoArray.$blogProfileID->get_output_blogcontent()|strip_tags:false|truncate:100:"..."|strip }--></span>
							<a href="/blog/viewBlog/&blogID=<!--{$recentBlogProfilesArray[resultColumnItemIndex]->get_blog_id()}-->" style="font-size:11px;">Continue Reading...</a>
						</div>
					</div>
		<!--{/section}-->
		</div>
		<!--{counter name=columnCounter}-->
    <!--{/section}-->

</div>
    <!--{* Divs containing navigation to search deeper into the 30 search results *}-->
<!--{*<div id="DropDownBottomNavigation">*}-->
<!--{*    <div id="DropDownBottomNavJoinRankedButton">Join eGloo's </div> *}-->
<!--{*    <div id="DropDownBottomNavHelpButton" class="clickable">Help</div>
    <div id="DropDownBottomNavPrivacyButton" class="clickable">Privacy</div>
    <div id="DropDownBottomNavLegalButton" class="clickable">Legal</div>
    <div id="DropDownBottomNavArrowLeft" class="clickable">◀</div>
    <div id="DropDownBottomNavResultBounds">1 - 30</div>
    <div id="DropDownBottomNavArrowRight" class="clickable">▶</div>
</div>*}-->
