<?php

/**
 * @class HTTPHandler
 * @ingroup file
 * @author(s):
 *     - Benjamin Williams <velvaline.snow@gmail.com>
 */
class HTTPHandler
{
	
	/**
	 * @attribute cookie_file
	 * @access private
	 */
	private $user_agent = "Quality-Control-0.0.1/bot";
	
	/**
	 * @method GET
	 * @access public
	 * @param <string> url
	 * @param <array> data
	 * @param <array> headers
	 * @return <string>
	 */
	public function GET( $url, $data="", $headers="" )
	{
		return $this->_exec( false, $url, $data, $headers );
	}
	
	/**
	 * @method POST
	 * @access public
	 * @param <string> url
	 * @param <array> data
	 * @param <array> headers
	 * @return <string>
	 */
	public function POST( $url, $data="", $headers="" )
	{
		return $this->_exec( true, $url, $data, $headers );
	}
	
	/**
	 * @method serialize
	 * @access public
	 * @param <string> url
	 * @param <array> data
	 * @return <string>
	 */
	public function serialize( $url, $data )
	{
		$params = "";

		foreach ( $data as $k => $v )
		{
			$params = $params . "" . $k . "=" . $v . "&";
		}

		return $url . "" . rtrim( $params, "&" );
	}

	/**
	 * @method _exec
	 * @access private
	 * @param <bool> is_post
	 * @param <string> url
	 * @param <array> data
	 * @param <array> headers
	 * @return <string>
	 */
	private function _exec( $is_post, $url, $data, $headers )
	{
		$curloptions = array(
			CURLOPT_COOKIEFILE => "cookies.tmp",
   			CURLOPT_COOKIEJAR => "cookies.tmp",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_USERAGENT => $this->user_agent,
			CURLOPT_POST => $is_post
		);
		
		$ch = curl_init();
		curl_setopt_array( $ch, $curloptions );
		curl_setopt( $ch, CURLOPT_URL, $url );

		if ( $data != "" )
		{
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
		}

		if ( $headers != "" )
		{
			curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
		}
		
		$result = curl_exec( $ch );
		return $result;
	}
}