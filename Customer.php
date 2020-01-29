<?php

// -------------------------------------------------------------
//  Project	Gallery Build
//  File	Customer.php
//
//  Author	John McMillan
//              McMillan Technology 
//
//  Implements Customer class, his details and options
// --------------------------------------------------------------

require_once "view.php";
require_once "connect.php";
require_once "PageBuilder.php";
require_once "configuration.php";

define ("N_OPTIONS", 10);

class Customer {
    public $name;
    public $addr1;
    public $addr2;
    public $addr3;
    public $addr4;
    public $postcode;
    public $region;
    public $email;
    public $options;
    public $orderdate;
    public $stylesheet;
    public $bannerstyle;
    private $bannercontent;
    private $bannerimage;
    private $menuStyle;
    private $bodyStyle;

    // ----------------------------------------
    //  Present form to create a customer
    //  
    // ----------------------------------------
    public function create() {
        $dta = array();
        $dta['action'] = "insert";
        $dta['name'] = $this->name;
        $dta['addr1'] = $this->addr1;
        $dta['addr2'] = $this->addr2;
        $dta['addr3'] = $this->addr3;
        $dta['addr4'] = $this->addr4;
        $dta['postcode'] = $this->postcode;
        $dta['email'] = $this->email;
        
        $dta['menustyle'] = $this->menuStyle;
        $dta['bodystyle'] = $this->bodyStyle;
        $dta['configBtn'] = '';
        
        showView("customerform.html", $dta);
    }
    
    public function bannercontent() {
        return $this->bannercontent;
    }
    
    public function bannerstyle() {
        return $this->bannerstyle;
    }

    public function menustyle() {
        return $this->menuStyle;
    }
    
    public function bodystyle() {
        return $this->bodyStyle;
    }

    public function open($id) 
    {
        $dbConnection = dbConnect();
        $sql = "SELECT * FROM customers WHERE id=$id";
        $result = $dbConnection->query($sql)
            or die("Failed to read record : " . $mysqli->error);
        $record = $result->fetch_assoc();
        
        $this->name = $record['name'];
        $this->addr1 = $record['addr1'];
        $this->addr2 = $record['addr2'];
        $this->addr3 = $record['addr3'];
        $this->addr4 = $record['addr4'];
        $this->postcode = $record['postcode'];
        $this->email = $record['email'];
        $this->region = $record['region'];
        $this->options = $record['options'];
        $this->orderdate = $record['orderdate'];
        $this->stylesheet = $record['stylesheet'];
        $this->bannerstyle = $record['bannerstyle'];
        $this->bannercontent = $record['bannercontent'];
        $this->bannerimage = $record['bannerimage'];
        $this->menuStyle = $record['menustyle'];
        $this->bodyStyle = $record['bodystyle'];
    }


    // ----------------------------------------
    //  Present form to edit a customer
    //
    //  Parameter custimer id
    // ----------------------------------------
    public function edit($id) {
        $dbConnection = dbConnect();
        $sql = "SELECT * FROM customers WHERE id=$id";
        $result = $dbConnection->query($sql)
            or die("Failed to read record : " . $mysqli->error);
        $record = $result->fetch_assoc();
        $record['action'] = "update";
        
        for ($n=0; $n<N_OPTIONS; $n++) {
            $optUser = substr($record['options'], $n, 1);
            if ($optUser == 1) {
                $op = "checked";
            } else {
                $op = '';
            }
            $inx = $n+1;
            $fld = "option$inx";
//            echo "$fld = $op \n";
            $record [$fld] = $op;
        }

        $cusId = $record['id'];
        $record['configBtn'] = "<button onclick='config($cusId)'>Configuration</button>";

        showView("customerform.html", $record);
    }

    // ----------------------------------------
    //  Insert new customer into the database
    //  
    //  Data comes from the post
    // ----------------------------------------
    public function insert() {
        $dbConnection = dbConnect();
        
        $name = $_POST['name'];
        $addr1 = $_POST['addr1'];
        $addr2 = $_POST['addr2'];
        $addr3 = $_POST['addr3'];
        $addr4 = $_POST['addr4'];
        $postcode = $_POST['postcode'];
        $email = $_POST['email'];
        $stylesheet = $_POST['stylesheet'];
        $bannerstyle = addslashes($_POST['bannerstyle']);
        $bannercontent = addslashes($_POST['bannercontent']);
        $bannerimage = addslashes($_POST['bannerimage']);
        $menustyle = addslashes($_POST['menustyle']);
        $bodystyle = addslashes($_POST['bodystyle']);

        $sql = "INSERT INTO customers "
            . "(name, addr1, addr2, addr3, addr4, postcode, email" 
            . "stylesheet, bannerstyle, bannercontent, bannerimage) "
                . "menustyle, bodystyle "
            . "VALUES ('$name', '$addr1', '$addr2','$addr3','$addr4', "
            ."'$postcode', '$email', '$stylesheet', '$bannerstyle', "
            . "'$bannercontent' '$bannerimage', $menustyle, $bodystyle)";

	$dbConnection->query($sql)
            or die("Failed to insert record : " . $dbConnection->error);
    }
    
    // ----------------------------------------
    //  Update customer record on the database
    //  
    // ----------------------------------------
    public function update($id) {
//        print_r($_POST);
        $name = $_POST['name'];
        $addr1 = $_POST['addr1'];
        $addr2 = $_POST['addr2'];
        $addr3 = $_POST['addr3'];
        $addr4 = $_POST['addr4'];
        $postcode = $_POST['postcode'];
        $email = $_POST['email'];
        $stylesheet = $_POST['stylesheet'];
        $bannerstyle = addslashes($_POST['bannerstyle']);
        $bannercontent = addslashes($_POST['bannercontent']);
        $bannerimage = addslashes($_POST['bannerimage']);
        $menustyle = addslashes($_POST['menustyle']);
        $bodystyle = addslashes($_POST['bodystyle']);
    
        $options = $this->packOptions();
        $sql = "UPDATE customers SET " 
            . "name= '$name'," 
            . "addr1='$addr1',"
            . "addr2='$addr2',"
            . "addr3='$addr3',"
            . "addr4='$addr4',"
            . "postcode='$postcode',"
            . "email='$email',"
            . "options='$options',"
            . "stylesheet='$stylesheet',"
            . "bannerstyle='$bannerstyle',"
            . "bannercontent='$bannercontent',"
            . "bannerimage='$bannerimage',"
            . "menustyle='$menustyle',"
            . "bodystyle='$bodystyle' "
            . "WHERE id=$id";

	$dbConnection = dbConnect();
        $dbConnection->query($sql)
            or die("Failed to update record : " . $dbConnection->error);
    }
    
    // ----------------------------------------
    //  Pack the options into a string
    //
    //  Fetch values from POST
    //
    //  Returns the packed string
    // ----------------------------------------

    private function packOptions() {
        $string = "";
        for ($i=1; $i<11; $i++) {
            $key = "chopt" . $i;
            if (array_key_exists($key, $_POST)) {
                $string .= '1';
            } else {
                $string .= '0';
            }
        }
        return $string;
    }

    // ----------------------------------------
    // Run the installation for a customer
    // 
    // ----------------------------------------
    public function install() {
        
        // Copy the custom style sheet
//        if (!copy("source/css/$this->stylesheet", "build/custom.css"))
//                echo "Error copying style sheet<br>";
        
        $file = fopen("pages.xml", "r");

        $pageXML = fread($file, 4000);      // Read the list of pages
        $xmlPageList = simplexml_load_string($pageXML);
        fclose($file);
        
        $page = new PageBuilder($this);
        $page->setOptions($this->options);
        $page->go();

        foreach ($xmlPageList as $filename) {
            $page->build((string)$filename);
        }
        
        $this->makeCustomStyle();
        $config = new Configuration;
        $config->install();
    }
    
    // ----------------------------------------
    // Make the customised style sheet
    // 
    // Take the relevant style.css file
    // Append the client's menu and body styles 
    // Write the result to custom.css
    // ------------------------------------------
    private function makeCustomStyle()
    {
        $styleFile = "source/css/" . $this->menuStyle;
        $fh = fopen($styleFile, "r")
            or die ("Error style file $styleFile not found");
        $stream = fread($fh, filesize($styleFile));
        fclose($fh);
        
        $bodyStyle = "\nbody\n{\n" 
            . $this->bodyStyle
            . "\n}\n";
        $stream .= $bodyStyle;
        $fout = "build/custom.css";
        $fh2 = fopen($fout, "w")
            or die ("Failed to create $fout`");
        fwrite($fh2, $stream);
        fclose($fh2);
    }

    // ----------------------------------------
    // ----------------------------------------
    private function makeCustomFile()
    {
        
    }

}
