<?php

 defined('MOODLE_INTERNAL') || die();

 $plugin->component = 'local_education'; // Full name of the plugin.
 $plugin->version   = 2023042301;        // The current plugin version (Date: YYYYMMDDXX).
 $plugin->requires  = 2010112400;        // Requires this Moodle version.
 $plugin->maturity  = MATURITY_STABLE;
 $plugin->release   = '1.1 (Build: 2023042301)';
 
// Register the user profile view callback
$handlers = array(
    'myprofile_navigation' => 'local_education_myprofile_navigation',
);
