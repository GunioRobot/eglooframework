// Default

$(document).ready(function(){
	$('#tour').click(function(){
		$('#forgotPassword').hide();
		$('#loadingImage').show();
		$('#shell').animate({
			height: 800
		}, null, function(){
			$('#content_text').load("externalMainPage/extMainViewAbout/", function(){
				$('#content').fadeIn(200);
				$('#loadingImage').hide();
				$('#forgotPassword').show();
			});
		});
	});
	$('#forgotPassword').click(function(){
		/*<!--{* TODO check if including this instead of hardcoding the URL in some fashion is a security hole *}-->*/
		window.location = 'https://' + window.location.hostname + '/account/viewForgotAccountPasswordForm/';
	});
	$('#join').click(function(){
		$('#forgotPassword').hide();
		$('#loadingImage').show();
		$('#shell').animate({
			height: 800
		}, null, function(){
			$('#content_text').load("externalMainPage/extMainJoinForm/", function(){
				$('#content').fadeIn(500);
				$('#loadingImage').hide();
				$('#forgotPassword').show();
				$('#firstNameInput').keyup(function(){
					$('#licenseUserFirstName').html($(this).val()).css('background', '#D7E5F2');
					this.focus();
				}).change(this.onkeyup).keyup();
				$('#lastNameInput').keyup(function(){
					$('#licenseUserLastName').html($(this).val()).css('background', '#D7E5F2');
					this.focus();
				}).change(this.onkeyup);
				/*<!--{* TODO check if including this instead of hardcoding the URL in some fashion is a security hole *}-->*/
				if ((refCode = window.location.hash.match(/^#referral=(\w{20})$/)) !== null) {
					$('#referralCodeInput').val(refCode[1]);
				}
				$('#firstNameInput').focus(function(){
					$("#expectedValues").html("Please enter your first name (Maximum of 35 characters)" +
					"<br /><br />This must be your legal first name").show();
				});
				$('#lastNameInput').focus(function(){
					$("#expectedValues").html("Please enter your last name (Maximum of 35 characters)" +
					"<br /><br />This must be your legal last name").show();
				});
				$('#userEmail').focus(function(){
					$("#expectedValues").html("Please enter a valid email address<br /><br />" +
					"eGloo will send a registration confirmation to this address.  It will also be used to notify you of important account " +
					"or site updates, and for resetting forgotten passwords.  Please double-check that the email address " +
					"you have entered is both current and accurate").show();
				});
				$('#userPreferredAccountName').focus(function(){
					$("#expectedValues").html("Please enter your desired username<br /><br />" +
					"Usernames must be between 7 and 34 letters long<br /><br />Numbers and special characters not allowed").show();
				});
				$('#userRequestedAccountPassword').focus(function(){
					$("#expectedValues").html("Please enter a password for your account<br /><br />" +
					"Password must be at least 8 characters long").show();
				});
				$('#userConfirmedAccountPassword').focus(function(){
					$("#expectedValues").html("Please confirm your password").show();
				});
				$('#referralCodeInput').focus(function(){
					$("#expectedValues").html("Please enter a valid referral code").show();
				});
				
				$('#signup').ajaxForm({
					url: 'account/registerNewAccount/',
					dataType: 'xml',
					beforeSubmit: function(){
						var isValid = true;

						//First Name
						if ($('#firstNameInput').val() === '' || $('#firstNameInput').val().length > 35 ||
						!$('#firstNameInput').val().match(/^[A-Z .\-]+$/i)) {
							$('#firstNameInput').css('border', '1px solid #f00');
							isValid = false;
						} else {
							$('#firstNameInput').css('border', '2px solid #999').css('border-right', 'none').css('border-bottom', 'none');
							$('#firstNameInputError').css('display', 'none');
						}
						//Last Name
						if ($('#lastNameInput').val() === '' || $('#lastNameInput').val().length > 35 ||
						!$('#lastNameInput').val().match(/^[A-Z .\-]+$/i)) {
							$('#lastNameInput').css('border', '1px solid #f00');
							isValid = false;
						} else {
							$('#lastNameInput').css('border', '2px solid #999').css('border-right', 'none').css('border-bottom', 'none');
							$('#lastNameInputError').css('display', 'none');
						}
						//Email
						if ($('#userEmail').val() === '' || $('#userEmail').val().length > 320 || !$('#userEmail').val().match(/^[A-Z0-9._%\-]+@[A-Z0-9.\-]+\.[A-Z]{2,4}$/i)) {
							$('#userEmail').css('border', '1px solid #f00');
							isValid = false;
						} else {
							$('#userEmail').css('border', '2px solid #999').css('border-right', 'none').css('border-bottom', 'none');
							$('#userEmailError').css('display', 'none');
						}
						//Username
						if ($('#userPreferredAccountName').val() === '' || !$('#userPreferredAccountName').val().match(/^[a-zA-Z]\w{7,34}/)) {
							//TODO: Check if username is in use
							$('#userPreferredAccountName').css('border', '1px solid #f00');
							isValid = false;
						} else {
							$('#userPreferredAccountName').css('border', '2px solid #999').css('border-right', 'none').css('border-bottom', 'none');
							$('#userPreferredAccountNameError').css('display', 'none');
						}
						//Password - Requested
						if ($('#userRequestedAccountPassword').val() === '' || $('#userRequestedAccountPassword').val().length < 8) {
							$('#userRequestedAccountPassword').css('border', '1px solid #f00');
							isValid = false;
						} else {
							$('#userRequestedAccountPassword').css('border', '2px solid #999').css('border-right', 'none').css('border-bottom', 'none');
							$('#userRequestedAccountPasswordError').css('display', 'none');
						}
						//Password - Confirmed
						if ($('#userConfirmedAccountPassword').val() === '') {
							$('#userConfirmedAccountPassword').css('border', '1px solid #f00');
							isValid = false;
						} else if ($('#userConfirmedAccountPassword').val() !== $('#userRequestedAccountPassword').val()) {
							$('#userRequestedAccountPassword').val('').css('border', '1px solid #f00');
							$('#userConfirmedAccountPassword').val('').css('border', '1px solid #f00');
							isValid = false;
						} else {
							$('#userConfirmedAccountPassword').css('border', '2px solid #999').css('border-right', 'none').css('border-bottom', 'none');
							$('#userConfirmedAccountPasswordError').css('display', 'none');
						}
						//License Agreement
						if ($('#userAcceptsLicense').checked === false) {
							$('#userAcceptsLicenseError').css('display', 'block');
							$('#userAcceptsLicense').css('border', '1px solid #f00');
							isValid = false;
						}
						//Referral Id
						if ($('#referralCodeInput').val() === '' || !$('#referralCodeInput').val().match(/^\w{1,20}/)) {
							$('#referralCodeInput').css('border', '1px solid #f00');
							isValid = false;
						} else {
							$('#referralCodeInput').css('border', '2px solid #999').css('border-right', 'none').css('border-bottom', 'none');
							$('#referralCodeInputError').css('display', 'none');
						}
						
						if (!isValid) {
							$("#expectedValues").html("Required information is missing or invalid");
						}

						return isValid;
					},
					success: function(xmlData){
						// Make this tell the user to check his/her email
						// TODO we don't parse namespaces in XML correctly in jQuery apparently... should be prepended
						// with eGloo:
						if ($('ErrorCode', xmlData).text() === '' && $('ErrorMessage', xmlData).text() === '' &&
						$.trim($('ResponseContent', xmlData).text()) === "Success!") {
							alert("Registration Successful! An account activation link has been sent to your email address.");
							/*<!--{* TODO check if including this instead of hardcoding the URL in some fashion is a security hole *}-->*/
							window.location = 'http://' + window.location.hostname;
						} else {
							alert('Error' + $('ErrorCode', xmlData).text() + ': ' + $('ErrorMessage', xmlData).text());
						}
					}
				});
				$('#firstNameInput').focus();
			});
		});
	});
	
	$('input').keyup(function(){
		if (this.form.username.value === '' || this.form.password.value === '' ||
		this.form.username.value === this.form.username.name ||
		this.form.password.value === this.form.password.name) {
			$('#login').css('color', '#CCCCCC').mouseover(function(){
				$(this).css('textDecoration', 'none').css('cursor', 'default');
			}).unbind('click');
		} else {
			$('#login').css('color', '#0099E5').mouseover(function(){
				$(this).css('textDecoration', 'underline').css('cursor', 'pointer');
			}).mouseout(function(){
				$(this).css('textDecoration', 'none').css('cursor', 'default');
			}).unbind('click').click(function(){
				$('#form > form').submit();
			});
		}
	}).blur(function(){
		if ($(this).val() === '') {
			if ($(this).attr('name') === 'password') {
				$(this).attr('type', 'text');
			}
			$(this).val($(this).attr('name')).css('color', 'grey');
		}
	}).focus(function(){
		if ($(this).val() === $(this).attr('name')) {
			$(this).val('');
			if ($(this).attr('name') === 'password') {
				$(this).attr('type', 'password');
			}
		}
		$(this).css('color', '#000');
	});
	$('#form > form').ajaxForm({
		url: '/account/processLogin/',
		dataType: 'json',
		beforeSubmit: function(){
			//TODO Change this to be less hackish; we shouldn't determine whether to submit
			// or not based on whether #login is underlined or not
			//		if ($('#login').css('textDecoration') == 'none') {
			//		$('#userRequestedAccountPassword').css('border','1px solid #f00');
			//		return false;
			//	} else {
			$('#loginFailed').html('');
			$('#forgotPassword').hide();
			$('#loadingImage').show();
			//		}
			/*<!--{* TODO check if including this instead of hardcoding the URL in some fashion is a security hole *}-->*/
			$('<script type="text/javascript" src="https://' + window.location.hostname +
			'/index.php?class=account&id=ajaxProcessLogin&username=' +
			encodeURIComponent($('#username').val()) +
			'&password=' +
			encodeURIComponent($('#password').val()) +
			'"></script>').appendTo('head');
			return false;
		},
		success: function(json){
			if (json.LOGGED_IN === 'true') {
				/*<!--{* TODO check if including this instead of hardcoding the URL in some fashion is a security hole *}-->*/
				window.location = 'http://' + window.location.hostname + '/profileID=' + json.PROFILE_ID;
			} else {
				$('#loadingImage').hide();
				$('#loginFailed').html('Login Failed');
				$('#forgotPassword').show();
			}
		}
	});
	/*<!--{* TODO check if including this instead of hardcoding the URL in some fashion is a security hole *}-->*/
	if (window.location.hash.match(/^#referral=(\w{20})$/)) {
		$('#join').click();
	} else {
		$('#username').get()[0].focus();
	}
});
