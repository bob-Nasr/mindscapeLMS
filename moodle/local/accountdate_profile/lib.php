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
 * This file contains the code for the plugin integration.
 *
 * @package   local_accountdate_profile
 * @copyright 2023, Eugene Mamaev <mamaeves@mail.ru>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * To add the category and node information into the my profile page.
 *
 * @param core_user\output\myprofile\tree $tree The myprofile tree to add categories and nodes to.
 * @param stdClass                        $user The user object that the profile page belongs to.
 * @param bool                            $iscurrentuser If the $user object is the current user.
 * @param stdClass                        $course The course to determine if we are in a course context or system context.
 * @return void
 */
function local_accountdate_profile_myprofile_navigation(core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course) {
    global $CFG, $DB;

    $userdetailscategory = new core_user\output\myprofile\category(
            'userdetails',
            get_string('accountdate', 'local_accountdate_profile')
    );

    $tree->add_category($userdetailscategory);

    if ($user->timecreated > 0) {
        $accountdate = userdate($user->timecreated);
    } else {
        $accountdate = get_string('statusunknown');
    }
    $node = new core_user\output\myprofile\node('userdetails', 'accountdate', $accountdate);
    $tree->add_node($node);

}
