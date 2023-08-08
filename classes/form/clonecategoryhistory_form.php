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

namespace tool_clonecategory\form;

defined('MOODLE_INTERNAL') || die;
require_once($CFG->libdir.'/formslib.php');

/**
 * Clone category history form.
 *
 * @package tool_clonecategory
 * @copyright 2018 tim@avide.com.au
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class clonecategoryhistory_form extends \moodleform {

    /**
     * The form definition.
     */
    public function definition() {
        $mform = $this->_form;
        $data = $this->_customdata;
        $buttonlabel = get_string('clone_history', 'tool_clonecategory');

        $mform->addElement('duration', 'timelimit', get_string('historylimit', 'tool_clonecategory'), array('defaultunit' => DAYSECS));
        $mform->setDefault('timelimit', $data['timelimit']);

        $this->add_action_buttons(true, $buttonlabel);
    }

    /**
     * Resets the form.
     */
    public function reset() {
        $this->_form->updateSubmission(null, null);
    }
}
