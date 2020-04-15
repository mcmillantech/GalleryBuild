<?php
// -------------------------------------------------------------
//  Project	Gallery Build
//  File	Configuration.php
//
//  Author	John McMillan
//              McMillan Technology 
//
//  Implements Customer's configuration class
// --------------------------------------------------------------

require_once "view.php";
require_once "connect.php";

class Configuration {
/*	Generated class */

	private $dbhost;
	private $dbname;
	private $dbuser;
	private $dbpw;
	private $artname;
	private $artfname;
	private $artaddr;
	private $artemail;
	private $artweb;
	private $ckedit;
	private $braintree;
	private $brmerp;
	private $bmers;
	private $brpublicp;
	private $brpublics;
	private $brprivatep;
	private $brprivates;

    function initialise()
    {
	$this->dbhost= '';
	$this->dbname= '';
	$this->dbuser= '';
	$this->dbpw= '';
	$this->artname= '';
	$this->artfname= '';
	$this->artaddr= '';
	$this->artemail= '';
	$this->artweb= '';
	$this->ckedit= '';
	$this->braintree= '';
	$this->brmerp= '';
	$this->bmers= '';
	$this->brpublicp= '';
	$this->brpublics= '';
	$this->brprivatep= '';
	$this->brprivates= '';
    }

    // ----------------------------------------
    // Edit the configuration
    // 
    // Fetch the record and present the edit form
    // ----------------------------------------
    function edit($cusId)
    {
        $mysqli = dbConnect();

        $sql = "SELECT * FROM config WHERE id = $cusId";
        $result = $mysqli->query($sql)
                or die("Error Config edit $sql");
        $record = $result->fetch_assoc();

        $record['action'] = "configupdate";

        showView("config.html", $record);
    }
    
    // ----------------------------------------
    // Insert new record into the database
    // 
    // ----------------------------------------
    function insert()
    {
        $mysqli = dbConnect();

//	$dta = $_GET;
        $cusId = $mysqli->insert_id;
        $cusId =1;
        
/*	$dbhost = addslashes($dta['dbhost']);
	$dbname = addslashes($dta['dbname']);
	$dbuser = addslashes($dta['dbuser']);
	$dbpw = addslashes($dta['dbpw']);
	$artname = addslashes($dta['artname']);
	$artfname = addslashes($dta['artfname']);
	$artaddr = addslashes($dta['artaddr']);
	$artemail = addslashes($dta['artemail']);
	$artweb = addslashes($dta['artweb']);
	$ckedit = addslashes($dta['ckedit']);
	$braintree = addslashes($dta['braintree']);
	$brmerp = addslashes($dta['brmerp']);
	$bmers = addslashes($dta['bmers']);
	$brpublicp = addslashes($dta['brpublicp']);
	$brpublics = addslashes($dta['brpublics']);
	$brprivatep = addslashes($dta['brprivatep']);
	$brprivates = addslashes($dta['brprivates']);
 
 */
	$sql = "INSERT into config ("
	 . "customer,dbhost,dbname,dbuser,dbpw,artname,artfname,artaddr,artemail,artweb,"
          . "ckedit,braintree,brmerp,bmers,brpublicp,brpublics,brprivatep,brprivates"
	 . ") VALUES ("
	 . "$cusId, '$this->dbhost','$this->dbname','$this->dbuser','$this->dbpw','$this->artname','$this->artfname','$this->artaddr','$this->artemail','$this->artweb','$this->ckedit','$this->braintree','$this->brmerp','$this->bmers','$this->brpublicp','$this->brpublics','$this->brprivatep','$this->brprivates'"
	 . ")";
//echo "\n$sql\n";
	$mysqli->query($sql)
            or die("Failed to insert record : " . $mysqli->error);
    }

    // ----------------------------------------
    // Update the database record
    // 
    // ----------------------------------------
    function update($cusId)
    {
        $mysqli = dbConnect();

	$dta = $_POST;
        
	$dbhost = addslashes($dta['dbhost']);
	$dbname = addslashes($dta['dbname']);
	$dbuser = addslashes($dta['dbuser']);
	$dbpw = addslashes($dta['dbpw']);
	$artname = addslashes($dta['artname']);
	$artfname = addslashes($dta['artfname']);
	$artaddr = addslashes($dta['artaddr']);
	$artemail = addslashes($dta['artemail']);
	$artweb = addslashes($dta['artweb']);
	$ckedit = addslashes($dta['ckedit']);
	$braintree = addslashes($dta['braintree']);
	$brmerp = addslashes($dta['brmerp']);
	$bmers = addslashes($dta['bmers']);
	$brpublicp = addslashes($dta['brpublicp']);
	$brpublics = addslashes($dta['brpublics']);
	$brprivatep = addslashes($dta['brprivatep']);
	$brprivates = addslashes($dta['brprivates']);
	$sql = "UPDATE config SET "
	 . "dbhost = '$dbhost',"
	 . "dbname = '$dbname',"
	 . "dbuser = '$dbuser',"
	 . "dbpw = '$dbpw',"
	 . "artname = '$artname',"
	 . "artfname = '$artfname',"
	 . "artaddr = '$artaddr',"
	 . "artemail = '$artemail',"
	 . "artweb = '$artweb',"
	 . "ckedit = '$ckedit',"
	 . "braintree = '$braintree',"
	 . "brmerp = '$brmerp',"
	 . "bmers = '$bmers',"
	 . "brpublicp = '$brpublicp',"
	 . "brpublics = '$brpublics',"
	 . "brprivatep = '$brprivatep',"
	 . "brprivates = '$brprivates'"
	 . " WHERE id=$cusId";

	$mysqli->query($sql)
            or die("Failed to update record : " . $mysqli->error);
    }
    
    // ----------------------------------------
    // Fetch the condiguration record
    // 
    // ----------------------------------------
    function fetch()
    {
        $mysqli = dbConnect();

	$sql = "SELECT * FROM config ";
	$result = $mysqli->query($sql)
		or die("Failed to read record : " . $mysqli->error);
	$record = mysqli_fetch_array($result, MYSQLI_ASSOC);

	$this->dbhost = $record['dbhost'];
	$this->dbname = $record['dbname'];
	$this->dbuser = $record['dbuser'];
	$this->dbpw = $record['dbpw'];
	$this->artname = $record['artname'];
	$this->artfname = $record['artfname'];
	$this->artaddr = $record['artaddr'];
	$this->artemail = $record['artemail'];
	$this->artweb = $record['artweb'];
	$this->ckedit = $record['ckedit'];
	$this->braintree = $record['braintree'];
	$this->brmerp = $record['brmerp'];
	$this->bmers = $record['bmers'];
	$this->brpublicp = $record['brpublicp'];
	$this->brpublics = $record['brpublics'];
	$this->brprivatep = $record['brprivatep'];
	$this->brprivates = $record['brprivates'];

	mysqli_free_result($result);

    }
            
    // ----------------------------------------
    // Create the configuration files for customer
    // 
    // Common.php, config.txt, bootstrap.php
    // ----------------------------------------
    function install()
    {
        $this->fetch();
        $this->makeCommon();
        $this->makeConfig();
        $this->makeBootstrap();
    }
    
    // ----------------------------------------
    // Build common.php
    // 
    // ----------------------------------------
    function makeCommon()
    {
        $inFile = "source/common.php";
        $fh = fopen($inFile, "r")
            or die ("Error source/common.php not found");
        $stream = fread($fh, filesize($inFile));
        fclose($fh);
        
        $outFile = "build/common.php";
        if (file_exists($outFile))
            unlink ($outFile);
        $fhOut = fopen($outFile, "w");
        fwrite($fhOut, $stream);

        $quot = '"';
        $str = "const WEBSITE = $quot$this->artweb$quot;\n";
        fwrite ($fhOut, $str);
        $str = "const USER_EMAIL = $quot$this->artemail$quot;\n";
        fwrite ($fhOut, $str);
        $str = "const USER_ADDRESS = $quot$this->artaddr$quot;\n";
        fwrite ($fhOut, $str);
        $str = "const ARTIST = $quot$this->artname$quot;\n";
        fwrite ($fhOut, $str);
        $str = "const ARTIST_FNAME = $quot$this->artfname$quot;\n";
        fwrite ($fhOut, $str);

        fwrite ($fhOut, "\n?>\n");
        fclose ($fhOut);
    }
    
    // ----------------------------------------
    // Build config.txt
    // 
    // ----------------------------------------
    function makeConfig()
    {
        $outFile = "build/config.txt";
        if (file_exists($outFile))
            unlink ($outFile);
        $fhOut = fopen($outFile, "w");
        
        $str = "dbhost\t\t$this->dbhost\n"; 
        fwrite ($fhOut, $str);
        $str = "dbname\t\t$this->dbname\n";
        fwrite ($fhOut, $str);
        $str = "dbuser\t\t$this->dbuser\n";
        fwrite ($fhOut, $str);
        $str = "dbpw\t\t$this->dbpw\n";
        fwrite ($fhOut, $str);
        
        $str = "images\t\timages\n";
        fwrite ($fhOut, $str);
        $str = "braintree\t$this->braintree\n";
        fwrite ($fhOut, $str);
        $str = "ckeditor\t$this->ckedit\n";
        fwrite ($fhOut, $str);

        fclose ($fhOut);
    }

    // ----------------------------------------
    // Make Braintree bootstrap.php
    // 
    // ----------------------------------------
    function makeBootstrap()
    {
        $inFile = "source/bootstrap.php";
        $fh = fopen($inFile, "r")
            or die ("Error source/bootstrap.php not found");
        $stream = fread($fh, filesize($inFile));
        fclose($fh);
        
        $outFile = "build/bootstrap.php";
        if (file_exists($outFile))
            unlink ($outFile);
        $fhOut = fopen($outFile, "w");
        fwrite($fhOut, $stream);            // Copy start of the file
                                            // Write sandbox section
        $str = "\t\$merchantId = \"$this->bmers\";\n"; 
        fwrite ($fhOut, $str);
        $str = "\t\$publicKey = \"$this->brpublics\";\n"; 
        fwrite ($fhOut, $str);
        $str = "\t\$privateKey = \"$this->brprivates\";\n"; 
        fwrite ($fhOut, $str);
        $str = "\t\$environment = 'sandbox';\n";
        fwrite ($fhOut, $str);

        $str = "}\n// Production keys\n"
            . "else{\n";
        fwrite ($fhOut, $str);
                                            // Write production section
        $str = "\t\$merchantId = \"$this->brmerp\";\n"; 
        fwrite ($fhOut, $str);
        $str = "\t\$publicKey = \"$this->brpublicp\";\n"; 
        fwrite ($fhOut, $str);
        $str = "\t\$privateKey = \"$this->brprivatep\";\n"; 
        fwrite ($fhOut, $str);
        $str = "\t\$environment = 'production';\n";
        fwrite ($fhOut, $str);
        
        fwrite ($fhOut, "\n}\n?>\n");
        fclose ($fhOut);
    }
    
}
