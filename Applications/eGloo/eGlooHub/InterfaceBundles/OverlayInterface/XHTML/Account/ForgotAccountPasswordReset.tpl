<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Forgot Account Password Reset</title>
</head>
<body>
    	
    	<h5>Please enter a new password</h5>
    	
    	<br/>
    	<form name="login" action="/account/resetForgottenPassword/" method="post" >
                Password: <input type="text" id="password1" name="password1" size="17"/><br />
                Re-enter: <input type="text" id="password2" name="password2" size="17"/><br />
				<input type="hidden" id="confirmationID" name="confirmationID" value="<!--{$confirmationID}-->" />
				<input type="hidden" id="uID" name="uID" value="<!--{$userID}-->" />
				<input type="submit" name="submit" value="reset" />
		</form>
    	
</body>
</html>