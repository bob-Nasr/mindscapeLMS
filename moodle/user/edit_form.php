<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Form to edit a users profile
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_user
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    //  It must be included from a Moodle page.
}

require_once ($CFG->dirroot . '/lib/formslib.php');

/**
 * Class user_edit_form.
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_edit_form extends moodleform
{

    /**
     * Define the form.
     */
    public function definition()
    {
        global $USER, $CFG, $COURSE, $DB;  // Include global DB

        $mform = $this->_form;
        $editoroptions = null;
        $filemanageroptions = null;
        $usernotfullysetup = user_not_fully_set_up($USER);

        if (!is_array($this->_customdata)) {
            throw new coding_exception('invalid custom data for user_edit_form');
        }
        $editoroptions = $this->_customdata['editoroptions'];
        $filemanageroptions = $this->_customdata['filemanageroptions'];
        $user = $this->_customdata['user'];
        $userid = $user->id;

        if (empty($user->country)) {
            // We must unset the value here so $CFG->country can be used as default one.
            unset($user->country);
        }

        // Accessibility: "Required" is bad legend text.
        $strgeneral = get_string('general');
        $strrequired = get_string('required');

        // Add some extra hidden fields.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'course', $COURSE->id);
        $mform->setType('course', PARAM_INT);

        // Print the required moodle fields first.
        $mform->addElement('header', 'moodle', $strgeneral);

        // Shared fields.
        useredit_shared_definition($mform, $editoroptions, $filemanageroptions, $user);

        // Extra settigs.
        if (!empty($CFG->disableuserimages) || $usernotfullysetup) {
            $mform->removeElement('deletepicture');
            $mform->removeElement('imagefile');
            $mform->removeElement('imagealt');
        }

        // If the user isn't fully set up, let them know that they will be able to change
        // their profile picture once their profile is complete.
        if ($usernotfullysetup) {
            $userpicturewarning = $mform->createElement('warning', 'userpicturewarning', 'notifymessage', get_string('newpictureusernotsetup'));
            $enabledusernamefields = useredit_get_enabled_name_fields();
            if ($mform->elementExists('moodle_additional_names')) {
                $mform->insertElementBefore($userpicturewarning, 'moodle_additional_names');
            } else if ($mform->elementExists('moodle_interests')) {
                $mform->insertElementBefore($userpicturewarning, 'moodle_interests');
            } else {
                $mform->insertElementBefore($userpicturewarning, 'moodle_optional');
            }

            // This is expected to exist when the form is submitted.
            $imagefile = $mform->createElement('hidden', 'imagefile');
            $mform->insertElementBefore($imagefile, 'userpicturewarning');
        }

        // Next the customisable profile fields.
        profile_definition($mform, $userid);

        // Fetch legal guardian info
        if (!empty($userid)) {
            // Assuming the user ID in the user table is linked to legal guardian details
            $guardian_info = $DB->get_record_sql('SELECT lg.namelegalguardians, lg.phonenumber, lgr.relationship
                                                      FROM {legalguardians} lg
                                                      JOIN {legalguardians_relationships} lgr ON lg.guardianid = lgr.guardianid
                                                      WHERE lgr.userid = ?', [$userid]);
            echo "<script>console.log('Guardian Info:', " . json_encode($guardian_info) . ");</script>";
        }

        // Add elements related to the Legal Guardian section
        $mform->addElement('header', 'legalguardian_section', 'Legal Guardians');
        $mform->addElement('text', 'guardian_name', 'Guardian Name', 'size="30"');
        $mform->setType('guardian_name', PARAM_TEXT);
        $mform->setDefault('guardian_name', $guardian_info->namelegalguardians ?? 'test');  // Set fetched name
        $mform->addElement('text', 'guardian_phone', 'Guardian Phone', 'size="30"');
        $mform->setType('guardian_phone', PARAM_TEXT);
        $mform->setDefault('guardian_phone', $guardian_info->phonenumber ?? '');  // Set fetched phone
        $mform->addElement('text', 'guardian_relation', 'Guardian Relation', 'size="30"');
        $mform->setType('guardian_relation', PARAM_TEXT);
        $mform->setDefault('guardian_relation', $guardian_info->relationship ?? '');  // Set fetched relation


        // Add elements related to the Legal Guardian section.
        $mform->addElement('header', 'education_section', 'Education');
        $mform->addElement('text', 'education_name', 'Education Name', 'size="30"');
        $mform->setType('education_name', PARAM_TEXT);
        $mform->addElement('text', 'education_type', 'Education Type', 'size="30"');
        $mform->setType('education_type', PARAM_TEXT);
        $mform->addElement('text', 'education_location', 'Education Location', 'size="30"');
        $mform->setType('education_location', PARAM_TEXT);
        // Add more elements as needed for the Legal Guardian section.

        $this->add_action_buttons(true, get_string('updatemyprofile'));

        $this->set_data($user);
    }

    /**
     * Extend the form definition after the data has been parsed.
     */
    public function definition_after_data()
    {
        global $CFG, $DB, $OUTPUT, $USER;

        $mform = $this->_form;
        $userid = $mform->getElementValue('id');

        // Trim required name fields.
        foreach (useredit_get_required_name_fields() as $field) {
            $mform->applyFilter($field, 'trim');
        }

        if ($user = $DB->get_record('user', array('id' => $userid))) {

            // Remove description.
            if (empty($user->description) && !empty($CFG->profilesforenrolledusersonly) && !$DB->record_exists('role_assignments', array('userid' => $userid))) {
                $mform->removeElement('description_editor');
            }

            // Print picture.
            $context = context_user::instance($user->id, MUST_EXIST);
            $fs = get_file_storage();
            $hasuploadedpicture = ($fs->file_exists($context->id, 'user', 'icon', 0, '/', 'f2.png') || $fs->file_exists($context->id, 'user', 'icon', 0, '/', 'f2.jpg'));
            if (!empty($user->picture) && $hasuploadedpicture) {
                $imagevalue = $OUTPUT->user_picture($user, array('courseid' => SITEID, 'size' => 64));
            } else {
                $imagevalue = get_string('none');
            }
            $imageelement = $mform->getElement('currentpicture');
            $imageelement->setValue($imagevalue);

            if ($mform->elementExists('deletepicture') && !$hasuploadedpicture) {
                $mform->removeElement('deletepicture');
            }

            // Disable fields that are locked by auth plugins.
            $fields = get_user_fieldnames();
            $authplugin = get_auth_plugin($user->auth);
            $customfields = $authplugin->get_custom_user_profile_fields();
            $customfieldsdata = profile_user_record($userid, false);
            $fields = array_merge($fields, $customfields);
            foreach ($fields as $field) {
                if ($field === 'description') {
                    // Hard coded hack for description field. See MDL-37704 for details.
                    $formfield = 'description_editor';
                } else {
                    $formfield = $field;
                }
                if (!$mform->elementExists($formfield)) {
                    continue;
                }

                // Get the original value for the field.
                if (in_array($field, $customfields)) {
                    $key = str_replace('profile_field_', '', $field);
                    $value = isset($customfieldsdata->{$key}) ? $customfieldsdata->{$key} : '';
                } else {
                    $value = $user->{$field};
                }

                $configvariable = 'field_lock_' . $field;
                if (isset($authplugin->config->{$configvariable})) {
                    if ($authplugin->config->{$configvariable} === 'locked') {
                        $mform->hardFreeze($formfield);
                        $mform->setConstant($formfield, $value);
                    } else if ($authplugin->config->{$configvariable} === 'unlockedifempty' and $value != '') {
                        $mform->hardFreeze($formfield);
                        $mform->setConstant($formfield, $value);
                    }
                }
            }

            // Next the customisable profile fields.
            profile_definition_after_data($mform, $user->id);

        } else {
            profile_definition_after_data($mform, 0);
        }
    }

    /**
     * Validate incoming form data.
     * @param array $usernew
     * @param array $files
     * @return array
     */
    public function validation($usernew, $files)
    {
        global $CFG, $DB;

        $errors = parent::validation($usernew, $files);

        $usernew = (object) $usernew;
        $user = $DB->get_record('user', array('id' => $usernew->id));

        // Validate email.
        if (!isset($usernew->email)) {
            // Mail not confirmed yet.
        } else if (!validate_email($usernew->email)) {
            $errors['email'] = get_string('invalidemail');
        } else if (($usernew->email !== $user->email) && empty($CFG->allowaccountssameemail)) {
            // Make a case-insensitive query for the given email address.
            $select = $DB->sql_equal('email', ':email', false) . ' AND mnethostid = :mnethostid AND id <> :userid';
            $params = array(
                'email' => $usernew->email,
                'mnethostid' => $CFG->mnet_localhost_id,
                'userid' => $usernew->id
            );
            // If there are other user(s) that already have the same email, show an error.
            if ($DB->record_exists_select('user', $select, $params)) {
                $errors['email'] = get_string('emailexists');
            }
        }

        if (isset($usernew->email) and $usernew->email === $user->email and over_bounce_threshold($user)) {
            $errors['email'] = get_string('toomanybounces');
        }

        if (isset($usernew->email) and !empty($CFG->verifychangedemail) and !isset($errors['email']) and !has_capability('moodle/user:update', context_system::instance())) {
            $errorstr = email_is_not_allowed($usernew->email);
            if ($errorstr !== false) {
                $errors['email'] = $errorstr;
            }
        }

        // Next the customisable profile fields.
        $errors += profile_validation($usernew, $files);

        return $errors;
    }
}


