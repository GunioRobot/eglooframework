<div id="ControlCenterCube_Image_ImageList">

	<!--{foreach name=Images from=$imageDTOArray item=currentEntry}-->

		<div class="imageItemContainer <!--{if $smarty.foreach.Images.index % 2 == 0}-->gray<!--{/if}-->">

			<div id="ControlCenterCube_Image_ID_<!--{*$currentEntry->getOtherProfileID()*}-->"
				class="imageItemName clickable">
				<!--{$currentEntry->getImageFileName()}-->
			</div>

			<div class="imageItemEdit clickable">
				<a href="#">edit</a>
			</div>
			<div class="imageItemView clickable">
				<a href="/image/viewImage/&imageID=<!--{$currentEntry->getImageFileHash()}-->&imageMIMEType=<!--{$currentEntry->getImageMIMEType()|replace:'/':'_'}-->">view</a>
			</div>
			<div class="imageItemSetProfileImage clickable">
				<span onclick="javascript:$.ajax({method:'GET',url:'/image/setProfileImage/&imageID=<!--{$currentEntry->getImageFileHash()}-->&imageMIMEType=<!--{$currentEntry->getImageMIMEType()|replace:'/':'_'}-->'});">set</span>
			</div>

			<div class="imageItemDetail"></div>

		</div>

	<!--{/foreach}-->

</div>