<?php

include_once( __DIR__ . "/classes/chatbot.logger.php" ); 
include_once( __DIR__ . "/classes/chatbot.db.php" );
include_once( __DIR__ . "/classes/chatbot.http.php" );
include_once( __DIR__ . "/classes/chatbot.mediawiki.php" );
include_once( __DIR__ . "/classes/chatbot.extensions.php" );
include_once( __DIR__ . "/classes/chatbot.events.php" );

class ChatBot
{
	public function __construct()
	{
		$this->mw = new MediaWiki();
		$this->http = new HTTPHandler();
		$this->logger = new Logger( "log.file" );
	}

	public function start( $ini_file )
	{
		$this->logger->action( "Loading configuration" );

		$conf = parse_ini_file( $ini_file, true );
		$this->cred = array (
			"username" => $conf["mediawiki"]["username"],
			"password" => $conf["mediawiki"]["password"],
			"wiki_url" => $conf["mediawiki"]["wiki_url"],
		);
		
		$this->logger->success( "Configuration loaded" );

		$this->mw->login( $this->cred );
		$this->get_room_info();
	}

	private function get_room_info()
	{
		$this->logger->action( "Getting room info" );

		$result = $this->http->POST( $this->cred["wiki_url"] . "/wikia.php?controller=Chat&format=json" );
		$result = json_decode( $result, true );

		$this->logger->success( "Room info recieved" );

		$this->config = array(
			"room_id" => $result["roomId"],
			"chatkey" => $result["chatkey"],
        	"server" => $result["nodeHostname"],
        	"server_id" => $result["nodeInstance"],
        	"port" => $result["nodePort"]
		);

		$this->get_session();
	}

	private function get_session()
	{
		$this->logger->action( "Retrieving chat session" );

		$data = array(
			"name" => str_replace( " ", "_", $this->cred["username"] ),
			"key" => $this->config["chatkey"],
			"roomId" => $this->config["room_id"],
			"serverId" => $this->config["server_id"],
			"EIO" => "2",
			"transport" => "polling"
		);

		$url = "http://" . $this->config["server"] . ":" . $this->config["port"] . "/socket.io/1/?";
		$url = $this->http->serialize( $url, $data );
		$result = $this->http->GET( $url );
		$result = preg_replace( "/.*\d\{/", "{", $result );
		$result = json_decode( $result, true );

		$this->config["sid"] = $result["sid"];
		$this->config["ping_interval"] = $result["pingInterval"];
		
		$this->logger->success( "Chat session retrieved" );
	}
}

$ini_file = "chatbot.ini";
$bot = new ChatBot();
$bot->start( $ini_file );
