
// Default



var appState = new Object();
var profileDOM = new Object();
var cubeIndex = new Object();
var serverEvents = new Object();
var inEditMode = null;
var inRankingMode = true;

function selectSize(){
	if( $("#size").val() != "" ){
		var o = "<font size= " + $("#size").val() + ">";
		encloseSelection(o, '</font>');
	}
}
function myokfunc(){
	var o = "[color: " + $("#myhexcode").val() + "]";
	encloseSelection(o, '[/color]');
}
function clearForm(){
	$("#msgpost").val("");
}

function userRateBackground( num, elmID ){
	var elmname = "user-rate" + elmID;
	if( num == 0 ){
		$('#'+elmname).css('background','url(/images/ranking/stars-off.gif) top left no-repeat');
	}else{
		$('#'+elmname).css('background','none');
	}
}



function invokeOverlay( keyword, myCaller ) {

	if ($(myCaller).attr('egloo:selected')) {
		$('#informationBoardForeground').hide();
        $('#dropdown').fadeTo(1,0,function(){$(this).hide();});
        $(myCaller).removeAttr('egloo:selected');
		$('#hiddenCollapseZone').hide().unbind();
    } else {                
        $('#dropdown').fadeTo(1,0.95, function() {
            /*<--{* FIX this needs to be centered properly //*}-->*/
            $("#dropDownContent").html("<div style='position:absolute;left:350px;top:150px;'><img src='/images/loadingAnimation.gif' /></div>");
            $('#informationBoardForeground').show();
        });
		
       $(myCaller).attr('egloo:selected','true'); 
       $('#dropDownContent').load('/infoBoard/' + keyword + '/', function(){$('#informationBoardForeground').show();});
	   $('#hiddenCollapseZone').show().click( function () {
	   		invokeOverlay( keyword, myCaller );
	   });
    }	
}


  function encloseSelection(prefix, suffix) {
	var textarea = document.getElementById("wikitext");
    textarea.focus();
    var start, end, sel, scrollPos, subst;
    if (typeof(document["selection"]) != "undefined") {
      sel = document.selection.createRange().text;
    } else if (typeof(textarea["setSelectionRange"]) != "undefined") {
      start = textarea.selectionStart;
      end = textarea.selectionEnd;
      scrollPos = textarea.scrollTop;
      sel = textarea.value.substring(start, end);
    }
    if (sel.match(/ $/)) { // exclude ending space char, if any
      sel = sel.substring(0, sel.length - 1);
      suffix = suffix + " ";
    }
    subst = prefix + sel + suffix;
    if (typeof(document["selection"]) != "undefined") {
      var range = document.selection.createRange().text = subst;
      textarea.caretPos -= suffix.length;
    } else if (typeof(textarea["setSelectionRange"]) != "undefined") {
      textarea.value = textarea.value.substring(0, start) + subst +
                       textarea.value.substring(end);
      if (sel) {
        textarea.setSelectionRange(start + subst.length, start + subst.length);
      } else {
        textarea.setSelectionRange(start + prefix.length, start + prefix.length);
      }
      textarea.scrollTop = scrollPos;
    }
  }

function getHeight( d ){
	var retval = 0;
	if(d.offsetHeight){
		retval = d.offsetHeight;
    }else if(d.style.pixelHeight){
        retval = d.style.pixelHeight;
    }
    return retval;
}

function setArchiveBackground(){
	var thisHeight = getHeight( document.getElementById("blogNav") );
	$("#blogNavBackground").css("height", (thisHeight - 3));
    $("#blogNav").css("margin-top", ((thisHeight * -1)));
}

$(document).ready(function() {
	$("#right li.archive").bind("click",function(){
		if( $("#" + $(this).html().split(" (")[0]).css("display") == "none" ){
			$("#" + $(this).html().split(" (")[0]).show("medium", function(){
				setArchiveBackground();
			});
			$(this).css("list-style","url(arrow-down.gif)");
		}else{
			$("#" + $(this).html().split(" (")[0]).hide("medium", function(){
				setArchiveBackground();
			});
			$("#" + $(this).html().split(" (")[0] + " ul").hide();
			$(this).css("list-style","url(arrow-right.gif)");
			$("#" + $(this).html().split(" (")[0]).children("li").css("list-style","url(arrow-right.gif)");
		}	
	});
	
	//make blog have white tranny background
    if( document.getElementById("blogContent") != null ){
	    var thisHeight = getHeight( document.getElementById("blogContent") );
	    $("#blogContentBackground").css("height", (thisHeight - 20));
	    $("#blogContent").css("margin-top", ((thisHeight * -1) - 1));
    }
	setArchiveBackground();
//	$.ColorPicker.init();
	
});