<?php
// local/guardians/profilefield.php

defined('MOODLE_INTERNAL') || die();

function local_guardians_user_profile_display($user)
{
    global $DB, $OUTPUT;

    // Retrieve the student's ID
    $student_id = $user->id;

    // Query to fetch the legal guardians' information
    $sql = "SELECT lg.name, lg.phone
            FROM {local_guardians} lg
            JOIN {local_guardians_students} ls ON lg.id = ls.legalguardian_id
            WHERE ls.student_id = :student_id";
    $params = array('student_id' => $student_id);
    $legalguardians = $DB->get_records_sql($sql, $params);

    // Display the legal guardians' information
    if (!empty($legalguardians)) {
        echo '<h3>Legal Guardians</h3>';
        echo '<ul>';
        foreach ($legalguardians as $guardian) {
            echo '<li>';
            echo 'Name: ' . $guardian->name . '<br>';
            echo 'Phone: ' . $guardian->phone;
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No legal guardians found.</p>';
    }

    // Display the custom field for legal guardian information
    echo '<h3>Legal Guardian Information</h3>';
    $field = new profile_field_legal_guardian();
    $content = $field->get_data($user, null); // Fetch existing data for the field (passing null for $profilefields).
    $field->edit_field($user, null, $content); // Pass null for $editor.
}
