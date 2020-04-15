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
    // Updates      changeAr - changes in option sequence
    // ---------------------------------------------------------
    private function processOptionXML($option)
    {
        $id = (int)$option->id;         // Fetch the option id
                                        // Is this option to be used?
        if ($this->checkOption($id) == 1)
            return;                     // No - ignore it
//echo "Op $id ";
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
    public function buildPage($page) {
        $this->name = $page;
  echo "\n<br>Build $page\n";
        
        $source = "Source/$page";           // Name the source and targets
                                            // Checl for an entry in change set
        $action = $this->checkForChanges($page);
        switch ($action) {                  // Action is file to remove or replace
            case "remove":
  echo "Remove ";
                return;
            case "":
                $source = "Source/$page";
                break;
            default :
                $source = $this->getReplaceName($action);
//    echo "<br>Procesing $name\n";
                echo " Replace with $source ";
                break;
        }
                                        // Now $source is the file to process
        $target = "build/$page";
        $this->buildPageOn($source, $target); // ... process and copy
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
    private function buildPageOn($source, $target)
    {
        echo getcwd();
        echo " Source $source\n";
        $ar = explode('.', $source);            // Get file type
        if (count($ar) < 2) {                   // Probably a directory
            echo "Error in $source: size wrong<br>";
            return;
        }
//        print_r($ar);
        $type = $ar[1];
                                        // Open source file and create target
        $sFile = @fopen($source, 'r');
        if ($sFile === FALSE) {
            echo "Error $source not found<br>";
            return;
        }
                                        // Read the source file
        $inStream = fread($sFile, filesize($source));
        fclose($sFile);
        $tFile = fopen($target, 'w');
        
        if ($source == "source/topv.html")  // The top file needs the customer banner
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
                echo "Other type $source";
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
        $firstSec = substr($inStream, 0, $psn); // Output the content before 1st sec
//        $output = $firstSec;
        fwrite($tFile, $firstSec);

        do {
            $newStream = substr($newStream, $psn);
            $output = $section->processOption($newStream);
            fwrite($tFile, $output);
            $psn = $section->end();
        } while (!$section->eof());

        fclose($tFile);
        return;
/*        
        $sections = explode($Command, $inStream);
        print_r($sections);
        foreach ($sections as $str) {
            $section->set ($str);
            $fixed = $section->fixedPart();
//            echo $fixed;
        }
        $cursor = 0;
                            // Now break the input into sections between commands
        while ($ptk = strpos($inStream, $Command)) {     // Locate next token
            $length = $ptk - $cursor;           // The length of the section
                                                // Output the section
            $section = substr ($inStream, $cursor, $length);
            fwrite($tFile, $section);
            
            $inStream = substr($inStream, $length); // Stream is now start of command
            $cmdLen = $this->processCommand($inStream, $Command);
            $this->writeOption($tFile);
            
            $inStream = substr($inStream, $cmdLen);
        }
*/
        fwrite ($tFile, $inStream);               // Write to end of file
        fclose($tFile);
    }

    // ----------------------------------------------------
    //  Write out the option code
    //  
    // ----------------------------------------------------
    private function writeOption($tFile)
    {
//echo "Write ";
//print_r($this->optionSet);
        $option = $this->optionSet['id'];
        $use = $this->checkOption($option);
        if ($use == 1) {
            $str = $this->optionSet['content'];
        } else {
            if (array_key_exists('altcontent', $this->optionSet))
                $str = $this->optionSet['altcontent'];
            else {
                $str = '';    
            }
        }
        fwrite($tFile, $str);
    }

    // ----------------------------------------------------
    //  Process a command (e.g. //#
    //  
    //  Parameters  Stream pointing to command
    //              HTLM or PHP command
    // ----------------------------------------------------
    private function processCommand($stream, $command)
    {
        $cmdLen = $this->decodeCommand($stream, $command);
//echo "After decode ";
//print_r($this->optionSet);
        
        return $cmdLen;
    }
    
    // ----------------------------------------------------
    // Decode the command
    // 
    // Parameters   Input stream at start of command
    //              Command, //# or <!--
    //
    //  Sets    optionSet - array
    //              error flag
    //              option id
    //              content
    //              alt content
    // ----------------------------------------------------
    private function decodeCommand($stream, $command)
    {
        $optionSet = array();
        $cursor = 0;
        $cmdLen = 0;
        $optionSet['err'] = 1;
                                    // Fetch 1st action - must be 'option'
        $cmd = $this->getCommand($stream, $command);
    // print_r($cmd);
        $len = $cmd[3];             // This is the end of the action line
        $cmdLen += $len;
/*        
        if ($cmd[1] == 'banner') {
            $stream = $this->makeBanner($stream);
            return $cmdLen;
        }
*/

        if ($cmd[1] != 'option') {
            echo "Error: option command expected $cmd[1] found<br>";
            return $cmdLen;
        }
        
        $optionSet['id'] = $cmd[2];
        $stream = substr ($stream, $len);       // Pt1 - content
        $pNext = strpos($stream, $command);      // Pt2 - end or alt command
        $str = substr($stream, 0, $pNext);      // Content is pt1 to pt2
        $optionSet['content'] = $str;
        $cmdLen += $pNext;
        
        $stream = substr ($stream, $pNext);
//trx($stream);
        $cmd2 = $this->getCommand($stream, $command);
//    print_r($cmd2);
        $len2 = $cmd2[3];
        $cmdLen += $len2;
        if ($cmd2[1] == 'end') {
            $optionSet['cmdLen'] = $cmdLen;
            $optionSet['err'] = 0;
            $this->optionSet = $optionSet;
            return $cmdLen;
        }

        if ($cmd2[1] != 'alt') {                 // Should now have read alt
            echo "Error: alt or end command expected<br>";
            return $cmdLen;
        }
        
        $stream = substr ($stream, $len2);      // Pt3 - alt content
        $pEnd = strpos($stream, $command);     // Pt4 - end
        $str2 = substr($stream, 0, $pEnd);      // Content is pt3 to pt4
        $optionSet['altcontent'] = $str2;
        $cmdLen += $pEnd;
        
        $stream = substr($stream, $pEnd);   // Now alt or end
        $cmd3 = $this->getCommand($stream, $command);
        $len3 = $cmd3[3];
        $cmdLen += $len3;
//print_r($cmd3);
        if ($cmd3[1] != 'end') {                 // Should now have read alt
            echo "Error: end command expected<br>";
            return $cmdLen;
        }
        
        $optionSet['err'] = 0;
        $this->optionSet = $optionSet;
        return $cmdLen;
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
//        echo "Make banner<br>";
        
        if (strpos($stream, $BANNER) === FALSE)
            return $stream;

        $content = $this->customer->bannercontent();

        return str_replace($BANNER, $content, $stream);
    }

    // ----------------------------------------------------
    // decode one line of command
    // 
    // Returns  aaray holding:
    //      the action
    //      option id
    //      start of command
    //      length of command line
    // ----------------------------------------------------
    private function getCommand($stream, $command)
    {
        $len = strpos($stream, "\n");
        $cmdStr = substr($stream, 0, $len);
//    echo "GC $cmdStr ";
        $ar = explode(' ', $cmdStr);
        $ar[3] = $len;

        return $ar;
    }

    // --------------------------------------------------------
    // Locate and decode one command line
    // 
    // Parameters   Input stream from start of token
    //              Offset to start of text after last line
    //              Comparator
    //              
    //  Generate an array holding the start of the line
    //  and the following text, the command and parameter
    //  
    //  Returns array:
    //      Start of token
    //      End of token
    //      Command
    //      Option
    // --------------------------------------------------------
    private function getToken($stream, $offset, $pCommand) 
    {
        $set = array();
                                            // Locate the next line
        $start = strpos($stream, $pCommand, $offset);
        $set['start'] = $start;
                                            // And text after the line
        $end = strpos($stream, "\n", $start + 1) + 1;
        $set['end'] = $end;
                                            // Fetch the command line
        $line = substr($stream, $start, $end - $start);
        $token = explode(' ', $line);       // and extract the command
        $command = $token[1];
        if ($command == 'option') {
            $option = $token[2];
        } else {
            $option = 0;                    // Why the 0?
        }
        $set['command'] = $command;
        $set['option'] = $option;
//    echo "<br>Get token $start, $end, $command $option<br>";
// print_r($set);
        return $set;
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