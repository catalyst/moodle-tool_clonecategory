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

use local_clonecategory\clonecategory_form;
use local_clonecategory\cloner;
use local_clonecategory\history_filter_form;
use local_clonecategory\history_table;
use local_clonecategory\queued_table;

require('../../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('clonecategory_action');

$action = optional_param('action', '', PARAM_ALPHA);
$config = get_config('local_clonecategory');

$action = optional_param('action', false, PARAM_ALPHA);
$source = optional_param('source', 0, PARAM_INT);
$dest = optional_param('destination', 0, PARAM_INT);
$name = optional_param('name', '', PARAM_RAW);
$start = optional_param('start', time(), PARAM_INT);
$cloneid = optional_param('cloneid', '', PARAM_TEXT);
$end = optional_param('end', strtotime('+3 month', time()), PARAM_INT);

$PAGE->set_url(new moodle_url("/local/clonecategory/action.php"));
$PAGE->set_context(context_system::instance());

$cloneform = new clonecategory_form(null, [
    'source' => $source,
    'destination' => $dest,
    'destcategoryname' => $name,
    'startdate' => $start,
    'enddate' => $end,
]);

// Redirect back if form is cancelled.
if ($cloneform->is_cancelled()) {
    redirect($returnurl);
}

if ($data = $cloneform->get_data()) {
    // Generate a uniqueid to track/group the clones.
    $cloneid = uniqid();

    // Queue each as an adhoc task.
    list($src, $dest) = cloner::prepare($data->source, $data->destination, $data->destcategoryname, $data->destcategoryidnumber);
    cloner::queue($src, $dest, $cloneid, $data->startdate, $data->enddate);

    // Redirect back without form data,
    // to avoid re-submission.
    // Include the cloneid, so the tables auto-group by clone id.
    $url = new moodle_url($PAGE->url, ['cloneid' => $cloneid]);
    redirect($url, get_string('queuedsuccessfully', 'local_clonecategory'), null, \core\output\notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('action_link', 'local_clonecategory'));
$cloneform->display();

echo $OUTPUT->heading(get_string('queued_table', 'local_clonecategory'));
echo $OUTPUT->heading(get_string('waitingforprocessing', 'local_clonecategory'), 3);

if (!empty($cloneid)) {
    $notifyoutput = html_writer::tag('span', get_string('showingclonesfor', 'local_clonecategory', $cloneid), ['class' => 'mr-2']);
    $notifyoutput .= html_writer::link($PAGE->url, get_string('clearfilter', 'local_clonecategory'),
        ['class' => 'btn btn-secondary']);
    echo $OUTPUT->notification($notifyoutput, \core\output\notification::NOTIFY_INFO);
}

queued_table::display($cloneid);

// If cloneid is given, also show history table for the given cloneid logs.
if (!empty($cloneid)) {
    echo $OUTPUT->heading(get_string('processed', 'local_clonecategory'), 3);
    history_table::display($cloneid);
}

echo $OUTPUT->single_button(new moodle_url('/local/clonecategory/history.php'), get_string('viewclonelogs', 'local_clonecategory'));

echo $OUTPUT->footer();
