function(cube,params) {
	$('#'+params['cubeID']).find('.userProfileCubeContent').load(
		'/dynamicContent/getCubeContent/contentType=xhtml&cubeID=<!--{$cubeElementInstanceID}-->&contentID=ContentViewContent', function() {
		$(this).fadeIn(500);
	});
}