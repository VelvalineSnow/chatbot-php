<?php

/**
 * @class Events
 * @ingroup file
 * @author(s):
 *     - Benjamin Williams <velvaline.snow@gmail.com>
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