<?php

class AuthenticationServerConnectionManager extends ServerConnectionManager {
	
	public function authenticateClient() {
		eGlooLogger::writeLog( eGlooLogger::$DEBUG, "Reading from client", eGlooLogger::$EGLOOIDENTD );
		$buffer = $this->readClient();
		eGlooLogger::writeLog( eGlooLogger::$DEBUG, "Client sent: \n$buffer", eGlooLogger::$EGLOOIDENTD );
		// TODO validate client
		
		$response = null;
		
		$responseLine = "eGlooNet Status Code: eGlooNet/1.0 401 Found \n";
		$locationLine = "Location: www.egloo.com \n";
		$cacheControlLine = "Cache-Control: private \n";
		$dateLine = "Date: " . date(  DATE_RFC2822 ) . " \n";
		
		$response = $responseLine . $locationLine . $cacheControlLine . $dateLine;
		
		eGlooLogger::writeLog( eGlooLogger::$DEBUG, "Writing to client", eGlooLogger::$EGLOOIDENTD );
		$this->writeClient( $response );
		eGlooLogger::writeLog( eGlooLogger::$DEBUG, "Message sent to client: \n$response", eGlooLogger::$EGLOOIDENTD );
		
		eGlooLogger::writeLog( eGlooLogger::$DEBUG, "Disconnecting client", eGlooLogger::$EGLOOIDENTD );
		$this->disconnectClient();
		eGlooLogger::writeLog( eGlooLogger::$DEBUG, "Client disconnected", eGlooLogger::$EGLOOIDENTD );
	}
	
}

?>