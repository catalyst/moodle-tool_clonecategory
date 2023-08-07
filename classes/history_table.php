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

namespace tool_clonecategory;

use table_sql;

defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir . '/tablelib.php');

/**
 * Clonecategory log history table
 *
 * @package    tool_clonecategory
 * @copyright  2023 Matthew Hilton <matthewhilton@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class history_table extends table_sql {
     /**
      * Create table
      * @param string $uniqueid
      * @param int $startdate
      * @param int $enddate
      */
    public function __construct(string $uniqueid, int $startdate, int $enddate) {
        global $PAGE, $DB;

        parent::__construct($uniqueid);

        $columns = [
            'timecreated',
            'message',
            'status'
        ];

        $headers = [
            get_string('history:time', 'tool_clonecategory'),
            get_string('history:message', 'tool_clonecategory'),
            get_string('history:status', 'tool_clonecategory'),
        ];

        $this->define_columns($columns);
        $this->define_headers($headers);
        $this->baseurl = $PAGE->url;
        $this->sortable(false, 'timecreated', SORT_DESC);
        $this->startdate = $startdate;
        $this->enddate = $enddate;
    }

    /**
     * Timecreated col
     * @param object $event
     */
    public function col_timecreated($event) {
        return userdate($event->timecreated);
    }

    /**
     * Message col
     * @param object $event
     */
    public function col_message($event) {
        return $event->other['log'];
    }

    /**
     * Status column.
     * @param object $event
     */
    public function col_status($event) {
        $success = $event->other['success'] ?? '';

        if ($success === false) {
            return \html_writer::tag('p', get_string('failed', 'tool_clonecategory'), ['class' => 'badge badge-danger']);
        }

        if ($success === true) {
            return \html_writer::tag('p', get_string('success', 'tool_clonecategory'), ['class' => 'badge badge-success']);
        }

        // Unknown success status.
        return '';
    }

    /**
     * Creates table and renders it.
     * @param int $startdate
     * @param int $enddate
     */
    public static function display(int $startdate = 0, int $enddate = 0) {
        $table = new history_table(uniqid('queued_table'), $startdate, $enddate);
        $table->set_attribute('class', 'generalbox generaltable table-sm');
        $table->out(100, true);
    }


    /**
     * Query the reader. Store results in the object for use by build_table.
     *
     * @param int $pagesize size of page for paginated displayed table.
     * @param bool $useinitialsbar do you want to use the initials bar.
     */
    public function query_db($pagesize, $useinitialsbar = true) {
        $manager = get_log_manager();
        $readers = $manager->get_readers();
        $reader = reset($readers);

        // Grab recordset for course_cloned event.
        $event = '\tool_clonecategory\event\course_cloned';
        $select = "eventname = ? AND timecreated > ? AND timecreated < ?";

        $this->rawdata = $reader->get_events_select($select, array($event, $this->startdate, $this->enddate), '', null, null);

    }
}
