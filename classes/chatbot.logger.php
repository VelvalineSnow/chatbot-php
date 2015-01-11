<?php

class Logger
{
	public function __construct( $file )
	{
		$this->file = $file;
	}

	public function error( $msg )
	{
		$this->_write( "[ Error ]", $msg );
	}

	public function action( $msg )
	{
		$this->_write( "[ Pending ]", $msg );
	}

	public function success( $msg )
	{
		$this->_write( "[ Success ]", $msg );
	}

	private function _write( $type, $msg )
	{
		$file = fopen( $this->file, "a" );
		fwrite( $file, "{" . date(DATE_RFC2822) . " }" . " " . $type . " " . $msg . "\n");
		fclose( $file );
	}
}