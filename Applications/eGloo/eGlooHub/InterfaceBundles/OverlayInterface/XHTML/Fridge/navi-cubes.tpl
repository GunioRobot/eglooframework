<script language="javascript">
$(document).ready(function(){
	$("#menuButtons li").bind("click",function(){
		//alert( $(this).attr("name") );
	});
	$("#menuButtons li").not(".on").bind("mouseover",function(){
//		$(this).css("color","#f00");
	});
	$("#menuButtons li").not(".on").bind("mouseout",function(){
//		$(this).css("color","#fff");
	});
	$("#contextOptions li").bind("mouseover",function(){
//		$(this).css("background-color","#00f");
	});
	$("#contextOptions li").bind("mouseout",function(){
//		$(this).css("background-color","transparent");
	});
});
</script>


<script type="text/javascript">
function showPicsTab(){
  $('#dropDownContent').load('/infoBoard/FridgePics/');
}

function addCubeToPage(){

	$("#fridgeContent input").each( 
	
	function(i) {
		if(this.checked){
			
			 $(this).parent("div").hide();
			
			var cubeType = this.value;
			var tID = 'fridgeItem_' + cubeType;
			
			$("<li id='" + tID + "'  class='dragdropsort moveable'></li>").appendTo("#center1");
	
	  		buildDynamicContent({
	                elementID: tID,
	                url:'/cube/getNewCubeElementInstance/cubeID=' + cubeType,
	                type:'cube',
	                replaceEl:true,
	                onComplete:function(){parseProfileDOM();}});
		}
	});	
}
</script>


<div id="eGlooFridge">
	<!-- where all the picture, images, etc. are held -->
	<div id="contentContainer">
		<!-- menu bar -->
		<ul id="menuButtons">
			<li id="cubes" class="on">my cubes</li>
			<!--{*<li id="pics" onclick="showPicsTab();">my pics</li>*}-->
			<li id="pics">my pics</li>
			<li id="tunes">my tunes</li>
			<li id="vids" class="last">my vids</li>
		</ul>
		<!-- main content -->
		<div id="fridgeContent">
			<!-- list of cubes, pics, tunes, vids -->
			<div class="myCubes scrollable">
				<ul>
				<!--{foreach name=FridgeDTOLoop from=$fridgeCubes item=fridgeDTO}-->
					<li id="fridgeItemID_<!--{$fridgeDTO->getElementTypeID()}-->" >

						<div><input type="checkbox" name="cubes" value="<!--{$fridgeDTO->getElementTypeID()}-->"> <!--{$fridgeDTO->getCubeName()}--> </div>
					</li> 

				<!--{/foreach}-->
				<!-- 
					<li><input type="checkbox" name="cubes" value="1"><img src="cube-profile.gif"></li>
					<li><input type="checkbox" name="cubes" value="1"><img src="cube-blog.gif"></li>
					<li><input type="checkbox" name="cubes" value="1"><img src="cube-pics.gif"></li>
					<li><input type="checkbox" name="cubes" value="1"><img src="cube-friends.gif"></li>
					-->
				</ul>
				<ul>
				<!--
					<li><input type="checkbox" name="cubes" value="1"><img src="cube-player.gif"></li>
					--> </ul>			
			</div>
			<!-- special buttons to add/remove/etc. -->
			<div id="contextButtons">
				<!--{*<p class="right"><img src="trash.gif" name="delete"><br>delete</p>*}-->
				<div>
					<p class="left divAction clickable" onClick="addCubeToPage();"><img src="cube.gif" name="cube"><br>create cube(s)</p>
<!--{*
					<p class="left"><br>&nbsp; | &nbsp;</p>
					<a href="#" onClick="addCubeToPage();" class="left" style="text-decoration:none"><br>Add Cube To Page</a>
*}-->
				</div>
			</div>
		</div>
	</div>
	<!-- where the albums or whatever are held -->
	<div id="contextMenu">
		<div id="contextOptions">
<!--{*
				<ul>
				<li>all</li>
				<li>current profile</li>
				<li>pic cubes</li>
				<li>vid cubes</li>
				<li>other peoples</li>
				<li>album</li>
				<li>album</li>
				<li>album</li>
				<li>album</li>
				<li>album</li>
			</ul>
*}-->
		</div>
	</div>
</div>