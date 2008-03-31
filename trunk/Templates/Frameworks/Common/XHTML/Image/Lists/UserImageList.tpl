<!--{foreach name=Images from=$userImageDTOList item=currentEntry}-->

	<div class="imageItemContainer <!--{if $smarty.foreach.Images.index % 2 == 0}-->gray<!--{/if}-->">

		<div id="UserImageList_Image_<!--{$currentEntry->getImageFileHash()}-->_<!--{$currentEntry->getImageMIMEType()|replace:'/':'_'}-->" class="UserImageList_Image" 
			class="imageItemName clickable" onclick="" style="cursor:pointer;">
			<span style="float:left;"><!--{$currentEntry->getImageFileName()|truncate:20:"...":true:true}--></span>
			<span onclick="javascript:$.ajax({method:'GET',url:'/image/setProfileImage/&imageID=<!--{$currentEntry->getImageFileHash()}-->&imageMIMEType=<!--{$currentEntry->getImageMIMEType()|replace:'/':'_'}-->'});" style="text-decoration:underline; float:right; margin-right:2px;">set</span>
		</div>
<!--{*	
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
*}-->	
			</div>

<!--{/foreach}-->
