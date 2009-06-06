// Default

function scrollUp(expr,trigger) {
    $(trigger).attr('egloo:mousedown','true');
    var oldTop = Number($(expr).top().slice(0,-2));
    var newTop = (oldTop < 0) ? (oldTop + 100) : 0;
    $(expr).animate({top:newTop},{duration:1}, function() {
        if ( $(trigger).attr('egloo:mousedown') == 'true') {
            window.setTimeout(null,scrollUp(this,trigger),100);
            scrollUp(this,trigger);
        } else {
            $(trigger).removeAttr('egloo:mousedown');
        }
    });
}

function scrollDown(expr,trigger) {
    $(trigger).attr('egloo:mousedown','true');
    var oldTop = Number($(expr).top().slice(0,-2));
    var newTop = (oldTop > -3000) ? (oldTop - 100) : -3000;
    $(expr).animate({top:newTop},{duration:1}, function() {
        if ( $(trigger).attr('egloo:mousedown') == 'true') {
            window.setTimeout(null,scrollDown(this,trigger),100);
        } else {
            $(trigger).removeAttr('egloo:mousedown');
        }
    }); 
}

$(document).ready(function() {
    $('#about').click( function() {
        $('#shell').animate({height:800}, null, function() {
            $('#content_text').load("/externalMainPage/extMainViewAbout/", function() {
                $('#content').fadeIn(200);
            });
        });
    });
    $('#join').click( function() {
        $('#shell').animate({height:800}, null, function() {
            $('#content_text').load("/externalMainPage/extMainJoinForm/", function() {
                $('#content').fadeIn(1000);
                /*$('.licenseInformationBox').mousewheel( function(event, delta) {
                    if ( delta < 0 ) {
                        $('#licenseText').animate({top:300},{duration:1});
                    } else {
                        $('#licenseText').animate({top:0},{duration:1});
                    }
                });*/
                //$('#licenseUpScroll').mousedown(function(){scrollUp('#licenseText',this);});
                //$('#licenseDownScroll').mousedown(function(){scrollDown('#licenseText',this);});
                $('#firstNameInput').keyup(function() {
	                $('#licenseUserFirstName').html($(this).val()).css('background','#D7E5F2');
	                this.focus();
                }).change(this.onkeyup).keyup();
                $('#lastNameInput').keyup(function() {
	                $('#licenseUserLastName').html($(this).val()).css('background','#D7E5F2');
	                this.focus();
                }).change(this.onkeyup);
                if ((refCode = window.location.hash.match(/^#referral=(\w{20})$/)) != null)
                    $('#referralCodeInput').val(refCode[1]);
                $('#signup').ajaxForm({url:'/account/registerNewAccount/',dataType:'xml',
                    beforeSubmit:function() {
                    	 //First Name
                    	 if ( $('#firstNameInput').val() == '' || $('#firstNameInput').val().length > 35 ) {                    	 	  
                    	 	  $('#firstNameInputError').css('display','block').html("Please enter your first name");
                    	 	  $('#firstNameInput').css('border','1px solid #f00');                    	 	                      	 	  
                    	 }else{
                    	 	  $('#firstNameInput').css('border','2px solid #999').css('border-right','none').css('border-bottom','none');	  
                    	 	  $('#firstNameInputError').css('display','none');
                    	 }
                    	 //Last Name
                    	 if ( $('#lastNameInput').val() == '' || $('#lastNameInput').val().length > 35 ) {
                    	 	  $('#lastNameInputError').css('display','block').html("Please enter your last name");
                    	 	  $('#lastNameInput').css('border','1px solid #f00');
                    	 }else{
                    	 	  $('#lastNameInput').css('border','2px solid #999').css('border-right','none').css('border-bottom','none');	  
                    	 	  $('#lastNameInputError').css('display','none');
                    	 }
                    	 //Email
                    	 if ( $('#userEmail').val() == '' || $('#userEmail').val().length > 320 || !$('#userEmail').val().match(/^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i) ) {
                    	 	  $('#userEmailError').css('display','block').html("Please enter a valid email address");
                    	 	  $('#userEmail').css('border','1px solid #f00');
                    	 }else{
                    	 	  $('#userEmail').css('border','2px solid #999').css('border-right','none').css('border-bottom','none');	  
                    	 	  $('#userEmailError').css('display','none');
                    	 }
                    	 //Username
                    	 if ( $('#userPreferredAccountName').val() == '' || !$('#userPreferredAccountName').val().match(/^[a-zA-Z]\w{7,34}/) ) {
                    	 	  $('#userPreferredAccountNameError').css('display','block').html("Please enter a valid username");
                    	 	  //TODO: Check if username is in use
                    	 	  $('#userPreferredAccountName').css('border','1px solid #f00');
                    	 }else{
                    	 	  $('#userPreferredAccountName').css('border','2px solid #999').css('border-right','none').css('border-bottom','none');	  
                    	 	  $('#userPreferredAccountNameError').css('display','none');
                    	 }
                    	 //Password - Requested
                    	 if ( $('#userRequestedAccountPassword').val() == '' || $('#userRequestedAccountPassword').val().length < 8 ) {
                    	 	  $('#userRequestedAccountPasswordError').css('display','block').html("Please enter a valid password");
                    	 	  //TODO: Encrypt password (sha256)
                    	 	  $('#userRequestedAccountPassword').css('border','1px solid #f00');
                    	 }else{
                    	 	  $('#userRequestedAccountPassword').css('border','2px solid #999').css('border-right','none').css('border-bottom','none');	  
                    	 	  $('#userRequestedAccountPasswordError').css('display','none');
                    	 }
                    	 //Password - Confirmed
                    	 if ( $('#userConfirmedAccountPassword').val() == '' ) {
                    	 	  $('#userConfirmedAccountPasswordError').css('display','block').html("Please confirm your password");     
                    	 	  $('#userConfirmedAccountPassword').css('border','1px solid #f00');
                    	 }else if( $('#userConfirmedAccountPassword').val() != $('#userRequestedAccountPassword').val() ) {
                    	 	  $('#userConfirmedAccountPasswordError').css('display','block').html("Please make sure both passwords match");                      	 	  
                    	 	  $('#userConfirmedAccountPassword').css('border','1px solid #f00');
                    	 }else{
                    	 	  $('#userConfirmedAccountPassword').css('border','2px solid #999').css('border-right','none').css('border-bottom','none');	  
                    	 	  $('#userConfirmedAccountPasswordError').css('display','none');
                    	 }
                    	 //License Agreement
                    	 if ( $('#userAcceptsLicense').checked == false ) {
                    	 	  $('#userAcceptsLicenseError').css('display','block');
                    	 	  $('#userAcceptsLicense').css('border','1px solid #f00');
                    	 }
                    	 //Referral Id
                    	 if ( $('#referralCodeInput').val() == '' || !$('#referralCodeInput').val().match(/^\w{1,20}/) ) {
                    	 	  $('#referralCodeInputError').css('display','block').html("Please enter the person's username that referred you");
                    	 	  $('#referralCodeInput').css('border','1px solid #f00');
                    	 }else{
                    	 	  $('#referralCodeInput').css('border','2px solid #999').css('border-right','none').css('border-bottom','none');	  
                    	 	  $('#referralCodeInputError').css('display','none');
                    	 }
                    	 
                    },success:function(xmlData) {
                         if ( $('eGloo:ErrorCode',xmlData).text() == '' && $('ErrorMessage',xmlData).text() == '' ) {
                             alert( $('eGloo:ErrorContent',xmlData).text() );
                         } else {
                             alert( 'Error' + $('eGloo:ErrorCode',xmlData).text() + ': ' + $('ErrorMessage',xmlData).text() );
                         }
                    }
                });
            });
        });
    });

    $('#inputFields > input').keyup( function () {
        if ( this.form.username.value == '' || this.form.password.value == '' ||
	         this.form.username.value == this.form.username.name ||
	         this.form.password.value == this.form.password.name ) {
                $('#login').css('color','#CCCCCC').mouseover( function() {
                    $(this).css('textDecoration','none').css('cursor','default');
                }).unbind('click');
	    } else {
                $('#login').css('color','#0099E5').mouseover( function() {
                    $(this).css('textDecoration','underline').css('cursor','pointer');
                }).mouseout( function() {
                    $(this).css('textDecoration','none').css('cursor','default');
                }).unbind('click').click( function() {$('#form > form').submit();});
	    }
	}).blur( function() {
	    if ( $(this).val() == '' ) {
	        if ( $(this).attr('name') == 'password' )
	            $(this).attr('type','text');
            $(this).val($(this).attr('name')).css('color','grey');
	    }
    }).focus( function() {
        if ( $(this).val() == $(this).attr('name') ) {
            $(this).val('');
            if ( $(this).attr('name') == 'password' ) {
                $(this).attr('type','password');
            }
        }
        $(this).css('color','#000');
    });
    $('#form > form').ajaxForm({url:'/account/processLogin/',dataType:'json',
        before:function() {
            
        },after:function(json) {
            if (json.LOGGED_IN == 'true') {
                window.location = '/internalMainPage/getInternalMainPage/';						 	
            } else {
                $('#loginFailed').html('Login Failed');
            }
        }});
    if (window.location.hash.match(/^#referral=(\w{20})$/)) $('#join').click();
    else $('#username').get()[0].focus();
});
