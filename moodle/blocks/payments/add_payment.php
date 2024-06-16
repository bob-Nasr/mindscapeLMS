<?php
// add_payment.php
require_once('../../config.php');
require_login();
require_once('add_payment_form.php');

$context = context_system::instance();
require_capability('moodle/site:config', $context);

$mform = new add_payment_form();

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/my'));
} else if ($data = $mform->get_data()) {
    global $DB;

    $payment = new stdClass();
    $payment->userid = $data->userid;
    $payment->amount = $data->amount;
    $payment->timecreated = $data->timecreated;

    $DB->insert_record('payments', $payment);
    redirect(new moodle_url('/my'), get_string('paymentsuccess', 'block_payments'));
}
