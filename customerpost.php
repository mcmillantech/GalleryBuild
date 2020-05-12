<?php

// -------------------------------------------------------------
//  Project	Gallery Build
//  File	Customerpost.php
//
//  Author	John McMillan
//              McMillan Technology 
//
//  Posted from customer form. Calls Customer methods to post
// --------------------------------------------------------------
require_once "Customer.php";
require_once 'configuration.php';

//echo "Customer post<br>";

$customer = new Customer();
$config = new Configuration;

$action = $_GET['action'];

switch ($action) {
    case "insert":
        $customer->insert();
        $config->insert();
        break;
    case "update":
        $customer->update(1);
        break;
    case "configedit":
        $cusId = $_GET['cus'];
        $config->edit($cusId);
        break;
    case "configupdate":
        $cusId = $_GET['cus'];
        $config->update($cusId);
        break;
        
}

?>

<br><br>
<button type="button" onClick="window.location='index.php'">Home</button>