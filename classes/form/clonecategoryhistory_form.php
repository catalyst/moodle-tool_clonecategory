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

        // Timestamp of start date of all newly cloned courses.
        $mform->addElement('date_selector', 'startdate', get_string('historystartdate', 'tool_clonecategory'));
        $mform->setDefault('startdate', $data['startdate']);

        // Timestamp of end date of all newly cloned courses.
        $mform->addElement('date_selector', 'enddate', get_string('historyenddate', 'tool_clonecategory'));
        $mform->setDefault('enddate', $data['enddate']);

        $this->add_action_buttons(true, $buttonlabel);
    }

    /**
     * Validates the data submit for this form.
     *
     * @param array $data An array of key,value data pairs.
     * @param array $files Any files that may have been submit as well.
     * @return array An array of errors.
     */
    public function validation($data, $files) {
        global $DB;
        $errors = parent::validation($data, $files);

        if (!empty($data['enddate']) && !empty($data['startdate'])) {
            if ($data['enddate'] < $data['startdate'] || $data['startdate'] === $data['enddate']) {
                $errors['enddate'] = get_string('error_date_problem', 'tool_clonecategory');
            }
        }
        return $errors;
    }

    /**
     * Resets the form.
     */
    public function reset() {
        $this->_form->updateSubmission(null, null);
    }
}
