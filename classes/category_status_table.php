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

use core\output\notification;
use core_course_category;
use moodle_url;
use table_sql;

defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir . '/tablelib.php');

/**
 * Clone category queued clones table.
 *
 * @package    local_clonecategory
 * @copyright  2023 Matthew Hilton <matthewhilton@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class category_status_table extends table_sql {

    /** @var core_course_category The destination category */
    private ?core_course_category $destinationcat = null;

    /** @var array array of pending tasks for the linked destination category */
    private array $pendingtasksforcategory = [];

    /**
     * Create table
     * @param string $uniqueid
     * @param int $srcid Source cateogory id
     * @param int $destid Destination category id
     */
    public function __construct(string $uniqueid, int $srcid, int $destid) {
        global $PAGE;

        parent::__construct($uniqueid);

        $columns = [
            'source',
            'dest',
            'status'
        ];

        $headers = [
            get_string('cloning:source', 'local_clonecategory'),
            get_string('cloning:dest', 'local_clonecategory'),
            get_string('cloning:status', 'local_clonecategory')
        ];

        $this->define_columns($columns);
        $this->define_headers($headers);
        $this->baseurl = $PAGE->url;

        $this->destinationcat = core_course_category::get($destid);
        $this->pendingtasksforcategory = cloner::get_pending_tasks_for_destination_category($destid);

        // Select all the courses in the source category.
        $this->set_sql('id,shortname', '{course}', 'category = :srcid', ['srcid' => $srcid]);
        $this->sortable(false, 'id');
        $this->collapsible(false);
    }

    /**
     * Source course col
     * @param object $row
     */
    public function col_source(object $row) {
        return $this->courselink($row->id, $row->shortname);
    }

    /**
     * Destination course col
     * @param object $row
     */
    public function col_dest(object $row) {
        $clonedestination = $this->get_row_corresponding_clone_course($row->shortname);

        if (empty($clonedestination->course)) {
            return $clonedestination->shortname;
        }

        return $this->courselink($clonedestination->course->id, $clonedestination->course->shortname);
    }

    /**
     * Status col
     * @param object $row
     */
    public function col_status(object $row) {
        $clonedestination = $this->get_row_corresponding_clone_course($row->shortname);

        // Does the clone destination course exist? If yes report success.
        if (!empty($clonedestination->course)) {
            return \html_writer::tag('p', get_string('success', 'local_clonecategory'), ['class' => 'badge badge-success']);
        }

        // Is there an adhoc task for the clone source course?
        // If so output either started or inprogress depending on adhoc task status.
        $relevanttasks = array_filter($this->pendingtasksforcategory, fn($t) => $t->get_custom_data()->courseid === (int) $row->id);

        if (!empty($relevanttasks)) {
            $task = array_pop($relevanttasks);

            // Not started yet.
            if (empty($task->get_timestarted())) {
                return \html_writer::tag('p', get_string('notstarted', 'local_clonecategory'), ['class' => 'badge badge-light']);
            }

            // Started.
            $timestamp = userdate($task->get_timestarted());
            $delta = format_time(time() - $task->get_timestarted());
            return \html_writer::tag('p', get_string('started', 'local_clonecategory', $delta), ['class' => 'badge badge-info',
                'title' => $timestamp]);;
        }

        // Else is unknown.
        return '';
    }

    /**
     * Returns the corresponding course (if exists) and shortname.
     * @param string $srccourseshortname The shortname of the source course.
     * @return object containing course object (if exists) and destination short name.
     */
    private function get_row_corresponding_clone_course(string $srccourseshortname): object {
        global $DB;

        // Work out what course the source course should be cloned to.
        $destcourseshortname = cloner::get_shortname_when_cloning_course($srccourseshortname, $this->destinationcat->idnumber);

        // Does this course exist?
        $course = $DB->get_record('course', ['shortname' => $destcourseshortname]);

        return (object) [
            'course' => $course,
            'shortname' => $destcourseshortname
        ];
    }

    /**
     * Generates a html link to the given course.
     * @param int $courseid
     * @param string $text
     * @return string
     */
    private function courselink(int $courseid, string $text): string {
        return \html_writer::link(new moodle_url('/course/view.php', ['id' => $courseid]), $text);
    }

     /**
      * Creates table and renders it.
      * @param int $srcid Source category id
      * @param int $destid Destination category id
      */
    public static function display(int $srcid, int $destid) {
        global $OUTPUT;

        // Show banner which tells user about what categories are being shown.
        $categories = core_course_category::get_many([$srcid, $destid]);
        $headerstring = get_string('cloning:fromto', 'local_clonecategory', [
            'src' => $categories[$srcid]->name,
            'dest' => $categories[$destid]->name
        ]);
        echo $OUTPUT->notification($headerstring, notification::NOTIFY_INFO);

        // Show table to show status.
        $table = new category_status_table(uniqid('category_status_table'), $srcid, $destid);
        $table->set_attribute('class', 'generalbox generaltable table-sm');
        $table->out(100, true);
    }
}
