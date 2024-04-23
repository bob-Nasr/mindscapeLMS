<?php

defined('MOODLE_INTERNAL') || die();

function xmldb_local_education_upgrade($oldversion) {
    global $DB;

    if ($oldversion < 2023042301) {
        // Upgrade savepoint reached
        upgrade_plugin_savepoint(true, 2023042301, 'local', 'education');
    }

    return true;
}
