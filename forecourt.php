<?php

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $amount = $_POST["amount"];
    $phone_number = $_POST["phone_number"];
    
    // Your callback URL (replace with your actual callback URL)
    $callback_url = "https://your-callback-url.com";
    
    // Include stk_push.php
    include 'stkpush.php';
    
    // Initiate payment using function from stk_push.php
    $response = initiate_payment($amount, $phone_number, $callback_url);
    
    // Handle response (e.g., display success or error message)
    if ($response) {
        echo "Payment initiated successfully!";
    } else {
        echo "Error initiating payment. Please try again later.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M-Pesa Payment</title>
</head>
<body>
    <h2>Make Payment Using M-Pesa</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="amount">Amount:</label>
        <input type="text" id="amount" name="amount" required><br><br>
        
        <label for="phone_number">Phone Number:</label>
        <input type="text" id="phone_number" name="phone_number" required><br><br>
        
        <input type="submit" value="Submit">
    </form>
</body>
</html>
