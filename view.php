<?php
// ------------------------------------------------------
//  Project	Simple model / view
//	File	View.php
//			Populates a view from array data
//
//	Author	John McMillan, McMillan Technolo0gy
//
//	Version	1.0.2
// ------------------------------------------------------
// ---------  Sample use - php file

/*
	$dta = array
	(
		"title" => "Test the view",
		"details" => "Here be details",
		"line2" => "This should be bold"
	);
	$list = array();		// Create a sub array for a list
	$line = array();
	
		$line["line"] = "Line 1";
		$line["data"] = "data 1";
		array_push($loop, $line);
		$line["line"] = "Line 2";
		$line["data"] = "Mersey";
		array_push($list, $line);

	$ar["list"] = $list;
	showView("tst.html", dta);
	
------ html file
<div>
	<h1>{title}</h1>
	Test for {details}<br>
	<b>{line2}</b><br>
	End<br>
	{list}
		List item {line} value {data}<br>
	{/list}<br>
	<br>After the loop<br>
</div>

*/

class View
{
	private $inputStream;
	private $outputStream;
	private $dta;
	private $streamPsn = 0;
	
	function __construct($pstream, $dta)
	{
		$this->inputStream = $pstream;
		$this->dta = $dta;
	}
	
	// -----------------------------------------------
	//	The main function
	//
	//	Called by the public showView function and
	//	iteratively in processing loops
	//
	//	Build the outpur HTML into outputStream
	//	and echo that
	// -----------------------------------------------
	public function go()
	{
		$this->processStream($this->inputStream, $this->dta);
		echo $this->outputStream;
	}
	
	// -----------------------------------------------
	//	Run through the input stream and process it
	//
	//	Parameters	The text of the view file
	//				Array of data passed from php
	// -----------------------------------------------
	private function processStream($stream, $dta)
	{
		$this->streamPsn = 0;			// The next byte to process, start at origin
//		for ($i=0; $i<10; $i++)			// (This can be used for debugging)
		do {
										// Find the next section enclosed in {}
			$pToken = $this->findNextPlaceholder($stream, $this->streamPsn);
			if ($pToken === FALSE)		// No more placeholders - output to end of file
			{
				$str = substr($stream, $this->streamPsn);
				$this->outputStream .= $str;
				break;					// ... and return, all finished!
			}
			$this->streamPsn = $this->processToken($pToken, $stream, $dta);
		} while (TRUE);
	}

	// -----------------------------------------------
	//	Locate the next placeholder
	//
	//	Step over comments, styles and scripts
	//
	//	Parameter input stream
	//
	//	Returns	position of next token (int)
	//			or FALSE when end of file reached
	// -----------------------------------------------
	private function findNextPlaceholder($stream)
	{
		$html = FALSE;
		$localStrPsn = $this->streamPsn;
		while (!$html)
		{
			$phPsn = strpos($stream, '{', $localStrPsn);
			if ($phPsn === FALSE)			// No more placeholders
				return FALSE;
										// Look for comments in forward stream
			$commentPsn = strpos($stream, "<!--", $localStrPsn );
			if ($commentPsn)			// Comment is present
			{
				if ($commentPsn < $phPsn)			// Comment is before next placeholder
				{
					$localStrPsn = strpos($stream, "-->", $commentPsn);
					if ($localStrPsn === FALSE)		// Unterminated comment
						return FALSE;				// ... Echo the rest of the stream
					$localStrPsn += 3;
					continue;						// Skip over the comment
				}
				else 
				{
					;
				}
			}

			$scriptPsn = strpos($stream, "<script", $localStrPsn );
			if ($scriptPsn)							// Script found
			{
				if ($scriptPsn < $phPsn)			// Script is before next placeholder
				{
					$localStrPsn = strpos($stream, "</script>", $scriptPsn);
					if ($localStrPsn === FALSE)		// Unterminated script
						return FALSE;				// ... Echo the rest of the stream
					$localStrPsn += 8;
					continue;						// Skip over the comment
				}
				else 
				{
//					$localStrPsn = $commentPsn;
				}
			}

			$stylePsn = strpos($stream, "<style", $localStrPsn );
			if ($stylePsn)						// Style found
			{
				if ($stylePsn < $phPsn)			// style is before next placeholder
				{
					$localStrPsn = strpos($stream, "</style>", $stylePsn);
					if ($localStrPsn === FALSE)		// Unterminated style
						return FALSE;				// ... Echo the rest of the stream
					$localStrPsn += 7;
					continue;						// Skip over the comment
				}
				else 
				{
//					$localStrPsn = $stylePsn;
				}
			}

			$html = TRUE;
		}
		
		return $phPsn;
	}
	
	// -----------------------------------------------
	//	Process a token (within braces)
	//
	//	Parameters	Pointer to token
	//				Start of the text stream before the token
	//	
	//	Returns		Pointer to stream after the token
	//
	//	Modifies	The output stream
	//
	//	Processes	Pass lists to doLoop
	//				Place the value of the token into
	//				the output stream
	// -----------------------------------------------
	private function processToken($pToken, $stream, $dta)
	{

						// Output the fixed text from the last placeholder
		$strLen = $pToken - $this->streamPsn;
		$str = substr($stream, $this->streamPsn, $strLen);
		$this->outputStream .= $str;

								// Set $token to the token name
		$pTkEnd = strpos($stream, "}", $pToken);
		$len = $pTkEnd - $pToken - 1;
		$token = substr($stream, $pToken+1, $len);

		$newPtr = $pTkEnd + 1;

		if (!array_key_exists($token, $dta))
			die ("View error: element $token not found in data array");
		if (is_array($dta[$token]) )			// The token identifies a list
		{
			$newPtr = $this->doLoop($token, $stream, $newPtr);
			return $newPtr;
		}
		$this->outputStream .= $dta[$token];
		
		return $newPtr;
	}
	
	// -----------------------------------------------
	//	Loop through a list
	//
	//	Parameters	Token that identifies the list
	//				Pointer to input stream at start
	//				Pointer to stream after the token
	//
	//	Returns		Pointer to stream after the list
	// -----------------------------------------------
	private function doLoop($token, $stream, $startPtr)
	{
		$endToken = "{/$token}";			// Locate end of the loop in the html
		$endOfLoop = strpos($stream, $endToken, $startPtr);
		if ($endOfLoop === FALSE)
			die ("View: end token token $endToken not found");
			
		$lengthOfStream = $endOfLoop - $startPtr;
		$thisStream = substr($stream, $startPtr, $lengthOfStream);

		foreach ($this->dta[$token] as $loopAr)
		{
			$this->processStream($thisStream, $loopAr);
		}
		$newPtr = $endOfLoop + strlen($endToken);
		return $newPtr;
	}
}

// --------------------------------------------
//	This is the interface from the PHP script
//
//
//	When complete, echo the merged html
// --------------------------------------------
function showView($viewFile, $dta)
{
	$hFile = fopen($viewFile, 'r')				// Load the view file
		or die ("No view file file $viewFile");
	$source = fread($hFile, filesize($viewFile));
	fclose($hFile);

	$view = new View($source, $dta);			// Construct a View object
	$view->go();								// ... and call it

	return;
}

?>
