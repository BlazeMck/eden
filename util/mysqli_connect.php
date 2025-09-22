<?php # script 9.2, database connection string

// set the db access information as constants
define('DB_HOST',     'localhost');
define('DB_NAME',     'edendb');

// make the connection
$dbc = @mysqli_connect(DB_HOST, null, null, DB_NAME)
	OR die('Could not connect to MySQL: ' . mysqli_connect_error() );
	
// set encoding
mysqli_set_charset($dbc, 'utf8');

