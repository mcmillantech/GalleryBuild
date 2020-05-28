<?php
// ------------------------------------------------------
//  Project	Artist Gallery
//  File	artistpaid.php
//		Return from Braintree
//
//  Author	John McMillan, McMillan Technology
// ------------------------------------------------------

    session_start();
    ini_set("display_errors", "1");
    error_reporting(E_ALL);
    require '../common.php';
    $config = setConfig();			// Connect to database
    $mysqli = dbConnect($config);

                                    // Prepend parent dir to Btaintree path
                                    // (Config holds relative to server root)
    $config['braintree'] = "../" . $config['braintree'];

    require_once "../bootstrap.php";

    const TEST = 0;				// Set to 1 to skip emails
    const RERUN = 0;				// Set to 1 to skip Braintree

    $dbConnection = dbConnect($config);

    $gateway = new Braintree_Gateway(
        [
            'environment' => $environment,
            'merchantId' => $merchantId,
            'publicKey' => $publicKey,
            'privateKey' => $privateKey
        ]
    );

/*    $result = $_SESSION['BT'];
    $transaction = $result->transaction;
    var_dump ($transaction);
    exit();
*/
    $userName = userName();
    $ref = getNextOrder();
    
    if (TEST) {
        doSuccess();
        return;
    }
    else if (RERUN == 0) 
        takePayment($gateway);

    echo "To exit, close this browser window";
//    echo "<br><button onClick='window.location.assign(\"index.php\")'>Done</button>";

// ----------------------------------------
//	Process Braintree payment
//	and notify customer
//
// ----------------------------------------
function takePayment($gateway)
{				// Instantiate a Braintree Gateway 
    global $userName, $ref;

    $nonceFromTheClient = $_POST["nonce"];
                                                // Then, create a transaction:
    $result = $gateway->transaction()->sale([
        'amount' => 25.00,
        'paymentMethodNonce' => "$nonceFromTheClient",
        'options' => [ 'submitForSettlement' => true]
    ]);
    if ($result->success) {
        $transaction = $result->transaction;
//    print_r($transaction);
//        $attribs = $result->transaction->_attributes;
        $id = $transaction->id;
        echo "  Id back = $id ";
        doSuccess();
    } else if ($result->transaction) {
        doFailure($result->transaction->processorResponseText);
    } else {
        $msg = $result->message;
        doFailure($msg);
    }
}

// ----------------------------------------
//	Payment failure
//
//	Notify customer
// ----------------------------------------
function doFailure($msg)
{

    echo "We are sorry, payment has not been accepted:<br>";
    echo "The message was $msg<br><br>";
//    $return = "artistfee.php?price=$price";
  //  echo "<br><button onClick='window.location.assign(\"$return\")'>Try again</button><br>";

}

// -----------------------------------
//	Process successful receipt
//
// -----------------------------------
function doSuccess()
{
    global $config, $dbConnection, $userName;

                                        // Present on screen message to artist
    echo "Thank you. Your payment has been received.<br><br>";

    updateUser();
    supportEmail($userName);
}

function writeOrder()
{
    global $mysqli, $ref, $userName;
    
    $dtSQL = date('Y-m-d');			// Makes today's date for SQL insertion
    $sql = "INSERT INTO orders (ref, name, price, date) "
        . "VALUES ($ref, '$userName', 25, '$dtSQL')";
   $mysqli->query($sql)
        or die ("rror writing order $sql");
    
}

// ----------------------------------------
//  send email to site owner
//
//  Parameters	Artist name
// ----------------------------------------
function supportEmail($userName)
{
    global $ref;
    
    $from = ARTIST;
    $replyTo = USER_EMAIL;
    $to = USER_EMAIL;

    $subject = 'Payment received from artist';
    $headers = "From: " . USER_EMAIL . "\r\n" .
        "Reply-To: " . USER_EMAIL . "\r\n" .
        'X-Mailer: PHP/' . phpversion();

    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= "From: $from\r\n"
        . "Reply-To: $replyTo\r\n" 
        . "X-Mailer: PHP/" . phpversion();

    $message = "Hi\r\n\r\n"
        . "Payment has been received from $userName. Reference is $ref";

    if (TEST == 0) {
        $reply = mail($to, $subject, $message, $headers);
        if (!$reply)
            myError(ERR_PM_CUSTOMER_EMAIL, "Email failed to send to $to<br>");
        echo "Mail sent to admin";
    }
    else {
        echo $message;
        echo "---- Customer mail -----";
    }

}

// ----------------------------------------------
//  Update the user
//
// ----------------------------------------------
function updateUser()
{
    global $mysqli;

    $userId = $_SESSION['userId'];
    $status = 4;
    
    $errTxt = "There was a problem updating your record. "
        . "Please tell USER_EMAIL";
    $sql = "UPDATE users SET status=4 WHERE id=$userId";
    $mysqli->query ($sql)
            or die($errTxt);
}

function userName()
{
    global $mysqli;

    $userId = $_SESSION['userId'];
    $sql = "SELECT fullname FROM users where id = $userId";
    $result = $mysqli->query($sql);
    if ($result=== FALSE)
        echo "There has been an error: $sql. Please pass this message to admin";
    $record = mysqli_fetch_array($result, MYSQLI_ASSOC);
    
    return $record['fullname'];
}

// ----------------------------------------------
//	Generate next order number from system table
//
// ----------------------------------------------
function getNextOrder()
{
	global $mysqli;

	$result = $mysqli->query("SELECT * FROM system")
		or die ("System read error");
	$record = $result->fetch_array(MYSQLI_ASSOC);
	$ordId = $record['nextorder'] + 1;
	$sql = "UPDATE system SET nextorder = $ordId";
	$mysqli->query($sql);

	return $ordId;
}

?>
</body>
</html>

