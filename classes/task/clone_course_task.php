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
 * Clone category action form page.
 *
 * @package    local_clonecategory
 * @copyright  2018, tim@avide.com.au, 2023 Matthew Hilton <matthewhilton@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_clonecategory\task;

/**
 * Course cloning task.
 */
class clone_course_task extends \core\task\adhoc_task {
    /**
     * Executes task.
     */
    public function execute() {
        $data = $this->get_custom_data();
        try {
            $src = \core_course_category::get($data->srcid);
            $dest = \core_course_category::get($data->destid);

            \local_clonecategory\cloner::clone_course($data->courseid, $src, $dest, $data->data);
        } catch (\Throwable $e) {
            // Catch so that adhoc task does not retry.

            // Log error into events log.
            $string = get_string('clonefailed', 'local_clonecategory', [
                'id' => $data->courseid,
                'message' => $e->getMessage()
            ]);

            \local_clonecategory\cloner::log_clone_status($string);
        }
    }
}
