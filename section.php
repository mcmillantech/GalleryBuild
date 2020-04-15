<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Section
{
    private $stream;
    private $optionString;
    private $fixedLen;
    private $prefix;
    private $pfixLen;
    private $ptEnd;
    private $eof;

    public function prefix($prefix)
    {
        $this->prefix = $prefix;
        $this->pfixLen = strlen($this->prefix);
        $this->eof = FALSE;
    }
    
    public function optionString($optionString)
    {
        $this->optionString = $optionString;
echo "Section $this->optionString\n";
    }
    
    public function end()
    {
        return $this->ptEnd;
    }
    
    public function eof()
    {
        return $this->eof;
    }
    /*
    public function set($str)
    {
//        echo "Set section --------------\n" . substr($str, 0, 80) . "\n";
        $this->stream = $str;
        $endPos = strpos($str, '# end');
        $this->fixedLen = strpos($str, "\n", $endPos);
//        $strTr = substr($str, 0, $this->fixedLen -40);
    }
*/    
    // ----------------------------------------------
    // Parameter    Stream, starting at //# option ..
    // 
    // ----------------------------------------------
    public function processOption($stream)
    {
    echo "\nProcess ";
    echo mySubstr($stream, 0, 70);
        $part2 = "";                                    // For the alt content
        
                                        // Extract the number of the option
        $command = mySubstr($stream, $this->pfixLen + 7, 20);
        sscanf ($command, "%d ", $option);
//        echo "opt $option ";

        $pt1 = strpos($stream, "\n") + 1;         // Start of default stream
                                                        // i.e. end of cmd line
        $pt1n = strpos($stream, $this->prefix, $pt1);   // End of default stream
        $part1 = mySubstr($stream, $pt1, $pt1n);        // Content of default stream
       echo "\nPt 1 $pt1,  $pt1n: $part1";
        
        $pt2 = $pt1n + $this->pfixLen;                  // Start of next command
        $action = substr($stream, $pt2, 3);
    echo "Cmd $action\n";
        if ($action == 'alt') {                         // Start, end and content of alt stream
            $pt3 = strpos($stream, "\n", $pt2) + 1;
            $pt3n = strpos($stream, $this->prefix, $pt3);
            $part2 = mySubstr($stream, $pt3, $pt3n);
            $pt4 = $pt3n;
        }
        else
            $pt4 = $pt2;
        
        $pt4 = strpos($stream, "\n", $pt4) + 1;      // Beyoud #end, i.e. fixed content
                                    // point ptEnd to the start of next command
        $this->ptEnd = strpos($stream, $this->prefix, $pt4);
        if ($this->ptEnd === FALSE) {
            $this->eof = TRUE;
            $this->ptEnd = strlen($stream);
            echo "End of file $this->ptEnd \n";
        }
        $fixed = mySubstr($stream, $pt4, $this->ptEnd);

        $useThis = $this->checkOption($option);
echo "Option $option $useThis\n";
        if ($useThis == 1) {
            $vbl = $part1;
        }
        else
            $vbl = $part2;
            
        echo "\n ---------done\n";
        return $vbl . $fixed;
        
    }

    public function fixedPart()
    {
        return substr($this->stream, $this->fixedLen);
    }
    
    private function checkOption($n) {
        return substr($this->optionString, $n-1, 1);
    }
    
}

function mySubstr($str, $start, $end)
    {
        $len = $end - $start;
        return substr($str, $start, $len);
    }
