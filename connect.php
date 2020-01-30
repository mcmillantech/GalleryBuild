<?php

// ----------------------------------------------
//  Connect to the database
//
//  Parameter Configuration onject
//		(db connection values)
//
//  Returns	  MYSQL connection
// ----------------------------------------------
function dbConnect() {
    $dbConnection = mysqli_connect("127.0.0.1", "root", "sql", "gallery")
        or die ("Connecr error" . mysqli_connect_error() );
    mysqli_select_db($dbConnection, "gallery") 
	or die (mysqli_error($dbConnection));

    return $dbConnection;
}


