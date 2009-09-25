<!--{strip}-->

	<!--{* Contains the logo in the upper left *}-->
	<div id="globalMenuLeft" class="globalMenu"> 
	    <div id="logo" class="globalMenuButton">egl<span class="oo">oo</span></div>
	</div>
	
	<!--{* Contains the top menu buttons for navigation *}-->            
	<div id="globalMenuMiddle" class="globalMenu">
	
		<!--{foreach name=globalMenuButtonLoop key=globalMenuButtonID 
				item=globalMenuButtonName from=$globalMenuBarDTO->getGlobalMenuButtons()}-->

			<div id="<!--{$globalMenuButtonID}-->" class="globalMenuButton clickable">
				<!--{$globalMenuButtonName}--><span class="fs12">▾</span>
			</div>

		<!--{/foreach}-->                

	    <div id="<!--{*$globalMenuBarDTO->getGlobalMenuSearchFieldID()*}-->" class="menuSearchItem">
	    	<input id="i1" size="20" type="text" value="" />
	    </div>
	    
	</div>
	       
	<!--{* Contains links for the username and user options *}-->
	<div id="globalMenuRight" class="globalMenu">

<!--	    <div id="GlobalMenuBar_User_Fullname" class="divAction"> -->
	    	<a href="/internalMainPage/getInternalMainPage/&profileID=<!--{$mainProfileID}-->" id="GlobalMenuBar_User_Fullname" class="divAction clickable" > <!--{$loggedInUserName}--> </a>
<!--	   </div> -->

	    <a id="GlobalMenuBar_Logout_Button" href="/account/processLogout/" class="divAction">Logout</a>

	    <div id="GlobalMenuBar_Options_Button" class="divAction clickable">Options</div>

	    <div id="GlobalMenuBar_Account_Button" class="divAction clickable">Account</div>

	</div>

<!--{/strip}-->