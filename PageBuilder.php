<?php

// -------------------------------------------------------------
//  Project	Gallery Build
//  File	PageBuilder.php
//
//  Author	John McMillan
//              McMillan Technology 
//
//  Implements PageBuilder class, used to build each page with options
//
//  Call points, all from Customer->install are 
//              setOptions to set the options string
//              go which creates the change set
//              build which builds one page
// --------------------------------------------------------------

ini_set("display_errors", "1");
error_reporting(E_ALL);

require_once 'Customer.php';
require_once 'section.php';

class PageBuilder {
    private $optionString;
    private $name;
    private $changeAr;              // Changes from options.xml in option sequence 
    private $changeSet;             // Changes from options.xml ordered by page 
    private $optionSet;             // Holds the content of option in source file
    private $customer;

//    public function __construct($changeSet) {
  //      $this->changeSet = $changeSet;
        
    public function __construct(Customer $cus) {
        $this->changeAr = array();
        $this->optionSet = array();
        $this->customer = $cus;
    }

    public function setOptions($param) {
        $this->optionString = $param;
echo "Set page options $param <br>";
    }
    
    // ---------------------------------------------------------
    // Call point from Customer -> install
    // 
    // Processes options.xml to create changes ordered by page
    // -----------------------------------------------------------
    public function go()
    {
        $xml = simplexml_load_file("options.xml");
                            // Take every XML elemebt option element, split it
                            // and add the file and option to the changeAt array
        foreach($xml->children() as $option) {
            $this->processOptionXML($option);
        }
        $this->changeSet = $this->sortOptions();    // And now sort to page seq
//        print_r($this->changeSet);
//        echo "<br>";
    }

    // ---------------------------------------------------------
    // Process an option from options.xml
    // 
    // parameter    SimpleXML for the option
    // 
    // Creates changeAr - changes in option sequence
    // ---------------------------------------------------------
    private function processOptionXML($option)
    {
        $id = (int)$option->id;         // Fetch the option id
                                        // Is this option to be used?
        if ($this->checkOption($id) == 0)
            return;                     // No - ignore it

        $changes = $option->changes;    // Drill into the options
            foreach($changes as $change) {
                foreach ($change as $page) {
                                        // Fetch the file and action for each
                    $thisFile = (string)$page->file;
                    $action = (string)$page->action;

                $ar = array(            // This holds an action by id
                    "option" => $id,
                    "action" => $action,
                    "file" => $thisFile
                );
                array_push($this->changeAr, $ar);
            }
        }
//        print_r($this->changeAr);
    }

    // ---------------------------------------------------------
    // Sort the options by page
    // 
    // Input    The changeAr array
    // 
    // Returns  Array of the action for each file
    // ---------------------------------------------------------
    private function sortOptions()
    {
        $pageActions = array();             // For the result
                                            // Sort changes by file
        usort($this->changeAr, "cmp");

        foreach ($this->changeAr as $line) {
            $file = $line['file'];
            $action = $line['action'];
            if (array_key_exists($file, $pageActions)) {
                continue;
            }
            $pageActions[$file] = $action;
        }
//    print_r($pageActions);
        return $pageActions;
    }

    // ---------------------------------------------------------
    // Main call point from Customer install
    // Build one page
    // 
    // Parameter    File name
    // 
    // Fetches the file level action for the file
    // If action is remove, do nothing. For default or replace,
    // pass the source and target files to buildPageOn.
    // 
    // This can be confusing. The default is the whole package
    // (option flag 1). We have to remove unused options, so
    // only process options where flag is 0
    // ---------------------------------------------------------
    public function buildPage($page, $sourcePath) {
        $this->name = $page;
        
        $source = "Source/$page";           // Name the source and targets
                                            // Check for an entry in change set
        $actionString = $this->checkForChanges($page);
                    // Replace command has a tail - remove it from the command
        if (substr($actionString, 0, 7) == "replace")
            $action = "replace";
        else
            $action = $actionString;

//    echo "<br>Action $actionString, $action";
        switch ($action) {              // Action is file to remove or replace
            case "remove":
                return;
            case "":                    // Default, just copy file
                $source = "$page";
                break;
            case "replace":
                $source = $this->getReplaceName($actionString);
                echo "<br>$page Replace with $source \n";
                break;
        }
//        echo " $source ";
                                        // Now $source is the file to process
        $target = "build/$page";
        $this->buildPageOn($source, $target, $sourcePath); // ... process and copy
    }
    
    // ----------------------------------------
    // See if there are changes for a file
    // 
    // Returns the action if so, otherwise blank
    // ----------------------------------------
    public function checkForChanges($file)
    {
        if (array_key_exists($file, $this->changeSet)) {
            $action= $this->changeSet[$file]; // Get the changes for this file
            return $action;
        }
        return "";
    }

    // ----------------------------------------
    // Perform the build
    // 
    // Parameters source and target file names
    // 
    // $stream is the content of the source
    // that has not been processed
    // ----------------------------------------
    private function buildPageOn($src, $target, $sourcePath)
    {
        $source = $sourcePath . $src;
        //echo "<br>Source $source\n";
    
        $ar = explode('.', $src);            // Get file type
        if (count($ar) < 2) {                   // Probably a directory
            echo "Error in $source: size wrong<br>";
            return;
        }
        $type = $ar[1];
                                        // Open source file and create target
        $sFile = @fopen($source, 'r');
        if ($sFile === FALSE) {
            echo "<br>Error buildPageOn $src, $sourcePath $source not found<br>";
            return FALSE;
        }
                                        // Read the source file
        $inStream = fread($sFile, filesize($source));
        fclose($sFile);
        $tFile = fopen($target, 'w');
        
        if ($src == "topv.html")  // The top file needs the customer banner
            $inStream = $this->makeBanner ($inStream);

        $prefix = "";                       // Set the start of token
        switch($type) {                     // according to file type
            case "php":
                $prefix = "//# ";
                break;
            case "html":
                $prefix = '<!--# ';
                break;
            default :                       // Just copy JS and CSS files
                echo "Other type $source <br>";
                fwrite ($tFile, $inStream);
                fclose($tFile);
                return;
        } 

        $command = $prefix . "option";      // Search string for commands

        $section = new Section();           // Set up a Section object
        $section->prefix($prefix);
        $section->optionString($this->optionString);

        $newStream = $inStream;             // Now loop throught the sections
        $psn = strpos($newStream, $command);    // Find the firts
        if ($psn === FALSE) {               // No changes
            fwrite($tFile, $inStream);
            fclose($tFile);
            return;
        }

        $firstSec = substr($inStream, 0, $psn); // Output the content before 1st sec
        fwrite($tFile, $firstSec);

        do {
            $newStream = substr($newStream, $psn);
            $output = $section->processOption($newStream);
            fwrite($tFile, $output);
            $psn = $section->end();
        } while (!$section->eof());

        fclose($tFile);
        return;
    }

    // ----------------------------------------------------
    // Make the top banner
    // 
    // Modify topv.html, replace the placeholder
    // with customer content
    // ----------------------------------------------------
    private function makeBanner($stream)
    {
        $BANNER = "<!--# banner -->";
        
        if (strpos($stream, $BANNER) === FALSE) {
            echo "No banner<br>";
            return $stream;
        }

        $content = $this->customer->bannercontent();

        return str_replace($BANNER, $content, $stream);
    }

    private function checkOption($n) {
        return substr($this->optionString, $n-1, 1);
    }
    
    private function getReplaceName($action)
    {
        $ar = explode(" ", $action);
        return $ar[1];
    }
}

function trace($str)
{
    $hFile = fopen("trace.txt", "w");
    fwrite($hFile, $str);
    fclose($hFile);
}

function cmp($a, $b)
{
    return strcmp($a["file"], $b["file"]);
}

function trx($str)
{
    echo substr($str, 0, 40) . "\n";
   
}