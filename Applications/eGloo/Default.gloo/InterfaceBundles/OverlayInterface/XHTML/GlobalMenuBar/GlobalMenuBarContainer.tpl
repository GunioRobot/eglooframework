<!--{strip}-->


<div style="height: 50px;position: absolute;left: 50%;margin-left: -500px;width: 20px;z-index: 20;background-color: #181F17">
</div>

<!--{* Contains the top menu buttons for navigation *}--> 
	<div id="fisheye" class="fisheye" style="background-color: #181F17">       
        <div id="globalMenuLeftFEContainer" class="fisheyeContainer">

			<div id="gMBFridge" onclick="invokeOverlay( 'Fridge', this )" class = "globalMenuButton clickable" >	
				<a href="#" class="fisheyeItem">
				<img class="png" src="images/overlay/menubar/fridge.png" />
				<span>Fridge</span></a>
			 </div>
			  
			<div id="gMBBlogs" onclick="invokeOverlay( 'Blogs', this )" class = "globalMenuButton clickable "  >
		  		<a href="#" class="fisheyeItem">
		  		<img class="png" src="images/overlay/menubar/messaging.png" />
		  		<span>Blogs</span></a>
			</div> 

			<div id="gMBPeople" class = "globalMenuButton clickable " >
			    <a href="#" class="fisheyeItem">
			    <img class="png" src="images/overlay/menubar/mypeople.png"  />
			    <span>People</span></a>
			</div>
			
			<div id="gMBRatings" class = "globalMenuButton clickable " >
	 			<a href="#" class="fisheyeItem">
	 			<img class="png" src="images/overlay/menubar/ratings.png" />
	 			<span>Ratings</span></a>
			</div>

			<div id="gMBRewards" class = "globalMenuButton clickable "> 
				<a href="#" class="fisheyeItem">
				<img class="png" src="images/overlay/menubar/rewards.png" />
				<span>Rewards</span></a>
			</div>
			
			<div id="gMBMusic"  class = "globalMenuButton clickable">
				<a href="#" class="fisheyeItem">
				<img class="png" src="images/overlay/menubar/favorites.png" />
				<span>Favorites</span></a>
			</div>
			
			<div id="gMBIcing"  class = "globalMenuButton clickable">	
				<a href="#" class="fisheyeItem">
				<img class="png" src="images/overlay/menubar/network.png" />
				<span>Network</span></a>
			</div>
		</div>
		 <div id="" class="menuSearchItem">
	    	<input id="i1" size="15" type="text" value="" />
	    </div> 
	</div>

	<!--{* Contains links for the username and user options *}-->
	<div id="globalMenuRight" class="globalMenu">

<!--	    <div id="GlobalMenuBar_User_Fullname" class="divAction"> -->
			<!--{if isset($loggedInUserName)}-->
		    	<a href="profileID=<!--{$mainProfileID}-->" id="GlobalMenuBar_User_Fullname" class="divAction clickable" ><!--{$loggedInUserName}--></a>
		    <!--{else}-->
		    	<div id="GlobalMenuBar_User_Fullname" class="divAction clickable" >test&nbsp;</div>
		    <!--{/if}-->
<!--	   </div> -->

		<div id="group">
	    	<a id="GlobalMenuBar_Logout_Button" href="account/processLogout/" class="divAction">Logout</a>
<!--{*
	   		<div id="GlobalMenuBar_Options_Button" class="divAction clickable">Options</div>
	    	<div id="GlobalMenuBar_Account_Button" class="divAction clickable">Account</div>
*}-->
			<!--{if $loggedInUserProfile === true}-->
	    		<a id="GlobalMenuBar_Image_Button" href="javascript:showProfileImage();" class="divAction">Set Profile Image</a>
	    	<!--{/if}-->
		</div>
	</div>

	<!--{if $loggedInUserProfile === true}-->
		<div id="profileImage">
	        <form enctype="multipart/form-data" method="post" action="/image/setProfileImage/" name="form">
	              <h1 id="profileImageTitle">Choose an image to upload</h1>
	              <input id="fileField" type="file" name="profileImage"> <br>
	              <input id="updateButton" type="submit" value="Update">
	              <input id="cancelButton" type="submit" value="Cancel">
	        </form>
		</div>
	<!--{/if}-->

<script type="text/javascript">

	function showProfileImage(){
		$("#profileImage").toggle();
		$("#profileImage").css("left", ( ( (document.body.clientWidth) - 300) / 2 ) + 'px');
	}

	$(document).ready( function() {
		$('#fisheye').Fisheye({
			maxWidth: 40,
			items: 'a',
			itemsText: 'span',
			container: '.fisheyeContainer',
			itemWidth: 40,
			proximity: 20,
			halign : 'left'
		});
		
		$('#cancelButton').click( function() { $("#profileImage").toggle(); return false; });
	});

</script>
<!--{/strip}-->