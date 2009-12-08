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
<link href="fridge-styles.css" rel="stylesheet" type="text/css" title="default" media="screen" />
		
<div id="eGlooFridge">
	<!-- where all the picture, images, etc. are held -->
	<div id="contentContainer">
		<!-- menu bar -->
		<ul id="menuButtons">
			<li name="messages" class="on">messages</li>
			<li name="alerts">alerts</li>
			<li name="more">more</li>
			<li name="more" class="last">more</li>
		</ul>
		<!-- main content -->
		<div id="content">
			<!-- list of cubes, pics, tunes, vids -->
			<p class="inner">unread&nbsp;&nbsp;read&nbsp;&nbsp;etc.&nbsp;&nbsp;etc.</p>
			<div class="inbox scrollable">			
				<ul>
					<li><input type="checkbox" name="tunes" value="1"><p><strong>Mark</strong> Wow - <b>Siiiiiiick</b></p></li>
					<li><input type="checkbox" name="tunes" value="1"><p><strong>Mark</strong> Good lord this song rox - <b>description...</b></p></li>
					<li><input type="checkbox" name="tunes" value="1"><p><strong>Mark</strong> Subject... - <b>Description...</b></p></li>
					<li><input type="checkbox" name="tunes" value="1"><p><strong>Mark</strong> Subject... - <b>Description...</b></p></li>
					<li><input type="checkbox" name="tunes" value="1"><p><strong>Mark</strong> Subject... - <b>Description...</b></p></li>
					<li><input type="checkbox" name="tunes" value="1"><p><strong>Mark</strong> Subject... - <b>Description...</b></p></li>
					<li><input type="checkbox" name="tunes" value="1"><p><strong>Mark</strong> Subject... - <b>Description...</b></p></li>
					<li><input type="checkbox" name="tunes" value="1"><p><strong>Mark</strong> Subject... - <b>Description...</b></p></li>
					<li><input type="checkbox" name="tunes" value="1"><p><strong>Mark</strong> Subject... - <b>Description...</b></p></li>
					<li><input type="checkbox" name="tunes" value="1"><p><strong>Mark</strong> Subject... - <b>Description...</b></p></li>
					<li><input type="checkbox" name="tunes" value="1"><p><strong>Mark</strong> Subject... - <b>Description...</b></p></li>
					<li><input type="checkbox" name="tunes" value="1"><p><strong>Mark</strong> Subject... - <b>Description...</b></p></li>
					<li><input type="checkbox" name="tunes" value="1"><p><strong>Mark</strong> Subject... - <b>Description...</b></p></li>
					<li><input type="checkbox" name="tunes" value="1"><p><strong>Mark</strong> Subject... - <b>Description...</b></p></li>
				</ul>				
			</div>
			<!-- special buttons to add/remove/etc. -->
			<div id="contextButtons">
				<p class="right"><img src="trash.gif" name="delete"><br>delete</p>
				<div>
					<p class="left"><img src="upload-pic.gif" name="upload-pic"><br>compose</p>
					<p class="left"><img src="album.gif" name="album"><br>move to folder</p>
				</div>
			</div>
		</div>
	</div>
	<!-- where the albums or whatever are held -->
	<div id="contextMenu">
		<div id="contextOptions">
			<ul>
				<li>inbox</li>
				<li>sent</li>
				<li>drafts</li>
				<li>etc.</li>
				<li>etc.</li>
			</ul>
		</div>
	</div>
</div>