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

		<!--{if isset($application_group_selected)}-->
<!--{*
			<div>
				<form>
				  <fieldset>
				    <legend>Active Configuration:</legend>
						<div><span>Application: </span><!--{$app}--></div>
						<div><span>Interface Bundle: </span><!--{$bundle}--></div>
				  </fieldset>
				</form>
			</div>
*}-->
			<!--{if isset($applications_selected)}-->
				<!--{if isset($application_bundle_selected)}-->
					<div>
						<form>
						  <fieldset>
							<!--{foreach from=$applications_selected item=application}-->
								<!--{strip}-->
							    <legend>Active Configuration:</legend>
									<div><span>Application: </span><!--{$application|lower|capitalize}--></div>
									<div><span>Interface Bundle: </span><!--{$bundle}--></div>
								<!--{/strip}-->
							<!--{/foreach}-->

						  </fieldset>
						</form>
					</div>
					<br /><br />

					<form method="post">
						<input type="hidden" name="application_group_selected" value="<!--{$application_group_selected}-->">
						<input type="hidden" name="applications_selected_serialized" value="<!--{$applications_selected_serialized}-->">
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
				<!--{else}-->
					<div>
						<form>
						  <fieldset>
							<!--{foreach from=$applications_selected item=application}-->
								<!--{strip}-->
							    <legend>Active Configuration:</legend>
									<div><span>Application: </span><!--{$application|lower|capitalize}--></div>
								<!--{/strip}-->
							<!--{/foreach}-->

						  </fieldset>
						</form>
					</div>
					<br /><br />

					<form method="post">
						<input type="hidden" name="application_group_selected" value="<!--{$application_group_selected}-->">
						<input type="hidden" name="applications_selected_serialized" value="<!--{$applications_selected_serialized}-->">
						<span>Bundles:</span>
						<div>
							<input type="hidden" name="application_group_selected" value="<!--{$application_group_selected}-->">
							<select name="application_bundle_selected" <!--{if isset($multiple_bundles) && $multiple_bundles}-->multiple="true"<!--{/if}-->>
							<!--{foreach from=$bundles item=bundle}-->
								<!--{strip}-->
									<option value="<!--{$bundle}-->" >
										<!--{$bundle}-->
									</option>
								<!--{/strip}-->
							<!--{/foreach}-->
							</select>
						<div/>

						<br />
						<br />

						<input type="submit" name="submit" value="Submit" />
					</form>
				<!--{/if}-->
			<!--{else}-->
				<form method="post">
					<span>Applications:</span>
					<div>
						<input type="hidden" name="application_group_selected" value="<!--{$application_group_selected}-->">
						<select name="applications_selected[]" <!--{if isset($multiple) && $multiple}-->multiple="true"<!--{/if}-->>
						<!--{foreach from=$applications item=application}-->
							<!--{strip}-->
								<option value="<!--{$application.application_name}-->" >
									<!--{$application.application_name|lower|capitalize}-->
								</option>
							<!--{/strip}-->
						<!--{/foreach}-->
						</select>
					<div/>

					<br />
					<br />

					<input type="submit" name="submit" value="Submit" />
				</form>
			<!--{/if}-->

		<!--{else}-->
			<form method="post">
				<span>Application Groups:</span>
				<div>
					<select name="application_group_selected">
					<!--{foreach from=$application_groups item=application_group}-->
						<!--{strip}-->
							<option value="<!--{$application_group}-->" >
								<!--{$application_group|lower|capitalize}-->
							</option>
						<!--{/strip}-->
					<!--{/foreach}-->
					</select>
				<div/>

				<br />
				<br />

				<input type="submit" name="submit" value="Submit" />
			</form>
		<!--{/if}-->


	</body>
</html>
