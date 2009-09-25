// Default

var appState = new Object();
var profileDOM = new Object();
var cubeIndex = new Object();
var serverEvents = new Object();
var inEditMode = null;
var inRankingMode = true;


function userRateBackground( num, elmID ){
	var elmname = "user-rate" + elmID;
	if( num == 0 ){
		$('#'+elmname).css('background','url(/images/ranking/stars-off.gif) top left no-repeat');
	}else{
		$('#'+elmname).css('background','none');
	}
}

function loadImageManager() {
    $('#cubeEditZone').fadeIn(1000, function(){
        $(this).load('/image/viewImageManager/');
    });
}

function loadRelationshipManager() {
    $('#cubeEditZone').fadeIn(1000, function(){
        $(this).load('/relationship/viewRelationshipManager/');
    });
}

function requestFriendship(profileID) {
    
}

function acceptFriendship(relationshipID) {
    $.ajax({type:'GET',url:( '/relationship/acceptRelationship/&relationshipID='+relationshipID)});
}

function rejectFriendship(relationshipID) {
    $.ajax({type:'GET',url:( '/relationship/declineRelationship/&relationshipID='+relationshipID)});
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
    var url = $('DynamicContent/BaseContentProcessor',params['xmlDef']).text()+'contentType=xhtml&cubeID='+
        $('DynamicContent/ContentID',params['xmlDef']).text()+'&contentID='+params['contentID'];
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
    
    cssLoad({xmlDef:xmlDef,contentID:'ContentViewContentStyle'});
    cubeIndex[elementID] = new Object();
    cubeIndex[elementID]['xmlDef'] = xmlDef;
    
    jsLoad( {xmlDef:xmlDef,contentID:'InitFunc',onComplete:function(jsonVal) {
            cubeIndex[elementID]['InitFunc'] = jsonVal.initFunc;
            $('#' + elementID).find('div.userProfileCubeDragDropSort').attr("id",'UserProfileCube_' + 
                $('Element/TypeID',xmlDef).text() + '_' +  $('Element/InstanceID',xmlDef).text() ).Grow(500,function() {
                    var cubeContent = $(this).find('div.userProfileCubeContent');
                    cubeContent.hide();
                    htmlLoad({target:cubeContent,xmlDef:xmlDef,contentID:'ContentViewContent',onComplete: 
                        function() {
                            cubeContent.fadeIn(500, function() {
                                cubeIndex[elementID]['InitFunc']($('#' + elementID),
                                    {cubeID:elementID,profileID:appState['VIEWING_PROFILE_ID']});
                            });
                        }
                    });
            });
            
            if ( appState['VIEWING_PROFILE_ID'] == appState['MAIN_PROFILE_ID'] ) {
                $('#UserProfile ul.sortable').SortableAddItem($('#' + elementID).get()[0]);
                $($('#UserProfileControlStrip').find('div').get()[3]).click();
            } else {
                $('#UserProfile div.userProfileCubeDragDropSort').find('.userProfileCubeCloseTrigger').remove().end(
                ).find('.userProfileCubeEditTrigger').remove().end().find('.userProfileCubeMinimizeTrigger').click(
                    function() {
                        $(this).parents('.userProfileCubeDragDropSort').find('.userProfileCubeContent').toggle().end();
                    }
               );
            }

            if (params['onComplete'] != null) params['onComplete']();
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
            rebuildArgs['newID'] = 'ElementContainer' + '_' + $('Element/TypeID',params['xmlDef']).text() + 
                '_' + $('Element/InstanceID',params['xmlDef']).text();; //ElementContainer
            rebuildArgs['addClass'] = 'elementContainer';

            rebuild(rebuildArgs);
            htmlLoad({target:'#'+rebuildArgs['newID'],xmlDef:params['xmlDef'],contentID:'ContentViewFrame',onComplete:
				function() {
					 if (params['type'] == 'cube') {
            			buildCube(elementID,params['xmlDef'],params);
        			} else if (params['type'] == 'cubeprefs') {
            			buildCubePrefs(elementID,params['xmlDef'],params);
        			}
				}
			});
            elementID = rebuildArgs['newID'];
        }
        

    }    
}

function buildCubePrefs(elementID,xmlDef,params) {
    htmlLoad({target:$('#'+elementID),xmlDef:xmlDef,contentID:'PreferencesViewContent',onComplete:
        function() {
            jsLoad({xmlDef:xmlDef,contentID:'InitPrefsFunc',onComplete:
                function(jsonVal) {
                    jsonVal({el:('#'+elementID),profileID:appState['MAIN_PROFILE_ID'],
                        onComplete:params['onComplete']});
                }
            });
        }
    });
}

function buildProfilePageElements(profileID,xmlDef,done) {
    if (xmlDef == null && (done == false || done == null)) {
        $.ajax({
            type:'GET',
            url:('/profile/getUserProfilePageElements/&profileID='+profileID+'&format=raw'),
            success: function(xmlDef){buildProfilePageElements(profileID,xmlDef,true);}
        });
    } else {
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
            attachEditHandlers();
        }
        var sets = xmlDef.match(/\|\|[^|]*?\|\|/g);
        for ( var i = 0; sets != null && i < sets.length; i++ ) {
            var setID = sets[i].match(/\|\|(.*?)\[\]=/)[1];
            var obj = sets[i].slice(2,-2).replace(/(.*?)=(.*?)(&|$)/g,'\'$2\',').replace(/,$/,'');
            center3Array = eval( 'new Array( ' + obj + ' );');
            for( el in center3Array ) {
                $('#' + setID).append('<li id=' + center3Array[el] + '></li>');
                var match = center3Array[el].match(/_(-?[0-9]*)$/);
                buildDynamicContent({elementID:center3Array[el],url:'/cube/getCubeElementInstance/cubeID=' + match[1],type:'cube',replaceEl:true});
            }
        }
    }
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
    for ( id in profileDOM )
        profileDOMString += profileDOM[id] != '' ? '||' + profileDOM[id] + '||' : '';

    $.cookie('userProfileCookie', profileDOMString, {expires: 7,path:'/'});
    /*<!--{* FIX Possible Security Hole *}-->*/
    $.ajax({type:'POST',url:( '/profile/updateUserProfilePage/&profileID='+appState['MAIN_PROFILE_ID'] ),dataType:'xml'});    
}

/*<!--{* TODO change this to use the current viewed profile ID, not the main profile ID *}-->*/

function cubeLoop() {
    for (element in cubeIndex)
        cubeIndex[element]['InitFunc']($('#' + element),{cubeID:element,profileID: appState['VIEWING_PROFILE_ID']});
}

function attachEditHandlers() {
    var edit = $('#UserProfileControlStrip').find('div').get()[3];
    $(edit).click(function() {
        $('#UserProfile div.userProfileCubeDragDropSort').find('div.dragDropHandle .userProfileCubeEditTrigger').each(function() {
            $(this).show();
        }).end().find('.userProfileCubeCloseTrigger').unbind("click").click(function() {
/*<!--{*            closeMode = $(this).parents('div.userProfileCubeDragDropSort').id();
            closeMode = closeMode.replace(/^UserProfileCube_-?[0-9]*?_\w*?_/,'');
            $.ajax({type:'GET',url:( '/cube/deleteCubeElementInstance/&cubeID='+closeMode ),dataType:'xml',
                success: function() {
                    $(this).parents('li').Puff(500, function() {$(this).remove();});parseProfileDOM();}
            });*}-->*/ 
        }).end().find('.userProfileCubeEditTrigger').unbind("click").click(function() {
            $(this).parents('div.userProfileCubeDragDropSort').fadeOut(500).TransferTo({to:'cubeEditZone',className:'transferer2', duration: 400});
            inEditMode = $(this).parents('.elementContainer').attr("id");

            $('#cubeEditZone').fadeIn(500,function(){
                buildCubePrefs('cubeEditZone',cubeIndex[inEditMode]['xmlDef'],{ onComplete:
                   function() {
                        $('#cubeEditZone').fadeOut(500).TransferTo({to:inEditMode,className:'transferer2', duration: 400}).hide();
                        $('#' + inEditMode).find('div.userProfileCubeDragDropSort').fadeIn(500);
                   }
                });
            });
        });
    });
    var portrait = $('#UserProfileControlStrip').find('div').get()[4];
    $(portrait).click(function() {
        $('#UserProfile div.userProfileCubeDragDropSort').find('div.dragDropHandle .userProfileCubeEditTrigger').each(function() {
            $(this).hide();
        });
    });
}

$(document).ready(function() {
	
	//hide/show ranking stars
	var controlStripRank = $('#UserProfileControlStrip').find('div').get()[1];
    $(controlStripRank).click(function() { 
		if(inRankingMode){
			$('#UserProfile div.userProfileCubeDragDropSort').find('div.ratetip').each(function() {$(this).hide("slow");}); 		
		} else {
			$('#UserProfile div.userProfileCubeDragDropSort').find('div.ratetip').each(function() {$(this).show();});
		}
		inRankingMode = !inRankingMode;
	 });
		

    $('#globalMenuMiddle > div.globalMenuButton').click(
        function () {
            if ($(this).attr('egloo:selected')) {
                $('#informationBoardForeground').hide();
                $('#dropdown').fadeTo(1,0,function(){$(this).hide();});
                $(this).css('background','#0D65AD').removeAttr('egloo:selected');
            } else {                
                $('#dropdown').fadeTo(1,0.95, function() {
                    /*<--{* FIX this needs to be centered properly *}-->*/
                    $("#dropDownContent").html("<div style='position:absolute;left:350px;top:150px;'><img src='/images/loadingAnimation.gif' /></div>");
                    $('#informationBoardForeground').show();
                });
                $(this).siblings('//[@egloo:selected]').css('background','#0D65AD').removeAttr('egloo:selected').end(
                    ).attr('egloo:selected','true').css('background','#2171B2');
                $('#dropDownContent').load('/infoBoard/viewInfoBoard/gMBID=' + this.id, function(){$('#informationBoardForeground').show();});
            }
    });
    $('#fridgeTab').toggle(
        function () {
	        $('#ControlCenterModules').fadeOut(500);
            var newTop = $('#Left_Column_Fridge').get(0).offsetTop;
            $('#Left_Column_Fridge').css('top',(newTop + 'px')).animate({top:0},1000,'bounceout', 
                function() {
                    $('#Left_Column_Fridge').css('top',0);
                    $('#fridgeTray').load('/fridge/showFridge/' + this.id, function() {
                        $('#fridgeTray ul.sortable').Sortable(
                            {
                                accept: 'fridgeLI',
                                opacity: 0.75,
                                fit: true,
                                fx: 200,
                                helperclass: 'blhelper',
                                revert: true,
                                onOut: function(draggable) {draggable.style.background = 'white'},
                                onStop: function() {
                                    if ( $(this).parents('#fridgeTray').length == 0 ) {
                                        $(this).removeClass('fridgeLI');
                                        var match = this.id.match(/_(-?[0-9]*)$/);
                                        buildDynamicContent({
                                            elementID:this.id,
                                            url:'/cube/getNewCubeElementInstance/cubeID=' + match[1],
                                            type:'cube',
                                            replaceEl:true,
                                            onComplete:function(){parseProfileDOM();}});
                                    }
                                }
                            });
                    }).show();
            });
        },
        function () {
            $('#ControlCenterModules').fadeIn(500);
            var newTop = $('#Left_Column_Right_Section').get(0).offsetHeight - $(this).get(0).offsetHeight;
            $('#fridgeTray').hide();
            $('#Left_Column_Fridge').animate({top:newTop},{duration:1000,easing:'easeout'}, function() {
                $('#Left_Column_Fridge').css('top','');
            });
        }
    );
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
    $('#GlobalMenuBar_User_Fullname').click( function() {
        buildProfilePage(appState['MAIN_PROFILE_ID']);
    });
    $('#blogButton').click();

	$('.menuSearchItem > input').keyup(function(event){
/*<!--{*
        // event.keyCode is UTF-8 keyCode.  use String.fromCharCode()
	    // and a regex match using unicode character classes to check
	    // input.  match server side
*}-->*/
	    if ( $(this).val().length >= 3 ) {
  	       $.ajax({method:'GET',url:'/search/searchByName/&nameSearchParam='+$(this).val(),success:function(xml) {
  	           //find('#Menu_1_Header')
  	           //.width('666px') requires re-centering code to work
  	           $('#dropdown').height( '75px' ).fadeTo(500,0.80);
  	       }});
	    }
	});
	listFriends();
	getFriendRequestCount();
    setInterval(cubeLoop,60000);    
});

function grabProfileSummary(el,profileID) {
    $(el).load('/infoBoard/viewPeopleUserProfile/&profileID='+profileID);
}