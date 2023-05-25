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

use table_sql;

defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir . '/tablelib.php');

/**
 * Clonecategory log history table
 *
 * @package    local_clonecategory
 * @copyright  2023 Matthew Hilton <matthewhilton@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class history_table extends table_sql {
     /**
      * Create table
      * @param string $uniqueid
      */
    public function __construct(string $uniqueid) {
        global $PAGE;

        parent::__construct($uniqueid);

        $columns = [
            'id',
            'timecreated',
            'message'
        ];

        $headers = [
            'id',
            'timecreated',
            'message'
        ];

        $this->define_columns($columns);
        $this->define_headers($headers);
        $this->baseurl = $PAGE->url;
        $this->set_sql('id,other,timecreated', '{logstore_standard_log}', "eventname = :eventname",
            ['eventname' => '\local_clonecategory\event\course_cloned']);
        $this->sortable(true, 'timecreated', SORT_DESC);
    }

    /**
     * Timecreated col
     * @param object $row
     */
    public function col_timecreated($row) {
        return userdate($row->timecreated);
    }

    /**
     * Message col
     * @param object $row
     */
    public function col_message($row) {
        $decoded = \tool_log\helper\reader::decode_other($row->other);
        return $decoded['log'] ?? '';
    }

    /**
     * Creates table and renders it.
     */
    public static function display() {
        $table = new history_table(uniqid('queued_table'));
        $table->out(30, true);
    }
}
