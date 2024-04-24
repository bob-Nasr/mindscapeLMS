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

function local_education_myprofile_navigation(core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course)
{
    global $DB;

    // Query to fetch the education records
    $sql = "SELECT e.educationid, e.start_date, e.end_date, e.education_type_id, et.education_typename
           FROM {education} e
           LEFT JOIN {education_type} et ON e.education_type_id = et.education_typeid
           WHERE e.user_id = ?";
    $params = array($user->id);
    $educations = $DB->get_records_sql($sql, $params);

    if (!empty($educations)) {

        // Create a new category for education
        $categoryname = get_string('education', 'local_education');
        $category = new core_user\output\myprofile\category('education', $categoryname, 'contact');

        // Add education records as nodes to the category
        foreach ($educations as $education) {
            // Check if end date is null and set it to "Present"
            $end_date_display = ($education->end_date === null) ? "Present" : $education->end_date;

            // Format education information for display
            // Bro bshrafak rja3 shouf haida
            // Hiye guardian la2anno tdarret, bas mish mafroud tkoun hek
            // Lezim tkoun education, bas 3am ya3tik "not found"
            // --- !!!!!!!!!!!!!!!!!!!!! ---
            $content = new \local_guardians\output\local_guardians_node_content(
                "$education->education_typename"
            );
            $node = new core_user\output\myprofile\node(
                'education',
                'education' . $education->educationid,
                '',
                null,
                null,
                "$education->start_date - $end_date_display",
                $content
            );

            $category->add_node($node);
        }

        // Add the category to the tree
        $tree->add_category($category);
    }
}
