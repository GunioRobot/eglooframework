<?php
/**
 * eGlooHTTPRequest Class File
 *
 * $file_block_description
 * 
 * Copyright 2011 eGloo, LLC
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *        http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *  
 * @author George Cooper
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @category System
 * @package HTTP
 * @subpackage REST
 * @version 1.0
 */

/**
 * eGlooHTTPRequest
 *
 * $short_description
 *
 * $long_description
 *
 * @category System
 * @package HTTP
 * @subpackage REST
 */
class eGlooHTTPRequest {

	const NOT_FOUND_IN_HEADERS_MESSAGE = 'Not found in HTTP headers';

	private static $http_host = null;
	private static $query_string = null;
	private static $referer = null;
	private static $remote_address = null;
	private static $request_time = null;
	private static $request_uri = null;
	private static $server_address = null;
	private static $server_name = null;
	private static $server_protocol = null;
	private static $user_agent = null;
	private static $user_agent_hash = null;

// eGlooHTTPRequest::getRemoteAddress()

	public static function getHTTPHost() {
		if ( self::$http_host === null ) {
			self::$http_host = isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : '';
		}

		return self::$http_host;
	}

	public static function getQueryString() {
		if ( self::$query_string === null ) {
			self::$query_string = isset( $_SERVER['QUERY_STRING'] ) ? $_SERVER['QUERY_STRING'] : '';
		}

		return self::$query_string;
	}

	public static function getReferer() {
		if ( self::$referer === null ) {
			self::$referer = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '';
		}

		return self::$referer;
	}

	public static function getRemoteAddress() {
		if ( self::$remote_address === null ) {
			self::$remote_address = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '';
		}

		return self::$remote_address;
	}

	public static function getRequestTime() {
		if ( self::$request_time === null ) {
			self::$request_time = isset( $_SERVER['REQUEST_TIME'] ) ? $_SERVER['REQUEST_TIME'] : time();
		}

		return self::$request_time;
	}

	public static function getRequestURI() {
		if ( self::$request_uri === null ) {
			self::$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '';
		}

		return self::$request_uri;
	}

	public static function getServerAddress() {
		if ( self::$server_address === null ) {
			self::$server_address = isset( $_SERVER['SERVER_ADDR'] ) ? $_SERVER['SERVER_ADDR'] : '';
		}

		return self::$server_address;
	}

	public static function getServerName() {
		if ( self::$server_name === null ) {
			self::$server_name = isset( $_SERVER['SERVER_NAME'] ) ? $_SERVER['SERVER_NAME'] : '';
		}

		return self::$server_name;
	}

	public static function getServerProtocol() {
		if ( self::$server_protocol === null ) {
			self::$server_protocol = isset( $_SERVER['SERVER_PROTOCOL'] ) ? $_SERVER['SERVER_PROTOCOL'] : '';
		}

		return self::$server_protocol;
	}

	public static function getUserAgent() {
		if ( self::$user_agent === null ) {
			self::$user_agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : 'Default';
		}

		return self::$user_agent;
	}

	public static function getUserAgentHash() {
		if ( self::$user_agent_hash === null ) {
			self::$user_agent_hash = hash( 'sha256', self::getUserAgent() );
		}

		return self::$user_agent_hash;
	}

	public static function isSSL() {
		return isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== '';
	}

	/*

// If you use only one hostname with several subdomains (using wildcard), these can be done to reduce the risk:
// 
// 1) Set "UseCanonicalName On" and set your "ServerName".
// 
// 2) Ensure that $_SERVER["HTTP_HOST"] is not empty and does not contains any unexpected characters, something like:
// 
// preg_match("/^[a-zA-Z0-9]*$/",$_SERVER["HTTP_HOST"])
// 
// 3) Check whether the value of $_SERVER["HTTP_HOST"] is contained in $_SERVER["SERVER_NAME"], for example: subdomain.example.com in example.com
// 
// strpos( $_SERVER["HTTP_HOST"], $_SERVER["SERVER_NAME"]) !==false )

	*/
}

