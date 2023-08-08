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
 * Clone category history page.
 *
 * @package    tool_clonecategory
 * @copyright  2023 Matthew Hilton <matthewhilton@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_clonecategory\form\clonecategoryhistory_form;
use tool_clonecategory\history_table;

require('../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

\core\session\manager::write_close();

admin_externalpage_setup('clonecategory_history');

// Default timelimit 31 days.
$timelimit = optional_param_array('timelimit', 2678400, PARAM_INT);

$clonehistoryform = new clonecategoryhistory_form(null, [
   'timelimit' => $timelimit,
]);

$PAGE->set_url(new moodle_url("/admin/tool/clonecategory/history.php"));
$PAGE->set_context(context_system::instance());

// Redirect back if form is cancelled.
if ($clonehistoryform->is_cancelled()) {
    redirect($PAGE->url);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('history_table', 'tool_clonecategory'));

$clonehistoryform->display();

if ($data = $clonehistoryform->get_data()) {
    history_table::display($data->timelimit);
}

echo $OUTPUT->footer();
