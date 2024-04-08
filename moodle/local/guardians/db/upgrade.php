<?php

defined('MOODLE_INTERNAL') || die();

function xmldb_local_guardians_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager(); // Loads the database manager.

    if ($oldversion < 2023040301) {
        // Rename table 'legal_guardians' to 'legalguardians' if it exists
        if ($dbman->table_exists('legal_guardians')) {
            $oldtable = new xmldb_table('legal_guardians');
            $dbman->rename_table($oldtable, 'legalguardians');
        }

        $table = new xmldb_table('legalguardians');
        // Add 'guardianid' as a regular field initially
        $field = new xmldb_field('guardianid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        if (!$dbman->field_exists($table, 'guardianid')) {
            $dbman->add_field($table, $field);
        }
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['guardianid']);

        // Copy data from 'id' to 'guardianid' then drop
        $field = new xmldb_field('id');
        if ($dbman->field_exists($table, $field)) {
            $DB->execute("UPDATE {legalguardians} SET guardianid = id");
            $dbman->drop_field($table, $field);
        }

        // You need to manually handle the primary key constraint in your database if necessary
        // Define table 'legalguardians_relationships' to be created
        $table = new xmldb_table('legalguardians_relationships');
        if (!$dbman->table_exists($table)) {
            // Define fields for 'legalguardians_relationships'
            $table->add_field('idguardiansstudents', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE);
            $table->add_field('guardianid', XMLDB_TYPE_INTEGER, '10');
            $table->add_field('relationship', XMLDB_TYPE_CHAR, '255');
            $table->add_field('userid', XMLDB_TYPE_INTEGER, '10');
            
            // Define keys for 'legalguardians_relationships'
            $table->add_key('primary', XMLDB_KEY_PRIMARY, ['idguardiansstudents']);
            
            // Create table 'legalguardians_relationships'
            $dbman->create_table($table);
        }
        // Upgrade savepoint reached
        upgrade_plugin_savepoint(true, 2023040301, 'local', 'guardians');
    }

    return true;
}
