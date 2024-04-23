<?php
// local/education/profilefield.php

defined('MOODLE_INTERNAL') || die();

function local_education_user_profile_display($user) {
    global $DB;

    // Retrieve the student's ID
    $student_id = $user->id;

    // Query to fetch the education records
    $sql = "SELECT e.start_date, e.end_date, et.education_typename
            FROM {education} e
            JOIN {education_type} et ON e.education_type_id = et.education_typeid
            WHERE e.user_id = :user_id";
    $params = array('user_id' => $student_id);
    $educations = $DB->get_records_sql($sql, $params);

    // Display the education information
    if (!empty($educations)) {
        echo '<h3>Educations</h3>';
        echo '<ul>';
        foreach ($educations as $education) {
            echo '<li>';
            echo 'Start Year: ' . $education->start_date . '<br>';
            echo 'End Year: ' . $education->end_date . '<br>';
            echo 'Education Type: ' . $education->education_typename;
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>No education records found.</p>';
    }
}
