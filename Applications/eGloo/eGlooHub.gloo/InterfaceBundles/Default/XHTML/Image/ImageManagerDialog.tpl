<!--{* @Name:   ImageManagerDialog
     * @Author: George Cooper (george.cooper@egloo.com)
     *
     * @Description: This template represents the default structural format for the
     * User Image Manager modal dialog.  It is meant to be plugged into a larger
     * template structure and requires external CSS for styling and screen positioning.
     * Effort is made to make this file XHTML compliant for at least XHTML version 1.0 
     * Transitional.
     *
     * @Standalone: No
     * @Provides: Structural Markup, Unique Page Elements, CSS classes
     * @Caching: Yes
     * @Compatibility: XHTML 1.0 Transitional (Tested)
     *}-->

<div id="ImageManager">

	<div id="container">

	    <!-- 
	    <ul>
	        <li><a href="#section-1" tabindex='1'>Files</a></li>
	        <li><a href="#section-2" tabindex='2'>Tagged</a></li>
	        <li><a href="#section-3" tabindex='3'>Upload</a></li>
	        <li><a href="#section-4" tabindex='4'>Upload</a></li>
	    </ul>
		-->

	    <div id="section-1">
			<!--{ if isset( $imageManagerExistingUserImageListContainerContentUseTemplate ) && 
					( $imageManagerExistingUserImageListContainerContentUseTemplate === true ) }-->
		        <div id="ImageManager_ExistingUserImageList">
		            <!--{include file=$imageManagerExistingUserImageListContainerContentTemplate}-->
		        </div>
		    <!--{/if}-->
	    </div>
	    
	    <div id="section-2">
			<!--{ if isset( $imageManagerExistingUserImageElementListContainerContentUseTemplate ) &&
					( $imageManagerExistingUserImageElementListContainerContentUseTemplate === true ) }-->
				<div id="ImageManager_ExistingUserImageElementList">
		            <!--{include file=$imageManagerExistingUserImageElementListContainerContentTemplate}-->
		        </div>
		    <!--{/if}-->
	    </div>
	    
	    <!--
	    <div id="section-3">	
			<!--{ if isset( $imageManagerUserUpdateImageElementFormContainerContentUseTemplate ) &&
					( $imageManagerUserUpdateImageElementFormContainerContentUseTemplate === true ) }-->
				<div id="ImageManager_UserUpdateImageElementForm">
		            <!--{include file=$imageManagerUserUpdateImageElementFormContainerContentTemplate}-->
		        </div>
		    <!--{/if}-->
	    </div>
		-->
		
	    <div id="section-4">			
			<!--{ if isset( $imageManagerUserUploadNewImageFormContainerContentUseTemplate ) &&
					( $imageManagerUserUploadNewImageFormContainerContentUseTemplate === true ) }-->
				<div id="ImageManager_UserUploadNewImageForm">
		            <!--{include file=$imageManagerUserUploadNewImageFormContainerContentTemplate}-->
		        </div>
		    <!--{/if}-->
	    </div>
	    
	    <div style="text-align:center">
	    	<input id="imageManagerDoneButton" type="button" value="Done" />
	    </div>
	    
	</div>	

</div>

<script type="text/javascript">
$(document).ready(function() {
	$('#container').tabs().triggerTab(2);
	$('#ImageManager_ExistingUserImageList div.UserImageList_Image').unbind('click').click( function() {
		$('#UserImageThumbnail_IMG').attr('src','/image/ViewImageThumbnail/'+
			'&imageID='+this.id.match(/^UserImageList_Image_(\w*?)_(\w*?_\w*?)$/)[1]+
			'&imageMIMEType='+this.id.match(/^UserImageList_Image_(\w*?)_(\w*?_\w*?)$/)[2]
		);
	    $('#ImageManager_UserUpdateImageElementForm').find('#UserUpdateImageElementForm #imageID').val(
	    	this.id.match(/^UserImageList_Image_(\w*?)_(\w*?_\w*?)$/)[1]).end(
	    ).find('#UserUpdateImageElementForm #imageMIMEType').val(
	    	this.id.match(/^UserImageList_Image_(\w*?)_(\w*?_\w*?)$/)[2]
	    );
	});
	$('#ajaxUploadFrame').load(function(){
		$('#ControlCenterCube_Image_ImageList').load('/image/getUserImageList/',function(){
			$('#ImageManager_ExistingUserImageList div.UserImageList_Image').unbind('click').click( function() {
				$('#UserImageThumbnail_IMG').attr('src','/image/ViewImageThumbnail/'+
					'&imageID='+this.id.match(/^UserImageList_Image_(\w*?)_(\w*?_\w*?)$/)[1]+
					'&imageMIMEType='+this.id.match(/^UserImageList_Image_(\w*?)_(\w*?_\w*?)$/)[2]
				);
			    $('#ImageManager_UserUpdateImageElementForm').find('#UserUpdateImageElementForm #imageID').val(
			    	this.id.match(/^UserImageList_Image_(\w*?)_(\w*?_\w*?)$/)[1]).end(
			    ).find('#UserUpdateImageElementForm #imageMIMEType').val(
			    	this.id.match(/^UserImageList_Image_(\w*?)_(\w*?_\w*?)$/)[2]
			    );
			});		
		});
	});

	$('#imageManagerDoneButton').click( function() {  document.location.href="/internalMainPage/getInternalMainPage/"; } );	
});	
</script>