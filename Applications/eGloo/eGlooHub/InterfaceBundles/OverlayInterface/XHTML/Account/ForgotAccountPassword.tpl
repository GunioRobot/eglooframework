<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Forgot Account Password</title>
</head>
<body>
    	
    	<h5>Please enter your email address and we will send you a link to reset your password</h5>
    	
    	<br/>
    	<form name="login" action="/account/sendPasswordResetConfirmation/" method="post" >
                <input id="emailAddress" type="text" name="emailAddress" size="17"/>
				<input type="submit" name="submit" value="reset" />
		</form>
    	
</body>
</html>