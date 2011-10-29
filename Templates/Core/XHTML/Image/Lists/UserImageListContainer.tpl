<fieldset id="UserUploadNewImageFormFieldset" style="margin:8px 4px 8px 4px;padding:4px 4px 8px 4px;">
	<LEGEND ACCESSKEY=I>Uploaded Images</LEGEND>

	<div id="ControlCenterCube_Image_ImageList" style="float:left;height:175px;width:150px;overflow:auto;border:1px #000 solid;">
		<!--{include file='./Frameworks/Common/XHTML/Image/Lists/UserImageList.tpl'}-->
	</div>

	<!--{* Prepend parent template to wrap these DOM ids correctly *}-->
	<div id="UserImageThumbnailPane" style="float:right;width:200px;height:175px;border:1px #000 solid;">
		<div id="UserImageThumbnailContainer" style="margin:0 auto 0 auto;">
				<img id="UserImageThumbnail_IMG" />
		</div>
	</div>

</fieldset>
