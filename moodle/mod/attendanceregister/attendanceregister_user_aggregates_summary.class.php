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
 * attendanceregister_user_aggregates_summary.class.php
 * Class containing User's Aggregate in an AttendanceRegister (only for summary aggregates)
 *
 * @package    mod
 * @subpackage attendanceregister
 * @version $Id
 * @author Lorenzo Nicora <fad@nicus.it>
 *
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Represents a User's Aggregate for a Register
 * Holds in a single Object attendanceregister_aggregate records for
 * summary infos only (total & grandtotal)
 * for a User and a Register instance.
 *
 * @author nicus
 */
class attendanceregister_user_aggregates_summary {

    /**
     * Grandtotal of all sessions
     */
    public $grandtotalduration = 0;

    /**
     * Total of all Online Sessions
     */
    public $onlinetotalduration = 0;

    /**
     * Total of all Offline Sessions
     */
    public $offlinetotalduration = 0;

    /**
     * Last calculated Session Logout
     */
    public $lastsassionlogout = 0;

}
