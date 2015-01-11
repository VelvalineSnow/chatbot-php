<?php

/**
 * Copyright (C) 2015  Benjamin Williams
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

class MediaWiki
{
	
	public function __construct()
	{
		$this->logger = new Logger( "log.file" );
	}

	public function login( $cred )
	{
		$this->logger->action( "Attempting to log into the wiki as " . $cred["username"] );

		$this->wiki = $cred["wiki_url"] . "/api.php";
		$http = new HTTPHandler();
		
		$data = array(
			"action" => "login",
			"lgname" => $cred["username"],
			"lgpass" => $cred["password"],
			"format" => "json",
		);
		
		$result = json_decode( $http->POST( $this->wiki, $data ), true );
		
		$data = array(
			"action" => "login",
			"lgname" => $cred["username"],
			"lgpassword" => $cred["password"],
			"lgtoken" => $result["login"]["token"],
			"format" => "json"
		);
		
		$result = json_decode( $http->POST( $this->wiki, $data ), true );
		
		if ( $result["login"]["result"] == "Success" )
		{
			$this->logger->success( "Logged into the wiki as " . $cred["username"] );
			return true;
		}
		else
		{
			$this->logger->error( "Failed to login! <" . var_export( $result["login"]["result"] )  . ">" );
			die();
		}
	}
}