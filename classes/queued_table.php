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
class queued_table extends table_sql {
    /**
     * Create table
     * @param string $uniqueid
     * @param string $cloneidfilter Clone ID to filter by
     */
    public function __construct(string $uniqueid, string $cloneidfilter = '') {
        global $PAGE, $DB;

        parent::__construct($uniqueid);

        $columns = [
            'id',
            'cloneid',
            'timecreated',
            'course',
            'categoryfrom',
            'categoryto',
            'timestarted'
        ];

        $headers = [
            get_string('queued:id', 'local_clonecategory'),
            get_string('queued:cloneid', 'local_clonecategory'),
            get_string('queued:timecreated', 'local_clonecategory'),
            get_string('queued:course', 'local_clonecategory'),
            get_string('queued:categoryfrom', 'local_clonecategory'),
            get_string('queued:categoryto', 'local_clonecategory'),
            get_string('queued:timestarted', 'local_clonecategory'),
        ];

        $this->define_columns($columns);
        $this->define_headers($headers);
        $this->baseurl = $PAGE->url;

        $where = "classname = :classname";
        $params = ['classname' => '\local_clonecategory\task\clone_course_task'];

        // Add additional clone id filter.
        if (!empty($cloneidfilter)) {
            $where .= " AND " . $DB->sql_like('customdata', ':cloneid');
            $params['cloneid'] = '%' . $cloneidfilter . '%';
        }

        $this->set_sql('id,customdata,timecreated,timestarted', '{task_adhoc}', $where, $params);
        $this->sortable(true, 'timecreated', SORT_DESC);
    }

    /**
     * Course col
     * @param object $row
     */
    public function col_course($row) {
        $courseid = json_decode($row->customdata)->courseid;
        $course = get_course($courseid);
        return $course->fullname;
    }

    /**
     * Category from col
     * @param object $row
     */
    public function col_categoryfrom($row) {
        return $this->get_cat_name(json_decode($row->customdata)->srcid);
    }

    /**
     * Category to col
     * @param object $row
     */
    public function col_categoryto($row) {
        return $this->get_cat_name(json_decode($row->customdata)->destid);
    }

    /**
     * Gets the name of category, or empty string if does not exist.
     * @param int $catid id of category
     * @return string
     */
    private function get_cat_name(int $catid): string {
        $category = \core_course_category::get($catid);
        return !empty($category) ? $category->name : '';
    }

    /**
     * Time created col
     * @param object $row
     */
    public function col_timecreated($row) {
        // Don't show timecreated if timestarted set.
        // Since the timecreated gets updated when the task begins.
        // Might be confusing for users.
        return empty($row->timestarted) ? userdate($row->timecreated) : get_string('taskstarted', 'local_clonecategory');
    }

    /**
     * Time started col
     * @param object $row
     */
    public function col_timestarted($row) {
        // Not started yet.
        if (empty($row->timestarted)) {
            return \html_writer::tag('p', get_string('notstarted', 'local_clonecategory'), ['class' => 'badge badge-light']);
        }

        // Started / in progress.
        $timestamp = userdate($row->timestarted);
        return \html_writer::tag('p', get_string('started', 'local_clonecategory', $timestamp), ['class' => 'badge badge-info']);
    }

    /**
     * Clone id col
     * @param object $row
     */
    public function col_cloneid($row) {
        $cloneid = json_decode($row->customdata)->cloneid ?? '';

        if (empty($cloneid)) {
            return '';
        }

        // Else show link that when clicked filters the table by the clone id.
        $url = new moodle_url($this->baseurl, ['cloneid' => $cloneid]);
        return \html_writer::link($url, $cloneid);
    }

    /**
     * Creates table and renders it.
     * @param string $cloneidfilter Clone ID to filter by, ignores if empty.
     */
    public static function display(string $cloneidfilter = '') {
        $table = new queued_table(uniqid('queued_table'), $cloneidfilter);
        $table->out(30, true);
    }
}
