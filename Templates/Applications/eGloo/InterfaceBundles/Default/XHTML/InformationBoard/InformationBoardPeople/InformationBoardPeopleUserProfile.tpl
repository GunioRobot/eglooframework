<div id="InformationBoardPeopleUserProfile">
	<div id="InformationBoardPeopleUserProfileLeftColumn" style="float:left;width:15%;margin:0 5px 25px 5px;overflow:hidden;">
		<!--{*<div id="InformationBoardPeopleUserProfileName" style="text-align:center;">
			<!--{$userProfileName}-->
		</div> *}-->
		<div id="InformationBoardPeopleUserProfileImage" style="height:148px;width:100%;overflow:hidden;">
			<!--{if $userProfileImageDTO->getImageDimensionX() >= $userProfileImageDTO->getImageDimensionY()}-->
				<img src="/image/viewProfileImage/&profileID=<!--{$userProfileID}-->" 
					border="0" width="100%" alt="" class="clickable" style="margin:0 auto 0 auto;" />
			<!--{else}-->
				<img src="/image/viewProfileImage/&profileID=<!--{$userProfileID}-->" 
					border="0" height="100%" alt="" class="clickable" style="margin:0 auto 0 auto;" />			
			<!--{/if}-->
		</div>
	</div>
	<div id="InformationBoardPeopleUserProfileCenterColumn" style="float:left;width:30%;margin:0 5px 0 5px;font-size:10pt;">
		<div id="InformationBoardPeopleUserProfileFullName" style="clear:both;">
			<div id="InformationBoardPeopleUserProfileFullNameLabel" style="float:left;margin:0px 0 0 4px;width:40%;">
				Name:
			</div>
			<div id="InformationBoardPeopleUserProfileFullNameInfo" style="float:left;margin:0px 0 0 4px;width:125px;">
				<!--{$userProfileRealName.firstname}--> <!--{$userProfileRealName.lastname}-->
			</div>
		</div>
		<div id="InformationBoardPeopleUserProfileGender" style="clear:both;">
			<div id="InformationBoardPeopleUserProfileGenderLabel" style="float:left;margin:4px 0 0 4px;width:40%;">
				Gender:
			</div>
			<div id="InformationBoardPeopleUserProfileGenderInfo" style="float:left;margin:4px 0 0 4px;width:125px;">
				<!--{$userProfileDTO->getSex()}-->
			</div>
		</div>
        <div id="InformationBoardPeopleUserProfileBirthDate" style="clear:both;">
        	<div id="InformationBoardPeopleUserProfileBirthDateLabel" style="float:left;margin:4px 0 0 4px;width:40%;">
        		Birthday:
        	</div>
        	<div id="InformationBoardPeopleUserProfileBirthDateInfo" style="float:left;margin:4px 0 0 4px;width:125px;">
        		<!--{$userBirthDate}-->
        	</div>
        </div>
        <div id="InformationBoardPeopleUserProfileInterestedIn" style="clear:both;">
        	<div id="InformationBoardPeopleUserProfileInterestedInLabel" style="float:left;margin:4px 0 0 4px;width:40%;">
        		Interested in:
        	</div>
        	<div id="InformationBoardPeopleUserProfileInterestedInInfo" style="float:left;margin:4px 0 0 4px;width:125px;">
				<!--{if $userProfileDTO->getInterestedInMen() && $userProfileDTO->getInterestedInWomen()}-->
					Men, Women
				<!--{elseif $userProfileDTO->getInterestedInMen()}-->
					Men
				<!--{elseif $userProfileDTO->getInterestedInWomen()}-->
					Women
				<!--{else}-->
					Not Specified
				<!--{/if $userProfileDTO->getInterestedInWomen()}-->
        	</div>
        </div>
        <div id="InformationBoardPeopleUserProfileLookingFor" style="clear:both;">
        	<div id="InformationBoardPeopleUserProfileLookingForLabel" style="float:left;margin:4px 0 0 4px;width:40%;">
        		Looking For:
        	</div>
        	<div id="InformationBoardPeopleUserProfileLookingForInfo" style="float:left;margin:4px 0 0 4px;width:125px;">
        		<!--{* Build "Looking For" Information
        			 * NOTE: Captured output must be stripped such that if all conditions fail,
        			 *       then the resulting value of $userProfileLookingForInfo must be the
        			 *       empty string ('').
        			 *}-->
        		<!--{capture name=userProfileLookingForInfo assign=userProfileLookingForInfo}--><!--{strip}-->
	        		<!--{if $userProfileDTO->getLookingForFriendship()}-->
	        			Friendship,&nbsp;
	        		<!--{/if}-->
	        		<!--{if $userProfileDTO->getLookingForRelationship()}-->
        				Relationship,&nbsp;
	        		<!--{/if}-->
	        		<!--{if $userProfileDTO->getLookingForDating()}-->
        				Dating,&nbsp;
	        		<!--{/if}-->
	        		<!--{if $userProfileDTO->getLookingForRandomPlay()}-->
        				Random Play,&nbsp;
	        		<!--{/if}-->
	        		<!--{if $userProfileDTO->getLookingForWhateverICanGet()}-->
        				Whatever I Can Get
	        		<!--{/if}-->
        		<!--{/strip}--><!--{/capture}-->
        		<!--{if $userProfileLookingForInfo != ''}-->
	        		<!--{$userProfileLookingForInfo|regex_replace:'/(,\s*)$/':''}-->
	        	<!--{else}-->
					Not Specified
				<!--{/if}-->
        	</div>
        </div>
        <div id="InformationBoardPeopleUserProfileHometown" style="clear:both;">
        	<div id="InformationBoardPeopleUserProfileHometownLabel" style="float:left;margin:4px 0 0 4px;width:40%;">
        		Hometown:
        	</div>
        	<div id="InformationBoardPeopleUserProfileHometownInfo" style="float:left;margin:4px 0 0 4px;width:125px;">
        		<!--{$userProfileDTO->getHomeTown()}-->
        	</div>
        </div>
	</div>
	<div id="InformationBoardPeopleUserProfileRightColumn" class="clickable" style="float:left;width:30%;margin:0 5px 0 5px;font-size:10pt;">
		<div id="InformationBoardPeopleUserProfileViewUserProfile" style="clear:both;margin:0 0 0 4px;">
			<div onclick="buildProfilePage('<!--{$userProfileID}-->');">
				<!--{$userProfileRealName.firstname}--> <!--{$userProfileRealName.lastname}-->'s Page
			</div>
		</div>
		<div id="InformationBoardPeopleUserProfileViewUserFriendList" class="clickable" style="clear:both;margin:4px 0 0 4px;">
			<div onclick="">
				<!--{$userProfileRealName.firstname}--> <!--{$userProfileRealName.lastname}-->'s Friends
			</div>
		</div>
		<div id="InformationBoardPeopleUserProfileIssueFriendRequest" class="clickable" style="clear:both;margin:24px 0 0 4px;">
			<!--{if $hasRelationship == true}-->
				<div onclick="$.ajax({type:'GET',url:( '/relationship/requestBiDirectionalRelationship/&relationshipType=Friends&profileID=<!--{$userProfileID}-->' ),dataType:'xml'});">
					<!--{*This needs to grab the relationship type in the future*}-->
					Remove from Friends
				</div>
			<!--{* TODO Add elseif has relationship request *}-->
			<!--{else}-->
				<div onclick="$.ajax({type:'GET',url:( '/relationship/requestBiDirectionalRelationship/&relationshipType=Friends&profileID=<!--{$userProfileID}-->' ),dataType:'xml'});">
					Add <!--{$userProfileRealName.firstname}--> <!--{$userProfileRealName.lastname}--> as a Friend
				</div>
			<!--{/if}-->
		</div>
	</div>
</div>