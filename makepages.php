<?php

// -------------------------------------------------------------
//  Project	Gallery Build
//  File	makepages.php
//
//  Author	John McMillan
//  McMillan Technology 
//
//  Builds pages.xml from the directory of the build folder
//
//  Standalone
// --------------------------------------------------------------

echo "<h1>Make Pages</h1>";

// $dir = "../gallery";

$file = fopen("pages.xml", "w");
                    // Start building the XML file
$xml = '<?xml version="1.0" encoding="UTF-8"?>';
fputs($file, $xml);
fputs($file, "\n<pageset>\n");
//$xml .= "\n";
folder($file, "");
folder($file, "admin/");

fputs($file, "</pageset>\n");

fclose($file);

//  Now test it
$file = fopen("pages.xml", "r");

$pageXML = fread($file, 3000);          // Test the result
$arPages = simplexml_load_string($pageXML);
print_r($arPages);
fclose($file);

// -----------------------------------------
// Make the XML fo a directory
// 
// Parameter    XML output file
//              folder, e.g. admin/
// -----------------------------------------
function folder($file, $folder)
{
    $dir = "source";
    if ($folder != "")
        $dir .= "/$folder";

    $contents = scandir($dir);          // Get the list of files in the folder

    foreach ($contents as $name) {      // Loop through the directory
        if (is_dir($name))              // Skip folders
            continue;

        $name = $folder . $name;
        $str = "    <file>$name</file>\n";
        fputs($file, $str);                 // Write the XML
    }
}

