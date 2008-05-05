<?php

class ServerConnection {

	private $host = null;
	private $port = null;
	
	private $lastBuffer = null;
	
	private $serverSocket = null;
	private $clientSocket = null;
	
	public function __construct( $host, $port ) {
		$this->host = $host;
		$this->port = $port;
		
		/* create a socket in the AF_INET family, using SOCK_STREAM for TCP connection */

		$this->serverSocket = socket_create( AF_INET, SOCK_STREAM, SOL_TCP );
		eGlooLogger::writeLog( eGlooLogger::$DEBUG, '$this->serverSocket: ' . $this->serverSocket, eGlooLogger::$EGLOOIDENTD );
		
		eGlooLogger::writeLog( eGlooLogger::$DEBUG, "Binding socket to $this->host on port $this->port", eGlooLogger::$EGLOOIDENTD );
		
		$retVal = socket_bind( $this->serverSocket, $this->host, $this->port );
		eGlooLogger::writeLog( eGlooLogger::$DEBUG, '$retVal: ' . $retVal, eGlooLogger::$EGLOOIDENTD );
	}
	
	public function listen() {
		$retVal = false;
		
		$listenReturnValue = socket_listen( $this->serverSocket, 5 );
		eGlooLogger::writeLog( eGlooLogger::$DEBUG, '$listenReturnValue: ' . $listenReturnValue, eGlooLogger::$EGLOOIDENTD );
		
		$this->clientSocket = socket_accept( $this->serverSocket );
		eGlooLogger::writeLog( eGlooLogger::$DEBUG, '$this->clientSocket: ' . $this->clientSocket, eGlooLogger::$EGLOOIDENTD );
		
		if ( $this->clientSocket !== false && $this->clientSocket !== null ) {
			$retVal = true;
		}
		
		return $retVal;
	}
	
	public function readClient( $unicode = false ) {
		eGlooLogger::writeLog( eGlooLogger::$DEBUG, 'readClient() $this->clientSocket: ' . $this->clientSocket, eGlooLogger::$EGLOOIDENTD );
		
		if ( $unicode ) {
			$this->lastBuffer = utf8_decode( socket_read( $this->clientSocket, 4096 ) );
		} else {
			$this->lastBuffer = socket_read( $this->clientSocket, 4096 );
		}
		
		eGlooLogger::writeLog( eGlooLogger::$DEBUG, '$this->lastBuffer: ' . $this->lastBuffer, eGlooLogger::$EGLOOIDENTD );
		return $this->lastBuffer;
	}
	
	public function writeClient( $buffer, $unicode = false ) {
		if ( $unicode ) {
			socket_write( $this->clientSocket, utf8_encode( $buffer ), strlen( utf8_encode( $buffer) ) );
		} else {
			socket_write( $this->clientSocket, $buffer, strlen( $buffer ) );
		}
	}
	
	public function disconnectClient() {
		socket_close( $this->clientSocket );
	}
	
}

?>