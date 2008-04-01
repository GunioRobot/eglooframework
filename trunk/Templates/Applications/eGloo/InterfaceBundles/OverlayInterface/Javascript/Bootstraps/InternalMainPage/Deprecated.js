function rankCube( id, rank ){
	var percent = (rank + 1) * 20;  
	$("div.ratetip" + id + " " + 'table.ranking td').css({width: percent + 'px'}).css('background','url(/images/ranking/stars-on.gif) top left no-repeat');
	$.get('/ranking/rankInstance/&elmInstance=' + id +'&rank=' + rank);
}

function userRateBackground( num, elmID ){
	var elmname = "user-rate" + elmID;
	if( num == 0 ){
		$('#'+elmname).css('background','url(/images/ranking/stars-off.gif) top left no-repeat');
	}else{
		$('#'+elmname).css('background','none');
	}
}

//hide/show ranking stars
/*
var controlStripRank = $('#UserProfileControlStrip').find('div').get()[1];
$(controlStripRank).click(function() { 
	if(inRankingMode){
		$('#UserProfile div.userProfileCubeDragDropSort').find('div.ratetip').each(function() {$(this).hide("slow");}); 		
	} else {
		$('#UserProfile div.userProfileCubeDragDropSort').find('div.ratetip').each(function() {$(this).show("slow");});
	}
	inRankingMode = !inRankingMode;
});
*/
