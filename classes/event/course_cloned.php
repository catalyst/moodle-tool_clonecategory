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

namespace tool_clonecategory\event;

/**
 * Registration of the system events.
 *
 * @package   tool_clonecategory
 * @copyright 2018, tim@avide.com.au
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_cloned extends \core\event\base {
    /**
     * Init method.
     */
    protected function init() {
        $this->data["crud"]        = "c";
        $this->data["edulevel"]    = self::LEVEL_OTHER;
        $this->data["objecttable"] = "course";
    }

    /**
     * Return localised event name.
     */
    public static function get_name() {
        return new \lang_string("eventcoursecloned", "tool_clonecategory");
    }

    /**
     * Returns description of what happened.
     */
    public function get_description() {
        return $this->other['log'];
    }

    /**
     * Get URL related to the action.
     */
    public function get_url() {
        return new \moodle_url("/admin/tool/clonecategory/action.php");
    }
}
