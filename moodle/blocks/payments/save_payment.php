<?php

require_once(__DIR__ . '/../../config.php');  // Adjust the path to config.php according to your Moodle installation
require_login();  // Ensure the user is logged in

global $DB, $USER;

// Ensure the script is accessed via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

// Get POST data
$studentname = required_param('studentname', PARAM_TEXT);
$phonenumber = required_param('phonenumber', PARAM_TEXT);
$amount = required_param('amount', PARAM_FLOAT);
$currency = required_param('currency', PARAM_TEXT);
$paymentarea = required_param('paymentarea', PARAM_TEXT);
$date = required_param('date', PARAM_TEXT);

// Log the student name and phone number being searched
error_log("Searching for student name: " . $studentname . " with phone number: " . $phonenumber);

// Prepare the SQL query
$sql = 'SELECT id FROM {user} WHERE CONCAT(firstname, " ", lastname) = ? AND phone1 = ?';
$params = [$studentname, $phonenumber];

// Fetch the user ID based on the student name and phone number
$users = $DB->get_records_sql($sql, $params);

if (empty($users)) {
    // Log the error with detailed information
    error_log("Student not found: " . $studentname . " with phone number: " . $phonenumber);
    echo json_encode(['status' => 'error', 'message' => 'Student not found.']);
    exit;
}

$userid = reset($users)->id;

// Create a DateTime object from the input date
$dateTime = new DateTime($date);

// Get the current time
$currentTime = new DateTime();

// Set the time of the DateTime object to the current time
$dateTime->setTime($currentTime->format('H'), $currentTime->format('i'), $currentTime->format('s'));

// Convert the DateTime object to a Unix timestamp
$timecreated = $dateTime->getTimestamp();

// Prepare the payment record
$payment = new stdClass();
$payment->component = 'block_payments';
$payment->paymentarea = $paymentarea;
$payment->itemid = 0;
$payment->userid = $userid;
$payment->amount = $amount;
$payment->currency = $currency;
$payment->accountid = 1; // Assuming default account id
$payment->gateway = 'manual';
$payment->timecreated = $timecreated;
$payment->timemodified = time();

// Insert the payment record
$DB->insert_record('payments', $payment);

// Return a success response
echo json_encode(['status' => 'success', 'message' => 'Payment saved successfully.']);

