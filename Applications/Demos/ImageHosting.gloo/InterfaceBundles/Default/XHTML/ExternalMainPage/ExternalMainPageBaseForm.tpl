<!--{* @Name:   ExternalMainPageBaseForm
     * @Author: George Cooper (george.cooper@egloo.com)
     *
     * @Description: This template represents the default structural format for the
     * eGloo external main page base form.  It is meant to be a stand-alone template 
     * structure and links in external CSS and Javascript for styling, screen positioning,
     * and dynamic functionality. Effort is made to make this file XHTML compliant for 
     * at least XHTML version 1.0 Transitional.
     *
     * @Standalone: Yes
     * @Provides: Structural Markup, Unique Page Elements, CSS Links, Javascript Links
     * @Caching: Yes
     * @Compatibility: XHTML 1.0 Transitional (Tested)
     *}-->     

<!--{* TODO
     * Because of Internet Explorer bugs in detecting standards versus quirks mode
     * based on the strictness of the DOCTYPE, this section will have to be revised at
     * a later point to be browser specific.  All associated CSS will have to be updated
     * to correctly account for the differences between quirks-mode and standards-mode
     * Internet Explorer rendering.
     *}-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	    <title>eGloo | Image Hosting Demo Service</title>
	    
<!--{*
	    <link href="/css/extMainRoot.css" rel="stylesheet" type="text/css" media="screen" />
	    
	    <script type="text/javascript" src="/javascript/jquery.js"></script>
		<script type="text/javascript" src="/javascript/form.js"></script>
	    <script type="text/javascript" src="/javascript/externalMainInit.js"></script>
*}-->
	</head>
    <body>
		<!-- <!--{$app}--> <!--{$bundle}--> -->
		Welcome to the eGloo Image Hosting Demo Service<br />
		<br />
		<form name="login" action="/account/processLogin/" method="post">
			Username: <input type="text" name="username" /><br />
			<br />
			Password: <input type="password" name="password" /><br />
			<input type="submit" value="Login" name="submit"/><br />
		</form>
		<br />
		<br />
		<br />
		<form name="register" action="/account/registerNewAccount/" method="post">
            <div class="labelcell">First Name:</div>
            <input id="firstNameInput" type="text" name="firstNameInput" />
            <div class="labelcell">Last Name:</div>
            <input id="lastNameInput" type="text" name="lastNameInput" />
            <span class="labelcell">Birthday:</span>
            <!--{html_select_date prefix="userBirth" start_year="-13" end_year="-106" reverse_years=true
            	time="--" day_value_format="%02d" day_format="%02d" month_extra="style=\"width:100px;\"" 
				day_extra="style=\"width:45px;\"" year_extra="style=\"width:60px;\""}-->
            <span class="labelcell">Gender:</span>
            <select id="userGender" name="userGender">
                <option value="Male">Male&nbsp;</option>
                <option value="Female" >Female&nbsp;</option>
            </select>
            <span class="labelcell">Email:</span>
            <input id="userEmail" type="text" name="userEmail"/>
            <div class="labelcell2">Preferred Username:</div>
            <input id="userPreferredAccountName" type="text" name="userPreferredAccountName"/>
            <div class="labelcell2">Account Password:</div>
            <input id="userRequestedAccountPassword" type="password" name="userRequestedAccountPassword"/>
            <div class="labelcell2">Confirm Password:</div>
            <input id="userConfirmedAccountPassword" type="password" name="userConfirmedAccountPassword"/>
		</form>
    </body>
</html>
