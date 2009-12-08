<!--{* @Name:   ConfigureApplication.tpl
     * @Author: George Cooper (george.cooper@egloo.com)
     *
     * @Description: This template represents the default structural format for the
     * eGloo application configuration template.  It is meant to be a stand-alone template 
     * structure and links in external CSS and Javascript for styling, screen positioning,
     * and dynamic functionality. Effort is made to make this file XHTML compliant for 
     * at least XHTML version 1.0 Transitional.
     *
     * @Standalone: Yes
     * @Provides: Structural Markup, Unique Page Elements, CSS Links, Javascript Links
     * @Caching: Yes
     * @Compatibility: XHTML 1.0 Transitional (Tested)
     *}-->     

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>eGloo Administration | Configure Application</title>
	</head>
	<body>
		<h1>eGloo Application Configuration</h1>

		<div>
			<form>
			  <fieldset>
			    <legend>Active Configuration:</legend>
					<div><span>Application: </span><!--{$app}--></div>
					<div><span>Interface Bundle: </span><!--{$bundle}--></div>
			  </fieldset>
			</form>
		</div>

		<div>
			<span>Available Applications:</span>
			<select>
			<!--{foreach from=$applications item=application}-->
				<option value="<!--{$application.application_name}-->"><!--{$application.application_name}--></option>
			<!--{/foreach}-->
			</select>
		</div>
	</body>
</html>
