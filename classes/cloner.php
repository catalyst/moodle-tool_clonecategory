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

namespace local_clonecategory;

use context_system;
use core\task\manager;
use core_course_category;
use core_course_external;
use core_php_time_limit;
use Exception;
use local_clonecategory\event\course_cloned;
use local_clonecategory\task\clone_course_task;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/course/externallib.php');

/**
 *  Cloner class
 *
 * @package    local_clonecategory
 * @copyright  2023 Matthew Hilton <matthewhilton@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cloner {
    /**
     * Prepares the clone by creating the categories if necessary.
     * If a child category name and id number is supplied, a sub-category will be made inside the destination category.
     *
     * @param int $sourcecatid Source category ID
     * @param int $destcatid Destination category ID
     * @param string $destchildname (if making a child category) Name of child category
     * @param string $destchildidnumber (if making a child category) ID Number of child category
     * @return array array containg the source and destination course categories.
     */
    public static function prepare(int $sourcecatid, int $destcatid, string $destchildname = '', string $destchildidnumber = '') {
        global $DB;

        // Category selection.
        $src = core_course_category::get($sourcecatid);
        $dest = core_course_category::get($destcatid);

        // If a destination category name was supplied, create it and update the $dest object.
        if (!empty($destchildname) && !empty($destchildidnumber)) {
            if ($rec = $DB->get_record('course_categories', ['name' => trim($destchildname),
                "idnumber" => trim($destchildidnumber), "parent" => $dest->id])) {
                // We have an existing destination with all these details, use that one.
                $dest = core_course_category::get($rec->id);
            } else {
                $dest = core_course_category::create([
                    "name" => trim($destchildname),
                    "idnumber" => trim($destchildidnumber),
                    "parent" => $dest->id
                ]);
            }
        }

        return [$src, $dest];
    }

    /**
     * Queues the cloning of the courses in the source category.
     * @param core_course_category $src source category
     * @param core_course_category $dest destination category
     * @param string $cloneid Unique id to identify clone in adhoc tasks and logs
     * @param int $startdate Date to set as course start time
     * @param int $enddate Date to set as course end time
     */
    public static function queue(core_course_category $src, core_course_category $dest, string $cloneid, int $startdate = 0,
        int $enddate = 0) {

        $courseids = array_keys($src->get_courses(['recursive' => false]));

        foreach ($courseids as $courseid) {
            $task = new clone_course_task();
            $task->set_custom_data((object) [
                'courseid' => $courseid,
                'destid' => $dest->id,
                'srcid' => $src->id,
                'startdate' => $startdate,
                'enddate' => $enddate,
                // Use a uniqueid to track the clone.
                'cloneid' => $cloneid
            ]);
            manager::queue_adhoc_task($task, true);
        }
    }

    /**
     * Performs the cloning of the given course to the destination category.
     * This is intended to be called from within an adhoc task.
     * @param int $courseid course to clone (in $src category)
     * @param core_course_category $src source category
     * @param core_course_category $dest destination category
     * @param string $cloneid Unique id to identify a group of clones in logs and adhoc tasks
     * @param int $startdate Course start date to set for cloned courses
     * @param int $enddate Course end date to set for cloned courses
     */
    public static function clone_course(int $courseid, core_course_category $src, core_course_category $dest, string $cloneid,
        int $startdate = 0, int $enddate = 0) {

        global $DB;

        core_php_time_limit::raise(600);

        $course = get_course($courseid);
        $idn = explode('_', $course->shortname);
        $shortname = reset($idn) . '_' . $dest->idnumber;

        // If a course matching the shortname and destination category already exists, skip it.
        if ($DB->record_exists("course", ["shortname" => $shortname, "category" => $dest->id])) {
            throw new Exception("Course with shortname {$shortname} already exists in the category. Skipped.");
        }

        $options = [
            ['name' => 'activities', 'value' => 1],
            ['name' => 'blocks', 'value' => 1],
            ['name' => 'filters', 'value' => 1],
            ['name' => 'users', 'value' => 0],
            ['name' => 'role_assignments', 'value' => 0],
            ['name' => 'comments', 'value' => 0],
            ['name' => 'userscompletion', 'value' => 0],
            ['name' => 'logs', 'value' => 0],
            ['name' => 'grade_histories', 'value' => 0]
        ];

        $clone = core_course_external::duplicate_course($course->id, $course->fullname, $shortname, $dest->id, 0, $options);

        $newid = $clone['id'];
        $newshortname = $clone['shortname'];
        $newfullname = str_replace($src->idnumber, $dest->idnumber, $course->fullname);

        $DB->set_field_select('course', 'fullname', $newfullname, "id = ?", [$newid]);
        $DB->set_field_select('course', 'startdate', $startdate, "id = ?", [$newid]);
        $DB->set_field_select('course', 'enddate', $enddate, "id = ?", [$newid]);

        $entry = "Cloned {$course->id}/{$course->shortname} into {$newid}/{$newshortname};";

        // Log success to event log.
        self::log_clone_status($cloneid, $entry, true, $clone['id']);
    }

    /**
     * Logs the status of a clone, by triggering a course_cloned event.
     *
     * @param string $cloneid Unique ID to identify the log
     * @param string $statusmsg
     * @param bool $success true if cloned successfully, else false
     * @param int $courseid
     */
    public static function log_clone_status(string $cloneid, string $statusmsg, bool $success, int $courseid = 0) {
        $event = course_cloned::create([
            "context"  => context_system::instance(),
            "objectid" => $courseid,
            "other" => ["log" => $statusmsg, "cloneid" => $cloneid, "success" => $success]
        ]);
        $event->trigger();

        mtrace($statusmsg);
    }
}
