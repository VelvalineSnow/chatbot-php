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

class EventHandler
{
	
	/**
	 * @attribute events
	 * @access private
	 * @state static
	 */
	private static $events = array(
		"ban",
		"join",
		"kick",
		"leave",
		"msg"
	);
	
	public function __construct()
	{
		$this->logger = new Logger( "log.file" );
	}

	/**
	 * @method process_event
	 * @access public
	 * @param <object> event
	 */
	public function process_event($event)
	{
		// Do stuff here
	}
	
	/**
	 * @method _is_valid_event
	 * @access private
	 * @param <string> event
	 */
	private function _is_valid_event( $event )
	{
		if ( isset( $this->events[$event] ) )
		{
			return true;
		} 
		else
		{
			return false;
		}
	}
	
	/**
	 * @method _trigger_events
	 * @access private
	 * @param <string> event
	 * @param <array> event_var
	 */
	private function _trigger_events( $event, $data )
	{
		if ( $this->_is_valid_event( $event ) == true )
		{
			switch ( $event )
			{
				case "ban":
					$this->_ban( $data );
					break;
				case "join":
					$this->_join( $data );
					break;
				case "kick":
					$this->_kick( $data );
					break;
				case "leave":
					$this->_leave( $data );
					break;
				case "msg":
					$this->_msg( $data );
					break;
			}
		}
		else
		{
			$this->logger->error( "Invalid event <$event> <" . var_export( $data ) . ">" );
			die();
		}
	}
}