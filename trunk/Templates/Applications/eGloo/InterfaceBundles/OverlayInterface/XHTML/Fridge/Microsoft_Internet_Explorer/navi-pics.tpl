<link href="/css/fridgeStyles.css" rel="stylesheet" type="text/css" media="screen" />

<script type="text/javascript" src="/javascript/jquery.js"></script>
<script type="text/javascript" src="/javascript/eGlooUtil_Extras.js"></script>

<script language="javascript">
$(document).ready(function(){
	$("#menuButtons li").bind("click",function(){
		alert( $(this).attr("name") );
	});
	$("#menuButtons li").not(".on").bind("mouseover",function(){
		$(this).css("color","#f00");
	});
	$("#menuButtons li").not(".on").bind("mouseout",function(){
		$(this).css("color","#fff");
	});
	$("#contextOptions li").bind("mouseover",function(){
		$(this).css("background-color","#00f");
	});
	$("#contextOptions li").bind("mouseout",function(){
		$(this).css("background-color","#000");
	});
});
</script>


<link href="fridge-styles.css" rel="stylesheet" type="text/css" title="default" media="screen" />
		
<div id="eGlooFridge">
	<!-- where all the picture, images, etc. are held -->
	<div id="contentContainer">
		<!-- menu bar -->
		<ul id="menuButtons">
			<li name="cubes">my cubes</li>
			<li name="pics" class="on">my pics</li>
			<li name="tunes">my tunes</li>
			<li name="vids" class="last">my vids</li>
		</ul>
		<!-- main content -->
		<div id="fridgeContent">
			<!-- list of cubes, pics, tunes, vids -->
			<div class="myPics scrollable">
				<ul>
					<li><input type="checkbox" name="pics" value="1"><img src="people.gif"></li>
					<li><input type="checkbox" name="pics" value="1"><img src="people.gif"></li>
					<li><input type="checkbox" name="pics" value="1"><img src="people.gif"></li>
					<li><input type="checkbox" name="pics" value="1"><img src="people.gif"></li>
					<li><input type="checkbox" name="pics" value="1"><img src="people.gif"></li>
				</ul>
				<ul>
					<li><input type="checkbox" name="pics" value="1"><img src="people.gif"></li>
					<li><input type="checkbox" name="pics" value="1"><img src="people.gif"></li>
					<li><input type="checkbox" name="pics" value="1"><img src="people.gif"></li>
					<li><input type="checkbox" name="pics" value="1"><img src="people.gif"></li>
					<li><input type="checkbox" name="pics" value="1"><img src="people.gif"></li>
				</ul>
				<ul>
					<li><input type="checkbox" name="pics" value="1"><img src="people.gif"></li>
					<li><input type="checkbox" name="pics" value="1"><img src="people.gif"></li>
					<li><input type="checkbox" name="pics" value="1"><img src="people.gif"></li>
					<li><input type="checkbox" name="pics" value="1"><img src="people.gif"></li>
					<li><input type="checkbox" name="pics" value="1"><img src="people.gif"></li>
				</ul>
				<ul>
					<li><input type="checkbox" name="pics" value="1"><img src="people.gif"></li>
					<li><input type="checkbox" name="pics" value="1"><img src="people.gif"></li>
					<li><input type="checkbox" name="pics" value="1"><img src="people.gif"></li>
					<li><input type="checkbox" name="pics" value="1"><img src="people.gif"></li>
					<li><input type="checkbox" name="pics" value="1"><img src="people.gif"></li>
				</ul>
					
			</div>
			
			
			
			<!-- special buttons to add/remove/etc. -->
			<div id="contextButtons">
				<p class="right"><img src="trash.gif" name="delete"><br>delete</p>
				<div>
					<p class="left"><img src="cube.gif" name="cube"><br>create pic cube</p>
					<p class="left"><img src="album.gif" name="album"><br>add to album</p>
					<p class="left"><img src="upload-pic.gif" name="upload-pic"><br>upload pic</p>
					
				</div>
			</div>
		</div>
	</div>
	<!-- where the albums or whatever are held -->
	<div id="contextMenu">
		<div id="contextOptions">
			<ul>
				<li>all</li>
				<li>album</li>
				<li>album</li>
				<li>album</li>
				<li>album</li>
				<li>album</li>
				<li>album</li>
				<li>album</li>
				<li>album</li>
				<li>album</li>
				<li>album</li>
				<li>album</li>
				<li>album</li>
				<li>album</li>
				<li>album</li>
				<li>album</li>
				<li>album</li>
			</ul>
		</div>
	</div>
</div>