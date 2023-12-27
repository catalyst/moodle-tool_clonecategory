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
 * Clone category lang strings
 *
 * @package    tool_clonecategory
 * @copyright  2018, tim@avide.com.au
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['plugintitle'] = 'Clone all courses in a category';
$string['action_link'] = 'Clone Category';
$string['pluginname'] = 'Clone Category';
$string['cloning_history'] = 'Cloning history';

$string['source_category'] = 'Source category';
$string['destination_category'] = 'Destination parent category';

$string['destination_category_name'] = 'Destination category name';
$string['destination_category_idnumber'] = 'Destination category idnumber';
$string['categoryname_help'] = 'if set, and an idnumber is also specified, this will be created underneath the desination category';
$string['categoryname'] = 'Optional - create sub-category in destination';

$string['error_missing_source_idnumber'] = 'Source Category is missing its idnumber (required)';
$string['error_missing_destination_idnumber'] = 'Destination Category is missing its idnumber (required)';
$string['error_date_problem'] = 'The end date must occur after the start date';
$string['error_destination_not_top_when_empty'] = 'Destination cannot be Top when not adding a new category';
$string['error_must_specify_both'] = 'You must enter both Name and IdNumber fields if entering either';

$string['list_courses'] = "List courses";
$string['clone_courses'] = "Clone courses";
$string['eventcoursecloned'] = 'Course cloned';
$string['queued_table'] = 'History';
$string['history_table'] = 'Clone logs';
$string['viewclonelogs'] = 'View all clone logs';
$string['taskstarted'] = 'In progress';
$string['showingclonesfor'] = 'Showing clones for clone id {$a}';
$string['clearfilter'] = 'Clear filter';
$string['waitingforprocessing'] = 'Waiting for processing';
$string['processed'] = 'Finished processing';

$string['queuedsuccessfully'] = 'Queued successfully';
$string['clonefailed'] = 'Cloning of course {$a->id} failed with message {$a->message}';

$string['cloning:fromto'] = 'Showing courses from category: {$a->src} being cloned to category: {$a->dest}';

$string['cloning:source'] = 'Source course';
$string['cloning:dest'] = 'Destination course';
$string['cloning:status'] = 'Status';

$string['history:id'] = 'Log id';
$string['history:time'] = 'Time';
$string['history:message'] = 'Message';
$string['history:status'] = 'Status';

$string['notstarted'] = 'Not started yet';
$string['started'] = 'Running for {$a}';
$string['failed'] = 'Failed';
$string['success'] = 'Success';

$string['error:invalididnumber'] = 'The ID number of the category given to create is already linked to a category that is not a child of the destination category selected';

$string['clone_history'] = 'View clone logs';
$string['historylimit'] = 'Limit logs to previous';

$string['privacy:metadata'] = 'The clone category plugin does not store any personal data.';

