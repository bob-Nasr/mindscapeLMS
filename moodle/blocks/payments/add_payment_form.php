<?php
// add_payment_form.php
require_once($CFG->libdir.'/formslib.php');

class add_payment_form extends moodleform {
    public function definition() {
        $mform = $this->_form;

        // Fetch all users
        global $DB;
        $users = $DB->get_records_menu('user', null, 'lastname ASC', 'id, CONCAT(firstname, " ", lastname) as name');
        
        // User select dropdown
        $mform->addElement('select', 'userid', get_string('student', 'block_payments'), $users);
        $mform->setType('userid', PARAM_INT);
        $mform->addRule('userid', null, 'required', null, 'client');

        // Amount
        $mform->addElement('text', 'amount', get_string('amount', 'block_payments'));
        $mform->setType('amount', PARAM_FLOAT);
        $mform->addRule('amount', null, 'required', null, 'client');

        // Hidden element for form validation
        $mform->addElement('hidden', 'timecreated', time());
        $mform->setType('timecreated', PARAM_INT);
    }
}
