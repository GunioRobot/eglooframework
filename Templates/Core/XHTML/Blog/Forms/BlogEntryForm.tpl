<div id="newBlogEntryFormContainer">

	<form method="POST" action="/blog/newBlogEntry/">
		<br />
		<div>Title:</div>
		    <input id="newBlogEntryTitle"
		           type="text"
		           name="newBlogEntryTitle"
		           tabindex="1"
		           autocomplete="off"
		           class=""
		           maxlength="128"
		           size="12"
		           style="clear:both;" />
			<br /><br />
		<div>Content:</div>
		    <textarea rows="10" cols="57" id="newBlogEntryContent"
		           type="text"
		           name="newBlogEntryContent"
		           tabindex="2"
		           autocomplete="off"
		           class=""
		           style="clear:both;"></textarea>

	    <input id="newBlogEntrySubmit"
	           type="submit"
	           name="newBlogEntrySubmit"
	           tabindex="3"
	           autocomplete="off"
	           class=""
	           value="Submit"
	           style="position:absolute;bottom:10px;right:10px;" />
	</form>

</div>

<script language="javascript">
	$(document).ready(function() {
        $('#newBlogEntryFormContainer form').ajaxForm( {url:'/blog/newBlogEntry/', before:'', method:'POST',
            after:function(){$("#TB_closeWindowButton").click();$('#blogButton').click();}, dataType:'json' });
    });
</script>