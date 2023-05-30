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

use local_clonecategory\category_status_table;
use local_clonecategory\cloner;
use local_clonecategory\form\clonecategory_form;
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

$destid = optional_param('destid', 0, PARAM_INT);
$srcid = optional_param('srcid', 0, PARAM_INT);


$end = optional_param('end', strtotime('+3 month', time()), PARAM_INT);

$PAGE->set_url(new moodle_url("/local/clonecategory/action.php", ['destid' => $destid, 'srcid' => $srcid]));
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
    // Queue each as an adhoc task.
    list($src, $dest) = cloner::prepare($data->source, $data->destination, $data->destcategoryname, $data->destcategoryidnumber);
    cloner::queue($src, $dest, $data->startdate, $data->enddate);

    $url = new moodle_url($PAGE->url, ['destid' => $dest->id, 'srcid' => $src->id]);
    redirect($url, get_string('queuedsuccessfully', 'local_clonecategory'), null, \core\output\notification::NOTIFY_SUCCESS);
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('action_link', 'local_clonecategory'));
$cloneform->display();

if (!empty($srcid) && !empty($destid)) {
    echo $OUTPUT->heading(get_string('queued_table', 'local_clonecategory'));
    category_status_table::display($srcid, $destid);
}

echo $OUTPUT->single_button(new moodle_url('/local/clonecategory/history.php'), get_string('viewclonelogs', 'local_clonecategory'));

echo $OUTPUT->footer();
