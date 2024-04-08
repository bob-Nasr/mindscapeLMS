<?php

defined('MOODLE_INTERNAL') || die();

/**
 * Checks if a given user exists.
 *
 * @param int $userid The user ID.
 * @return bool True if the user exists, false otherwise.
 */
function local_guardians_user_exists($userid) {
    global $DB;
    return $DB->record_exists('user', array('id' => $userid));
}

/**
 * Inserts a new guardian-student relationship.
 * 
 * @param int $idlegalguardian Legal guardian ID.
 * @param int $userid Student's user ID.
 * @param string $relationship Relationship description.
 * @throws dml_exception If the user does not exist.
 */
function local_guardians_insert_relationship($idlegalguardian, $userid, $relationship) {
    global $DB;
    
    if (!local_guardians_user_exists($userid)) {
        throw new dml_exception('User does not exist: ' . $userid);
    }
    
    $record = new stdClass();
    $record->idlegalguardian = $idlegalguardian;
    $record->userid = $userid;
    $record->relationship = $relationship;
    
    $DB->insert_record('guardians_students', $record);
}

/**
 * Handles the user_deleted event.
 *
 * @param \core\event\user_deleted $event The event object.
 */
function local_guardians_user_deleted(\lib\classes\event\user_deleted $event) {
    global $DB;

    $userid = $event->relateduserid;
    
    // Delete related records from guardians_students
    $DB->delete_records('guardians_students', array('userid' => $userid));
}
