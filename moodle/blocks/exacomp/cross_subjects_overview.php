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

require __DIR__ . '/inc.php';

$courseid = required_param('courseid', PARAM_INT);

block_exacomp_require_login($courseid);

/* PAGE IDENTIFIER - MUST BE CHANGED. Please use string identifier from lang file */
$page_identifier = 'tab_cross_subjects_overview';

/* PAGE URL - MUST BE CHANGED */
$PAGE->set_url('/blocks/exacomp/cross_subjects_overview.php', array('courseid' => $courseid));
$PAGE->set_heading(block_exacomp_get_string('blocktitle'));
$PAGE->set_title(block_exacomp_get_string($page_identifier));

// build breadcrumbs navigation
block_exacomp_build_breadcrum_navigation($courseid);

$output = block_exacomp_get_renderer();

// build tab navigation & print header
echo $output->header_v2('tab_cross_subjects');

if (block_exacomp_is_teacher() || block_exacomp_is_admin()) {
    $course_crosssubs = block_exacomp_get_cross_subjects_by_course($courseid);
    echo $output->cross_subjects_overview_teacher($course_crosssubs);
} else {
    $course_crosssubs = block_exacomp_get_cross_subjects_by_course($courseid, $USER->id);
    echo $output->cross_subjects_overview_student($course_crosssubs);
}

/* END CONTENT REGION */
echo $output->footer();
