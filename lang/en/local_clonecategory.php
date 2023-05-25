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
 * @package    local_clonecategory
 * @copyright  2018, tim@avide.com.au
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['plugintitle'] = 'Clone all courses in a category';
$string['action_link'] = 'Clone Category';
$string['pluginname'] = 'Clone Category';

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
$string['queued_table'] = 'Requested clones waiting for processing';
$string['history_table'] = 'Clone logs';
$string['viewclonelogs'] = 'View clone logs';
$string['taskstarted'] = 'In progress';

$string['queuedsuccessfully'] = 'Queued successfully';
$string['clonefailed'] = 'Cloning of course {$a->id} failed with message {$a->message}';

$string['queued:id'] = 'Task ID';
$string['queued:timecreated'] = 'Time requested';
$string['queued:course'] = 'Course';
$string['queued:categoryfrom'] = 'Category from';
$string['queued:categoryto'] = 'Category to';
$string['queued:timestarted'] = 'Processing start time';
