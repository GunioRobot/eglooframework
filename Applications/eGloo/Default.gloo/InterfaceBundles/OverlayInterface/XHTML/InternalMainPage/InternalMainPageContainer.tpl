<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	    <title>eGloo | Welcome</title>
	    <!--{* Import statements for the CSS Styling *}-->
	    <link href="css/intMainRoot.css" rel="stylesheet" type="text/css" media="screen" />
	    <link href="css/fridgeStyles.css" rel="stylesheet" type="text/css" media="screen" />
	    
    	<script type="text/javascript" src="javascript/eGlooUtil.js"></script>
    	 
<!--{*    	<script type="text/javascript" src="/javascript/ifxPackRoot.js"></script> *}-->
    	<script type="text/javascript" src="javascript/internalMainInit.js"></script>
    	
    	
    </head>
    <body>
    	<div id="eGlooApplicationState">
    		<div id="eas_MainProfileID"><!--{$eas_MainProfileID}--></div>
    		<div id="eas_ActiveViewedProfileID"><!--{$eas_ViewingProfileID}--></div>
    	</div>

	   <!--{include file='XHTML/GlobalMenuBar/GlobalMenuBarContainer.tpl'}-->
	   <!--{include file='XHTML/InformationBoard/InformationBoardContainer.tpl'}-->
	   <!--{include file='XHTML/ProfileCenter/ProfileCenterContainer.tpl'}-->
 
	    <div id="cubeEditZone"></div>
	    <div id="ajaxUploadFrameContainer">
			<iframe id="ajaxUploadFrame" name="ajaxUploadFrame" style="display:none"></iframe>
	    </div>
	    <div id="hiddenCollapseZone" style="position:absolute;left:0;right:0;top:0;bottom:0;display:none;background-color:transparent;">&nbsp;</div>
    </body>
</html>