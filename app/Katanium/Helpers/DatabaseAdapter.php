<?php

namespace Katanium\Helpers;

/**
* Database Adapter
*
* To give models to access database
*/
class DatabaseAdapter
{
	protected static $db = null;

	public static function getDBConnection()
	{
		return static::$db;
	}

	public static function setDBConnection($conn)
	{
		static::$db = $conn;
	}
}
