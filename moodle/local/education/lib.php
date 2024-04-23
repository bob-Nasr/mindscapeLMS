<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Checks if a given user exists.
 *
 * @param int $userid The user ID.
 * @return bool True if the user exists, false otherwise.
 */
function local_education_user_exists($userid)
{
    global $DB;
    return $DB->record_exists('user', array('id' => $userid));
}

/**
 * Inserts a new education record.
 *
 * @param int $educationid Education ID.
 * @param int $start_date Start date of education.
 * @param int $end_date End date of education.
 * @param int $education_type_id Education type ID.
 * @param int $user_id Student's user ID.
 * @throws dml_exception If the user does not exist.
 */
function local_education_insert_record($educationid, $start_date, $end_date, $education_type_id, $user_id)
{
    global $DB;
    if (!local_education_user_exists($user_id)) {
        throw new dml_exception('User does not exist: ' . $user_id);
    }
    $record = new stdClass();
    $record->educationid = $educationid;
    $record->start_date = $start_date;
    $record->end_date = $end_date;
    $record->education_type_id = $education_type_id;
    $record->user_id = $user_id;
    $DB->insert_record('education', $record);
}

/**
 * Handles the user_deleted event.
 *
 * @param \core\event\user_deleted $event The event object.
 */
function local_education_user_deleted(\core\event\user_deleted $event)
{
    global $DB;
    $userid = $event->relateduserid;
    // Delete related records from education
    $DB->delete_records('education', array('user_id' => $userid));
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
function local_education_extend_navigation_user_settings($navigation, $user, $context, $course, $coursecontext)
{
    global $PAGE;
    if (!empty($PAGE->url) && $PAGE->url->compare(new moodle_url('/user/profile.php'), URL_MATCH_BASE)) {
        $navigation->add(
            'Education',
            new moodle_url('/local/education/user_profile.php', array('id' => $user->id)),
            navigation_node::TYPE_SETTING
        );
    }
}

/**
 * Navigation callback to display education in user profile.
 *
 * @param core_user\output\myprofile\tree $tree The profile tree.
 * @param stdClass $user The user object.
 * @param bool $iscurrentuser True if the user is the current user, false otherwise.
 * @param stdClass $course The course object.
 */
function local_education_myprofile_navigation(core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course)
{
    global $DB;

    // Query to fetch the education records
    $sql = "SELECT e.educationid, e.start_date, e.end_date, e.education_type_id
           FROM {education} e
           WHERE e.user_id = ?";
    $params = array($user->id);
    $educations = $DB->get_records_sql($sql, $params);
    debug_to_console("Test1");

    if (!empty($educations)) {
        debug_to_console("Test2");

        // Create a new category for education
        $categoryname = get_string('education', 'local_education');
        $category = new core_user\output\myprofile\category('education', $categoryname, 'contact');

        // Add education records as nodes to the category
        foreach ($educations as $education) {
            debug_to_console("Test3");
            debug_to_console( $education->educationid);
            debug_to_console( $education->start_date);
            debug_to_console( $education->end_date);
            debug_to_console( $education->education_type_id);
            debug_to_console( "dONE");

            // Format education information for display

            // $content = "Start Year: " . $education->start_date . ", End Year: " . $education->end_date;
            $content = new \local_education\output\local_education_node_content($education->start_date);
            $node = new core_user\output\myprofile\node('education', 'education'.$education->educationid, '', null, null,
                                                         $education->educationid, $content);

            $category->add_node($node);

        }

        // Add the category to the tree
        $tree->add_category($category);

    }
}
