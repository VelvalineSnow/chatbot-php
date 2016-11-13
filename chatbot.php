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
		$this->mw     = new MediaWiki();
		$this->http   = new HTTPHandler();
		$this->logger = new Logger( "log.file" );
	}

	public function __destruct()
	{
		unlink( "cookies.tmp" );
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
		$this->connect();
	}

	private function get_room_info()
	{
		$this->logger->action( "Getting room info" );

		$result = $this->http->POST( $this->cred["wiki_url"] . "/wikia.php?controller=Chat&format=json" );
		$result = json_decode( $result, true );

		$this->logger->success( "Room info recieved" );

		$this->config = array(
			"roomId"   => $result["roomId"],
			"chatkey"  => $result["chatkey"]
		);

		$this->get_id();
		$this->get_session();
	}

	private function get_id()
	{
		$this->logger->action( "Retrieving wikia id" );

		$data = array(
			"action" => "query",
			"meta"   => "siteinfo",
			"siprop" => "wikidesc",
			"format" => "json"
		);

		$url = $this->cred["wiki_url"] . "/api.php?";
		$url = $this->http->serialize( $url, $data );
		$result = $this->http->GET( $url );
		$result = json_decode( $result, true );

		$this->config["wikiId"]   = $result["query"]["wikidesc"]["id"];
		$this->config["serverId"] = $result["query"]["wikidesc"]["id"];

		$this->logger->success( "Retrieved wiki id" );
	}

	private function get_session()
	{
		$this->logger->action( "Retrieving chat session" );

		$data = array(
			"name"      => str_replace( " ", "_", $this->cred["username"] ),
			"key"       => $this->config["chatkey"],
			"roomId"    => $this->config["roomId"],
			"wikiId"    => $this->config["wikiId"],
			"serverId"  => $this->config["serverId"],
			"transport" => "polling"
		);

		$url = "http://chat.wikia-services.com/socket.io/?";
		$url = $this->http->serialize( $url, $data );
		$result = $this->http->GET( $url );
		$result = preg_replace( "/.*\d\{/", "{", $result );
		$result = json_decode( $result, true );
		
		$this->logger->log( json_encode( $result ) );

		$this->config["sid"] = $result["sid"];
		$this->config["ping_interval"] = $result["pingInterval"];
		
		$this->logger->success( "Chat session retrieved" );
		$this->logger->log( "Chat SID: " . $this->config["sid"] );
		$this->logger->log( "Chat ping interval: " . $this->config["ping_interval"] );
	}

	private function connect()
	{
		$this->logger->action( "Attempting to connect to chat" );

		$data = array(
			"name"      => str_replace( " ", "_", $this->cred["username"] ),
			"key"       => $this->config["chatkey"],
			"roomId"    => $this->config["roomId"],
			"wikiId"    => $this->config["wikiId"],
			"serverId"  => $this->config["serverId"],
			"EIO"       => "3",
			"transport" => "polling",
			"t"         => time(),
			"sid"       => $this->config["sid"]
		);

		$packet = array(
			"sid" => $this->config["sid"]
		);

		$url = "http://chat.wikia-services.com/socket.io/?";
		$url = $this->http->serialize( $url, $data );
		$result = $this->http->POST( $url, json_encode( $packet ) );

		$this->logger->log( $url );

		$this->logger->log( $result );
	}
}

$ini_file = "chatbot.ini";
$bot = new ChatBot();
$bot->start( $ini_file );