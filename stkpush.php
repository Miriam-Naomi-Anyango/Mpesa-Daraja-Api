<?php
//INCLUDE THE ACCESS TOKEN FILE
include 'accessToken.php';
date_default_timezone_set('Africa/Nairobi');
$processrequestUrl = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
$callbackurl = 'https://starofhopeafh.com/darajaapp/callback.php';
$passkey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
$BusinessShortCode = '174379';
$Timestamp = date('YmdHis');
// ENCRIPT  DATA TO GET PASSWORD
$Password = base64_encode($BusinessShortCode . $passkey . $Timestamp);
$phone = '254769996443';//phone number to receive the stk push
$money = '1';
$PartyA = $phone;
$PartyB = '254708374149';
$AccountReference = 'MICHRI SOFTWARES';
$TransactionDesc = 'stkpush test';
$Amount = $money;
$stkpushheader = ['Content-Type:application/json', 'Authorization:Bearer ' . $access_token];
//INITIATE CURL
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $processrequestUrl);
curl_setopt($curl, CURLOPT_HTTPHEADER, $stkpushheader); //setting custom header
$curl_post_data = array(
  //Fill in the request parameters with valid values
  'BusinessShortCode' => $BusinessShortCode,
  'Password' => $Password,
  'Timestamp' => $Timestamp,
  'TransactionType' => 'CustomerPayBillOnline',
  'Amount' => $Amount,
  'PartyA' => $PartyA,
  'PartyB' => $BusinessShortCode,
  'PhoneNumber' => $PartyA,
  'CallBackURL' => $callbackurl,
  'AccountReference' => $AccountReference,
  'TransactionDesc' => $TransactionDesc
);

$data_string = json_encode($curl_post_data);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
echo $curl_response = curl_exec($curl);

//ECHO  RESPONSE
$data = json_decode($curl_response, true);
// var_dump($data);
// Access CheckoutRequestId and ResultCode
$checkoutRequestId = $data['CheckoutRequestID'];
$resultCode = $data['ResponseCode'];
// if ($ResponseCode == "0") {
//   echo "The CheckoutRequestID for this transaction is : " . $CheckoutRequestID;
// }


// Establish a connection to the remote database
$servername = "108.163.255.210";
$username = "starofho_miriam";
$password = "mFcBp^=BQ~5J";
$dbname = "starofho_mpesa";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// var_dump($conn);

sleep(10);
function handleCallback() {
// while (time() - $start_time < 180) {
    // Check if fetch request returns data
    $data = fetchData($conn, $checkoutRequestId); // Assuming fetchData() is your function to fetch data
    if (!empty($data)) {
        // Data received, handle it
        echo "end string";
        handleData($data); // Assuming handleData() is your function to handle data
        // break; // Exit the loop
    }

//    // Sleep for a short interval to avoid high CPU usage
    // usleep(100000); // Sleep for 100 milliseconds (adjust as needed)
// }

// If the loop completes without fetching data
// if (time() - $start_time >= 180) {
//     echo "Loop ran for 3 minutes without fetching data.";
// }

// Function to fetch data
function fetchData($conn, $checkoutRequestId) {
    // Prepare the SQL statement with a placeholder for the CheckoutRequestID
    $sql = "SELECT * FROM transactions WHERE CheckoutRequestID=?";
    
    // Prepare the statement
    $stmt = $conn->prepare($sql);
    
    // Bind the parameter
    $stmt->bind_param("s", $checkoutRequestId);
    
    // Execute the statement
    $stmt->execute();
    
    // Get the result
    $result = $stmt->get_result();

    $data = array();
    if ($result->num_rows > 0) {
        // Fetch data row by row
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    
    // Close the statement
    $stmt->close();
    
    return $data;
}

// Function to handle data
function handleData($data) {
    // Implement your logic to handle the fetched data
    foreach ($data as $row) {
        // Check the value of ResultCode
        $resultCode = $row['ResultCode'];
        
        // Redirect to payment_response.php
        header("Location: payment_response.php");
        exit();

        // Display result using semantic colors
        if ($resultCode == 0) {
            echo '<div style="color: green;">Payment Successful</div>';
            } else {
            echo '<div style="color: red;">Payment Failed</div>';
        }
    }
}
}
// Close the database connection
// $conn->close();





//CHECK IF THE TRASACTION WAS SUCCESSFUL 
// if ($ResultCode == 0) {
//   //STORE THE TRANSACTION DETAILS IN THE DATABASE
//   mysqli_query($db, "INSERT INTO transactions (MerchantRequestID, CheckoutRequestID, ResultCode, Amount, MpesaReceiptNumber, PhoneNumber) VALUES ('$MerchantRequestID','$CheckoutRequestID','$ResultCode','$Amount','$TransactionId','$UserPhoneNumber')");
// }