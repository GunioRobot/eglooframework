<form target="ajaxUploadFrame" enctype="multipart/form-data" action="/image/uploadImage/" method="POST">
	<fieldset id="UserUploadNewImageFormFieldset" style="margin:15px 4px 8px 4px;padding:8px 4px 8px 4px;">
		<LEGEND ACCESSKEY=I>Upload New Image</LEGEND>
		<div>
		    <!--{* MAX_FILE_SIZE must precede the file input field *}-->
		    <input type="hidden" name="MAX_FILE_SIZE" value="3000000000" />
		    <!--{* Name of input element determines name in $_FILES array *}-->
		    <!--{*Image to Upload:*}-->
		</div>
		<input name="userfile" type="file" size="28" />
	    <input type="submit" value="Upload" style="margin-top:5px;" />
	</fieldset>
</form>
