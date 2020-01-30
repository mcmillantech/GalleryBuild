<?php

// -------------------------------------------------------------
//  Project	Gallery Build
//  File	customeraction.php
//
//  Author	John McMillan
//              McMillan Technology 
//
//  Called by buttons in index.php
//
//  Parameters	GET action: create, edit or install
//              id - for edit and install
// --------------------------------------------------------------

require_once 'Customer.php';

$action = $_GET['action'];
if (array_key_exists('id', $_GET)) {
    $id = $_GET['id'];
}

$customer = new Customer();

switch ($action) {
    case "create":
        $customer->create();
        break;
    case "edit":
        $id = $_GET['id'];
        $customer->edit($id);
        break;
    case "install":
        $customer->open($id);
        echo "<h3>Installing customer $customer->name$</h3>";
        $customer->install();
        break;
}

?>
<br>
<input type="button" value="Home" onclick="document.location='index.php'"/>
