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
	    <title>eGloo | Welcome</title>
	    
	    <link href="css/extMainRoot.css" rel="stylesheet" type="text/css" media="screen" />
	    
	    <script type="text/javascript" src="javascript/jquery.js"></script>
		<script type="text/javascript" src="javascript/form.js"></script>
	    <script type="text/javascript" src="javascript/externalMainInit.js"></script>
	</head>
    <body>
		<div style="position:relative;width:100%;height:100%;">
	        <div id="container">
	            <div id="shell">
	                <div id="top">
	                    <div id="logo"><img src="images/logo.gif" alt="eGloo" width="304" height="138" /></div>
	                    <div id="form">
	                        <form name="login" action="/account/processLogin/" method="get" style="overflow:hidden;">
                                <input id="username" type="text" name="username" size="17" class="field" value="username"/>
                                <br />
                                <input id="password" type="text" name="password" size="17" class="field" value="password"/>	                         
	                            <div id="elements">
	                            	<div id="login">login</div>
		                            <span class="spantext">&nbsp;|&nbsp;</span>
		                            <div id="join">join</div>
		                            <span class="spantext">&nbsp;|&nbsp;</span>
		                            <a href="browse" id="browse">browse</a>
		                            <span class="spantext">&nbsp;|&nbsp;</span>
		                            <div id="tour">tour</div>
		                      	</div>
	                            <input type="submit" name="submit" style="display:none;" />
	                        </form>
	                    </div>
	                    <div class="halo">halo one: beta | svn revision: <!--{$svnVersion}--> </div>
	                    <div id="updates">
							<span class="footer_option link">eGloo Blog</span>
							<span class="spantext2">&nbsp;|&nbsp;</span>
		                    <span class="footer_option link">Latest Updates</span>
						</div>
						<div id="loadingImage"><img src="images/ajax-loader.gif" alt="Loading..." width="32" height="32" /></div>
						<div id="loginFailed"></div>
	                    <div id="forgotPassword" class="link">forgot password?</div>
<!--{*
	                    <div id="forgotPassword"><a href="/account/viewForgotAccountPasswordForm/">forgot password?</a></div>
*}-->
	                </div>
	            </div>
	            <div id="content">
	                <div id="content_text" style="color:black"></div>
	            </div>
	        </div>
	    </div>
        <div id="copyright">Copyright &copy; 2007 eGloo, LLC. All rights reserved.</div>
        <div id="footer">
			<span class="footer_option link">Terms of Use</span>
		   	<span class="spantext2">&nbsp;|&nbsp;</span>
			<span class="footer_option link">Privacy Policy</span>
			<span class="spantext2">&nbsp;|&nbsp;</span>
			<span class="footer_option link">Help</span>
			<span class="spantext2">&nbsp;|&nbsp;</span>
			<span class="footer_option link">Job Opportunities</span>
			<span class="spantext2">&nbsp;|&nbsp;</span>
			<span class="footer_option link">Media Info</span>
			<span class="spantext2">&nbsp;|&nbsp;</span>
			<span class="footer_option link">About Us</span>
			<span class="spantext2">&nbsp;|&nbsp;</span>
			<span class="footer_option link">Contact Us</span>
        </div>
    </body>
</html>
