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

admin_externalpage_setup('clonecategory_history');

$start = optional_param('start', strtotime('-3 month', time()), PARAM_INT);
$end = optional_param('end', time(), PARAM_INT);

$clonehistoryform = new clonecategoryhistory_form(null, [
   'startdate' => $start,
   'enddate' => $end,
]);

// Redirect back if form is cancelled.
if ($clonehistoryform->is_cancelled()) {
    redirect(new moodle_url("/admin/tool/clonecategory/history.php"));
}

$PAGE->set_url(new moodle_url("/admin/tool/clonecategory/history.php"));
$PAGE->set_context(context_system::instance());

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('history_table', 'tool_clonecategory'));

if ($data = $clonehistoryform->get_data()) {
    history_table::display($data->startdate, $data->enddate);
}
$clonehistoryform->display();

echo $OUTPUT->footer();
