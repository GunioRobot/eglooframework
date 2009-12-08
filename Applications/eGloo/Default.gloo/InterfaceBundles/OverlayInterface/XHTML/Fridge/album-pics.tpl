<link href="/css/fridgeStyles.css" rel="stylesheet" type="text/css" media="screen" />

<script language="javascript" src="/javascript/jquery.js"></script>
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
		<div id="availableCubes">
			<!-- list of cubes, pics, tunes, vids -->
			<div class="albumPics scrollable">
				<ul>
					<li><img src="people.gif"></li>
					<li><img src="people.gif"></li>
					<li><img src="people.gif"></li>
					<li><img src="people.gif"></li>
					<li><img src="people.gif"></li>
					<li><img src="people.gif"></li>
					<li><img src="people.gif"></li>
				</ul>
				<ul>
					<li><img src="people.gif"></li>
					<li><img src="people.gif"></li>
					<li><img src="people.gif"></li>
					<li><img src="people.gif"></li>
					<li><img src="people.gif"></li>
					<li><img src="people.gif"></li>
					<li><img src="people.gif"></li>
				</ul>
				<ul>
					<li><img src="people.gif"></li>
					<li><img src="people.gif"></li>
					<li><img src="people.gif"></li>
					<li><img src="people.gif"></li>
					<li><img src="people.gif"></li>
					<li><img src="people.gif"></li>
					<li><img src="people.gif"></li>
				</ul>
				<ul>
					<li><img src="people.gif"></li>
					<li><img src="people.gif"></li>
					<li><img src="people.gif"></li>
					<li><img src="people.gif"></li>
					<li><img src="people.gif"></li>
					<li><img src="people.gif"></li>
					<li><img src="people.gif"></li>
				</ul>
				<ul>
					<li><img src="people.gif"></li>
					<li><img src="people.gif"></li>
					<li><img src="people.gif"></li>
					<li><img src="people.gif"></li>
					<li><img src="people.gif"></li>
					<li><img src="people.gif"></li>
					<li><img src="people.gif"></li>
				</ul>
			</div>
			<!-- special buttons to add/remove/etc. -->
			<div id="contextButtons">
				<center>
				<p><img src="album.gif" name="album"><br>add to album</p>
				</center>
			</div>
		</div>
	</div>
	<!-- where the albums or whatever are held -->
	<div id="contextMenu">
		<div id="contextOptions">
			<ul>
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