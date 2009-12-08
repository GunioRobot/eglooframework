<!--{* @Name:   UserControlCenterContainer
     * @Author: George Cooper (george.cooper@egloo.com)
     *
     * @Description: This template represents the default structural format for an
     * eGloo basic user control center.  It is meant to be plugged into a larger
     * template structure and requires external CSS for styling and screen positioning.
     * Effort is made to make this file XHTML compliant for at least XHTML version
     * 1.0 Transitional.
     *
     * @Standalone: No
     * @Provides: Structural Markup, Unique Page Elements, CSS classes
     * @Caching: Partial
     * @Compatibility: XHTML 1.0 Transitional (Tested)
     *
     * @Token username (string) username of the account that this profile belongs to
     *}-->     

<div id="leftbar">
    <div id="Left_Block_3">
        <div id="Left_Column_Top"></div>
		<div id="LeftColumnTop"></div>
        <div id="Left_Column_Right_Section">

		    <!--{* @id: ControlCenterModules
		         *
		         * @Description:
		         *}-->

			<div id="ControlCenterModules">
				<div id="ControlCenterModule_ActionCube">
					<div id="ControlCenterModule_ActionCubeHandle" style="display:block;padding:3px;text-decoration:none;color:white;font-size:12px;background:transparent url(/images/aquaHeaderIcon.gif) 0 -1px repeat-x;">Actions</div>
					<div id="ControlCenterModule_ActionCubeContent">
						<div class="clickable" onclick="loadImageManager();">Image Manager</div>
						<!--{*<div onclick="loadRelationshipManager();">Relationship Manager</div>*}-->
					</div>
				</div>
				<div id="ControlCenterModule_FriendsCube">
                	<div id="controlCenterModule_FriendsCubeHandle" style="display:block;padding:3px;text-decoration:none;color:white;font-size:12px;background:transparent url(/images/aquaHeaderIcon.gif) 0 -1px repeat-x;">Friends</div>
                    <div id="controlCenterModule_FriendsCubeContent"></div>
				</div>
			</div>
            <div id="Left_Column_Fridge">
                <div id="fridgeTab" class="clickable" style="display:block;padding:3px;text-decoration:none;color:white;font-size:12px;background:transparent url(/images/aquaHeaderIcon.gif) 0 -1px repeat-x;">Fridge <span style="font-size:12pt;">▾</span></div>
                <div id="fridgeTray"></div>
            </div>      
        </div>
        <div id="LeftBottomControl">
        	<div class="BottomControlInnerTop">
        	</div>
        	<div class="BottomControlInner">
	        	<span id="scrolldiv_scrollUp" class="button clickable">▲</span>
	            <span id="scrolldiv_scrollDown" class="button clickable">▼</span>
			</div>
        </div>
    </div>
</div>