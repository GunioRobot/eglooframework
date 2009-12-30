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
		<h1>eGloo Application Cache Configuration</h1>

		<div>
			<form>
			  <fieldset>
			    <legend>Active Configuration:</legend>
					<div><span>Application: </span><!--{$app}--></div>
					<div><span>Interface Bundle: </span><!--{$bundle}--></div>
			  </fieldset>
			</form>
		</div>

		<br /><br />

		<form method="post">
			<span>Languages:</span>
			<div>
				<select name="languages_selected[]" multiple="true">
				<!--{foreach from=$languages item=language}-->
					<!--{strip}-->
						<option <!--{if in_array($language.code, $languages_selected)}-->selected<!--{/if}--> value="<!--{$language.code}-->" >
							<!--{$language.language|lower|capitalize}-->
						</option>
					<!--{/strip}-->
				<!--{/foreach}-->
				</select>
			<div/>

			<br />
			<br />

			<span>Countries:</span>
			<div>
				<select name="countries_selected[]" multiple="true">
					<!--{foreach from=$countries item=country}-->
						<!--{strip}-->
							<option <!--{if in_array($country.A2, $countries_selected)}-->selected<!--{/if}--> value="<!--{$country.A2}-->" >
								<!--{$country.country|lower|capitalize}-->
							</option>
						<!--{/strip}-->
					<!--{/foreach}-->
				</select>
			<div/>

			<input type="submit" name="submit" value="Submit" />
		</form>
	</body>
</html>
