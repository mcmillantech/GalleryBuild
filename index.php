<?php

// -------------------------------------------------------------
//  Project	Gallery Build
//  File	index.php
//
//  Author	John McMillan
//  McMillan Technology 
//
//  Entry point. Currently allows Customer create, edit and install
//
//  View is customerList.html
// --------------------------------------------------------------

require_once 'Customer.php';
require_once 'connect.php';
require_once 'view.php';

showCustomers();

//$customer = new Customer();

//$customer->edit(1);

// ----------------------------------------
//  Show a list of customers
//  
// ----------------------------------------
function showCustomers()
{
    $dta = array();
    $list = array();
    
    $dbConnection = dbConnect();
    $sql = "SELECT * FROM customers";
    $result = $dbConnection->query($sql)
        or die("Index failed to read customer : " . $dbConnection->error);
    while ($record = $result->fetch_assoc()) {
        $line = array();
        $line['id'] = $record['id'];
        $line['name'] = $record['name'];
        array_push($list, $line);
    	$dta["list"] = $list;
    }

    showView("customerList.html", $dta);
}
