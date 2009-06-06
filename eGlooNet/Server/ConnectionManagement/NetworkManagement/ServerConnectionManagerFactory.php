<?php

final class ServerConnectionManagerFactory {
	
	static public function getAuthenticationServerConnectionManager() {
		// In the future, AuthenticationServerConnectionManager will be an interface
		// which defines authentication server connection managers by connection type
		return new AuthenticationServerConnectionManager();
	}

	static public function getRegistrationServerConnectionManager() {
		// In the future, AuthenticationServerConnectionManager will be an interface
		// which defines authentication server connection managers by connection type
		return new RegistrationServerConnectionManager();
	}
	
}

?>