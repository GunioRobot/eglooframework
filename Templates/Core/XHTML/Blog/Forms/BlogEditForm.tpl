<div id="editBlogEntryFormContainer">

	<form method="post" action="/blog/editBlogEntry/&blogID=<!--{$blogEntryID}-->">
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
		           style="clear:both;"
		           value="<!--{$blogEntryTitle}-->" />
			<br /><br />
		<div>Content:</div>
		    <textarea rows="10" cols="57" id="newBlogEntryContent"
		           type="text"
		           name="newBlogEntryContent"
		           tabindex="2"
		           autocomplete="off"
		           class=""
		           style="clear:both;"><!--{$blogEntryContent}--></textarea>

	    <input id="editBlogEntrySubmit"
	           type="submit"
	           name="editBlogEntrySubmit"
	           tabindex="3"
	           autocomplete="off"
	           class=""
	           value="Submit"
	           style="position:absolute;bottom:10px;right:10px;" />
	</form>

</div>

<script language="javascript">
	$(document).ready(function() {
        $('#editBlogEntryFormContainer form').ajaxForm(
        	{url:'/blog/editBlogEntry/&blogID=<!--{$blogEntryID}-->',dataType:'json',method:'POST',
        		before: function() {},
        		after: function() {$("#TB_closeWindowButton").click();$('#blogButton').click();}
        });
    });
</script>