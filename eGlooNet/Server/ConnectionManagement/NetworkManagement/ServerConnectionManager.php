<?php

abstract class ServerConnectionManager {

	private $serverConnection = null;
	private $clientConnected = null;
	
	public function __construct() {
		$host = '127.0.0.1';
		$port = '10000';
		
		$this->serverConnection = new ServerConnection( $host, $port );
		$this->clients = array();
	}

	public function listen() {
		$this->clientConnected = $this->serverConnection->listen();
	}
	
	public function clientConnected() {
		return $this->clientConnected;
	}
	
	public function disconnectClient() {
		// TODO
	}
	
	public function readClient( $unicode = false ) {
		return $this->serverConnection->readClient( $unicode );
	}
	
	public function writeClient( $buffer, $unicode = false ) {
		return $this->serverConnection->writeClient( $buffer, $unicode );
	}
}

?>