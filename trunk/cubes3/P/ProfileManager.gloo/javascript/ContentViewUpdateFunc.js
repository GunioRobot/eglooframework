function(cube,params) {
	$.getJSON('/profile/getUserProfileData/&profileID=' + params['profileID'] + '&format=json', null, function (json1) {
	$('#'+params['cubeID']).find('.userProfileCubeContentGenderInfo').html(
		json1['gender']).end().find('.userProfileCubeContentInterestedInInfo').html(
		json1['sexualInterest']).end().find('.userProfileCubeContentBirthDateInfo').html(
		json1['birthDate']).end().find('.userProfileCubeContentLookingForInfo').html(
		json1['lookingFor'].replace(/,\s*$/,'')).end().find('.userProfileCubeContentHometownInfo').html(
		json1['hometown']);
	});
}

