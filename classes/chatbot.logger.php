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