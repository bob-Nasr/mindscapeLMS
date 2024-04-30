<?php
/**
 * Form handler for processing legal guardian information.
 *
 * @package    local_guardians
 * @subpackage classes
 * @copyright  Copyright (c) [2024], [Mindscape]
 */

// Ensure this file is included at the top of any PHP file that uses the Moodle API.
require_once(__DIR__ . '/../../../config.php');

// Ensure the user is logged in.
require_login();

// Get the user ID of the current user.
$userid = $USER->id;

// Retrieve the submitted form data.
$guardian_name = optional_param('namelegalguardians', '', PARAM_TEXT);
$phone_number = optional_param('phonenumber', '', PARAM_TEXT);
$relationship = optional_param('relationship', '', PARAM_ALPHA);

// Load the user's legal guardian information.
$guardian_relationship = $DB->get_record('legalguardians_relationships', array('userid' => $userid));

// Update the legal guardian information in the `legalguardians` table.
if ($guardian_relationship) {
    // Update existing legal guardian record.
    $guardian = $DB->get_record('legalguardians', array('guardianid' => $guardian_relationship->guardianid));
    if ($guardian) {
        $guardian->guardian_name = $guardian_name;
        $guardian->guardian_number = $phone_number;
        $DB->update_record('legalguardians', $guardian);
    }
} else {
    // Create new legal guardian record.
    $guardian = new stdClass();
    $guardian->guardian_name = $guardian_name;
    $guardian->guardian_number = $phone_number;
    $guardianid = $DB->insert_record('legalguardians', $guardian);

    // Update the legal guardian relationship in the `legalguardians_relationships` table.
    $guardian_relationship = new stdClass();
    $guardian_relationship->userid = $userid;
    $guardian_relationship->guardianid = $guardianid;
    $guardian_relationship->relationship = $relationship;
    $DB->insert_record('legalguardians_relationships', $guardian_relationship);
}

// Redirect back to the user profile page after form submission.
redirect(new moodle_url('/user/profile.php'));
