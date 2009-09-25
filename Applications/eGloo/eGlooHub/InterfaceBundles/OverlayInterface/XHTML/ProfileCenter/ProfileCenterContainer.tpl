<!--{* @Name:   UserProfileCenterContainer
     * @Author: George Cooper (george.cooper@egloo.com)
     *
     * @Description: This template represents the default structural format for an
     * eGloo basic user profile.  It is meant to be plugged into a larger template
     * structure and requires external CSS for styling and screen positioning.
     * Effort is made to make this file XHTML compliant for at least XHTML version
     * 1.0 Transitional.
     *
     * @Standalone: No
     * @Provides: Structural Markup, Unique Page Elements, CSS classes
     * @Caching: Partial
     * @Compatibility: XHTML 1.0 Transitional (Tested)
     *
     * @Token username (string) username of the account that this profile belongs to
     *}-->     

<!--{if $buildContainer === true}-->     
<div id="UserProfile">
<!--{/if}-->


	<!--{* @id: UserProfileControlStrip
		 *
		 * @Description: This is the div that contains information about the current
		 * profile being viewed, as well as actions that can be performed related to
		 * the owner of the profile or the profile itself, such as messaging the user,
		 * adding the user as a friend, subscribing to the page, viewing previous
		 * revisions and other functions.
	     *}-->
<!--{*
	<!--{if $loggedInUserProfile === true}-->
    <div id="UserProfileControlStrip">
    	<div id="UserProfileControlStripUserName" class=""><!--{$username}--></div>
    	<div id="UserProfileControlStripRanking" class="clickable">Ranking</div>
    	<div id="UserProfileControlStripPermissions" class="clickable">Permissions</div>
    	<div id="UserProfileControlStripEdit" class="clickable">Edit</div>
    	<div id="UserProfileControlStripPortrait" class="clickable">Portrait</div>
    </div>
    <!--{else}-->
    <div id="UserProfileControlStrip">
    	<div id="UserProfileControlStripUserName" class=""><!--{$username}--></div>
    	<div id="UserProfileControlStripRanking" class="clickable">Ranking</div>
    	<div id="UserProfileControlStripAddFriend" class="clickable">Add Friend</div>
    	<div id="UserProfileControlStripSubscribe" class="clickable">Subscribe</div>
    	<div id="UserProfileControlStripMessage" class="clickable">Message</div>
    </div>    	
	<!--{/if}-->	
*}-->
    
    
    
     <div id="UserProfileControlStrip">
     	<div id="UserProfileControlStripUserName" class=""><!--{$username}--></div>
     	<div id="UserProfileControlStripBlogs" class="clickable" onclick="window.location = '/blog/viewBlog/&profileID=<!--{$profileID}-->'">Blogs</div>
<!--{*  <div id="UserProfileControlStripPictures" class="clickable">Pictures</div>
     	<div id="UserProfileControlStripVideos" class="clickable">Videos</div>
     	<div id="UserProfileControlStripMusic" class="clickable">Music</div>*}-->
     	<div id="UserProfileControlStripProfile" class="clickable" onclick="window.location = '/profileID=<!--{$eas_ViewingProfileID}-->'">Profile</div>
     </div>
    
    <!--{* @id: UserProfileContent
         *
         * @Description: 
         *}-->
    <div id="UserProfileContent">

    	<!--{* Left Column *}-->
        <div class="defaultProfileColumn">
            <div id="unmov">
                <img id="UserProfileImage_IMG" src="/image/viewProfileImage/&profileID=<!--{$profileID}-->&profileImageHash=<!--{$profileImageHash}-->" border="0" height="300px"  alt="profile picture" style="margin:0 auto 0 auto;" />
            </div>
			
            <ul id="center0" class="sortable boxy">
            <!--{foreach from=$cubeColumn0Output item=currentEntry key=key}-->
				<li id="ElementContainer_<!--{$key}-->" class="elementContainer dragdropsort" style="overflow: hidden; display: block; opacity: 0.9999;">
				<!--{$currentEntry}-->
				</li>
			<!--{/foreach}-->
            </ul>
        </div>

        <!--{* Center Column *}-->
        <div class="defaultProfileColumn">
            <ul id="center1" class="sortable boxy">
            <!--{foreach from=$cubeColumn1Output item=currentEntry key=key}-->
				<li id="ElementContainer_<!--{$key}-->" class="elementContainer dragdropsort" style="overflow: hidden; display: block; opacity: 0.9999;">
				<!--{$currentEntry}-->
				</li>
			<!--{/foreach}-->            
            </ul>
        </div>

        <!--{* Right Column *}-->
        <div class="defaultProfileColumn">
            <ul id="center2" class="sortable boxy">
            <!--{foreach from=$cubeColumn2Output item=currentEntry key=key}-->
				<li id="ElementContainer_<!--{$key}-->" class="elementContainer dragdropsort" style="overflow: hidden; display: block; opacity: 0.9999;">
				<!--{$currentEntry}-->
				</li>
			<!--{/foreach}-->
            </ul>
        </div>
    </div>

    <!--{* Copyright Notice Footer *}-->
<!--{*<div id="copyright"><!--{$copyrightNotice}--Copyright &copy; 2007 eGloo, LLC. All rights reserved.</div>
	<div id="footer">
		<span class="footer_option">Terms of Use</span>
		<span class="spantext2">&nbsp;|&nbsp;</span>
		<span class="footer_option">Privacy Policy</span>
		<span class="spantext2">&nbsp;|&nbsp;</span>
		<span class="footer_option">Help</span>
		<span class="spantext2">&nbsp;|&nbsp;</span>
		<span class="footer_option">About Us</span>
		<span class="spantext2">&nbsp;|&nbsp;</span>        
		<span class="footer_option">Contact Us</span>
	</div>*}-->

<!--{if $buildContainer == true}-->     
</div>
<!--{/if}-->