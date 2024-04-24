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

/**
 * Extends navigation for user settings.
 *
 * @param navigation_node $navigation The navigation node.
 * @param stdClass $user The user object.
 * @param context $context The context.
 * @param stdClass $course The course object.
 * @param context $coursecontext The course context.
 */
function local_guardians_extend_navigation_user_settings($navigation, $user, $context, $course, $coursecontext) {
   global $PAGE;
   if (!empty($PAGE->url) && $PAGE->url->compare(new moodle_url('/user/profile.php'), URL_MATCH_BASE)) {
       $navigation->add(
           'Legal Guardians',
           new moodle_url('/local/guardians/user_profile.php', array('id' => $user->id)),
           navigation_node::TYPE_SETTING
       );
   }
}
 
function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}

function local_guardians_myprofile_navigation(core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course) {
   global $DB;

   // Query to fetch the legal guardians' information
   $sql = "SELECT lg.guardianid, lg.namelegalguardians, lg.phonenumber
           FROM {legalguardians} lg
           JOIN {legalguardians_relationships} ls ON lg.guardianid = ls.guardianid
           WHERE ls.userid = $user->id";
   $params = array('student_id' => $user->id);
   $legalguardians = $DB->get_records_sql($sql, $params);

   if (!empty($legalguardians)) {

       // Create a new category for legal guardians
       $categoryname = get_string('legalguardians', 'local_guardians');
       $category = new core_user\output\myprofile\category('legalguardians', $categoryname, 'contact');

       // Add legal guardians' information as nodes to the category
       foreach ($legalguardians as $guardian) {

        $content = new \local_guardians\output\local_guardians_node_content($guardian->namelegalguardians);

        $node = new core_user\output\myprofile\node('legalguardians', 'guardian_'.$guardian->guardianid, '', null, null,
                                                     $guardian->phonenumber, $content);
        $category->add_node($node);
    }

       // Add the category to the tree
       $tree->add_category($category);
   }
}
