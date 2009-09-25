
// Default

var appState = new Object();
var profileDOM = new Object();
var cubeIndex = new Object();
var serverEvents = new Object();
var inEditMode = null;
var inRankingMode = true;

appState.overlay = new Object();

function requestFriendship(profileID) {
    
}

function acceptFriendship(otherProfileID, relationshipType) {
    $.ajax({type:'GET',url:( '/relationship/acceptRelationship/&requesterProfileID='+otherProfileID + '&relationshipType=' + relationshipType)});
}

function rejectFriendship(otherProfileID, relationshipType) {
    $.ajax({type:'GET',url:( '/relationship/declineRelationship/&requesterProfileID='+otherProfileID + '&relationshipType=' + relationshipType)});
}

function removeFriendship(relationshipID) {
    $.ajax({type:'GET',url:( '/relationship/removeRelationship/&relationshipID='+relationshipID)});
}

function listFriendRequests(profileID) {
    
}

function listFriends(profileID) {
    $('#controlCenterModule_FriendsCubeContent').load('/relationship/getAllRelationships/', function() {});
}

function getFriendRequestCount(profileID) {
    $.getJSON('/relationship/getAllRelationshipRequests/&retVal=count',
        function(json){
            appState['numberAlerts'] = json.relationshipRequestsCount;
            $('#alertButton').html('Alerts ('+appState['numberAlerts']+')');        }
    );
}

function cssLoad(params) {
    $.plg.cssBase = '';
    var url = $('DynamicContent/BaseContentProcessor',params['xmlDef']).text()+'contentType=css&cubeID='+
        $('DynamicContent/ContentID',params['xmlDef']).text()+'&contentID='+params['contentID'];
    $.requirecss(url);
}

function htmlLoad(params) {
	var url = "/dynamicContent/getCubeContent/contentType=xhtml&cubeID=" + params['cubeID'] + "&contentID=" + params['contentID'];
	$(params['target']).load(url,params['onComplete']);
}

function jsLoad(params) {
    var url = $('DynamicContent/BaseContentProcessor',params['xmlDef']).text()+'contentType=js&cubeID='+
        $('DynamicContent/ContentID',params['xmlDef']).text()+'&contentID='+params['contentID'];
    $.getJSON(url,function(json){params['onComplete'](json);});
}

function rebuild(params) {
    var newID = params['newID'];
    var oldID = params['oldID'];
    var addClass = params['addClass'];

    $('#'+oldID).clone().empty().attr("id",newID + '_tmp').addClass(addClass).insertAfter($('#'+oldID)).end().remove();
    $('#'+newID+'_tmp').attr("id",newID);
}

function buildCube(elementID,xmlDef,params) {
	
    cubeIndex[elementID] = new Object();
    cubeIndex[elementID]['xmlDef'] = xmlDef;
    
    jsLoad( {xmlDef:xmlDef,contentID:'AllFunc',onComplete:function(jsonVal) {
		
            cubeIndex[elementID]['InitFunc'] = jsonVal.initFunc;
			cubeIndex[elementID]['prefViewInitFunc'] = jsonVal.prefViewInitFunc;
			cubeIndex[elementID]['contViewUpFunc'] = jsonVal.contViewUpFunc;
			
			var cubeLoadTarget = '#' + elementID;
			$(cubeLoadTarget).hide();
			htmlLoad({target:cubeLoadTarget,cubeID:$('DynamicContent/ContentID',params['xmlDef']).text(),contentID:'WholeCube',onComplete:
				function() {
					$('#' + elementID).find('div.userProfileCubeDragDropSort').attr("id",'UserProfileCube_' + 
	                $('Element/InstanceID',xmlDef).text() ).each(function() {
	                			cubeIndex[elementID]['InitFunc']($('#' + elementID), {cubeID:elementID,profileID:appState['VIEWING_PROFILE_ID']});
	                		});
	            	if ( appState['VIEWING_PROFILE_ID'] == appState['MAIN_PROFILE_ID'] ) {
	                	$('#UserProfile ul.sortable').SortableAddItem($('#' + elementID).get()[0]);
						 attachEditHandlers();
	                	//$($('#UserProfileControlStrip').find('div').get()[3]).click();
	            	} else {
	                	$('#UserProfile div.userProfileCubeDragDropSort').find('.userProfileCubeCloseTrigger').remove().end(
	                	).find('.userProfileCubeEditTrigger').remove().end().find('.userProfileCubeMinimizeTrigger').click(function() {
	                        $(this).parents('.userProfileCubeDragDropSort').find('.userProfileCubeContent').toggle().end();
	                    });
					}
					$(cubeLoadTarget).fadeIn(500, function(){});
					if (params['onComplete'] != null) params['onComplete']();
				}			
			});
        }
    });
}

function buildDynamicContent(params) {
    if (params['xmlDef'] == null && (params['done'] == false || params['done'] == null)) {
        $.ajax({
            type:'GET',
            url: ( params['url'] != null ? params['url'] : null),
            dataType:'xml',
            success: function(xmlDef){params['xmlDef']=xmlDef;buildDynamicContent(params);}
        });
    } else {
        var elementID = params['elementID'];
        
        if ($('Element',params['xmlDef']) != null) {
            var rebuildArgs = new Object();
            rebuildArgs['oldID'] = elementID;
            rebuildArgs['newID'] = 'ElementContainer' + '_' + $('Element/InstanceID',params['xmlDef']).text();; //ElementContainer
            rebuildArgs['addClass'] = 'elementContainer';

            rebuild(rebuildArgs);
			elementID = rebuildArgs['newID'];
			if (params['type'] == 'cube') {
        		buildCube(elementID,params['xmlDef'],params);
    		} else if (params['type'] == 'cubeprefs') {
				buildCubePrefs(elementID,elementID,params);
    		}
		}
    }    
}

function buildCubePrefs(targetArea, elementID, params) {	
    htmlLoad({target:$('#'+targetArea),cubeID:elementID.substring(17),contentID:'PreferencesViewContent',onComplete:
		function(){
			cubeIndex[elementID]['prefViewInitFunc']({el:('#'+targetArea),profileID:appState['MAIN_PROFILE_ID'], onComplete:params['onComplete']});
		}
    });
}

function buildProfilePageElements(profileID,xmlDef,done) {
	
	if ( appState['VIEWING_PROFILE_ID'] == appState['MAIN_PROFILE_ID'] ) {
        $('#UserProfile ul.sortable').Sortable({
            accept: 'dragdropsort',
            handle: 'div.dragDropHandle',
            opacity: 0.75,
            fit: true,
            /*fx: 5500,*/
            floats: true,
            helperclass: 'blhelper',
            onStop:function() { parseProfileDOM() },
            revert: true
		});
	}

	
	$("div.userProfileCubeDragDropSort").each(function(){
	
		var cubeID = this.id.substring(16);
		var url = "/dynamicContent/getCubeContent/contentType=js&cubeID=" + cubeID + "&contentID=AllFunc";
		var elementID = "ElementContainer_" + cubeID;

		//get the javascript for the cubes that have been loaded.
		$.getJSON(url,function(json){
			cubeIndex[elementID] = new Object();
        	cubeIndex[elementID]['InitFunc'] = json.initFunc;
			cubeIndex[elementID]['prefViewInitFunc'] = json.prefViewInitFunc;
			cubeIndex[elementID]['contViewUpFunc'] = json.contViewUpFunc;
			
			//once the javascript is loaded, attach the handles			
			if ( appState['VIEWING_PROFILE_ID'] == appState['MAIN_PROFILE_ID'] ) {
	            attachEditHandlers();
				//$($('#UserProfileControlStrip').find('div').get()[3]).click();
			}
		});
	});	
}

function buildProfilePage(profileID,done) {
    if (done == false || done == null) {
        $('#eas_ActiveViewedProfileID').html( profileID );
        appState['VIEWING_PROFILE_ID'] = profileID;
        $('#UserProfile').load('/profile/viewUserProfilePage/&profileID=' + profileID, function() {
            buildProfilePage(profileID,true);
        }); 
    } else {
        buildProfilePageElements(profileID);
    }
}

function parseProfileDOM() {
    profileDOM = new Object();
    $('#UserProfile ul.sortable').each( function() {
        profileDOM[this.id] = $.SortSerialize(this.id).hash;
    });
    
    var profileDOMString = '';
    for ( id in profileDOM ) {
        profileDOMString += profileDOM[id] != '' ? '||' + profileDOM[id] + '||' : '';
	}
   // $.cookie('userProfileCookie', profileDOMString, {expires: 7,path:'/'});
    /*<!--{* FIX Possible Security Hole *}-->*/
	$.post("/profile/updateUserProfilePage/", { pageLayout: profileDOMString } );
   // $.ajax({type:'POST',url:( '/profile/updateUserProfilePage/&pageLayout='+profileDOMString ),dataType:'xml'});    
}

/*<!--{* TODO change this to use the current viewed profile ID, not the main profile ID *}-->*/

function cubeLoop() { for (element in cubeIndex) refreshCube(element); }

function refreshCube(element){ if(cubeIndex[element]['contViewUpFunc'] != null) cubeIndex[element]['contViewUpFunc']($('#' + element),{cubeID:element,profileID: appState['VIEWING_PROFILE_ID']}); }

function attachEditHandlers() {
	/*
	var edit = $('#UserProfileControlStrip').find('div').get()[3];
    $(edit).click(function() {
	*/	
	
        $('#UserProfile div.userProfileCubeDragDropSort').find('div.dragDropHandle .userProfileCubeEditTrigger').each(function() {
            $(this).show();
        }).end().find('.userProfileCubeCloseTrigger').unbind("click").click(function() {
			delete cubeIndex[ $(this).parents('.elementContainer').attr("id") ];
			$(this).parents('li').fadeOut(500, function() {$(this).remove(); parseProfileDOM();});
        }).end().find('.userProfileCubeEditTrigger').unbind("click").click(function() {
            $(this).parents('div.userProfileCubeDragDropSort').fadeOut(500).TransferTo({to:'cubeEditZone',className:'transferer2', duration: 400});
            inEditMode = $(this).parents('.elementContainer').attr("id");

            $('#cubeEditZone').fadeIn(500,function(){
               buildCubePrefs('cubeEditZone', inEditMode,{ onComplete:
                   function() {
                        $('#cubeEditZone').fadeOut(500).TransferTo({to:inEditMode,className:'transferer2', duration: 400}).hide();
                        $('#' + inEditMode).find('div.userProfileCubeDragDropSort').fadeIn(500);
						refreshCube(inEditMode);
						$('#cubeEditZone').empty();
                   }
                });
            });
        });
	/*
    });
	
    var portrait = $('#UserProfileControlStrip').find('div').get()[4];
    $(portrait).click(function() {
        $('#UserProfile div.userProfileCubeDragDropSort').find('div.dragDropHandle .userProfileCubeEditTrigger').each(function() {
            $(this).hide();
        });
    });
    */
}

function invokeOverlay( keyword, myCaller, args ) {

	args = ( typeof args == 'undefined' || typeof args !== 'string' ) ? '' : args;

	if ( myCaller === 'HIDE_OVERLAY' || appState.overlay.lastCaller === myCaller ) {
		if (appState.overlay.visible === true) {
			$('#informationBoardForeground').hide();
			$('#dropdown').fadeTo(1, 0, function(){
				$(this).hide();
			});
			$('#hiddenCollapseZone').hide().unbind();
		}
		
		appState.overlay = {lastCaller:null, visible:false};
    } else {                
        $('#dropdown').fadeTo(1,0.95, function() {
            /*<--{* FIX this needs to be centered properly //*}-->*/
            $("#dropDownContent").html("<div style='position:absolute;left:350px;top:150px;'><img src='/images/loadingAnimation.gif' /></div>");
            $('#informationBoardForeground').show();
        });

		appState.overlay.visible = true;
		$('#dropDownContent').load('/infoBoard/' + keyword + '/' + args, function() {
			/*<!--{* Make sure we're still visible when the server returns its result *}-->*/
			if ( appState.overlay.visible ) {
				$('#informationBoardForeground').show();
			}
		});
		$('#hiddenCollapseZone').show().click( function () {
			invokeOverlay( keyword, 'HIDE_OVERLAY', args );
		});

		appState.overlay.lastCaller = ( myCaller === 'SHOW_OVERLAY' ) ? null : myCaller;
    }

}

$(document).ready(function() {
	
    $('#alertButton').click( function() {
        $('#CommCenterContent').load('/relationship/getAllRelationshipRequests/', function() {
            $(this).fadeIn(500);
        });
    });
    $('#blogButton').click( function() {
        $('#CommCenterContent').load('/blog/viewBlogEntryListEditable/', function() {
            TB_init();
            $(this).fadeIn(500);
        });
    });
    $('#messageButton').click( function() {
        $('#CommCenterContent').load('/message/getMessageList/', function() {
            $(this).fadeIn(500);
        });
    });
    appState['MAIN_PROFILE_ID'] = $('#eGlooApplicationState').find('#eas_MainProfileID').text();
    appState['VIEWING_PROFILE_ID'] = $('#eGlooApplicationState').find('#eas_ActiveViewedProfileID').text();
    buildProfilePageElements(appState['MAIN_PROFILE_ID']);
    $('#blogButton').click();

	$('.menuSearchItem > input').keyup(function(event){
/*<!--{*
        // event.keyCode is UTF-8 keyCode.  use String.fromCharCode()
	    // and a regex match using unicode character classes to check
	    // input.  match server side
*}-->*/
	    if ( $(this).val().length >= 3 ) {
			invokeOverlay( 'searchPeople', 'SHOW_OVERLAY', 'name=' + $(this).val() );
	    } else {
			invokeOverlay( 'searchPeople', 'HIDE_OVERLAY' );
		}
	}).blur(function(){
		$(this).val('');
	});
	listFriends();
	getFriendRequestCount();
    setInterval(cubeLoop,60000);    
});

function grabProfileSummary(el,profileID) {
    $(el).load('/infoBoard/viewPeopleUserProfile/&profileID='+profileID);
}