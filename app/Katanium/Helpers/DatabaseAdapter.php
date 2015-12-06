<?php

namespace Katanium\Helpers;

/**
* Database Adapter
*
* To give models to access database
*/
class DatabaseAdapter
{
	protected $db = null;

	public function getDBConnection()
	{
		return $this->db;
	}

	public function setDBConnection($conn)
	{
		$this->db = $conn;
	}
}
