<?php

/**
 * @class MediaWiki
 * @ingroup file
 * @author(s):
 *     - Benjamin Williams <velvaline.snow@gmail.com>
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