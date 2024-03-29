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

// Capability definitions for the Attendance Register module.
//
// The capabilities are loaded into the database table when the module is
// installed or updated. Whenever the capability definitions are updated,
// the module version number should be bumped up.
//
// The system has four possible values for a capability:
// CAP_ALLOW, CAP_PREVENT, CAP_PROHIBIT, and inherit (not set).
//
//
// CAPABILITY NAMING CONVENTION
//
// It is important that capability names are unique. The naming convention
// for capabilities that are specific to modules and blocks is as follows:
// [mod/block]/<component_name>:<capabilityname>
//
// component_name should be the same as the directory name of the mod or block.
//
// Core moodle capabilities are defined thus:
// moodle/<capabilityclass>:<capabilityname>
//
// Examples: mod/forum:viewpost
// block/recent_activity:view
// moodle/site:deleteuser
//
// The variable name for the capability definitions array follows the format
// $<componenttype>_<component_name>_capabilities
//
// For the core capabilities, the variable is $moodle_capabilities.

defined('MOODLE_INTERNAL') || die();

$capabilities = [

    'mod/attendanceregister:addinstance' => [
        'riskbitmask'  => RISK_XSS,
        'captype'      => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes'   => [
            'editingteacher' => CAP_ALLOW,
            'manager'  => CAP_ALLOW,
        ],
        'clonepermissionsfrom' => 'moodle/course:manageactivities',
    ],

    'mod/attendanceregister:tracked' => [

        'captype' => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'legacy' => [
            'student' => CAP_ALLOW,
            'teacher' => CAP_PREVENT,
            'editingteacher' => CAP_PREVENT,
            'manager' => CAP_PREVENT,
        ],
    ],


    'mod/attendanceregister:viewotherregisters' => [

        'riskbitmask' => RISK_PERSONAL,

        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'legacy' => [
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        ],
    ],

    'mod/attendanceregister:viewownregister' => [

        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'legacy' => [
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        ],
    ],

    'mod/attendanceregister:addownofflinesess' => [

        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'legacy' => [
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        ],
    ],

    'mod/attendanceregister:deleteownofflinesess' => [

        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'legacy' => [
            'student' => CAP_ALLOW,
        ],
    ],

    'mod/attendanceregister:deleteotherofflinesess' => [

        'riskbitmask' => RISK_PERSONAL,

        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'legacy' => [
            'student' => CAP_PREVENT,
            'teacher' => CAP_PREVENT,
            'editingteacher' => CAP_PREVENT,
            'manager' => CAP_PREVENT,
        ],
    ],

    'mod/attendanceregister:recalcsessions' => [

        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'legacy' => [
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        ],
    ],

    'mod/attendanceregister:addotherofflinesess' => [
        'riskbitmask' => RISK_PERSONAL,

        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'legacy' => [
            'student' => CAP_PREVENT,
            'teacher' => CAP_PREVENT,
            'editingteacher' => CAP_PREVENT,
            'manager' => CAP_PREVENT,
        ],
    ],

];


