function(params) {
	$('form', params['el']).ajaxForm(
		{url:('/profile/setUserProfileData/&profileID=' + params['profileID']),after:params['onComplete']}
	);
	$(params['el']).find('#cancelUserProfileDataEdit').click( function() { params['onComplete'](); } );
}