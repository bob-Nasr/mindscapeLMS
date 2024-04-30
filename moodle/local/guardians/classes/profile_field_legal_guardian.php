<?php
defined('MOODLE_INTERNAL') || die();

class profile_field_legal_guardian {

    public static function get_name() {
        return 'legal_guardian';
    }

    public static function get_description() {
        return get_string('legalguardianfield', 'local_guardians');
    }

    public static function get_type() {
        return 'text'; // Set the field type directly as 'text'.
    }

    public static function is_editable_by_user($user) {
        // Implement logic to determine if the field is editable by the given user.
        return true; // For example, always return true for simplicity.
    }

    public static function get_data($user, $profilefields) {
        // Implement logic to retrieve the legal guardian information for the given user.
        $guardian_name = ''; // Retrieve guardian name from database
        $phone_number = ''; // Retrieve phone number from database
        $relationship = ''; // Retrieve relationship from database
        
        return array(
            'legal_guardian_name' => $guardian_name,
            'phone_number' => $phone_number,
            'relationship' => $relationship
        );
    }

    public static function edit_field($user, $editor, $content) {
        // Implement logic to display the form fields for editing legal guardian information.
        $output = '';
        $output .= html_writer::label(get_string('legalguardianname', 'local_guardians'), 'legal_guardian_name');
        $output .= html_writer::tag('input', '', array('type' => 'text', 'id' => 'legal_guardian_name', 'name' => 'legal_guardian_name', 'class' => 'form-control', 'value' => $content['legal_guardian_name']));
        
        // Add similar HTML output for phone number and relationship fields.

        return $output;
    }

    public static function save_data($user, $data) {
        // Implement logic to save the edited legal guardian information to the database.
        $guardian_name = $data['legal_guardian_name'];
        $phone_number = $data['phone_number'];
        $relationship = $data['relationship'];

        // Update or insert the legal guardian information in the database.
        // Adjust this logic based on your database schema.
    }
}
