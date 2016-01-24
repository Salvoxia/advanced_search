<?php

class SearchQuery extends DBNormalQuery
{
	/**
	 * Return the total number of rows returned by the last query (if there were no limits).
	 *
	 * @return int
	 */
	public function totalRecordCount()
	{
		$res = mysqli_query(self::$dbconn->id(), "SELECT FOUND_ROWS()");

		if ($this->resid === false || self::$dbconn->id()->errno) {
			if (defined('KB_PROFILE')) {
				DBDebug::recordError("Database error: " . self::$dbconn->id()->error);
				DBDebug::recordError("SQL: " . $sql);
			}
			if (defined('DB_HALTONERROR') && DB_HALTONERROR) {
				echo "Database error: " . self::$dbconn->id()->error . "<br />";
				echo "SQL: " . $sql . "<br />";
				trigger_error("SQL error (" . self::$dbconn->id()->error, E_USER_ERROR);
				exit;
			} else {
				trigger_error("SQL error (" . self::$dbconn->id()->error, E_USER_WARNING);
				return false;
			}
		}
		
		$row = $res ? $res->fetch_row() : false;
 		$total = $row[0];
 		
 		return $total;
	}
}

?>