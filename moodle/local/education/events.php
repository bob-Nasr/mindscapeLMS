
<?php

defined('MOODLE_INTERNAL') || die();

$observers = array(
    array(
        'eventname' => 'lib\classes\event\user_deleted', // Full namespaced path to the event class
        'callback'  => 'local_education_user_deleted', // Function to handle the event
    ),
);
