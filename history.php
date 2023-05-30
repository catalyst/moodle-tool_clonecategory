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
 * @package    local_clonecategory
 * @copyright  2023 Matthew Hilton <matthewhilton@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_clonecategory\history_table;

require('../../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('clonecategory_history');

$PAGE->set_url(new moodle_url("/local/clonecategory/history.php"));
$PAGE->set_context(context_system::instance());

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('history_table', 'local_clonecategory'));
history_table::display();
echo $OUTPUT->footer();
